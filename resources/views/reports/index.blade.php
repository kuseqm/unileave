@extends('layouts.master')

@section('title', 'Reports Dashboard')

@section('content')
<div class="container-fluid px-2">
    
    <x-breadcrumb :items="[
        ['name' => 'Reports', 'url' => '#']
    ]" />
    
    <x-page-header title="REPORTS DASHBOARD" description="View and export system reports">
    </x-page-header>
    
    <!-- Stats Cards (all dynamic) -->
    <div class="row g-2 mb-3">
        <x-stats-card value="{{ $reports->count() }}" label="Total Reports" subtext="available" />
        <x-stats-card value="{{ $reports->where('is_active', true)->count() }}" label="Active" subtext="ready to use" color="success" />
        <x-stats-card value="{{ $last30DaysCount }}" label="Last 30 days" subtext="generated" color="info" />
        <x-stats-card value="{{ $mostViewedName }}" label="Most viewed" subtext="report" color="primary" />
    </div>
    
    <!-- Two Column Layout: Reports Table (70%) + Help Card (30%) -->
    <div class="row g-2">
        
        <!-- LEFT COLUMN: Reports Table -->
        <div class="col-lg-8 col-md-7">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <i class="ti ti-chart-bar text-primary me-1" style="font-size: 0.8rem;"></i>
                            <span style="font-size: 0.7rem; font-weight: 600;">Available Reports</span>
                        </div>
                        <div class="text-muted" style="font-size: 0.6rem;">
                            Showing {{ $reports->count() }} reports
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0" style="font-size: 0.7rem;">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-2 py-1" width="50">#</th>
                                    <th class="py-1">Report Name</th>
                                    <th class="py-1">Description</th>
                                    <th class="py-1">Status</th>
                                    <th class="pe-2 py-1">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($reports as $index => $report)
                                <tr>
                                    <td class="ps-2 py-1 text-center">{{ $index + 1 }}</td>
                                    <td class="ps-2 py-1">
                                        <i class="ti ti-file-text text-primary me-1"></i>
                                        {{ $report->display_name }}
                                    </td>
                                    <td class="py-1">{{ \Illuminate\Support\Str::limit($report->description, 80) }}</td>
                                    <td class="py-1">
                                        @if($report->is_active)
                                            <span class="badge bg-success-subtle text-success rounded-sm">Active</span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary rounded-sm">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="pe-2 py-1">
                                        <a href="{{ route('reports.show', $report->name) }}" class="btn btn-outline-info btn-sm rounded-sm" style="padding: 0.1rem 0.3rem;" title="Run report">
                                            <i class="ti ti-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-3 text-muted">No reports found</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer py-1 px-2">
                    <small class="text-muted">Click the eye icon to run a report, apply filters, and export to PDF or Excel.</small>
                </div>
            </div>
        </div>
        
        <!-- RIGHT COLUMN: Help Card -->
        <div class="col-lg-4 col-md-5">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-info-circle text-info me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">How to Use Reports</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <div class="mb-2">
                        <div class="fw-semibold" style="font-size: 0.7rem;">📊 Running a Report</div>
                        <ol class="mb-0 ps-3" style="font-size: 0.65rem; line-height: 1.5;">
                            <li>Click the <i class="ti ti-eye"></i> <strong>eye icon</strong> next to any report.</li>
                            <li>Use the <strong>filters</strong> (date range, department, status, etc.) to refine data.</li>
                            <li>Click <strong>"Apply Filters"</strong> to refresh the report.</li>
                        </ol>
                    </div>
                    <div class="mb-2">
                        <div class="fw-semibold" style="font-size: 0.7rem;">📤 Export Options</div>
                        <ul class="mb-0 ps-3" style="font-size: 0.65rem; line-height: 1.5;">
                            <li><strong>PDF:</strong> Click the <span class="badge bg-danger">PDF</span> button to download a professional report.</li>
                            <li><strong>Excel:</strong> Click the <span class="badge bg-success">Excel</span> button to get data in XLSX format.</li>
                            <li>All exports respect the applied filters.</li>
                        </ul>
                    </div>
                    <div class="mb-2">
                        <div class="fw-semibold" style="font-size: 0.7rem;">📈 Charts & Tables</div>
                        <ul class="mb-0 ps-3" style="font-size: 0.65rem; line-height: 1.5;">
                            <li>Most reports include an interactive <strong>chart</strong> (bar, pie, line).</li>
                            <li>Below the chart, a <strong>detailed data table</strong> shows all rows.</li>
                        </ul>
                    </div>
                    <div class="alert alert-info py-1 px-2 mb-0 rounded-sm" style="font-size: 0.65rem;">
                        <i class="ti ti-lightbulb me-1"></i>
                        <strong>Tip:</strong> Use the calendar pickers for date‑driven reports (e.g., Leave Summary, Leave Taken).
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection