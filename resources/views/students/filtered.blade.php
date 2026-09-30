@php
    $title = match($gender) {
        'M' => 'MALE GRADUATES',
        'F' => 'FEMALE GRADUATES',
        default => 'GRADUATES',
    };
    $description = 'Filtered records';
    if ($campusId) $description .= ' for selected campus';
    if ($year) $description .= ' in ' . $year;
    $description .= ' (' . number_format($students->total()) . ' found)';
@endphp
@extends('layouts.master')

@section('title', $title)

@section('content')
<div class="container-fluid px-2">

    <x-page-header :title="$title" :description="$description">
        <a href="{{ route('students.statistics') }}" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
            <i class="ti ti-arrow-left me-1"></i> Back to Statistics
        </a>
    </x-page-header>

    <div class="row g-2 mb-2">
        <div class="col-lg-6">
            <div class="card rounded-sm h-100">
                <div class="card-header py-1 px-2 bg-light"><span style="font-size: 0.7rem; font-weight: 600;"><i class="ti ti-chart-line text-primary me-1"></i>By Graduation Year</span></div>
                <div class="card-body p-2" style="height: 260px;">
                    <canvas id="yearChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card rounded-sm h-100">
                <div class="card-header py-1 px-2 bg-light"><span style="font-size: 0.7rem; font-weight: 600;"><i class="ti ti-book text-primary me-1"></i>Top 10 Programmes</span></div>
                <div class="card-body p-2" style="height: 260px;">
                    <canvas id="programmeChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="card rounded-sm">
        <div class="card-header py-1 px-2 bg-light">
            <div class="d-flex align-items-center">
                <i class="ti ti-school text-primary me-1" style="font-size: 0.8rem;"></i>
                <span style="font-size: 0.7rem; font-weight: 600;">Records</span>
                <span class="text-muted ms-2 small">Showing {{ $students->firstItem() ?? 0 }} to {{ $students->lastItem() ?? 0 }} of {{ $students->total() }}</span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm mb-0" style="font-size: 0.72rem;">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-2 py-1">Reg. Number</th>
                            <th class="py-1">Name</th>
                            <th class="py-1">Programme</th>
                            <th class="py-1">Grad. Year</th>
                            <th class="py-1">Campus</th>
                            <th class="py-1">Gender</th>
                            <th class="py-1">Degree</th>
                            <th class="pe-2 py-1 text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $student)
                        <tr>
                            <td class="ps-2 py-1"><code style="font-size: 0.65rem;">{{ $student->registration_number }}</code></td>
                            <td class="py-1">{{ $student->formatted_name }}</td>
                            <td class="py-1" @if($student->program) title="{{ $student->program->name }}" @endif>{{ $student->effective_degree_abbreviation ?? $student->program?->code ?? '—' }}</td>
                            <td class="py-1">{{ $student->graduation_year }}</td>
                            <td class="py-1">{{ $student->campus?->name ?? '—' }}</td>
                            <td class="py-1">{{ $student->gender === 'M' ? 'Male' : ($student->gender === 'F' ? 'Female' : '—') }}</td>
                            <td class="py-1">{{ $student->degree_name ?? $student->program?->degree_name ?? '—' }}</td>
                            <td class="pe-2 py-1 text-end">
                                <a href="{{ route('students.show', $student->id) }}" class="btn btn-sm btn-outline-info rounded-sm" style="font-size: 0.6rem; padding: 0.15rem 0.4rem;" title="View">
                                    <i class="ti ti-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-3 text-muted" style="font-size: 0.7rem;">
                                <i class="ti ti-school" style="font-size: 48px;"></i>
                                <p class="mt-2">No records found</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer py-1 px-2">
            <x-pagination :items="$students" :perPageOptions="[10, 25, 50, 100]" />
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const byYear = @json($byYear).reverse();
    new Chart(document.getElementById('yearChart'), {
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
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { precision: 0 } },
                x: { ticks: { font: { size: 10 }, maxRotation: 45, minRotation: 0 } }
            }
        }
    });

    const byProgramme = @json($byProgramme).reverse();
    new Chart(document.getElementById('programmeChart'), {
        type: 'bar',
        data: {
            labels: byProgramme.map(row => row.label),
            datasets: [{
                label: 'Graduates',
                data: byProgramme.map(row => row.total),
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
</script>
@endsection
