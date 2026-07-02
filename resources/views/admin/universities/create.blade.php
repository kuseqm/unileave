@extends('layouts.master')

@section('title', 'Add University')

@section('content')
<div class="container-fluid px-2">
    
    <!-- Breadcrumb -->
    <x-breadcrumb :items="[
        ['name' => 'Universities', 'url' => route('admin.universities.index')],
        ['name' => 'Add University', 'url' => '#']
    ]" />
    
    <!-- Page Header -->
    <x-page-header title="ADD UNIVERSITY" description="Create a new university/institution in the system">
        <a href="{{ route('admin.universities.index') }}" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
            <i class="ti ti-arrow-left me-1"></i> Back
        </a>
    </x-page-header>
    
    <div class="row g-2">
        <!-- Main Form -->
        <div class="col-lg-7">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-building text-primary me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">University Information</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <form action="{{ route('admin.universities.store') }}" method="POST">
                        @csrf
                        
                        <div class="row g-1">
                            <div class="col-md-6 mb-1">
                                <label class="form-label" style="font-size: 0.65rem;">University Name <span class="text-danger">*</span></label>
                                <input type="text" 
                                       name="name" 
                                       class="form-control form-control-sm rounded-sm @error('name') is-invalid @enderror" 
                                       value="{{ old('name') }}" 
                                       placeholder="e.g., University of Dar es Salaam"
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
                                       placeholder="e.g., UDSM"
                                       style="font-size: 0.7rem; padding: 0.2rem 0.3rem;"
                                       required>
                                @error('code')
                                    <div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>
                                @enderror
                                <small class="text-muted" style="font-size: 0.6rem;">Short code/abbreviation</small>
                            </div>
                        </div>
                        
                        <div class="row g-1">
                            <div class="col-md-6 mb-1">
                                <label class="form-label" style="font-size: 0.65rem;">GPS Latitude</label>
                                <input type="text" 
                                       name="gps_lat" 
                                       class="form-control form-control-sm rounded-sm @error('gps_lat') is-invalid @enderror" 
                                       value="{{ old('gps_lat') }}" 
                                       placeholder="e.g., -6.7800"
                                       style="font-size: 0.7rem; padding: 0.2rem 0.3rem;">
                                @error('gps_lat')
                                    <div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>
                                @enderror
                                <small class="text-muted" style="font-size: 0.6rem;">Decimal format</small>
                            </div>
                            <div class="col-md-6 mb-1">
                                <label class="form-label" style="font-size: 0.65rem;">GPS Longitude</label>
                                <input type="text" 
                                       name="gps_lng" 
                                       class="form-control form-control-sm rounded-sm @error('gps_lng') is-invalid @enderror" 
                                       value="{{ old('gps_lng') }}" 
                                       placeholder="e.g., 39.2020"
                                       style="font-size: 0.7rem; padding: 0.2rem 0.3rem;">
                                @error('gps_lng')
                                    <div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>
                                @enderror
                                <small class="text-muted" style="font-size: 0.6rem;">Decimal format</small>
                            </div>
                        </div>
                        
                        <div class="mb-1">
                            <label class="form-label" style="font-size: 0.65rem;">Address</label>
                            <textarea name="address" 
                                      class="form-control form-control-sm rounded-sm @error('address') is-invalid @enderror" 
                                      rows="2"
                                      style="font-size: 0.7rem; padding: 0.2rem 0.3rem;"
                                      placeholder="Full address of the university">{{ old('address') }}</textarea>
                            @error('address')
                                <div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="row g-1">
                            <div class="col-md-6 mb-1">
                                <label class="form-label" style="font-size: 0.65rem;">Phone</label>
                                <input type="text" 
                                       name="phone" 
                                       class="form-control form-control-sm rounded-sm @error('phone') is-invalid @enderror" 
                                       value="{{ old('phone') }}" 
                                       placeholder="e.g., +255 22 2410500"
                                       style="font-size: 0.7rem; padding: 0.2rem 0.3rem;">
                                @error('phone')
                                    <div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-1">
                                <label class="form-label" style="font-size: 0.65rem;">Email</label>
                                <input type="email" 
                                       name="email" 
                                       class="form-control form-control-sm rounded-sm @error('email') is-invalid @enderror" 
                                       value="{{ old('email') }}" 
                                       placeholder="info@university.ac.tz"
                                       style="font-size: 0.7rem; padding: 0.2rem 0.3rem;">
                                @error('email')
                                    <div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>
                                @enderror
                            </div>
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
                            <a href="{{ route('admin.universities.index') }}" class="btn btn-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.25rem 0.5rem;">Cancel</a>
                            <button type="submit" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.25rem 0.5rem;">
                                <i class="ti ti-device-floppy me-1"></i> Create University
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Right Column: Next Steps Card -->
        <div class="col-lg-5">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-checks text-success me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Next Steps</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <p class="text-muted mb-2" style="font-size: 0.65rem;">After creating the university, you can continue setting up the institution structure:</p>
                    
                    <div class="vstack gap-2">
                        <div class="d-flex align-items-start gap-2 p-1 rounded">
                            <div class="rounded-circle bg-primary-light p-1" style="background: rgba(37, 99, 235, 0.1);">
                                <i class="ti ti-map-pin text-primary" style="font-size: 0.8rem;"></i>
                            </div>
                            <div>
                                <strong class="d-block" style="font-size: 0.7rem;">Add Campuses</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">Create campuses/locations under this university</small>
                                <div class="mt-1">
                                    <a href="{{ route('admin.campuses.create') }}" class="btn btn-sm btn-outline-primary rounded-sm" style="font-size: 0.6rem; padding: 0.15rem 0.3rem;">
                                        <i class="ti ti-plus me-1"></i> Add Campus
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex align-items-start gap-2 p-1 rounded">
                            <div class="rounded-circle bg-info-light p-1">
                                <i class="ti ti-school text-info" style="font-size: 0.8rem;"></i>
                            </div>
                            <div>
                                <strong class="d-block" style="font-size: 0.7rem;">Add Schools/Faculties</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">Create schools or faculties under campuses</small>
                                <div class="mt-1">
                                    <button class="btn btn-sm btn-outline-info rounded-sm" style="font-size: 0.6rem; padding: 0.15rem 0.3rem;" disabled>
                                        <i class="ti ti-plus me-1"></i> Coming Soon
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex align-items-start gap-2 p-1 rounded">
                            <div class="rounded-circle bg-success-light p-1">
                                <i class="ti ti-users text-success" style="font-size: 0.8rem;"></i>
                            </div>
                            <div>
                                <strong class="d-block" style="font-size: 0.7rem;">Assign Staff</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">Add staff members and assign to departments</small>
                                <div class="mt-1">
                                    <a href="{{ route('admin.staff.create') }}" class="btn btn-sm btn-outline-success rounded-sm" style="font-size: 0.6rem; padding: 0.15rem 0.3rem;">
                                        <i class="ti ti-plus me-1"></i> Add Staff
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <hr class="my-2">
                    
                    <div class="alert alert-light border mb-0 py-1 px-2 rounded-sm">
                        <div class="d-flex align-items-start gap-1">
                            <i class="ti ti-info-circle text-primary mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">Pro Tip</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">Hierarchy: University → Campus → School/Faculty → Department → Staff</small>
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