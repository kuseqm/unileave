@extends('layouts.master')

@section('title', 'Graduate Statistics')

@section('content')
@php
    $femaleShare = $totals->total > 0 ? round(($totals->females / $totals->total) * 100) : 0;
    $campusOptions = collect([['value' => '', 'label' => 'All campuses']])->concat($campuses->map(fn ($campus) => ['value' => $campus->id, 'label' => $campus->name]))->all();
    $yearOptions = collect([['value' => '', 'label' => 'All years']])->concat($years->map(fn ($y) => ['value' => $y, 'label' => (string) $y]))->all();
@endphp
<div class="container-fluid px-2">

    <x-page-header title="GRADUATE STATISTICS" description="How many graduates, by year, faculty, programme and campus - with the female share">
        <a href="{{ route('students.statistics.programs') }}" class="btn btn-outline-primary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
            <i class="ti ti-chart-bar me-1"></i> Programme Gender
        </a>
        <a href="{{ route('students.statistics.program-charts') }}" class="btn btn-outline-primary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
            <i class="ti ti-layout-grid me-1"></i> Programme Charts
        </a>
        <a href="{{ route('students.index') }}" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
            <i class="ti ti-arrow-left me-1"></i> Back to Graduates
        </a>
    </x-page-header>

    <x-search-card :action="route('students.statistics')" :reset-url="route('students.statistics')" :show-search="false" icon="ti-filter" title="Filter Graduates" :auto-submit="true">
        <x-slot:filters>
            <div class="col-md-4">
                <x-scrollable-dropdown name="campus_id" id="stats_campus_id" label="Campus" icon="ti-map-pin" placeholder="All campuses" :value="$campusId" :options="$campusOptions" :searchable="false" />
            </div>
            <div class="col-md-3">
                <x-scrollable-dropdown name="year" id="stats_year" label="Graduation Year" icon="ti-calendar" placeholder="All years" :value="$year" :options="$yearOptions" :searchable="false" />
            </div>
            <input type="hidden" name="metric" value="{{ $metric }}">
        </x-slot:filters>
    </x-search-card>

    <div class="row g-2 mb-2">
        <x-stats-card :value="number_format($totals->total)" label="Graduates" subtext="in this view" color="primary" icon="school" :link="route('students.filtered', ['campus_id' => $campusId, 'year' => $year])" />
        <x-stats-card :value="number_format($totals->males)" label="Males" color="info" icon="gender-male" :link="route('students.filtered', ['gender' => 'M', 'campus_id' => $campusId, 'year' => $year])" />
        <x-stats-card :value="number_format($totals->females)" label="Females" color="danger" icon="gender-female" :link="route('students.filtered', ['gender' => 'F', 'campus_id' => $campusId, 'year' => $year])" />
        <x-stats-card :value="$femaleShare.'%'" label="Female share" subtext="of graduates with a total of {{ number_format($totals->total) }}" color="warning" icon="percentage" />
    </div>

    <div class="row g-2 mb-2">
        <div class="col-lg-8">
            <div class="card rounded-sm h-100">
                <div class="card-header py-1 px-2 bg-light"><span style="font-size: 0.7rem; font-weight: 600;"><i class="ti ti-book text-primary me-1"></i>Top 10 Programmes</span></div>
                <div class="card-body p-2" style="height: 300px;">
                    <canvas id="programmeChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card rounded-sm h-100">
                <div class="card-header py-1 px-2 bg-light"><span style="font-size: 0.7rem; font-weight: 600;"><i class="ti ti-chart-pie text-primary me-1"></i>Gender Split</span></div>
                <div class="card-body p-2 d-flex justify-content-center align-items-center" style="height: 300px;">
                    <canvas id="genderChart" style="max-height: 260px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-2 mb-2">
        <div class="col-lg-4">
            <div class="card rounded-sm h-100">
                <div class="card-header py-1 px-2 bg-light"><span style="font-size: 0.7rem; font-weight: 600;"><i class="ti ti-map-pin text-primary me-1"></i>By Campus</span></div>
                <div class="card-body p-2 d-flex justify-content-center align-items-center" style="height: 260px;">
                    <canvas id="campusChart" style="max-height: 220px;"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card rounded-sm h-100">
                <div class="card-header py-1 px-2 bg-light"><span style="font-size: 0.7rem; font-weight: 600;"><i class="ti ti-school text-primary me-1"></i>By Study Level</span></div>
                <div class="card-body p-2" style="height: 260px;">
                    <canvas id="levelChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card rounded-sm h-100">
                <div class="card-header py-1 px-2 bg-light"><span style="font-size: 0.7rem; font-weight: 600;"><i class="ti ti-chart-line text-primary me-1"></i>Graduates by Year</span></div>
                <div class="card-body p-2" style="height: 260px;">
                    <canvas id="graduatesByYearChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-2 mb-2">
        <div class="col-12">
            <div class="card rounded-sm h-100">
                <div class="card-header py-1 px-2 bg-light"><span style="font-size: 0.7rem; font-weight: 600;"><i class="ti ti-building-arch text-primary me-1"></i>By Faculty / School</span></div>
                <div class="card-body p-2" style="height: 320px;">
                    <canvas id="facultyChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    @if((int) $totals->total !== (int) $totals->males + (int) $totals->females)
    <x-alert-light class="mb-2 py-1 px-2 rounded-sm">
        <small class="text-muted" style="font-size: 0.62rem;"><i class="ti ti-alert-triangle text-warning me-1"></i>{{ number_format($totals->total - $totals->males - $totals->females) }} graduate(s) have no sex recorded, so they count in Total only. They are listed on the <a href="{{ route('students.import.index') }}">Import page</a> to be corrected.</small>
    </x-alert-light>
    @endif

    <div class="row g-2 mb-2">
        <div class="col-lg-5">
            <div class="card rounded-sm h-100">
                <div class="card-header py-1 px-2 bg-light"><span style="font-size: 0.7rem; font-weight: 600;"><i class="ti ti-calendar text-primary me-1"></i>By Graduation Year</span></div>
                <x-gender-breakdown-table :rows="$byYear" heading="Year" max-height="340px" />
            </div>
        </div>
        <div class="col-lg-7">
            <div class="card rounded-sm h-100">
                <div class="card-header py-1 px-2 bg-light"><span style="font-size: 0.7rem; font-weight: 600;"><i class="ti ti-building-arch text-primary me-1"></i>By Faculty / School</span></div>
                <x-gender-breakdown-table :rows="$byFaculty" heading="Faculty / School" max-height="340px" />
            </div>
        </div>
    </div>

    <div class="card rounded-sm mb-2">
        <div class="card-header py-1 px-2 bg-light d-flex justify-content-between">
            <span style="font-size: 0.7rem; font-weight: 600;"><i class="ti ti-book text-primary me-1"></i>By Programme</span>
            <span class="text-muted" style="font-size: 0.62rem;">{{ $byProgramme->count() }} programmes</span>
        </div>
        <x-gender-breakdown-table :rows="$byProgramme" heading="Programme" max-height="420px" />
    </div>

    <div class="row g-2 mb-2">
        <div class="col-lg-6">
            <div class="card rounded-sm h-100">
                <div class="card-header py-1 px-2 bg-light"><span style="font-size: 0.7rem; font-weight: 600;"><i class="ti ti-map-pin text-primary me-1"></i>By Campus</span></div>
                <x-gender-breakdown-table :rows="$byCampus" heading="Campus" />
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card rounded-sm h-100">
                <div class="card-header py-1 px-2 bg-light"><span style="font-size: 0.7rem; font-weight: 600;"><i class="ti ti-school text-primary me-1"></i>By Study Level</span></div>
                <x-gender-breakdown-table :rows="$byLevel" heading="Level" />
            </div>
        </div>
    </div>

    <div class="card rounded-sm mb-3">
        <div class="card-header py-1 px-2 bg-light d-flex align-items-center justify-content-between flex-wrap gap-1">
            <span style="font-size: 0.7rem; font-weight: 600;"><i class="ti ti-table text-primary me-1"></i>Programme by Year</span>
            <ul class="nav nav-pills gap-1">
                @foreach($metrics as $key => $metricLabel)
                <li class="nav-item">
                    <a class="nav-link py-0 px-2 {{ $metric === $key ? 'active' : '' }}" style="font-size: 0.65rem;" href="{{ route('students.statistics', array_filter(['campus_id' => $campusId, 'year' => $year, 'metric' => $key])) }}">{{ $metricLabel }}</a>
                </li>
                @endforeach
            </ul>
        </div>
        <div class="table-responsive" style="max-height: 480px; overflow: auto;">
            <table class="table table-sm mb-0 align-middle" style="font-size: 0.68rem;">
                <thead class="table-light" style="position: sticky; top: 0; z-index: 2;">
                    <tr>
                        <th style="position: sticky; left: 0; background: #f8f9fa; min-width: 240px;">Programme</th>
                        @foreach($matrixYears as $matrixYear)
                            <th class="text-end">{{ $matrixYear }}</th>
                        @endforeach
                        <th class="text-end">All years</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($matrix as $row)
                    <tr>
                        <td style="position: sticky; left: 0; background: #fff;" class="fw-semibold">{{ $row['label'] }}</td>
                        @foreach($matrixYears as $matrixYear)
                            <td class="text-end {{ ($row['cells'][$matrixYear] ?? 0) === 0 ? 'text-muted' : '' }}">{{ ($row['cells'][$matrixYear] ?? 0) === 0 ? '·' : number_format($row['cells'][$matrixYear]) }}</td>
                        @endforeach
                        <td class="text-end fw-semibold">{{ number_format($row['total']) }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="{{ $matrixYears->count() + 2 }}" class="text-center text-muted py-3">No graduates match.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const byYear = @json($byYear);
    const totals = @json($totals);

    new Chart(document.getElementById('graduatesByYearChart'), {
        type: 'line',
        data: {
            labels: byYear.map(row => row.label),
            datasets: [{
                label: 'Graduates',
                data: byYear.map(row => row.total),
                borderColor: 'rgba(13, 110, 253, 1)',
                backgroundColor: 'rgba(13, 110, 253, 0.1)',
                borderWidth: 2,
                pointRadius: 3,
                pointBackgroundColor: 'rgba(13, 110, 253, 1)',
                tension: 0.3,
                fill: true,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { beginAtZero: true, ticks: { precision: 0 } },
                x: { ticks: { font: { size: 9 }, maxRotation: 45, minRotation: 0 } }
            }
        }
    });

    const unknown = Math.max(0, parseInt(totals.total) - parseInt(totals.males) - parseInt(totals.females));

    new Chart(document.getElementById('genderChart'), {
        type: 'doughnut',
        data: {
            labels: ['Males', 'Females', 'Not recorded'],
            datasets: [{
                data: [
                    parseInt(totals.males),
                    parseInt(totals.females),
                    unknown
                ],
                backgroundColor: ['#0dcaf0', '#dc3545', '#adb5bd'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { font: { size: 11 } } }
            }
        }
    });

    const byCampus = @json($byCampus);
    new Chart(document.getElementById('campusChart'), {
        type: 'doughnut',
        data: {
            labels: byCampus.map(row => row.label),
            datasets: [{
                data: byCampus.map(row => row.total),
                backgroundColor: ['#0d6efd', '#20c997', '#fd7e14', '#6610f2', '#dc3545', '#0dcaf0'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'right', labels: { font: { size: 10 }, boxWidth: 12 } }
            }
        }
    });

    const byLevel = @json($byLevel);
    new Chart(document.getElementById('levelChart'), {
        type: 'bar',
        data: {
            labels: byLevel.map(row => row.label),
            datasets: [{
                label: 'Graduates',
                data: byLevel.map(row => row.total),
                backgroundColor: 'rgba(32, 201, 151, 0.7)',
                borderColor: 'rgba(32, 201, 151, 1)',
                borderWidth: 1,
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
        }
    });

    const byProgramme = @json($byProgramme);
    const topProgrammes = byProgramme.slice(0, 10).reverse();
    new Chart(document.getElementById('programmeChart'), {
        type: 'bar',
        data: {
            labels: topProgrammes.map(row => row.label),
            datasets: [{
                label: 'Graduates',
                data: topProgrammes.map(row => row.total),
                backgroundColor: 'rgba(111, 66, 193, 0.7)',
                borderColor: 'rgba(111, 66, 193, 1)',
                borderWidth: 1,
                borderRadius: 4,
                maxBarThickness: 18
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { x: { beginAtZero: true, ticks: { precision: 0 } } }
        }
    });

    const byFaculty = @json($byFaculty).reverse();
    new Chart(document.getElementById('facultyChart'), {
        type: 'bar',
        data: {
            labels: byFaculty.map(row => row.label + (row.sub ? ' — ' + row.sub : '')),
            datasets: [{
                label: 'Graduates',
                data: byFaculty.map(row => row.total),
                backgroundColor: 'rgba(184, 143, 66, 0.7)',
                borderColor: 'rgba(184, 143, 66, 1)',
                borderWidth: 1,
                borderRadius: 4,
                maxBarThickness: 20
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { x: { beginAtZero: true, ticks: { precision: 0 } } }
        }
    });

    (function () {
        const form = document.querySelector('form[action="{{ route('students.statistics') }}"]');
        if (! form) return;

        form.addEventListener('click', function (e) {
            const item = e.target.closest('.scrollable-dropdown-menu .dropdown-item');
            if (item) {
                setTimeout(() => form.submit(), 0);
            }
        });
    })();
</script>
@endsection
