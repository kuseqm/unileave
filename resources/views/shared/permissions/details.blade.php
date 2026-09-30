@extends('layouts.master')

@section('title', 'Permission Request Details')

@section('content')
<div class="container-fluid px-2">

    <!-- Breadcrumb -->
    <x-breadcrumb :items="[
        ['name' => 'Permissions', 'url' => route('permissions.my-requests')],
        ['name' => 'Request #' . $permissionRequest->id, 'url' => '#']

    ]" />

    <!-- Page Header -->
    <x-page-header title="PERMISSION REQUEST DETAILS" description="View complete permission request information and approval timeline">
        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
            <i class="ti ti-arrow-left me-1"></i> Back
        </a>
    </x-page-header>

    <!-- Status Alert -->
    <x-alert-light class="mb-3 py-1 px-2 rounded-sm" style="font-size: 0.7rem;">

        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <i class="ti ti-info-circle me-1 text-primary"></i>
                <strong>Request ID:</strong> #{{ $permissionRequest->id }} |
                <strong>Submitted:</strong> {{ $permissionRequest->created_at->format('d M Y, H:i') }}
            </div>
            <div class="d-flex gap-2">
                @if($permissionRequest->status === 'pending')
                    <span class="badge bg-warning rounded-sm px-2 py-1">
                        <i class="ti ti-clock me-1"></i> Pending
                    </span>
                @elseif($permissionRequest->status === 'approved')
                    <span class="badge bg-success rounded-sm px-2 py-1">
                        <i class="ti ti-check-circle me-1"></i> Approved
                    </span>
                @elseif($permissionRequest->status === 'rejected')
                    <span class="badge bg-danger rounded-sm px-2 py-1">
                        <i class="ti ti-x-circle me-1"></i> Rejected
                    </span>
                @elseif($permissionRequest->status === 'cancelled')
                    <span class="badge bg-secondary rounded-sm px-2 py-1">
                        <i class="ti ti-ban me-1"></i> Cancelled
                    </span>
                @endif

                @if($permissionRequest->hod_status === 'approved' && $permissionRequest->hr_status === 'pending')
                    <span class="badge bg-info rounded-sm px-2 py-1">
                        <i class="ti ti-building"></i> Awaiting HR
                    </span>
                @endif
            </div>
        
