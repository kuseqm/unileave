@extends('layouts.master')

@section('title', 'Programme Charts')

@section('content')
@php
    $campusOptions = collect([['value' => '', 'label' => 'All campuses']])->concat($campuses->map(fn ($campus) => ['value' => $campus->id, 'label' => $campus->name]))->all();
    $yearOptions = collect([['value' => '', 'label' => 'All years']])->concat($years->map(fn ($y) => ['value' => $y, 'label' => (string) $y]))->all();
    $femaleShare = $totals->total > 0 ? round(($totals->females / $totals->total) * 100) : 0;
@endphp
<div class="container-fluid px-2">

    <x-page-header title="PROGRAMME CHARTS" description="Each programme gets its own male / female bar chart by graduation year">
        <a href="{{ route('students.statistics') }}" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
            <i class="ti ti-arrow-left me-1"></i> Back to Statistics
        </a>
    </x-page-header>

    <x-search-card :action="route('students.statistics.program-charts')" :reset-url="route('students.statistics.program-charts')" :show-search="false" icon="ti-filter" title="Filter" :auto-submit="true">
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

    <div class="row g-2">
        @forelse($programs as $index => $program)
        <div class="col-md-6 col-lg-4 col-xl-3">
            <div class="card rounded-sm h-100">
                <div class="card-header py-1 px-2 bg-light">
                    <span style="font-size: 0.7rem; font-weight: 600;">
                        <i class="ti ti-book text-primary me-1"></i>{{ $program['label'] }}
                    </span>
                    <span class="text-muted" style="font-size: 0.6rem;">{{ $program['sub'] }} · {{ number_format($program['total']) }}</span>
                </div>
                <div class="card-body p-2" style="height: 220px;">
                    <canvas id="programChart-{{ $index }}"></canvas>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card rounded-sm py-5 text-center text-muted" style="font-size: 0.7rem;">
                No graduates match the selected filters.
            </div>
        </div>
        @endforelse
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const programData = @json($programs);

    programData.forEach(function (program, index) {
        const canvas = document.getElementById('programChart-' + index);
        if (! canvas) return;

        new Chart(canvas, {
            type: 'bar',
            data: {
                labels: program.years.map(String),
                datasets: [
                    {
                        label: 'Males',
                        data: program.years.map(year => program.males[year] || 0),
                        backgroundColor: 'rgba(13, 202, 240, 0.75)',
                        borderColor: 'rgba(13, 202, 240, 1)',
                        borderWidth: 1,
                        borderRadius: 3,
                        maxBarThickness: 14
                    },
                    {
                        label: 'Females',
                        data: program.years.map(year => program.females[year] || 0),
                        backgroundColor: 'rgba(220, 53, 69, 0.75)',
                        borderColor: 'rgba(220, 53, 69, 1)',
                        borderWidth: 1,
                        borderRadius: 3,
                        maxBarThickness: 14
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    title: { display: false }
                },
                scales: {
                    x: {
                        ticks: { font: { size: 9 }, maxRotation: 45 },
                        grid: { display: false }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: { precision: 0, font: { size: 9 } },
                        grid: { color: 'rgba(0,0,0,0.05)' }
                    }
                }
            }
        });
    });

    (function () {
        const form = document.querySelector('form[action="{{ route('students.statistics.program-charts') }}"]');
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
