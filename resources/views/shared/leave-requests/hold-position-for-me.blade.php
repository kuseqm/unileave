@extends('layouts.master')

@section('title', 'Who Is Holding My Position')

@section('content')
<div class="container-fluid px-2">

    <!-- Breadcrumb -->
    <x-breadcrumb :items="[
        ['name' => 'Who Is Holding My Position', 'url' => '#']
    ]" />

    <!-- Page Header -->
    <x-page-header title="WHO IS HOLDING MY POSITION" description="Track who you've asked to act in your place while on leave">
        <a href="{{ route('leave-requests.create') }}" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
            <i class="ti ti-plus me-1"></i> Apply for Leave
        </a>
    </x-page-header>

    <!-- Stats Cards -->
    @php
        $stats = [
            'total' => $holdHistories->total() ?? 0,
            'pending' => $holdHistories->where('status', 'pending')->count(),
            'accepted' => $holdHistories->where('status', 'accepted')->count(),
            'declined' => $holdHistories->where('status', 'declined')->count(),
        ];
    @endphp

    <div class="row g-2 mb-3">
        <x-stats-card value="{{ $stats['total'] }}" label="Total Requests" subtext="all time" />
        <x-stats-card value="{{ $stats['pending'] }}" label="Pending" subtext="awaiting their response" color="warning" />
        <x-stats-card value="{{ $stats['accepted'] }}" label="Accepted" subtext="they accepted" color="success" />
        <x-stats-card value="{{ $stats['declined'] }}" label="Declined" subtext="they declined" color="danger" />
    </div>
    
    <x-alert-success />
    
    <x-alert-danger />
    
    <!-- Search Card -->
    <x-search-card
        placeholder="Search by holder name..."
        id="holdPositionSearchInput"
        name="search"
        :value="request('search')"
        :showStatusFilter="true"
        :statusOptions="['pending' => 'Pending', 'accepted' => 'Accepted', 'declined' => 'Declined']"
    >
        <div id="holdPositionSearchStats" class="text-muted" style="font-size: 0.65rem;">
            {{ $holdHistories->total() }} acting requests total
        </div>
    </x-search-card>

    <div class="row g-2">
        <!-- Delegations Table - main column -->
        <div class="col-lg-8">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-user-check text-primary me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">People Holding Your Position</span>
                    </div>
                </div>
                <div id="holdPositionResults">
                    @include('shared.leave-requests._hold-position-for-me-results')
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
                            <li><span class="badge bg-warning-subtle text-warning rounded-sm">Pending</span> - awaiting their response</li>
                            <li><span class="badge bg-success-subtle text-success rounded-sm">Accepted</span> - they agreed to act for you</li>
                            <li><span class="badge bg-danger-subtle text-danger rounded-sm">Declined</span> - they turned down the request</li>
                        </ul>
                    </div>

                    <div class="mb-2">
                        <div class="fw-semibold" style="font-size: 0.7rem;"><i class="ti ti-link me-1 text-primary"></i> Quick Links</div>
                        <ul class="mb-0 ps-3" style="font-size: 0.65rem; color: #555;">
                            <li><a href="{{ route('leave-requests.i-held-position-for') }}">My Assignments</a> - requests where you hold someone else's position</li>
                            <li><a href="{{ route('leave-requests.my-requests') }}">My Leave Requests</a> - your own leave requests</li>
                        </ul>
                    </div>

                    <x-alert-light class="mb-0 py-1 px-2 rounded-sm">
                        <div class="d-flex align-items-start gap-1">
                            <i class="ti ti-tips text-warning mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">Tip</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">&bull; A declined nomination means your leave request may need a new acting staff before it can proceed</small>
                            </div>
                        </div>
                    </x-alert-light>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    initLiveSearch({
        url: '{{ route('leave-requests.hold-position-for-me') }}',
        resultsSelector: '#holdPositionResults',
        statsSelector: '#holdPositionSearchStats',
        searchInputSelector: '#holdPositionSearchInput',
        suggestionsSelector: '#holdPositionSearchInputSuggestions',
        recentSearchesKey: 'holdPositionRecentSearches',
        statLabel: 'acting requests',
        perPage: 10,
        filterSelectors: ['#statusFilter'],
        extraParams: () => ({
            status: document.getElementById('statusFilter')?.value || '',
        }),
    });
});
</script>

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
</style>
@endsection