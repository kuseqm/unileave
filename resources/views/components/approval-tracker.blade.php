@props(['leaveRequest'])

@php
    // Helper function to check if an approval level is completed
    function isLevelCompleted($leaveRequest, $level) {
        if (!$leaveRequest->relationLoaded('approvalLogs')) {
            $leaveRequest->load('approvalLogs');
        }
        
        return $leaveRequest->approvalLogs
            ->where('level', $level)
            ->where('action', 'approved')
            ->isNotEmpty();
    }
    
    // FULL APPROVAL FLOW: Acting Staff → HOD → Dean → HR Approval → DVC → HR Final
    $stages = [
        'acting' => ['label' => 'Acting Staff', 'icon' => 'user-check', 'level' => 'acting'],
        'hod' => ['label' => 'HOD', 'icon' => 'user-tie', 'level' => 'hod'],
        'dean' => ['label' => 'Dean', 'icon' => 'school', 'level' => 'dean'],
        'hr_approval' => ['label' => 'HR Approval', 'icon' => 'users', 'level' => 'hr_approval'],
        'dvc' => ['label' => 'DVC', 'icon' => 'crown', 'level' => 'dvc'],
        'hr_final' => ['label' => 'HR Final', 'icon' => 'users-check', 'level' => 'hr_final'],
    ];
    
    $currentLevel = $leaveRequest->current_approval_level;
    $actingStatus = $leaveRequest->acting_status;
    
    // Determine completion status for each stage
    $isActingCompleted = $actingStatus == 'accepted';
    $isHodCompleted = isLevelCompleted($leaveRequest, 'hod');
    $isDeanCompleted = isLevelCompleted($leaveRequest, 'dean');
    $isHrApprovalCompleted = isLevelCompleted($leaveRequest, 'hr_approval');
    $isDvcCompleted = isLevelCompleted($leaveRequest, 'dvc');
    $isHrFinalCompleted = $leaveRequest->status == 'approved';
    
    // Determine current stage
    $isActingCurrent = $actingStatus == 'pending' && !$isActingCompleted;
    $isHodCurrent = $currentLevel == 'hod' && !$isHodCompleted;
    $isDeanCurrent = $currentLevel == 'dean' && !$isDeanCompleted;
    $isHrApprovalCurrent = $currentLevel == 'hr_approval' && !$isHrApprovalCompleted;
    $isDvcCurrent = ($currentLevel == 'dvca' || $currentLevel == 'dvc_pfa') && !$isDvcCompleted;
    $isHrFinalCurrent = $currentLevel == 'hr_final' && !$isHrFinalCompleted;
    
    // Determine pending/declined status
    $isActingDeclined = $actingStatus == 'declined';
    $isHodDeclined = !$isHodCompleted && $currentLevel != 'hod' && !$isHodCurrent;
    $isDeanDeclined = !$isDeanCompleted && $currentLevel != 'dean' && !$isDeanCurrent;
    $isHrApprovalDeclined = !$isHrApprovalCompleted && $currentLevel != 'hr_approval' && !$isHrApprovalCurrent;
    $isDvcDeclined = !$isDvcCompleted && $currentLevel != 'dvca' && $currentLevel != 'dvc_pfa' && !$isDvcCurrent;
    $isHrFinalDeclined = $leaveRequest->status == 'rejected';
    
    // Status text mapping
    $statusTexts = [
        'acting' => ['pending' => 'Waiting', 'completed' => 'Accepted', 'current' => 'Waiting'],
        'hod' => ['pending' => 'Pending', 'completed' => 'Approved', 'current' => 'Pending'],
        'dean' => ['pending' => 'Pending', 'completed' => 'Approved', 'current' => 'Pending'],
        'hr_approval' => ['pending' => 'Pending', 'completed' => 'Approved', 'current' => 'Pending'],
        'dvc' => ['pending' => 'Pending', 'completed' => 'Approved', 'current' => 'Pending'],
        'hr_final' => ['pending' => 'Processing', 'completed' => 'Completed', 'current' => 'Processing'],
    ];
@endphp

