@extends('layouts.master')

@section('title', 'Reports')

@section('content')
<div class="container-fluid px-2">
    
    <x-breadcrumb :items="[
        ['name' => 'Administration', 'url' => '#'],
        ['name' => 'Reports', 'url' => '#']
    ]" />
    
    <x-page-header title="REPORTS" description="Generate and view leave reports">
    </x-page-header>
    
    <div class="row g-2">
        <div class="col-md-4">
            <div class="card rounded-sm text-center">
                <div class="card-body py-3">
                    <i class="ti ti-file-text" style="font-size: 2rem; color: #3B82F6;"></i>
                    <h6 class="mt-2" style="font-size: 0.8rem;">Leave Summary</h6>
                    <p class="text-muted" style="font-size: 0.65rem;">View leave requests summary by department</p>
                    <a href="{{ route('reports.leave-summary') }}" class="btn btn-primary btn-sm rounded-sm mt-2">Generate</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card rounded-sm text-center">
                <div class="card-body py-3">
                    <i class="ti ti-users" style="font-size: 2rem; color: #10B981;"></i>
                    <h6 class="mt-2" style="font-size: 0.8rem;">Staff Report</h6>
                    <p class="text-muted" style="font-size: 0.65rem;">Staff list with leave balances</p>
                    <a href="{{ route('reports.staff') }}" class="btn btn-primary btn-sm rounded-sm mt-2">Generate</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card rounded-sm text-center">
                <div class="card-body py-3">
                    <i class="ti ti-calendar" style="font-size: 2rem; color: #F59E0B;"></i>
                    <h6 class="mt-2" style="font-size: 0.8rem;">Leave Balances</h6>
                    <p class="text-muted" style="font-size: 0.65rem;">Staff leave balance report</p>
                    <a href="{{ route('reports.leave-balances') }}" class="btn btn-primary btn-sm rounded-sm mt-2">Generate</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection