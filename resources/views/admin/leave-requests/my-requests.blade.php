@extends('layouts.master')

@section('title', 'My Leave Requests')

@section('content')
<div class="container-fluid px-2">
    
    <!-- Breadcrumb -->
    <x-breadcrumb :items="[
        ['name' => 'My Leave Requests', 'url' => '#']
    ]" />
    
    <!-- Page Header -->
    <x-page-header title="MY LEAVE REQUESTS" description="View and manage your leave requests">
        <a href="{{ route('admin.leave-requests.create') }}" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
            <i class="ti ti-plus me-1"></i> Apply for Leave
        </a>
    </x-page-header>
    
    <!-- Stats Cards -->
    <div class="row g-2 mb-3">
        <x-stats-card value="{{ $stats['total'] }}" label="Total Requests" subtext="all time" />
        <x-stats-card value="{{ $stats['pending'] }}" label="Pending" subtext="awaiting approval" color="warning" />
        <x-stats-card value="{{ $stats['approved'] }}" label="Approved" subtext="granted" color="success" />
        <x-stats-card value="{{ $stats['rejected'] }}" label="Rejected" subtext="denied" color="danger" />
    </div>
    
    <x-alert-success />

    <x-alert-danger />

    <div class="row g-2">
        <!-- Leave Requests Table - main column -->
        <div class="col-lg-8">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-calendar text-primary me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Leave Requests</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0" style="font-size: 0.75rem;">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-2 py-1">#</th>
                                    <th class="py-1">Leave Type</th>
                                    <th class="py-1">Dates</th>
                                    <th class="py-1">Days</th>
                                    <th class="py-1">Acting Staff</th>
                                    <th class="py-1">Status</th>
                                    <th class="py-1">Approval Level</th>
                                    <th class="pe-2 py-1">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($leaveRequests as $index => $request)
                                <tr>
                                    <td class="ps-2 py-1">{{ $leaveRequests->firstItem() + $index }}</td>
                                    <td class="py-1">{{ $request->leaveType->name ?? 'N/A' }} ({{ $request->leaveType->code ?? '' }})</td>
                                    <td class="py-1">{{ \Carbon\Carbon::parse($request->start_date)->format('d M Y') }} - {{ \Carbon\Carbon::parse($request->end_date)->format('d M Y') }}</td>
                                    <td class="py-1">{{ $request->days }} day(s)</td>
                                    <td class="py-1">
                                        @if($request->actingStaff)
                                            {{ $request->actingStaff->first_name }} {{ $request->actingStaff->last_name }}
                                            <br>
                                            @if($request->acting_status == 'pending')
                                                <span class="badge bg-warning-subtle text-warning rounded-sm" style="font-size: 0.55rem;">Awaiting Response</span>
                                            @elseif($request->acting_status == 'accepted')
                                                <span class="badge bg-success-subtle text-success rounded-sm" style="font-size: 0.55rem;">Accepted</span>
                                            @else
                                                <span class="badge bg-danger-subtle text-danger rounded-sm" style="font-size: 0.55rem;">Declined</span>
                                            @endif
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td class="py-1">
                                        @if($request->status == 'pending')
                                            <span class="badge bg-warning-subtle text-warning rounded-sm" style="font-size: 0.6rem;">Pending</span>
                                        @elseif($request->status == 'approved')
                                            <span class="badge bg-success-subtle text-success rounded-sm" style="font-size: 0.6rem;">Approved</span>
                                        @elseif($request->status == 'rejected')
                                            <span class="badge bg-danger-subtle text-danger rounded-sm" style="font-size: 0.6rem;">Rejected</span>
                                        @elseif($request->status == 'cancelled')
                                            <span class="badge bg-secondary-subtle text-secondary rounded-sm" style="font-size: 0.6rem;">Cancelled</span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary rounded-sm" style="font-size: 0.6rem;">{{ ucfirst($request->status) }}</span>
                                        @endif
                                    </td>
                                    <td class="py-1">{{ $request->approval_level_name }}</td>
                                    <td class="pe-2 py-1">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.leave-requests.show', $request->id) }}" class="btn btn-sm btn-outline-info rounded-sm" title="View" style="padding: 0.15rem 0.3rem;">
                                                <i class="ti ti-eye" style="font-size: 0.7rem;"></i>
                                            </a>
                                            @if($request->status == 'pending' && $request->acting_status == 'pending')
                                            <button type="button" class="btn btn-sm btn-outline-danger rounded-sm" onclick="cancelRequest({{ $request->id }})" title="Cancel" style="padding: 0.15rem 0.3rem;">
                                                <i class="ti ti-x" style="font-size: 0.7rem;"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center py-3 text-muted" style="font-size: 0.7rem;">
                                        No leave requests found
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer py-1 px-2">
                    <x-pagination :items="$leaveRequests" :perPageOptions="[10, 25, 50, 100]" />
                </div>
            </div>
        </div>

        <!-- Information - narrow column -->
        <div class="col-lg-4">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-info-circle text-info me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Information</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <div class="mb-2">
                        <div class="fw-semibold" style="font-size: 0.7rem;"><i class="ti ti-flag me-1 text-primary"></i> Understanding Status</div>
                        <ul class="mb-0 ps-3" style="font-size: 0.65rem; color: #555;">
                            <li><span class="badge bg-warning-subtle text-warning rounded-sm">Pending</span> - still moving through approval</li>
                            <li><span class="badge bg-success-subtle text-success rounded-sm">Approved</span> - granted, letter available</li>
                            <li><span class="badge bg-danger-subtle text-danger rounded-sm">Rejected</span> - denied at some stage</li>
                            <li><span class="badge bg-secondary-subtle text-secondary rounded-sm">Cancelled</span> - withdrawn by you</li>
                        </ul>
                    </div>

                    <div class="mb-2">
                        <div class="fw-semibold" style="font-size: 0.7rem;"><i class="ti ti-route me-1 text-primary"></i> Approval Level</div>
                        <p class="mb-0" style="font-size: 0.65rem; color: #555;">
                            Shows where your request currently sits in the chain (HOD &rarr; Dean &rarr; DVCA/DVC-PFA &rarr; HR Logistics). "Acting Staff" must accept before the request can move forward.
                        </p>
                    </div>

                    <x-alert-light class="mb-0 py-1 px-2 rounded-sm">
                        <div class="d-flex align-items-start gap-1">
                            <i class="ti ti-tips text-warning mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">Tips</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">&bull; Click the eye icon to see full approval progress</small><br>
                                <small class="text-muted" style="font-size: 0.6rem;">&bull; Cancel is only available while Pending and your acting staff hasn't responded yet</small>
                            </div>
                        </div>
                    </x-alert-light>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function cancelRequest(id) {
    confirmAction('Are you sure you want to cancel this leave request?', function () {
        fetch(`/admin/leave-requests/${id}/cancel`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json'
            }
        }).then(response => response.json()).then(data => {
            if(data.success) {
                location.reload();
            } else {
                alert('Failed to cancel request');
            }
        });
    });
}
</script>
@endsection