<div class="card rounded-sm mb-3 border-0 shadow-sm">
    <div class="card-body py-3 px-3">
        <div class="approval-tracker">
            
            <!-- Stage 1: Acting Staff -->
            <div class="tracker-step 
                {{ $isActingCompleted ? 'completed' : '' }}
                {{ $isActingCurrent ? 'current' : '' }}
                {{ $isActingDeclined ? 'declined' : '' }}
                {{ (!$isActingCompleted && !$isActingCurrent && !$isActingDeclined) ? 'pending' : '' }}">
                <div class="step-dot">
                    @if($isActingCompleted)
                        <i class="ti ti-check step-icon"></i>
                    @elseif($isActingCurrent)
                        <i class="ti ti-loader step-icon"></i>
                    @elseif($isActingDeclined)
                        <i class="ti ti-x step-icon"></i>
                    @endif
                </div>
                <div class="step-label">{{ $stages['acting']['label'] }}</div>
                <div class="step-status">
                    @if($isActingCompleted)
                        <span class="status-completed">{{ $statusTexts['acting']['completed'] }}</span>
                    @elseif($isActingCurrent)
                        <span class="status-current">{{ $statusTexts['acting']['current'] }}</span>
                    @elseif($isActingDeclined)
                        <span class="status-declined">Declined</span>
                    @else
                        <span class="status-pending">{{ $statusTexts['acting']['pending'] }}</span>
                    @endif
                </div>
            </div>
            
            <div class="tracker-line {{ $isActingCompleted ? 'completed' : '' }}"></div>
            
            <!-- Stage 2: HOD -->
            <div class="tracker-step 
                {{ $isHodCompleted ? 'completed' : '' }}
                {{ $isHodCurrent ? 'current' : '' }}
                {{ $isHodDeclined ? 'declined' : '' }}
                {{ (!$isHodCompleted && !$isHodCurrent && !$isHodDeclined) ? 'pending' : '' }}">
                <div class="step-dot">
                    @if($isHodCompleted)
                        <i class="ti ti-check step-icon"></i>
                    @elseif($isHodCurrent)
                        <i class="ti ti-loader step-icon"></i>
                    @elseif($isHodDeclined)
                        <i class="ti ti-x step-icon"></i>
                    @endif
                </div>
                <div class="step-label">{{ $stages['hod']['label'] }}</div>
                <div class="step-status">
                    @if($isHodCompleted)
                        <span class="status-completed">{{ $statusTexts['hod']['completed'] }}</span>
                    @elseif($isHodCurrent)
                        <span class="status-current">{{ $statusTexts['hod']['current'] }}</span>
                    @elseif($isHodDeclined)
                        <span class="status-declined">Declined</span>
                    @else
                        <span class="status-pending">{{ $statusTexts['hod']['pending'] }}</span>
                    @endif
                </div>
            </div>
            
            <div class="tracker-line {{ $isHodCompleted ? 'completed' : '' }}"></div>
            
            <!-- Stage 3: Dean -->
            <div class="tracker-step 
                {{ $isDeanCompleted ? 'completed' : '' }}
                {{ $isDeanCurrent ? 'current' : '' }}
                {{ $isDeanDeclined ? 'declined' : '' }}
                {{ (!$isDeanCompleted && !$isDeanCurrent && !$isDeanDeclined) ? 'pending' : '' }}">
                <div class="step-dot">
                    @if($isDeanCompleted)
                        <i class="ti ti-check step-icon"></i>
                    @elseif($isDeanCurrent)
                        <i class="ti ti-loader step-icon"></i>
                    @elseif($isDeanDeclined)
                        <i class="ti ti-x step-icon"></i>
                    @endif
                </div>
                <div class="step-label">{{ $stages['dean']['label'] }}</div>
                <div class="step-status">
                    @if($isDeanCompleted)
                        <span class="status-completed">{{ $statusTexts['dean']['completed'] }}</span>
                    @elseif($isDeanCurrent)
                        <span class="status-current">{{ $statusTexts['dean']['current'] }}</span>
                    @elseif($isDeanDeclined)
                        <span class="status-declined">Declined</span>
                    @else
                        <span class="status-pending">{{ $statusTexts['dean']['pending'] }}</span>
                    @endif
                </div>
            </div>
            
            <div class="tracker-line {{ $isDeanCompleted ? 'completed' : '' }}"></div>
            
            <!-- Stage 4: HR Approval -->
            <div class="tracker-step 
                {{ $isHrApprovalCompleted ? 'completed' : '' }}
                {{ $isHrApprovalCurrent ? 'current' : '' }}
                {{ $isHrApprovalDeclined ? 'declined' : '' }}
                {{ (!$isHrApprovalCompleted && !$isHrApprovalCurrent && !$isHrApprovalDeclined) ? 'pending' : '' }}">
                <div class="step-dot">
                    @if($isHrApprovalCompleted)
                        <i class="ti ti-check step-icon"></i>
                    @elseif($isHrApprovalCurrent)
                        <i class="ti ti-loader step-icon"></i>
                    @elseif($isHrApprovalDeclined)
                        <i class="ti ti-x step-icon"></i>
                    @endif
                </div>
                <div class="step-label">{{ $stages['hr_approval']['label'] }}</div>
                <div class="step-status">
                    @if($isHrApprovalCompleted)
                        <span class="status-completed">{{ $statusTexts['hr_approval']['completed'] }}</span>
                    @elseif($isHrApprovalCurrent)
                        <span class="status-current">{{ $statusTexts['hr_approval']['current'] }}</span>
                    @elseif($isHrApprovalDeclined)
                        <span class="status-declined">Declined</span>
                    @else
                        <span class="status-pending">{{ $statusTexts['hr_approval']['pending'] }}</span>
                    @endif
                </div>
            </div>
            
            <div class="tracker-line {{ $isHrApprovalCompleted ? 'completed' : '' }}"></div>
            
            <!-- Stage 5: DVC -->
            <div class="tracker-step 
                {{ $isDvcCompleted ? 'completed' : '' }}
                {{ $isDvcCurrent ? 'current' : '' }}
                {{ $isDvcDeclined ? 'declined' : '' }}
                {{ (!$isDvcCompleted && !$isDvcCurrent && !$isDvcDeclined) ? 'pending' : '' }}">
                <div class="step-dot">
                    @if($isDvcCompleted)
                        <i class="ti ti-check step-icon"></i>
                    @elseif($isDvcCurrent)
                        <i class="ti ti-loader step-icon"></i>
                    @elseif($isDvcDeclined)
                        <i class="ti ti-x step-icon"></i>
                    @endif
                </div>
                <div class="step-label">{{ $stages['dvc']['label'] }}</div>
                <div class="step-status">
                    @if($isDvcCompleted)
                        <span class="status-completed">{{ $statusTexts['dvc']['completed'] }}</span>
                    @elseif($isDvcCurrent)
                        <span class="status-current">{{ $statusTexts['dvc']['current'] }}</span>
                    @elseif($isDvcDeclined)
                        <span class="status-declined">Declined</span>
                    @else
                        <span class="status-pending">{{ $statusTexts['dvc']['pending'] }}</span>
                    @endif
                </div>
            </div>
            
            <div class="tracker-line {{ $isDvcCompleted ? 'completed' : '' }}"></div>
            
            <!-- Stage 6: HR Final -->
            <div class="tracker-step 
                {{ $isHrFinalCompleted ? 'completed' : '' }}
                {{ $isHrFinalCurrent ? 'current' : '' }}
                {{ $isHrFinalDeclined ? 'declined' : '' }}
                {{ (!$isHrFinalCompleted && !$isHrFinalCurrent && !$isHrFinalDeclined) ? 'pending' : '' }}">
                <div class="step-dot">
                    @if($isHrFinalCompleted)
                        <i class="ti ti-check step-icon"></i>
                    @elseif($isHrFinalCurrent)
                        <i class="ti ti-loader step-icon"></i>
                    @elseif($isHrFinalDeclined)
                        <i class="ti ti-x step-icon"></i>
                    @endif
                </div>
                <div class="step-label">{{ $stages['hr_final']['label'] }}</div>
                <div class="step-status">
                    @if($isHrFinalCompleted)
                        <span class="status-completed">{{ $statusTexts['hr_final']['completed'] }}</span>
                    @elseif($isHrFinalCurrent)
                        <span class="status-current">{{ $statusTexts['hr_final']['current'] }}</span>
                    @elseif($isHrFinalDeclined)
                        <span class="status-declined">Rejected</span>
                    @else
                        <span class="status-pending">{{ $statusTexts['hr_final']['pending'] }}</span>
                    @endif
                </div>
            </div>
            
        </div>
    </div>
