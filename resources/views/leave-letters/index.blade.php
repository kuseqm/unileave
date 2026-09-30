@extends('layouts.master')

@section('title', 'My Leave Letters')

@section('content')
<div class="container-fluid px-2">
    
    <x-breadcrumb :items="[
        ['name' => 'Dashboard', 'url' => route('dashboard')],
        ['name' => 'My Leave Letters', 'url' => '#']
    ]" />
    
    <x-page-header title="MY LEAVE LETTERS" description="Leave approval letters from your requests">
        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
            <i class="ti ti-arrow-left me-1"></i> Back
        </a>
    </x-page-header>
    
    <!-- Stats Cards -->
    <div class="row g-2 mb-3">
        <x-stats-card value="{{ $stats['total'] }}" label="Total Letters" subtext="all time" color="primary" icon="file-text" />
        <x-stats-card value="{{ $stats['draft'] }}" label="Draft" subtext="not finalized" color="secondary" icon="file" />
        <x-stats-card value="{{ $stats['generated'] }}" label="Generated" subtext="ready" color="success" icon="check" />
        <x-stats-card value="{{ $stats['printed'] }}" label="Printed" subtext="already printed" color="info" icon="printer" />
    </div>
    
    <!-- Search Card -->
    <x-search-card
        placeholder="Search by letter number or title..."
        id="leaveLettersSearchInput"
        name="search"
        :value="request('search')"
        :showStatusFilter="true"
        :statusOptions="['draft' => 'Draft', 'generated' => 'Generated']"
    >
        <div id="leaveLettersSearchStats" class="text-muted" style="font-size: 0.65rem;">
            {{ $letters->total() }} letters total
        </div>
    </x-search-card>

    <div class="row g-2">
        <!-- Letters Table - main column -->
        <div class="col-lg-8">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-file-text text-primary me-1" style="font-size: 0.7rem;"></i>
                        <span style="font-weight: 600; font-size: 0.7rem;">Leave Approval Letters</span>
                    </div>
                </div>
                <div id="leaveLettersResults">
                    @include('leave-letters._results')
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
                            <li><span class="badge bg-secondary-subtle text-secondary rounded-sm">Draft</span> - not yet finalized</li>
                            <li><span class="badge bg-success-subtle text-success rounded-sm">Generated</span> - ready to view or print</li>
                        </ul>
                    </div>

                    <x-alert-light class="mb-0 py-1 px-2 rounded-sm">
                        <div class="d-flex align-items-start gap-1">
                            <i class="ti ti-tips text-warning mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">Tips</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">&bull; Letters are generated once your leave request is fully approved</small>
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
        url: '{{ route('leave-letters.index') }}',
        resultsSelector: '#leaveLettersResults',
        statsSelector: '#leaveLettersSearchStats',
        searchInputSelector: '#leaveLettersSearchInput',
        suggestionsSelector: '#leaveLettersSearchInputSuggestions',
        recentSearchesKey: 'leaveLettersRecentSearches',
        statLabel: 'letters',
        perPage: 15,
        filterSelectors: ['#statusFilter'],
        extraParams: () => ({
            stage: document.getElementById('statusFilter')?.value || '',
        }),
    });
});
</script>
@endsection