@extends('layouts.master')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid px-2">
    
    <!-- Welcome Header -->
    <div class="row mb-2">
        <div class="col-12">
            <h1 class="h4 mb-1" style="color: #1E293B;">Welcome back, {{ Auth::user()->first_name }}!</h1>
            <p class="text-muted" style="font-size: 0.7rem;">{{ now()->format('l, F j, Y') }}</p>
        </div>
    </div>
    
    <!-- STATS CARDS (Role-Based using x-stats-card) -->
    <div class="row g-2 mb-3">
        @role('staff')
            <x-stats-card value="{{ $myTotalRequests ?? 0 }}" label="My Requests" subtext="all time" icon="list" />
            <x-stats-card value="{{ $myPendingRequests ?? 0 }}" label="Pending" subtext="awaiting approval" color="warning" icon="clock" />
            <x-stats-card value="{{ $myApprovedRequests ?? 0 }}" label="Approved" subtext="granted" color="success" icon="check" />
            <x-stats-card value="{{ $myLeaveBalance ?? 0 }}" label="Leave Balance" subtext="days remaining" color="info" icon="calendar" />
        @endrole
        
        @hasanyrole('hod|dean|dvca|dvc-pfa')
            <x-stats-card value="{{ $pendingApprovalsCount ?? 0 }}" label="Pending Approvals" subtext="awaiting your decision" color="warning" icon="clock" />
            <x-stats-card value="{{ $myDepartmentRequests ?? 0 }}" label="Department Requests" subtext="this month" icon="building" />
            <x-stats-card value="{{ $approvedCount ?? 0 }}" label="Approved" subtext="this month" color="success" icon="check" />
            <x-stats-card value="{{ $rejectedCount ?? 0 }}" label="Rejected" subtext="this month" color="danger" icon="x" />
        @endhasanyrole
        
        @role('hr-officer')
            <x-stats-card value="{{ $pendingLogistics ?? 0 }}" label="Pending Logistics" subtext="awaiting processing" color="warning" icon="settings" />
            <x-stats-card value="{{ $processedThisMonth ?? 0 }}" label="Processed" subtext="this month" color="success" icon="device-floppy" />
            <x-stats-card value="{{ $totalStaff ?? 0 }}" label="Total Staff" subtext="active" icon="users" />
            <x-stats-card value="{{ $onLeaveToday ?? 0 }}" label="On Leave Today" subtext="currently absent" color="info" icon="calendar" />
        @endrole
        
        @role('admin')
            <x-stats-card value="{{ $totalStaff ?? 0 }}" label="Total Staff" subtext="active" icon="users" />
            <x-stats-card value="{{ $pendingApprovalsCount ?? 0 }}" label="Pending Approvals" subtext="system-wide" color="warning" icon="clock" />
            <x-stats-card value="{{ $onLeaveToday ?? 0 }}" label="On Leave Today" subtext="currently absent" color="info" icon="calendar" />
            <x-stats-card value="{{ $leaveTypesCount ?? 0 }}" label="Leave Types" subtext="active policies" color="success" icon="tags" />
        @endrole
    </div>
    
    <!-- ACTING STAFF REQUESTS -->
    @if(isset($actingRequests) && $actingRequests->count() > 0)
    <div class="card rounded-sm mb-2">
        <div class="card-header py-1 px-2 bg-light">
            <div class="d-flex align-items-center">
                <i class="ti ti-user-check text-warning me-1" style="font-size: 0.8rem;"></i>
                <span style="font-size: 0.7rem; font-weight: 600;">Action Required: Hold Position Requests</span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm mb-0" style="font-size: 0.75rem;">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-2 py-1">From</th>
                            <th class="py-1">Leave Type</th>
                            <th class="py-1">Dates</th>
                            <th class="py-1">Days</th>
                            <th class="pe-2 py-1">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($actingRequests as $request)
                        <tr>
                            <td class="ps-2 py-1">
                                <strong>{{ $request->staff->first_name }} {{ $request->staff->last_name }}</strong>
                                <div class="text-muted" style="font-size: 0.6rem;">{{ $request->staff->staff_id }}</div>
                            </td>
                            <td class="py-1">{{ $request->leaveType->name ?? 'N/A' }}</td>
                            <td class="py-1">{{ \Carbon\Carbon::parse($request->start_date)->format('d M Y') }} - {{ \Carbon\Carbon::parse($request->end_date)->format('d M Y') }}</td>
                            <td class="py-1">{{ $request->days }}</td>
                            <td class="pe-2 py-1">
                                <a href="{{ route('leave-requests.acting.response', $request->acting_token) }}" class="btn btn-sm btn-warning rounded-sm" style="font-size: 0.65rem; padding: 0.15rem 0.3rem;">
                                    <i class="ti ti-user-check me-1"></i> Respond
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
    
    <!-- PENDING APPROVALS -->
    @if(isset($pendingApprovals) && $pendingApprovals->count() > 0)
    <div class="card rounded-sm mb-2">
        <div class="card-header py-1 px-2 bg-light d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <i class="ti ti-clock text-warning me-1" style="font-size: 0.8rem;"></i>
                <span style="font-size: 0.7rem; font-weight: 600;">Pending Approvals</span>
            </div>
            <a href="{{ route('leave-requests.pending') }}" class="small" style="font-size: 0.65rem;">View all →</a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm mb-0" style="font-size: 0.75rem;">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-2 py-1">Staff</th>
                            <th class="py-1">Leave Type</th>
                            <th class="py-1">Dates</th>
                            <th class="py-1">Days</th>
                            <th class="py-1">Acting Staff</th>
                            <th class="pe-2 py-1">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendingApprovals->take(5) as $request)
                        <tr>
                            <td class="ps-2 py-1">
                                <strong>{{ $request->staff->first_name }} {{ $request->staff->last_name }}</strong>
                                <div class="text-muted" style="font-size: 0.6rem;">{{ $request->staff->staff_id }}</div>
                            </td>
                            <td class="py-1">{{ $request->leaveType->name ?? 'N/A' }}</td>
                            <td class="py-1">{{ \Carbon\Carbon::parse($request->start_date)->format('d M Y') }} - {{ \Carbon\Carbon::parse($request->end_date)->format('d M Y') }}</td>
                            <td class="py-1">{{ $request->days }}</td>
                            <td class="py-1">
                                @if($request->actingStaff)
                                    {{ $request->actingStaff->first_name }} {{ $request->actingStaff->last_name }}
                                    <span class="badge bg-success-subtle text-success rounded-sm" style="font-size: 0.55rem;">Accepted</span>
                                @endif
                            </td>
                            <td class="pe-2 py-1">
                                <a href="{{ route('leave-requests.pending') }}" class="btn btn-sm btn-primary rounded-sm" style="font-size: 0.65rem; padding: 0.15rem 0.3rem;">
                                    <i class="ti ti-check me-1"></i> Review
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
    
    <!-- MY RECENT REQUESTS -->
    @hasanyrole('staff|admin')
    <div class="card rounded-sm">
        <div class="card-header py-1 px-2 bg-light d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <i class="ti ti-calendar text-primary me-1" style="font-size: 0.8rem;"></i>
                <span style="font-size: 0.7rem; font-weight: 600;">My Recent Leave Requests</span>
            </div>
            <a href="{{ route('leave-requests.my-requests') }}" class="small" style="font-size: 0.65rem;">View all →</a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm mb-0" style="font-size: 0.75rem;">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-2 py-1">Leave Type</th>
                            <th class="py-1">Dates</th>
                            <th class="py-1">Days</th>
                            <th class="py-1">Acting Staff</th>
                            <th class="py-1">Status</th>
                            <th class="pe-2 py-1">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($myRecentRequests ?? [] as $request)
                        <tr>
                            <td class="ps-2 py-1">{{ $request->leaveType->name ?? 'N/A' }}</td>
                            <td class="py-1">{{ \Carbon\Carbon::parse($request->start_date)->format('d M Y') }} - {{ \Carbon\Carbon::parse($request->end_date)->format('d M Y') }}</td>
                            <td class="py-1">{{ $request->days }}</td>
                            <td class="py-1">
                                @if($request->actingStaff)
                                    {{ $request->actingStaff->first_name }} {{ $request->actingStaff->last_name }}
                                    <span class="badge bg-success-subtle text-success rounded-sm" style="font-size: 0.55rem;">Accepted</span>
                                @endif
                            </td>
                            <td class="py-1">
                                @if($request->status == 'pending')
                                    <span class="badge bg-warning-subtle text-warning rounded-sm" style="font-size: 0.6rem;">Pending</span>
                                @elseif($request->status == 'approved')
                                    <span class="badge bg-success-subtle text-success rounded-sm" style="font-size: 0.6rem;">Approved</span>
                                @elseif($request->status == 'rejected')
                                    <span class="badge bg-danger-subtle text-danger rounded-sm" style="font-size: 0.6rem;">Rejected</span>
                                @endif
                            </td>
                            <td class="pe-2 py-1">
                                <a href="{{ route('leave-requests.show', $request->id) }}" class="btn btn-sm btn-outline-info rounded-sm" style="padding: 0.15rem 0.3rem;">
                                    <i class="ti ti-eye" style="font-size: 0.7rem;"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-2 text-muted" style="font-size: 0.7rem;">No leave requests found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endhasanyrole
    
    <!-- QUICK ACTIONS -->
    <div class="row g-2 mt-2">
        @role('staff')
        <div class="col-md-3">
            <div class="card rounded-sm text-center">
                <div class="card-body py-2 px-2">
                    <a href="{{ route('leave-requests.create') }}" class="text-decoration-none">
                        <i class="ti ti-calendar-plus text-primary" style="font-size: 1.2rem;"></i>
                        <div class="small mt-1">Apply for Leave</div>
                    </a>
                </div>
            </div>
        </div>
        @endrole
        
        @hasanyrole('hod|dean|dvca|dvc-pfa')
        <div class="col-md-3">
            <div class="card rounded-sm text-center">
                <div class="card-body py-2 px-2">
                    <a href="{{ route('leave-requests.pending') }}" class="text-decoration-none">
                        <i class="ti ti-clock text-warning" style="font-size: 1.2rem;"></i>
                        <div class="small mt-1">Pending Approvals</div>
                    </a>
                </div>
            </div>
        </div>
        @endhasanyrole
        
        @role('admin|hr-officer')
        <div class="col-md-3">
            <div class="card rounded-sm text-center">
                <div class="card-body py-2 px-2">
                    <a href="{{ route('reports.index') }}" class="text-decoration-none">
                        <i class="ti ti-chart-bar text-success" style="font-size: 1.2rem;"></i>
                        <div class="small mt-1">Leave Reports</div>
                    </a>
                </div>
            </div>
        </div>
        @endrole
        
        <div class="col-md-3">
            <div class="card rounded-sm text-center">
                <div class="card-body py-2 px-2">
                    <a href="{{ route('profile.show') }}" class="text-decoration-none">
                        <i class="ti ti-user text-info" style="font-size: 1.2rem;"></i>
                        <div class="small mt-1">My Profile</div>
                    </a>
                </div>
            </div>
        </div>
    </div>

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

    // Function to highlight changed element
    function highlightElement(elementId) {
        const element = document.getElementById(elementId);
        if (element) {
            element.classList.add('stat-highlight');
            setTimeout(() => {
                element.classList.remove('stat-highlight');
            }, 1000);
        }
    }

    // Function to update stats via AJAX
    function refreshDashboardStats() {
        fetch('{{ route("dashboard.stats") }}')
            .then(response => response.json())
            .then(data => {
                if (data.pendingApprovalsCount !== undefined && currentStats.pendingApprovalsCount !== data.pendingApprovalsCount) {
                    const element = document.getElementById('stat-pending');
                    if (element) {
                        element.innerText = data.pendingApprovalsCount;
                        highlightElement('stat-pending');
                    }
                    currentStats.pendingApprovalsCount = data.pendingApprovalsCount;
                }
                
                if (data.totalStaff !== undefined && currentStats.totalStaff !== data.totalStaff) {
                    const element = document.getElementById('stat-total-staff');
                    if (element) {
                        element.innerText = data.totalStaff.toLocaleString();
                        highlightElement('stat-total-staff');
                    }
                    currentStats.totalStaff = data.totalStaff;
                }
                
                if (data.onLeaveToday !== undefined && currentStats.onLeaveToday !== data.onLeaveToday) {
                    const element = document.getElementById('stat-on-leave');
                    if (element) {
                        element.innerText = data.onLeaveToday;
                        highlightElement('stat-on-leave');
                    }
                    currentStats.onLeaveToday = data.onLeaveToday;
                }
                
                if (data.upcomingLeave !== undefined && currentStats.upcomingLeave !== data.upcomingLeave) {
                    const element = document.getElementById('stat-upcoming');
                    if (element) {
                        element.innerText = data.upcomingLeave;
                        highlightElement('stat-upcoming');
                    }
                    currentStats.upcomingLeave = data.upcomingLeave;
                }
                
                if (data.leaveUsed !== undefined && currentStats.leaveUsed !== data.leaveUsed) {
                    const element = document.getElementById('stat-leave-used');
                    if (element) {
                        element.innerText = data.leaveUsed.toLocaleString();
                        highlightElement('stat-leave-used');
                    }
                    currentStats.leaveUsed = data.leaveUsed;
                }
                
                if (data.lowBalanceCount !== undefined && currentStats.lowBalanceCount !== data.lowBalanceCount) {
                    const element = document.getElementById('stat-low-balance');
                    if (element) {
                        element.innerText = data.lowBalanceCount;
                        highlightElement('stat-low-balance');
                    }
                    currentStats.lowBalanceCount = data.lowBalanceCount;
                }
            })
            .catch(error => console.error('Error refreshing stats:', error));
    }

    // Auto-refresh every 30 seconds
    let refreshInterval = setInterval(refreshDashboardStats, 30000);

    // Stop refresh when page is hidden
    document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
            clearInterval(refreshInterval);
        } else {
            refreshInterval = setInterval(refreshDashboardStats, 30000);
            refreshDashboardStats();
        }
    });
    </script>

    <style>
    .stat-highlight {
        animation: statFlash 0.5s ease-in-out;
    }
    @keyframes statFlash {
        0% { background-color: transparent; transform: scale(1); }
        50% { background-color: rgba(59, 130, 246, 0.2); transform: scale(1.05); }
        100% { background-color: transparent; transform: scale(1); }
    }
    </style>

</div>

<script>
function processLogistics(id) {
    confirmAction('Process this leave request? This will update the staff\'s leave balance.', function () {
        fetch(`/admin/leave-requests/${id}/final-approve`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json'
            }
        }).then(response => response.json()).then(data => {
            if(data.success) {
                location.reload();
            } else {
                alert('Failed to process request');
            }
        });
    }, { danger: false, confirmText: 'Process' });
}
</script>
@endsection