@extends('layouts.master')

@section('title', 'Leave Request Details')

@section('content')
<div class="container-fluid px-2">
    
    <!-- Breadcrumb -->
    <x-breadcrumb :items="[
        ['name' => 'My Leave Requests', 'url' => route('leave-requests.my-requests')],
        ['name' => 'Leave Request Details', 'url' => '#']
    ]" />
    
    <!-- Page Header -->
    <x-page-header title="LEAVE REQUEST DETAILS" description="View leave request information">
        <div class="d-flex gap-1">
            <a href="{{ route('leave-requests.my-requests') }}" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
                <i class="ti ti-arrow-left me-1"></i> Back
            </a>
            
            <!-- View Approval Letter Button (only for approved requests) -->
            @if($leaveRequest->status === 'approved')
                <a href="{{ route('leave-requests.letter', $leaveRequest->id) }}" target="_blank" class="btn btn-success btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
                    <i class="ti ti-file-text me-1"></i> View Letter
                </a>
            @endif
        </div>
    </x-page-header>
    
    <div class="row g-2">
        <!-- Main Details -->
        <div class="col-lg-7">
            <!-- Request Status Card -->
            <div class="card rounded-sm mb-2">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-info-circle text-primary me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Request Information</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <div class="row g-1">
                        <div class="col-md-6">
                            <div class="text-muted" style="font-size: 0.6rem;">Leave Type</div>
                            <div class="fw-semibold" style="font-size: 0.75rem;">{{ $leaveRequest->leaveType->name ?? 'N/A' }} ({{ $leaveRequest->leaveType->code ?? '' }})</div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted" style="font-size: 0.6rem;">Status</div>
                            <div>
                                @if($leaveRequest->status == 'pending')
                                    <span class="badge bg-warning-subtle text-warning rounded-sm" style="font-size: 0.6rem;">Pending</span>
                                @elseif($leaveRequest->status == 'approved')
                                    <span class="badge bg-success-subtle text-success rounded-sm" style="font-size: 0.6rem;">Approved</span>
                                @elseif($leaveRequest->status == 'rejected')
                                    <span class="badge bg-danger-subtle text-danger rounded-sm" style="font-size: 0.6rem;">Rejected</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary rounded-sm" style="font-size: 0.6rem;">{{ ucfirst($leaveRequest->status) }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <div class="row g-1 mt-1">
                        <div class="col-md-4">
                            <div class="text-muted" style="font-size: 0.6rem;">Start Date</div>
                            <div style="font-size: 0.75rem;">{{ \Carbon\Carbon::parse($leaveRequest->start_date)->format('d M Y') }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-muted" style="font-size: 0.6rem;">End Date</div>
                            <div style="font-size: 0.75rem;">{{ \Carbon\Carbon::parse($leaveRequest->end_date)->format('d M Y') }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-muted" style="font-size: 0.6rem;">Expected Return Date</div>
                            <div style="font-size: 0.75rem;">{{ $leaveRequest->expected_return_date ? \Carbon\Carbon::parse($leaveRequest->expected_return_date)->format('d M Y') : 'N/A' }}</div>
                        </div>
                    </div>
                    
                    <div class="row g-1 mt-1">
                        <div class="col-md-6">
                            <div class="text-muted" style="font-size: 0.6rem;">Total Days</div>
                            <div class="fw-semibold" style="font-size: 0.75rem;">{{ $leaveRequest->days }} day(s)</div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted" style="font-size: 0.6rem;">Current Approval Level</div>
                            <div class="fw-semibold" style="font-size: 0.75rem;">
                                @php
                                    $levelNames = [
                                        1 => 'HOD',
                                        2 => 'Dean',
                                        3 => 'HR',
                                        4 => 'DVC',
                                        5 => 'HR Logistics'
                                    ];
                                @endphp
                                {{ $levelNames[$leaveRequest->current_approval_level] ?? 'Completed' }}
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-2">
                        <div class="text-muted" style="font-size: 0.6rem;">Reason</div>
                        <div class="p-1 bg-light rounded-sm" style="font-size: 0.7rem;">{{ $leaveRequest->reason }}</div>
                    </div>
                    
                    @if($leaveRequest->letter_content)
                    <div class="mt-2">
                        <div class="text-muted" style="font-size: 0.6rem;">Letter Content</div>
                        <div class="p-1 bg-light rounded-sm" style="font-size: 0.7rem; white-space: pre-line;">{{ $leaveRequest->letter_content }}</div>
                    </div>
                    @endif
                    
                    @if($leaveRequest->rejection_reason)
                    <div class="mt-2">
                        <div class="text-muted" style="font-size: 0.6rem;">Rejection Reason</div>
                        <div class="p-1 bg-danger-subtle rounded-sm text-danger" style="font-size: 0.7rem;">{{ $leaveRequest->rejection_reason }}</div>
                    </div>
                    @endif
                </div>
            </div>
            
            <!-- Acting Staff Card -->
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-user-check text-primary me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Acting Staff (Hold Position)</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <div class="row g-1">
                        <div class="col-md-6">
                            <div class="text-muted" style="font-size: 0.6rem;">Name</div>
                            <div class="fw-semibold" style="font-size: 0.75rem;">
                                @if($leaveRequest->actingStaff)
                                    {{ $leaveRequest->actingStaff->first_name }} {{ $leaveRequest->actingStaff->last_name }}
                                @else
                                    Not specified
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted" style="font-size: 0.6rem;">Status</div>
                            <div>
                                @if($leaveRequest->acting_status == 'pending')
                                    <span class="badge bg-warning-subtle text-warning rounded-sm" style="font-size: 0.6rem;">Awaiting Response</span>
                                @elseif($leaveRequest->acting_status == 'accepted')
                                    <span class="badge bg-success-subtle text-success rounded-sm" style="font-size: 0.6rem;">Accepted</span>
                                @elseif($leaveRequest->acting_status == 'declined')
                                    <span class="badge bg-danger-subtle text-danger rounded-sm" style="font-size: 0.6rem;">Declined</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @if($leaveRequest->acting_responded_at)
                    <div class="mt-1">
                        <div class="text-muted" style="font-size: 0.6rem;">Responded At</div>
                        <div style="font-size: 0.7rem;">{{ \Carbon\Carbon::parse($leaveRequest->acting_responded_at)->format('d M Y H:i') }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Approval Timeline -->
        <div class="col-lg-5">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-history text-primary me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Approval Timeline</span>
                    </div>
                </div>
                <div class="card-body p-2">
                    
                    <!-- VISUAL TIMELINE WITH DOTS - FIXED FOR INTEGER LEVELS -->
                    @php
                        // Map integer levels to stage keys
                        $stageMap = [
                            1 => 'hod',
                            2 => 'dean',
                            3 => 'hr_approval',
                            4 => 'dvc',
                            5 => 'hr_final',
                        ];
                        
                        $stages = [
                            ['key' => 'acting', 'label' => 'Acting', 'level' => null],
                            ['key' => 'hod', 'label' => 'HOD', 'level' => 1],
                            ['key' => 'dean', 'label' => 'Dean', 'level' => 2],
                            ['key' => 'hr_approval', 'label' => 'HR', 'level' => 3],
                            ['key' => 'dvc', 'label' => 'DVC', 'level' => 4],
                            ['key' => 'hr_final', 'label' => 'Final Logistics', 'level' => 5],
                        ];
                        
                        // Track completed stages from approval logs
                        $completedLevels = [];
                        foreach ($leaveRequest->approvalLogs->where('action', 'approved') as $log) {
                            $fromRole = strtolower($log->from_role);
                            if ($fromRole == 'hod') $completedLevels[1] = true;
                            if ($fromRole == 'dean') $completedLevels[2] = true;
                            if ($fromRole == 'hr') $completedLevels[3] = true;
                            if (in_array($fromRole, ['dvca', 'dvc_pfa'])) $completedLevels[4] = true;
                            if ($fromRole == 'hr_logistics') $completedLevels[5] = true;
                        }
                        
                        $currentLevelInt = (int)$leaveRequest->current_approval_level;
                        $actingStatus = $leaveRequest->acting_status;
                        
                        // Determine stage statuses using integer comparison
                        $stageStatus = [];
                        foreach ($stages as $stage) {
                            if ($stage['key'] == 'acting') {
                                $stageStatus['acting'] = $actingStatus == 'accepted' ? 'completed' : ($actingStatus == 'pending' ? 'current' : 'pending');
                            } else {
                                $level = $stage['level'];
                                if (isset($completedLevels[$level])) {
                                    $stageStatus[$stage['key']] = 'completed';
                                } elseif ($currentLevelInt == $level) {
                                    $stageStatus[$stage['key']] = 'current';
                                } else {
                                    $stageStatus[$stage['key']] = 'pending';
                                }
                            }
                        }
                        
                        $nextPendingStage = null;
                        foreach ($stages as $stage) {
                            if ($stage['key'] != 'acting' && $stageStatus[$stage['key']] == 'current') {
                                $nextPendingStage = $stage['label'];
                                break;
                            }
                        }
                        if (!$nextPendingStage && $actingStatus == 'pending') {
                            $nextPendingStage = 'Acting Staff';
                        }
                    @endphp
                    
                    <div class="timeline-visual mb-2">
                        <div class="timeline-dots">
                            @foreach($stages as $index => $stage)
                                <div class="timeline-step {{ $stageStatus[$stage['key']] }}">
                                    <div class="timeline-dot">
                                        @if($stageStatus[$stage['key']] == 'completed')
                                            <i class="ti ti-check"></i>
                                        @elseif($stageStatus[$stage['key']] == 'current')
                                            <i class="ti ti-loader"></i>
                                        @else
                                            <i class="ti ti-circle"></i>
                                        @endif
                                    </div>
                                    <div class="timeline-label">{{ $stage['label'] }}</div>
                                </div>
                                @if(!$loop->last)
                                    <div class="timeline-line {{ $stageStatus[$stage['key']] == 'completed' ? 'completed' : '' }}"></div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    
                    <!-- Status Summary -->
                    @if($leaveRequest->status == 'approved')
                        <div class="status-summary mb-2 p-1 rounded-sm" style="background: #D1FAE5; border-left: 3px solid #10B981;">
                            <div class="text-success" style="font-size: 0.65rem;">
                                <i class="ti ti-check-circle me-1"></i> Leave request fully approved and completed
                            </div>
                        </div>
                    @elseif($leaveRequest->status == 'rejected')
                        <div class="status-summary mb-2 p-1 rounded-sm" style="background: #FEE2E2; border-left: 3px solid #EF4444;">
                            <div class="text-danger" style="font-size: 0.65rem;">
                                <i class="ti ti-x-circle me-1"></i> Leave request was rejected
                            </div>
                        </div>
                    @elseif($nextPendingStage)
                        <div class="status-summary mb-2 p-1 rounded-sm" style="background: #FEF3C7; border-left: 3px solid #F59E0B;">
                            <div class="text-warning" style="font-size: 0.65rem;">
                                <i class="ti ti-clock me-1"></i> Waiting for {{ $nextPendingStage }} approval
                            </div>
                        </div>
                    @endif
                    
                    <hr class="my-1" style="border-color: #E8E2D5;">
                    
                    <!-- Detailed Approval Logs -->
                    <div class="approval-logs-list" style="max-height: 280px; overflow-y: auto;">
                        @php
                            $allLogs = $leaveRequest->approvalLogs->sortByDesc('created_at');
                            $hasActingResponse = $leaveRequest->acting_responded_at;
                        @endphp
                        
                        @if($hasActingResponse)
                            <div class="log-item d-flex align-items-start gap-2 py-1 px-1 mb-1 rounded-sm" style="background: #F8FAFC;">
                                <div class="rounded-circle p-1" style="width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; background: {{ $leaveRequest->acting_status == 'accepted' ? '#D1FAE5' : '#FEE2E2' }};">
                                    <i class="ti ti-{{ $leaveRequest->acting_status == 'accepted' ? 'check' : 'x' }}" style="font-size: 0.6rem; color: {{ $leaveRequest->acting_status == 'accepted' ? '#059669' : '#DC2626' }};"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                                        <span class="fw-semibold" style="font-size: 0.65rem;">
                                            {{ ucfirst($leaveRequest->acting_status) }} by Acting Staff
                                            @if($leaveRequest->actingStaff)
                                                ({{ $leaveRequest->actingStaff->first_name }} {{ $leaveRequest->actingStaff->last_name }})
                                            @endif
                                        </span>
                                        <span class="text-muted" style="font-size: 0.5rem;">{{ \Carbon\Carbon::parse($leaveRequest->acting_responded_at)->diffForHumans() }}</span>
                                    </div>
                                    <div class="text-muted" style="font-size: 0.55rem;">From: Acting Staff → To: HOD</div>
                                </div>
                            </div>
                        @endif
                        
                        @forelse($allLogs as $log)
                            @php
                                $fromRoleDisplay = ucfirst(str_replace('_', ' ', $log->from_role));
                                $toRoleDisplay = $log->to_role ? ucfirst(str_replace('_', ' ', $log->to_role)) : 'Final';
                                $approverName = $log->approver->first_name ?? 'System';
                                $approverName .= isset($log->approver->last_name) ? ' ' . $log->approver->last_name : '';
                            @endphp
                            <div class="log-item d-flex align-items-start gap-2 py-1 px-1 mb-1 rounded-sm" style="background: #F8FAFC;">
                                <div class="rounded-circle p-1" style="width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; background: {{ $log->action == 'approved' ? '#D1FAE5' : ($log->action == 'rejected' ? '#FEE2E2' : '#EFF6FF') }};">
                                    <i class="ti ti-{{ $log->action == 'approved' ? 'check' : ($log->action == 'rejected' ? 'x' : 'arrow-right') }}" style="font-size: 0.6rem; color: {{ $log->action == 'approved' ? '#059669' : ($log->action == 'rejected' ? '#DC2626' : '#3B82F6') }};"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                                        <span class="fw-semibold" style="font-size: 0.65rem;">
                                            {{ ucfirst($log->action) }} by {{ $approverName }}
                                        </span>
                                        <span class="text-muted" style="font-size: 0.5rem;">{{ $log->created_at->diffForHumans() }}</span>
                                    </div>
                                    <div class="text-muted" style="font-size: 0.55rem;">From: {{ $fromRoleDisplay }} → To: {{ $toRoleDisplay }}</div>
                                    @if($log->comment)
                                        <div class="small text-muted mt-0" style="font-size: 0.5rem;">"{{ $log->comment }}"</div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            @if(!$hasActingResponse)
                                <div class="text-center py-2 text-muted" style="font-size: 0.65rem;">No approval activity yet</div>
                            @endif
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Ultra Compact Timeline Styles */
.timeline-visual {
    width: 100%;
    overflow-x: auto;
    padding-bottom: 0.15rem;
}

.timeline-dots {
    display: flex;
    align-items: center;
    justify-content: space-between;
    min-width: 360px;
    gap: 0;
}

.timeline-step {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.1rem;
    flex: 1;
    text-align: center;
    min-width: 35px;
}

.timeline-dot {
    width: 16px;
    height: 16px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.45rem;
    transition: all 0.2s ease;
}

.timeline-step.completed .timeline-dot {
    background: #10B981;
    color: white;
}

.timeline-step.current .timeline-dot {
    background: #3B82F6;
    color: white;
    animation: timelinePulse 1.5s infinite;
}

.timeline-step.pending .timeline-dot {
    background: #E2E8F0;
    color: #94A3B8;
    border: 1px solid #CBD5E1;
}

.timeline-label {
    font-size: 0.45rem;
    font-weight: 500;
    color: #64748B;
    white-space: nowrap;
}

.timeline-step.completed .timeline-label {
    color: #10B981;
    font-weight: 600;
}

.timeline-step.current .timeline-label {
    color: #3B82F6;
    font-weight: 600;
}

.timeline-line {
    flex: 0.25;
    height: 1.5px;
    background: #E2E8F0;
    margin: 0 0.05rem;
    margin-bottom: 0.7rem;
    min-width: 6px;
}

.timeline-line.completed {
    background: #10B981;
}

@keyframes timelinePulse {
    0% {
        transform: scale(1);
        box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.4);
    }
    70% {
        transform: scale(1.15);
        box-shadow: 0 0 0 2px rgba(59, 130, 246, 0);
    }
    100% {
        transform: scale(1);
        box-shadow: 0 0 0 0 rgba(59, 130, 246, 0);
    }
}

/* Status Summary */
.status-summary {
    border-radius: 4px;
}

/* Logs list scrollbar */
.approval-logs-list::-webkit-scrollbar {
    width: 3px;
}

.approval-logs-list::-webkit-scrollbar-track {
    background: #F1F5F9;
    border-radius: 3px;
}

.approval-logs-list::-webkit-scrollbar-thumb {
    background: #CBD5E1;
    border-radius: 3px;
}

.log-item:hover {
    background: #F1F5F9 !important;
    transition: background 0.2s ease;
}

/* Responsive adjustments */
@media (max-width: 550px) {
    .timeline-dots {
        min-width: 320px;
    }
    .timeline-label {
        font-size: 0.4rem;
    }
    .timeline-dot {
        width: 14px;
        height: 14px;
    }
}
</style>

<x-live-refresh :url="route('leave-requests.show.heartbeat', $leaveRequest->id)" />
@endsection