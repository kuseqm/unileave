@extends('layouts.master')

@section('title', 'Leave Policies')

@section('content')
<div class="container-fluid px-2">
    
    <x-breadcrumb :items="[
        ['name' => 'Settings', 'url' => '#'],
        ['name' => 'Leave Policies', 'url' => '#']
    ]" />
    
    <x-page-header title="LEAVE POLICIES" description="Configure leave policy rules">
    </x-page-header>
    
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show py-1 px-2 mb-2 rounded-sm" role="alert">
        <i class="ti ti-check-circle me-1"></i> {{ session('success') }}
        <button type="button" class="btn-close py-0" data-bs-dismiss="alert"></button>
    </div>
    @endif
    
    <form method="POST" action="{{ route('settings.hr.update') }}">
        @csrf
        @method('PUT')
        
        <!-- Leave Limits -->
        <div class="card rounded-sm mb-3">
            <div class="card-header py-1 px-2 bg-light">
                <i class="ti ti-calendar me-1"></i>
                <span style="font-weight: 600;">Leave Limits</span>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Annual Leave (Days/Year)</label>
                        <input type="number" class="form-control form-control-sm" name="settings[max_annual_leave]" value="{{ setting('max_annual_leave', '30', 'hr-officer') }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Sick Leave (Days/Year)</label>
                        <input type="number" class="form-control form-control-sm" name="settings[max_sick_leave]" value="{{ setting('max_sick_leave', '14', 'hr-officer') }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Casual Leave (Days/Year)</label>
                        <input type="number" class="form-control form-control-sm" name="settings[max_casual_leave]" value="{{ setting('max_casual_leave', '10', 'hr-officer') }}">
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Carryover Rules -->
        <div class="card rounded-sm mb-3">
            <div class="card-header py-1 px-2 bg-light">
                <i class="ti ti-transfer me-1"></i>
                <span style="font-weight: 600;">Carryover Rules</span>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Maximum Carryover Days</label>
                        <input type="number" class="form-control form-control-sm" name="settings[carryover_days]" value="{{ setting('carryover_days', '5', 'hr-officer') }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Carryover Expiry (Months)</label>
                        <input type="number" class="form-control form-control-sm" name="settings[carryover_expiry]" value="{{ setting('carryover_expiry', '3', 'hr-officer') }}">
                        <small class="text-muted">Months into new year before carryover expires</small>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Approval Escalation -->
        <div class="card rounded-sm mb-3">
            <div class="card-header py-1 px-2 bg-light">
                <i class="ti ti-clock me-1"></i>
                <span style="font-weight: 600;">Approval Escalation</span>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">HOD Auto-Approve Limit (Days)</label>
                        <input type="number" class="form-control form-control-sm" name="settings[hod_auto_approve_days]" value="{{ setting('hod_auto_approve_days', '3', 'hod') }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Dean Auto-Approve Limit (Days)</label>
                        <input type="number" class="form-control form-control-sm" name="settings[dean_auto_approve_days]" value="{{ setting('dean_auto_approve_days', '7', 'dean') }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Escalate After (Days)</label>
                        <input type="number" class="form-control form-control-sm" name="settings[escalate_after_days]" value="{{ setting('escalate_after_days', '5', 'hr-officer') }}">
                        <small class="text-muted">Days pending before escalation</small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Advance Leave Limit (Days)</label>
                        <input type="number" class="form-control form-control-sm" name="settings[advance_leave_limit]" value="{{ setting('advance_leave_limit', '5', 'hr-officer') }}">
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Blackout Rules -->
        <div class="card rounded-sm mb-3">
            <div class="card-header py-1 px-2 bg-light">
                <i class="ti ti-ban me-1"></i>
                <span style="font-weight: 600;">Blackout Rules</span>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <div class="form-check form-switch">
                            <input type="hidden" name="settings[block_leave_during_exams]" value="false">
                            <input type="checkbox" class="form-check-input" name="settings[block_leave_during_exams]" value="true" id="exam_blackout">
                            <label class="form-check-label" for="exam_blackout">Block leave during examination period</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <button type="submit" class="btn btn-primary btn-sm rounded-sm">Save Policies</button>
        <a href="{{ route('dashboard') }}" class="btn btn-secondary btn-sm rounded-sm">Cancel</a>
    </form>
</div>
@endsection