</div>

<style>
/* Same CSS as before - keep your existing styles */
.approval-tracker {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 0.25rem;
}

.tracker-step {
    flex: 1;
    text-align: center;
    min-width: 80px;
    position: relative;
}

.step-dot {
    width: 40px;
    height: 40px;
    background: #F1F5F9;
    border: 2px solid #E2E8F0;
    border-radius: 50%;
    margin: 0 auto 0.5rem auto;
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
}

.step-icon {
    font-size: 1rem;
    font-weight: bold;
}

.tracker-step.completed .step-dot {
    background: #10B981;
    border-color: #10B981;
    box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);
}
.tracker-step.completed .step-icon {
    color: white;
}

.tracker-step.current .step-dot {
    background: #3B82F6;
    border-color: #3B82F6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
    animation: pulse 1.5s infinite;
}
.tracker-step.current .step-icon {
    color: white;
}

.tracker-step.declined .step-dot {
    background: #EF4444;
    border-color: #EF4444;
}
.tracker-step.declined .step-icon {
    color: white;
}

.tracker-step.pending .step-dot {
    background: #F1F5F9;
    border-color: #E2E8F0;
}

.step-label {
    font-size: 0.7rem;
    font-weight: 600;
    color: #1E293B;
    margin-bottom: 0.2rem;
}

