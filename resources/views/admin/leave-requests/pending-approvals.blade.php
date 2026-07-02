@extends('layouts.master')

@section('title', 'Pending Approvals')

@section('content')
<div class="container-fluid px-2">
    
    <!-- Breadcrumb -->
    <x-breadcrumb :items="[
        ['name' => 'Pending Approvals', 'url' => '#']
    ]" />
    
    <!-- Page Header -->
    <x-page-header title="PENDING APPROVALS" description="Review and process leave requests">
        <!-- No action buttons needed -->
    </x-page-header>
    
    <!-- Stats Cards -->
    <div class="row g-2 mb-3">
        <x-stats-card value="{{ $pendingRequests->total() }}" label="Total Pending" subtext="awaiting your action" color="warning" />
        <x-stats-card value="{{ $pendingRequests->where('current_approval_level', 'hod')->count() }}" label="HOD Level" subtext="department review" color="info" />
        <x-stats-card value="{{ $pendingRequests->where('current_approval_level', 'dean')->count() }}" label="Dean Level" subtext="school review" color="primary" />
        <x-stats-card value="{{ $pendingRequests->where('current_approval_level', 'dvca')->count() + $pendingRequests->where('current_approval_level', 'dvc_pfa')->count() }}" label="DVC Level" subtext="university review" color="success" />
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
    
    <!-- Pending Approvals Table -->
    <div class="card rounded-sm">
        <div class="card-header py-1 px-2 bg-light">
            <div class="d-flex align-items-center">
                <i class="ti ti-clock text-primary me-1" style="font-size: 0.8rem;"></i>
                <span style="font-size: 0.7rem; font-weight: 600;">Requests Awaiting Approval</span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm mb-0" style="font-size: 0.75rem;">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-2 py-1">#</th>
                            <th class="py-1">Staff</th>
                            <th class="py-1">Leave Type</th>
                            <th class="py-1">Dates</th>
                            <th class="py-1">Days</th>
                            <th class="py-1">Reason</th>
                            <th class="py-1">Current Level</th>
                            <th class="pe-2 py-1">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pendingRequests as $index => $request)
                        <tr>
                            <td class="ps-2 py-1">{{ $pendingRequests->firstItem() + $index }}</td>
                            <td class="py-1">
                                <div class="d-flex align-items-center gap-1">
                                    <div class="rounded-circle bg-light d-flex align-items-center justify-content-center" style="width: 24px; height: 24px;">
                                        <span class="fw-semibold" style="font-size: 0.6rem;">{{ strtoupper(substr($request->staff->first_name, 0, 1)) }}{{ strtoupper(substr($request->staff->last_name, 0, 1)) }}</span>
                                    </div>
                                    <div>
                                        <span class="fw-semibold">{{ $request->staff->first_name }} {{ $request->staff->last_name }}</span>
                                        <div class="text-muted" style="font-size: 0.6rem;">{{ $request->staff->staff_id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-1">{{ $request->leaveType->name ?? 'N/A' }}<br><small class="text-muted">{{ $request->leaveType->code ?? '' }}</small></td>
                            <td class="py-1">{{ \Carbon\Carbon::parse($request->start_date)->format('d M Y') }}<br><small>to</small><br>{{ \Carbon\Carbon::parse($request->end_date)->format('d M Y') }}</td>
                            <td class="py-1">{{ $request->days }} day(s)</td>
                            <td class="py-1">{{ \Str::limit($request->reason, 50) }}</td>
                            <td class="py-1">
                                @if($request->current_approval_level == 'hod')
                                    <span class="badge bg-info-subtle text-info rounded-sm" style="font-size: 0.6rem;">HOD</span>
                                @elseif($request->current_approval_level == 'dean')
                                    <span class="badge bg-primary-subtle text-primary rounded-sm" style="font-size: 0.6rem;">Dean</span>
                                @elseif($request->current_approval_level == 'dvca')
                                    <span class="badge bg-success-subtle text-success rounded-sm" style="font-size: 0.6rem;">DVCA</span>
                                @elseif($request->current_approval_level == 'dvc_pfa')
                                    <span class="badge bg-success-subtle text-success rounded-sm" style="font-size: 0.6rem;">DVC-PFA</span>
                                @elseif($request->current_approval_level == 'hr_logistics')
                                    <span class="badge bg-secondary-subtle text-secondary rounded-sm" style="font-size: 0.6rem;">HR Logistics</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary rounded-sm" style="font-size: 0.6rem;">{{ ucfirst($request->current_approval_level) }}</span>
                                @endif
                            </td>
                            <td class="pe-2 py-1">
                                <button type="button" class="btn btn-sm btn-success rounded-sm" onclick="openApproveModal({{ $request->id }})" title="Approve" style="padding: 0.15rem 0.3rem;">
                                    <i class="ti ti-check" style="font-size: 0.7rem;"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-danger rounded-sm" onclick="openRejectModal({{ $request->id }})" title="Reject" style="padding: 0.15rem 0.3rem;">
                                    <i class="ti ti-x" style="font-size: 0.7rem;"></i>
                                </button>
                                <a href="{{ route('admin.leave-requests.show', $request->id) }}" class="btn btn-sm btn-outline-info rounded-sm" title="View Details" style="padding: 0.15rem 0.3rem;">
                                    <i class="ti ti-eye" style="font-size: 0.7rem;"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-3 text-muted" style="font-size: 0.7rem;">
                                No pending approvals
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer py-1 px-2">
            <x-pagination :items="$pendingRequests" :perPageOptions="[10, 25, 50, 100]" />
        </div>
    </div>
</div>

<!-- Approve Modal -->
<div class="modal fade" id="approveModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-sm">
            <div class="modal-header py-1 px-2">
                <h5 class="modal-title" style="font-size: 0.8rem;">Approve Leave Request</h5>
                <button type="button" class="btn-close py-0" data-bs-dismiss="modal"></button>
            </div>
            <form id="approveForm" method="POST">
                @csrf
                <div class="modal-body py-2 px-2">
                    <p style="font-size: 0.75rem;">Are you sure you want to approve this leave request?</p>
                    <div class="mb-1">
                        <label class="form-label" style="font-size: 0.65rem;">Comment (Optional)</label>
                        <textarea name="comment" class="form-control form-control-sm rounded-sm" rows="2" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;"></textarea>
                    </div>
                </div>
                <div class="modal-footer py-1 px-2">
                    <button type="button" class="btn btn-secondary btn-sm rounded-sm" data-bs-dismiss="modal" style="font-size: 0.7rem;">Cancel</button>
                    <button type="submit" class="btn btn-success btn-sm rounded-sm" style="font-size: 0.7rem;">Approve</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-sm">
            <div class="modal-header py-1 px-2">
                <h5 class="modal-title" style="font-size: 0.8rem;">Reject Leave Request</h5>
                <button type="button" class="btn-close py-0" data-bs-dismiss="modal"></button>
            </div>
            <form id="rejectForm" method="POST">
                @csrf
                <div class="modal-body py-2 px-2">
                    <p style="font-size: 0.75rem;">Are you sure you want to reject this leave request?</p>
                    <div class="mb-1">
                        <label class="form-label" style="font-size: 0.65rem;">Reason for Rejection <span class="text-danger">*</span></label>
                        <textarea name="rejection_reason" class="form-control form-control-sm rounded-sm" rows="3" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;" required></textarea>
                    </div>
                </div>
                <div class="modal-footer py-1 px-2">
                    <button type="button" class="btn btn-secondary btn-sm rounded-sm" data-bs-dismiss="modal" style="font-size: 0.7rem;">Cancel</button>
                    <button type="submit" class="btn btn-danger btn-sm rounded-sm" style="font-size: 0.7rem;">Reject</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openApproveModal(id) {
    const form = document.getElementById('approveForm');
    form.action = `/admin/leave-requests/${id}/approve`;
    new bootstrap.Modal(document.getElementById('approveModal')).show();
}

function openRejectModal(id) {
    const form = document.getElementById('rejectForm');
    form.action = `/admin/leave-requests/${id}/reject`;
    new bootstrap.Modal(document.getElementById('rejectModal')).show();
}
</script>
@endsection