@extends('layouts.master')

@section('title', 'Document Verification Log')

@section('content')
<div class="container-fluid px-2">

    <!-- Page Header -->
    <x-page-header title="DOCUMENT VERIFICATION LOG" :description="$isCrossInstitute ? 'Every QR-code scan across all institutions' : 'Every QR-code scan of your institution\'s documents'">
        <div class="d-flex gap-2">
            <div class="btn-group btn-group-sm" role="group">
                <a href="{{ route('verification-logs.index') }}" class="btn {{ $isArchivedView ? 'btn-outline-secondary' : 'btn-secondary' }} rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
                    <i class="ti ti-list me-1"></i> Active
                </a>
                <a href="{{ route('verification-logs.index', ['view' => 'archived']) }}" class="btn {{ $isArchivedView ? 'btn-secondary' : 'btn-outline-secondary' }} rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
                    <i class="ti ti-archive me-1"></i> Archived
                    @if($stats['archived'] > 0)
                        <span class="badge bg-dark rounded-pill ms-1" style="font-size: 0.55rem;">{{ $stats['archived'] }}</span>
                    @endif
                </a>
            </div>
            <a href="{{ route('verification-logs.index', request()->query()) }}" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
                <i class="ti ti-refresh me-1"></i> Refresh
            </a>
        </div>
    </x-page-header>

    <!-- Stats Cards Row -->
    <div class="row g-2 mb-3">
        <x-stats-card value="{{ number_format($stats['total']) }}" label="Total Scans" subtext="all time recorded" color="primary" icon="scan" />
        <x-stats-card value="{{ number_format($stats['successful']) }}" label="Verified" subtext="genuine document scans" color="success" icon="shield-check" />
        <x-stats-card value="{{ number_format($stats['failed']) }}" label="Failed" subtext="code not recognized" color="danger" icon="shield-x" />
        <x-stats-card value="{{ number_format($stats['today']) }}" label="Today" subtext="scans in last 24 hours" color="info" icon="calendar-event" />
    </div>

    <div class="row g-2">
        <!-- Search + table - main column -->
        <div class="col-lg-8">
            <x-search-card
                placeholder="Search by code or IP address..."
                id="verificationLogSearchInput"
                name="search"
                :value="request('search')"
                :showCategoryFilter="true"
                :showTypeFilter="true"
                :showDateRange="true"
                categoryLabel="Document Type"
                typeLabel="Outcome"
                :categoryOptions="['leave_letter' => 'Leave Letter', 'travel_letter' => 'Travel Letter', 'report' => 'Report', 'unknown' => 'Unrecognized Code']"
                :typeOptions="['successful' => 'Verified', 'failed' => 'Failed']"
                :dateFromValue="request('date_from')"
                :dateToValue="request('date_to')"
            >
                <div id="verificationLogSearchStats" class="text-muted" style="font-size: 0.65rem;">
                    {{ $logs->total() }} scans total
                </div>
            </x-search-card>

            <div id="verificationLogResults">
                @include('shared.verification-logs._results', ['logs' => $logs, 'isArchivedView' => $isArchivedView])
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
                        <div class="fw-semibold" style="font-size: 0.7rem;"><i class="ti ti-flag me-1 text-primary"></i> About This Log</div>
                        <ul class="mb-0 ps-3" style="font-size: 0.65rem; color: #555;">
                            <li>Records every scan of a QR verification code, whether it succeeded or not</li>
                            <li>{{ $isCrossInstitute ? 'You see scans for every institution in the system' : 'You only see scans of your own institution\'s documents' }}</li>
                            <li>A cluster of failed scans on similar codes can indicate tampering attempts</li>
                        </ul>
                    </div>

                    <div class="mb-2">
                        <div class="fw-semibold" style="font-size: 0.7rem;"><i class="ti ti-filter me-1 text-primary"></i> Filtering</div>
                        <ul class="mb-0 ps-3" style="font-size: 0.65rem; color: #555;">
                            <li>Filter by Document Type or Outcome to narrow results</li>
                            <li>Use the date range to review a specific period</li>
                        </ul>
                    </div>

                    <x-alert-light class="mb-0 py-1 px-2 rounded-sm">
                        <div class="d-flex align-items-start gap-1">
                            <i class="ti ti-tips text-warning mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">Tips</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">&bull; Records are immutable and kept for audit purposes</small>
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
        url: '{{ route('verification-logs.index') }}',
        resultsSelector: '#verificationLogResults',
        statsSelector: '#verificationLogSearchStats',
        searchInputSelector: '#verificationLogSearchInput',
        suggestionsSelector: '#verificationLogSearchInputSuggestions',
        recentSearchesKey: 'verificationLogRecentSearches',
        statLabel: 'scans',
        perPage: 50,
        filterSelectors: ['#categoryFilter', '#typeFilter', '#dateFromFilter', '#dateToFilter'],
        extraParams: () => ({
            document_type: document.getElementById('categoryFilter')?.value || '',
            outcome: document.getElementById('typeFilter')?.value || '',
            date_from: document.getElementById('dateFromFilter')?.value || '',
            date_to: document.getElementById('dateToFilter')?.value || '',
            view: new URLSearchParams(window.location.search).get('view') || '',
        }),
    });
});
</script>
@endsection
