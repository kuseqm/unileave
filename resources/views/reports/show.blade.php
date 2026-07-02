@extends('layouts.master')

@section('title', $definition->display_name)

@section('content')
<div class="container-fluid px-2">
    
    <!-- Back button + title (like in school edit) -->
    <div class="card rounded-sm mb-3">
        <div class="card-body py-2 px-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h1 class="h5 mb-0" style="color: #3B82F6; font-weight: 700;">{{ strtoupper($definition->display_name) }}</h1>
                    <p class="text-muted mb-0" style="font-size: 0.7rem;">{{ $definition->description }}</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
                        <i class="ti ti-arrow-left me-1"></i> Back
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards Row (dynamic based on report data) -->
    @php
        // Compute stats from $reportData
        $rows = $reportData['rows'] ?? collect();
        $totalRequests = $rows->count();
        
        // For Department Roster, use different stats
        $stats = $reportData['stats'] ?? [];
        $totalDepartments = $stats['total_departments'] ?? 0;
        $totalStaff = $stats['total_staff'] ?? 0;
        $departmentsWithHod = $stats['departments_with_hod'] ?? 0;
        
        // Regular report stats
        $pendingCount = method_exists($rows, 'where') ? $rows->where('status', 'Pending')->count() : 0;
        $approvedCount = method_exists($rows, 'where') ? $rows->where('status', 'Approved')->count() : 0;
        $rejectedCount = method_exists($rows, 'where') ? $rows->where('status', 'Rejected')->count() : 0;
    @endphp

    <div class="row g-2 mb-3">
        @if($totalDepartments > 0)
            <!-- Department Roster Stats -->
            <x-stats-card value="{{ $totalDepartments }}" label="Total Departments" subtext="across institution" />
            <x-stats-card value="{{ $totalStaff }}" label="Total Staff" subtext="active employees" color="success" />
            <x-stats-card value="{{ $departmentsWithHod }}" label="Departments with HOD" subtext="assigned heads" color="info" />
            <x-stats-card value="{{ $rows->count() }}" label="Total Assignments" subtext="staff records" color="primary" />
        @else
            <!-- Regular Report Stats -->
            <x-stats-card value="{{ $totalRequests }}" label="Total Requests" subtext="all time" />
            <x-stats-card value="{{ $pendingCount }}" label="Pending" subtext="awaiting approval" color="warning" />
            <x-stats-card value="{{ $approvedCount }}" label="Approved" subtext="granted" color="success" />
            <x-stats-card value="{{ $rejectedCount }}" label="Rejected" subtext="denied" color="danger" />
        @endif
    </div>

    <!-- Sophisticated Search Card -->
    <x-search-card placeholder="Search by staff name, department or leave type..." id="reportSearchInput" />

    <!-- Graph + Explanation Cards (side by side) -->
    <div class="row g-2 mb-3">
        <!-- Graph Card -->
        <div class="col-lg-7">
            <div class="card rounded-sm h-100">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-chart-bar text-primary me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Chart View</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    @if(isset($reportData['chart']) && $reportData['chart'])
                    <canvas id="reportChart" style="height: 300px; width: 100%;"></canvas>
                    @elseif(isset($reportData['chart_data']) && !empty($reportData['chart_data']['labels']))
                    <canvas id="reportChart" style="height: 300px; width: 100%;"></canvas>
                    @else
                    <p class="text-muted text-center mb-0" style="font-size: 0.7rem;">No chart available for this report.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Explanation Card -->
        <div class="col-lg-5">
            <div class="card rounded-sm h-100">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-info-circle text-info me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">About this Report</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <p style="font-size: 0.7rem;">{{ $definition->description }}</p>
                    @if(isset($reportData['chart']) || isset($reportData['chart_data']))
                    <div class="mt-2">
                        <div class="fw-semibold" style="font-size: 0.65rem;">Chart Legend</div>
                        <div class="d-flex gap-3 mt-1">
                            <div><span class="badge bg-primary" style="width: 12px; height: 12px; display: inline-block;"></span> <span style="font-size: 0.6rem;">Value</span></div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Long Details Card (Data Table) -->
    <div class="card rounded-sm">
        <div class="card-header py-1 px-2 bg-light">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <i class="ti ti-table text-primary me-1" style="font-size: 0.8rem;"></i>
                    <span style="font-size: 0.7rem; font-weight: 600;">Detailed Records</span>
                </div>
                <div class="text-muted" style="font-size: 0.6rem;">
                    Showing {{ $rows->count() }} records
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm mb-0" style="font-size: 0.7rem;" id="reportTable">
                    <thead class="bg-light">
                        <tr>
                            @foreach($rows->first() ?? [] as $col => $val)
                                <th class="py-1">{{ ucwords(str_replace('_', ' ', $col)) }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rows as $row)
                        <tr>
                            @foreach($row as $value)
                                <td class="py-1">{{ $value }}</td>
                            @endforeach
                        </tr>
                        @empty
                        <tr><td colspan="100" class="text-center py-3 text-muted">No data found</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer py-1 px-2">
            <small class="text-muted">Export options: 
                <a href="{{ route('reports.export.pdf', $definition->name) . '?' . http_build_query(request()->query()) }}" class="text-danger">PDF</a> | 
                <a href="{{ route('reports.export.excel', $definition->name) . '?' . http_build_query(request()->query()) }}" class="text-success">Excel</a>
            </small>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    @if(isset($reportData['chart']) && $reportData['chart'])
    const ctx = document.getElementById('reportChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($reportData['chart']['labels']) !!},
            datasets: [{
                label: '{{ $definition->chart_config["title"] ?? "Value" }}',
                data: {!! json_encode($reportData['chart']['series']) !!},
                backgroundColor: 'rgba(54, 162, 235, 0.5)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            scales: { y: { beginAtZero: true } }
        }
    });
    @elseif(isset($reportData['chart_data']) && !empty($reportData['chart_data']['labels']))
    const ctx = document.getElementById('reportChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($reportData['chart_data']['labels']) !!},
            datasets: {!! json_encode($reportData['chart_data']['datasets']) !!}
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            scales: { y: { beginAtZero: true } }
        }
    });
    @endif

    // Search functionality for the table
    const searchInput = document.getElementById('reportSearchInput');
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            const term = this.value.toLowerCase();
            const rows = document.querySelectorAll('#reportTable tbody tr');
            rows.forEach(row => {
                const text = row.innerText.toLowerCase();
                row.style.display = text.includes(term) ? '' : 'none';
            });
        });
    }
</script>
@endpush
@endsection