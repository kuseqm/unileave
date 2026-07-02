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
    
    $stages = [
        ['completed' => $isActingCompleted, 'current' => $isActingCurrent, 'label' => 'Act'],
        ['completed' => $isHodCompleted, 'current' => $isHodCurrent, 'label' => 'HOD'],
        ['completed' => $isDeanCompleted, 'current' => $isDeanCurrent, 'label' => 'Dean'],
        ['completed' => $isHrCompleted, 'current' => $isHrCurrent, 'label' => 'HR'],
        ['completed' => $isDvcCompleted, 'current' => $isDvcCurrent, 'label' => 'DVC'],
        ['completed' => $isHrFinalCompleted, 'current' => $isHrFinalCurrent, 'label' => 'Fin'],
    ];
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
    gap: 0.25rem;
    background: transparent;
}

.mini-step-wrapper {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
}

.mini-step {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.7rem;
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
    width: 20px;
    height: 2px;
    background: #E2E8F0;
    transition: all 0.3s ease;
}

.mini-line.completed {
    background: #10B981;
}

.mini-step-label {
    font-size: 0.6rem;
    font-weight: 500;
    color: #64748B;
    margin-left: 0.2rem;
}

@keyframes miniPulse {
    0% {
        transform: scale(1);
        box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.4);
    }
    70% {
        transform: scale(1.1);
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0);
    }
    100% {
        transform: scale(1);
        box-shadow: 0 0 0 0 rgba(59, 130, 246, 0);
    }
}

.mini-step:hover {
    transform: scale(1.05);
}

@media (max-width: 576px) {
    .mini-step-label {
        display: none;
    }
    .mini-line {
        width: 12px;
    }
    .mini-step {
        width: 20px;
        height: 20px;
        font-size: 0.55rem;
    }
}
</style>