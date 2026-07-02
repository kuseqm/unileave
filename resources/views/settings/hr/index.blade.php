@extends('layouts.master')

@section('title', 'HR Settings')

@section('content')
<div class="container-fluid px-2">
    
    <x-breadcrumb :items="[
        ['name' => 'Settings', 'url' => '#'],
        ['name' => 'HR Settings', 'url' => '#']
    ]" />
    
    <x-page-header title="HR SETTINGS" description="Configure human resources preferences">
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
        
        <!-- Document Requirements -->
        <div class="card rounded-sm mb-3">
            <div class="card-header py-1 px-2 bg-light">
                <i class="ti ti-file-text me-1"></i>
                <span style="font-weight: 600;">Document Requirements</span>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Require Medical Certificate for Sick Leave > 3 days</label>
                        <div class="form-check form-switch">
                            <input type="hidden" name="settings[require_medical_certificate]" value="false">
                            <input type="checkbox" class="form-check-input" name="settings[require_medical_certificate]" value="true" id="medical_cert">
                            <label class="form-check-label" for="medical_cert">Yes, require document</label>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Maximum File Size (MB)</label>
                        <input type="number" class="form-control form-control-sm" name="settings[max_file_size]" value="{{ setting('max_file_size', '5', 'hr-officer') }}">
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Allowed File Types</label>
                        <input type="text" class="form-control form-control-sm" name="settings[allowed_file_types]" value="{{ setting('allowed_file_types', 'pdf,jpg,jpeg,png', 'hr-officer') }}">
                        <small class="text-muted">Comma-separated list: pdf,jpg,jpeg,png,doc,docx</small>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- HR Approval Settings -->
        <div class="card rounded-sm mb-3">
            <div class="card-header py-1 px-2 bg-light">
                <i class="ti ti-checklist me-1"></i>
                <span style="font-weight: 600;">Approval Settings</span>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Auto-Approve HR Logistics</label>
                        <div class="form-check form-switch">
                            <input type="hidden" name="settings[auto_approve_logistics]" value="false">
                            <input type="checkbox" class="form-check-input" name="settings[auto_approve_logistics]" value="true" id="auto_approve">
                            <label class="form-check-label" for="auto_approve">Auto-approve after DVC approval</label>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">HR Approval Timeout (Hours)</label>
                        <input type="number" class="form-control form-control-sm" name="settings[hr_approval_timeout]" value="{{ setting('hr_approval_timeout', '48', 'hr-officer') }}">
                    </div>
                </div>
            </div>
        </div>
        
        <button type="submit" class="btn btn-primary btn-sm rounded-sm">Save HR Settings</button>
        <a href="{{ route('dashboard') }}" class="btn btn-secondary btn-sm rounded-sm">Cancel</a>
    </form>
</div>
@endsection