@extends('layouts.master')

@section('title', 'ID Cards')

@section('content')
<div class="container-fluid px-2">

    <!-- Breadcrumb -->
    <x-breadcrumb :items="[
        ['name' => 'Staff', 'url' => route('staff.index')],
        ['name' => 'ID Cards', 'url' => '#']
    ]" />

    <!-- Page Header -->
    <x-page-header title="ID CARDS" description="Generate and manage staff identity cards">
        <a href="{{ route('admin.id-cards.create') }}" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
            <i class="ti ti-plus me-1"></i> Generate ID Card
        </a>
    </x-page-header>

    <!-- Stats Cards -->
    <div class="row g-2 mb-3">
        <x-stats-card value="{{ $totalCards }}" label="Total ID Cards" subtext="↑ +{{ $newThisMonth }} this month" />
        <x-stats-card value="{{ $validCards }}" label="Valid Cards" subtext="currently valid" color="success" />
        <x-stats-card value="{{ $expiredCards }}" label="Expired Cards" subtext="need renewal" color="warning" />
        <x-stats-card value="{{ $totalStaff }}" label="Total Staff" subtext="eligible for cards" color="info" />
    </div>

    <!-- Alerts -->
    <x-alert-success />

    <x-alert-danger />

    <!-- Search Card -->
    <div id="searchCard">
        <x-search-card placeholder="Search by staff name or staff ID..." id="idCardsSearchInput" name="search" :value="request('search')">
            <div id="idCardsSearchStats" class="text-muted" style="font-size: 0.65rem;">
                {{ $totalCards }} ID cards total
            </div>
        </x-search-card>
    </div>

    <div class="row g-2">
        <!-- ID Cards Table - main column -->
        <div class="col-lg-8">
            <div id="idCardsResults">
                @include('admin.id-cards._results')
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
                        <div class="fw-semibold" style="font-size: 0.7rem;"><i class="ti ti-id me-1 text-primary"></i> About ID Cards</div>
                        <p class="mb-0" style="font-size: 0.65rem; color: #555;">Each generated ID card is tied to a staff member and an authorized signature, and carries a validity/expiry date used for gate verification.</p>
                    </div>

                    <div class="mb-2">
                        <div class="fw-semibold" style="font-size: 0.7rem;"><i class="ti ti-link me-1 text-primary"></i> Quick Links</div>
                        <ul class="mb-0 ps-3" style="font-size: 0.65rem; color: #555;">
                            <li><a href="{{ route('admin.authorized-signatures.index') }}">Authorized Signatures</a> - manage signatures used on cards</li>
                            <li><a href="{{ route('staff.index') }}">Staff</a> - view staff eligible for cards</li>
                            <li><a href="{{ route('admin.gate-entry-logs.index') }}">Gate Entry Logs</a> - see cards in use at the gate</li>
                        </ul>
                    </div>

                    <x-alert-light class="mb-0 py-1 px-2 rounded-sm">
                        <div class="d-flex align-items-start gap-1">
                            <i class="ti ti-tips text-warning mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">Tips</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">&bull; Expired cards need to be regenerated, not edited</small><br>
                                <small class="text-muted" style="font-size: 0.6rem;">&bull; Each staff member should hold only one valid card at a time</small>
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
        url: '{{ route('admin.id-cards.index') }}',
        resultsSelector: '#idCardsResults',
        statsSelector: '#idCardsSearchStats',
        searchInputSelector: '#idCardsSearchInput',
        suggestionsSelector: '#idCardsSearchInputSuggestions',
        recentSearchesKey: 'idCardsRecentSearches',
        statLabel: 'ID cards',
        perPage: 10,
    });
});
</script>
@endsection
