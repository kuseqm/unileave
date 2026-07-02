@extends('layouts.master')

@section('title', 'Add Blackout Period')

@section('content')
<div class="container-fluid px-2">
    
    <x-breadcrumb :items="[
        ['name' => 'Blackout Periods', 'url' => route('blackout.index')],
        ['name' => 'Add Blackout', 'url' => '#']
    ]" />
    
    <x-page-header title="ADD BLACKOUT PERIOD" description="Create a period when leave requests are restricted">
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
                        <i class="ti ti-ban text-danger me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Blackout Period Information</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <form action="{{ route('blackout.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-1">
                            <label class="form-label" style="font-size: 0.65rem;">Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control form-control-sm rounded-sm" value="{{ old('name') }}" placeholder="e.g., Final Examination Week" style="font-size: 0.7rem;" required>
                            <small class="text-muted" style="font-size: 0.55rem;">A descriptive name for this blackout period</small>
                        </div>
                        
                        <div class="row g-1">
                            <div class="col-md-6 mb-1">
                                <label class="form-label" style="font-size: 0.65rem;">Start Date <span class="text-danger">*</span></label>
                                <input type="date" name="start_date" class="form-control form-control-sm rounded-sm" value="{{ old('start_date') }}" style="font-size: 0.7rem;" required>
                            </div>
                            <div class="col-md-6 mb-1">
                                <label class="form-label" style="font-size: 0.65rem;">End Date <span class="text-danger">*</span></label>
                                <input type="date" name="end_date" class="form-control form-control-sm rounded-sm" value="{{ old('end_date') }}" style="font-size: 0.7rem;" required>
                            </div>
                        </div>
                        
                        <div class="mb-1">
                            <label class="form-label" style="font-size: 0.65rem;">Restriction Type <span class="text-danger">*</span></label>
                            <select name="restriction_type" class="form-select form-select-sm rounded-sm" style="font-size: 0.7rem;" required>
                                <option value="hard">Hard Block - Cannot apply for leave</option>
                                <option value="soft">Soft Warning - Warning only, can still apply</option>
                            </select>
                            <small class="text-muted" style="font-size: 0.55rem;">Hard block prevents leave applications; soft block shows a warning</small>
                        </div>
                        
                        <div class="mb-1 form-check">
                            <input type="checkbox" name="override_allowed" class="form-check-input" id="overrideAllowed" value="1">
                            <label class="form-check-label" for="overrideAllowed" style="font-size: 0.65rem;">Allow override by Admin/HR</label>
                            <small class="text-muted d-block" style="font-size: 0.55rem;">If checked, Admin and HR can approve leave during this period</small>
                        </div>
                        
                        <div class="d-flex justify-content-end gap-1 mt-2">
                            <a href="{{ route('blackout.index') }}" class="btn btn-secondary btn-sm rounded-sm" style="font-size: 0.7rem;">Cancel</a>
                            <button type="submit" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem;">
                                <i class="ti ti-device-floppy me-1"></i> Create Blackout Period
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
                        <span style="font-size: 0.7rem; font-weight: 600;">About Blackout Periods</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <div class="alert alert-light border mb-2 py-1 px-2 rounded-sm">
                        <div class="d-flex align-items-start gap-1">
                            <i class="ti ti-tips text-warning mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">What is a Blackout Period?</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">A blackout period is a time when leave requests are restricted or blocked entirely.</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert alert-light border mb-2 py-1 px-2 rounded-sm">
                        <div class="d-flex align-items-start gap-1">
                            <i class="ti ti-hierarchy text-primary mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">Restriction Types</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">• <strong>Hard Block</strong> - Staff cannot apply for leave during this period</small><br>
                                <small class="text-muted" style="font-size: 0.6rem;">• <strong>Soft Warning</strong> - Staff see a warning but can still apply</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert alert-light border mb-0 py-1 px-2 rounded-sm">
                        <div class="d-flex align-items-start gap-1">
                            <i class="ti ti-calendar text-success mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">Common Blackout Periods</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">• Final examination weeks</small><br>
                                <small class="text-muted" style="font-size: 0.6rem;">• University accreditation visits</small><br>
                                <small class="text-muted" style="font-size: 0.6rem;">• Peak admission periods</small><br>
                                <small class="text-muted" style="font-size: 0.6rem;">• Budget preparation periods</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection