@extends('layouts.master')

@section('title', 'Graduates')

@section('content')
<div class="container-fluid px-2">

    <!-- Page Header -->
    <x-page-header title="GRADUATES" description="Search graduate records and verify graduation status">
        @can('import_students')
        <a href="{{ route('students.import.index') }}" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
            <i class="ti ti-upload me-1"></i> Import Graduation Roster
        </a>
        <a href="{{ route('students.certificates.index') }}" class="btn btn-outline-primary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
            <i class="ti ti-certificate me-1"></i> Certificate Numbers
        </a>
        @endcan
        <a href="{{ route('students.index') }}" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
            <i class="ti ti-refresh me-1"></i> Refresh
        </a>
    </x-page-header>

    <!-- Stats Cards Row -->
    <div class="row g-2 mb-3">
        <x-stats-card value="{{ number_format($stats['total']) }}" label="Total Records" subtext="records on file" color="primary" icon="school" />
        <x-stats-card value="{{ number_format($stats['graduated']) }}" label="Graduated" subtext="confirmed graduates" color="success" icon="certificate" />
        <x-stats-card value="{{ number_format($stats['not_graduated']) }}" label="Not Graduated" subtext="still enrolled / withdrawn" color="secondary" icon="user-question" />
        <x-stats-card value="{{ number_format($graduateCounts->females ?? 0) }}" label="Female Graduates" subtext="{{ ($graduateCounts->total ?? 0) > 0 ? round(($graduateCounts->females / $graduateCounts->total) * 100) : 0 }}% of graduates" color="danger" icon="gender-female" link="{{ route('students.statistics') }}" />
    </div>

    <x-search-card
        placeholder="Search by registration number or name..."
        id="studentSearchInput"
        name="search"
        :value="request('search')"
        :showTypeFilter="true"
        typeLabel="Status"
        :typeOptions="['graduated' => 'Graduated', 'not_graduated' => 'Not Graduated']"
    >
        <div id="studentSearchStats" class="text-muted" style="font-size: 0.65rem;">
            {{ $students->total() }} records total
        </div>
    </x-search-card>

    <div class="row g-2">
        <!-- Results table - main column -->
        <div class="col-lg-8">
            <div id="studentResults">
                @include('students._results', ['students' => $students])
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
                        <div class="fw-semibold" style="font-size: 0.7rem;"><i class="ti ti-flag me-1 text-primary"></i> About This Page</div>
                        <ul class="mb-0 ps-3" style="font-size: 0.65rem; color: #555;">
                            <li>Search by registration number or full name</li>
                            <li>Open a graduate's record to verify graduation status, edit missing details, and generate a confirmation letter</li>
                            <li>{{ $isCrossInstitute ? 'You see records for every institution' : 'You only see your own institution\'s records' }}</li>
                        </ul>
                    </div>

                    <x-alert-light class="mb-0 py-1 px-2 rounded-sm">
                        <div class="d-flex align-items-start gap-1">
                            <i class="ti ti-tips text-warning mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">Graduation Roster import</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">GPA, classification and degree wording are added or updated later, only when a real verification request needs them.</small>
                            </div>
                        </div>
                    </x-alert-light>
                </div>
            </div>

            @if($graduateCounts && $graduateCounts->total > 0)
            @php($femaleShare = round(($graduateCounts->females / $graduateCounts->total) * 100))
            <div class="card rounded-sm mt-2">
                <div class="card-header py-1 px-2 bg-light d-flex align-items-center justify-content-between">
                    <span class="d-flex align-items-center">
                        <i class="ti ti-chart-bar text-primary me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Statistics</span>
                    </span>
                    <a href="{{ route('students.statistics') }}" class="text-decoration-none" style="font-size: 0.62rem;">Full statistics <i class="ti ti-arrow-right"></i></a>
                </div>
                <div class="card-body py-2 px-2">
                    <div class="row g-1 text-center mb-2">
                        <div class="col-4"><div class="fw-bold text-info" style="font-size: 0.95rem;">{{ number_format($graduateCounts->males) }}</div><div class="text-muted" style="font-size: 0.58rem;">Males</div></div>
                        <div class="col-4"><div class="fw-bold text-danger" style="font-size: 0.95rem;">{{ number_format($graduateCounts->females) }}</div><div class="text-muted" style="font-size: 0.58rem;">Females</div></div>
                        <div class="col-4"><div class="fw-bold text-warning" style="font-size: 0.95rem;">{{ $femaleShare }}%</div><div class="text-muted" style="font-size: 0.58rem;">Female share</div></div>
                    </div>
                    <x-gender-breakdown-table :rows="$latestYears" heading="Year" max-height="240px" />
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    initLiveSearch({
        url: '{{ route('students.index') }}',
        resultsSelector: '#studentResults',
        statsSelector: '#studentSearchStats',
        searchInputSelector: '#studentSearchInput',
        suggestionsSelector: '#studentSearchInputSuggestions',
        recentSearchesKey: 'studentRecentSearches',
        statLabel: 'students',
        perPage: 50,
        filterSelectors: ['#typeFilter'],
        extraParams: () => ({
            status: document.getElementById('typeFilter')?.value || '',
        }),
    });
});
</script>
@endsection
