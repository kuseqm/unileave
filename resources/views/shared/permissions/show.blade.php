@extends('layouts.master')

@section('title', 'Permission Request Details')

@section('content')
<div class="container-fluid px-2">
    
    <!-- Breadcrumb -->
    <x-breadcrumb :items="[
        ['name' => 'My Permission Requests', 'url' => route('permissions.my-requests')],
        ['name' => 'Permission Details', 'url' => '#']
    ]" />
    
    <!-- Page Header -->
    <x-page-header title="PERMISSION REQUEST DETAILS" description="View permission request information">
        <a href="{{ route('permissions.my-requests') }}" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
            <i class="ti ti-arrow-left me-1"></i> Back
        </a>
    </x-page-header>
    
    <div class="row g-2">
        <!-- Main Details Column -->
        <div class="col-lg-7">
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
                            <div class="text-muted" style="font-size: 0.6rem;">Permission Type</div>
                            <div class="fw-semibold" style="font-size: 0.75rem;">
                                {{ str_replace('_', ' ', ucfirst($permissionRequest->permission_type)) }}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted" style="font-size: 0.6rem;">Status</div>
                            <div>
                                @if($permissionRequest->status == 'pending')
                                    <span class="badge bg-warning-subtle text-warning rounded-sm" style="font-size: 0.65rem;">Pending</span>
                                @elseif($permissionRequest->status == 'approved')
                                    <span class="badge bg-success-subtle text-success rounded-sm" style="font-size: 0.65rem;">Approved</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger rounded-sm" style="font-size: 0.65rem;">Rejected</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <div class="row g-1 mt-1">
                        <div class="col-md-6">
                            <div class="text-muted" style="font-size: 0.6rem;">Start Date & Time</div>
                            <div style="font-size: 0.75rem;">{{ \Carbon\Carbon::parse($permissionRequest->start_datetime)->format('d M Y H:i') }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted" style="font-size: 0.6rem;">End Date & Time</div>
                            <div style="font-size: 0.75rem;">{{ \Carbon\Carbon::parse($permissionRequest->end_datetime)->format('d M Y H:i') }}</div>
                        </div>
                    </div>
                    
                    <div class="row g-1 mt-1">
                        <div class="col-md-6">
                            <div class="text-muted" style="font-size: 0.6rem;">Duration</div>
                            <div class="fw-semibold" style="font-size: 0.75rem;">{{ $permissionRequest->hours }} hour(s)</div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted" style="font-size: 0.6rem;">Submitted On</div>
                            <div style="font-size: 0.7rem;">{{ \Carbon\Carbon::parse($permissionRequest->created_at)->format('d M Y H:i') }}</div>
                        </div>
                    </div>
                    
                    <div class="mt-2">
                        <div class="text-muted" style="font-size: 0.6rem;">Reason</div>
                        <div class="p-1 bg-light rounded-sm" style="font-size: 0.7rem;">{{ $permissionRequest->reason }}</div>
                    </div>
                    
                    @if($permissionRequest->permission_type == 'official_duty' && $permissionRequest->official_duty_description)
                    <div class="mt-2">
                        <div class="text-muted" style="font-size: 0.6rem;">Official Duty Description</div>
                        <div class="p-1 bg-light rounded-sm" style="font-size: 0.7rem;">{{ $permissionRequest->official_duty_description }}</div>
                    </div>
                    @endif
                    
                    @if($permissionRequest->rejection_reason)
                    <div class="mt-2">
                        <div class="text-muted text-danger" style="font-size: 0.6rem;">Rejection Reason</div>
                        <div class="p-1 bg-danger-subtle text-danger rounded-sm" style="font-size: 0.7rem;">{{ $permissionRequest->rejection_reason }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Approval Timeline Column -->
        <div class="col-lg-5">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-history text-primary me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Approval Timeline</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    @php
                        // Define approval stages for permission
                        $stages = [
                            'hod' => ['label' => 'HOD', 'icon' => 'user-tie'],
                            'hr_final' => ['label' => 'HR Officer', 'icon' => 'users'],
                        ];
                        
                        $currentLevel = $permissionRequest->current_approval_level;
                        $status = $permissionRequest->status;
                        
                        // Determine which stages are completed
                        $hodCompleted = $status != 'pending' || ($currentLevel != 'hod' && $currentLevel != 'acting');
                        $hrCompleted = $status == 'approved';
                    @endphp
                    
                    <div class="approval-timeline p-2">
                        <!-- Stage 1: HOD -->
                        <div class="timeline-item d-flex align-items-start gap-2 py-2 {{ $hodCompleted ? 'completed' : ($currentLevel == 'hod' ? 'current' : 'pending') }}">
                            <div class="timeline-dot">
                                @if($hodCompleted)
                                    <i class="ti ti-check"></i>
                                @elseif($currentLevel == 'hod')
                                    <i class="ti ti-loader"></i>
                                @else
                                    <i class="ti ti-circle"></i>
                                @endif
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-semibold" style="font-size: 0.7rem;">Head of Department</div>
                                <div class="text-muted" style="font-size: 0.6rem;">
                                    @if($hodCompleted)
                                        Approved
                                    @elseif($currentLevel == 'hod')
                                        Pending Review
                                    @else
                                        Pending
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <!-- Connector Line -->
                        <div class="timeline-connector {{ $hodCompleted ? 'completed' : '' }}"></div>
                        
                        <!-- Stage 2: HR Final -->
                        <div class="timeline-item d-flex align-items-start gap-2 py-2 {{ $hrCompleted ? 'completed' : ($currentLevel == 'hr_final' ? 'current' : 'pending') }}">
                            <div class="timeline-dot">
                                @if($hrCompleted)
                                    <i class="ti ti-check"></i>
                                @elseif($currentLevel == 'hr_final')
                                    <i class="ti ti-loader"></i>
                                @else
                                    <i class="ti ti-circle"></i>
                                @endif
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-semibold" style="font-size: 0.7rem;">HR Officer</div>
                                <div class="text-muted" style="font-size: 0.6rem;">
                                    @if($hrCompleted)
                                        Final Approval
                                    @elseif($currentLevel == 'hr_final')
                                        Processing
                                    @else
                                        Pending
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    @if($permissionRequest->status == 'approved')
                    <x-alert-success>
    Request has been fully approved.
</x-alert-success>
                    @elseif($permissionRequest->status == 'rejected')
                    <x-alert-danger :session="false">
    Request has been rejected.
</x-alert-danger>
                    @elseif($currentLevel == 'hod')
                    <x-alert-warning>
    Waiting for Head of Department approval.
</x-alert-warning>
                    @elseif($currentLevel == 'hr_final')
                    <x-alert-warning>
    Waiting for HR Officer final approval.
</x-alert-warning>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.approval-timeline {
    position: relative;
}

.timeline-item {
    position: relative;
    transition: all 0.2s ease;
}

.timeline-dot {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.7rem;
    flex-shrink: 0;
}

.timeline-item.completed .timeline-dot {
    background: #10B981;
    color: white;
}

.timeline-item.current .timeline-dot {
    background: #3B82F6;
    color: white;
    animation: timelinePulse 1.5s infinite;
}

.timeline-item.pending .timeline-dot {
    background: #E2E8F0;
    color: #94A3B8;
    border: 1px solid #CBD5E1;
}

.timeline-connector {
    width: 2px;
    height: 16px;
    background: #E2E8F0;
    margin-left: 13px;
    margin-top: -4px;
    margin-bottom: -4px;
}

.timeline-connector.completed {
    background: #10B981;
}

@keyframes timelinePulse {
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
</style>
@endsection