</x-alert-light>
    </div>

    <div class="row g-3">
        <!-- LEFT COLUMN - Staff & Permission Info -->
        <div class="col-md-5">
            <!-- Staff Information Card -->
            <div class="card rounded-sm mb-3 border shadow-sm">
                <div class="card-header bg-light py-2 px-3 border-bottom">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-user-circle text-primary me-2" style="font-size: 1.1rem;"></i>
                        <span style="font-size: 0.75rem; font-weight: 600;">STAFF INFORMATION</span>
                    </div>
                </div>
                <div class="card-body p-3">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                            <i class="ti ti-user text-primary" style="font-size: 1.5rem;"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold">{{ $permissionRequest->staff->full_name ?? 'N/A' }}</h6>
                            <small class="text-muted">Staff ID: {{ $permissionRequest->staff->staff_id ?? 'N/A' }}</small>
                        </div>
                    </div>
                    <table class="table table-sm table-borderless mb-0" style="font-size: 0.75rem;">
                        <tr>
                            <td class="text-muted ps-0" style="width: 35%;">Department:</td>
                            <td class="fw-semibold">{{ $permissionRequest->staff->department->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-0">Position:</td>
                            <td class="fw-semibold">{{ $permissionRequest->staff->position ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-0">Category:</td>
                            <td>
                                @if($permissionRequest->staff->staff_category === 'academic')
                                    <span class="badge bg-primary-subtle text-primary rounded-sm">Academic</span>
                                @elseif($permissionRequest->staff->staff_category === 'administrative')
                                    <span class="badge bg-info-subtle text-info rounded-sm">Administrative</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary rounded-sm">Supporting</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-0">Email:</td>
                            <td>{{ $permissionRequest->staff->email ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-0">Phone:</td>
                            <td>{{ $permissionRequest->staff->phone ?? 'N/A' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <!-- Permission Details Card -->
            <div class="card rounded-sm mb-3 border shadow-sm">
                <div class="card-header bg-light py-2 px-3 border-bottom">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-calendar text-primary me-2" style="font-size: 1.1rem;"></i>
                        <span style="font-size: 0.75rem; font-weight: 600;">PERMISSION DETAILS</span>
                    </div>
                </div>
                <div class="card-body p-3">
                    <table class="table table-sm table-borderless mb-0" style="font-size: 0.75rem;">
                        <tr>
                            <td class="text-muted ps-0" style="width: 40%;">Permission Type:</td>
                            <td class="fw-semibold">
                                @php
                                    $typeLabels = [
                                        'late_arrival' => '⏰ Late Arrival',
                                        'early_departure' => '🏃 Early Departure',
                                        'official_duty' => '📋 Official Duty',
                                        'personal' => '🏠 Personal',
                                        'other' => '📝 Other'
                                    ];
                                @endphp
                                {{ $typeLabels[$permissionRequest->permission_type] ?? $permissionRequest->permission_type }}
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-0">Date:</td>
                            <td class="fw-semibold">{{ \Carbon\Carbon::parse($permissionRequest->date)->format('l, d M Y') }}</td>
                        </tr>
                        @if($permissionRequest->start_time && $permissionRequest->end_time)
                        <tr>
                            <td class="text-muted ps-0">Time:</td>
                            <td class="fw-semibold">{{ \Carbon\Carbon::parse($permissionRequest->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($permissionRequest->end_time)->format('h:i A') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-0">Duration:</td>
                            <td class="fw-semibold">{{ $permissionRequest->hours }} hours</td>
                        </tr>
                        @endif
                        <tr>
                            <td class="text-muted ps-0">Reason:</td>
                            <td class="text-muted">{{ $permissionRequest->reason ?? 'No reason provided' }}</td>
                        </tr>
                        @if($permissionRequest->hod_comment)
                        <tr>
                            <td class="text-muted ps-0">HOD Comment:</td>
                            <td class="text-muted">{{ $permissionRequest->hod_comment }}</td>
                        </tr>
                        @endif
                        @if($permissionRequest->hr_comment)
                        <tr>
                            <td class="text-muted ps-0">HR Comment:</td>
                            <td class="text-muted">{{ $permissionRequest->hr_comment }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>
            
            <!-- Approval Status Summary Card -->
            <div class="card rounded-sm mb-3 border shadow-sm">
                <div class="card-header bg-light py-2 px-3 border-bottom">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-chart-donut text-primary me-2" style="font-size: 1.1rem;"></i>
                        <span style="font-size: 0.75rem; font-weight: 600;">APPROVAL STATUS</span>
                    </div>
                </div>
                <div class="card-body p-3">
                    <div class="row g-2">
                        <div class="col-6">
                            <div class="text-center p-2 border rounded-sm">
                                <div class="text-muted mb-1" style="font-size: 0.65rem;">HOD Approval</div>
                                @if($permissionRequest->hod_status === 'pending')
                                    <span class="badge bg-warning rounded-sm"><i class="ti ti-clock"></i> Pending</span>
                                @elseif($permissionRequest->hod_status === 'approved')
                                    <span class="badge bg-success rounded-sm"><i class="ti ti-check"></i> Approved</span>
                                @else
                                    <span class="badge bg-danger rounded-sm"><i class="ti ti-x"></i> Rejected</span>
                                @endif
                                @if($permissionRequest->hod_acted_at)
                                    <div class="text-muted mt-1" style="font-size: 0.6rem;">
                                        {{ \Carbon\Carbon::parse($permissionRequest->hod_acted_at)->format('d M Y, H:i') }}
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-2 border rounded-sm">
                                <div class="text-muted mb-1" style="font-size: 0.65rem;">HR Approval</div>
                                @if($permissionRequest->hr_status === 'pending')
                                    <span class="badge bg-warning rounded-sm"><i class="ti ti-clock"></i> Pending</span>
                                @elseif($permissionRequest->hr_status === 'approved')
                                    <span class="badge bg-success rounded-sm"><i class="ti ti-check"></i> Approved</span>
                                @elseif($permissionRequest->hr_status === 'rejected')
                                    <span class="badge bg-danger rounded-sm"><i class="ti ti-x"></i> Rejected</span>
                                @else
                                    <span class="badge bg-secondary rounded-sm">N/A</span>
                                @endif
                                @if($permissionRequest->hr_acted_at)
                                    <div class="text-muted mt-1" style="font-size: 0.6rem;">
                                        {{ \Carbon\Carbon::parse($permissionRequest->hr_acted_at)->format('d M Y, H:i') }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @if($totalApprovalTime)
                        <div class="mt-2 text-center">
                            <hr class="my-2">
                            <div class="text-success" style="font-size: 0.7rem;">
                                <i class="ti ti-timer"></i> Total Approval Time: {{ $totalApprovalTime }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- RIGHT COLUMN - Approval Timeline -->
        <div class="col-md-7">
            <!-- Approval Timeline Card -->
            <div class="card rounded-sm mb-3 border shadow-sm">
                <div class="card-header bg-light py-2 px-3 border-bottom">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <i class="ti ti-timeline text-primary me-2" style="font-size: 1.1rem;"></i>
                            <span style="font-size: 0.75rem; font-weight: 600;">APPROVAL TIMELINE</span>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if(count($approvalTimeline) > 0)
                        <div class="timeline-container p-3">
                            @php $prevDate = null; @endphp
                            @foreach($approvalTimeline as $index => $timeline)
                                @php
                                    $currentDate = \Carbon\Carbon::parse($timeline['date']);
                                    $duration = $prevDate ? $currentDate->diffForHumans($prevDate, ['parts' => 2, 'short' => true]) : ($index === 0 ? 'N/A' : null);
                                    $prevDate = $currentDate;
                                    
                                    $actionIcon = match($timeline['action']) {
                                        'approved' => '✅',
                                        'submitted' => '📝',
                                        'rejected' => '❌',
                                        default => '⏳'
                                    };
                                    
                                    $actionClass = match($timeline['action']) {
                                        'approved' => 'text-success',
                                        'submitted' => 'text-info',
                                        'rejected' => 'text-danger',
                                        default => 'text-warning'
                                    };
                                    
                                    $bgClass = match($timeline['action']) {
                                        'approved' => 'bg-success bg-opacity-10',
                                        'submitted' => 'bg-info bg-opacity-10',
                                        'rejected' => 'bg-danger bg-opacity-10',
                                        default => 'bg-warning bg-opacity-10'
                                    };
                                @endphp
                                <div class="timeline-item d-flex mb-3">
                                    <div class="timeline-icon me-3">
                                        <div class="rounded-circle {{ $bgClass }} d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                            <span style="font-size: 1rem;">{{ $actionIcon }}</span>
                                        </div>
                                    </div>
                                    <div class="timeline-content flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-start flex-wrap">
                                            <div>
                                                <span class="fw-semibold {{ $actionClass }}" style="font-size: 0.75rem;">
                                                    {{ ucfirst($timeline['action']) }}
                                                </span>
                                                <div class="text-muted" style="font-size: 0.65rem;">
                                                    by <strong>{{ $timeline['by'] }}</strong>
                                                    @if(isset($timeline['role']))
                                                        <span class="badge bg-light text-dark ms-1">{{ $timeline['role'] }}</span>
                                                    @endif
                                                </div>
                                                @if($timeline['comment'] && $timeline['action'] !== 'submitted')
                                                    <div class="text-muted mt-1 p-1 bg-light rounded-sm" style="font-size: 0.65rem; font-style: italic; max-width: 90%;">
                                                        "{{ $timeline['comment'] }}"
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="text-end">
                                                <div class="text-muted" style="font-size: 0.65rem;">
                                                    {{ $currentDate->format('d M Y, H:i') }}
                                                </div>
                                                @if($duration && $timeline['action'] !== 'submitted')
                                                    <div class="text-muted" style="font-size: 0.6rem;">
                                                        <i class="ti ti-clock"></i> +{{ $duration }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            
                            <!-- Final Status -->
                            <div class="timeline-item d-flex">
                                <div class="timeline-icon me-3">
                                    <div class="rounded-circle bg-light d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                        <span style="font-size: 1rem;">
                                            @if($permissionRequest->status === 'approved') ✅
                                            @elseif($permissionRequest->status === 'rejected') ❌
                                            @elseif($permissionRequest->status === 'cancelled') 🚫
                                            @else ⏳
                                            @endif
                                        </span>
                                    </div>
                                </div>
                                <div class="timeline-content flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <span class="fw-semibold" style="font-size: 0.75rem;">
                                                Final Status: 
                                                @if($permissionRequest->status === 'approved') 
                                                    <span class="text-success">Approved</span>
                                                @elseif($permissionRequest->status === 'rejected')
                                                    <span class="text-danger">Rejected</span>
                                                @elseif($permissionRequest->status === 'cancelled')
                                                    <span class="text-secondary">Cancelled</span>
                                                @else
                                                    <span class="text-warning">In Progress</span>
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-4 text-muted" style="font-size: 0.7rem;">
                            <i class="ti ti-timeline"></i> No approval activity yet.
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Action Buttons (for approvers) -->
            <div class="mt-3 d-flex justify-content-end align-items-center">
                <div>
                    @php
                        $realHodId = $permissionRequest->staff->department->hod_staff_id ?? null;
                        // Self-collision: the applicant IS their own department's
                        // HOD, so it's the acting HOD they picked who reviews it,
                        // not necessarily anyone holding the 'hod' role.
                        $canReviewAsHod = $realHodId == $permissionRequest->staff_id
                            ? $permissionRequest->acting_hod_id == auth()->id()
                            : $realHodId == auth()->id();
                    @endphp
                    @if($canReviewAsHod && $permissionRequest->hod_status === 'pending')
                        <!-- Approve Button -->
                        <button type="button" class="btn btn-success btn-sm rounded-sm me-2" style="font-size: 0.7rem;" data-bs-toggle="modal" data-bs-target="#approveModal">
                            <i class="ti ti-check me-1"></i> Approve
                        </button>
                        <!-- Reject Button -->
                        <button type="button" class="btn btn-danger btn-sm rounded-sm" style="font-size: 0.7rem;" data-bs-toggle="modal" data-bs-target="#rejectModal">
                            <i class="ti ti-x me-1"></i> Reject
                        </button>
                    @endif
                    
                    @if((auth()->user()->hasRole('hr-officer') || auth()->user()->hasRole('hr-manager')) && $permissionRequest->hod_status === 'approved' && $permissionRequest->hr_status === 'pending')
                        <!-- Final Approve Button -->
                        <button type="button" class="btn btn-success btn-sm rounded-sm me-2" style="font-size: 0.7rem;" data-bs-toggle="modal" data-bs-target="#hrApproveModal">
                            <i class="ti ti-check me-1"></i> Final Approve
                        </button>
                        <!-- Final Reject Button -->
                        <button type="button" class="btn btn-danger btn-sm rounded-sm" style="font-size: 0.7rem;" data-bs-toggle="modal" data-bs-target="#hrRejectModal">
                            <i class="ti ti-x me-1"></i> Reject
                        </button>
                    @endif
                    
                    @if(auth()->id() === $permissionRequest->staff_id && $permissionRequest->status === 'pending')
                        <button type="button" class="btn btn-outline-danger btn-sm rounded-sm" style="font-size: 0.7rem;" onclick="confirmCancel()">
                            <i class="ti ti-ban me-1"></i> Cancel Request
                        </button>
                        
                        <form id="cancel-form" action="{{ route('permissions.cancel', $permissionRequest->id) }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- HOD Approve Modal -->
<div class="modal fade" id="approveModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-sm">
            <form action="{{ route('permissions.hod.approve', $permissionRequest->id) }}" method="POST">
                @csrf
                <div class="modal-header py-2 px-3 border-bottom">
                    <h5 class="modal-title" style="font-size: 0.9rem;">
                        <i class="ti ti-check-circle me-2 text-success"></i> Approve Permission Request
                    </h5>
                    <button type="button" class="btn-close py-0" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body py-3 px-3">
                    <p class="text-muted mb-2" style="font-size: 0.75rem;">
                        Approve permission request from <strong>{{ $permissionRequest->staff->full_name }}</strong>
                    </p>
                    <x-alert-info>
    This will send the request to HR for final approval.
</x-alert-info>
                </div>
                <div class="modal-footer py-2 px-3">
                    <button type="button" class="btn btn-secondary btn-sm rounded-sm" data-bs-dismiss="modal" style="font-size: 0.7rem;">Cancel</button>
                    <button type="submit" class="btn btn-success btn-sm rounded-sm" style="font-size: 0.7rem;">
                        <i class="ti ti-check"></i> Confirm Approve
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- HOD Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-sm">
            <form action="{{ route('permissions.hod.reject', $permissionRequest->id) }}" method="POST">
                @csrf
                <div class="modal-header py-2 px-3 border-bottom">
                    <h5 class="modal-title" style="font-size: 0.9rem;">
                        <i class="ti ti-x-circle me-2 text-danger"></i> Reject Permission Request
                    </h5>
                    <button type="button" class="btn-close py-0" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body py-3 px-3">
                    <div class="mb-2">
                        <label class="form-label" style="font-size: 0.7rem;">Reason for Rejection *</label>
                        <textarea name="hod_comment" class="form-control form-control-sm rounded-sm" rows="3" required style="font-size: 0.7rem;"></textarea>
                    </div>
                </div>
                <div class="modal-footer py-2 px-3">
                    <button type="button" class="btn btn-secondary btn-sm rounded-sm" data-bs-dismiss="modal" style="font-size: 0.7rem;">Cancel</button>
                    <button type="submit" class="btn btn-danger btn-sm rounded-sm" style="font-size: 0.7rem;">
                        <i class="ti ti-x"></i> Confirm Reject
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- HR Approve Modal -->
<div class="modal fade" id="hrApproveModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-sm">
            <form action="{{ route('permissions.hr.approve', $permissionRequest->id) }}" method="POST">
                @csrf
                <div class="modal-header py-2 px-3 border-bottom">
                    <h5 class="modal-title" style="font-size: 0.9rem;">
                        <i class="ti ti-check-circle me-2 text-success"></i> Final Approval (HR)
                    </h5>
                    <button type="button" class="btn-close py-0" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body py-3 px-3">
                    <p class="text-muted mb-0" style="font-size: 0.75rem;">
                        Confirm final approval for <strong>{{ $permissionRequest->staff->full_name }}</strong>'s permission request.
                    </p>
                </div>
                <div class="modal-footer py-2 px-3">
                    <button type="button" class="btn btn-secondary btn-sm rounded-sm" data-bs-dismiss="modal" style="font-size: 0.7rem;">Cancel</button>
                    <button type="submit" class="btn btn-success btn-sm rounded-sm" style="font-size: 0.7rem;">
                        <i class="ti ti-check"></i> Approve
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- HR Reject Modal -->
<div class="modal fade" id="hrRejectModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-sm">
            <form action="{{ route('permissions.hr.reject', $permissionRequest->id) }}" method="POST">
                @csrf
                <div class="modal-header py-2 px-3 border-bottom">
                    <h5 class="modal-title" style="font-size: 0.9rem;">
                        <i class="ti ti-x-circle me-2 text-danger"></i> Reject Permission Request (HR)
                    </h5>
                    <button type="button" class="btn-close py-0" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body py-3 px-3">
                    <div class="mb-2">
                        <label class="form-label" style="font-size: 0.7rem;">Reason for Rejection *</label>
                        <textarea name="hr_comment" class="form-control form-control-sm rounded-sm" rows="3" required style="font-size: 0.7rem;"></textarea>
                    </div>
                </div>
                <div class="modal-footer py-2 px-3">
                    <button type="button" class="btn btn-secondary btn-sm rounded-sm" data-bs-dismiss="modal" style="font-size: 0.7rem;">Cancel</button>
                    <button type="submit" class="btn btn-danger btn-sm rounded-sm" style="font-size: 0.7rem;">
                        <i class="ti ti-x"></i> Reject
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.timeline-container {
    max-height: 450px;
    overflow-y: auto;
}

.timeline-item {
    position: relative;
}

.timeline-item:not(:last-child) .timeline-content::after {
    content: '';
    position: absolute;
    left: -18px;
    top: 36px;
    bottom: -12px;
    width: 2px;
    background-color: #e5e7eb;
}

.timeline-icon {
    position: relative;
    z-index: 1;
}

/* Custom scrollbar */
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

.timeline-container::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}
</style>

<script>
function confirmCancel() {
    confirmAction('Are you sure you want to cancel this permission request? This action cannot be undone.', function () {
        document.getElementById('cancel-form').submit();
    });
}
</script>

<x-live-refresh :url="route('permissions.show.heartbeat', $permissionRequest->id)" />
@endsection