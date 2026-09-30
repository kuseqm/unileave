@extends('layouts.master')

@section('title', 'Unresponsive Acting Staff')

@section('content')
<div class="container-fluid px-2">

    <!-- Breadcrumb -->
    <x-breadcrumb :items="[
        ['name' => 'Leave Management', 'url' => '#'],
        ['name' => 'Unresponsive Acting Staff', 'url' => '#']
    ]" />

    <!-- Page Header -->
    <x-page-header title="UNRESPONSIVE ACTING STAFF" description="Leave requests where the designated acting staff has not responded in over 48 hours" />

    <!-- Stats Cards -->
    <div class="row g-2 mb-3">
        <x-stats-card value="{{ $stats['total'] }}" label="Total Unresponsive" subtext="over 48h" color="warning" />
        <x-stats-card value="{{ $stats['over_72h'] }}" label="Over 72 Hours" subtext="needs escalation" color="danger" />
        <x-stats-card value="{{ $stats['over_week'] }}" label="Over 1 Week" subtext="critical" color="dark" />
        <x-stats-card value="{{ $stats['departments'] }}" label="Departments Affected" subtext="distinct depts" color="info" />
    </div>

    <x-alert-success />

    <x-alert-danger />

    <!-- Search Card -->
    <x-search-card placeholder="Search by staff or acting staff name..." id="unresponsiveSearchInput" name="search" :value="request('search')">
        <div id="unresponsiveSearchStats" class="text-muted" style="font-size: 0.65rem;">
            {{ $unresponsiveRequests->total() }} {{ $view === 'dismissed' ? 'dismissed' : 'unresponsive' }} requests total
        </div>
    </x-search-card>

    <div class="row g-2">
        <!-- Unresponsive requests - main column -->
        <div class="col-lg-8">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div class="d-flex align-items-center">
                            <i class="ti ti-alert-circle text-danger me-1" style="font-size: 0.8rem;"></i>
                            <span style="font-size: 0.7rem; font-weight: 600;">Unresponsive Acting Requests</span>
                        </div>
                        <div class="btn-group btn-group-sm" role="group">
                            <button type="button" id="tabActive" class="btn {{ $view === 'dismissed' ? 'btn-outline-secondary' : 'btn-secondary' }}" style="font-size: 0.65rem;">
                                Active ({{ $activeTotal }})
                            </button>
                            <button type="button" id="tabDismissed" class="btn {{ $view === 'dismissed' ? 'btn-secondary' : 'btn-outline-secondary' }}" style="font-size: 0.65rem;">
                                Dismissed ({{ $dismissedTotal }})
                            </button>
                        </div>
                    </div>
                </div>
                <div id="unresponsiveResults">
                    @include('shared.leave-requests._unresponsive-acting-results')
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
                        <div class="fw-semibold" style="font-size: 0.7rem;"><i class="ti ti-clock-exclamation me-1 text-warning"></i> Why this matters</div>
                        <small class="text-muted" style="font-size: 0.65rem;">A leave request can't move forward until the acting staff member accepts or declines holding the position. Past 48 hours, it's flagged here so it doesn't stall indefinitely.</small>
                    </div>

                    <x-alert-light class="mb-0 py-1 px-2 rounded-sm">

                        <div class="d-flex align-items-start gap-1">
                            <i class="ti ti-tips text-warning mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">Tips</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">&bull; Reassign or override acting approval directly from a request's details</small><br>
                                <small class="text-muted" style="font-size: 0.6rem;">&bull; Dismissed requests are kept for reference, not deleted</small>
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
    let currentView = '{{ $view }}';
    const tabActive = document.getElementById('tabActive');
    const tabDismissed = document.getElementById('tabDismissed');

    const handle = initLiveSearch({
        url: '{{ route('leave-requests.unresponsive-acting') }}',
        resultsSelector: '#unresponsiveResults',
        statsSelector: '#unresponsiveSearchStats',
        searchInputSelector: '#unresponsiveSearchInput',
        suggestionsSelector: '#unresponsiveSearchInputSuggestions',
        recentSearchesKey: 'unresponsiveRecentSearches',
        statLabel: 'requests',
        perPage: 10,
        extraParams: () => ({ view: currentView }),
    });

    function setTab(view) {
        currentView = view;
        tabActive.classList.toggle('btn-secondary', view === 'active');
        tabActive.classList.toggle('btn-outline-secondary', view !== 'active');
        tabDismissed.classList.toggle('btn-secondary', view === 'dismissed');
        tabDismissed.classList.toggle('btn-outline-secondary', view !== 'dismissed');
        handle?.load(1);
    }

    tabActive.addEventListener('click', () => setTab('active'));
    tabDismissed.addEventListener('click', () => setTab('dismissed'));
});
</script>
@endsection
