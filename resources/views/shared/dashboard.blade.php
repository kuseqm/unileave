@extends('layouts.master')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid px-2">
    
    <!-- Breadcrumb + Welcome Row -->
    <div class="row align-items-center mb-3">
        <div class="col">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0" style="font-size: 0.75rem;">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-muted">Home</a></li>
                    <li class="breadcrumb-item active">Dashboard</li>
                </ol>
            </nav>
        </div>
    </div>
    
    <!-- Stat Cards - Using x-stats-card component with clickable links -->
    <div class="row g-2 mb-3">
        <x-stats-card id="stat-total-staff" value="{{ number_format($totalStaff ?? 0) }}" label="Total Staff" subtext="↑ +{{ number_format($newStaffThisMonth ?? 0) }} this month" icon="users" link="{{ $statsLinks['total_staff'] ?? null }}" />
        
        <x-stats-card id="stat-pending" value="{{ number_format($pendingApprovalsCount ?? 0) }}" label="Pending" subtext="awaiting approval" color="warning" icon="clock" link="{{ $statsLinks['pending_approvals'] ?? route('approvals') }}" />
        
        <x-stats-card id="stat-on-leave" value="{{ number_format($onLeaveToday ?? 0) }}" label="On Leave" subtext="currently away" color="success" icon="calendar" link="{{ $statsLinks['on_leave'] ?? null }}" />
        
        <x-stats-card id="stat-leave-used" value="{{ number_format($leaveUsed ?? 0) }}" label="Days Used" subtext="this year" icon="chart-pie" link="{{ $statsLinks['leave_used'] ?? null }}" />
        
        <x-stats-card id="stat-low-balance" value="{{ number_format($lowBalanceCount ?? 0) }}" label="Low Balance" subtext="below 5 days" color="danger" icon="alert-triangle" link="{{ $statsLinks['low_balance'] ?? null }}" />
        
        <x-stats-card id="stat-upcoming" value="{{ number_format($upcomingLeave ?? 0) }}" label="Upcoming" subtext="next 7 days" color="info" icon="calendar-event" link="{{ $statsLinks['upcoming_leave'] ?? null }}" />
        
        <x-stats-card value="{{ number_format($totalDepartments ?? 0) }}" label="Depts" subtext="active" icon="building" link="{{ $statsLinks['total_departments'] ?? null }}" />
        
        <x-stats-card value="{{ number_format($leaveTypesCount ?? 0) }}" label="Leave Types" subtext="policies" icon="tags" link="{{ $statsLinks['leave_types'] ?? null }}" />
    </div>
    
    <!-- Main Content -->
    <div class="row g-2">
        
        <!-- Left Column -->
        <div class="col-lg-7">
            <!-- Recent Leave Requests Table - Clickable Rows -->
            <div class="card">
                <div class="card-header py-2 px-3 d-flex justify-content-between align-items-center">
                    <span style="font-size: 0.8rem; font-weight: 600;">Recent Leave Requests</span>
                    <div>
                        <a href="{{ route('leave-requests.status') }}" style="font-size: 0.7rem; margin-right: 0.5rem;">
                            <i class="ti ti-list me-1"></i>View All Status
                        </a>
                        <a href="{{ route('approvals') }}" style="font-size: 0.7rem;">View all →</a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0" style="font-size: 0.75rem;">
                            <thead>
                                <tr class="small">
                                    <th class="ps-3 fw-semibold">Staff</th>
                                    <th class="fw-semibold">Leave Type</th>
                                    <th class="fw-semibold">Duration</th>
                                    <th class="fw-semibold">Days</th>
                                    <th class="pe-3 fw-semibold">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $user = auth()->user();
                                    $role = $user->getRoleNames()->first();
                                    
                                    $recentQuery = App\Models\LeaveRequest::with(['staff', 'leaveType']);
                                    
                                    switch ($role) {
                                        case 'staff':
                                            $recentQuery->where('staff_id', $user->id);
                                            break;
                                        case 'unit_head':
                                            $unitHeadDept = $user->unitHeadDepartment;
                                            if ($unitHeadDept) {
                                                $recentQuery->whereHas('staff', function($q) use ($unitHeadDept) {
                                                    $q->where('department_id', $unitHeadDept->id);
                                                });
                                            } else {
                                                $recentQuery->whereRaw('1 = 0');
                                            }
                                            break;
                                        case 'hod':
                                            $recentQuery->whereHas('staff.department', function($q) use ($user) {
                                                $q->where('hod_staff_id', $user->id);
                                            });
                                            break;
                                        case 'dean':
                                            $recentQuery->whereHas('staff.department.school', function($q) use ($user) {
                                                $q->where('dean_staff_id', $user->id);
                                            });
                                            break;
                                        case 'dvca':
                                            $recentQuery->whereHas('staff', function($q) {
                                                $q->where('staff_category', 'academic');
                                            });
                                            break;
                                        case 'dvc-pfa':
                                            $recentQuery->whereHas('staff', function($q) {
                                                $q->where('staff_category', 'administrative');
                                            });
                                            break;
                                        case 'hr-officer':
                                        case 'hr-manager':
                                        case 'admin':
                                            break;
                                        default:
                                            $recentQuery->where('staff_id', $user->id);
                                            break;
                                    }
                                    
                                    $recentRequests = $recentQuery->latest()->limit(5)->get();
                                @endphp
                                
                                @forelse($recentRequests as $request)
                                <tr style="font-size: 0.75rem; cursor: pointer;" onclick="window.location='{{ route('leave-requests.show', $request->id) }}'" class="clickable-row">
                                    <td class="ps-3">
                                        <strong>{{ $request->staff->first_name }} {{ $request->staff->last_name }}</strong>
                                        <div class="text-muted" style="font-size: 0.65rem;">{{ $request->staff->staff_id ?? '' }}</div>
                                    </td>
                                    <td>{{ $request->leaveType->name ?? 'N/A' }}</td>
                                    <td>{{ \Carbon\Carbon::parse($request->start_date)->format('M d') }} - {{ \Carbon\Carbon::parse($request->end_date)->format('M d') }}</td>
                                    <td>{{ $request->days }}</td>
                                    <td class="pe-3">
                                        @if($request->status == 'pending')
                                            <span class="badge bg-warning-subtle text-warning px-2 py-1 rounded-pill" style="font-size: 0.65rem;">Pending</span>
                                        @elseif($request->status == 'approved')
                                            <span class="badge bg-success-subtle text-success px-2 py-1 rounded-pill" style="font-size: 0.65rem;">Approved</span>
                                        @else
                                            <span class="badge bg-danger-subtle text-danger px-2 py-1 rounded-pill" style="font-size: 0.65rem;">Rejected</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-3 text-muted">No leave requests found</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Department Leave Requests Card - Clickable Rows -->
            @hasanyrole('admin|hr-officer')
            <div class="card mt-2">
                <div class="card-header py-2 px-3 d-flex justify-content-between align-items-center">
                    <span style="font-size: 0.8rem; font-weight: 600;">Department Leave Requests</span>
                    <a href="{{ route('admin.departments.index') }}" style="font-size: 0.7rem;">Manage Departments →</a>
                </div>
                <div class="card-body p-0">
                    @forelse($departmentLeaveStats ?? [] as $dept)
                    <div class="d-flex justify-content-between align-items-center px-3 py-2" style="font-size: 0.75rem; border-bottom: 1px solid #E8E2D5; cursor: pointer;" onclick="window.location='{{ route('staff.index', ['department_id' => $dept->id]) }}'">
                        <span class="hover-link">{{ $dept->name }}</span>
                        <div class="d-flex align-items-center gap-3">
                            <span class="text-muted" style="font-size: 0.7rem;">{{ $dept->request_count ?? 0 }} requests</span>
                            <div class="progress" style="width: 100px; height: 4px;">
                                <div class="progress-bar bg-primary" style="width: {{ $dept->percentage ?? 0 }}%"></div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-3 text-muted" style="font-size: 0.75rem;">No department data available</div>
                    @endforelse
                </div>
            </div>
            @endhasanyrole
        </div>
        
        <!-- Right Column -->
        <div class="col-lg-5">
            <!-- Quick Actions Card -->
            <div class="card">
                <div class="card-header py-2 px-3">
                    <span style="font-size: 0.8rem; font-weight: 600;">Quick Actions</span>
                </div>
                <div class="card-body p-2">
                    <div class="row g-1">
                        <div class="col-6">
                            <a href="{{ route('staff.create') }}" class="btn btn-primary w-100 py-1" style="font-size: 0.7rem;">
                                <i class="ti ti-user-plus me-1"></i> Add Staff
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('admin.import.staff') }}" class="btn btn-outline-secondary w-100 py-1" style="font-size: 0.7rem;">
                                <i class="ti ti-file-import me-1"></i> Import
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('blackout.index') }}" class="btn btn-outline-secondary w-100 py-1" style="font-size: 0.7rem;">
                                <i class="ti ti-ban me-1"></i> Blackout
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary w-100 py-1" style="font-size: 0.7rem;">
                                <i class="ti ti-chart-bar me-1"></i> Reports
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Low Balance Alerts - Clickable Staff Names -->
            @hasanyrole('admin|hr-officer')
            <div class="card mt-2">
                <div class="card-header py-2 px-3 d-flex justify-content-between align-items-center">
                    <span style="font-size: 0.8rem; font-weight: 600;">⚠️ Low Balance Alerts</span>
                    <a href="{{ route('staff.index', ['filter' => 'low_balance']) }}" style="font-size: 0.7rem;">View All →</a>
                </div>
                <div class="card-body p-0">
                    @forelse($lowBalanceStaff ?? [] as $staff)
                    <div class="d-flex justify-content-between align-items-center px-3 py-2" style="font-size: 0.75rem; border-bottom: 1px solid #E8E2D5; cursor: pointer;" onclick="window.location='{{ route('staff.show', $staff->id) }}'">
                        <div>
                            <span class="fw-semibold hover-link">{{ $staff->first_name }} {{ $staff->last_name }}</span>
                            <div class="text-muted" style="font-size: 0.65rem;">{{ $staff->staff_id ?? '' }}</div>
                        </div>
                        <span class="text-danger fw-semibold" style="font-size: 0.75rem;">
                            @php
                                $balance = $staff->leaveBalances->where('leave_type_id', 1)->first();
                                echo ($balance ? $balance->balance : 0) . ' days left';
                            @endphp
                        </span>
                    </div>
                    @empty
                    <div class="text-center py-3 text-muted" style="font-size: 0.75rem;">No low balance alerts</div>
                    @endforelse
                </div>
            </div>
            @endhasanyrole
            
            <!-- Unresponsive Acting Staff Widget -->
            @hasanyrole('hod|hr-officer|admin')
            @php
                $unresponsiveCount = App\Models\LeaveRequest::where('acting_status', 'pending')
                    ->where('created_at', '<', now()->subHours(48))
                    ->count();
            @endphp
            @if($unresponsiveCount > 0)
            <div class="card mt-2 border-danger">
                <div class="card-header py-2 px-3 bg-danger-subtle border-danger">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-alert-circle text-danger me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.75rem; font-weight: 600;">⚠️ Unresponsive Acting Staff</span>
                        <span class="badge bg-danger ms-2" style="font-size: 0.6rem;">{{ $unresponsiveCount }}</span>
                    </div>
                </div>
                <div class="card-body p-2">
                    <div class="alert alert-danger py-1 px-2 mb-0 rounded-sm" style="font-size: 0.65rem; cursor: pointer;" onclick="window.location='{{ route('leave-requests.pending') }}'">
                        <i class="ti ti-clock me-1"></i>
                        <strong>{{ $unresponsiveCount }}</strong> leave request(s) have been waiting for acting staff response for over 48 hours.
                        <div class="mt-1">
                            <span class="text-danger fw-semibold" style="font-size: 0.65rem;">
                                Click to review pending requests →
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            @endhasanyrole
            
            <!-- Active Blackouts - Clickable Rows -->
            <div class="card mt-2">
                <div class="card-header py-2 px-3 d-flex justify-content-between align-items-center">
                    <span style="font-size: 0.8rem; font-weight: 600;">🚫 Active Blackouts</span>
                    <a href="{{ route('blackout.index') }}" style="font-size: 0.7rem;">Manage →</a>
                </div>
                <div class="card-body p-0">
                    @forelse($blackoutPeriods ?? [] as $blackout)
                    <div class="d-flex justify-content-between align-items-center px-3 py-2" style="font-size: 0.75rem; border-bottom: 1px solid #E8E2D5; cursor: pointer;" onclick="window.location='{{ route('blackout.show', $blackout->id) }}'">
                        <div>
                            <span class="fw-semibold hover-link">{{ $blackout->name }}</span>
                            <div class="text-muted" style="font-size: 0.65rem;">
                                {{ \Carbon\Carbon::parse($blackout->start_date)->format('M d') }} - {{ \Carbon\Carbon::parse($blackout->end_date)->format('M d, Y') }}
                            </div>
                        </div>
                        <span class="badge {{ $blackout->restriction_type == 'hard' ? 'bg-danger-subtle text-danger' : 'bg-warning-subtle text-warning' }} px-2 py-1 rounded-pill" style="font-size: 0.65rem;">{{ $blackout->restriction_type == 'hard' ? 'Hard Block' : 'Soft Warning' }}</span>
                    </div>
                    @empty
                    <div class="text-center py-3 text-muted" style="font-size: 0.75rem;">No active blackout periods</div>
                    @endforelse
                </div>
            </div>
            
            <!-- Recent Approvals - Clickable Rows -->
            @role('admin')
            <div class="card mt-2">
                <div class="card-header py-2 px-3 d-flex justify-content-between align-items-center">
                    <span style="font-size: 0.8rem; font-weight: 600;">✅ Recent Approvals</span>
                    <a href="{{ route('leave-requests.status', ['filter' => 'approved']) }}" style="font-size: 0.7rem;">View All →</a>
                </div>
                <div class="card-body p-0">
                    @forelse($recentApprovals ?? [] as $approval)
                    <div class="d-flex justify-content-between align-items-center px-3 py-2" style="font-size: 0.75rem; border-bottom: 1px solid #E8E2D5; cursor: pointer;" onclick="window.location='{{ route('leave-requests.show', $approval->id) }}'">
                        <div>
                            <span class="hover-link">{{ $approval->staff->first_name ?? 'Unknown' }} {{ $approval->staff->last_name ?? '' }}</span>
                            <div class="text-muted" style="font-size: 0.65rem;">{{ $approval->leaveType->name ?? 'Leave Request' }}</div>
                        </div>
                        <span class="badge {{ $approval->status == 'approved' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} px-2 py-1 rounded-pill" style="font-size: 0.65rem;">{{ ucfirst($approval->status ?? 'Pending') }}</span>
                    </div>
                    @empty
                    <div class="text-center py-3 text-muted" style="font-size: 0.75rem;">No recent activity</div>
                    @endforelse
                </div>
            </div>
            @endrole
        </div>
    </div>
    
    <!-- Leave Type Distribution - Clickable Rows -->
    @hasanyrole('admin|hr-officer')
    <div class="row g-2 mt-2">
        <div class="col-12">
            <div class="card">
                <div class="card-header py-2 px-3 d-flex justify-content-between align-items-center">
                    <span style="font-size: 0.8rem; font-weight: 600;">Leave Type Distribution</span>
                    <a href="{{ route('reports.index') }}" style="font-size: 0.7rem;">View Reports →</a>
                </div>
                <div class="card-body p-2">
                    <div class="row g-2">
                        @forelse($leaveTypeStats ?? [] as $type)
                        <div class="col-md-3 text-center py-2" style="cursor: pointer;" onclick="window.location='{{ route('leave-requests.status', ['leave_type' => $type->id]) }}'">
                            <div class="text-muted" style="font-size: 0.7rem;" class="hover-link">{{ $type->name }}</div>
                            <div class="fw-bold" style="font-size: 1rem;">{{ $type->request_count ?? 0 }}</div>
                            <div class="text-muted" style="font-size: 0.65rem;">requests</div>
                            <div class="progress mx-auto mt-1" style="width: 80%; height: 3px;">
                                <div class="progress-bar bg-primary" style="width: {{ $type->percentage ?? 0 }}%"></div>
                            </div>
                        </div>
                        @empty
                        <div class="col-12 text-center py-3 text-muted" style="font-size: 0.75rem;">No leave type data available</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endhasanyrole
</div>

<style>
/* Hover effect for clickable rows */
.clickable-row:hover {
    background-color: #F8FAFC !important;
    transition: background 0.2s ease;
}

.hover-link:hover {
    text-decoration: underline;
    color: #3B82F6 !important;
}

.d-flex.justify-content-between.align-items-center.px-3.py-2:hover {
    background-color: #F8FAFC;
}

.col-md-3.text-center.py-2:hover {
    background-color: #F8FAFC;
    border-radius: 8px;
    transform: translateY(-2px);
    transition: all 0.2s ease;
}

.stat-highlight {
    animation: statFlash 0.5s ease-in-out;
}

@keyframes statFlash {
    0% { background-color: transparent; transform: scale(1); }
    50% { background-color: rgba(59, 130, 246, 0.2); transform: scale(1.05); }
    100% { background-color: transparent; transform: scale(1); }
}
</style>

<!-- Real-time Stats Auto-Refresh -->
<script>
// Store current values to detect changes
let currentStats = {
    pendingApprovalsCount: {{ $pendingApprovalsCount ?? 0 }},
    totalStaff: {{ $totalStaff ?? 0 }},
    onLeaveToday: {{ $onLeaveToday ?? 0 }},
    upcomingLeave: {{ $upcomingLeave ?? 0 }},
    leaveUsed: {{ $leaveUsed ?? 0 }},
    lowBalanceCount: {{ $lowBalanceCount ?? 0 }}
};

