@extends('layouts.master')

@section('title', 'Pending Travel Approvals')

@section('content')
<div class="container-fluid px-2">
    
    <x-breadcrumb :items="[
        ['name' => 'Dashboard', 'url' => route('dashboard')],
        ['name' => 'Pending Travel Approvals', 'url' => '#']
    ]" />
    
    <x-page-header title="PENDING TRAVEL APPROVALS" description="Travel requests awaiting your approval">
    </x-page-header>
    
    <x-search-card placeholder="Search by staff name or staff ID..." id="pendingTravelSearchInput" name="search" :value="request('search')">
        <div id="pendingTravelSearchStats" class="text-muted" style="font-size: 0.65rem;">
            {{ $pendingRequests->total() }} pending approvals total
        </div>
    </x-search-card>

    <div class="row g-2">
        <!-- Pending requests - main column -->
        <div class="col-lg-8">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-clock text-primary me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Travel Requests Pending Your Approval</span>
                    </div>
                </div>
                <div id="pendingTravelResults">
                    @include('shared.travel-permissions._pending-approvals-results')
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
                    <x-alert-light class="mb-0 py-1 px-2 rounded-sm">

                        <div class="d-flex align-items-start gap-1">
                            <i class="ti ti-tips text-warning mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">Tips</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">&bull; Review the applicant's own letter (if written) from the request's details page</small><br>
                                <small class="text-muted" style="font-size: 0.6rem;">&bull; Rejecting requires a reason, shared with the applicant</small>
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
        url: '{{ route('travel-permissions.pending') }}',
        resultsSelector: '#pendingTravelResults',
        statsSelector: '#pendingTravelSearchStats',
        searchInputSelector: '#pendingTravelSearchInput',
        suggestionsSelector: '#pendingTravelSearchInputSuggestions',
        recentSearchesKey: 'pendingTravelRecentSearches',
        statLabel: 'pending approvals',
        perPage: 10,
    });
});
</script>

<x-live-refresh :url="route('travel-permissions.pending.heartbeat')" />
@endsection