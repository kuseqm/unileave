@extends('layouts.master')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid px-2">
    
    <!-- Breadcrumb + Welcome Row -->
    <div class="row align-items-center mb-3">
        <div class="col">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0" style="font-size: 0.75rem;">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-muted">Home</a></li>
                    <li class="breadcrumb-item active">Dashboard</li>
                </ol>
            </nav>
        </div>
    
    </div>
    
    <!-- Stat Cards - Using x-stats-card component -->
    <div class="row g-2 mb-3">
        <x-stats-card value="{{ number_format($totalStaff ?? 156) }}" label="Total Staff" subtext="↑ +{{ number_format($newStaffThisMonth ?? 8) }} this month" icon="users" />
        <x-stats-card value="{{ number_format($pendingApprovalsCount ?? 12) }}" label="Pending" subtext="awaiting approval" color="warning" icon="clock" link="{{ route('approvals') }}" />
        <x-stats-card value="{{ number_format($onLeaveToday ?? 8) }}" label="On Leave" subtext="vs yesterday +2" color="success" icon="calendar" />
        <x-stats-card value="{{ number_format($leaveUsed ?? 342) }}" label="Days Used" subtext="↑ +{{ number_format($leaveIncrease ?? 23) }} this year" icon="chart-pie" />
        <x-stats-card value="{{ number_format($lowBalanceCount ?? 5) }}" label="Low Balance" subtext="below 5 days" color="danger" icon="alert-triangle" />
        <x-stats-card value="{{ number_format($upcomingLeave ?? 18) }}" label="Upcoming" subtext="next 7 days" color="info" icon="calendar-event" />
        <x-stats-card value="{{ number_format($totalDepartments ?? 6) }}" label="Depts" subtext="active" icon="building" />
        <x-stats-card value="{{ number_format($leaveTypesCount ?? 10) }}" label="Leave Types" subtext="policies" icon="tags" />
    </div>
    
    <!-- Main Content - Goldilocks Size -->
    <div class="row g-2">
        
        <!-- Left Column -->
        <div class="col-lg-7">
            <div class="card">
                <div class="card-header py-2 px-3 d-flex justify-content-between align-items-center">
                    <span style="font-size: 0.8rem; font-weight: 600;">Recent Leave Requests</span>
                    <a href="{{ route('approvals') }}" style="font-size: 0.7rem;">View all →</a>
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
                                @php $recentRequests = App\Models\LeaveRequest::with(['staff', 'leaveType'])->latest()->limit(5)->get(); @endphp
                                @forelse($recentRequests as $request)
                                <tr style="font-size: 0.75rem;">
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
                                <tr><td colspan="5" class="text-center py-3 text-muted">No leave requests found</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            @hasanyrole('admin|hr-officer')
            <div class="card mt-2">
                <div class="card-header py-2 px-3">
                    <span style="font-size: 0.8rem; font-weight: 600;">Department Leave Requests</span>
                </div>
                <div class="card-body p-0">
                    @forelse($departmentLeaveStats ?? [] as $dept)
                    <div class="d-flex justify-content-between align-items-center px-3 py-2" style="font-size: 0.75rem; border-bottom: 1px solid #E8E2D5;">
                        <span>{{ $dept->name }}</span>
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
            <div class="card">
                <div class="card-header py-2 px-3">
                    <span style="font-size: 0.8rem; font-weight: 600;">Quick Actions</span>
                </div>
                <div class="card-body p-2">
                    <div class="row g-1">
                        <div class="col-6">
                            <a href="{{ route('admin.staff.create') }}" class="btn btn-primary w-100 py-1" style="font-size: 0.7rem;">
                                <i class="ti ti-user-plus me-1"></i> Add Staff
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('admin.import.staff') }}" class="btn btn-outline-secondary w-100 py-1" style="font-size: 0.7rem;">
                                <i class="ti ti-file-import me-1"></i> Import
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('admin.blackout.index') }}" class="btn btn-outline-secondary w-100 py-1" style="font-size: 0.7rem;">
                                <i class="ti ti-ban me-1"></i> Blackout
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-secondary w-100 py-1" style="font-size: 0.7rem;">
                                <i class="ti ti-chart-bar me-1"></i> Reports
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            @hasanyrole('admin|hr-officer')
            <div class="card mt-2">
                <div class="card-header py-2 px-3">
                    <span style="font-size: 0.8rem; font-weight: 600;">⚠️ Low Balance Alerts</span>
                </div>
                <div class="card-body p-0">
                    @forelse($lowBalanceStaff ?? [] as $staff)
                    <div class="d-flex justify-content-between align-items-center px-3 py-2" style="font-size: 0.75rem; border-bottom: 1px solid #E8E2D5;">
                        <div>
                            <span class="fw-semibold">{{ $staff->first_name }} {{ $staff->last_name }}</span>
                            <div class="text-muted" style="font-size: 0.65rem;">{{ $staff->staff_id ?? '' }}</div>
                        </div>
                        <span class="text-danger fw-semibold" style="font-size: 0.75rem;">{{ $staff->leave_balance ?? 2 }} days left</span>
                    </div>
                    @empty
                    <div class="text-center py-3 text-muted" style="font-size: 0.75rem;">No low balance alerts</div>
                    @endforelse
                </div>
            </div>
            @endhasanyrole
            
            <div class="card mt-2">
                <div class="card-header py-2 px-3 d-flex justify-content-between align-items-center">
                    <span style="font-size: 0.8rem; font-weight: 600;">🚫 Active Blackouts</span>
                    <a href="{{ route('admin.blackout.index') }}" style="font-size: 0.7rem;">Manage →</a>
                </div>
                <div class="card-body p-0">
                    @forelse($blackoutPeriods ?? [] as $blackout)
                    <div class="d-flex justify-content-between align-items-center px-3 py-2" style="font-size: 0.75rem; border-bottom: 1px solid #E8E2D5;">
                        <div>
                            <span class="fw-semibold">{{ $blackout->name }}</span>
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
            
            @role('admin')
            <div class="card mt-2">
                <div class="card-header py-2 px-3">
                    <span style="font-size: 0.8rem; font-weight: 600;">✅ Recent Approvals</span>
                </div>
                <div class="card-body p-0">
                    @forelse($recentApprovals ?? [] as $approval)
                    <div class="d-flex justify-content-between align-items-center px-3 py-2" style="font-size: 0.75rem; border-bottom: 1px solid #E8E2D5;">
                        <div>
                            <span>{{ $approval->staff->first_name ?? 'Unknown' }} {{ $approval->staff->last_name ?? '' }}</span>
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
    
    @hasanyrole('admin|hr-officer')
    <div class="row g-2 mt-2">
        <div class="col-12">
            <div class="card">
                <div class="card-header py-2 px-3">
                    <span style="font-size: 0.8rem; font-weight: 600;">Leave Type Distribution</span>
                </div>
                <div class="card-body p-2">
                    <div class="row g-2">
                        @forelse($leaveTypeStats ?? [] as $type)
                        <div class="col-md-3 text-center py-2">
                            <div class="text-muted" style="font-size: 0.7rem;">{{ $type->name }}</div>
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
@endsection