function highlightElement(elementId) {
    const element = document.getElementById(elementId);
    if (element) {
        element.classList.add('stat-highlight');
        setTimeout(() => {
            element.classList.remove('stat-highlight');
        }, 1000);
    }
}

function refreshDashboardStats() {
    fetch('{{ route("dashboard.stats") }}')
        .then(response => response.json())
        .then(data => {
            if (data.pendingApprovalsCount !== undefined && currentStats.pendingApprovalsCount !== data.pendingApprovalsCount) {
                const element = document.getElementById('stat-pending');
                if (element) {
                    const valueSpan = element.querySelector('.fw-bold');
                    if (valueSpan) valueSpan.innerText = data.pendingApprovalsCount;
                    highlightElement('stat-pending');
                }
                currentStats.pendingApprovalsCount = data.pendingApprovalsCount;
            }
            
            if (data.totalStaff !== undefined && currentStats.totalStaff !== data.totalStaff) {
                const element = document.getElementById('stat-total-staff');
                if (element) {
                    const valueSpan = element.querySelector('.fw-bold');
                    if (valueSpan) valueSpan.innerText = data.totalStaff.toLocaleString();
                    highlightElement('stat-total-staff');
                }
                currentStats.totalStaff = data.totalStaff;
            }
            
            if (data.onLeaveToday !== undefined && currentStats.onLeaveToday !== data.onLeaveToday) {
                const element = document.getElementById('stat-on-leave');
                if (element) {
                    const valueSpan = element.querySelector('.fw-bold');
                    if (valueSpan) valueSpan.innerText = data.onLeaveToday;
                    highlightElement('stat-on-leave');
                }
                currentStats.onLeaveToday = data.onLeaveToday;
            }
            
            if (data.upcomingLeave !== undefined && currentStats.upcomingLeave !== data.upcomingLeave) {
                const element = document.getElementById('stat-upcoming');
                if (element) {
                    const valueSpan = element.querySelector('.fw-bold');
                    if (valueSpan) valueSpan.innerText = data.upcomingLeave;
                    highlightElement('stat-upcoming');
                }
                currentStats.upcomingLeave = data.upcomingLeave;
            }
            
            if (data.leaveUsed !== undefined && currentStats.leaveUsed !== data.leaveUsed) {
                const element = document.getElementById('stat-leave-used');
                if (element) {
                    const valueSpan = element.querySelector('.fw-bold');
                    if (valueSpan) valueSpan.innerText = data.leaveUsed.toLocaleString();
                    highlightElement('stat-leave-used');
                }
                currentStats.leaveUsed = data.leaveUsed;
            }
            
            if (data.lowBalanceCount !== undefined && currentStats.lowBalanceCount !== data.lowBalanceCount) {
                const element = document.getElementById('stat-low-balance');
                if (element) {
                    const valueSpan = element.querySelector('.fw-bold');
                    if (valueSpan) valueSpan.innerText = data.lowBalanceCount;
                    highlightElement('stat-low-balance');
                }
                currentStats.lowBalanceCount = data.lowBalanceCount;
            }
        })
        .catch(error => console.error('Error refreshing stats:', error));
}

let refreshInterval = setInterval(refreshDashboardStats, 30000);

document.addEventListener('visibilitychange', function() {
    if (document.hidden) {
        clearInterval(refreshInterval);
    } else {
        refreshInterval = setInterval(refreshDashboardStats, 30000);
        refreshDashboardStats();
    }
});
</script>
@endsection