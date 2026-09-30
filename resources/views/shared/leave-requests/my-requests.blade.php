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

    <x-alert-success />
    <x-alert-danger />

    <!-- Stats Cards -->
    <div class="row g-2 mb-3">
        <x-stats-card value="{{ $stats['total'] ?? 0 }}" label="Total Requests" subtext="all time" />
        <x-stats-card value="{{ $stats['pending'] ?? 0 }}" label="Pending" subtext="awaiting approval" color="warning" />
        <x-stats-card value="{{ $stats['approved'] ?? 0 }}" label="Approved" subtext="granted" color="success" />
        <x-stats-card value="{{ $stats['rejected'] ?? 0 }}" label="Rejected" subtext="denied" color="danger" />
    </div><!-- Search Card -->
    <x-search-card
        placeholder="Search by leave type..."
        id="myLeaveRequestsSearchInput"
        name="search"
        :value="request('search')"
        :showStatusFilter="true"
        :statusOptions="['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected', 'cancelled' => 'Cancelled']"
    >
        <div id="myLeaveRequestsSearchStats" class="text-muted" style="font-size: 0.65rem;">
            {{ $leaveRequests->total() }} requests total
        </div>
    </x-search-card>

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
                <div id="myLeaveRequestsResults">
                    @include('shared.leave-requests._my-requests-results')
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
                        <div class="fw-semibold" style="font-size: 0.7rem;"><i class="ti ti-link me-1 text-primary"></i> Quick Links</div>
                        <ul class="mb-0 ps-3" style="font-size: 0.65rem; color: #555;">
                            <li><a href="{{ route('leave-requests.i-held-position-for') }}">My Assignments</a> - requests where you hold someone's position</li>
                            <li><a href="{{ route('leave-requests.create') }}">Apply for Leave</a> - submit a new request</li>
                        </ul>
                    </div>

                    <x-alert-light class="mb-0 py-1 px-2 rounded-sm">

                        <div class="d-flex align-items-start gap-1">
                            <i class="ti ti-tips text-warning mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">Tips</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">&bull; Click the eye icon to see full approval progress</small><br>
                                <small class="text-muted" style="font-size: 0.6rem;">&bull; Cancel is only available while still Pending</small>
                            </div>

</x-alert-light>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    initLiveSearch({
        url: '{{ route('leave-requests.my-requests') }}',
        resultsSelector: '#myLeaveRequestsResults',
        statsSelector: '#myLeaveRequestsSearchStats',
        searchInputSelector: '#myLeaveRequestsSearchInput',
        suggestionsSelector: '#myLeaveRequestsSearchInputSuggestions',
        recentSearchesKey: 'myLeaveRequestsRecentSearches',
        statLabel: 'requests',
        perPage: 10,
        filterSelectors: ['#statusFilter'],
        extraParams: () => ({
            status: document.getElementById('statusFilter')?.value || '',
        }),
    });
});
</script>

<script>
function showCancelModal(id, name) {
    document.getElementById('cancelForm').action = '/leave-requests/' + id + '/cancel';
    new bootstrap.Modal(document.getElementById('cancelModal')).show();
}

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
                <form id="cancelForm" action="#" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-warning btn-sm rounded-sm" style="font-size: 0.7rem;">Cancel Request</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection