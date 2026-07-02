@extends('layouts.master')

@section('title', 'Vice Chancellor Dashboard')

@section('content')
<div class="container-fluid px-2">
    
    <!-- Breadcrumb -->
    <x-breadcrumb :items="[
        ['name' => 'VC Dashboard', 'url' => '#']
    ]" />
    
    <!-- Page Header -->
    <x-page-header title="VICE CHANCELLOR DASHBOARD" description="University Leave Management Overview">
        <div>
            <button type="button" onclick="window.print();" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
                <i class="ti ti-printer me-1"></i> Print
            </button>
            <a href="{{ route('reports.index') }}" class="btn btn-outline-primary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
                <i class="ti ti-chart-bar me-1"></i> Full Reports
            </a>
        </div>
    </x-page-header>
    
    <!-- Filter Bar -->
    <div class="card rounded-sm mb-3">
        <div class="card-header py-1 px-2 bg-light">
            <div class="d-flex align-items-center">
                <i class="ti ti-filter text-primary me-1"></i>
                <span style="font-weight: 600; font-size: 0.7rem;">Filters</span>
            </div>
        </div>
        <div class="card-body py-2 px-2">
            <form method="GET" action="{{ route('vc.dashboard') }}" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label" style="font-size: 0.6rem;">Date Range</label>
                    <select name="date_range" class="form-select form-select-sm">
                        <option value="all" {{ ($currentFilters['date_range'] ?? 'this_year') == 'all' ? 'selected' : '' }}>All Time</option>
                        <option value="this_year" {{ ($currentFilters['date_range'] ?? 'this_year') == 'this_year' ? 'selected' : '' }}>This Year</option>
                        <option value="last_year" {{ ($currentFilters['date_range'] ?? '') == 'last_year' ? 'selected' : '' }}>Last Year</option>
                        <option value="this_month" {{ ($currentFilters['date_range'] ?? '') == 'this_month' ? 'selected' : '' }}>This Month</option>
                        <option value="last_month" {{ ($currentFilters['date_range'] ?? '') == 'last_month' ? 'selected' : '' }}>Last Month</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label" style="font-size: 0.6rem;">Department</label>
                    <select name="department_id" class="form-select form-select-sm">
                        <option value="">All Departments</option>
                        @foreach($departments ?? [] as $dept)
                        <option value="{{ $dept->id }}" {{ ($currentFilters['department_id'] ?? '') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label" style="font-size: 0.6rem;">Leave Type</label>
                    <select name="leave_type_id" class="form-select form-select-sm">
                        <option value="">All Leave Types</option>
                        @foreach($leaveTypes ?? [] as $type)
                        <option value="{{ $type->id }}" {{ ($currentFilters['leave_type_id'] ?? '') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <div class="d-flex gap-1">
                        <button type="submit" class="btn btn-primary btn-sm w-100">Apply Filters</button>
                        <a href="{{ route('vc.dashboard') }}" class="btn btn-secondary btn-sm w-100">Reset</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Export Buttons -->
    <div class="d-flex justify-content-end gap-1 mb-2">
        <a href="{{ route('vc.reports.excel', request()->query()) }}" class="btn btn-success btn-sm rounded-sm" style="font-size: 0.7rem;">
            <i class="ti ti-file-spreadsheet me-1"></i> Export Excel
        </a>
        <a href="{{ route('vc.reports.pdf', request()->query()) }}" class="btn btn-danger btn-sm rounded-sm" style="font-size: 0.7rem;">
            <i class="ti ti-file-text me-1"></i> Export PDF
        </a>
    </div>
    
    <!-- Stats Cards -->
    <div class="row g-2 mb-3">
        <div class="col-md-3">
            <div class="card rounded-sm text-center h-100">
                <div class="card-body py-2">
                    <div class="text-primary fw-bold" style="font-size: 1.5rem;">{{ number_format($totalRequests ?? 0) }}</div>
                    <div class="text-muted" style="font-size: 0.65rem;">Total Leave Requests</div>
                    <div class="small text-muted">Filtered period</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card rounded-sm text-center h-100">
                <div class="card-body py-2">
                    <div class="text-success fw-bold" style="font-size: 1.5rem;">{{ $activeOnLeave ?? 0 }}</div>
                    <div class="text-muted" style="font-size: 0.65rem;">Active Staff on Leave</div>
                    <div class="small text-muted">Currently away</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card rounded-sm text-center h-100">
                <div class="card-body py-2">
                    <div class="text-warning fw-bold" style="font-size: 1.5rem;">{{ $pendingApprovals ?? 0 }}</div>
                    <div class="text-muted" style="font-size: 0.65rem;">Pending Approvals</div>
                    <div class="small text-muted">Awaiting decision</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card rounded-sm text-center h-100">
                <div class="card-body py-2">
                    <div class="text-info fw-bold" style="font-size: 1.5rem;">{{ $avgApprovalTime ?? 0 }}</div>
                    <div class="text-muted" style="font-size: 0.65rem;">Avg Approval Time</div>
                    <div class="small text-muted">Days to finalize</div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row g-2">
        <!-- Left Column -->
        <div class="col-lg-8">
            
            <!-- Pending Approvals by Level Card -->
            <div class="card rounded-sm mb-2">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-clock text-warning me-1"></i>
                        <span style="font-weight: 600; font-size: 0.7rem;">Pending Approvals by Level</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <div class="row text-center">
                        <div class="col">
                            <div class="fw-bold" style="font-size: 1.2rem;">{{ $pendingByLevel['hod'] ?? 0 }}</div>
                            <div class="text-muted" style="font-size: 0.6rem;">HOD</div>
                            <div class="progress mt-1" style="height: 4px;">
                                <div class="progress-bar bg-primary" style="width: {{ ($pendingApprovals ?? 0) > 0 ? (($pendingByLevel['hod'] ?? 0) / ($pendingApprovals ?? 1)) * 100 : 0 }}%"></div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="fw-bold" style="font-size: 1.2rem;">{{ $pendingByLevel['dean'] ?? 0 }}</div>
                            <div class="text-muted" style="font-size: 0.6rem;">Dean</div>
                            <div class="progress mt-1" style="height: 4px;">
                                <div class="progress-bar bg-info" style="width: {{ ($pendingApprovals ?? 0) > 0 ? (($pendingByLevel['dean'] ?? 0) / ($pendingApprovals ?? 1)) * 100 : 0 }}%"></div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="fw-bold" style="font-size: 1.2rem;">{{ $pendingByLevel['hr'] ?? 0 }}</div>
                            <div class="text-muted" style="font-size: 0.6rem;">HR</div>
                            <div class="progress mt-1" style="height: 4px;">
                                <div class="progress-bar bg-warning" style="width: {{ ($pendingApprovals ?? 0) > 0 ? (($pendingByLevel['hr'] ?? 0) / ($pendingApprovals ?? 1)) * 100 : 0 }}%"></div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="fw-bold" style="font-size: 1.2rem;">{{ $pendingByLevel['dvc'] ?? 0 }}</div>
                            <div class="text-muted" style="font-size: 0.6rem;">DVC</div>
                            <div class="progress mt-1" style="height: 4px;">
                                <div class="progress-bar bg-danger" style="width: {{ ($pendingApprovals ?? 0) > 0 ? (($pendingByLevel['dvc'] ?? 0) / ($pendingApprovals ?? 1)) * 100 : 0 }}%"></div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="fw-bold" style="font-size: 1.2rem;">{{ $pendingByLevel['hr_final'] ?? 0 }}</div>
                            <div class="text-muted" style="font-size: 0.6rem;">HR Final</div>
                            <div class="progress mt-1" style="height: 4px;">
                                <div class="progress-bar bg-success" style="width: {{ ($pendingApprovals ?? 0) > 0 ? (($pendingByLevel['hr_final'] ?? 0) / ($pendingApprovals ?? 1)) * 100 : 0 }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Monthly Trends Chart -->
            <div class="card rounded-sm mb-2">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-chart-line text-primary me-1"></i>
                        <span style="font-weight: 600; font-size: 0.7rem;">Leave Trends (Last 12 Months)</span>
                    </div>
                </div>
                <div class="card-body p-2">
                    <canvas id="trendsChart" height="200" style="width: 100%;"></canvas>
                </div>
            </div>
            
            <!-- Department Comparison -->
            <div class="card rounded-sm mb-2">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-building text-primary me-1"></i>
                        <span style="font-weight: 600; font-size: 0.7rem;">Department Comparison ({{ $currentYear ?? date('Y') }} vs {{ $previousYear ?? date('Y')-1 }})</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0" style="font-size: 0.7rem;">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-2 py-1">Department</th>
                                    <th class="py-1 text-center">{{ $currentYear ?? date('Y') }}</th>
                                    <th class="py-1 text-center">{{ $previousYear ?? date('Y')-1 }}</th>
                                    <th class="pe-2 py-1 text-center">Change</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($departmentCompare ?? [] as $dept)
                                <tr>
                                    <td class="ps-2 py-1">{{ $dept['name'] ?? 'N/A' }}</td>
                                    <td class="py-1 text-center">{{ $dept['current'] ?? 0 }}</td>
                                    <td class="py-1 text-center">{{ $dept['previous'] ?? 0 }}</td>
                                    <td class="pe-2 py-1 text-center">
                                        @if(($dept['change'] ?? 0) > 0)
                                            <span class="text-danger">+{{ $dept['change'] }}%</span>
                                        @elseif(($dept['change'] ?? 0) < 0)
                                            <span class="text-success">{{ $dept['change'] }}%</span>
                                        @else
                                            <span class="text-muted">0%</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-2 text-muted">No department data available</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Monthly Heatmap -->
            <div class="card rounded-sm mb-2">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-calendar text-primary me-1"></i>
                        <span style="font-weight: 600; font-size: 0.7rem;">Monthly Distribution ({{ $currentYear ?? date('Y') }})</span>
                    </div>
                </div>
                <div class="card-body p-2">
                    <canvas id="heatmapChart" height="150" style="width: 100%;"></canvas>
                </div>
            </div>
            
            <!-- Recent Approvals -->
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-history text-primary me-1"></i>
                        <span style="font-weight: 600; font-size: 0.7rem;">Recent Approvals</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0" style="font-size: 0.7rem;">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-2 py-1">Staff</th>
                                    <th class="py-1">Leave Type</th>
                                    <th class="py-1">Period</th>
                                    <th class="py-1 text-center">Days</th>
                                    <th class="pe-2 py-1 text-center">Approved</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentApprovals ?? [] as $approval)
                                <tr>
                                    <td class="ps-2 py-1">{{ $approval->staff->full_name ?? 'N/A' }}</td>
                                    <td class="py-1">{{ $approval->leaveType->name ?? 'N/A' }}</td>
                                    <td class="py-1">{{ \Carbon\Carbon::parse($approval->start_date)->format('d M') }} - {{ \Carbon\Carbon::parse($approval->end_date)->format('d M Y') }}</td>
                                    <td class="py-1 text-center">{{ $approval->days }}</td>
                                    <td class="pe-2 py-1 text-center">{{ $approval->updated_at->format('d M Y') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-2 text-muted">No recent approvals</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Right Column -->
        <div class="col-lg-4">
            
            <!-- Staff Category Distribution -->
            <div class="card rounded-sm mb-2">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-users text-primary me-1"></i>
                        <span style="font-weight: 600; font-size: 0.7rem;">Leave by Staff Category</span>
                    </div>
                </div>
                <div class="card-body p-2">
                    <canvas id="categoryChart" height="150" style="width: 100%;"></canvas>
                    <div class="row text-center mt-2">
                        <div class="col-4">
                            <div class="fw-bold text-primary">{{ number_format($categoryStats['academic'] ?? 0) }}</div>
                            <div class="text-muted" style="font-size: 0.55rem;">Academic</div>
                        </div>
                        <div class="col-4">
                            <div class="fw-bold text-info">{{ number_format($categoryStats['administrative'] ?? 0) }}</div>
                            <div class="text-muted" style="font-size: 0.55rem;">Administrative</div>
                        </div>
                        <div class="col-4">
                            <div class="fw-bold text-secondary">{{ number_format($categoryStats['supporting'] ?? 0) }}</div>
                            <div class="text-muted" style="font-size: 0.55rem;">Supporting</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Payment Status Summary -->
            <div class="card rounded-sm mb-2">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-currency-dollar text-primary me-1"></i>
                        <span style="font-weight: 600; font-size: 0.7rem;">Payment Status Summary</span>
                    </div>
                </div>
                <div class="card-body p-2">
                    <canvas id="paymentChart" height="150" style="width: 100%;"></canvas>
                    <div class="row text-center mt-2">
                        <div class="col-4">
                            <div class="fw-bold text-success">{{ $paymentStatsPercentage['paid'] ?? 0 }}%</div>
                            <div class="text-muted" style="font-size: 0.55rem;">Paid</div>
                            <div class="small text-muted">{{ number_format($paymentStats['paid'] ?? 0) }} requests</div>
                        </div>
                        <div class="col-4">
                            <div class="fw-bold text-warning">{{ $paymentStatsPercentage['partial'] ?? 0 }}%</div>
                            <div class="text-muted" style="font-size: 0.55rem;">Partial</div>
                            <div class="small text-muted">{{ number_format($paymentStats['partial'] ?? 0) }} requests</div>
                        </div>
                        <div class="col-4">
                            <div class="fw-bold text-danger">{{ $paymentStatsPercentage['unpaid'] ?? 0 }}%</div>
                            <div class="text-muted" style="font-size: 0.55rem;">Unpaid</div>
                            <div class="small text-muted">{{ number_format($paymentStats['unpaid'] ?? 0) }} requests</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Top Leave Types -->
            <div class="card rounded-sm mb-2">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-tags text-primary me-1"></i>
                        <span style="font-weight: 600; font-size: 0.7rem;">Top Leave Types</span>
                    </div>
                </div>
                <div class="card-body p-2">
                    @foreach($topLeaveTypes ?? [] as $type)
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span style="font-size: 0.7rem;">{{ $type->name }}</span>
                        <div class="d-flex align-items-center gap-2">
                            <div class="progress" style="width: 100px; height: 4px;">
                                <div class="progress-bar bg-primary" style="width: {{ ($type->leave_requests_count > 0 && ($topLeaveTypes->first()->leave_requests_count ?? 1) > 0) ? min(100, ($type->leave_requests_count / $topLeaveTypes->first()->leave_requests_count) * 100) : 0 }}%"></div>
                            </div>
                            <span class="text-muted" style="font-size: 0.65rem;">{{ $type->leave_requests_count }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            
            <!-- Alerts & Action Items -->
            <div class="card rounded-sm mb-2">
                <div class="card-header py-1 px-2 bg-danger-subtle">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-alert-triangle text-danger me-1"></i>
                        <span style="font-weight: 600; font-size: 0.7rem; color: #DC2626;">Alerts & Action Items</span>
                    </div>
                </div>
                <div class="card-body p-2">
                    @forelse($alerts ?? [] as $alert)
                    <div class="alert alert-{{ $alert['type'] }} py-1 px-2 mb-2 rounded-sm" style="font-size: 0.65rem;">
                        <i class="{{ $alert['icon'] }} me-1"></i>
                        <strong>{{ $alert['title'] }}</strong>
                        <div class="mt-1">{{ $alert['message'] }}</div>
                        @if(isset($alert['action_url']) && $alert['action_url'] != '#')
                        <div class="mt-1">
                            <a href="{{ $alert['action_url'] }}" class="small">{{ $alert['action_text'] ?? 'View' }} →</a>
                        </div>
                        @endif
                    </div>
                    @empty
                    <div class="text-center text-muted py-2" style="font-size: 0.65rem;">
                        <i class="ti ti-check-circle me-1"></i>
                        No alerts at this time. All metrics are within normal range.
                    </div>
                    @endforelse
                </div>
            </div>
            
            <!-- Monthly Projection -->
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-calendar text-primary me-1"></i>
                        <span style="font-weight: 600; font-size: 0.7rem;">Leave Projections</span>
                    </div>
                </div>
                <div class="card-body p-2">
                    @foreach($projection ?? [] as $proj)
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span style="font-size: 0.7rem;">{{ $proj['month'] }}</span>
                        <div class="d-flex align-items-center gap-2">
                            <span class="fw-semibold" style="font-size: 0.7rem;">{{ number_format($proj['projected']) }}</span>
                            <span class="badge bg-{{ $proj['confidence'] == 'high' ? 'success' : 'secondary' }}-subtle text-{{ $proj['confidence'] == 'high' ? 'success' : 'secondary' }} rounded-pill" style="font-size: 0.55rem;">
                                {{ ucfirst($proj['confidence']) }}
                            </span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Monthly Trends Chart
    const ctx = document.getElementById('trendsChart')?.getContext('2d');
    if (ctx && @json($monthlyTrends ?? []).length > 0) {
        const trends = @json($monthlyTrends);
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: trends.map(t => t.month),
                datasets: [
                    {
                        label: 'Total Requests',
                        data: trends.map(t => t.total),
                        borderColor: '#3B82F6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        fill: true,
                        tension: 0.3
                    },
                    {
                        label: 'Approved',
                        data: trends.map(t => t.approved),
                        borderColor: '#10B981',
                        backgroundColor: 'transparent',
                        tension: 0.3
                    },
                    {
                        label: 'Rejected',
                        data: trends.map(t => t.rejected),
                        borderColor: '#EF4444',
                        backgroundColor: 'transparent',
                        tension: 0.3
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { position: 'bottom', labels: { font: { size: 10 } } }
                },
                scales: { y: { beginAtZero: true, grid: { color: '#E8E2D5' } } }
            }
        });
    }
    
    // Monthly Heatmap
    const heatmapCtx = document.getElementById('heatmapChart')?.getContext('2d');
    if (heatmapCtx && @json($monthlyHeatmap ?? []).length > 0) {
        const heatmapData = @json($monthlyHeatmap);
        const maxCount = Math.max(...heatmapData.map(d => d.count), 1);
        
        new Chart(heatmapCtx, {
            type: 'bar',
            data: {
                labels: heatmapData.map(d => d.month),
                datasets: [{
                    label: 'Leave Requests',
                    data: heatmapData.map(d => d.count),
                    backgroundColor: heatmapData.map(d => {
                        const intensity = d.count / maxCount;
                        if (intensity > 0.7) return '#EF4444';
                        if (intensity > 0.4) return '#F59E0B';
                        return '#10B981';
                    }),
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, title: { display: true, text: 'Requests', font: { size: 10 } } } }
            }
        });
    }
    
    // Category Chart (Pie)
    const categoryCtx = document.getElementById('categoryChart')?.getContext('2d');
    if (categoryCtx) {
        new Chart(categoryCtx, {
            type: 'doughnut',
            data: {
                labels: ['Academic', 'Administrative', 'Supporting'],
                datasets: [{
                    data: [{{ $categoryStats['academic'] ?? 0 }}, {{ $categoryStats['administrative'] ?? 0 }}, {{ $categoryStats['supporting'] ?? 0 }}],
                    backgroundColor: ['#3B82F6', '#10B981', '#94A3B8'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { position: 'bottom', labels: { font: { size: 9 } } }
                }
            }
        });
    }
    
    // Payment Chart
    const paymentCtx = document.getElementById('paymentChart')?.getContext('2d');
    if (paymentCtx) {
        new Chart(paymentCtx, {
            type: 'doughnut',
            data: {
                labels: ['Paid', 'Partial', 'Unpaid'],
                datasets: [{
                    data: [{{ $paymentStats['paid'] ?? 0 }}, {{ $paymentStats['partial'] ?? 0 }}, {{ $paymentStats['unpaid'] ?? 0 }}],
                    backgroundColor: ['#10B981', '#F59E0B', '#EF4444'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { position: 'bottom', labels: { font: { size: 9 } } }
                }
            }
        });
    }
</script>

<style media="print">
    .sidebar-area, .topbar, .page-header .btn, .btn, .breadcrumb, .sticky-banner {
        display: none !important;
    }
    .card-header {
        background: #f8f9fa !important;
        print-color-adjust: exact;
    }
</style>
@endsection