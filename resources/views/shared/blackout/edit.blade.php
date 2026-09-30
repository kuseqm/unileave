@extends('layouts.master')

@section('title', 'Edit Blackout Period')

@section('content')
<div class="container-fluid px-2">
    
    <x-breadcrumb :items="[
        ['name' => 'Blackout Periods', 'url' => route('blackout.index')],
        ['name' => 'Edit Blackout', 'url' => '#']
    ]" />
    
    <x-page-header title="EDIT BLACKOUT PERIOD" description="Update blackout period information">
        <a href="{{ route('blackout.index') }}" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
            <i class="ti ti-arrow-left me-1"></i> Back
        </a>
    </x-page-header>
    
    <div class="row g-2">
        <!-- Main Form Column -->
        <div class="col-lg-7">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-edit text-primary me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Edit Blackout Period</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <form action="{{ route('blackout.update', $blackout->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-1">
                            <label class="form-label" style="font-size: 0.65rem;">Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control form-control-sm rounded-sm" value="{{ old('name', $blackout->name) }}" placeholder="e.g., Final Examination Week" style="font-size: 0.7rem;" required>
                        </div>
                        
                        <div class="row g-1">
                            <div class="col-md-6 mb-1">
                                <label class="form-label" style="font-size: 0.65rem;">Start Date <span class="text-danger">*</span></label>
                                <input type="date" name="start_date" class="form-control form-control-sm rounded-sm" value="{{ old('start_date', $blackout->start_date) }}" style="font-size: 0.7rem;" required>
                            </div>
                            <div class="col-md-6 mb-1">
                                <label class="form-label" style="font-size: 0.65rem;">End Date <span class="text-danger">*</span></label>
                                <input type="date" name="end_date" class="form-control form-control-sm rounded-sm" value="{{ old('end_date', $blackout->end_date) }}" style="font-size: 0.7rem;" required>
                            </div>
                        </div>
                        
                        <div class="mb-1">
                            <label class="form-label" style="font-size: 0.65rem;">Restriction Type <span class="text-danger">*</span></label>
                            <select name="restriction_type" class="form-select form-select-sm rounded-sm" style="font-size: 0.7rem;" required>
                                <option value="hard" {{ $blackout->restriction_type == 'hard' ? 'selected' : '' }}>Hard Block - Cannot apply for leave</option>
                                <option value="soft" {{ $blackout->restriction_type == 'soft' ? 'selected' : '' }}>Soft Warning - Warning only, can still apply</option>
                            </select>
                        </div>
                        
                        <div class="mb-1 form-check">
                            <input type="checkbox" name="override_allowed" class="form-check-input" id="overrideAllowed" value="1" {{ $blackout->override_allowed ? 'checked' : '' }}>
                            <label class="form-check-label" for="overrideAllowed" style="font-size: 0.65rem;">Allow override by Admin/HR</label>
                            <small class="text-muted d-block" style="font-size: 0.55rem;">If checked, Admin and HR can approve leave during this period</small>
                        </div>
                        
                        <div class="d-flex justify-content-end gap-1 mt-2">
                            <a href="{{ route('blackout.index') }}" class="btn btn-secondary btn-sm rounded-sm" style="font-size: 0.7rem;">Cancel</a>
                            <button type="submit" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem;">
                                <i class="ti ti-save me-1"></i> Update Blackout Period
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Info Column -->
        <div class="col-lg-5">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-info-circle text-info me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Blackout Period Information</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    @php
                        $daysLeft = \Carbon\Carbon::parse($blackout->end_date)->diffInDays(now(), false);
                        $isActive = $daysLeft > 0 && \Carbon\Carbon::parse($blackout->start_date)->isPast();
                    @endphp
                    
                    @if($isActive)
                    <x-alert-danger :session="false">
    <strong>This blackout period is currently ACTIVE!</strong>
                        <small>Staff cannot apply for leave during this period.</small>
</x-alert-danger>
                    @endif
                    
                    <x-alert-light class="mb-2 py-1 px-2 rounded-sm">

                        <div class="d-flex align-items-start gap-1">
                            <i class="ti ti-calendar text-primary mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">Period Details</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">• Starts: {{ \Carbon\Carbon::parse($blackout->start_date)->format('d M Y') }}</small><br>
                                <small class="text-muted" style="font-size: 0.6rem;">• Ends: {{ \Carbon\Carbon::parse($blackout->end_date)->format('d M Y') }}</small><br>
                                <small class="text-muted" style="font-size: 0.6rem;">• Duration: {{ \Carbon\Carbon::parse($blackout->start_date)->diffInDays($blackout->end_date) + 1 }} days</small>
                            </div>
                        
</x-alert-light>
                    </div>
                    
                    <x-alert-light class="mb-2 py-1 px-2 rounded-sm">

                        <div class="d-flex align-items-start gap-1">
                            <i class="ti ti-tips text-warning mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">Restriction Type</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">
                                    @if($blackout->restriction_type == 'hard')
                                        <span class="text-danger">Hard Block</span> - Staff cannot apply for leave during this period
                                    @else
                                        <span class="text-warning">Soft Warning</span> - Staff see a warning but can still apply
                                    @endif
                                </small>
                            </div>
                        
</x-alert-light>
                    </div>
                    
                    <x-alert-light class="mb-0 py-1 px-2 rounded-sm">

                        <div class="d-flex align-items-start gap-1">
                            <i class="ti ti-user-check text-success mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">Override Setting</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">
                                    @if($blackout->override_allowed)
                                        <span class="text-success">Override Allowed</span> - Admin/HR can approve leave during this period
                                    @else
                                        <span class="text-muted">Override Not Allowed</span> - No leave approvals during this period
                                    @endif
                                </small>
                            </div>
                        
</x-alert-light>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection