<?php

namespace App\Services;

use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * The one place a leave request is checked against policy.
 *
 * Every limit here comes from the HR policy of the applicant's university
 * (see PolicyEngine for how a university's own rules, the platform's, and the
 * catalog defaults are layered) - nothing is decided by the form. The
 * application form shows the same limits live (forForm), but this class is
 * what actually enforces them, so a hand-crafted request gets no further.
 */
class LeavePolicy
{
    /**
     * Leave types whose yearly entitlement has a setting of its own.
     * Every other type is limited by "max_days_per_year", which a policy rule
     * can set per leave type and which otherwise falls back to the type's own
     * allowance (leave_types.days_allowed).
     */
    public const ENTITLEMENT_SETTINGS = [
        'LC1' => 'max_annual_leave',
        'LC2' => 'max_sick_leave',
        'LC3' => 'max_compassionate_leave',
    ];

    private const CATEGORY = 'hr_policy';

    public function __construct(private PolicyEngine $engine) {}

    /**
     * What applies to this applicant for this type of leave.
     *
     * @return array{
     *     min_notice_days: int,
     *     max_consecutive_days: int|null,
     *     yearly_limit: int|null,
     *     return_date_rule: string,
     *     max_return_gap_days: int,
     *     allow_half_day: bool,
     * }
     */
    public function limitsFor(User $applicant, LeaveType $type): array
    {
        $context = $this->contextFor($applicant, $type);
        $yearlySetting = self::ENTITLEMENT_SETTINGS[$type->code] ?? 'max_days_per_year';

        return [
            'min_notice_days' => (int) $this->engine->get('min_notice_days', $context, null, self::CATEGORY),
            'max_consecutive_days' => $this->nullableInt($this->engine->get('max_consecutive_leave_days', $context, null, self::CATEGORY)),
            'yearly_limit' => $this->nullableInt($this->engine->get(
                $yearlySetting,
                $context,
                $yearlySetting === 'max_days_per_year' ? $type->days_allowed : null,
                self::CATEGORY
            )),
            'return_date_rule' => (string) $this->engine->get('return_date_rule', $context, null, self::CATEGORY),
            'max_return_gap_days' => (int) $this->engine->get('max_return_gap_days', $context, null, self::CATEGORY),
            'allow_half_day' => filter_var($this->engine->get('allow_half_day', $context, null, self::CATEGORY), FILTER_VALIDATE_BOOLEAN),
        ];
    }

    /**
     * Days already taken (or awaiting a decision) in a calendar year for one
     * leave type. A request counts in the year it starts.
     */
    public function daysUsedInYear(User $applicant, LeaveType $type, int $year, ?int $exceptRequestId = null): float
    {
        return (float) LeaveRequest::where('staff_id', $applicant->id)
            ->where('leave_type_id', $type->id)
            ->whereYear('start_date', $year)
            ->whereNotIn('status', ['rejected', 'cancelled'])
            ->when($exceptRequestId, fn ($query) => $query->whereKeyNot($exceptRequestId))
            ->sum('days');
    }

    /**
     * Whether HR may change an already-submitted request to this many days,
     * and if not, why.
     *
     * HR reviewing a request may grant fewer days than were asked for (the
     * adjustment reasons are all of that kind), and that can never break a
     * limit. Granting *more* is held to the same limits as applying for that
     * many in the first place: longest single request, and the yearly
     * entitlement - counted without this request itself, which is already
     * on record.
     *
     * Part of a day can only be granted where the policy allows half-day
     * leave for this person and leave type; elsewhere the days stay whole.
     */
    public function adjustmentError(LeaveRequest $leaveRequest, float $days): ?string
    {
        $current = (float) $leaveRequest->days;

        if ($days === $current) {
            return null;
        }

        $applicant = $leaveRequest->staff;
        $type = $leaveRequest->leaveType;
        $limits = $this->limitsFor($applicant, $type);

        if (fmod($days, 1.0) !== 0.0 && ! $limits['allow_half_day']) {
            return sprintf('Half days are not allowed for %s, so the days must be a whole number.', $type->name);
        }

        if ($days < $current) {
            return null;
        }

        if ($limits['max_consecutive_days'] !== null && $days > $limits['max_consecutive_days']) {
            return $this->consecutiveMessage($type, $limits['max_consecutive_days'], $days);
        }

        if ($limits['yearly_limit'] !== null) {
            $year = Carbon::parse($leaveRequest->start_date)->year;
            $used = $this->daysUsedInYear($applicant, $type, $year, $leaveRequest->id);

            if ($used + $days > $limits['yearly_limit']) {
                return $this->yearlyMessage($type, $limits['yearly_limit'], $year, $used, $days);
            }
        }

        return null;
    }