.step-status {
    font-size: 0.6rem;
}

.status-completed {
    color: #10B981;
    font-weight: 500;
}

.status-current {
    color: #3B82F6;
    font-weight: 500;
}

.status-declined {
    color: #EF4444;
    font-weight: 500;
}

.status-pending {
    color: #94A3B8;
}

.tracker-line {
    flex: 0.3;
    height: 2px;
    background: #E2E8F0;
    min-width: 20px;
    transition: all 0.3s ease;
}

.tracker-line.completed {
    background: #10B981;
}

@keyframes pulse {
    0% {
        transform: scale(1);
        box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.4);
    }
    70% {
        transform: scale(1.05);
        box-shadow: 0 0 0 6px rgba(59, 130, 246, 0);
    }
    100% {
        transform: scale(1);
        box-shadow: 0 0 0 0 rgba(59, 130, 246, 0);
    }
}

@media (max-width: 768px) {
    .approval-tracker {
        flex-direction: column;
        align-items: stretch;
        gap: 0.75rem;
    }
    .tracker-line {
        display: none;
    }
    .tracker-step {
        display: flex;
        align-items: center;
        gap: 1rem;
        text-align: left;
        min-width: auto;
    }
    .step-dot {
        margin: 0;
        width: 32px;
        height: 32px;
        flex-shrink: 0;
    }
    .step-label {
        width: 100px;
        margin-bottom: 0;
        font-size: 0.75rem;
    }
    .step-status {
        flex: 1;
        text-align: right;
    }
    .tracker-step:not(:last-child)::after {
        content: '';
        position: absolute;
        left: 16px;
        bottom: -12px;
        width: 2px;
        height: 12px;
        background: #E2E8F0;
    }
    .tracker-step.completed:not(:last-child)::after {
        background: #10B981;
    }
}

@media (max-width: 480px) {
    .step-label {
        width: 80px;
        font-size: 0.65rem;
    }
    .step-status {
        font-size: 0.55rem;
    }
    .step-dot {
        width: 28px;
        height: 28px;
    }
    .step-icon {
        font-size: 0.8rem;
    }
}
</style>