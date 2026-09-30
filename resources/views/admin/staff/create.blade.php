@extends('layouts.master')

@section('title', 'Add Staff')

@section('content')
<div class="container-fluid px-2">
    
    <!-- Breadcrumb -->
    <x-breadcrumb :items=" [
        ['name' => 'Staff', 'url' => route('staff.index')],
        ['name' => 'Add Staff', 'url' => '#']
    ]" />
    
    <!-- Page Header -->
    <x-page-header title="ADD STAFF MEMBER" description="Create a new staff account in the system" backUrl="{{ route('staff.index') }}" />
    
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
        <div class="col-lg-7">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-user-plus text-primary me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Staff Information</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <form action="{{ route('staff.store') }}" method="POST" id="staffForm">
                        @csrf
                        
                        <!-- Name Fields with Title and Gender -->
                        <div class="row g-1">
                            <!-- Title (NEW) -->
                            <div class="col-md-2 mb-1">
                                <label class="form-label" style="font-size: 0.65rem;">Title</label>
                                <select name="title" class="form-select form-select-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 1.4rem 0.2rem 0.3rem;">
                                    <option value="">Title</option>
                                    <option value="Mr" {{ old('title') == 'Mr' ? 'selected' : '' }}>Mr</option>
                                    <option value="Mrs" {{ old('title') == 'Mrs' ? 'selected' : '' }}>Mrs</option>
                                    <option value="Ms" {{ old('title') == 'Ms' ? 'selected' : '' }}>Ms</option>
                                    <option value="Dr" {{ old('title') == 'Dr' ? 'selected' : '' }}>Dr</option>
                                    <option value="Prof" {{ old('title') == 'Prof' ? 'selected' : '' }}>Prof</option>
                                    <option value="Eng" {{ old('title') == 'Eng' ? 'selected' : '' }}>Eng</option>
                                    <option value="Rev" {{ old('title') == 'Rev' ? 'selected' : '' }}>Rev</option>
                                </select>
                            </div>

                            <!-- Gender (NEW) -->
                            <div class="col-md-2 mb-1">
                                <label class="form-label" style="font-size: 0.65rem;">Gender</label>
                                <select name="gender" class="form-select form-select-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 1.4rem 0.2rem 0.3rem;">
                                    <option value="">Gender</option>
                                    <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                                    <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                            </div>
                            
                            <div class="col-md-4 mb-1">
                                <label class="form-label" style="font-size: 0.65rem;">First Name <span class="text-danger">*</span></label>
                                <input type="text" name="first_name" class="form-control form-control-sm rounded-sm @error('first_name') is-invalid @enderror" value="{{ old('first_name') }}" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;" required>
                                @error('first_name')<div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4 mb-1">
                                <label class="form-label" style="font-size: 0.65rem;">Middle Name</label>
                                <input type="text" name="middle_name" class="form-control form-control-sm rounded-sm @error('middle_name') is-invalid @enderror" value="{{ old('middle_name') }}" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;">
                                @error('middle_name')<div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4 mb-1">
                                <label class="form-label" style="font-size: 0.65rem;">Last Name <span class="text-danger">*</span></label>
                                <input type="text" name="last_name" class="form-control form-control-sm rounded-sm @error('last_name') is-invalid @enderror" value="{{ old('last_name') }}" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;" required>
                                @error('last_name')<div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        
                        <div class="mb-1">
                            <label class="form-label" style="font-size: 0.65rem;">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control form-control-sm rounded-sm @error('email') is-invalid @enderror" value="{{ old('email') }}" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;" required>
                            @error('email')<div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>@enderror
                        </div>
                        
                        <div class="row g-1">
                            <div class="col-md-6 mb-1">
                                <label class="form-label" style="font-size: 0.65rem;">Role <span class="text-danger">*</span></label>
                                <select name="role" class="form-select form-select-sm rounded-sm @error('role') is-invalid @enderror" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;" required>
                                    <option value="">Select Role</option>
                                    @foreach($roles as $role)
                                    <option value="{{ $role }}">{{ ucfirst($role) }}</option>
                                    @endforeach
                                </select>
                                @error('role')<div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>@enderror
                            </div>
                            
                            <div class="col-md-6 mb-1">
                                <label class="form-label" style="font-size: 0.65rem;">Staff Category</label>
                                <select name="staff_category" id="staff_category" class="form-select form-select-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 1.4rem 0.2rem 0.3rem;">
                                    <option value="">Select Category</option>
                                    <option value="academic" {{ old('staff_category') == 'academic' ? 'selected' : '' }}>Academic</option>
                                    <option value="administrative" {{ old('staff_category') == 'administrative' ? 'selected' : '' }}>Administrative</option>
                                    <option value="supporting" {{ old('staff_category') == 'supporting' ? 'selected' : '' }}>Supporting</option>
                                </select>
                                <small class="text-muted" style="font-size: 0.6rem;">Suggested from role, but you can change it</small>
                            </div>
                        </div>
                        
                        <!-- Location Section -->
                        <div class="border rounded-sm p-2 mt-2 mb-2" style="background: #f8fafc;">
                            <strong class="d-block mb-1" style="font-size: 0.7rem;"><i class="ti ti-map-pin me-1"></i> Location Assignment</strong>
                            
                            @if($isSuperAdmin)
                            <div class="mb-1">
                                <label class="form-label" style="font-size: 0.65rem;">University <span class="text-danger">*</span></label>
                                <select id="university_id" name="university_id" class="form-select form-select-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;" required>
                                    <option value="">Select University</option>
                                    @foreach($universities as $uni)
                                    <option value="{{ $uni->id }}">{{ $uni->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-1">
                                <label class="form-label" style="font-size: 0.65rem;">Campus <span class="text-danger">*</span></label>
                                <select id="campus_id" name="campus_id" class="form-select form-select-sm rounded-sm" disabled style="font-size: 0.7rem; padding: 0.2rem 0.3rem;" required>
                                    <option value="">Select University First</option>
                                </select>
                            </div>
                            @else
                            <div class="mb-1">
                                <label class="form-label" style="font-size: 0.65rem;">Institute</label>
                                <div class="form-control form-control-sm rounded-sm bg-light" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;">
                                    {{ auth()->user()->university->name ?? 'Your institute' }}
                                </div>
                                <small class="text-muted" style="font-size: 0.6rem;">Every staff member you add belongs to your own institute.</small>
                            </div>

                            <div class="mb-1">
                                <label class="form-label" style="font-size: 0.65rem;">Campus <span class="text-danger">*</span></label>
                                <select id="campus_id" name="campus_id" class="form-select form-select-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;" required>
                                    <option value="">Select Campus</option>
                                    @foreach($campuses as $campus)
                                    <option value="{{ $campus->id }}">{{ $campus->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @endif
                            
                            <div class="mb-1">
                                <label class="form-label" style="font-size: 0.65rem;">School/Faculty</label>
                                <select id="school_id" name="school_id" class="form-select form-select-sm rounded-sm" disabled style="font-size: 0.7rem; padding: 0.2rem 0.3rem;">
                                    <option value="">Select Campus First</option>
                                </select>
                            </div>
                            
                            <div class="mb-1">
                                <label class="form-label" style="font-size: 0.65rem;">Department <span class="text-danger">*</span></label>
                                <select id="department_id" name="department_id" class="form-select form-select-sm rounded-sm" disabled style="font-size: 0.7rem; padding: 0.2rem 0.3rem;" required>
                                    <option value="">Select School First</option>
                                </select>
                            </div>
                        </div>
                        
                        <x-alert-info>
    <strong>Temporary Password:</strong> <code>Temp@123</code> (user will be required to change on first login)
</x-alert-info>
                        
                        <div class="d-flex justify-content-end gap-1 mt-2">
                            <a href="{{ route('staff.index') }}" class="btn btn-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.25rem 0.5rem;">Cancel</a>
                            <button type="submit" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.25rem 0.5rem;">
                                <i class="ti ti-device-floppy me-1"></i> Create Staff
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Info Card -->
        <div class="col-lg-5">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-info-circle text-info me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Staff Creation Guide</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <div class="mb-2">
                        <div class="fw-semibold" style="font-size: 0.7rem;"><i class="ti ti-user me-1 text-primary"></i> Staff Categories</div>
                        <ul class="mb-0 ps-3" style="font-size: 0.65rem; color: #555;">
                            <li><strong>Academic</strong> - Teaching staff, professors, lecturers</li>
                            <li><strong>Administrative</strong> - Office staff, HR, finance</li>
                            <li><strong>Supporting</strong> - Technical, IT, library, maintenance</li>
                        </ul>
                    </div>
                    
                    <div class="mb-2">
                        <div class="fw-semibold" style="font-size: 0.7rem;"><i class="ti ti-shield me-1 text-primary"></i> Roles & Permissions</div>
                        <ul class="mb-0 ps-3" style="font-size: 0.65rem; color: #555;">
                            <li><strong>Staff</strong> - Basic leave requests and tracking</li>
                            <li><strong>HOD</strong> - Approve departmental leave requests</li>
                            <li><strong>HR Officer</strong> - Final approvals and reports</li>
                            <li><strong>Admin</strong> - Full system control</li>
                        </ul>
                    </div>
                    
                    <x-alert-secondary>
    <span>New staff will receive a welcome email with their temporary password.</span>
</x-alert-secondary>
                </div>
            </div>
            
            <!-- Quick Stats -->
            <div class="card rounded-sm mt-2">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-chart-bar text-success me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">System Statistics</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="fw-bold text-primary" style="font-size: 1.1rem;">{{ \App\Models\User::count() }}</div>
                            <div class="text-muted" style="font-size: 0.6rem;">Total Staff</div>
                        </div>
                        <div class="col-6">
                            <div class="fw-bold text-success" style="font-size: 1.1rem;">{{ \App\Models\Department::count() }}</div>
                            <div class="text-muted" style="font-size: 0.6rem;">Departments</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

        <!-- Category suggestion script -->
<script>
// Suggests a category from the chosen role, but never overwrites a value
// the admin already picked themselves - the category select stays a free
// choice, not something the role locks in.
const staffCategorySelect = document.getElementById('staff_category');
let categoryTouchedByUser = false;
staffCategorySelect.addEventListener('change', function() {
    categoryTouchedByUser = true;
});

document.querySelector('select[name="role"]').addEventListener('change', function() {
    if (categoryTouchedByUser) {
        return;
    }

    const role = this.value;
    let category = '';

    if (role === 'hod' || role === 'staff') {
        category = 'academic';
    } else if (role === 'hr-officer' || role === 'admin') {
        category = 'administrative';
    }

    staffCategorySelect.value = category;
});

// Cascading selects - UPDATED URLs
// A plain admin has no University select at all (they're pinned to their
// own institute server-side), so this only wires up for super-admin.
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

document.getElementById('university_id')?.addEventListener('change', function() {
    const universityId = this.value;
    const campusSelect = document.getElementById('campus_id');
    const schoolSelect = document.getElementById('school_id');
    const deptSelect = document.getElementById('department_id');

    campusSelect.innerHTML = '<option value="">Select Campus</option>';
    schoolSelect.innerHTML = '<option value="">Select School</option>';
    deptSelect.innerHTML = '<option value="">Select Department</option>';
    campusSelect.disabled = true;
    schoolSelect.disabled = true;
    deptSelect.disabled = true;

    if (universityId) {
        fetch(`/staff/campuses/${universityId}`)
            .then(res => res.json())
            .then(data => {
                data.forEach(campus => {
                    campusSelect.innerHTML += `<option value="${campus.id}">${campus.name}</option>`;
                });
                campusSelect.disabled = false;
            });
    }
});

document.getElementById('campus_id').addEventListener('change', function() {
    const campusId = this.value;
    const schoolSelect = document.getElementById('school_id');
    const deptSelect = document.getElementById('department_id');

    schoolSelect.innerHTML = '<option value="">Select School</option>';
    deptSelect.innerHTML = '<option value="">Select Department</option>';
    schoolSelect.disabled = true;
    deptSelect.disabled = true;

    if (campusId) {
        fetch(`/staff/schools/${campusId}`)
            .then(res => res.json())
            .then(data => {
                data.forEach(school => {
                    schoolSelect.innerHTML += `<option value="${school.id}">${school.name}</option>`;
                });
                schoolSelect.disabled = false;
                loadDepartments(campusId, schoolSelect.value || '', null);
            });
    }
});

document.getElementById('school_id').addEventListener('change', function() {
    const campusId = document.getElementById('campus_id').value;
    const schoolId = this.value;
    const selectedDeptId = document.getElementById('department_id').value;
    loadDepartments(campusId, schoolId, selectedDeptId);
});
</script>
@endsection