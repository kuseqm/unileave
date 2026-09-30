@extends('layouts.master')

@section('title', 'Outgoing Letters')

@section('content')
<div class="container-fluid px-2">
    
    <!-- Breadcrumb -->
    <x-breadcrumb :items="[
        ['name' => 'Central Registry', 'url' => '#'],
        ['name' => 'Outgoing Letters', 'url' => '#']
    ]" />
    
    <!-- Page Header -->
    <x-page-header title="OUTGOING LETTERS" description="Manage all outgoing correspondence">
        <a href="{{ route('registry.outgoing.create') }}" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
            <i class="ti ti-plus me-1"></i> New Outgoing Letter
        </a>
    </x-page-header>
    
    <!-- Search Card -->
    <x-search-card
        placeholder="Search by ref number, recipient, subject..."
        id="outgoingSearchInput"
        name="search"
        :value="request('search')"
        :showStatusFilter="true"
        :statusOptions="['draft' => 'Draft', 'pending_approval' => 'Pending Approval', 'approved' => 'Approved', 'dispatched' => 'Dispatched']"
    >
        <div id="outgoingSearchStats" class="text-muted" style="font-size: 0.65rem;">
            {{ $letters->total() }} letters total
        </div>
    </x-search-card>

    <div class="row g-2">
        <!-- Letters Table Card - main column -->
        <div class="col-lg-8">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-mail-forward text-primary me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Outgoing Letters List</span>
                    </div>
                </div>
                <div id="outgoingResults">
                    @include('registry.outgoing._results')
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
                            <li><span class="badge bg-secondary-subtle text-secondary rounded-sm">Draft</span> - still being written</li>
                            <li><span class="badge bg-warning-subtle text-warning rounded-sm">Pending Approval</span> - awaiting sign-off</li>
                            <li><span class="badge bg-info-subtle text-info rounded-sm">Approved</span> - cleared to send</li>
                            <li><span class="badge bg-success-subtle text-success rounded-sm">Dispatched</span> - sent or printed</li>
                        </ul>
                    </div>

                    <div class="mb-2">
                        <div class="fw-semibold" style="font-size: 0.7rem;"><i class="ti ti-link me-1 text-primary"></i> Quick Links</div>
                        <ul class="mb-0 ps-3" style="font-size: 0.65rem; color: #555;">
                            <li><a href="{{ route('registry.outgoing.create') }}">New Outgoing Letter</a> - draft a new letter</li>
                            <li><a href="{{ route('registry.print-queue.index') }}">Print Queue</a> - track printing and dispatch</li>
                        </ul>
                    </div>

                    <x-alert-light class="mb-0 py-1 px-2 rounded-sm">
                        <div class="d-flex align-items-start gap-1">
                            <i class="ti ti-tips text-warning mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">Tips</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">&bull; Letters marked "Requires physical printing" appear in the Print Queue once approved</small><br>
                                <small class="text-muted" style="font-size: 0.6rem;">&bull; Replying to an incoming letter auto-fills the subject line</small>
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
        url: '{{ route('registry.outgoing.index') }}',
        resultsSelector: '#outgoingResults',
        statsSelector: '#outgoingSearchStats',
        searchInputSelector: '#outgoingSearchInput',
        suggestionsSelector: '#outgoingSearchInputSuggestions',
        recentSearchesKey: 'outgoingRecentSearches',
        statLabel: 'letters',
        perPage: 15,
        filterSelectors: ['#statusFilter'],
        extraParams: () => ({
            status: document.getElementById('statusFilter')?.value || '',
        }),
    });
});
</script>
@endsection