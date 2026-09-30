<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Policy;
use App\Models\PolicyRule;
use App\Models\PolicyRuleChange;
use App\Models\University;
use App\Models\User;
use App\Services\PolicyEngine;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class PolicySettingsTest extends TestCase
{
    private University $university;

    private University $other;

    private User $hr;

    private Policy $platform;

    protected function setUp(): void
    {
        parent::setUp();

        $this->university = University::factory()->create();
        $this->other = University::factory()->create();

        foreach (['view', 'create', 'edit', 'delete', 'approve', 'publish', 'archive'] as $action) {
            Permission::findOrCreate("{$action}_policies", 'web');
        }

        $this->hr = $this->staffOf($this->university);

        $this->platform = $this->policy(null, 'HR-LEAVE-001', [
            ['max_annual_leave', 28],
            ['max_annual_leave', 45, 'staff_category', 'academic', 20],
        ]);
    }

    private function staffOf(University $university, array $attributes = []): User
    {
        $user = User::factory()->create(['university_id' => $university->id] + $attributes);
        $user->givePermissionTo(Permission::where('name', 'like', '%_policies')->get());

        return $user;
    }

    /**
     * @param  array<int, array{0: string, 1: string|int, 2?: string|null, 3?: string|null, 4?: int}>  $rules  [key, value, conditionField, conditionValue, priority]
     */
    private function policy(?int $universityId, string $code, array $rules = [], string $status = 'active'): Policy
    {
        $policy = Policy::create([
            'university_id' => $universityId,
            'code' => $code,
            'title' => "Policy {$code}",
            'category' => 'hr_policy',
            'scope' => 'university-wide',
            'status' => $status,
            'created_by' => $this->hr?->id ?? User::factory()->create()->id,
        ]);

        foreach ($rules as $rule) {
            $this->rule($policy, $rule);
        }

        return $policy;
    }

    private function rule(Policy $policy, array $rule): PolicyRule
    {
        return PolicyRule::create([
            'policy_id' => $policy->id,
            'key' => $rule[0],
            'label' => $rule[0],
            'value' => (string) $rule[1],
            'type' => 'numeric',
            'condition_field' => $rule[2] ?? null,
            'operator' => isset($rule[2]) ? 'eq' : null,
            'condition_value' => $rule[3] ?? null,
            'priority' => $rule[4] ?? 0,
            'is_active' => true,
        ]);
    }

    private function ownPolicy(string $status = 'active', array $rules = []): Policy
    {
        return $this->policy($this->university->id, 'HR-LEAVE-UNI', $rules, $status);
    }

    // ---- where a value comes from ------------------------------------------

    public function test_the_engine_says_where_each_value_comes_from(): void
    {
        $own = $this->ownPolicy('active', [['max_sick_leave', 20]]);
        $engine = app(PolicyEngine::class);
        $context = ['university_id' => $this->university->id];

        $sick = $engine->resolve('max_sick_leave', $context, null, 'hr_policy');
        $this->assertSame(['university', 20], [$sick['source'], $sick['value']]);
        $this->assertSame($own->id, $sick['rule']->policy_id);

        $annual = $engine->resolve('max_annual_leave', $context, null, 'hr_policy');
        $this->assertSame(['platform', 28], [$annual['source'], $annual['value']]);

        $noRule = $engine->resolve('max_compassionate_leave', $context, null, 'hr_policy');
        $this->assertSame(['default', 3, null], [$noRule['source'], $noRule['value'], $noRule['rule']]);
    }

    public function test_the_settings_screen_shows_the_value_in_force_and_its_source(): void
    {
        $this->ownPolicy('active', [['max_sick_leave', 20]]);

        $this->actingAs($this->hr)->get(route('policies.settings'))
            ->assertOk()
            ->assertSeeInOrder(['Sick leave entitlement', '20 days per year', 'Your university', 'HR-LEAVE-UNI'])
            ->assertSeeInOrder(['Annual leave entitlement', '28 days per year', 'Platform default', 'HR-LEAVE-001'])
            ->assertSeeInOrder(['Compassionate leave entitlement', '3 days per year', 'System default'])
            ->assertSeeInOrder(['Yearly limit for a leave type', "Each leave type's own allowance", 'System default']);
    }

    public function test_the_screen_can_show_what_applies_to_a_kind_of_staff(): void
    {
        $this->actingAs($this->hr)->get(route('policies.settings', ['staff_category' => 'academic']))
            ->assertOk()
            ->assertSeeInOrder(['Annual leave entitlement', '45 days per year', 'Platform default']);

        $this->actingAs($this->hr)->get(route('policies.settings', ['staff_category' => 'astronaut']))
            ->assertOk()
            ->assertSeeInOrder(['Annual leave entitlement', '28 days per year']);
    }

    public function test_it_lists_the_other_rules_behind_a_value(): void
    {
        $this->actingAs($this->hr)->get(route('policies.settings'))
            ->assertOk()
            ->assertSee('2 rules defined')
            ->assertSee('45 days per year - Staff category is Academic')
            ->assertSee('in force here');
    }

    public function test_another_universitys_settings_never_appear(): void
    {
        $this->policy($this->other->id, 'HR-LEAVE-OTHER', [['max_sick_leave', 99]]);

        $this->actingAs($this->hr)->get(route('policies.settings'))
            ->assertOk()
            ->assertDontSee('99 days per year')
            ->assertDontSee('HR-LEAVE-OTHER');
    }

    public function test_without_a_policy_of_its_own_the_screen_says_so_and_offers_no_change(): void
    {
        $this->actingAs($this->hr)->get(route('policies.settings'))
            ->assertOk()
            ->assertSee('has no HR Policy of its own yet')
            ->assertDontSee('Set for my university');
    }

    public function test_a_draft_policy_is_flagged_as_not_in_force(): void
    {
        $this->ownPolicy('draft', [['max_sick_leave', 20]]);

        $this->actingAs($this->hr)->get(route('policies.settings'))
            ->assertOk()
            ->assertSee('not in force')
            ->assertSeeInOrder(['Sick leave entitlement', '12 days per year', 'System default']);
    }

    public function test_change_links_lead_to_the_right_place(): void
    {
        $own = $this->ownPolicy('active', [['max_sick_leave', 20]]);
        $sickRule = $own->rules()->firstOrFail();

        $this->actingAs($this->hr)->get(route('policies.settings'))
            ->assertOk()
            ->assertSee(route('policies.rules.edit', [$own, $sickRule]), false)
            ->assertSee(route('policies.rules.create', [$own, 'key' => 'max_annual_leave']), false);
    }

    public function test_the_old_leave_policies_page_now_leads_to_the_real_settings(): void
    {
        Permission::findOrCreate('view_settings', 'web');
        $this->hr->givePermissionTo('view_settings');

        $this->actingAs($this->hr)->get(route('settings.policies'))->assertRedirect('/policies/settings');
    }

    // ---- accountable changes -----------------------------------------------

    private function submit(Policy $policy, array $data)
    {
        return $this->actingAs($this->hr)->post(route('policies.rules.store', $policy), $data);
    }

    public function test_changing_a_policy_in_force_needs_a_reason(): void
    {
        $own = $this->ownPolicy('active');

        $this->submit($own, ['key' => 'max_sick_leave', 'value' => '20', 'is_active' => '1'])->assertSessionHasErrors('reason');
        $this->submit($own, ['key' => 'max_sick_leave', 'value' => '20', 'is_active' => '1', 'reason' => 'ok'])->assertSessionHasErrors('reason');

        $this->assertSame(0, $own->rules()->count());
        $this->assertSame(0, PolicyRuleChange::count());
    }

    public function test_a_draft_needs_no_reason(): void
    {
        $draft = $this->ownPolicy('draft');

        $this->submit($draft, ['key' => 'max_sick_leave', 'value' => '20', 'is_active' => '1'])->assertSessionHasNoErrors();

        $this->assertSame(1, PolicyRuleChange::count());
        $this->assertNull(PolicyRuleChange::firstOrFail()->reason);
    }

    public function test_adding_a_rule_is_recorded_with_who_and_why(): void
    {
        $own = $this->ownPolicy('active');

        $this->submit($own, ['key' => 'max_sick_leave', 'value' => '20', 'is_active' => '1', 'reason' => 'Council resolution 4/2026'])
            ->assertSessionHasNoErrors();

        $change = PolicyRuleChange::firstOrFail();

        $this->assertSame($own->id, $change->policy_id);
        $this->assertSame('created', $change->action);
        $this->assertSame('max_sick_leave', $change->key);
        $this->assertNull($change->old_values);
        $this->assertSame('20', $change->new_values['value']);
        $this->assertSame('Council resolution 4/2026', $change->reason);
        $this->assertSame($this->hr->id, $change->changed_by);
    }

    public function test_editing_a_rule_records_what_it_was_and_what_it_became(): void
    {
        $own = $this->ownPolicy('active', [['max_sick_leave', 12]]);
        $rule = $own->rules()->firstOrFail();

        $this->actingAs($this->hr)
            ->put(route('policies.rules.update', [$own, $rule]), ['value' => '20', 'is_active' => '1', 'reason' => 'Raised after the staff survey'])
            ->assertSessionHasNoErrors();

        $change = PolicyRuleChange::firstOrFail();

        $this->assertSame('updated', $change->action);
        $this->assertSame('12', $change->old_values['value']);
        $this->assertSame('20', $change->new_values['value']);
        $this->assertSame('Raised after the staff survey', $change->reason);
        $this->assertSame('20', $rule->fresh()->value);
    }

    public function test_saving_a_rule_without_changing_it_leaves_no_history(): void
    {
        $own = $this->ownPolicy('active', [['max_sick_leave', 12]]);
        $rule = $own->rules()->firstOrFail();

        $this->actingAs($this->hr)
            ->put(route('policies.rules.update', [$own, $rule]), ['value' => '12', 'is_active' => '1', 'reason' => 'Checked, no change needed'])
            ->assertSessionHasNoErrors();

        $this->assertSame(0, PolicyRuleChange::count());
    }

    public function test_deleting_a_rule_needs_a_reason_and_is_remembered(): void
    {
        $own = $this->ownPolicy('active', [['max_sick_leave', 12]]);
        $rule = $own->rules()->firstOrFail();

        $this->actingAs($this->hr)->delete(route('policies.rules.destroy', [$own, $rule]))->assertSessionHasErrors('reason');
        $this->assertSame(1, $own->rules()->count());

        $this->actingAs($this->hr)
            ->delete(route('policies.rules.destroy', [$own, $rule]), ['reason' => 'Superseded by the new HR manual'])
            ->assertSessionHasNoErrors();

        $this->assertSame(0, $own->rules()->count());

        $change = PolicyRuleChange::firstOrFail();
        $this->assertSame('deleted', $change->action);
        $this->assertSame('12', $change->old_values['value']);
        $this->assertNull($change->new_values);
        $this->assertSame($rule->id, $change->policy_rule_id);
    }

    public function test_a_change_also_reaches_the_organisation_audit_trail(): void
    {
        $own = $this->ownPolicy('active');

        $this->submit($own, ['key' => 'max_sick_leave', 'value' => '20', 'is_active' => '1', 'reason' => 'Council resolution 4/2026']);

        $audit = AuditLog::where('module', 'policy_rule')->firstOrFail();

        $this->assertSame($this->hr->id, $audit->user_id);
        $this->assertSame('created', $audit->action);
        $this->assertSame('Council resolution 4/2026', $audit->new_values['reason']);
        $this->assertSame('HR-LEAVE-UNI', $audit->new_values['policy']);
    }

    public function test_the_history_page_lists_changes_and_the_screen_shows_recent_ones(): void
    {
        $own = $this->ownPolicy('active');

        $this->submit($own, ['key' => 'max_sick_leave', 'value' => '20', 'is_active' => '1', 'reason' => 'Council resolution 4/2026']);

        $this->actingAs($this->hr)->get(route('policies.changes', $own))
            ->assertOk()
            ->assertSee('Sick leave entitlement')
            ->assertSee('Set to: 20 days per year - Everyone')
            ->assertSee('Council resolution 4/2026')
            ->assertSee($this->hr->full_name);

        $this->actingAs($this->hr)->get(route('policies.settings'))
            ->assertOk()
            ->assertSee('Council resolution 4/2026');
    }

    public function test_another_universitys_history_cannot_be_opened(): void
    {
        $theirs = $this->policy($this->other->id, 'HR-LEAVE-OTHER', [['max_sick_leave', 99]]);

        $this->actingAs($this->hr)->get(route('policies.changes', $theirs))->assertNotFound();
    }

    public function test_a_platform_default_cannot_be_changed_by_a_university(): void
    {
        $this->submit($this->platform, ['key' => 'max_sick_leave', 'value' => '20', 'is_active' => '1', 'reason' => 'Trying it on'])->assertForbidden();

        $this->assertSame(0, PolicyRuleChange::count());
    }
}
