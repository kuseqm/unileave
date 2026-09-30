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
        <x-stats-card value="{{ $stats['total'] }}" label="Total Pending" subtext="awaiting your action" color="warning" />
        <x-stats-card value="{{ $stats['this_week'] }}" label="This Week" subtext="submitted this week" color="info" />
        <x-stats-card value="{{ $stats['this_month'] }}" label="This Month" subtext="submitted this month" color="primary" />
        <x-stats-card value="{{ $stats['overdue'] }}" label="Overdue" subtext="pending over 48h" color="danger" />
    </div>

    <x-alert-success />
    
    <x-alert-danger />
    
    <!-- Search Card -->
    <x-search-card placeholder="Search by staff name or staff ID..." id="pendingApprovalsSearchInput" name="search" :value="request('search')">
        <div id="pendingApprovalsSearchStats" class="text-muted" style="font-size: 0.65rem;">
            {{ $pendingRequests->total() }} pending approvals total
        </div>
    </x-search-card>

    <div class="row g-2">
        <!-- Pending Approvals Table - main column -->
        <div class="col-lg-8">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-clock text-primary me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Requests Awaiting Approval</span>
                    </div>
                </div>
                <div id="pendingApprovalsResults">
                    @include('shared.leave-requests._pending-approvals-results')
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
                        <div class="fw-semibold" style="font-size: 0.7rem;"><i class="ti ti-route me-1 text-primary"></i> Approval Chain</div>
                        <p class="mb-0" style="font-size: 0.65rem; color: #555;">
                            Requests move HOD &rarr; Dean &rarr; DVCA/DVC-PFA &rarr; HR Logistics. You only see requests currently waiting on your level.
                        </p>
                    </div>

                    <div class="mb-2">
                        <div class="fw-semibold" style="font-size: 0.7rem;"><i class="ti ti-link me-1 text-primary"></i> Quick Links</div>
                        <ul class="mb-0 ps-3" style="font-size: 0.65rem; color: #555;">
                            <li><a href="{{ route('leave-requests.approvals-history') }}">Approvals History</a> - decisions you've already made</li>
                        </ul>
                    </div>

                    <x-alert-light class="mb-0 py-1 px-2 rounded-sm">
                        <div class="d-flex align-items-start gap-1">
                            <i class="ti ti-tips text-warning mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">Tips</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">&bull; Rejecting a request requires a reason - it is shown to the requester</small><br>
                                <small class="text-muted" style="font-size: 0.6rem;">&bull; This list refreshes automatically as new requests reach your level</small>
                            </div>
                        </div>
                    </x-alert-light>
                </div>
            </div>
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
    form.action = `/leave-requests/${id}/approve`;
    new bootstrap.Modal(document.getElementById('approveModal')).show();
}

function openRejectModal(id) {
    const form = document.getElementById('rejectForm');
    form.action = `/leave-requests/${id}/reject`;
    new bootstrap.Modal(document.getElementById('rejectModal')).show();
}

document.addEventListener('DOMContentLoaded', function () {
    initLiveSearch({
        url: '{{ route('leave-requests.pending') }}',
        resultsSelector: '#pendingApprovalsResults',
        statsSelector: '#pendingApprovalsSearchStats',
        searchInputSelector: '#pendingApprovalsSearchInput',
        suggestionsSelector: '#pendingApprovalsSearchInputSuggestions',
        recentSearchesKey: 'pendingApprovalsRecentSearches',
        statLabel: 'pending approvals',
        perPage: 10,
    });
});
</script>

<x-live-refresh :url="route('leave-requests.pending.heartbeat')" />
@endsection