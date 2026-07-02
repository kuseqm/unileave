@extends('layouts.master')

@section('title', 'Leave Request Details')

@section('content')
<div class="container-fluid px-2">
    
    <!-- Breadcrumb -->
    <x-breadcrumb :items="[
        ['name' => 'My Leave Requests', 'url' => route('admin.leave-requests.my-requests')],
        ['name' 'Leave Request Details', 'url' => '#']
    ]" />
    
    <!-- Page Header -->
    <x-page-header title="LEAVE REQUEST DETAILS" description="View leave request information">
        <a href="{{ route('admin.leave-requests.my-requests') }}" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
            <i class="ti ti-arrow-left me-1"></i> Back
        </a>
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
                        <div class="col-md-6">
                            <div class="text-muted" style="font-size: 0.6rem;">Start Date</div>
                            <div style="font-size: 0.75rem;">{{ \Carbon\Carbon::parse($leaveRequest->start_date)->format('d M Y') }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted" style="font-size: 0.6rem;">End Date</div>
                            <div style="font-size: 0.75rem;">{{ \Carbon\Carbon::parse($leaveRequest->end_date)->format('d M Y') }}</div>
                        </div>
                    </div>
                    
                    <div class="row g-1 mt-1">
                        <div class="col-md-6">
                            <div class="text-muted" style="font-size: 0.6rem;">Total Days</div>
                            <div class="fw-semibold" style="font-size: 0.75rem;">{{ $leaveRequest->days }} day(s)</div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted" style="font-size: 0.6rem;">Current Approval Level</div>
                            <div class="fw-semibold" style="font-size: 0.75rem;">{{ $leaveRequest->approval_level_name }}</div>
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
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($leaveRequest->approvalLogs as $log)
                        <div class="list-group-item d-flex align-items-center gap-2 py-1 px-2">
                            <div class="rounded-circle bg-{{ $log->action == 'approved' ? 'success' : ($log->action == 'rejected' ? 'danger' : 'info') }}-subtle p-1" style="width: 24px; height: 24px; display: flex; align-items: center; justify-content: center;">
                                <i class="ti ti-{{ $log->action == 'approved' ? 'check' : ($log->action == 'rejected' ? 'x' : 'arrow-right') }}" style="font-size: 0.7rem;"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-semibold" style="font-size: 0.7rem;">
                                    {{ ucfirst($log->action) }} by {{ $log->approver->first_name ?? 'System' }} {{ $log->approver->last_name ?? '' }}
                                </div>
                                <div class="text-muted" style="font-size: 0.6rem;">
                                    From: {{ ucfirst(str_replace('_', ' ', $log->from_role)) }} → To: {{ ucfirst(str_replace('_', ' ', $log->to_role ?? 'Final')) }}
                                </div>
                                @if($log->comment)
                                <div class="small text-muted mt-0" style="font-size: 0.55rem;">"{{ $log->comment }}"</div>
                                @endif
                                <div class="text-muted" style="font-size: 0.55rem;">{{ $log->created_at->diffForHumans() }}</div>
                            </div>
                        </div>
                        @empty
                        <div class="list-group-item text-center py-2 text-muted" style="font-size: 0.7rem;">
                            No approval activity yet
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection