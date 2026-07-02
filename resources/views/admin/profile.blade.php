@extends('layouts.master')

@section('title', 'My Profile')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-4">
            <!-- Profile Card -->
            <div class="card">
                <div class="card-body text-center">
                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 120px; height: 120px; font-size: 3rem; font-weight: 600;">
                        {{ strtoupper(substr(Auth::user()->first_name, 0, 1)) }}{{ strtoupper(substr(Auth::user()->last_name, 0, 1)) }}
                    </div>
                    <h4 class="mb-1">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</h4>
                    <p class="text-muted">{{ Auth::user()->staff_id }}</p>
                    <p class="text-muted mb-3">{{ Auth::user()->email }}</p>
                    <div class="d-flex justify-content-center gap-2 flex-wrap">
                        <span class="badge bg-primary">{{ ucfirst(Auth::user()->staff_category) }}</span>
                        <span class="badge bg-info">{{ ucfirst(Auth::user()->getRoleNames()->first() ?? 'Staff') }}</span>
                        @if(Auth::user()->status == 'active')
                            <span class="badge bg-success">Active</span>
                        @elseif(Auth::user()->status == 'inactive')
                            <span class="badge bg-warning">Inactive</span>
                        @else
                            <span class="badge bg-danger">Suspended</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Contact Card -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="ti ti-phone me-2"></i> Contact Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="text-muted small text-uppercase">Phone Number</label>
                        <p class="fw-semibold mb-0">{{ Auth::user()->phone ?? 'Not provided' }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small text-uppercase">Email Address</label>
                        <p class="fw-semibold mb-0">{{ Auth::user()->email }}</p>
                    </div>
                    <div>
                        <label class="text-muted small text-uppercase">Joined Date</label>
                        <p class="fw-semibold mb-0">{{ \Carbon\Carbon::parse(Auth::user()->joining_date)->format('d F Y') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <!-- Employment Details Card -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="ti ti-briefcase me-2"></i> Employment Details
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="text-muted small text-uppercase">Staff ID</label>
                                <p class="fw-semibold mb-0">{{ Auth::user()->staff_id }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="text-muted small text-uppercase">University</label>
                                <p class="fw-semibold mb-0">{{ Auth::user()->university->name ?? 'N/A' }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="text-muted small text-uppercase">Campus</label>
                                <p class="fw-semibold mb-0">{{ Auth::user()->campus->name ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="text-muted small text-uppercase">School/Faculty</label>
                                <p class="fw-semibold mb-0">{{ Auth::user()->school->name ?? 'N/A' }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="text-muted small text-uppercase">Department</label>
                                <p class="fw-semibold mb-0">{{ Auth::user()->department->name ?? 'N/A' }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="text-muted small text-uppercase">Staff Category</label>
                                <p class="fw-semibold mb-0">{{ ucfirst(Auth::user()->staff_category) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Leave Statistics Card -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="ti ti-chart-bar me-2"></i> Leave Statistics
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-4">
                            <div class="stat-circle mx-auto mb-2">
                                <i class="ti ti-calendar-check text-primary" style="font-size: 2rem;"></i>
                            </div>
                            <h3 class="text-primary mb-0">{{ $totalLeaveTaken ?? 0 }}</h3>
                            <small class="text-muted">Days Taken</small>
                        </div>
                        <div class="col-4">
                            <div class="stat-circle mx-auto mb-2">
                                <i class="ti ti-calendar-stats text-success" style="font-size: 2rem;"></i>
                            </div>
                            <h3 class="text-success mb-0">{{ $remainingLeave ?? 28 }}</h3>
                            <small class="text-muted">Days Remaining</small>
                        </div>
                        <div class="col-4">
                            <div class="stat-circle mx-auto mb-2">
                                <i class="ti ti-clock text-warning" style="font-size: 2rem;"></i>
                            </div>
                            <h3 class="text-warning mb-0">{{ $pendingLeave ?? 0 }}</h3>
                            <small class="text-muted">Pending Requests</small>
                        </div>
                    </div>

                    @if(($totalLeaveTaken ?? 0) > 0)
                    <div class="progress mt-3" style="height: 8px;">
                        @php
                            $total = ($totalLeaveTaken ?? 0) + ($remainingLeave ?? 28);
                            $percentage = $total > 0 ? round(($totalLeaveTaken / $total) * 100) : 0;
                        @endphp
                        <div class="progress-bar bg-primary" style="width: {{ $percentage }}%"></div>
                    </div>
                    <div class="text-center mt-2">
                        <small class="text-muted">{{ $percentage }}% of annual leave used</small>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Recent Leave Requests Card -->
            <div class="card mt-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="ti ti-history me-2"></i> Recent Leave Requests
                    </h5>
                    <a href="#" class="btn btn-sm btn-link">View All</a>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($recentLeaveRequests ?? [] as $request)
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-semibold">{{ $request->leaveType->name ?? 'Leave Request' }}</div>
                                    <small class="text-muted">
                                        {{ \Carbon\Carbon::parse($request->start_date)->format('d M Y') }} -
                                        {{ \Carbon\Carbon::parse($request->end_date)->format('d M Y') }}
                                        ({{ $request->days }} days)
                                    </small>
                                </div>
                                <div>
                                    @if($request->status == 'approved')
                                        <span class="badge bg-success">Approved</span>
                                    @elseif($request->status == 'pending')
                                        <span class="badge bg-warning">Pending</span>
                                    @elseif($request->status == 'rejected')
                                        <span class="badge bg-danger">Rejected</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="list-group-item text-center text-muted py-4">
                            No recent leave requests
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.stat-circle {
    width: 50px;
    height: 50px;
    background: var(--bg-light);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>
@endsection
