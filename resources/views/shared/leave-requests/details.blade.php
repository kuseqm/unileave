@extends('layouts.master')

@section('title', 'Leave Request Details')

@section('content')
<div class="container-fluid px-2">
    
    <!-- Breadcrumb -->
    <x-breadcrumb :items="[
        ['name' => 'Leave Requests', 'url' => route('leave-requests.my-requests')],
        ['name' => 'Request #' . $leaveRequest->id, 'url' => '#']
    ]" />
    
    <!-- Page Header -->
    <x-page-header title="LEAVE REQUEST DETAILS" description="View complete leave request information and approval timeline">
        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
            <i class="ti ti-arrow-left me-1"></i> Back
        </a>
    </x-page-header>
    
    <!-- Status Alert -->
    <div class="alert alert-light border mb-3 py-1 px-2 rounded-sm" style="font-size: 0.7rem;">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <i class="ti ti-info-circle me-1 text-primary"></i>
                <strong>Request ID:</strong> #{{ $leaveRequest->id }} | 
                <strong>Submitted:</strong> {{ $leaveRequest->created_at->format('d M Y, H:i') }}
            </div>
            <div class="d-flex gap-2">
                @if($leaveRequest->status === 'pending')
                    <span class="badge bg-warning rounded-sm px-2 py-1">
                        <i class="ti ti-clock me-1"></i> Pending
                    </span>
                @elseif($leaveRequest->status === 'approved')
                    <span class="badge bg-success rounded-sm px-2 py-1">
                        <i class="ti ti-check-circle me-1"></i> Approved
                    </span>
                @elseif($leaveRequest->status === 'rejected')
                    <span class="badge bg-danger rounded-sm px-2 py-1">
                        <i class="ti ti-x-circle me-1"></i> Rejected
                    </span>
                @elseif($leaveRequest->status === 'cancelled')
                    <span class="badge bg-secondary rounded-sm px-2 py-1">
                        <i class="ti ti-ban me-1"></i> Cancelled
                    </span>
                @endif
                
                @if($leaveRequest->acting_status === 'accepted')
                    <span class="badge bg-info rounded-sm px-2 py-1">
                        <i class="ti ti-user-check me-1"></i> Acting Accepted
                    </span>
                @endif
            </div>
        </div>
    </div>
    
    <div class="row g-2">
        <!-- LEFT COLUMN - Staff & Leave Info -->
        <div class="col-lg-5">
            <!-- Staff Information Card -->
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-user-circle text-primary me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">STAFF INFORMATION</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <div class="d-flex align-items-center mb-2">
                        <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 40px; height: 40px;">
                            <i class="ti ti-user text-primary" style="font-size: 1.2rem;"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold" style="font-size: 0.75rem;">{{ $leaveRequest->staff->full_name ?? 'N/A' }}</h6>
                            <small class="text-muted" style="font-size: 0.6rem;">Staff ID: {{ $leaveRequest->staff->staff_id ?? 'N/A' }}</small>
                        </div>
                    </div>
                    <table class="table table-sm table-borderless mb-0" style="font-size: 0.7rem;">
                        <tr><td class="text-muted ps-0" style="width: 35%;">Department:</td><td class="fw-semibold">{{ $leaveRequest->staff->department->name ?? 'N/A' }}</td></tr>
                        <tr><td class="text-muted ps-0">Position:</td><td class="fw-semibold">{{ $leaveRequest->staff->position ?? 'N/A' }}</td></tr>
                        <tr><td class="text-muted ps-0">Category:</td>
                            <td>
                                @if($leaveRequest->staff->staff_category === 'academic')
                                    <span class="badge bg-primary-subtle text-primary rounded-sm" style="font-size: 0.6rem;">Academic</span>
                                @elseif($leaveRequest->staff->staff_category === 'administrative')
                                    <span class="badge bg-info-subtle text-info rounded-sm" style="font-size: 0.6rem;">Administrative</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary rounded-sm" style="font-size: 0.6rem;">Supporting</span>
                                @endif
                            </td>
                        </tr>
                        <tr><td class="text-muted ps-0">Email:</td><td>{{ $leaveRequest->staff->email ?? 'N/A' }}</td></tr>
                    </table>
                </div>
            </div>
            
            <!-- Leave Details Card -->
            <div class="card rounded-sm mt-2">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-calendar text-primary me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">LEAVE DETAILS</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <table class="table table-sm table-borderless mb-0" style="font-size: 0.7rem;">
                        <tr><td class="text-muted ps-0" style="width: 40%;">Leave Type:</td><td class="fw-semibold">{{ $leaveRequest->leaveType->name ?? 'N/A' }}</td></tr>
                        <tr><td class="text-muted ps-0">Start Date:</td><td>{{ \Carbon\Carbon::parse($leaveRequest->start_date)->format('l, d M Y') }}</td></tr>
                        <tr><td class="text-muted ps-0">End Date:</td><td>{{ \Carbon\Carbon::parse($leaveRequest->end_date)->format('l, d M Y') }}</td></tr>
                        <tr><td class="text-muted ps-0">Number of Days:</td><td class="fw-semibold">{{ $leaveRequest->days }} days</td></tr>
                        @if($leaveRequest->half_day && $leaveRequest->half_day !== 'full')
                        <tr><td class="text-muted ps-0">Half Day:</td><td>{{ $leaveRequest->half_day === 'morning' ? 'Morning' : 'Afternoon' }}</td></tr>
                        @endif
                        <tr><td class="text-muted ps-0">Acting Staff:</td><td>{{ $leaveRequest->actingStaff->full_name ?? 'N/A' }}</td></tr>
                        <tr><td class="text-muted ps-0">Reason:</td><td class="text-muted">{{ $leaveRequest->reason ?? 'No reason provided' }}</td></tr>
                        @if($leaveRequest->rejection_reason)
                        <tr><td class="text-muted ps-0 text-danger">Rejection Reason:</td><td class="text-danger">{{ $leaveRequest->rejection_reason }}</td></tr>
                        @endif
                    </table>
                </div>
            </div>
            
            <!-- Contact Information Card -->
            @if($leaveRequest->contact_number_during_leave || $leaveRequest->leave_address || $leaveRequest->emergency_contact_name)
            <div class="card rounded-sm mt-2">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-phone-call text-primary me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">CONTACT INFORMATION</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <table class="table table-sm table-borderless mb-0" style="font-size: 0.7rem;">
                        @if($leaveRequest->emergency_contact_name)
                        <tr><td class="text-muted ps-0" style="width: 40%;">Emergency Contact:</td><td>{{ $leaveRequest->emergency_contact_name }} ({{ $leaveRequest->emergency_contact_phone ?? 'N/A' }})</td></tr>
                        @endif
                        @if($leaveRequest->contact_number_during_leave)
                        <tr><td class="text-muted ps-0">Contact during leave:</td><td>{{ $leaveRequest->contact_number_during_leave }}</td></tr>
                        @endif
                        @if($leaveRequest->leave_address)
                        <tr><td class="text-muted ps-0">Leave Address:</td><td>{{ $leaveRequest->leave_address }}</td></tr>
                        @endif
                    </table>
                </div>
            </div>
            @endif
        </div>
        
        <!-- RIGHT COLUMN - Approval Timeline & Documents -->
        <div class="col-lg-7">
            <!-- Approval Timeline Card -->
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <i class="ti ti-timeline text-primary me-1" style="font-size: 0.8rem;"></i>
                            <span style="font-size: 0.7rem; font-weight: 600;">APPROVAL TIMELINE</span>
                        </div>
                        @if($totalApprovalTime)
                        <span class="badge bg-success rounded-sm" style="font-size: 0.6rem;">
                            <i class="ti ti-timer"></i> Total: {{ $totalApprovalTime }}
                        </span>
                        @endif
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    @php
                        $approvalLogs = $leaveRequest->approvalLogs ?? collect();
                        $prevDate = null;
                        $currentUserRole = auth()->user()->getRoleNames()->first();
                        $isApprover = in_array($currentUserRole, ['admin', 'vice-chancellor', 'hod', 'dean', 'hr-officer', 'dvca', 'dvc-pfa']);
                    @endphp
                    
                    @if($approvalLogs->count() > 0 || $leaveRequest->acting_status !== 'pending')
                        <div class="timeline-container" style="max-height: 400px; overflow-y: auto;">
                            <!-- Submission Event -->
                            <div class="d-flex mb-2">
                                <div class="me-2">
                                    <div class="rounded-circle bg-info bg-opacity-10 d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">
                                        <i class="ti ti-file text-info" style="font-size: 0.8rem;"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between flex-wrap">
                                        <div>
                                            <span class="fw-semibold text-info" style="font-size: 0.7rem;">Submitted</span>
                                            <div class="text-muted" style="font-size: 0.6rem;">by {{ $leaveRequest->staff->full_name ?? 'Staff' }}</div>
                                        </div>
                                        <div class="text-muted" style="font-size: 0.6rem;">{{ $leaveRequest->created_at->format('d M Y, H:i') }}</div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Acting Staff Event -->
                            @if($leaveRequest->acting_status !== 'pending')
                            <div class="d-flex mb-2">
                                <div class="me-2">
                                    <div class="rounded-circle {{ $leaveRequest->acting_status === 'accepted' ? 'bg-success' : 'bg-danger' }} bg-opacity-10 d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">
                                        <i class="ti ti-user-check {{ $leaveRequest->acting_status === 'accepted' ? 'text-success' : 'text-danger' }}" style="font-size: 0.8rem;"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between flex-wrap">
                                        <div>
                                            <span class="fw-semibold {{ $leaveRequest->acting_status === 'accepted' ? 'text-success' : 'text-danger' }}" style="font-size: 0.7rem;">
                                                Acting Staff {{ ucfirst($leaveRequest->acting_status) }}
                                            </span>
                                            <div class="text-muted" style="font-size: 0.6rem;">by {{ $leaveRequest->actingStaff->full_name ?? 'N/A' }}</div>
                                        </div>
                                        <div class="text-muted" style="font-size: 0.6rem;">{{ $leaveRequest->acting_responded_at ? \Carbon\Carbon::parse($leaveRequest->acting_responded_at)->format('d M Y, H:i') : 'N/A' }}</div>
                                    </div>
                                </div>
                            </div>
                            @endif
                            
                            <!-- Approval Logs -->
                            @foreach($approvalLogs as $log)
                                @php
                                    $currentDate = \Carbon\Carbon::parse($log->created_at);
                                    $duration = $prevDate ? $currentDate->diffForHumans($prevDate, ['parts' => 2, 'short' => true]) : null;
                                    $prevDate = $currentDate;
                                    
                                    $actionIcon = match($log->action) {
                                        'approved' => '✅',
                                        'rejected' => '❌',
                                        default => '⏳'
                                    };
                                    
                                    $actionClass = match($log->action) {
                                        'approved' => 'text-success',
                                        'rejected' => 'text-danger',
                                        default => 'text-warning'
                                    };
                                    
                                    // Format role display
                                    $fromRoleDisplay = match($log->from_role) {
                                        'HOD' => 'Head of ' . ($leaveRequest->staff->department->name ?? 'Department'),
                                        'Dean' => 'Dean of ' . ($leaveRequest->staff->department->school->name ?? 'Faculty'),
                                        'HR' => 'Human Resources',
                                        'DVC' => $leaveRequest->staff->staff_category === 'academic' ? 'Deputy Vice Chancellor (Academic)' : 'Deputy Vice Chancellor (PFA)',
                                        'HR Logistics' => 'HR Logistics',
                                        default => $log->from_role
                                    };
                                    
                                    $toRoleDisplay = match($log->to_role) {
                                        'HOD' => 'Head of ' . ($leaveRequest->staff->department->name ?? 'Department'),
                                        'Dean' => 'Dean of ' . ($leaveRequest->staff->department->school->name ?? 'Faculty'),
                                        'HR' => 'Human Resources',
                                        'DVC' => $leaveRequest->staff->staff_category === 'academic' ? 'Deputy Vice Chancellor (Academic)' : 'Deputy Vice Chancellor (PFA)',
                                        'HR Logistics' => 'HR Logistics',
                                        'Completed' => 'Process Completed',
                                        default => $log->to_role
                                    };
                                @endphp
                                <div class="d-flex mb-2">
                                    <div class="me-2">
                                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">
                                            <span style="font-size: 0.8rem;">{{ $actionIcon }}</span>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between flex-wrap">
                                            <div>
                                                <span class="fw-semibold {{ $actionClass }}" style="font-size: 0.7rem;">
                                                    {{ ucfirst($log->action) }} by {{ $log->approver->full_name ?? 'System' }}
                                                </span>
                                                <div class="text-muted" style="font-size: 0.6rem;">
                                                    {{ $fromRoleDisplay }} → {{ $toRoleDisplay }}
                                                </div>
                                                @if($log->comment && $isApprover)
                                                    <div class="text-muted mt-1 p-1 bg-light rounded-sm" style="font-size: 0.6rem; font-style: italic;">
                                                        "{{ $log->comment }}"
                                                    </div>
                                                @elseif($log->comment && !$isApprover)
                                                    <div class="text-muted mt-1 p-1 bg-light rounded-sm" style="font-size: 0.6rem; font-style: italic;">
                                                        <i class="ti ti-lock"></i> Comment visible to approvers only
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="text-end">
                                                <div class="text-muted" style="font-size: 0.6rem;">{{ $currentDate->format('d M Y, H:i') }}</div>
                                                @if($duration)
                                                    <div class="text-muted" style="font-size: 0.55rem;">
                                                        <i class="ti ti-clock"></i> +{{ $duration }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-3 text-muted" style="font-size: 0.7rem;">
                            <i class="ti ti-timeline"></i> No approval activity yet. Waiting for initial review.
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Documents Card -->
            @if($leaveRequest->documents && $leaveRequest->documents->count() > 0)
            <div class="card rounded-sm mt-2">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-file-text text-primary me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">SUPPORTING DOCUMENTS</span>
                    </div>
                </div>
                <div class="card-body py-1 px-2">
                    @foreach($leaveRequest->documents as $doc)
                        <div class="d-flex justify-content-between align-items-center py-1 border-bottom" style="font-size: 0.65rem;">
                            <div>
                                <i class="ti ti-file me-1"></i>
                                {{ $doc->file_name }}
                                <span class="text-muted ms-1">({{ round($doc->file_size / 1024) }} KB)</span>
                            </div>
                            <a href="{{ Storage::url($doc->file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-sm" style="font-size: 0.55rem; padding: 0.1rem 0.4rem;">
                                <i class="ti ti-download"></i> View
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
            
            <!-- Print Letter Button (only for approved leaves) -->
            @if($leaveRequest->status === 'approved')
            <div class="mt-2">
                <button class="btn btn-outline-primary btn-sm rounded-sm w-100" style="font-size: 0.7rem; padding: 0.25rem 0.5rem;">
                    <i class="ti ti-printer me-1"></i> Print Approval Letter
                </button>
            </div>
            @endif
        </div>
    </div>
</div>

<style>
.timeline-container {
    max-height: 450px;
    overflow-y: auto;
}

.timeline-container::-webkit-scrollbar {
    width: 4px;
}

.timeline-container::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

.timeline-container::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 4px;
}
</style>
@endsection