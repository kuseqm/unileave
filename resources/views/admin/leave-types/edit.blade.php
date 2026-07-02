@extends('layouts.master')

@section('title', 'Edit Leave Type')

@section('content')
<div class="container-fluid px-2">
    
    <!-- Breadcrumb -->
    <x-breadcrumb :items="[
        ['name' => 'Leave Types', 'url' => route('admin.leave-types.index')],
        ['name' => 'Edit Leave Type', 'url' => '#']
    ]" />
    
    <!-- Page Header -->
    <x-page-header title="EDIT LEAVE TYPE" description="Update leave type configuration">
        <a href="{{ route('admin.leave-types.index') }}" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
            <i class="ti ti-arrow-left me-1"></i> Back
        </a>
    </x-page-header>
    
    <div class="row g-2">
        <!-- Main Form -->
        <div class="col-lg-7">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-edit text-primary me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Edit: {{ $leaveType->name }}</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <form action="{{ route('admin.leave-types.update', $leaveType->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row g-1">
                            <div class="col-md-4 mb-1">
                                <label class="form-label" style="font-size: 0.65rem;">Code <span class="text-danger">*</span></label>
                                <input type="text" name="code" class="form-control form-control-sm rounded-sm @error('code') is-invalid @enderror" value="{{ old('code', $leaveType->code) }}" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;" required>
                                @error('code')<div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-8 mb-1">
                                <label class="form-label" style="font-size: 0.65rem;">Leave Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control form-control-sm rounded-sm @error('name') is-invalid @enderror" value="{{ old('name', $leaveType->name) }}" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;" required>
                                @error('name')<div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        
                        <div class="mb-1">
                            <label class="form-label" style="font-size: 0.65rem;">Description</label>
                            <textarea name="description" class="form-control form-control-sm rounded-sm @error('description') is-invalid @enderror" rows="2" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;">{{ old('description', $leaveType->description) }}</textarea>
                            @error('description')<div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>@enderror
                        </div>
                        
                        <div class="row g-1">
                            <div class="col-md-6 mb-1">
                                <div class="form-check">
                                    <input type="checkbox" name="requires_certificate" value="1" class="form-check-input" id="requires_certificate" {{ old('requires_certificate', $leaveType->requires_certificate) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="requires_certificate" style="font-size: 0.7rem;">Requires Doctor's Certificate</label>
                                </div>
                            </div>
                            <div class="col-md-6 mb-1">
                                <div class="form-check">
                                    <input type="checkbox" name="requires_letter" value="1" class="form-check-input" id="requires_letter" {{ old('requires_letter', $leaveType->requires_letter) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="requires_letter" style="font-size: 0.7rem;">Requires Letter of Request</label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row g-1">
                            <div class="col-md-6 mb-1">
                                <div class="form-check">
                                    <input type="checkbox" name="deduct_balance" value="1" class="form-check-input" id="deduct_balance" {{ old('deduct_balance', $leaveType->deduct_balance) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="deduct_balance" style="font-size: 0.7rem;">Deduct from Balance</label>
                                </div>
                            </div>
                            <div class="col-md-6 mb-1">
                                <div class="form-check">
                                    <input type="checkbox" name="vc_discretion" value="1" class="form-check-input" id="vc_discretion" {{ old('vc_discretion', $leaveType->vc_discretion) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="vc_discretion" style="font-size: 0.7rem;">Requires VC Discretion</label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row g-1">
                            <div class="col-md-6 mb-1">
                                <label class="form-label" style="font-size: 0.65rem;">Max Consecutive Days</label>
                                <input type="number" name="max_consecutive_days" class="form-control form-control-sm rounded-sm @error('max_consecutive_days') is-invalid @enderror" value="{{ old('max_consecutive_days', $leaveType->max_consecutive_days) }}" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;">
                                @error('max_consecutive_days')<div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>@enderror
                                <small class="text-muted" style="font-size: 0.6rem;">Leave empty for unlimited</small>
                            </div>
                            <div class="col-md-6 mb-1">
                                <label class="form-label" style="font-size: 0.65rem;">Status</label>
                                <select name="status" class="form-select form-select-sm rounded-sm @error('status') is-invalid @enderror" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;" required>
                                    <option value="active" {{ old('status', $leaveType->status) == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status', $leaveType->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                                @error('status')<div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-end gap-1 mt-2">
                            <a href="{{ route('admin.leave-types.index') }}" class="btn btn-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.25rem 0.5rem;">Cancel</a>
                            <button type="submit" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.25rem 0.5rem;">
                                <i class="ti ti-device-floppy me-1"></i> Update Leave Type
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Right Column: Info Card -->
        <div class="col-lg-5">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-info-circle text-info me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Leave Type Guide</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <div class="mb-2">
                        <div class="fw-semibold" style="font-size: 0.7rem;"><i class="ti ti-file me-1 text-primary"></i> Configuration Tips</div>
                        <ul class="mb-0 ps-3" style="font-size: 0.65rem; color: #555;">
                            <li><strong>Certificate</strong> - Enable for medical leaves</li>
                            <li><strong>Letter</strong> - Enable for formal request leaves</li>
                            <li><strong>Balance Deduction</strong> - Annual, Sick should deduct</li>
                            <li><strong>VC Discretion</strong> - Special Leave only</li>
                        </ul>
                    </div>
                    
                    <div class="mb-2">
                        <div class="fw-semibold" style="font-size: 0.7rem;"><i class="ti ti-alert-triangle me-1 text-warning"></i> Impact of Changes</div>
                        <ul class="mb-0 ps-3" style="font-size: 0.65rem; color: #555;">
                            <li>Changes affect future applications only</li>
                            <li>Existing leave requests are not modified</li>
                            <li>Balance calculations use current settings</li>
                            <li>Deactivating hides from staff selection</li>
                        </ul>
                    </div>
                    
                    <div class="alert alert-light border mb-0 py-1 px-2 rounded-sm">
                        <div class="d-flex align-items-start gap-1">
                            <i class="ti ti-stats text-success mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">Usage Statistics</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">Used in {{ $leaveType->leaveRequests->count() }} leave request(s)</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Quick Stats Card -->
            <div class="card rounded-sm mt-2">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-chart-bar text-success me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Leave Statistics</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="fw-bold text-primary" style="font-size: 1rem;">{{ $leaveType->leaveRequests->count() }}</div>
                            <div class="text-muted" style="font-size: 0.6rem;">Total Requests</div>
                        </div>
                        <div class="col-6">
                            <div class="fw-bold text-success" style="font-size: 1rem;">{{ $leaveType->leaveRequests->where('status', 'approved')->count() }}</div>
                            <div class="text-muted" style="font-size: 0.6rem;">Approved</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection