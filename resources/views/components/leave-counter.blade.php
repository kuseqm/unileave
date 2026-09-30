@props(['user'])

@php
    use Carbon\Carbon;

    // Leave year = the active academic year (see App\Models\AcademicYear /
    // admin > Academic Years). Falls back to the old fixed Sept 1 - Aug 31
    // window only if no academic year has been activated yet, so this
    // widget never breaks on a fresh install.
    $today = Carbon::now()->startOfDay(); // Start of day to avoid time issues
    $currentAcademicYear = App\Models\AcademicYear::current();

    if ($currentAcademicYear) {
        $yearStart = $currentAcademicYear->start_date->copy()->startOfDay();
        $yearEnd = $currentAcademicYear->end_date->copy()->endOfDay();
    } else {
        $yearStart = Carbon::now()->month >= 9
            ? Carbon::now()->startOfMonth()->setMonth(9)->setDay(1)->startOfDay()
            : Carbon::now()->subYear()->startOfMonth()->setMonth(9)->setDay(1)->startOfDay();

        $yearEnd = Carbon::now()->month >= 9
            ? Carbon::now()->addYear()->startOfMonth()->setMonth(8)->setDay(31)->endOfDay()
            : Carbon::now()->startOfMonth()->setMonth(8)->setDay(31)->endOfDay();
    }

    // Days remaining until the leave year ends (whole number, no decimals)
    $daysUntilYearEnd = max(0, (int) $today->diffInDays($yearEnd, false));

    // Annual leave type - its own days_allowed is the real entitlement,
    // instead of a number hardcoded here that could drift out of sync.
    $annualLeaveType = App\Models\LeaveType::where('name', 'like', '%Annual%')->first();
    $annualLeaveTypeId = $annualLeaveType ? $annualLeaveType->id : null;
    $annualAllocation = $annualLeaveType->days_allowed ?? 28;

    // Get approved annual leave requests for this user in current leave year
    $approvedLeaveDays = 0;
    $hasApprovedLeave = false;
    $remainingLeaveDays = $annualAllocation;
    $leaveEndDate = null;
    
    if ($annualLeaveTypeId) {
        $approvedLeaves = $user->leaveRequests()
            ->where('leave_type_id', $annualLeaveTypeId)
            ->where('status', 'approved')
            ->where(function($q) use ($yearStart, $yearEnd) {
                $q->whereBetween('start_date', [$yearStart, $yearEnd])
                  ->orWhereBetween('end_date', [$yearStart, $yearEnd]);
            })
            ->get();
        
        if ($approvedLeaves->isNotEmpty()) {
            $hasApprovedLeave = true;
            
            foreach ($approvedLeaves as $leave) {
                $start = Carbon::parse($leave->start_date)->startOfDay();
                $end = Carbon::parse($leave->end_date)->startOfDay();
                // Add 1 to include both start and end days
                $days = (int) $start->diffInDays($end) + 1;
                $approvedLeaveDays += $days;
                $leaveEndDate = $end;
            }
            
            $remainingLeaveDays = max(0, $annualAllocation - $approvedLeaveDays);
        }
    }
    
    // Determine display mode and styling
    if (!$hasApprovedLeave || $approvedLeaveDays == 0) {
        // PART A: No leave requested yet
        $mode = 'no_leave';
        $daysValue = $daysUntilYearEnd;
        
        // Color reflects urgency to request leave before the year runs out:
        // green while there's plenty of time, orange inside the last 90 days,
        // red inside the last 30 days.
        if ($daysUntilYearEnd > 90) {
            $color = 'green';
            $statusClass = 'status-green';
            $icon = 'ti ti-calendar-check';
        } elseif ($daysUntilYearEnd > 30) {
            $color = 'orange';
            $statusClass = 'status-orange';
            $icon = 'ti ti-alert-triangle';
        } else {
            $color = 'red';
            $statusClass = 'status-red';
            $icon = 'ti ti-alert-circle';
        }

        $message = "You have {$daysUntilYearEnd} days to request leave";

    } elseif ($approvedLeaveDays >= $annualAllocation) {
        // Reset: Staff has completed 28 days
        $mode = 'completed';
        $daysValue = $daysUntilYearEnd;

        if ($daysUntilYearEnd > 90) {
            $color = 'green';
            $statusClass = 'status-green';
            $icon = 'ti ti-calendar-check';
        } elseif ($daysUntilYearEnd > 30) {
            $color = 'orange';
            $statusClass = 'status-orange';
            $icon = 'ti ti-alert-triangle';
        } else {
            $color = 'red';
            $statusClass = 'status-red';
            $icon = 'ti ti-alert-circle';
        }

        $message = "Leave completed! {$daysUntilYearEnd} days left in year";
        
    } else {
        // PART B: Leave requested and in progress
        $mode = 'in_progress';
        $daysValue = $remainingLeaveDays;
        
        // Determine color based on remaining days to complete 28 days
        if ($remainingLeaveDays > 5) {
            $color = 'green';
            $statusClass = 'status-green';
            $icon = 'ti ti-calendar-stats';
        } elseif ($remainingLeaveDays >= 3 && $remainingLeaveDays <= 5) {
            $color = 'orange';
            $statusClass = 'status-orange';
            $icon = 'ti ti-alert-triangle';
        } else {
            $color = 'red';
            $statusClass = 'status-red';
            $icon = 'ti ti-alert-circle';
        }
        
        $message = "Leave balance: {$remainingLeaveDays} days remaining";
        
        // Add return date info if available
        if ($leaveEndDate) {
            $returnDate = Carbon::parse($leaveEndDate)->addDay()->startOfDay();
            $daysUntilReturn = (int) $today->diffInDays($returnDate, false);
            if ($daysUntilReturn > 0 && $daysUntilReturn <= 14) {
                $message .= " • Return in {$daysUntilReturn} days";
            }
        }
    }

    // Compact label for narrow screens: the full sentence hides below 768px
    // (see @media rule below) since it doesn't fit, but hiding it down to just
    // the icon leaves an unlabeled colored dot with no information in it — so
    // show this short form instead of nothing.
    $shortMessage = match ($mode) {
        'no_leave' => "{$daysValue}d to request",
        'in_progress' => "{$daysValue}d left",
        default => "{$daysValue}d left",
    };
