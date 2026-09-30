@extends('layouts.master')

@section('title', 'Incoming Letters')

@section('content')
<div class="container-fluid px-2">
    
    <!-- Breadcrumb -->
    <x-breadcrumb :items="[
        ['name' => 'Central Registry', 'url' => '#'],
        ['name' => 'Incoming Letters', 'url' => '#']
    ]" />
    
    <!-- Page Header -->
    <x-page-header title="INCOMING LETTERS" description="Manage all incoming correspondence">
        <div class="d-flex gap-1">
            <a href="{{ route('registry.incoming.create') }}" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
                <i class="ti ti-plus me-1"></i> New Incoming Letter
            </a>
        </div>
    </x-page-header>
    
    <!-- Search Card -->
    <x-search-card
        placeholder="Search by ref, sender, subject..."
        id="incomingSearchInput"
        name="search"
        :value="request('search')"
        :showStatusFilter="true"
        :showCategoryFilter="true"
        :showTypeFilter="true"
        categoryLabel="Office"
        typeLabel="Priority"
        :statusOptions="['received' => 'Received', 'assigned' => 'Assigned', 'in_progress' => 'In Progress', 'replied' => 'Replied', 'closed' => 'Closed']"
        :categoryOptions="$offices->pluck('name', 'id')"
        :typeOptions="['normal' => 'Normal', 'urgent' => 'Urgent', 'confidential' => 'Confidential']"
    >
        <div id="incomingSearchStats" class="text-muted" style="font-size: 0.65rem;">
            {{ $letters->total() }} letters total
        </div>
    </x-search-card>

    <div class="row g-2">
        <!-- Letters Table Card - main column -->
        <div class="col-lg-8">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-list text-primary me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Incoming Letters List</span>
                    </div>
                </div>
                <div id="incomingResults">
                    @include('registry.incoming._results')
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
                            <li><span class="badge bg-secondary-subtle text-secondary rounded-sm">Received</span> - logged, not yet assigned</li>
                            <li><span class="badge bg-info-subtle text-info rounded-sm">Assigned</span> - routed to an office</li>
                            <li><span class="badge bg-warning-subtle text-warning rounded-sm">In Progress</span> - being actioned</li>
                            <li><span class="badge bg-success-subtle text-success rounded-sm">Replied</span> - a response was sent</li>
                            <li><span class="badge bg-dark-subtle text-dark rounded-sm">Closed</span> - fully resolved</li>
                        </ul>
                    </div>

                    <div class="mb-2">
                        <div class="fw-semibold" style="font-size: 0.7rem;"><i class="ti ti-link me-1 text-primary"></i> Quick Links</div>
                        <ul class="mb-0 ps-3" style="font-size: 0.65rem; color: #555;">
                            <li><a href="{{ route('registry.incoming.create') }}">New Incoming Letter</a> - register a letter received</li>
                            <li><a href="{{ route('registry.offices.index') }}">Offices</a> - manage routing destinations</li>
                        </ul>
                    </div>

                    <x-alert-light class="mb-0 py-1 px-2 rounded-sm">
                        <div class="d-flex align-items-start gap-1">
                            <i class="ti ti-tips text-warning mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">Tips</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">&bull; Confidential letters are only visible to the addressed office</small><br>
                                <small class="text-muted" style="font-size: 0.6rem;">&bull; Use the priority filter to spot urgent items quickly</small>
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
        url: '{{ route('registry.incoming.index') }}',
        resultsSelector: '#incomingResults',
        statsSelector: '#incomingSearchStats',
        searchInputSelector: '#incomingSearchInput',
        suggestionsSelector: '#incomingSearchInputSuggestions',
        recentSearchesKey: 'incomingRecentSearches',
        statLabel: 'letters',
        perPage: 15,
        filterSelectors: ['#statusFilter', '#categoryFilter', '#typeFilter'],
        extraParams: () => ({
            status: document.getElementById('statusFilter')?.value || '',
            office_id: document.getElementById('categoryFilter')?.value || '',
            priority: document.getElementById('typeFilter')?.value || '',
        }),
    });
});
</script>
@endsection