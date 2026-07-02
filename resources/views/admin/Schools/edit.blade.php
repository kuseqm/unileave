@extends('layouts.master')

@section('title', 'Edit School/Faculty')

@section('content')
<div class="container-fluid px-2">
    
    <!-- Breadcrumb -->
    <x-breadcrumb :items="[
        ['name' => 'Schools', 'url' => route('admin.schools.index')],
        ['name' => 'Edit School', 'url' => '#']
    ]" />
    
    <!-- Page Header -->
    <x-page-header title="EDIT SCHOOL/FACULTY" description="Update school or faculty information">
        <a href="{{ route('admin.schools.index') }}" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
            <i class="ti ti-arrow-left me-1"></i> Back
        </a>
    </x-page-header>
    
    <div class="row g-2">
        <!-- Main Form -->
        <div class="col-lg-7">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-school text-primary me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Edit: {{ $school->name }}</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <form action="{{ route('admin.schools.update', $school->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-1">
                            <label class="form-label" style="font-size: 0.65rem;">Campus <span class="text-danger">*</span></label>
                            <select name="campus_id" class="form-select form-select-sm rounded-sm @error('campus_id') is-invalid @enderror" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;" required>
                                <option value="">Select Campus</option>
                                @foreach($campuses as $campus)
                                <option value="{{ $campus->id }}" {{ old('campus_id', $school->campus_id) == $campus->id ? 'selected' : '' }}>
                                    {{ $campus->name }} ({{ $campus->university->name ?? 'N/A' }})
                                </option>
                                @endforeach
                            </select>
                            @error('campus_id')
                                <div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="row g-1">
                            <div class="col-md-6 mb-1">
                                <label class="form-label" style="font-size: 0.65rem;">School/Faculty Name <span class="text-danger">*</span></label>
                                <input type="text" 
                                       name="name" 
                                       class="form-control form-control-sm rounded-sm @error('name') is-invalid @enderror" 
                                       value="{{ old('name', $school->name) }}" 
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
                                       value="{{ old('code', $school->code) }}" 
                                       style="font-size: 0.7rem; padding: 0.2rem 0.3rem;"
                                       required>
                                @error('code')
                                    <div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="mb-1">
                            <label class="form-label" style="font-size: 0.65rem;">Dean</label>
                            <select name="dean_staff_id" class="form-select form-select-sm rounded-sm @error('dean_staff_id') is-invalid @enderror" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;">
                                <option value="">Select Dean</option>
                                @foreach($staff as $dean)
                                <option value="{{ $dean->id }}" {{ old('dean_staff_id', $school->dean_staff_id) == $dean->id ? 'selected' : '' }}>
                                    {{ $dean->first_name }} {{ $dean->last_name }} ({{ $dean->staff_id }})
                                </option>
                                @endforeach
                            </select>
                            @error('dean_staff_id')
                                <div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-1">
                            <label class="form-label" style="font-size: 0.65rem;">Description</label>
                            <textarea name="description" 
                                      class="form-control form-control-sm rounded-sm @error('description') is-invalid @enderror" 
                                      rows="2"
                                      style="font-size: 0.7rem; padding: 0.2rem 0.3rem;">{{ old('description', $school->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-1">
                            <label class="form-label" style="font-size: 0.65rem;">Status</label>
                            <select name="status" class="form-select form-select-sm rounded-sm @error('status') is-invalid @enderror" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;">
                                <option value="active" {{ old('status', $school->status) == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status', $school->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="d-flex justify-content-end gap-1 mt-2">
                            <a href="{{ route('admin.schools.index') }}" class="btn btn-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.25rem 0.5rem;">Cancel</a>
                            <button type="submit" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.25rem 0.5rem;">
                                <i class="ti ti-device-floppy me-1"></i> Update School
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Right Column: Statistics -->
        <div class="col-lg-5">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-chart-bar text-success me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Statistics</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="fw-bold text-info" style="font-size: 1rem;">{{ $school->departments->count() }}</div>
                            <div class="text-muted" style="font-size: 0.6rem;">Department(s)</div>
                        </div>
                        <div class="col-6">
                            <div class="fw-bold text-success" style="font-size: 1rem;">{{ $school->staff->count() }}</div>
                            <div class="text-muted" style="font-size: 0.6rem;">Staff Member(s)</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card rounded-sm mt-2">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-folder text-primary me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Quick Actions</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <div class="d-grid gap-1">
                        <a href="{{ route('admin.departments.create') }}?school_id={{ $school->id }}" class="btn btn-sm btn-outline-primary rounded-sm" style="font-size: 0.7rem; padding: 0.25rem 0.5rem;">
                            <i class="ti ti-plus me-1"></i> Add Department
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection