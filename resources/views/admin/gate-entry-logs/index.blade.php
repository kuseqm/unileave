@extends('layouts.master')

@section('title', 'Gate Entry Logs')

@section('content')
<div class="container-fluid px-2">
    <x-breadcrumb :items="[
        ['name' => 'Gate Entry Logs', 'url' => route('admin.gate-entry-logs.index')]
    ]" />

    <x-page-header title="GATE ENTRY LOGS" description="View and filter entry/exit records.">
        @can('record_entry_exit')
        <a href="{{ route('admin.gate-entry-logs.create') }}" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
            <i class="ti ti-plus me-1"></i> Manual Entry
        </a>
        @endcan
        @canany(['record_entry_exit', 'verify_qr_codes'])
        <a href="{{ route('admin.gate-entry-logs.scan') }}" class="btn btn-outline-primary btn-sm rounded-sm ms-1" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
            <i class="ti ti-scan me-1"></i> Scan
        </a>
        @endcanany
    </x-page-header>

    <x-alert-success />
    <x-alert-danger />

    <x-search-card
        placeholder="Search by staff, gate or reference..."
        id="gateEntrySearchInput"
        name="search"
        :value="request('search')"
        :showCategoryFilter="true"
        categoryLabel="Action"
        :categoryOptions="['entry' => 'Entry', 'exit' => 'Exit']"
        :showDateRange="true"
        :dateFromValue="request('from')"
        :dateToValue="request('to')"
    >
        <div id="gateEntrySearchStats" class="text-muted" style="font-size: 0.65rem;">
            {{ $logs->total() }} gate logs
        </div>
    </x-search-card>

    <div class="row g-2">
        <!-- Entry/Exit Log - main column -->
        <div class="col-lg-8">
            <div class="card border-0 rounded-sm shadow-sm">
                <div class="card-body p-0" id="gateEntryResults">
                    @include('admin.gate-entry-logs._results')
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
                        <div class="fw-semibold" style="font-size: 0.7rem;"><i class="ti ti-door-enter me-1 text-primary"></i> About Gate Logs</div>
                        <p class="mb-0" style="font-size: 0.65rem; color: #555;">Every gate entry or exit, whether scanned via QR or recorded manually, is logged here with the staff member, gate and reference.</p>
                    </div>

                    <div class="mb-2">
                        <div class="fw-semibold" style="font-size: 0.7rem;"><i class="ti ti-link me-1 text-primary"></i> Quick Links</div>
                        <ul class="mb-0 ps-3" style="font-size: 0.65rem; color: #555;">
                            @canany(['record_entry_exit', 'verify_qr_codes'])
                            <li><a href="{{ route('admin.gate-entry-logs.scan') }}">Scan</a> - verify a QR code at the gate</li>
                            @endcanany
                            @can('record_entry_exit')
                            <li><a href="{{ route('admin.gate-entry-logs.create') }}">Manual Entry</a> - record entry/exit without scanning</li>
                            @endcan
                        </ul>
                    </div>

                    <x-alert-light class="mb-0 py-1 px-2 rounded-sm">
                        <div class="d-flex align-items-start gap-1">
                            <i class="ti ti-tips text-warning mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">Tips</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">&bull; Use the date range filter to audit a specific day or period</small><br>
                                <small class="text-muted" style="font-size: 0.6rem;">&bull; Filter by Entry or Exit to spot mismatched pairs</small>
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
        url: '{{ route('admin.gate-entry-logs.index') }}',
        resultsSelector: '#gateEntryResults',
        statsSelector: '#gateEntrySearchStats',
        searchInputSelector: '#gateEntrySearchInput',
        suggestionsSelector: '#gateEntrySearchInputSuggestions',
        recentSearchesKey: 'gateEntryRecentSearches',
        statLabel: 'gate logs',
        perPage: 20,
        filterSelectors: ['#categoryFilter', '#dateFromFilter', '#dateToFilter'],
        extraParams: () => ({
            action: document.getElementById('categoryFilter')?.value || '',
            from: document.getElementById('dateFromFilter')?.value || '',
            to: document.getElementById('dateToFilter')?.value || '',
        }),
    });
});
</script>
@endsection
