@extends('layouts.master')

@section('title', 'Add Leave Type')

@section('content')
<div class="container-fluid px-2">
    
    <!-- Breadcrumb -->
    <x-breadcrumb :items="[
        ['name' => 'Leave Types', 'url' => route('admin.leave-types.index')],
        ['name' => 'Add Leave Type', 'url' => '#']
    ]" />
    
    <!-- Page Header -->
    <x-page-header title="ADD LEAVE TYPE" description="Create a new leave type (e.g., Blood Donation Leave)">
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
                        <i class="ti ti-plus text-primary me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Leave Type Information</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <form action="{{ route('admin.leave-types.store') }}" method="POST">
                        @csrf
                        
                        <div class="row g-1">
                            <div class="col-md-4 mb-1">
                                <label class="form-label" style="font-size: 0.65rem;">Code <span class="text-danger">*</span></label>
                                <input type="text" name="code" class="form-control form-control-sm rounded-sm @error('code') is-invalid @enderror" value="{{ old('code') }}" placeholder="e.g., LC12" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;" required>
                                @error('code')<div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-8 mb-1">
                                <label class="form-label" style="font-size: 0.65rem;">Leave Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control form-control-sm rounded-sm @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="e.g., Blood Donation Leave" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;" required>
                                @error('name')<div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        
                        <div class="mb-1">
                            <label class="form-label" style="font-size: 0.65rem;">Description</label>
                            <textarea name="description" class="form-control form-control-sm rounded-sm @error('description') is-invalid @enderror" rows="2" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;" placeholder="Brief description of this leave type">{{ old('description') }}</textarea>
                            @error('description')<div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>@enderror
                        </div>
                        
                        <div class="row g-1">
                            <div class="col-md-6 mb-1">
                                <div class="form-check">
                                    <input type="checkbox" name="requires_certificate" value="1" class="form-check-input" id="requires_certificate" {{ old('requires_certificate') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="requires_certificate" style="font-size: 0.7rem;">Requires Doctor's Certificate</label>
                                </div>
                            </div>
                            <div class="col-md-6 mb-1">
                                <div class="form-check">
                                    <input type="checkbox" name="requires_letter" value="1" class="form-check-input" id="requires_letter" {{ old('requires_letter') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="requires_letter" style="font-size: 0.7rem;">Requires Letter of Request</label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row g-1">
                            <div class="col-md-6 mb-1">
                                <div class="form-check">
                                    <input type="checkbox" name="deduct_balance" value="1" class="form-check-input" id="deduct_balance" {{ old('deduct_balance', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="deduct_balance" style="font-size: 0.7rem;">Deduct from Balance</label>
                                </div>
                            </div>
                            <div class="col-md-6 mb-1">
                                <div class="form-check">
                                    <input type="checkbox" name="vc_discretion" value="1" class="form-check-input" id="vc_discretion" {{ old('vc_discretion') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="vc_discretion" style="font-size: 0.7rem;">Requires VC Discretion</label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row g-1">
                            <div class="col-md-6 mb-1">
                                <label class="form-label" style="font-size: 0.65rem;">Max Consecutive Days</label>
                                <input type="number" name="max_consecutive_days" class="form-control form-control-sm rounded-sm @error('max_consecutive_days') is-invalid @enderror" value="{{ old('max_consecutive_days', 28) }}" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;">
                                @error('max_consecutive_days')<div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>@enderror
                                <small class="text-muted" style="font-size: 0.6rem;">For reference only - applications are not checked against this. Set the limit in your HR policy ("Longest single request", per leave type).</small>
                            </div>
                            <div class="col-md-6 mb-1">
                                <label class="form-label" style="font-size: 0.65rem;">Status</label>
                                <select name="status" class="form-select form-select-sm rounded-sm @error('status') is-invalid @enderror" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;" required>
                                    <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                                @error('status')<div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-end gap-1 mt-2">
                            <a href="{{ route('admin.leave-types.index') }}" class="btn btn-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.25rem 0.5rem;">Cancel</a>
                            <button type="submit" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.25rem 0.5rem;">
                                <i class="ti ti-device-floppy me-1"></i> Create Leave Type
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
                        <div class="fw-semibold" style="font-size: 0.7rem;"><i class="ti ti-file me-1 text-primary"></i> About Leave Types</div>
                        <ul class="mb-0 ps-3" style="font-size: 0.65rem; color: #555;">
                            <li><strong>Code</strong> - Unique identifier (e.g., LC12, LC13)</li>
                            <li><strong>Certificate</strong> - Doctor's note required</li>
                            <li><strong>Letter</strong> - Written request letter required</li>
                            <li><strong>Balance Deduction</strong> - Whether days are deducted from staff balance</li>
                            <li><strong>VC Discretion</strong> - Requires Vice Chancellor approval</li>
                        </ul>
                    </div>
                    
                    <div class="mb-2">
                        <div class="fw-semibold" style="font-size: 0.7rem;"><i class="ti ti-alert-triangle me-1 text-warning"></i> Important Notes</div>
                        <ul class="mb-0 ps-3" style="font-size: 0.65rem; color: #555;">
                            <li>Codes must be unique (LC1-LC11 are reserved)</li>
                            <li>Use LC12 and above for custom leave types</li>
                            <li>Changing settings affects future applications only</li>
                            <li>Inactive types won't appear in applications</li>
                        </ul>
                    </div>
                    
                    <x-alert-light class="mb-0 py-1 px-2 rounded-sm">

                        <div class="d-flex align-items-start gap-1">
                            <i class="ti ti-help text-primary mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">Pro Tip</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">Example: Blood Donation Leave (LC12) - 1 day, requires certificate, deducts balance</small>
                            </div>
                        
</x-alert-light>
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
                            <div class="fw-bold text-primary" style="font-size: 1rem;">{{ \App\Models\LeaveType::count() }}</div>
                            <div class="text-muted" style="font-size: 0.6rem;">Total Types</div>
                        </div>
                        <div class="col-6">
                            <div class="fw-bold text-success" style="font-size: 1rem;">{{ \App\Models\LeaveType::where('status', 'active')->count() }}</div>
                            <div class="text-muted" style="font-size: 0.6rem;">Active Types</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Auto-generate code from name
document.querySelector('input[name="name"]')?.addEventListener('blur', function() {
    const codeField = document.querySelector('input[name="code"]');
    if (codeField && !codeField.value) {
        let code = this.value.substring(0, 4).toUpperCase().replace(/\s/g, '');
        codeField.value = code;
    }
});
</script>
@endsection