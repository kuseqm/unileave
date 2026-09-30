<?php

namespace Tests\Feature;

use App\Models\Policy;
use App\Models\PolicyRule;
use App\Models\University;
use App\Models\User;
use App\Services\PolicyEngine;
use Tests\TestCase;

class PolicyEngineTest extends TestCase
{
    private University $uniA;

    private University $uniB;

    private User $creator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->uniA = University::factory()->create();
        $this->uniB = University::factory()->create();
        $this->creator = User::factory()->create(['university_id' => $this->uniA->id]);
    }

    /**
     * @param  array<int, array{0: string, 1: mixed, 2?: string|null, 3?: string|null, 4?: int}>  $rules  [key, value, conditionField, conditionValue, priority]
     */
    private function policy(?int $universityId, array $rules, string $status = 'active', ?string $effectiveDate = null, string $code = 'HR-LEAVE-001'): Policy
    {
        $policy = Policy::create([
            'university_id' => $universityId,
            'code' => $code,
            'title' => 'Leave policy',
            'category' => 'hr_policy',
            'scope' => 'university-wide',
            'status' => $status,
            'effective_date' => $effectiveDate,
            'created_by' => $this->creator->id,
        ]);

        foreach ($rules as $rule) {
            PolicyRule::create([
                'policy_id' => $policy->id,
                'key' => $rule[0],
                'value' => (string) $rule[1],
                'type' => is_numeric($rule[1]) ? 'numeric' : 'string',
                'condition_field' => $rule[2] ?? null,
                'condition_value' => $rule[3] ?? null,
                'priority' => $rule[4] ?? 0,
                'is_active' => true,
            ]);
        }

        return $policy;
    }

    private function value(string $key, array $context = [], $default = null)
    {
        return app(PolicyEngine::class)->get($key, $context, $default, 'hr_policy');
    }

    public function test_platform_rule_applies_to_a_university_that_has_none_of_its_own(): void
    {
        $this->policy(null, [['max_annual_leave', 28]]);

        $this->assertEquals(28, $this->value('max_annual_leave', ['university_id' => $this->uniA->id]));
        $this->assertEquals(28, $this->value('max_annual_leave', ['university_id' => $this->uniB->id]));
    }

    public function test_a_universitys_own_rule_overrides_the_platform_for_that_university_only(): void
    {
        $this->policy(null, [['max_annual_leave', 28]]);
        $this->policy($this->uniA->id, [['max_annual_leave', 21]]);

        $this->assertEquals(21, $this->value('max_annual_leave', ['university_id' => $this->uniA->id]));
        $this->assertEquals(28, $this->value('max_annual_leave', ['university_id' => $this->uniB->id]));
    }

    public function test_another_universitys_rule_never_leaks_into_a_university_that_has_none(): void
    {
        $this->policy($this->uniA->id, [['max_annual_leave', 99]]);

        $this->assertEquals(28, $this->value('max_annual_leave', ['university_id' => $this->uniB->id], 28));
        $this->assertEquals(28, $this->value('max_annual_leave', ['university_id' => null], 28));
    }

    public function test_a_universitys_conditional_rule_that_does_not_match_falls_back_to_the_platform(): void
    {
        $this->policy(null, [['max_annual_leave', 28]]);
        $this->policy($this->uniA->id, [['max_annual_leave', 40, 'staff_category', 'academic']]);

        $this->assertEquals(40, $this->value('max_annual_leave', ['university_id' => $this->uniA->id, 'staff_category' => 'academic']));
        $this->assertEquals(28, $this->value('max_annual_leave', ['university_id' => $this->uniA->id, 'staff_category' => 'administrative']));
    }

    public function test_a_universitys_unconditional_rule_beats_a_conditional_platform_rule(): void
    {
        $this->policy(null, [['max_annual_leave', 45, 'staff_category', 'academic']]);
        $this->policy($this->uniA->id, [['max_annual_leave', 30]]);

        $this->assertEquals(30, $this->value('max_annual_leave', ['university_id' => $this->uniA->id, 'staff_category' => 'academic']));
    }

    public function test_within_a_layer_the_highest_priority_matching_rule_wins_over_the_default(): void
    {
        $this->policy(null, [
            ['max_annual_leave', 28, null, null, 0],
            ['max_annual_leave', 35, 'contract_type', 'permanent', 15],
            ['max_annual_leave', 45, 'staff_category', 'academic', 20],
        ]);

        $context = ['university_id' => $this->uniA->id, 'staff_category' => 'academic', 'contract_type' => 'permanent'];

        $this->assertEquals(45, $this->value('max_annual_leave', $context));
        $this->assertEquals(35, $this->value('max_annual_leave', ['staff_category' => 'administrative'] + $context));
        $this->assertEquals(28, $this->value('max_annual_leave', ['staff_category' => 'administrative', 'contract_type' => 'contract'] + $context));
    }

    public function test_a_draft_university_policy_has_no_effect_until_it_is_published(): void
    {
        $this->policy(null, [['max_annual_leave', 28]]);
        $draft = $this->policy($this->uniA->id, [['max_annual_leave', 21]], 'draft');

        $this->assertEquals(28, $this->value('max_annual_leave', ['university_id' => $this->uniA->id]));

        $draft->update(['status' => 'active']);

        $this->assertEquals(21, $this->value('max_annual_leave', ['university_id' => $this->uniA->id]));
    }

    public function test_a_published_policy_with_a_future_effective_date_only_applies_once_it_arrives(): void
    {
        $this->policy(null, [['max_annual_leave', 28]]);
        $this->policy($this->uniA->id, [['max_annual_leave', 21]], 'active', now()->addDays(10)->toDateString());

        $this->assertEquals(28, $this->value('max_annual_leave', ['university_id' => $this->uniA->id]));

        $this->travel(11)->days();

        $this->assertEquals(21, $this->value('max_annual_leave', ['university_id' => $this->uniA->id]));
    }

    public function test_an_inactive_rule_is_ignored(): void
    {
        $policy = $this->policy(null, [['max_annual_leave', 45], ['max_consecutive_leave_days', 10]]);
        $policy->rules()->update(['is_active' => false]);

        $context = ['university_id' => $this->uniA->id];

        // Falls through to the catalog: a stated default, or no limit at all.
        $this->assertEquals(28, $this->value('max_annual_leave', $context));
        $this->assertNull($this->value('max_consecutive_leave_days', $context));
    }

    public function test_a_setting_nothing_defines_falls_back_to_its_catalog_default(): void
    {
        $this->assertEquals(28, $this->value('max_annual_leave', ['university_id' => $this->uniA->id]));
        $this->assertNull($this->value('a_setting_that_does_not_exist', ['university_id' => $this->uniA->id]));
    }

    public function test_the_callers_default_is_returned_when_nothing_applies(): void
    {
        $this->assertSame(28, $this->value('max_annual_leave', ['university_id' => $this->uniA->id], 28));
    }

    public function test_the_signed_in_users_university_is_used_when_the_context_names_none(): void
    {
        $this->policy(null, [['max_annual_leave', 28]]);
        $this->policy($this->uniA->id, [['max_annual_leave', 21]]);

        $this->actingAs(User::factory()->create(['university_id' => $this->uniA->id]));
        $this->assertEquals(21, $this->value('max_annual_leave'));

        $this->actingAs(User::factory()->create(['university_id' => $this->uniB->id]));
        $this->assertEquals(28, $this->value('max_annual_leave'));
    }

    public function test_an_explicit_null_university_means_platform_only_even_when_someone_is_signed_in(): void
    {
        $this->policy(null, [['max_annual_leave', 28]]);
        $this->policy($this->uniA->id, [['max_annual_leave', 21]]);

        $this->actingAs(User::factory()->create(['university_id' => $this->uniA->id]));

        $this->assertEquals(28, $this->value('max_annual_leave', ['university_id' => null]));
    }

    public function test_get_all_lets_a_university_override_individual_keys_and_inherit_the_rest(): void
    {
        $this->policy(null, [['max_annual_leave', 28], ['max_sick_leave', 12]]);
        $this->policy($this->uniA->id, [['max_annual_leave', 21]]);

        $values = app(PolicyEngine::class)->getAll('hr_policy', ['university_id' => $this->uniA->id]);

        $this->assertEquals(21, $values['max_annual_leave']);
        $this->assertEquals(12, $values['max_sick_leave']);
    }

    public function test_policy_lookup_by_code_prefers_the_universitys_own_copy(): void
    {
        $platform = $this->policy(null, [['max_annual_leave', 28]]);
        $own = $this->policy($this->uniA->id, [['max_annual_leave', 21]]);
        $engine = app(PolicyEngine::class);

        $this->assertTrue($engine->policy('HR-LEAVE-001', ['university_id' => $this->uniA->id])->is($own));
        $this->assertTrue($engine->policy('HR-LEAVE-001', ['university_id' => $this->uniB->id])->is($platform));
        $this->assertEquals(['max_annual_leave' => 21], $engine->evaluatePolicy('HR-LEAVE-001', ['university_id' => $this->uniA->id]));
    }

    public function test_two_universities_can_each_hold_their_own_policy_with_the_same_code(): void
    {
        $this->policy($this->uniA->id, [['max_annual_leave', 21]]);
        $this->policy($this->uniB->id, [['max_annual_leave', 30]]);

        $this->assertEquals(21, $this->value('max_annual_leave', ['university_id' => $this->uniA->id]));
        $this->assertEquals(30, $this->value('max_annual_leave', ['university_id' => $this->uniB->id]));
    }
}
