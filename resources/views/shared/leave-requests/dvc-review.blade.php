@extends('layouts.master')

@section('title', 'DVC Review')

@section('content')
<div class="container-fluid px-2">
    
    <x-breadcrumb :items="[
        ['name' => 'DVC Review', 'url' => '#']
    ]" />
    
    <x-page-header title="DVC REVIEW" description="Review leave request with HR recommendations">
        <a href="{{ route('leave-requests.pending') }}" class="btn btn-outline-secondary btn-sm rounded-sm">
            <i class="ti ti-arrow-left me-1"></i> Back to Pending
        </a>
    </x-page-header>
    
    <div class="row">
        <!-- Left Column - Request Details -->
        <div class="col-lg-7">
            <!-- Staff Dossier -->
            <div class="card rounded-sm mb-3">
                <div class="card-header py-1 px-2 bg-light">
                    <i class="ti ti-user me-1"></i>
                    <span style="font-weight: 600;">Staff Information</span>
                </div>
                <div class="card-body p-2">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="text-muted" style="font-size: 0.6rem;">Name</div>
                            <div class="fw-semibold" style="font-size: 0.75rem;">{{ $leaveRequest->staff->full_name }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted" style="font-size: 0.6rem;">Staff ID</div>
                            <div class="fw-semibold" style="font-size: 0.75rem;">{{ $leaveRequest->staff->staff_id }}</div>
                        </div>
                        <div class="col-md-6 mt-2">
                            <div class="text-muted" style="font-size: 0.6rem;">Department</div>
                            <div style="font-size: 0.7rem;">{{ $leaveRequest->staff->department->name ?? 'N/A' }}</div>
                        </div>
                        <div class="col-md-6 mt-2">
                            <div class="text-muted" style="font-size: 0.6rem;">Staff Category</div>
                            <div style="font-size: 0.7rem;">{{ ucfirst($leaveRequest->staff->staff_category) }}</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Leave Request Details -->
            <div class="card rounded-sm mb-3">
                <div class="card-header py-1 px-2 bg-light">
                    <i class="ti ti-file-text me-1"></i>
                    <span style="font-weight: 600;">Leave Request</span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="text-muted" style="font-size: 0.6rem;">Leave Type</div>
                            <div class="fw-semibold" style="font-size: 0.75rem;">{{ $leaveRequest->leaveType->name ?? 'N/A' }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted" style="font-size: 0.6rem;">Days Requested</div>
                            <div class="fw-semibold" style="font-size: 0.75rem;">{{ $leaveRequest->days }} days</div>
                        </div>
                        <div class="col-12 mt-2">
                            <div class="text-muted" style="font-size: 0.6rem;">Leave Period</div>
                            <div style="font-size: 0.7rem;">{{ \Carbon\Carbon::parse($leaveRequest->start_date)->format('d M Y') }} - {{ \Carbon\Carbon::parse($leaveRequest->end_date)->format('d M Y') }}</div>
                        </div>
                        <div class="col-12 mt-2">
                            <div class="text-muted" style="font-size: 0.6rem;">Reason</div>
                            <div class="p-1 bg-light rounded-sm" style="font-size: 0.7rem;">{{ $leaveRequest->reason }}</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- HR Recommendations -->
            @if($hrLog)
            <div class="card rounded-sm mb-3 border-primary">
                <div class="card-header py-1 px-2 bg-primary-subtle">
                    <i class="ti ti-clipboard-list text-primary me-1"></i>
                    <span style="font-weight: 600;">HR Recommendations</span>
                </div>
                <div class="card-body">
                    <div class="text-muted" style="font-size: 0.65rem;">Comment from HR</div>
                    <div class="p-1 bg-light rounded-sm mb-2" style="font-size: 0.7rem; white-space: pre-line;">{{ $hrLog->comment ?? 'No additional comments' }}</div>
                    <div class="text-muted" style="font-size: 0.6rem;">Reviewed by: {{ $hrLog->approver->full_name ?? 'HR Officer' }} on {{ $hrLog->created_at->format('d M Y H:i') }}</div>
                </div>
            </div>
            @endif
            
            <!-- Department Coverage -->
            @if($departmentStaffOnLeave->count() > 0)
            <div class="card rounded-sm mb-3">
                <div class="card-header py-1 px-2 bg-light">
                    <i class="ti ti-building me-1"></i>
                    <span style="font-weight: 600;">Department Coverage</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0" style="font-size: 0.7rem;">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-2">Staff</th>
                                    <th>Leave Period</th>
                                    <th class="pe-2">Days</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($departmentStaffOnLeave as $staffLeave)
                                <tr>
                                    <td class="ps-2">{{ $staffLeave->staff->full_name }}</td>
                                    <td>{{ \Carbon\Carbon::parse($staffLeave->start_date)->format('d M') }} - {{ \Carbon\Carbon::parse($staffLeave->end_date)->format('d M Y') }}</td>
                                    <td class="pe-2">{{ $staffLeave->days }} days</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
            
            <!-- Approval Timeline -->
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <i class="ti ti-history me-1"></i>
                    <span style="font-weight: 600;">Approval Timeline</span>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @foreach($leaveRequest->approvalLogs->sortBy('created_at') as $log)
                        <div class="list-group-item py-1 px-2" style="font-size: 0.65rem;">
                            <div class="d-flex justify-content-between">
                                <span><strong>{{ ucfirst($log->from_role) }}</strong> → {{ ucfirst($log->to_role) }}</span>
                                <span class="text-{{ $log->action == 'approved' ? 'success' : 'danger' }}">{{ ucfirst($log->action) }}</span>
                                <span class="text-muted">{{ $log->created_at->format('d M H:i') }}</span>
                            </div>
                            @if($log->comment)
                            <div class="text-muted mt-0" style="font-size: 0.55rem;">"{{ $log->comment }}"</div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Right Column - DVC Actions -->
        <div class="col-lg-5">
            <!-- Acting Staff Info -->
            <div class="card rounded-sm mb-3">
                <div class="card-header py-1 px-2 bg-light">
                    <i class="ti ti-user-check me-1"></i>
                    <span style="font-weight: 600;">Acting Staff (Hold Position)</span>
                </div>
                <div class="card-body">
                    <div class="fw-semibold" style="font-size: 0.75rem;">{{ $leaveRequest->actingStaff->full_name ?? 'N/A' }}</div>
                    <div class="text-muted" style="font-size: 0.65rem;">{{ $leaveRequest->actingStaff->email ?? '' }}</div>
                    <div class="text-muted" style="font-size: 0.6rem;">{{ $leaveRequest->actingStaff->staff_id ?? '' }}</div>
                    <div class="mt-1">
                        @if($leaveRequest->acting_status == 'accepted')
                            <span class="badge bg-success-subtle text-success rounded-sm">Accepted</span>
                        @else
                            <span class="badge bg-warning-subtle text-warning rounded-sm">Pending</span>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- DVC Decision -->
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <i class="ti ti-decision me-1"></i>
                    <span style="font-weight: 600;">Your Decision</span>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('leave-requests.dvc-review.process', $leaveRequest->id) }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label class="form-label" style="font-size: 0.7rem;">DVC Comment (Optional)</label>
                            <textarea name="dvc_comment" class="form-control form-control-sm" rows="3" style="font-size: 0.7rem;" placeholder="Add your remarks..."></textarea>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" name="action" value="approve" class="btn btn-success btn-sm">
                                <i class="ti ti-check"></i> Approve & Forward to HR Logistics
                            </button>
                            <button type="submit" name="action" value="reject" class="btn btn-danger btn-sm">
                                <i class="ti ti-x"></i> Reject Request
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Summary Card -->
            <div class="card rounded-sm mt-3">
                <div class="card-header py-1 px-2 bg-light">
                    <i class="ti ti-info-circle me-1"></i>
                    <span style="font-weight: 600;">Summary</span>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted" style="font-size: 0.65rem;">Total Days</span>
                        <span class="fw-semibold" style="font-size: 0.7rem;">{{ $leaveRequest->days }} days</span>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted" style="font-size: 0.65rem;">Acting Staff</span>
                        <span class="fw-semibold" style="font-size: 0.7rem;">{{ $leaveRequest->actingStaff->full_name ?? 'N/A' }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted" style="font-size: 0.65rem;">HR Status</span>
                        <span class="badge bg-success-subtle text-success rounded-sm">Reviewed</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection