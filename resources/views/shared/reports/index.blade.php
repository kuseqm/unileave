@extends('layouts.master')

@section('title', 'Reports')

@section('content')
<div class="container-fluid px-2">
    
    <x-breadcrumb :items="[
        ['name' => 'Dashboard', 'url' => route('dashboard')],
        ['name' => 'Reports', 'url' => '#']
    ]" />
    
    <x-page-header title="REPORTS" description="View leave reports based on your role">
    </x-page-header>
    
    @if($role == 'admin' || $role == 'hr-officer' || $role == 'hr-manager')
    <!-- Full Reports for Admin/HR -->
    <div class="row g-2">
        <div class="col-md-3">
            <div class="card rounded-sm text-center">
                <div class="card-body py-3">
                    <i class="ti ti-file-text" style="font-size: 1.5rem; color: #3B82F6;"></i>
                    <h6 class="mt-2" style="font-size: 0.75rem;">Leave Summary</h6>
                    <p class="text-muted" style="font-size: 0.6rem;">Department leave summary</p>
                    <a href="{{ route('reports.leave-summary') }}" class="btn btn-primary btn-sm rounded-sm mt-2">View</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card rounded-sm text-center">
                <div class="card-body py-3">
                    <i class="ti ti-users" style="font-size: 1.5rem; color: #10B981;"></i>
                    <h6 class="mt-2" style="font-size: 0.75rem;">Staff Directory</h6>
                    <p class="text-muted" style="font-size: 0.6rem;">Complete staff list</p>
                    <a href="{{ route('reports.staff') }}" class="btn btn-primary btn-sm rounded-sm mt-2">View</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card rounded-sm text-center">
                <div class="card-body py-3">
                    <i class="ti ti-calendar" style="font-size: 1.5rem; color: #F59E0B;"></i>
                    <h6 class="mt-2" style="font-size: 0.75rem;">Leave Balances</h6>
                    <p class="text-muted" style="font-size: 0.6rem;">Staff leave balances</p>
                    <a href="{{ route('reports.leave-balances') }}" class="btn btn-primary btn-sm rounded-sm mt-2">View</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card rounded-sm text-center">
                <div class="card-body py-3">
                    <i class="ti ti-clock" style="font-size: 1.5rem; color: #EF4444;"></i>
                    <h6 class="mt-2" style="font-size: 0.75rem;">Pending Approvals</h6>
                    <p class="text-muted" style="font-size: 0.6rem;">Requests awaiting approval</p>
                    <a href="{{ route('reports.pending-approvals') }}" class="btn btn-primary btn-sm rounded-sm mt-2">View</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card rounded-sm text-center">
                <div class="card-body py-3">
                    <i class="ti ti-calendar-check" style="font-size: 1.5rem; color: #8B5CF6;"></i>
                    <h6 class="mt-2" style="font-size: 0.75rem;">Leave Taken</h6>
                    <p class="text-muted" style="font-size: 0.6rem;">Leave days used</p>
                    <a href="{{ route('reports.leave-taken') }}" class="btn btn-primary btn-sm rounded-sm mt-2">View</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card rounded-sm text-center">
                <div class="card-body py-3">
                    <i class="ti ti-calendar-event" style="font-size: 1.5rem; color: #EC4899;"></i>
                    <h6 class="mt-2" style="font-size: 0.75rem;">Leave Forecast</h6>
                    <p class="text-muted" style="font-size: 0.6rem;">Upcoming leave</p>
                    <a href="{{ route('reports.leave-forecast') }}" class="btn btn-primary btn-sm rounded-sm mt-2">View</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card rounded-sm text-center">
                <div class="card-body py-3">
                    <i class="ti ti-alert-triangle" style="font-size: 1.5rem; color: #F97316;"></i>
                    <h6 class="mt-2" style="font-size: 0.75rem;">Low Balance Alerts</h6>
                    <p class="text-muted" style="font-size: 0.6rem;">Staff with &lt;5 days</p>
                    <a href="{{ route('reports.low-balance-alerts') }}" class="btn btn-primary btn-sm rounded-sm mt-2">View</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card rounded-sm text-center">
                <div class="card-body py-3">
                    <i class="ti ti-alert-circle" style="font-size: 1.5rem; color: #DC2626;"></i>
                    <h6 class="mt-2" style="font-size: 0.75rem;">Exceeded Leave</h6>
                    <p class="text-muted" style="font-size: 0.6rem;">Overdue leave</p>
                    <a href="{{ route('reports.exceeded-leave') }}" class="btn btn-primary btn-sm rounded-sm mt-2">View</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card rounded-sm text-center">
                <div class="card-body py-3">
                    <i class="ti ti-chart-line" style="font-size: 1.5rem; color: #06B6D4;"></i>
                    <h6 class="mt-2" style="font-size: 0.75rem;">Approval Time</h6>
                    <p class="text-muted" style="font-size: 0.6rem;">Performance metrics</p>
                    <a href="{{ route('reports.approval-time') }}" class="btn btn-primary btn-sm rounded-sm mt-2">View</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card rounded-sm text-center">
                <div class="card-body py-3">
                    <i class="ti ti-pie-chart" style="font-size: 1.5rem; color: #14B8A6;"></i>
                    <h6 class="mt-2" style="font-size: 0.75rem;">Leave Type Distribution</h6>
                    <p class="text-muted" style="font-size: 0.6rem;">Leave usage stats</p>
                    <a href="{{ route('reports.leave-type-distribution') }}" class="btn btn-primary btn-sm rounded-sm mt-2">View</a>
                </div>
            </div>
        </div>
    </div>
    
    @elseif($role == 'hod' || $role == 'dean')
    <!-- Limited Reports for HOD/Dean -->
    <div class="row g-2">
        <div class="col-md-4">
            <div class="card rounded-sm text-center">
                <div class="card-body py-3">
                    <i class="ti ti-file-text" style="font-size: 1.5rem; color: #3B82F6;"></i>
                    <h6 class="mt-2" style="font-size: 0.75rem;">Leave Summary</h6>
                    <p class="text-muted" style="font-size: 0.6rem;">Department/School summary</p>
                    <a href="{{ route('reports.leave-summary') }}" class="btn btn-primary btn-sm rounded-sm mt-2">View</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card rounded-sm text-center">
                <div class="card-body py-3">
                    <i class="ti ti-users" style="font-size: 1.5rem; color: #10B981;"></i>
                    <h6 class="mt-2" style="font-size: 0.75rem;">Staff Directory</h6>
                    <p class="text-muted" style="font-size: 0.6rem;">Staff list</p>
                    <a href="{{ route('reports.staff') }}" class="btn btn-primary btn-sm rounded-sm mt-2">View</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card rounded-sm text-center">
                <div class="card-body py-3">
                    <i class="ti ti-calendar" style="font-size: 1.5rem; color: #F59E0B;"></i>
                    <h6 class="mt-2" style="font-size: 0.75rem;">Leave Balances</h6>
                    <p class="text-muted" style="font-size: 0.6rem;">Staff balances</p>
                    <a href="{{ route('reports.leave-balances') }}" class="btn btn-primary btn-sm rounded-sm mt-2">View</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card rounded-sm text-center">
                <div class="card-body py-3">
                    <i class="ti ti-clock" style="font-size: 1.5rem; color: #EF4444;"></i>
                    <h6 class="mt-2" style="font-size: 0.75rem;">Pending Approvals</h6>
                    <p class="text-muted" style="font-size: 0.6rem;">Awaiting your decision</p>
                    <a href="{{ route('reports.pending-approvals') }}" class="btn btn-primary btn-sm rounded-sm mt-2">View</a>
                </div>
            </div>
        </div>
    </div>
    
    @elseif($role == 'dvca' || $role == 'dvc-pfa')
    <!-- Limited Reports for DVC -->
    <div class="row g-2">
        <div class="col-md-4">
            <div class="card rounded-sm text-center">
                <div class="card-body py-3">
                    <i class="ti ti-file-text" style="font-size: 1.5rem; color: #3B82F6;"></i>
                    <h6 class="mt-2" style="font-size: 0.75rem;">Leave Summary</h6>
                    <p class="text-muted" style="font-size: 0.6rem;">Academic/Admin summary</p>
                    <a href="{{ route('reports.leave-summary') }}" class="btn btn-primary btn-sm rounded-sm mt-2">View</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card rounded-sm text-center">
                <div class="card-body py-3">
                    <i class="ti ti-users" style="font-size: 1.5rem; color: #10B981;"></i>
                    <h6 class="mt-2" style="font-size: 0.75rem;">Staff Directory</h6>
                    <p class="text-muted" style="font-size: 0.6rem;">Staff list</p>
                    <a href="{{ route('reports.staff') }}" class="btn btn-primary btn-sm rounded-sm mt-2">View</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card rounded-sm text-center">
                <div class="card-body py-3">
                    <i class="ti ti-clock" style="font-size: 1.5rem; color: #EF4444;"></i>
                    <h6 class="mt-2" style="font-size: 0.75rem;">Pending Approvals</h6>
                    <p class="text-muted" style="font-size: 0.6rem;">Awaiting your decision</p>
                    <a href="{{ route('reports.pending-approvals') }}" class="btn btn-primary btn-sm rounded-sm mt-2">View</a>
                </div>
            </div>
        </div>
    </div>
    
    @elseif($role == 'staff')
    <!-- Basic Reports for Staff -->
    <div class="row g-2">
        <div class="col-md-4">
            <div class="card rounded-sm text-center">
                <div class="card-body py-3">
                    <i class="ti ti-file-text" style="font-size: 1.5rem; color: #3B82F6;"></i>
                    <h6 class="mt-2" style="font-size: 0.75rem;">My Leave Summary</h6>
                    <p class="text-muted" style="font-size: 0.6rem;">Your leave requests</p>
                    <a href="{{ route('reports.leave-summary') }}" class="btn btn-primary btn-sm rounded-sm mt-2">View</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card rounded-sm text-center">
                <div class="card-body py-3">
                    <i class="ti ti-calendar" style="font-size: 1.5rem; color: #F59E0B;"></i>
                    <h6 class="mt-2" style="font-size: 0.75rem;">My Leave Balance</h6>
                    <p class="text-muted" style="font-size: 0.6rem;">Your leave balance</p>
                    <a href="{{ route('reports.leave-balances') }}" class="btn btn-primary btn-sm rounded-sm mt-2">View</a>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection