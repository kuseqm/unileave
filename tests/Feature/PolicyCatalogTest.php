<?php

namespace Tests\Feature;

use App\Models\Policy;
use App\Models\PolicyRule;
use App\Models\University;
use App\Models\User;
use App\Services\PolicyCatalog;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class PolicyCatalogTest extends TestCase
{
    private University $university;

    private User $hr;

    private Policy $policy;

    protected function setUp(): void
    {
        parent::setUp();

        $this->university = University::factory()->create();

        foreach (['view', 'create', 'edit', 'delete', 'approve', 'publish', 'archive'] as $action) {
            Permission::findOrCreate("{$action}_policies", 'web');
        }

        $this->hr = User::factory()->create(['university_id' => $this->university->id]);
        $this->hr->givePermissionTo(Permission::where('name', 'like', '%_policies')->get());

        $this->policy = Policy::create([
            'university_id' => $this->university->id,
            'code' => 'HR-LEAVE-001',
            'title' => 'Leave policy',
            'category' => 'hr_policy',
            'scope' => 'university-wide',
            'status' => 'draft',
            'created_by' => $this->hr->id,
        ]);
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function rule(array $overrides = []): array
    {
        return array_merge([
            'key' => 'max_annual_leave',
            'value' => '30',
            'is_active' => '1',
        ], $overrides);
    }

    private function submit(array $data)
    {
        return $this->actingAs($this->hr)->post(route('policies.rules.store', $this->policy), $data);
    }

    public function test_every_catalog_entry_is_well_formed(): void
    {
        $catalog = app(PolicyCatalog::class);

        foreach ($catalog->keys() as $key => $definition) {
            $this->assertArrayHasKey($definition['category'], Policy::CATEGORIES, "{$key} has an unknown category");
            $this->assertContains($definition['type'], ['numeric', 'boolean', 'select', 'time', 'string'], "{$key} has an unknown type");
            $this->assertArrayHasKey($definition['enforcement'], PolicyCatalog::ENFORCEMENT_LABELS, "{$key} has an unknown enforcement level");
            $this->assertNotEmpty($definition['label'], "{$key} has no label");

            foreach ($definition['conditions'] as $field) {
                $this->assertArrayHasKey($field, config('policy_catalog.conditions'), "{$key} uses an undefined condition {$field}");
            }

            if (in_array($definition['enforcement'], ['enforced', 'display'], true)) {
                $this->assertNotEmpty($definition['enforced_by'] ?? null, "{$key} claims to be read but does not say where");
            }

            if ($definition['type'] === 'select') {
                $this->assertNotEmpty($definition['options'], "{$key} is a select with no options");
                $this->assertArrayHasKey($definition['default'], $definition['options'], "{$key} defaults to a choice it does not offer");
            }

            if ($definition['type'] === 'numeric' && $definition['default'] !== null) {
                $this->assertGreaterThanOrEqual($definition['min'] ?? PHP_INT_MIN, $definition['default'], "{$key} default is below its minimum");
                $this->assertLessThanOrEqual($definition['max'] ?? PHP_INT_MAX, $definition['default'], "{$key} default is above its maximum");
            }
        }
    }

    public function test_every_setting_the_code_reads_is_in_the_catalog(): void
    {
        $catalog = app(PolicyCatalog::class);

        foreach (['max_annual_leave', 'max_sick_leave', 'max_compassionate_leave'] as $key) {
            $this->assertTrue($catalog->has($key), "{$key} is read by the application but missing from the catalog");
        }
    }

    public function test_a_valid_rule_is_saved_with_its_label_and_type_from_the_catalog(): void
    {
        $this->submit($this->rule(['label' => 'Tampered', 'type' => 'json']))
            ->assertRedirect(route('policies.show', $this->policy));

        $rule = $this->policy->rules()->firstOrFail();

        $this->assertSame('Annual leave entitlement', $rule->label);
        $this->assertSame('numeric', $rule->type);
        $this->assertSame('30', $rule->value);
        $this->assertSame($this->hr->id, $rule->created_by);
    }

    public function test_a_setting_the_system_does_not_know_is_refused(): void
    {
        $this->submit($this->rule(['key' => 'made_up_setting']))->assertSessionHasErrors('key');

        $this->assertSame(0, $this->policy->rules()->count());
    }

    public function test_a_setting_from_another_policy_category_is_refused(): void
    {
        $this->submit($this->rule(['key' => 'pass_mark', 'value' => '40']))->assertSessionHasErrors('key');
    }

    public function test_values_outside_the_settings_range_are_refused(): void
    {
        foreach (['-1', '400', '28.5', 'plenty', ''] as $value) {
            $this->submit($this->rule(['value' => $value]))->assertSessionHasErrors('value');
        }

        $this->assertSame(0, $this->policy->rules()->count());
    }

    public function test_a_select_setting_only_accepts_its_listed_choices(): void
    {
        $this->submit($this->rule(['key' => 'return_date_rule', 'value' => 'whenever']))->assertSessionHasErrors('value');
        $this->submit($this->rule(['key' => 'return_date_rule', 'value' => 'employee']))->assertSessionHasNoErrors();
    }

    public function test_a_condition_may_only_use_fields_the_setting_supports(): void
    {
        $this->submit($this->rule([
            'key' => 'return_date_rule', 'value' => 'employee',
            'condition_field' => 'staff_category', 'operator' => 'eq', 'condition_value' => 'academic',
        ]))->assertSessionHasErrors('condition_field');
    }

    public function test_a_condition_may_only_use_values_the_field_has(): void
    {
        $base = ['condition_field' => 'staff_category', 'operator' => 'eq'];

        foreach (['astronaut', '', 'academic,supporting'] as $value) {
            $this->submit($this->rule($base + ['condition_value' => $value]))->assertSessionHasErrors('condition_value');
        }
    }

    public function test_one_setting_can_have_several_rules_with_different_conditions(): void
    {
        $this->submit($this->rule(['value' => '28']))->assertSessionHasNoErrors();
        $this->submit($this->rule([
            'value' => '45',
            'condition_field' => 'staff_category', 'operator' => 'eq', 'condition_value' => 'academic',
        ]))->assertSessionHasNoErrors();
        $this->submit($this->rule([
            'value' => '35',
            'condition_field' => 'staff_category', 'operator' => 'in', 'condition_value' => 'administrative, supporting',
        ]))->assertSessionHasNoErrors();

        $this->assertSame(3, $this->policy->rules()->count());

        $academic = $this->policy->rules()->where('condition_value', 'academic')->firstOrFail();
        $general = $this->policy->rules()->whereNull('condition_field')->firstOrFail();

        $this->assertGreaterThan($general->priority, $academic->priority, 'a conditional rule must outrank the everyone-rule');
        $this->assertSame('administrative,supporting', $this->policy->rules()->where('operator', 'in')->firstOrFail()->condition_value);
    }

    public function test_the_same_setting_with_the_same_condition_twice_is_refused(): void
    {
        $this->submit($this->rule())->assertSessionHasNoErrors();
        $this->submit($this->rule(['value' => '40']))->assertSessionHasErrors('key');

        $condition = ['condition_field' => 'staff_category', 'operator' => 'in', 'condition_value' => 'academic,supporting'];

        $this->submit($this->rule($condition))->assertSessionHasNoErrors();
        $this->submit($this->rule(['condition_value' => 'supporting,academic'] + $condition))->assertSessionHasErrors('key');

        $this->assertSame(2, $this->policy->rules()->count());
    }

    public function test_the_setting_of_an_existing_rule_cannot_be_changed(): void
    {
        $rule = PolicyRule::create([
            'policy_id' => $this->policy->id, 'key' => 'max_annual_leave', 'label' => 'Annual leave entitlement',
            'value' => '28', 'type' => 'numeric', 'priority' => 0, 'is_active' => true,
        ]);

        $this->actingAs($this->hr)
            ->put(route('policies.rules.update', [$this->policy, $rule]), $this->rule(['key' => 'max_sick_leave', 'value' => '31']))
            ->assertRedirect(route('policies.show', $this->policy));

        $rule->refresh();

        $this->assertSame('max_annual_leave', $rule->key);
        $this->assertSame('31', $rule->value);
        $this->assertSame($this->hr->id, $rule->updated_by);
    }

    public function test_a_rule_can_be_saved_inactive(): void
    {
        $this->submit(['key' => 'max_annual_leave', 'value' => '30'])->assertSessionHasNoErrors();

        $this->assertFalse($this->policy->rules()->firstOrFail()->is_active);
    }

    public function test_the_rule_editor_pages_render_for_a_policy_owner(): void
    {
        $rule = PolicyRule::create([
            'policy_id' => $this->policy->id, 'key' => 'max_annual_leave', 'label' => 'Annual leave entitlement',
            'value' => '28', 'type' => 'numeric', 'priority' => 0, 'is_active' => true,
        ]);

        PolicyRule::create([
            'policy_id' => $this->policy->id, 'key' => 'retired_setting', 'label' => 'Retired',
            'value' => '1', 'type' => 'numeric', 'priority' => 0, 'is_active' => true,
        ]);

        $this->actingAs($this->hr)->get(route('policies.rules.index', $this->policy))
            ->assertOk()
            ->assertSee('Annual leave entitlement')
            ->assertSee('28 days per year')
            ->assertSee('Unknown setting');

        $this->actingAs($this->hr)->get(route('policies.rules.create', $this->policy))
            ->assertOk()
            ->assertSee('Minimum notice before leave starts');

        $this->actingAs($this->hr)->get(route('policies.rules.edit', [$this->policy, $rule]))->assertOk();
    }
}
