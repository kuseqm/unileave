@extends('layouts.master')

@section('title', 'Edit Staff')

@section('content')
<div class="container-fluid px-2">
    
    <!-- Breadcrumb -->
    <x-breadcrumb :items=" [
        ['name' => 'Staff', 'url' => route('staff.index')],
        ['name' => 'Edit Staff', 'url' => '#']
    ]" />
    
    <!-- Page Header -->
    <x-page-header title="EDIT STAFF MEMBER" description="Update staff information and permissions" backUrl="{{ route('staff.index') }}" />
    
    <x-alert-danger />
    
    @if($errors->any())
    <x-alert-danger :session="false">
        <strong>Please fix the following errors:</strong>
        <ul class="mb-0 mt-1 ps-3">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close py-0" style="font-size: 0.6rem;" data-bs-dismiss="alert"></button>
    </x-alert-danger>
    @endif
    
    <div class="row g-2">
        <!-- Main Form -->
        <div class="col-lg-8">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-edit text-primary me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Staff Information</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <form action="{{ route('staff.update', $staff->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <!-- Tab Navigation -->
                        <ul class="nav nav-tabs mb-2" id="staffEditTabs" role="tablist" style="font-size: 0.7rem;">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="basic-tab" data-bs-toggle="tab" data-bs-target="#basic" type="button" role="tab">Basic Info</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="employment-tab" data-bs-toggle="tab" data-bs-target="#employment" type="button" role="tab">Employment</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="professional-tab" data-bs-toggle="tab" data-bs-target="#professional" type="button" role="tab">Professional</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="location-tab" data-bs-toggle="tab" data-bs-target="#location" type="button" role="tab">Location</button>
                            </li>
                        </ul>
                        
                        <div class="tab-content">
                            
                            <!-- Tab 1: Basic Information -->
                            <div class="tab-pane fade show active" id="basic" role="tabpanel">
                                <div class="row g-1">
                                    <div class="col-md-2 mb-1">
                                        <label class="form-label" style="font-size: 0.65rem;">Title</label>
                                        <select name="title" class="form-select form-select-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;">
                                            <option value="">Select Title</option>
                                            <option value="Mr" {{ old('title', $staff->title) == 'Mr' ? 'selected' : '' }}>Mr</option>
                                            <option value="Mrs" {{ old('title', $staff->title) == 'Mrs' ? 'selected' : '' }}>Mrs</option>
                                            <option value="Ms" {{ old('title', $staff->title) == 'Ms' ? 'selected' : '' }}>Ms</option>
                                            <option value="Dr" {{ old('title', $staff->title) == 'Dr' ? 'selected' : '' }}>Dr</option>
                                            <option value="Prof" {{ old('title', $staff->title) == 'Prof' ? 'selected' : '' }}>Prof</option>
                                            <option value="Eng" {{ old('title', $staff->title) == 'Eng' ? 'selected' : '' }}>Eng</option>
                                            <option value="Rev" {{ old('title', $staff->title) == 'Rev' ? 'selected' : '' }}>Rev</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2 mb-1">
                                        <label class="form-label" style="font-size: 0.65rem;">Gender</label>
                                        <select name="gender" class="form-select form-select-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;">
                                            <option value="">Select Gender</option>
                                            <option value="male" {{ old('gender', $staff->gender) == 'male' ? 'selected' : '' }}>Male</option>
                                            <option value="female" {{ old('gender', $staff->gender) == 'female' ? 'selected' : '' }}>Female</option>
                                            <option value="other" {{ old('gender', $staff->gender) == 'other' ? 'selected' : '' }}>Other</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3 mb-1">
                                        <label class="form-label" style="font-size: 0.65rem;">Date of Birth</label>
                                        <input type="date" name="date_of_birth" class="form-control form-control-sm rounded-sm" 
                                            value="{{ old('date_of_birth', $staff->date_of_birth ? $staff->date_of_birth->format('Y-m-d') : '') }}" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;">
                                    </div>
                                    <div class="col-md-3 mb-1">
                                        <label class="form-label" style="font-size: 0.65rem;">National ID</label>
                                        <input type="text" name="national_id" class="form-control form-control-sm rounded-sm" 
                                            value="{{ old('national_id', $staff->national_id) }}" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;">
                                    </div>
                                    <div class="col-md-2 mb-1">
                                        <label class="form-label" style="font-size: 0.65rem;">Staff ID</label>
                                        <input type="text" class="form-control form-control-sm rounded-sm" value="{{ $staff->staff_id }}" style="font-size: 0.7rem; padding: 0.2rem 0.3rem; background: #f5f5f5;" readonly>
                                    </div>
                                </div>
                                
                                <div class="row g-1 mt-1">
                                    <div class="col-md-4 mb-1">
                                        <label class="form-label" style="font-size: 0.65rem;">First Name <span class="text-danger">*</span></label>
                                        <input type="text" name="first_name" class="form-control form-control-sm rounded-sm @error('first_name') is-invalid @enderror" value="{{ old('first_name', $staff->first_name) }}" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;" required>
                                        @error('first_name')<div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-4 mb-1">
                                        <label class="form-label" style="font-size: 0.65rem;">Middle Name</label>
                                        <input type="text" name="middle_name" class="form-control form-control-sm rounded-sm @error('middle_name') is-invalid @enderror" value="{{ old('middle_name', $staff->middle_name) }}" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;">
                                        @error('middle_name')<div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-4 mb-1">
                                        <label class="form-label" style="font-size: 0.65rem;">Last Name <span class="text-danger">*</span></label>
                                        <input type="text" name="last_name" class="form-control form-control-sm rounded-sm @error('last_name') is-invalid @enderror" value="{{ old('last_name', $staff->last_name) }}" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;" required>
                                        @error('last_name')<div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                                
                                <div class="row g-1 mt-1">
                                    <div class="col-md-6 mb-1">
                                        <label class="form-label" style="font-size: 0.65rem;">Email <span class="text-danger">*</span></label>
                                        <input type="email" name="email" class="form-control form-control-sm rounded-sm @error('email') is-invalid @enderror" value="{{ old('email', $staff->email) }}" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;" required>
                                        @error('email')<div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6 mb-1">
                                        <label class="form-label" style="font-size: 0.65rem;">Phone Number</label>
                                        <input type="text" name="phone" class="form-control form-control-sm rounded-sm" 
                                            value="{{ old('phone', $staff->phone) }}" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;">
                                    </div>
                                </div>
                                
                                <div class="row g-1 mt-1">
                                    <div class="col-md-6 mb-1">
                                        <label class="form-label" style="font-size: 0.65rem;">Marital Status</label>
                                        <select name="marital_status" class="form-select form-select-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;">
                                            <option value="">Select</option>
                                            <option value="single" {{ old('marital_status', $staff->marital_status) == 'single' ? 'selected' : '' }}>Single</option>
                                            <option value="married" {{ old('marital_status', $staff->marital_status) == 'married' ? 'selected' : '' }}>Married</option>
                                            <option value="divorced" {{ old('marital_status', $staff->marital_status) == 'divorced' ? 'selected' : '' }}>Divorced</option>
                                            <option value="widowed" {{ old('marital_status', $staff->marital_status) == 'widowed' ? 'selected' : '' }}>Widowed</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-1">
                                        <label class="form-label" style="font-size: 0.65rem;">Spouse Name</label>
                                        <input type="text" name="spouse_name" class="form-control form-control-sm rounded-sm" 
                                            value="{{ old('spouse_name', $staff->spouse_name) }}" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;">
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Tab 2: Employment Information -->
                            <div class="tab-pane fade" id="employment" role="tabpanel">
                                <div class="row g-1">
                                    <div class="col-md-6 mb-1">
                                        <label class="form-label" style="font-size: 0.65rem;">Role <span class="text-danger">*</span></label>
                                        <select name="role" class="form-select form-select-sm rounded-sm @error('role') is-invalid @enderror" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;" required>
                                            <option value="">Select Role</option>
                                            @foreach($roles as $role)
                                            <option value="{{ $role }}" {{ $staffRole == $role ? 'selected' : '' }}>{{ ucfirst($role) }}</option>
                                            @endforeach
                                        </select>
                                        @error('role')<div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6 mb-1">
                                        <label class="form-label" style="font-size: 0.65rem;">Job Title</label>
                                        <input type="text" name="job_title" class="form-control form-control-sm rounded-sm" 
                                            value="{{ old('job_title', $staff->job_title) }}" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;">
                                    </div>
                                </div>
                                
                                <div class="row g-1 mt-1">
                                    <div class="col-md-4 mb-1">
                                        <label class="form-label" style="font-size: 0.65rem;">Staff Category</label>
                                        <select name="staff_category" class="form-select form-select-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;">
                                            <option value="academic" {{ old('staff_category', $staff->staff_category) == 'academic' ? 'selected' : '' }}>Academic</option>
                                            <option value="administrative" {{ old('staff_category', $staff->staff_category) == 'administrative' ? 'selected' : '' }}>Administrative</option>
                                            <option value="supporting" {{ old('staff_category', $staff->staff_category) == 'supporting' ? 'selected' : '' }}>Supporting</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-1">
                                        <label class="form-label" style="font-size: 0.65rem;">Job Grade</label>
                                        <input type="text" name="job_grade" class="form-control form-control-sm rounded-sm" 
                                            value="{{ old('job_grade', $staff->job_grade) }}" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;">
                                    </div>
                                    <div class="col-md-4 mb-1">
                                        <label class="form-label" style="font-size: 0.65rem;">Status <span class="text-danger">*</span></label>
                                        <select name="status" class="form-select form-select-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;" required>
                                            <option value="active" {{ $staff->status == 'active' ? 'selected' : '' }}>Active</option>
                                            <option value="inactive" {{ $staff->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                            <option value="suspended" {{ $staff->status == 'suspended' ? 'selected' : '' }}>Suspended</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="row g-1 mt-1">
                                    <div class="col-md-4 mb-1">
                                        <label class="form-label" style="font-size: 0.65rem;">Joining Date</label>
                                        <input type="date" name="joining_date" class="form-control form-control-sm rounded-sm" 
                                            value="{{ old('joining_date', $staff->joining_date ? $staff->joining_date->format('Y-m-d') : '') }}" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;">
                                    </div>
                                    <div class="col-md-4 mb-1">
                                        <label class="form-label" style="font-size: 0.65rem;">Contract Type</label>
                                        <select name="contract_type" class="form-select form-select-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;">
                                            <option value="">Select</option>
                                            <option value="permanent" {{ old('contract_type', $staff->contract_type) == 'permanent' ? 'selected' : '' }}>Permanent</option>
                                            <option value="contract" {{ old('contract_type', $staff->contract_type) == 'contract' ? 'selected' : '' }}>Contract</option>
                                            <option value="temporary" {{ old('contract_type', $staff->contract_type) == 'temporary' ? 'selected' : '' }}>Temporary</option>
                                            <option value="probation" {{ old('contract_type', $staff->contract_type) == 'probation' ? 'selected' : '' }}>Probation</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-1">
                                        <label class="form-label" style="font-size: 0.65rem;">Contract End Date</label>
                                        <input type="date" name="contract_end_date" class="form-control form-control-sm rounded-sm" 
                                            value="{{ old('contract_end_date', $staff->contract_end_date ? $staff->contract_end_date->format('Y-m-d') : '') }}" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;">
                                    </div>
                                </div>
                                
                                <div class="row g-1 mt-1">
                                    <div class="col-md-4 mb-1">
                                        <label class="form-label" style="font-size: 0.65rem;">Reporting To</label>
                                        <select name="reporting_to" class="form-select form-select-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;">
                                            <option value="">None</option>
                                            @foreach($supervisors ?? [] as $supervisor)
                                            <option value="{{ $supervisor->id }}" {{ old('reporting_to', $staff->reporting_to) == $supervisor->id ? 'selected' : '' }}>
                                                {{ $supervisor->full_name }} ({{ $supervisor->staff_id }})
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-1">
                                        <label class="form-label" style="font-size: 0.65rem;">TIN Number</label>
                                        <input type="text" name="tin_number" class="form-control form-control-sm rounded-sm" 
                                            value="{{ old('tin_number', $staff->tin_number) }}" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;">
                                    </div>
                                    <div class="col-md-4 mb-1">
                                        <label class="form-label" style="font-size: 0.65rem;">NSSF Number</label>
                                        <input type="text" name="nssf_number" class="form-control form-control-sm rounded-sm" 
                                            value="{{ old('nssf_number', $staff->nssf_number) }}" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;">
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Tab 3: Professional Information -->
                            <div class="tab-pane fade" id="professional" role="tabpanel">
                                <div class="row g-1">
                                    <div class="col-md-12 mb-1">
                                        <label class="form-label" style="font-size: 0.65rem;">Highest Qualification</label>
                                        <input type="text" name="qualification" class="form-control form-control-sm rounded-sm" 
                                            value="{{ old('qualification', $staff->qualification) }}" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;" placeholder="e.g., PhD, Masters, Bachelor's">
                                    </div>
                                </div>
                                
                                <x-alert-info>
    Additional qualifications, skills, languages, certifications, awards, and work experience can be managed from the staff's profile page after creation.
</x-alert-info>
                            </div>
                            
                            <!-- Tab 4: Location Assignment -->
                            <div class="tab-pane fade" id="location" role="tabpanel">
                                <div class="border rounded-sm p-2" style="background: #f8fafc;">
                                    <div class="mb-1">
                                        <label class="form-label" style="font-size: 0.65rem;">University <span class="text-danger">*</span></label>
                                        <select id="university_id" name="university_id" class="form-select form-select-sm rounded-sm @error('university_id') is-invalid @enderror" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;" required>
                                            <option value="">Select University</option>
                                            @foreach($universities as $uni)
                                            <option value="{{ $uni->id }}" {{ old('university_id', $staff->university_id) == $uni->id ? 'selected' : '' }}>{{ $uni->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('university_id')<div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>@enderror
                                    </div>

                                    <div class="mb-1">
                                        <label class="form-label" style="font-size: 0.65rem;">Campus <span class="text-danger">*</span></label>
                                        <select id="campus_id" name="campus_id" class="form-select form-select-sm rounded-sm @error('campus_id') is-invalid @enderror" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;" required>
                                            <option value="">Select University First</option>
                                        </select>
                                        @error('campus_id')<div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>@enderror
                                    </div>

                                    <div class="mb-1">
                                        <label class="form-label" style="font-size: 0.65rem;">School/Faculty</label>
                                        <select id="school_id" name="school_id" class="form-select form-select-sm rounded-sm @error('school_id') is-invalid @enderror" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;">
                                            <option value="">Select Campus First</option>
                                        </select>
                                        @error('school_id')<div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>@enderror
                                    </div>

                                    <div class="mb-1">
                                        <label class="form-label" style="font-size: 0.65rem;">Department <span class="text-danger">*</span></label>
                                        <select id="department_id" name="department_id" class="form-select form-select-sm rounded-sm @error('department_id') is-invalid @enderror" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;" required>
                                            <option value="">Select School First</option>
                                        </select>
                                        @error('department_id')<div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-end gap-1 mt-3">
                            <a href="{{ route('staff.index') }}" class="btn btn-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.25rem 0.5rem;">Cancel</a>
                            <button type="submit" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.25rem 0.5rem;">
                                <i class="ti ti-device-floppy me-1"></i> Update Staff
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Password Reset Card -->
        <div class="col-lg-4">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-key text-warning me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Password Management</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2 text-center">
                    <p class="mb-2" style="font-size: 0.7rem;">Reset password for <strong>{{ $staff->first_name }} {{ $staff->middle_name ? $staff->middle_name . ' ' : '' }}{{ $staff->last_name }}</strong></p>
                    <button type="button" class="btn btn-warning btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.25rem 0.5rem;" onclick="showResetModal({{ $staff->id }}, '{{ $staff->first_name }}')">
                        <i class="ti ti-key me-1"></i> Reset Password
                    </button>
                    <p class="text-muted mt-2 mb-0" style="font-size: 0.6rem;">Temporary password will be: <code>Temp@123</code></p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Password Reset Modal -->
<div class="modal fade" id="resetPasswordModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-sm">
            <div class="modal-header py-1 px-2">
                <h5 class="modal-title" style="font-size: 0.8rem;">Confirm Password Reset</h5>
                <button type="button" class="btn-close py-0" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body py-2 px-2">
                <p style="font-size: 0.75rem;">Are you sure you want to reset the password for <strong id="resetStaffName"></strong>?</p>
                <x-alert-info>
    Temporary password: <code>Temp@123</code>
</x-alert-info>
            </div>
            <div class="modal-footer py-1 px-2">
                <button type="button" class="btn btn-secondary btn-sm rounded-sm" data-bs-dismiss="modal" style="font-size: 0.7rem;">Cancel</button>
                <a href="#" id="resetConfirmBtn" class="btn btn-warning btn-sm rounded-sm" style="font-size: 0.7rem;">Reset Password</a>
            </div>
        </div>
    </div>
</div>

<script>
// Cascading selects for edit page
const initialDepartmentId = '{{ old('department_id', $staff->department_id) }}';

function loadDepartments(campusId, schoolId, selectedDeptId) {
    const deptSelect = document.getElementById('department_id');
    deptSelect.innerHTML = '<option value="">Select Department</option>';
    deptSelect.disabled = true;

    if (!campusId) return;

    const url = `/staff/departments-by-campus/${campusId}?school_id=${encodeURIComponent(schoolId || '')}`;
    fetch(url)
        .then(res => res.json())
        .then(data => {
            data.forEach(dept => {
                const selected = selectedDeptId == dept.id ? 'selected' : '';
                deptSelect.innerHTML += `<option value="${dept.id}" ${selected}>${dept.name}</option>`;
            });
            deptSelect.disabled = false;
        });
}

document.getElementById('university_id').addEventListener('change', function() {
    const universityId = this.value;
    const campusSelect = document.getElementById('campus_id');
    const schoolSelect = document.getElementById('school_id');
    const deptSelect = document.getElementById('department_id');
    const currentCampusId = '{{ old('campus_id', $staff->campus_id) }}';
    const currentSchoolId = '{{ old('school_id', $staff->school_id) }}';

    campusSelect.innerHTML = '<option value="">Select Campus</option>';
    schoolSelect.innerHTML = '<option value="">Select School</option>';
    deptSelect.innerHTML = '<option value="">Select Department</option>';
    campusSelect.disabled = false;
    schoolSelect.disabled = true;
    deptSelect.disabled = true;

    if (universityId) {
        fetch(`/staff/campuses/${universityId}`)
            .then(res => res.json())
            .then(data => {
                data.forEach(campus => {
                    const selected = currentCampusId == campus.id ? 'selected' : '';
                    campusSelect.innerHTML += `<option value="${campus.id}" ${selected}>${campus.name}</option>`;
                });
                if (currentCampusId) {
                    campusSelect.dispatchEvent(new Event('change'));
                }
            });
    }
});

document.getElementById('campus_id').addEventListener('change', function() {
    const campusId = this.value;
    const schoolSelect = document.getElementById('school_id');
    const deptSelect = document.getElementById('department_id');
    const currentSchoolId = '{{ old('school_id', $staff->school_id) }}';

    schoolSelect.innerHTML = '<option value="">Select School</option>';
    deptSelect.innerHTML = '<option value="">Select Department</option>';
    schoolSelect.disabled = true;
    deptSelect.disabled = true;

    if (campusId) {
        fetch(`/staff/schools/${campusId}`)
            .then(res => res.json())
            .then(data => {
                data.forEach(school => {
                    const selected = currentSchoolId == school.id ? 'selected' : '';
                    schoolSelect.innerHTML += `<option value="${school.id}" ${selected}>${school.name}</option>`;
                });
                schoolSelect.disabled = false;

                if (currentSchoolId) {
                    schoolSelect.value = currentSchoolId;
                    schoolSelect.dispatchEvent(new Event('change'));
                } else {
                    loadDepartments(campusId, schoolSelect.value || '', initialDepartmentId);
                }
            });
    }
});

document.getElementById('school_id').addEventListener('change', function() {
    const campusId = document.getElementById('campus_id').value;
    const schoolId = this.value;
    const selectedDeptId = document.getElementById('department_id').value || initialDepartmentId;
    loadDepartments(campusId, schoolId, selectedDeptId);
});

// Trigger initial load
if (document.getElementById('university_id').value) {
    document.getElementById('university_id').dispatchEvent(new Event('change'));
}

// Reset Password Modal
let resetId = '';
let resetName = '';

function showResetModal(id, name) {
    resetId = id;
    resetName = name;
    document.getElementById('resetStaffName').innerText = name;
    document.getElementById('resetConfirmBtn').href = `/staff/${id}/reset-password`;
    new bootstrap.Modal(document.getElementById('resetPasswordModal')).show();
}
</script>
@endsection