<?php

namespace App\Models;

use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class TravelPermission extends Model
{
    use HasFactory;

    protected $table = 'travel_permissions';

    protected $fillable = [
        'staff_id',
        'acting_staff_id',
        'acting_dean_id',
        'destination',
        'start_date',
        'end_date',
        'days',
        'travel_reason',
        'official_benefit',
        'personal_description',
        'cost_coverage',
        'additional_details',
        'letter_content',
        'handover_notes',
        'acting_status',
        'acting_responded_at',
        'acting_token',
        'acting_comment',
        'status',
        'current_approval_level',
        'rejection_reason',
        'invitation_letter_path',
        'endorsement_path',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'acting_responded_at' => 'datetime',
        // Without this, the decimal(5,2) column returns as a raw string
        // like "3.00" - reads like a price next to a whole day count.
        'days' => 'float',
    ];

    // Relationships
    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    public function actingStaff()
    {
        return $this->belongsTo(User::class, 'acting_staff_id');
    }

    /**
     * HOD standing in as acting Dean, when the applicant is their own
     * school's Dean and would otherwise have to approve their own request.
     */
    public function actingDean()
    {
        return $this->belongsTo(User::class, 'acting_dean_id');
    }

    // Accessor for days calculation
    public function getDaysAttribute()
    {
        // diffInWeekdays()+1 overcounts whenever the end date falls on a
        // weekend - count each calendar day in the range directly instead.
        return CarbonPeriod::create($this->start_date, $this->end_date)->filter(fn ($d) => ! $d->isWeekend())->count();
    }

    /**
     * Check if the given user can approve/reject this request at its current level.
     * Mirrors scopePendingForRole()'s rule for a single record.
     */
    public function canUserApprove($user)
    {
        $role = $user->getRoleNames()->first();

        if ($role == 'admin') {
            return $user->hasRole('super-admin') || $this->staff->university_id == $user->university_id;
        }

        // Self-collision stand-ins: when the applicant is their own
        // department's Unit Head/HOD or their own school's Dean, the
        // person reviewing at that stage isn't necessarily an
        // unit_head/hod/dean by role - authorize them directly against the
        // specific stand-in field rather than falling through to the role
        // checks below, which would otherwise match nobody or the applicant.
        if ($this->current_approval_level === 'unit_head'
            && ($this->staff->department->unit_head_staff_id ?? null) == $this->staff_id) {
            return $user->id == $this->acting_staff_id;
        }

        if ($this->current_approval_level === 'hod'
            && ($this->staff->department->hod_staff_id ?? null) == $this->staff_id) {
            return $user->id == $this->acting_staff_id;
        }

        if ($this->current_approval_level === 'dean'
            && ($this->staff->department->school->dean_staff_id ?? null) == $this->staff_id) {
            return $user->id == $this->acting_dean_id;
        }

        switch ($role) {
            case 'unit_head':
                return $this->current_approval_level === 'unit_head'
                    && $this->staff->department && $this->staff->department->unit_head_staff_id == $user->id;
            case 'hod':
                return $this->current_approval_level === 'hod'
                    && $this->staff->department && $this->staff->department->hod_staff_id == $user->id;
            case 'dean':
                return $this->current_approval_level === 'dean'
                    && $this->staff->department && $this->staff->department->school
                    && $this->staff->department->school->dean_staff_id == $user->id;
            case 'dvca':
                // staff_category alone has no institute boundary - a DVC at
                // one university must never approve travel for another. A
                // DVC also can't approve their own travel request - any
                // other dvca at the same university can still act.
                return $this->current_approval_level === 'dvc' && $this->staff->staff_category == 'academic'
                    && $this->staff->university_id == $user->university_id
                    && $this->staff_id != $user->id;
            case 'dvc-pfa':
                return $this->current_approval_level === 'dvc' && $this->staff->staff_category == 'administrative'
                    && $this->staff->university_id == $user->university_id
                    && $this->staff_id != $user->id;
            case 'hr-officer':
            case 'hr-manager':
                // Same principle as the dvca/dvc-pfa cases above - an HR
                // officer can't approve their own travel request at the
                // final stage; any other HR officer at the same university
                // still can.
                return $this->current_approval_level === 'hr_final'
                    && $this->staff->university_id == $user->university_id
                    && $this->staff_id != $user->id;
            default:
                return false;
        }
    }

    // Scope for pending approvals by role
    public function scopePendingForRole($query, $user)
    {
        $role = $user->getRoleNames()->first();

        // Self-collision stand-ins: this person isn't necessarily
        // unit_head/hod/dean by role, but they're who an applicant (who IS
        // their own department's Unit Head/HOD, or their own school's
        // Dean) picked to stand in for this one review. Without this, the
        // notification sent to them would point at a queue that never
        // shows the request.
        $standinIds = self::standinApprovalIdsFor($user);

        switch ($role) {
            case 'admin':
                return $user->hasRole('super-admin') ? $query : $query->whereHas('staff', function ($q) use ($user) {
                    $q->where('university_id', $user->university_id);
                });
            case 'unit_head':
                $query->where('current_approval_level', 'unit_head')
                    ->where('staff_id', '!=', $user->id)
                    ->whereHas('staff.department', function ($q) use ($user) {
                        $q->where('unit_head_staff_id', $user->id);
                    });
                break;
            case 'hod':
                $query->where('current_approval_level', 'hod')
                    ->where('staff_id', '!=', $user->id)
                    ->whereHas('staff.department', function ($q) use ($user) {
                        $q->where('hod_staff_id', $user->id);
                    });
                break;
            case 'dean':
                $query->where('current_approval_level', 'dean')
                    ->where('staff_id', '!=', $user->id)
                    ->whereHas('staff.department.school', function ($q) use ($user) {
                        $q->where('dean_staff_id', $user->id);
                    });
                break;
            case 'dvca':
                return $query->where('current_approval_level', 'dvc')
                    ->where('staff_id', '!=', $user->id)
                    ->whereHas('staff', function ($q) use ($user) {
                        $q->where('staff_category', 'academic')
                            ->where('university_id', $user->university_id);
                    });
            case 'dvc-pfa':
                return $query->where('current_approval_level', 'dvc')
                    ->where('staff_id', '!=', $user->id)
                    ->whereHas('staff', function ($q) use ($user) {
                        $q->where('staff_category', 'administrative')
                            ->where('university_id', $user->university_id);
                    });
            case 'hr-officer':
            case 'hr-manager':
                return $query->where('current_approval_level', 'hr_final')
                    ->where('staff_id', '!=', $user->id)
                    ->whereHas('staff', function ($q) use ($user) {
                        $q->where('university_id', $user->university_id);
                    });
            default:
                return $standinIds->isEmpty() ? $query->whereRaw('1 = 0') : $query->whereIn('id', $standinIds);
        }

        if ($standinIds->isNotEmpty()) {
            $query->orWhere(function ($q) use ($standinIds) {
                $q->whereIn('id', $standinIds)
                    ->where('status', 'pending')
                    ->where('acting_status', 'accepted');
            });
        }

        return $query;
    }

    /**
     * IDs of pending travel requests where $user is the designated
     * stand-in reviewer (acting Unit Head/HOD/Dean) for an applicant who
     * is their own department's Unit Head/HOD, or their own school's
     * Dean. Re-verifies the collision in PHP rather than trusting the
     * stand-in fields alone, in case the real seat-holder changed after
     * submission.
     */
    public static function standinApprovalIdsFor(User $user): Collection
    {
        return self::where('status', 'pending')
            ->where('acting_status', 'accepted')
            ->where(function ($q) use ($user) {
                $q->where(function ($uhQ) use ($user) {
                    $uhQ->where('current_approval_level', 'unit_head')->where('acting_staff_id', $user->id);
                })->orWhere(function ($hodQ) use ($user) {
                    $hodQ->where('current_approval_level', 'hod')->where('acting_staff_id', $user->id);
                })->orWhere(function ($deanQ) use ($user) {
                    $deanQ->where('current_approval_level', 'dean')->where('acting_dean_id', $user->id);
                });
            })
            ->with('staff.department.school')
            ->get()
            ->filter(function ($travelRequest) {
                if ($travelRequest->current_approval_level === 'unit_head') {
                    return ($travelRequest->staff->department->unit_head_staff_id ?? null) == $travelRequest->staff_id;
                }
                if ($travelRequest->current_approval_level === 'hod') {
                    return ($travelRequest->staff->department->hod_staff_id ?? null) == $travelRequest->staff_id;
                }

                return ($travelRequest->staff->department->school->dean_staff_id ?? null) == $travelRequest->staff_id;
            })
            ->pluck('id');
    }
}
