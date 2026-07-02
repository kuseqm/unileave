@extends('layouts.master')

@section('title', 'Add Department')

@section('content')
<div class="container-fluid px-2">
    
    <!-- Breadcrumb -->
    <x-breadcrumb :items="[
        ['name' => 'Departments', 'url' => route('admin.departments.index')],
        ['name' => 'Add Department', 'url' => '#']
    ]" />
    
    <!-- Page Header -->
    <x-page-header title="ADD DEPARTMENT" description="Create a new department under a school/faculty">
        <a href="{{ route('admin.departments.index') }}" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
            <i class="ti ti-arrow-left me-1"></i> Back
        </a>
    </x-page-header>
    
    <div class="row g-2">
        <!-- Main Form -->
        <div class="col-lg-7">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-folder text-primary me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Department Information</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <form action="{{ route('admin.departments.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-1">
                            <label class="form-label" style="font-size: 0.65rem;">School/Faculty <span class="text-danger">*</span></label>
                            <select name="school_id" class="form-select form-select-sm rounded-sm @error('school_id') is-invalid @enderror" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;" required>
                                <option value="">Select School/Faculty</option>
                                @foreach($schools as $school)
                                <option value="{{ $school->id }}" {{ ($selectedSchool ?? old('school_id')) == $school->id ? 'selected' : '' }}>
                                    {{ $school->name }} ({{ $school->campus->name ?? 'N/A' }})
                                </option>
                                @endforeach
                            </select>
                            @error('school_id')
                                <div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="row g-1">
                            <div class="col-md-6 mb-1">
                                <label class="form-label" style="font-size: 0.65rem;">Department Name <span class="text-danger">*</span></label>
                                <input type="text" 
                                       name="name" 
                                       class="form-control form-control-sm rounded-sm @error('name') is-invalid @enderror" 
                                       value="{{ old('name') }}" 
                                       placeholder="e.g., Computer Science"
                                       style="font-size: 0.7rem; padding: 0.2rem 0.3rem;"
                                       required>
                                @error('name')
                                    <div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-1">
                                <label class="form-label" style="font-size: 0.65rem;">Code <span class="text-danger">*</span></label>
                                <input type="text" 
                                       name="code" 
                                       class="form-control form-control-sm rounded-sm @error('code') is-invalid @enderror" 
                                       value="{{ old('code') }}" 
                                       placeholder="e.g., CS"
                                       style="font-size: 0.7rem; padding: 0.2rem 0.3rem;"
                                       required>
                                @error('code')
                                    <div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>
                                @enderror
                                <small class="text-muted" style="font-size: 0.6rem;">Short code for the department</small>
                            </div>
                        </div>
                        
                        <div class="mb-1">
                            <label class="form-label" style="font-size: 0.65rem;">Head of Department (HOD)</label>
                            <select name="hod_staff_id" class="form-select form-select-sm rounded-sm @error('hod_staff_id') is-invalid @enderror" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;">
                                <option value="">Select HOD</option>
                                @foreach($hodCandidates as $hod)
                                <option value="{{ $hod->id }}" {{ old('hod_staff_id') == $hod->id ? 'selected' : '' }}>
                                    {{ $hod->title ? $hod->title . ' ' : '' }}{{ $hod->first_name }} {{ $hod->last_name }} ({{ $hod->staff_id }})
                                </option>
                                @endforeach
                            </select>
                            @error('hod_staff_id')
                                <div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>
                            @enderror
                            <small class="text-muted" style="font-size: 0.6rem;">Academic staff member who heads this department</small>
                        </div>
                        
                        <!-- ✅ ADDED: Unit Head Selection (for non-academic staff) -->
                        <div class="mb-1">
                            <label class="form-label" style="font-size: 0.65rem;">Unit Head</label>
                            <select name="unit_head_staff_id" class="form-select form-select-sm rounded-sm @error('unit_head_staff_id') is-invalid @enderror" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;">
                                <option value="">Select Unit Head</option>
                                @foreach($unitHeadCandidates as $unitHead)
                                <option value="{{ $unitHead->id }}" {{ old('unit_head_staff_id') == $unitHead->id ? 'selected' : '' }}>
                                    {{ $unitHead->title ? $unitHead->title . ' ' : '' }}{{ $unitHead->first_name }} {{ $unitHead->last_name }} ({{ $unitHead->staff_id }}) - {{ ucfirst($unitHead->staff_category) }}
                                </option>
                                @endforeach
                            </select>
                            @error('unit_head_staff_id')
                                <div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>
                            @enderror
                            <small class="text-muted" style="font-size: 0.6rem;">Staff member who heads this unit (for non-academic departments)</small>
                        </div>
                        
                        <div class="mb-1">
                            <label class="form-label" style="font-size: 0.65rem;">Description</label>
                            <textarea name="description" 
                                      class="form-control form-control-sm rounded-sm @error('description') is-invalid @enderror" 
                                      rows="2"
                                      style="font-size: 0.7rem; padding: 0.2rem 0.3rem;"
                                      placeholder="Brief description of the department">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-1">
                            <label class="form-label" style="font-size: 0.65rem;">Status</label>
                            <select name="status" class="form-select form-select-sm rounded-sm @error('status') is-invalid @enderror" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;">
                                <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="d-flex justify-content-end gap-1 mt-2">
                            <a href="{{ route('admin.departments.index') }}" class="btn btn-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.25rem 0.5rem;">Cancel</a>
                            <button type="submit" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.25rem 0.5rem;">
                                <i class="ti ti-device-floppy me-1"></i> Create Department
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
                        <span style="font-size: 0.7rem; font-weight: 600;">About Departments</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <div class="alert alert-light border mb-2 py-1 px-2 rounded-sm">
                        <div class="d-flex align-items-start gap-1">
                            <i class="ti ti-hierarchy text-primary mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">Hierarchy</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">University → Campus → School/Faculty → <strong>Department</strong> → Staff</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert alert-light border mb-2 py-1 px-2 rounded-sm">
                        <div class="d-flex align-items-start gap-1">
                            <i class="ti ti-user-check text-success mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">Leadership Roles</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">• <strong>HOD</strong> - Head of Department (academic staff)<br>
                                • <strong>Unit Head</strong> - Head of Unit (non-academic staff)</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert alert-light border mb-0 py-1 px-2 rounded-sm">
                        <div class="d-flex align-items-start gap-1">
                            <i class="ti ti-tips text-warning mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">Important Notes</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">• Each department must belong to a school/faculty<br>
                                • HOD role can be assigned to academic staff only<br>
                                • Unit Head can be assigned to any staff member<br>
                                • Departments can have multiple staff members</small>
                            </div>
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