    /**
     * Check a request against policy.
     *
     * `errors` is keyed by the form field each message belongs to;
     * `return_date` is the date to store as the expected return.
     *
     * `$halfDay` is 'morning' or 'afternoon' for half a day of leave (which
     * the policy has to allow, and which must start and end on the same day),
     * and 'full' otherwise.
     *
     * @return array{
     *     errors: array<string, string>,
     *     days: int|float,
     *     half_day: string,
     *     return_date: Carbon,
     *     limits: array<string, mixed>,
     * }
     */
    public function evaluate(User $applicant, LeaveType $type, Carbon $start, Carbon $end, ?Carbon $requestedReturn = null, ?Carbon $today = null, string $halfDay = 'full'): array
    {
        $start = $start->copy()->startOfDay();
        $end = $end->copy()->startOfDay();
        $today = ($today ?? Carbon::today())->copy()->startOfDay();

        $limits = $this->limitsFor($applicant, $type);
        $isHalfDay = in_array($halfDay, ['morning', 'afternoon'], true);
        $days = $isHalfDay ? 0.5 : (int) $start->diffInDays($end) + 1;
        $errors = [];

        if ($isHalfDay && ! $limits['allow_half_day']) {
            $errors['half_day'] = sprintf('Half-day leave is not allowed for %s.', $type->name);
        } elseif ($isHalfDay && ! $start->equalTo($end)) {
            $errors['half_day'] = 'A half day must start and end on the same day.';
        }

        $earliestStart = $today->copy()->addDays($limits['min_notice_days']);
        if ($start->lt($earliestStart)) {
            $errors['start_date'] = sprintf(
                '%s must be requested at least %s before it starts, so the department can arrange cover. The earliest start date is %s.',
                $type->name,
                $this->days($limits['min_notice_days']),
                $earliestStart->format('d M Y')
            );
        }

        if ($limits['max_consecutive_days'] !== null && $days > $limits['max_consecutive_days']) {
            $errors['end_date'] = $this->consecutiveMessage($type, $limits['max_consecutive_days'], $days);
        }

        if ($limits['yearly_limit'] !== null) {
            $used = $this->daysUsedInYear($applicant, $type, $start->year);

            if ($used + $days > $limits['yearly_limit']) {
                $errors['end_date'] ??= $this->yearlyMessage($type, $limits['yearly_limit'], $start->year, $used, $days);
            }
        }

        $dayAfterLeave = $end->copy()->addDay();
        $returnDate = $dayAfterLeave;

        if ($limits['return_date_rule'] === 'employee' && $requestedReturn) {
            $requestedReturn = $requestedReturn->copy()->startOfDay();
            $latestReturn = $dayAfterLeave->copy()->addDays($limits['max_return_gap_days']);

            if ($requestedReturn->lt($dayAfterLeave) || $requestedReturn->gt($latestReturn)) {
                $errors['expected_return_date'] = sprintf(
                    'The return date must be between %s and %s.',
                    $dayAfterLeave->format('d M Y'),
                    $latestReturn->format('d M Y')
                );
            } else {
                $returnDate = $requestedReturn;
            }
        }

        return [
            'errors' => $errors,
            'days' => $days,
            'half_day' => $isHalfDay ? $halfDay : 'full',
            'return_date' => $returnDate,
            'limits' => $limits,
        ];
    }

    /**
     * Everything the application form needs to show the limits live, keyed by
     * leave type id. The form only guides; evaluate() decides.
     *
     * @param  Collection<int, LeaveType>  $leaveTypes
     * @return array{today: string, types: array<int, array<string, mixed>>}
     */
    public function forForm(User $applicant, Collection $leaveTypes, ?Carbon $today = null): array
    {
        $today = ($today ?? Carbon::today())->copy()->startOfDay();
        $years = [$today->year, $today->year + 1];

        return [
            'today' => $today->toDateString(),
            'types' => $leaveTypes->mapWithKeys(fn (LeaveType $type) => [
                $type->id => $this->limitsFor($applicant, $type) + [
                    'name' => $type->name,
                    'used' => collect($years)->mapWithKeys(fn (int $year) => [
                        $year => $this->daysUsedInYear($applicant, $type, $year),
                    ])->all(),
                ],
            ])->all(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function contextFor(User $applicant, LeaveType $type): array
    {
        return [
            // The applicant's university, not the submitter's - a request
            // filed on someone's behalf follows that person's rules.
            'university_id' => $applicant->university_id,
            'staff_category' => $applicant->staff_category,
            'contract_type' => $applicant->contract_type,
            'leave_type' => $type->code,
        ];
    }

    private function nullableInt(mixed $value): ?int
    {
        return $value === null || $value === '' ? null : (int) $value;
    }

    private function consecutiveMessage(LeaveType $type, int $limit, float $days): string
    {
        return sprintf(
            'A single %s request cannot be longer than %s. This one is %s.',
            $type->name,
            $this->days($limit),
            $this->days($days)
        );
    }

    private function yearlyMessage(LeaveType $type, int $limit, int $year, float $used, float $days): string
    {
        return sprintf(
            '%s is limited to %s in %d. %s already used or awaiting approval, and this request is for %s (total %s).',
            $type->name,
            $this->days($limit),
            $year,
            $this->days($used),
            $this->days($days),
            $this->number($used + $days)
        );
    }

    /**
     * "1 day", "12 days", "0.5 days" - leave can be granted in half days.
     */
    private function days(int|float $count): string
    {
        return $this->number($count).' '.((float) $count === 1.0 ? 'day' : 'days');
    }

    private function number(int|float $value): string
    {
        return rtrim(rtrim(number_format($value, 1, '.', ''), '0'), '.');
    }
}
