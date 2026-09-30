@extends('layouts.master')

@section('title', 'My Request Status')

@section('content')
<div class="container-fluid px-2">
    
    <!-- Breadcrumb -->
    <x-breadcrumb :items="[
        ['name' => 'Dashboard', 'url' => route('dashboard')],
        ['name' => 'My Request Status', 'url' => '#']
    ]" />
    
    <!-- Page Header -->
    <x-page-header title="MY REQUEST STATUS" description="Track the progress of your leave requests">
        <a href="{{ route('leave-requests.create') }}" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
            <i class="ti ti-plus me-1"></i> New Request
        </a>
    </x-page-header>
    
    <!-- Stats Cards -->
    <div class="row g-2 mb-3">
        <x-stats-card value="{{ $stats['total'] }}" label="Total Requests" subtext="all time" icon="clipboard-list" />
        <x-stats-card value="{{ $stats['pending'] }}" label="Pending" subtext="awaiting approval" color="warning" icon="clock" />
        <x-stats-card value="{{ $stats['approved'] }}" label="Approved" subtext="granted" color="success" icon="check" />
        <x-stats-card value="{{ $stats['rejected'] }}" label="Rejected" subtext="denied" color="danger" icon="x" />
    </div>

    <x-alert-success />
    
    <x-alert-danger />
    
    <!-- Search Card -->
    <x-search-card
        placeholder="Search by leave type..."
        id="statusSearchInput"
        name="search"
        :value="request('search')"
        :showStatusFilter="true"
        :statusOptions="['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected', 'cancelled' => 'Cancelled']"
    >
        <div id="statusSearchStats" class="text-muted" style="font-size: 0.65rem;">
            {{ $leaveRequests->total() }} requests total
        </div>
    </x-search-card>

    <div class="row g-2">
        <!-- Requests List - main column -->
        <div class="col-lg-8">
            <div class="card rounded-sm">
                <div class="card-header py-2 px-3 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-list text-primary me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.75rem; font-weight: 600;">My Leave Requests</span>
                    </div>
                </div>
                <div id="statusResults">
                    @include('shared.leave-requests._status-results')
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

                    <x-alert-light class="mb-0 py-1 px-2 rounded-sm">

                        <div class="d-flex align-items-start gap-1">
                            <i class="ti ti-tips text-warning mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">Tips</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">&bull; Use the status filter to narrow down to one stage</small><br>
                                <small class="text-muted" style="font-size: 0.6rem;">&bull; A pending request can still be cancelled from here</small>
                            </div>

</x-alert-light>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.request-item {
    transition: background 0.2s ease;
}
.request-item:hover {
    background: #F8FAFC;
}
.custom-select-sm {
    font-size: 0.65rem;
    padding: 0.2rem 1.2rem 0.2rem 0.4rem;
    border: 1px solid #E8E2D5;
    border-radius: 4px;
    background: white;
    cursor: pointer;
}
.custom-select-sm:focus {
    outline: none;
    border-color: #3B82F6;
}
</style>

<script>
// Cancel Request Function
function cancelRequest(requestId) {
    confirmAction('Are you sure you want to cancel this leave request? This action cannot be undone.', function () {
        fetch(`/leave-requests/${requestId}/cancel`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Failed to cancel request');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred');
        });
    });
}

document.addEventListener('DOMContentLoaded', function () {
    initLiveSearch({
        url: '{{ route('leave-requests.status') }}',
        resultsSelector: '#statusResults',
        statsSelector: '#statusSearchStats',
        searchInputSelector: '#statusSearchInput',
        suggestionsSelector: '#statusSearchInputSuggestions',
        recentSearchesKey: 'statusRecentSearches',
        statLabel: 'requests',
        perPage: 10,
        filterSelectors: ['#statusFilter'],
        extraParams: () => ({
            status: document.getElementById('statusFilter')?.value || '',
        }),
    });
});
</script>

<x-live-refresh :url="route('leave-requests.status.heartbeat')" />
@endsection