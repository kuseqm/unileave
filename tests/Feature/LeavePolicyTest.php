<?php

namespace Tests\Feature;

use App\Models\ApprovalLog;
use App\Models\Department;
use App\Models\LeaveRequest;
use App\Models\LeaveRequestDraft;
use App\Models\LeaveType;
use App\Models\Policy;
use App\Models\PolicyRule;
use App\Models\StaffEmergencyContact;
use App\Models\University;
use App\Models\User;
use App\Services\LeavePolicy;
use Carbon\Carbon;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class LeavePolicyTest extends TestCase
{
    /** A Monday. */
    private const TODAY = '2026-09-21';

    private University $university;

    private User $staff;

    private User $colleague;

    /** @var array<string, LeaveType> */
    private array $types = [];

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow(self::TODAY.' 09:00:00');

        $this->university = University::factory()->create();
        $department = Department::factory()->create();

        $this->staff = User::factory()->create([
            'university_id' => $this->university->id,
            'department_id' => $department->id,
            'staff_category' => 'administrative',
        ]);
        $this->colleague = User::factory()->create([
            'university_id' => $this->university->id,
            'department_id' => $department->id,
        ]);

        foreach ([
            'LC1' => ['Annual Leave', 28],
            'LC2' => ['Sick Leave', 28],
            'LC3' => ['Compassionate Leave', 5],
            'LC4' => ['Maternity/Paternity Leave', 84],
            'LC5' => ['Special Leave', 10],
            'LC10' => ['University Business', null],
        ] as $code => [$name, $allowed]) {
            $this->types[$code] = LeaveType::forceCreate([
                'code' => $code, 'name' => $name, 'days_allowed' => $allowed,
                'status' => 'active', 'sort_order' => 1,
                'requires_certificate' => false, 'requires_letter' => false, 'deduct_balance' => true,
                'vc_discretion' => false, 'is_configurable' => false,
            ]);
        }

        // The platform default, shaped like the seeded HR-LEAVE-001.
        $this->rules(null, [
            ['max_annual_leave', 28],
            ['max_annual_leave', 45, 'staff_category', 'eq', 'academic', 20],
            ['max_sick_leave', 12],
            ['max_compassionate_leave', 3],
            ['min_notice_days', 3],
            ['min_notice_days', 0, 'leave_type', 'in', 'LC2,LC3', 10],
            ['allow_half_day', 'true', 'leave_type', 'in', 'LC1,LC2,LC3', 10],
        ]);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    /**
     * @param  array<int, array{0: string, 1: string|int, 2?: string|null, 3?: string|null, 4?: string|null, 5?: int}>  $rules  [key, value, conditionField, operator, conditionValue, priority]
     */
    private function rules(?int $universityId, array $rules, string $code = 'HR-LEAVE-001'): Policy
    {
        $policy = Policy::create([
            'university_id' => $universityId,
            'code' => $code,
            'title' => 'Leave policy',
            'category' => 'hr_policy',
            'scope' => 'university-wide',
            'status' => 'active',
            'created_by' => $this->staff->id,
        ]);

        foreach ($rules as $rule) {
            PolicyRule::create([
                'policy_id' => $policy->id,
                'key' => $rule[0],
                'value' => (string) $rule[1],
                'type' => in_array($rule[1], ['true', 'false'], true) ? 'boolean' : 'numeric',
                'condition_field' => $rule[2] ?? null,
                'operator' => $rule[3] ?? null,
                'condition_value' => $rule[4] ?? null,
                'priority' => $rule[5] ?? 0,
                'is_active' => true,
            ]);
        }

        return $policy;
    }

    private function evaluate(string $type, string $start, string $end, ?string $return = null, ?User $applicant = null): array
    {
        return app(LeavePolicy::class)->evaluate(
            $applicant ?? $this->staff,
            $this->types[$type],
            Carbon::parse($start),
            Carbon::parse($end),
            $return ? Carbon::parse($return) : null
        );
    }

    private function takeLeave(string $type, string $start, string $end, string $status = 'approved', ?User $staff = null): LeaveRequest
    {
        return LeaveRequest::forceCreate([
            'staff_id' => ($staff ?? $this->staff)->id,
            'leave_type_id' => $this->types[$type]->id,
            'start_date' => $start,
            'end_date' => $end,
            'days' => Carbon::parse($start)->diffInDays(Carbon::parse($end)) + 1,
            'reason' => 'Test leave',
            'status' => $status,
        ]);
    }

    public function test_leave_must_be_requested_the_policys_notice_period_ahead(): void
    {
        $this->assertArrayHasKey('start_date', $this->evaluate('LC1', '2026-09-21', '2026-09-22')['errors'], 'same day');
        $this->assertArrayHasKey('start_date', $this->evaluate('LC1', '2026-09-23', '2026-09-24')['errors'], 'two days ahead');
        $this->assertArrayNotHasKey('start_date', $this->evaluate('LC1', '2026-09-24', '2026-09-25')['errors'], 'exactly three days ahead');

        $message = $this->evaluate('LC1', '2026-09-22', '2026-09-23')['errors']['start_date'];
        $this->assertStringContainsString('at least 3 days', $message);
        $this->assertStringContainsString('24 Sep 2026', $message);
    }

    public function test_urgent_leave_types_are_exempt_from_the_notice_period(): void
    {
        $this->assertSame([], $this->evaluate('LC2', '2026-09-21', '2026-09-23')['errors']);
        $this->assertSame([], $this->evaluate('LC3', '2026-09-21', '2026-09-22')['errors']);
    }

    public function test_a_universitys_own_policy_replaces_the_platform_value_for_its_staff_only(): void
    {
        $this->rules($this->university->id, [['min_notice_days', 7]], 'HR-LEAVE-UNI');

        $this->assertArrayHasKey('start_date', $this->evaluate('LC1', '2026-09-27', '2026-09-29')['errors'], 'six days ahead is too soon at this university');
        $this->assertSame([], $this->evaluate('LC1', '2026-09-28', '2026-09-29')['errors'], 'seven days is enough');

        $elsewhere = User::factory()->create(['university_id' => University::factory()->create()->id]);
        $this->assertSame([], $this->evaluate('LC1', '2026-09-27', '2026-09-29', null, $elsewhere)['errors'], 'another university still gets the platform three days');
    }

    public function test_the_yearly_entitlement_follows_the_applicants_category(): void
    {
        // 28 for administrative staff: 29 days is one too many.
        $this->assertArrayHasKey('end_date', $this->evaluate('LC1', '2026-10-01', '2026-10-29')['errors']);
        $this->assertSame([], $this->evaluate('LC1', '2026-10-01', '2026-10-28')['errors']);

        $academic = User::factory()->create(['university_id' => $this->university->id, 'staff_category' => 'academic']);

        $this->assertSame([], $this->evaluate('LC1', '2026-10-01', '2026-11-14', null, $academic)['errors'], '45 days for academic staff');
        $this->assertArrayHasKey('end_date', $this->evaluate('LC1', '2026-10-01', '2026-11-15', null, $academic)['errors']);
    }

    public function test_days_already_taken_or_pending_count_towards_the_year_but_rejected_and_cancelled_do_not(): void
    {
        $this->takeLeave('LC1', '2026-03-01', '2026-03-10', 'approved');
        $this->takeLeave('LC1', '2026-05-01', '2026-05-05', 'pending');
        $this->takeLeave('LC1', '2026-06-01', '2026-06-20', 'rejected');
        $this->takeLeave('LC1', '2026-07-01', '2026-07-20', 'cancelled');
        $this->takeLeave('LC1', '2025-12-01', '2025-12-20', 'approved'); // last year

        // 10 + 5 = 15 used, so 13 left.
        $this->assertSame([], $this->evaluate('LC1', '2026-10-01', '2026-10-13')['errors']);

        $error = $this->evaluate('LC1', '2026-10-01', '2026-10-14')['errors']['end_date'];
        $this->assertStringContainsString('15 days already used', $error);
        $this->assertStringContainsString('total 29', $error);
    }

    public function test_sick_and_compassionate_entitlements_are_enforced(): void
    {
        $this->assertSame([], $this->evaluate('LC2', '2026-09-21', '2026-10-02')['errors'], '12 days');
        $this->assertArrayHasKey('end_date', $this->evaluate('LC2', '2026-09-21', '2026-10-03')['errors'], '13 days');
        $this->assertArrayHasKey('end_date', $this->evaluate('LC3', '2026-09-21', '2026-09-24')['errors'], '4 days against 3');
    }

    public function test_other_leave_types_use_the_allowance_set_on_the_type_unless_a_policy_rule_says_otherwise(): void
    {
        $this->assertSame([], $this->evaluate('LC4', '2026-10-01', '2026-12-23')['errors'], '84 days of maternity leave');
        $this->assertArrayHasKey('end_date', $this->evaluate('LC4', '2026-10-01', '2026-12-24')['errors'], '85 days');
        $this->assertSame([], $this->evaluate('LC10', '2026-10-01', '2027-01-31')['errors'], 'a type with no allowance has no limit');

        $this->rules($this->university->id, [['max_days_per_year', 14, 'leave_type', 'eq', 'LC5', 0]], 'HR-LEAVE-UNI');

        $this->assertSame([], $this->evaluate('LC5', '2026-10-01', '2026-10-14')['errors'], 'the university raised special leave from 10 to 14 days');
        $this->assertArrayHasKey('end_date', $this->evaluate('LC5', '2026-10-01', '2026-10-15')['errors']);
    }

    public function test_a_single_request_can_be_capped_for_chosen_leave_types_only(): void
    {
        $this->rules($this->university->id, [['max_consecutive_leave_days', 14, 'leave_type', 'eq', 'LC1', 0]], 'HR-LEAVE-UNI');

        $this->assertSame([], $this->evaluate('LC1', '2026-10-01', '2026-10-14')['errors']);

        $error = $this->evaluate('LC1', '2026-10-01', '2026-10-15')['errors']['end_date'];
        $this->assertStringContainsString('cannot be longer than 14 days', $error);

        $this->assertSame([], $this->evaluate('LC4', '2026-10-01', '2026-12-23')['errors'], 'maternity is not capped by the annual-leave rule');
    }

    public function test_the_return_date_is_the_day_after_leave_ends_unless_policy_lets_staff_choose(): void
    {
        $result = $this->evaluate('LC1', '2026-10-01', '2026-10-10', '2026-10-20');

        $this->assertSame([], $result['errors'], 'a chosen date is ignored, not rejected, when the system sets it');
        $this->assertSame('2026-10-11', $result['return_date']->toDateString());
    }

    public function test_when_staff_choose_their_return_date_it_must_fall_inside_the_allowed_window(): void
    {
        $this->rules($this->university->id, [
            ['return_date_rule', 'employee'],
            ['max_return_gap_days', 3],
        ], 'HR-LEAVE-UNI');

        // Leave ends Sat 10 Oct: the earliest return is 11th, the latest 14th.
        $this->assertSame('2026-10-14', $this->evaluate('LC1', '2026-10-01', '2026-10-10', '2026-10-14')['return_date']->toDateString());
        $this->assertSame('2026-10-11', $this->evaluate('LC1', '2026-10-01', '2026-10-10')['return_date']->toDateString(), 'left blank: the next day');

        foreach (['2026-10-10', '2026-10-15', '2026-10-20'] as $tooEarlyOrLate) {
            $errors = $this->evaluate('LC1', '2026-10-01', '2026-10-10', $tooEarlyOrLate)['errors'];

            $this->assertArrayHasKey('expected_return_date', $errors, $tooEarlyOrLate);
            $this->assertStringContainsString('between 11 Oct 2026 and 14 Oct 2026', $errors['expected_return_date']);
        }
    }

    public function test_the_form_gets_the_same_limits_the_server_enforces(): void
    {
        $forForm = app(LeavePolicy::class)->forForm($this->staff, collect($this->types)->values());

        $this->assertSame('2026-09-21', $forForm['today']);

        $annual = $forForm['types'][$this->types['LC1']->id];
        $this->assertSame(3, $annual['min_notice_days']);
        $this->assertSame(28, $annual['yearly_limit']);
        $this->assertNull($annual['max_consecutive_days']);
        $this->assertSame('computed', $annual['return_date_rule']);
        $this->assertEquals([2026 => 0, 2027 => 0], $annual['used']);

        $this->assertSame(0, $forForm['types'][$this->types['LC2']->id]['min_notice_days']);
    }

    private function submission(array $overrides = []): array
    {
        return array_merge([
            'leave_type_id' => $this->types['LC1']->id,
            'start_date' => '2026-10-01',
            'end_date' => '2026-10-05',
            'reason' => 'Family matters to attend to.',
            'acting_staff_id' => $this->colleague->id,
            'handover_notes' => 'Please cover the department meeting on Tuesday.',
            'emergency_contact_name' => 'Jane Doe',
            'emergency_contact_phone' => '0712345678',
            'contact_number_during_leave' => '0798765432',
            'leave_address' => '12 Uhuru Street, Morogoro',
            'travel_destination' => 'Morogoro',
        ], $overrides);
    }

    private function applyAs(User $user, array $data)
    {
        Permission::findOrCreate('apply_leave', 'web');
        $user->givePermissionTo('apply_leave');

        return $this->actingAs($user)->post(route('leave-requests.store'), $data);
    }

    public function test_submitting_with_too_little_notice_is_refused_and_nothing_is_saved(): void
    {
        $this->applyAs($this->staff, $this->submission(['start_date' => '2026-09-22', 'end_date' => '2026-09-24']))
            ->assertSessionHasErrors('start_date');

        $this->assertSame(0, LeaveRequest::count());
    }

    public function test_submitting_beyond_the_yearly_entitlement_is_refused(): void
    {
        $this->applyAs($this->staff, $this->submission(['start_date' => '2026-10-01', 'end_date' => '2026-10-30']))
            ->assertSessionHasErrors('end_date');

        $this->assertSame(0, LeaveRequest::count());
    }

    public function test_a_valid_request_is_saved_with_the_return_date_the_policy_dictates(): void
    {
        $this->applyAs($this->staff, $this->submission(['expected_return_date' => '2026-10-20']))
            ->assertSessionHasNoErrors();

        $request = LeaveRequest::firstOrFail();

        $this->assertSame(5, (int) $request->days);
        $this->assertSame('2026-10-06', $request->expected_return_date->toDateString());
    }

    public function test_the_application_form_carries_the_limits_for_the_applicant(): void
    {
        Permission::findOrCreate('apply_leave', 'web');
        $this->staff->givePermissionTo('apply_leave');

        $this->actingAs($this->staff)->get(route('leave-requests.create'))
            ->assertOk()
            ->assertSee('policyLimits', false)
            ->assertSee('"min_notice_days":3', false)
            ->assertSee('id="return_date_hint"', false);
    }

    // ---- HR adjusting the days of a submitted request ----------------------

    private function pendingAtHr(string $start, string $end, string $type = 'LC1'): LeaveRequest
    {
        return LeaveRequest::forceCreate([
            'staff_id' => $this->staff->id,
            'leave_type_id' => $this->types[$type]->id,
            'start_date' => $start,
            'end_date' => $end,
            'days' => Carbon::parse($start)->diffInDays(Carbon::parse($end)) + 1,
            'reason' => 'Test leave',
            'status' => 'pending',
            'current_approval_level' => LeaveRequest::LEVEL_HR,
        ]);
    }

    private function hrAdjusts(LeaveRequest $request, float $days, ?string $reason = null)
    {
        Permission::findOrCreate('approve_hr_level', 'web');
        // Whoever the request goes to next has to be findable by role.
        foreach (['dvca', 'dvc-pfa', 'hr-officer', 'hr-manager'] as $role) {
            Role::findOrCreate($role, 'web');
        }
        $hr = User::factory()->create(['university_id' => $this->university->id]);
        $hr->givePermissionTo('approve_hr_level');
        $hr->assignRole(Role::findOrCreate('hr-officer', 'web'));

        return $this->actingAs($hr)->put(route('leave-requests.hr-review.process', $request->id), array_filter([
            'action' => 'approve',
            'adjusted_days' => $days,
            'adjustment_reason' => $reason,
            'payment_status' => 'paid',
        ], fn ($value) => $value !== null));
    }

    public function test_hr_cannot_raise_the_days_beyond_the_yearly_entitlement(): void
    {
        $request = $this->pendingAtHr('2026-10-01', '2026-10-20'); // 20 days, limit 28

        $this->hrAdjusts($request, 29)->assertSessionHas('error');

        $request->refresh();
        $this->assertEquals(20, $request->days);
        $this->assertSame(LeaveRequest::LEVEL_HR, (int) $request->current_approval_level, 'the request must not move on');
        $this->assertNull($request->payment_decided_by, 'nothing HR entered may be half-saved');
    }

    public function test_the_request_being_adjusted_is_not_counted_against_itself(): void
    {
        $request = $this->pendingAtHr('2026-10-01', '2026-10-20'); // already on record as 20 days

        $this->hrAdjusts($request, 28)->assertSessionMissing('error');

        $this->assertEquals(28, $request->fresh()->days);
    }

    public function test_hr_adjusting_upwards_counts_the_days_already_taken_in_the_year(): void
    {
        $this->takeLeave('LC1', '2026-03-01', '2026-03-10'); // 10 days used
        $request = $this->pendingAtHr('2026-10-01', '2026-10-10'); // 10 more

        $this->hrAdjusts($request, 19)->assertSessionHas('error'); // 10 + 19 > 28
        $this->assertEquals(10, $request->fresh()->days);

        $this->hrAdjusts($request, 18)->assertSessionMissing('error'); // 10 + 18 = 28
        $this->assertEquals(18, $request->fresh()->days);
    }

    public function test_hr_cannot_raise_a_request_beyond_a_single_request_cap(): void
    {
        $this->rules($this->university->id, [['max_consecutive_leave_days', 14, 'leave_type', 'eq', 'LC1', 0]], 'HR-LEAVE-UNI');
        $request = $this->pendingAtHr('2026-10-01', '2026-10-10');

        $this->hrAdjusts($request, 15)->assertSessionHas('error');
        $this->assertEquals(10, $request->fresh()->days);
    }

    public function test_hr_can_always_grant_fewer_days_even_when_the_request_was_already_over_a_limit(): void
    {
        // Submitted before the limit existed: 30 days against an entitlement of 28.
        $request = $this->pendingAtHr('2026-10-01', '2026-10-30');

        $this->hrAdjusts($request, 29)->assertSessionMissing('error');
        $this->assertEquals(29, $request->fresh()->days);
    }

    public function test_every_change_of_days_is_written_into_the_request_history_with_its_original_value(): void
    {
        $request = $this->pendingAtHr('2026-10-01', '2026-10-10');

        $this->hrAdjusts($request, 4, 'low_balance')->assertSessionMissing('error');
        $withReason = ApprovalLog::where('request_id', $request->id)->firstOrFail();
        $this->assertStringContainsString('10 → 4 days. Reason: low_balance', $withReason->comment);

        $second = $this->pendingAtHr('2026-11-01', '2026-11-08');
        $this->hrAdjusts($second, 6)->assertSessionMissing('error');
        $withoutReason = ApprovalLog::where('request_id', $second->id)->firstOrFail();
        $this->assertStringContainsString('8 → 6 days', $withoutReason->comment);
        $this->assertStringNotContainsString('Reason:', $withoutReason->comment);
    }

    public function test_approving_without_changing_the_days_is_not_checked_or_logged_as_an_adjustment(): void
    {
        $request = $this->pendingAtHr('2026-10-01', '2026-10-30'); // over the limit, but HR is not changing it

        $this->hrAdjusts($request, 30)->assertSessionMissing('error');

        $this->assertStringNotContainsString('Day Adjustment', (string) ApprovalLog::where('request_id', $request->id)->firstOrFail()->comment);
    }

    // ---- half days ---------------------------------------------------------

    private function halfDay(string $type, string $halfDay = 'morning', string $date = '2026-10-05'): array
    {
        return app(LeavePolicy::class)->evaluate($this->staff, $this->types[$type], Carbon::parse($date), Carbon::parse($date), null, null, $halfDay);
    }

    public function test_half_days_are_a_policy_setting_that_differs_by_leave_type(): void
    {
        $policy = app(LeavePolicy::class);

        $this->assertTrue($policy->limitsFor($this->staff, $this->types['LC1'])['allow_half_day']);
        $this->assertFalse($policy->limitsFor($this->staff, $this->types['LC4'])['allow_half_day'], 'no rule for maternity: the catalog default is no');
    }

    public function test_a_half_day_counts_as_half_a_day(): void
    {
        $result = $this->halfDay('LC1');

        $this->assertSame([], $result['errors']);
        $this->assertSame(0.5, $result['days']);
        $this->assertSame('morning', $result['half_day']);
    }

    public function test_half_days_count_towards_the_yearly_entitlement(): void
    {
        $this->takeLeave('LC1', '2026-03-01', '2026-03-27'); // 27 days used

        $this->assertSame([], $this->halfDay('LC1', 'afternoon')['errors'], '27 + 0.5 is within 28');
        $this->assertSame([], $this->evaluate('LC1', '2026-10-05', '2026-10-05')['errors'], '27 + 1 is exactly 28');

        $this->takeLeave('LC1', '2026-04-01', '2026-04-01'); // 28 used

        $this->assertStringContainsString('total 28.5', $this->halfDay('LC1')['errors']['end_date']);
    }

    public function test_a_half_day_is_refused_where_the_policy_does_not_allow_it(): void
    {
        $this->assertStringContainsString('not allowed for Maternity/Paternity Leave', $this->halfDay('LC4')['errors']['half_day']);
    }

    public function test_a_universitys_own_policy_can_switch_half_days_off_or_on(): void
    {
        $this->rules($this->university->id, [
            ['allow_half_day', 'false', 'leave_type', 'eq', 'LC1', 10],
            ['allow_half_day', 'true', 'leave_type', 'eq', 'LC4', 10],
        ], 'HR-LEAVE-UNI');

        $this->assertArrayHasKey('half_day', $this->halfDay('LC1')['errors']);
        $this->assertSame([], $this->halfDay('LC4')['errors']);
    }

    public function test_a_half_day_must_be_a_single_day(): void
    {
        $result = app(LeavePolicy::class)->evaluate($this->staff, $this->types['LC1'], Carbon::parse('2026-10-05'), Carbon::parse('2026-10-06'), null, null, 'morning');

        $this->assertSame('A half day must start and end on the same day.', $result['errors']['half_day']);
    }

    public function test_submitting_a_half_day_saves_half_a_day(): void
    {
        $this->applyAs($this->staff, $this->submission(['start_date' => '2026-10-05', 'end_date' => '2026-10-05', 'half_day' => 'afternoon']))
            ->assertSessionHasNoErrors();

        $request = LeaveRequest::firstOrFail();

        $this->assertEquals(0.5, $request->days);
        $this->assertSame('afternoon', $request->half_day);
    }

    public function test_submitting_a_half_day_where_it_is_not_allowed_is_refused(): void
    {
        $this->applyAs($this->staff, $this->submission(['leave_type_id' => $this->types['LC4']->id, 'start_date' => '2026-10-05', 'end_date' => '2026-10-05', 'half_day' => 'morning']))
            ->assertSessionHasErrors('half_day');

        $this->assertSame(0, LeaveRequest::count());
    }

    public function test_an_ordinary_request_is_saved_as_full_days(): void
    {
        $this->applyAs($this->staff, $this->submission())->assertSessionHasNoErrors();

        $request = LeaveRequest::firstOrFail();

        $this->assertEquals(5, $request->days);
        $this->assertSame('full', $request->half_day);
    }

    public function test_the_form_offers_half_days_only_with_the_policy_flag(): void
    {
        $forForm = app(LeavePolicy::class)->forForm($this->staff, collect($this->types)->values());

        $this->assertTrue($forForm['types'][$this->types['LC1']->id]['allow_half_day']);
        $this->assertFalse($forForm['types'][$this->types['LC4']->id]['allow_half_day']);

        Permission::findOrCreate('apply_leave', 'web');
        $this->staff->givePermissionTo('apply_leave');

        $this->actingAs($this->staff)->get(route('leave-requests.create'))
            ->assertOk()
            ->assertSee('id="half_day"', false)
            ->assertSee('"allow_half_day":true', false);
    }

    public function test_hr_can_grant_part_of_a_day_only_where_half_days_are_allowed(): void
    {
        $annual = $this->pendingAtHr('2026-10-01', '2026-10-05'); // annual: allowed
        $this->hrAdjusts($annual, 2.5)->assertSessionMissing('error');
        $this->assertEquals(2.5, $annual->fresh()->days);

        $maternity = $this->pendingAtHr('2026-11-01', '2026-11-05', 'LC4'); // not allowed
        $this->hrAdjusts($maternity, 2.5)->assertSessionHas('error');
        $this->assertEquals(5, $maternity->fresh()->days);

        $this->hrAdjusts($maternity, 3)->assertSessionMissing('error');
        $this->assertEquals(3, $maternity->fresh()->days, 'whole days are always fine');
    }

    public function test_granting_a_half_day_request_as_whole_days_clears_its_half_day_mark(): void
    {
        $request = $this->pendingAtHr('2026-10-05', '2026-10-05');
        $request->forceFill(['days' => 0.5, 'half_day' => 'morning'])->save();

        $this->hrAdjusts($request, 1)->assertSessionMissing('error');

        $request->refresh();
        $this->assertEquals(1, $request->days);
        $this->assertSame('full', $request->half_day);
    }

    public function test_hr_leaving_a_half_day_request_unchanged_keeps_it_a_half_day(): void
    {
        $request = $this->pendingAtHr('2026-10-05', '2026-10-05', 'LC4');
        $request->forceFill(['days' => 0.5, 'half_day' => 'morning'])->save();

        $this->hrAdjusts($request, 0.5)->assertSessionMissing('error');

        $this->assertSame('morning', $request->fresh()->half_day);
    }

    // ---- the wizard's "Next" cannot reach Review/Sign on an empty draft ----

    private function saveDraft(array $data)
    {
        Permission::findOrCreate('apply_leave', 'web');
        $this->staff->givePermissionTo('apply_leave');

        return $this->actingAs($this->staff)->post(route('leave-requests.draft.save'), $data);
    }

    public function test_clicking_next_on_an_empty_form_does_not_reach_review(): void
    {
        $this->saveDraft(['current_step' => 2])->assertSessionHas('error');

        $draft = LeaveRequestDraft::where('staff_id', $this->staff->id)->firstOrFail();
        $this->assertSame(1, $draft->current_step);
    }

    public function test_clicking_next_with_only_some_fields_filled_still_does_not_reach_review(): void
    {
        $this->saveDraft([
            'current_step' => 2,
            'leave_type_id' => $this->types['LC1']->id,
            'start_date' => '2026-10-05',
            'end_date' => '2026-10-10',
            // handover_notes and reason left out.
        ])->assertSessionHas('error');

        $draft = LeaveRequestDraft::where('staff_id', $this->staff->id)->firstOrFail();
        $this->assertSame(1, $draft->current_step);
        // What was typed is still kept, not thrown away.
        $this->assertSame('2026-10-05', $draft->form_data['start_date']);
    }

    public function test_next_reaches_review_once_the_essentials_are_filled(): void
    {
        $this->saveDraft([
            'current_step' => 2,
            'leave_type_id' => $this->types['LC1']->id,
            'start_date' => '2026-10-05',
            'end_date' => '2026-10-10',
            'emergency_contact_name' => 'Jane Doe',
            'emergency_contact_phone' => '0712345678',
            'contact_number_during_leave' => '0798765432',
            'leave_address' => '12 Uhuru Street, Morogoro',
            'travel_destination' => 'Morogoro',
            'handover_notes' => 'Cover the department meeting.',
            'reason' => 'Family matters to attend to.',
        ])->assertSessionMissing('error');

        $this->assertSame(2, LeaveRequestDraft::where('staff_id', $this->staff->id)->firstOrFail()->current_step);
    }

    public function test_going_back_to_step_one_is_never_blocked(): void
    {
        $this->saveDraft(['current_step' => 1])->assertSessionMissing('error');

        $this->assertSame(1, LeaveRequestDraft::where('staff_id', $this->staff->id)->firstOrFail()->current_step);
    }

    // ---- a hand-typed emergency contact is kept for next time --------------

    public function test_a_hand_typed_emergency_contact_is_saved_for_next_time(): void
    {
        $this->applyAs($this->staff, $this->submission([
            'emergency_contact_name' => 'Jane Doe',
            'emergency_contact_phone' => '0712345678',
        ]))->assertSessionHasNoErrors();

        $contact = StaffEmergencyContact::where('staff_id', $this->staff->id)->firstOrFail();
        $this->assertSame('Jane Doe', $contact->full_name);
        $this->assertSame('0712345678', $contact->phone_primary);
    }

    public function test_submitting_the_same_hand_typed_contact_again_does_not_duplicate_it(): void
    {
        $this->applyAs($this->staff, $this->submission([
            'start_date' => '2026-10-05', 'end_date' => '2026-10-06',
            'emergency_contact_name' => ' Jane Doe ', 'emergency_contact_phone' => '0712 345 678',
        ]));

        // Only one leave request may be in flight at a time - clear the first
        // one so the second submission below isn't blocked by that, unrelated
        // rule.
        LeaveRequest::where('staff_id', $this->staff->id)->update(['status' => 'rejected']);

        $this->applyAs($this->staff, $this->submission([
            'start_date' => '2026-11-05', 'end_date' => '2026-11-06',
            'emergency_contact_name' => 'jane doe', 'emergency_contact_phone' => '0712345678',
        ]));

        $this->assertSame(1, StaffEmergencyContact::where('staff_id', $this->staff->id)->count());
    }

    public function test_picking_an_existing_saved_contact_does_not_create_another_one(): void
    {
        $existing = StaffEmergencyContact::create([
            'staff_id' => $this->staff->id, 'full_name' => 'Jane Doe', 'relationship' => 'Spouse', 'phone_primary' => '0712345678', 'priority' => 5,
        ]);

        $this->applyAs($this->staff, $this->submission(['emergency_contact_id' => $existing->id]))
            ->assertSessionHasNoErrors();

        $this->assertSame(1, StaffEmergencyContact::where('staff_id', $this->staff->id)->count());
    }

    public function test_emergency_and_leave_contact_fields_are_required_for_a_self_service_submission(): void
    {
        foreach (['emergency_contact_name', 'emergency_contact_phone', 'contact_number_during_leave', 'leave_address', 'travel_destination'] as $field) {
            $this->applyAs($this->staff, $this->submission([$field => '']))->assertSessionHasErrors($field);
        }

        $this->assertSame(0, LeaveRequest::count());
        $this->assertSame(0, StaffEmergencyContact::count());
    }
}
