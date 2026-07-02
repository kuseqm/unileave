<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['user']));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['user']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    use Carbon\Carbon;
    
    // Leave year: September 1 to August 30
    $today = Carbon::now()->startOfDay(); // Start of day to avoid time issues
    $yearStart = Carbon::now()->month >= 9 
        ? Carbon::now()->startOfMonth()->setMonth(9)->setDay(1)->startOfDay()
        : Carbon::now()->subYear()->startOfMonth()->setMonth(9)->setDay(1)->startOfDay();
    
    $yearEnd = Carbon::now()->month >= 9
        ? Carbon::now()->addYear()->startOfMonth()->setMonth(8)->setDay(30)->endOfDay()
        : Carbon::now()->startOfMonth()->setMonth(8)->setDay(30)->endOfDay();
    
    // Days remaining until August 30 (whole number, no decimals)
    $daysUntilYearEnd = max(0, (int) $today->diffInDays($yearEnd, false));
    
    // Annual leave type ID
    $annualLeaveType = App\Models\LeaveType::where('name', 'like', '%Annual%')->first();
    $annualLeaveTypeId = $annualLeaveType ? $annualLeaveType->id : null;
    
    // Get approved annual leave requests for this user in current leave year
    $approvedLeaveDays = 0;
    $hasApprovedLeave = false;
    $remainingLeaveDays = 28;
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
            
            $remainingLeaveDays = max(0, 28 - $approvedLeaveDays);
        }
    }
    
    // Determine display mode and styling
    if (!$hasApprovedLeave || $approvedLeaveDays == 0) {
        // PART A: No leave requested yet
        $mode = 'no_leave';
        $daysValue = $daysUntilYearEnd;
        
        // Determine color based on days remaining until Aug 30
        if ($daysUntilYearEnd > 28) {
            $color = 'green';
            $statusClass = 'status-green';
            $icon = 'ti ti-calendar-check';
        } elseif ($daysUntilYearEnd == 28) {
            $color = 'orange';
            $statusClass = 'status-orange';
            $icon = 'ti ti-alert-triangle';
        } else {
            $color = 'red';
            $statusClass = 'status-red';
            $icon = 'ti ti-alert-circle';
        }
        
        $message = "No leave requested in {$daysUntilYearEnd} days";
        
    } elseif ($approvedLeaveDays >= 28) {
        // Reset: Staff has completed 28 days
        $mode = 'completed';
        $daysValue = $daysUntilYearEnd;
        
        if ($daysUntilYearEnd > 28) {
            $color = 'green';
            $statusClass = 'status-green';
            $icon = 'ti ti-calendar-check';
        } elseif ($daysUntilYearEnd == 28) {
            $color = 'orange';
            $statusClass = 'status-orange';
            $icon = 'ti ti-alert-triangle';
        } else {
            $color = 'red';
            $statusClass = 'status-red';
            $icon = 'ti ti-alert-circle';
        }
        
        $message = "Leave completed! {$daysUntilYearEnd} days remaining in year";
        
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
?>

<div class="leave-counter-wrapper">
    <div class="leave-counter <?php echo e($statusClass); ?>" title="<?php echo e($message); ?>">
        <i class="<?php echo e($icon); ?>"></i>
        <span class="leave-counter-text"><?php echo e($message); ?></span>
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
}

@media (max-width: 480px) {
    .leave-counter-wrapper {
        margin-right: 0.25rem;
    }
    
    .leave-counter {
        padding: 0.25rem 0.5rem;
    }
}
</style><?php /**PATH C:\wamp64\www\unileave\resources\views/components/leave-counter.blade.php ENDPATH**/ ?>