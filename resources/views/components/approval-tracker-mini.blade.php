@props(['leaveRequest'])

@php
    // Track completed stages from approval logs (same logic as timeline)
    $completedLevels = [];
    foreach ($leaveRequest->approvalLogs->where('action', 'approved') as $log) {
        $fromRole = strtolower($log->from_role);
        if ($fromRole == 'hod') $completedLevels[1] = true;
        if ($fromRole == 'dean') $completedLevels[2] = true;
        if ($fromRole == 'hr') $completedLevels[3] = true;
        // FIXED: Check for all possible DVC role variations
        if (in_array($fromRole, ['dvca', 'dvc-pfa', 'dvc'])) $completedLevels[4] = true;
        if ($fromRole == 'hr_logistics') $completedLevels[5] = true;
    }
    
    $currentLevelInt = (int)$leaveRequest->current_approval_level;
    $actingStatus = $leaveRequest->acting_status;
    $isFullyApproved = $leaveRequest->status == 'approved';
    
    // Determine completion based on approval logs OR fully approved status
    $isActingCompleted = $actingStatus == 'accepted';
    $isHodCompleted = isset($completedLevels[1]) || ($isFullyApproved);
    $isDeanCompleted = isset($completedLevels[2]) || ($isFullyApproved);
    $isHrCompleted = isset($completedLevels[3]) || ($isFullyApproved);
    $isDvcCompleted = isset($completedLevels[4]) || ($isFullyApproved);
    $isHrFinalCompleted = isset($completedLevels[5]) || ($isFullyApproved);
    
    // Current stage logic - ONLY ONE stage should be "current" at a time
    $isActingCurrent = ($actingStatus == 'pending' && !$isActingCompleted);
    $isHodCurrent = ($isActingCompleted && $currentLevelInt == 1 && !$isHodCompleted && !$isFullyApproved);
    $isDeanCurrent = ($isHodCompleted && $currentLevelInt == 2 && !$isDeanCompleted && !$isFullyApproved);
    $isHrCurrent = ($isDeanCompleted && $currentLevelInt == 3 && !$isHrCompleted && !$isFullyApproved);
    $isDvcCurrent = ($isHrCompleted && $currentLevelInt == 4 && !$isDvcCompleted && !$isFullyApproved);
    $isHrFinalCurrent = ($isDvcCompleted && $currentLevelInt == 5 && !$isHrFinalCompleted && !$isFullyApproved);

    // Some stages don't apply to every request, and showing them anyway
    // reads as "still waiting on this person" when nobody will ever act on
    // it: the VC's own leave skips straight to HR Logistics; an HOD's own
    // leave has nobody else to review the HOD stage, so it's skipped; a
    // Dean's own leave (or a school with no Dean assigned at all) skips
    // the Dean stage the same way; HR staff sit administratively parallel
    // to HOD/Dean, so their own leave skips both of those stages too, even
    // when the real HOD/Dean is a totally different, unrelated person.
    $isVC = $leaveRequest->staff->hasRole('vice-chancellor');
    $isOwnHr = $leaveRequest->staff->hasRole('hr-officer') || $leaveRequest->staff->hasRole('hr-manager');
    $isOwnHod = ($leaveRequest->staff->department->hod_staff_id ?? null) == $leaveRequest->staff_id;
    $isOwnDean = ($leaveRequest->staff->department->school->dean_staff_id ?? null) == $leaveRequest->staff_id;
    $hasDean = (bool) ($leaveRequest->staff->department->school->dean_staff_id ?? null);

    $stages = [
        ['completed' => $isActingCompleted, 'current' => $isActingCurrent, 'label' => 'Act', 'applicable' => true],
        ['completed' => $isHodCompleted, 'current' => $isHodCurrent, 'label' => 'HOD', 'applicable' => ! $isVC && ! $isOwnHr && ! $isOwnHod],
        ['completed' => $isDeanCompleted, 'current' => $isDeanCurrent, 'label' => 'Dean', 'applicable' => ! $isVC && ! $isOwnHr && ! $isOwnDean && $hasDean],
        ['completed' => $isHrCompleted, 'current' => $isHrCurrent, 'label' => 'HR', 'applicable' => ! $isVC],
        ['completed' => $isDvcCompleted, 'current' => $isDvcCurrent, 'label' => 'DVC', 'applicable' => ! $isVC],
        ['completed' => $isHrFinalCompleted, 'current' => $isHrFinalCurrent, 'label' => 'Final Logistics', 'applicable' => true],
    ];

    $stages = array_values(array_filter($stages, fn ($stage) => $stage['applicable']));
@endphp

<div class="approval-tracker-mini">
    @foreach($stages as $index => $stage)
        <div class="mini-step-wrapper">
            <div class="mini-step
                {{ $stage['completed'] ? 'completed' : '' }}
                {{ $stage['current'] ? 'current' : '' }}
                {{ (!$stage['completed'] && !$stage['current']) ? 'pending' : '' }}">
                @if($stage['completed'])
                    <i class="ti ti-check"></i>
                @elseif($stage['current'])
                    <i class="ti ti-loader"></i>
                @else
                    <i class="ti ti-circle"></i>
                @endif
            </div>
            <span class="mini-step-label d-none d-sm-inline">{{ $stage['label'] }}</span>
        </div>
        @if(!$loop->last)
            <div class="mini-line {{ $stage['completed'] ? 'completed' : '' }}"></div>
        @endif
    @endforeach
</div>

<style>
.approval-tracker-mini {
    display: inline-flex;
    align-items: center;
    gap: 0.15rem;
    background: transparent;
}

.mini-step-wrapper {
    display: inline-flex;
    align-items: center;
    gap: 0.15rem;
}

.mini-step {
    width: 15px;
    height: 15px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.5rem;
    transition: all 0.2s ease;
}

.mini-step.completed {
    background: #10B981;
    color: white;
}

.mini-step.current {
    background: #3B82F6;
    color: white;
    animation: miniPulse 1.5s infinite;
}

.mini-step.pending {
    background: #E2E8F0;
    color: #94A3B8;
}

.mini-line {
    width: 10px;
    height: 1.5px;
    background: #E2E8F0;
    transition: all 0.3s ease;
}

.mini-line.completed {
    background: #10B981;
}

.mini-step-label {
    font-size: 0.55rem;
    font-weight: 500;
    color: #64748B;
    margin-left: 0.1rem;
}

@keyframes miniPulse {
    0% {
        transform: scale(1);
        box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.4);
    }
    70% {
        transform: scale(1.1);
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0);
    }
    100% {
        transform: scale(1);
        box-shadow: 0 0 0 0 rgba(59, 130, 246, 0);
    }
}

@media (max-width: 576px) {
    /* Icon glyphs need enough circle size to stay legible, which fights
       against actually being small - on a phone, drop to plain color-coded
       dots instead (no icon, no connecting line) so the whole row can
       shrink to something genuinely pocket-sized. */
    .approval-tracker-mini {
        gap: 9px;
    }
    .mini-step-wrapper {
        gap: 0;
    }
    .mini-step-label {
        display: none;
    }
    .mini-line {
        display: none;
    }
    .mini-step {
        width: 9px;
        height: 9px;
    }
    .mini-step i {
        display: none;
    }
    .mini-step.current {
        animation: none;
        box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.25);
    }
}
</style>