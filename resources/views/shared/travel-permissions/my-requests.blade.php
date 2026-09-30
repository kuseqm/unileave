@extends('layouts.master')

@section('title', 'My Travel Requests')

@section('content')
<div class="container-fluid px-2">
    
    <x-breadcrumb :items="[
        ['name' => 'Dashboard', 'url' => route('dashboard')],
        ['name' => 'My Travel Requests', 'url' => '#']
    ]" />
    
    <x-page-header title="MY TRAVEL REQUESTS" description="View all your travel permission requests">
        <a href="{{ route('travel-permissions.create') }}" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
            <i class="ti ti-plus me-1"></i> New Travel Request
        </a>
    </x-page-header>
    
    <x-search-card
        placeholder="Search by destination or reason..."
        id="myTravelRequestsSearchInput"
        name="search"
        :value="request('search')"
        :showStatusFilter="true"
        :statusOptions="['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected']"
    >
        <div id="myTravelRequestsSearchStats" class="text-muted" style="font-size: 0.65rem;">
            {{ $travelRequests->total() }} requests total
        </div>
    </x-search-card>

    <div class="row g-2">
        <!-- Travel requests - main column -->
        <div class="col-lg-8">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-plane text-primary me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Travel Permission Requests</span>
                    </div>
                </div>
                <div id="myTravelRequestsResults">
                    @include('shared.travel-permissions._my-requests-results')
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
                            <li><span class="badge bg-success-subtle text-success rounded-sm">Approved</span> - authorized, letter available</li>
                            <li><span class="badge bg-danger-subtle text-danger rounded-sm">Rejected</span> - denied at some stage</li>
                        </ul>
                    </div>

                    <x-alert-light class="mb-0 py-1 px-2 rounded-sm">

                        <div class="d-flex align-items-start gap-1">
                            <i class="ti ti-tips text-warning mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">Tips</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">Use the status filter to narrow down to one stage.</small>
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
        url: '{{ route('travel-permissions.my-requests') }}',
        resultsSelector: '#myTravelRequestsResults',
        statsSelector: '#myTravelRequestsSearchStats',
        searchInputSelector: '#myTravelRequestsSearchInput',
        suggestionsSelector: '#myTravelRequestsSearchInputSuggestions',
        recentSearchesKey: 'myTravelRequestsRecentSearches',
        statLabel: 'requests',
        perPage: 10,
        filterSelectors: ['#statusFilter'],
        extraParams: () => ({
            status: document.getElementById('statusFilter')?.value || '',
        }),
    });
});
</script>

<x-live-refresh :url="route('travel-permissions.my-requests.heartbeat')" />
@endsection