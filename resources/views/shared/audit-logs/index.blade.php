@extends('layouts.master')

@section('title', 'Audit Logs')

@section('content')
<div class="container-fluid px-2">
    
    <x-breadcrumb :items="[
        ['name' => 'Administration', 'url' => '#'],
        ['name' => 'Audit Logs', 'url' => '#']
    ]" />
    
    <x-page-header title="AUDIT LOGS" description="System activity log">
    </x-page-header>
    
    <!-- Stats -->
    <div class="row g-2 mb-3">
        <x-stats-card value="{{ $logs->total() }}" label="Total Logs" subtext="system-wide" />
        <x-stats-card value="{{ $logs->where('action', 'create')->count() }}" label="Created" subtext="new records" color="success" />
        <x-stats-card value="{{ $logs->where('action', 'update')->count() }}" label="Updated" subtext="modified records" color="warning" />
        <x-stats-card value="{{ $logs->where('action', 'delete')->count() }}" label="Deleted" subtext="removed records" color="danger" />
    </div>
    
    <!-- Search Card -->
    <x-search-card
        placeholder="Search by user name, email, or details..."
        id="auditLogsSearchInput"
        name="search"
        :value="request('search')"
        :showCategoryFilter="true"
        :showTypeFilter="true"
        :showDateRange="true"
        categoryLabel="Module"
        typeLabel="Action"
        :categoryOptions="$modules->mapWithKeys(fn($m) => [$m => ucfirst($m)])"
        :typeOptions="$actions->mapWithKeys(fn($a) => [$a => ucfirst($a)])"
        :dateFromValue="request('date_from')"
        :dateToValue="request('date_to')"
    >
        <div id="auditLogsSearchStats" class="text-muted" style="font-size: 0.65rem;">
            {{ $logs->total() }} logs total
        </div>
    </x-search-card>

    <div class="row g-2">
        <!-- Audit Logs Table - main column -->
        <div class="col-lg-8">
            <div id="auditLogsResults">
                @include('shared.audit-logs._results')
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
                        <div class="fw-semibold" style="font-size: 0.7rem;"><i class="ti ti-flag me-1 text-primary"></i> Understanding Actions</div>
                        <ul class="mb-0 ps-3" style="font-size: 0.65rem; color: #555;">
                            <li><span class="text-success">Created</span> - a new record was added</li>
                            <li><span class="text-warning">Updated</span> - an existing record was modified</li>
                            <li><span class="text-danger">Deleted</span> - a record was removed</li>
                        </ul>
                    </div>

                    <div class="mb-2">
                        <div class="fw-semibold" style="font-size: 0.7rem;"><i class="ti ti-filter me-1 text-primary"></i> Filtering</div>
                        <ul class="mb-0 ps-3" style="font-size: 0.65rem; color: #555;">
                            <li>Filter by Module to focus on one part of the system</li>
                            <li>Filter by Action to isolate a specific change type</li>
                            <li>Use the date range to narrow a time window</li>
                        </ul>
                    </div>

                    <x-alert-light class="mb-0 py-1 px-2 rounded-sm">
                        <div class="d-flex align-items-start gap-1">
                            <i class="ti ti-tips text-warning mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">Tips</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">&bull; Logs are system-wide and read-only</small>
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
        url: '{{ route('audit-logs.index') }}',
        resultsSelector: '#auditLogsResults',
        statsSelector: '#auditLogsSearchStats',
        searchInputSelector: '#auditLogsSearchInput',
        suggestionsSelector: '#auditLogsSearchInputSuggestions',
        recentSearchesKey: 'auditLogsRecentSearches',
        statLabel: 'logs',
        perPage: 50,
        filterSelectors: ['#categoryFilter', '#typeFilter', '#dateFromFilter', '#dateToFilter'],
        extraParams: () => ({
            module: document.getElementById('categoryFilter')?.value || '',
            action: document.getElementById('typeFilter')?.value || '',
            date_from: document.getElementById('dateFromFilter')?.value || '',
            date_to: document.getElementById('dateToFilter')?.value || '',
        }),
    });
});
</script>
@endsection