@endphp

<div class="leave-counter-wrapper">
    <div class="leave-counter {{ $statusClass }}" title="{{ $message }}">
        <i class="{{ $icon }}"></i>
        <span class="leave-counter-text">{{ $message }}</span>
        <span class="leave-counter-text-short">{{ $shortMessage }}</span>
    </div>
</div>

<style>
.leave-counter-wrapper {
    display: inline-flex;
    align-items: center;
    margin-right: 0.5rem;
}

.leave-counter {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.35rem 0.85rem;
    border-radius: 30px;
    font-size: 0.75rem;
    font-weight: 500;
    white-space: nowrap;
    transition: all 0.2s ease;
    cursor: default;
}

/* Green Status */
.leave-counter.status-green {
    background: rgba(16, 185, 129, 0.12);
    color: #059669;
    border: 1px solid rgba(16, 185, 129, 0.25);
}

/* Orange Status */
.leave-counter.status-orange {
    background: rgba(245, 158, 11, 0.12);
    color: #D97706;
    border: 1px solid rgba(245, 158, 11, 0.25);
    animation: softPulse 1s infinite;
}

/* Red Status */
.leave-counter.status-red {
    background: rgba(239, 68, 68, 0.12);
    color: #DC2626;
    border: 1px solid rgba(239, 68, 68, 0.25);
    animation: pulse 1.5s infinite;
}

/* Icons */
.leave-counter i {
    font-size: 1rem;
}

/* Compact label shown only below 768px - see .leave-counter-text-short below */
.leave-counter-text-short {
    display: none;
}

/* Animations */
@keyframes softPulse {
    0% { opacity: 1; }
    50% { opacity: 0.85; }
    100% { opacity: 1; }
}

@keyframes pulse {
    0% {
        transform: scale(1);
        box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.4);
    }
    70% {
        transform: scale(1.02);
        box-shadow: 0 0 0 4px rgba(239, 68, 68, 0);
    }
    100% {
        transform: scale(1);
        box-shadow: 0 0 0 0 rgba(239, 68, 68, 0);
    }
}

/* Mobile Responsive */
@media (max-width: 768px) {
    .leave-counter {
        padding: 0.25rem 0.6rem;
        font-size: 0.65rem;
    }
    
    .leave-counter i {
        font-size: 0.85rem;
    }
    
    .leave-counter-text {
        display: none;
    }

    .leave-counter-text-short {
        display: inline;
        white-space: nowrap;
    }
}

@media (max-width: 480px) {
    .leave-counter-wrapper {
        margin-right: 0.25rem;
    }
    
    .leave-counter {
        padding: 0.25rem 0.5rem;
    }
}
</style>