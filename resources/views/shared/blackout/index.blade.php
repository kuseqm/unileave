@extends('layouts.master')

@section('title', 'Blackout Periods')

@section('content')
<div class="container-fluid px-2">
    
    <x-breadcrumb :items="[
        ['name' => 'Dashboard', 'url' => route('dashboard')],
        ['name' => 'Blackout Periods', 'url' => '#']
    ]" />
    
    <x-page-header title="BLACKOUT PERIODS" description="Periods when leave requests are restricted">
        @hasanyrole('admin|hr-officer|dean')
        <a href="{{ route('blackout.create') }}" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
            <i class="ti ti-plus me-1"></i> Add Blackout Period
        </a>
        @endhasanyrole
    </x-page-header>
    
    <!-- Stats Cards -->
    @php
        $totalBlackouts = \App\Models\BlackoutPeriod::count();
        $activeBlackouts = \App\Models\BlackoutPeriod::where('end_date', '>=', now())->count();
        $hardBlackouts = \App\Models\BlackoutPeriod::where('restriction_type', 'hard')->where('end_date', '>=', now())->count();
        $upcomingBlackouts = \App\Models\BlackoutPeriod::where('start_date', '>', now())->count();
    @endphp
    
    <div class="row g-2 mb-3">
        <x-stats-card value="{{ $totalBlackouts }}" label="Total Blackouts" subtext="all time" icon="ban" />
        <x-stats-card value="{{ $activeBlackouts }}" label="Active Blackouts" subtext="currently in effect" color="danger" icon="alert-circle" />
        <x-stats-card value="{{ $hardBlackouts }}" label="Hard Blocks" subtext="no leave allowed" color="danger" icon="x-circle" />
        <x-stats-card value="{{ $upcomingBlackouts }}" label="Upcoming" subtext="starting soon" color="info" icon="calendar" />
    </div>
    
    <!-- Search Card -->
    <x-search-card
        placeholder="Search by name..."
        id="blackoutSearchInput"
        name="search"
        :value="request('search')"
        :showStatusFilter="true"
        statusLabel="Restriction"
        :statusOptions="['hard' => 'Hard Block', 'soft' => 'Soft Warning']"
    >
        <div id="blackoutSearchStats" class="text-muted" style="font-size: 0.65rem;">
            {{ $blackoutPeriods->total() }} blackout periods total
        </div>
    </x-search-card>

    <div class="card rounded-sm">
        <div class="card-header py-1 px-2 bg-light">
            <div class="d-flex align-items-center">
                <i class="ti ti-ban text-danger me-1" style="font-size: 0.8rem;"></i>
                <span style="font-size: 0.7rem; font-weight: 600;">Blackout Periods List</span>
            </div>
        </div>
        <div id="blackoutResults">
            @include('shared.blackout._results')
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    initLiveSearch({
        url: '{{ route('blackout.index') }}',
        resultsSelector: '#blackoutResults',
        statsSelector: '#blackoutSearchStats',
        searchInputSelector: '#blackoutSearchInput',
        suggestionsSelector: '#blackoutSearchInputSuggestions',
        recentSearchesKey: 'blackoutRecentSearches',
        statLabel: 'blackout periods',
        perPage: 10,
        filterSelectors: ['#statusFilter'],
        extraParams: () => ({
            restriction_type: document.getElementById('statusFilter')?.value || '',
        }),
    });
});
</script>

<style>
.clickable-row {
    transition: background 0.2s ease;
}
.clickable-row:hover {
    background-color: #F8FAFC !important;
}
.hover-link:hover {
    text-decoration: underline;
    color: #3B82F6 !important;
}
.d-flex.gap-1 {
    gap: 0.25rem;
}
</style>
@endsection