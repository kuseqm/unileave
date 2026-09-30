@extends('layouts.master')

@section('title', 'Programme Gender Comparison')

@section('content')
@php
    $campusOptions = collect([['value' => '', 'label' => 'All campuses']])->concat($campuses->map(fn ($campus) => ['value' => $campus->id, 'label' => $campus->name]))->all();
    $yearOptions = collect([['value' => '', 'label' => 'All years']])->concat($years->map(fn ($y) => ['value' => $y, 'label' => (string) $y]))->all();
    $femaleShare = $totals->total > 0 ? round(($totals->females / $totals->total) * 100) : 0;
@endphp
<div class="container-fluid px-2">

    <x-page-header title="PROGRAMME GENDER COMPARISON" description="Males versus females for every programme, visualised with side-by-side bars">
        <a href="{{ route('students.statistics.program-charts') }}" class="btn btn-outline-primary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
            <i class="ti ti-layout-grid me-1"></i> Individual Programme Charts
        </a>
        <a href="{{ route('students.statistics') }}" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
            <i class="ti ti-arrow-left me-1"></i> Back to Statistics
        </a>
    </x-page-header>

    <x-search-card :action="route('students.statistics.programs')" :reset-url="route('students.statistics.programs')" :show-search="false" icon="ti-filter" title="Filter" :auto-submit="true">
        <x-slot:filters>
            <div class="col-md-4">
                <x-scrollable-dropdown name="campus_id" id="stats_campus_id" label="Campus" icon="ti-map-pin" placeholder="All campuses" :value="$campusId" :options="$campusOptions" :searchable="false" />
            </div>
            <div class="col-md-3">
                <x-scrollable-dropdown name="year" id="stats_year" label="Graduation Year" icon="ti-calendar" placeholder="All years" :value="$year" :options="$yearOptions" :searchable="false" />
            </div>
        </x-slot:filters>
    </x-search-card>

    <div class="row g-2 mb-3">
        <x-stats-card :value="number_format($totals->total)" label="Graduates" color="primary" icon="school" />
        <x-stats-card :value="number_format($totals->males)" label="Males" color="info" icon="gender-male" />
        <x-stats-card :value="number_format($totals->females)" label="Females" color="danger" icon="gender-female" />
        <x-stats-card :value="$femaleShare.'%'" label="Female share" color="warning" icon="percentage" />
    </div>

    <div class="card rounded-sm">
        <div class="card-header py-1 px-2 bg-light d-flex align-items-center justify-content-between flex-wrap gap-1">
            <span style="font-size: 0.7rem; font-weight: 600;">
                <i class="ti ti-chart-bar text-primary me-1"></i>Males vs Females by Programme
            </span>
            <span class="text-muted" style="font-size: 0.62rem;">{{ $byProgramme->count() }} programmes shown</span>
        </div>
        <div class="card-body p-2" style="height: 520px;">
            <canvas id="programGenderChart"></canvas>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const byProgramme = @json($byProgramme);

    new Chart(document.getElementById('programGenderChart'), {
        type: 'bar',
        data: {
            labels: byProgramme.map(row => row.label + (row.sub ? ' · ' + row.sub : '')),
            datasets: [
                {
                    label: 'Males',
                    data: byProgramme.map(row => parseInt(row.males)),
                    backgroundColor: 'rgba(13, 202, 240, 0.75)',
                    borderColor: 'rgba(13, 202, 240, 1)',
                    borderWidth: 1,
                    borderRadius: 3,
                    maxBarThickness: 16
                },
                {
                    label: 'Females',
                    data: byProgramme.map(row => parseInt(row.females)),
                    backgroundColor: 'rgba(220, 53, 69, 0.75)',
                    borderColor: 'rgba(220, 53, 69, 1)',
                    borderWidth: 1,
                    borderRadius: 3,
                    maxBarThickness: 16
                }
            ]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'top', labels: { font: { size: 12 }, usePointStyle: true } },
                tooltip: {
                    callbacks: {
                        title: function (context) {
                            const row = byProgramme[context[0].dataIndex];
                            return row.full_name;
                        },
                        label: function (context) {
                            const row = byProgramme[context.dataIndex];
                            const total = parseInt(row.males) + parseInt(row.females);
                            const value = context.raw;
                            const share = total > 0 ? Math.round((value / total) * 100) : 0;
                            return `${context.dataset.label}: ${value} (${share}%)`;
                        }
                    }
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    ticks: { precision: 0 }
                },
                y: {
                    ticks: { font: { size: 10 } }
                }
            }
        }
    });

    (function () {
        const form = document.querySelector('form[action="{{ route('students.statistics.programs') }}"]');
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
