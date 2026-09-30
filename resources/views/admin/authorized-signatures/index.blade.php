@extends('layouts.master')

@section('title', 'Authorized Signatures')

@section('content')
<div class="container-fluid px-2">

    <!-- Breadcrumb -->
    <x-breadcrumb :items="[
        ['name' => 'Staff', 'url' => route('staff.index')],
        ['name' => 'Authorized Signatures', 'url' => '#']
    ]" />

    <!-- Page Header -->
    <x-page-header title="AUTHORIZED SIGNATURES" description="Manage the signatures used to authorize ID cards">
        <a href="{{ route('admin.authorized-signatures.create') }}" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
            <i class="ti ti-plus me-1"></i> Add Signature
        </a>
    </x-page-header>

    <!-- Stats Cards -->
    <div class="row g-2 mb-3">
        <x-stats-card value="{{ $totalSignatures }}" label="Total Signatures" subtext="↑ +{{ $newThisMonth }} this month" />
        <x-stats-card value="{{ $inUse }}" label="In Use" subtext="linked to ID cards" color="success" />
        <x-stats-card value="{{ $unused }}" label="Unused" subtext="not yet linked" color="secondary" />
        <x-stats-card value="{{ $newThisMonth }}" label="New This Month" subtext="added recently" color="info" />
    </div>

    <!-- Alerts -->
    <x-alert-success />

    <x-alert-danger />

    <!-- Search Card -->
    <x-search-card placeholder="Search by signature name..." id="signaturesSearchInput" name="search" :value="request('search')">
        <div id="signaturesSearchStats" class="text-muted" style="font-size: 0.65rem;">
            {{ $totalSignatures }} signatures total
        </div>
    </x-search-card>

    <div class="row g-2">
        <!-- Signatures Table - main column -->
        <div class="col-lg-8">
            <div id="signaturesResults">
                @include('admin.authorized-signatures._results')
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
                        <div class="fw-semibold" style="font-size: 0.7rem;"><i class="ti ti-signature me-1 text-primary"></i> About Signatures</div>
                        <p class="mb-0" style="font-size: 0.65rem; color: #555;">Authorized signatures are attached to printed ID cards to certify them. Each signature can be linked to one or more ID cards.</p>
                    </div>

                    <div class="mb-2">
                        <div class="fw-semibold" style="font-size: 0.7rem;"><i class="ti ti-link me-1 text-primary"></i> Quick Links</div>
                        <ul class="mb-0 ps-3" style="font-size: 0.65rem; color: #555;">
                            <li><a href="{{ route('admin.id-cards.index') }}">ID Cards</a> - manage cards using these signatures</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    initLiveSearch({
        url: '{{ route('admin.authorized-signatures.index') }}',
        resultsSelector: '#signaturesResults',
        statsSelector: '#signaturesSearchStats',
        searchInputSelector: '#signaturesSearchInput',
        suggestionsSelector: '#signaturesSearchInputSuggestions',
        recentSearchesKey: 'signaturesRecentSearches',
        statLabel: 'signatures',
        perPage: 10,
    });
});
</script>
@endsection
