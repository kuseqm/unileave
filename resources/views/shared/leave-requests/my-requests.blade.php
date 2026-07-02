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
        <a href="{{ route('leave-requests.create') }}" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
            <i class="ti ti-plus me-1"></i> Apply for Leave
        </a>
    </x-page-header>
    
    <!-- Stats Cards -->
        <!-- Stats Cards -->
        <div class="row g-2 mb-3">
            <x-stats-card value="{{ $stats['total'] ?? 0 }}" label="Total Requests" subtext="all time" />
            <x-stats-card value="{{ $stats['pending'] ?? 0 }}" label="Pending" subtext="awaiting approval" color="warning" />
            <x-stats-card value="{{ $stats['approved'] ?? 0 }}" label="Approved" subtext="granted" color="success" />
            <x-stats-card value="{{ $stats['rejected'] ?? 0 }}" label="Rejected" subtext="denied" color="danger" />
        </div>
    
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show py-1 px-2 mb-2 rounded-sm" style="font-size: 0.7rem;" role="alert">
        <i class="ti ti-check-circle me-1"></i> {{ session('success') }}
        <button type="button" class="btn-close py-0" style="font-size: 0.6rem;" data-bs-dismiss="alert"></button>
    </div>
    @endif
    
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show py-1 px-2 mb-2 rounded-sm" style="font-size: 0.7rem;" role="alert">
        <i class="ti ti-alert-circle me-1"></i> {{ session('error') }}
        <button type="button" class="btn-close py-0" style="font-size: 0.6rem;" data-bs-dismiss="alert"></button>
    </div>
    @endif
    
    <!-- Leave Requests Table -->
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
                                    {{ $request->actingStaff->first_name }} @if($request->actingStaff->middle_name){{ $request->actingStaff->middle_name }} @endif{{ $request->actingStaff->last_name }}
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
                                <x-action-buttons 
                                    viewRoute="{{ route('leave-requests.show', $request->id) }}"
                                    showCancel="{{ $request->status == 'pending' && $request->acting_status == 'pending' }}"
                                    cancelRoute="{{ route('leave-requests.cancel', $request->id) }}"
                                    cancelId="{{ $request->id }}"
                                    cancelName="{{ $request->leaveType->name ?? 'Leave Request' }}"
                                />
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

<script>
function cancelRequest(id) {
    if(confirm('Are you sure you want to cancel this leave request?')) {
        fetch(`/leave-requests/${id}/cancel`, {
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
    }
}
function showCancelModal(id, name) {
    document.getElementById('cancelConfirmBtn').href = '/leave-requests/' + id + '/cancel';
    new bootstrap.Modal(document.getElementById('cancelModal')).show();
}

// Keep this for backward compatibility (if needed)
function cancelRequest(id) {
    showCancelModal(id, '');
}
</script>

<!-- Cancel Request Modal -->
<div class="modal fade" id="cancelModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-sm">
            <div class="modal-header py-1 px-2">
                <h5 class="modal-title" style="font-size: 0.8rem;">Cancel Leave Request</h5>
                <button type="button" class="btn-close py-0" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body py-2 px-2">
                <p style="font-size: 0.75rem;">Are you sure you want to cancel this leave request?</p>
                <p class="text-muted mb-0" style="font-size: 0.7rem;">This action cannot be undone.</p>
            </div>
            <div class="modal-footer py-1 px-2">
                <button type="button" class="btn btn-secondary btn-sm rounded-sm" data-bs-dismiss="modal" style="font-size: 0.7rem;">Close</button>
                <a href="#" id="cancelConfirmBtn" class="btn btn-warning btn-sm rounded-sm" style="font-size: 0.7rem;">Cancel Request</a>
            </div>
        </div>
    </div>
</div>

@endsection