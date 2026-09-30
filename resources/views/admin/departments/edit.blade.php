@extends('layouts.master')

@section('title', 'Edit Department')

@section('content')
<div class="container-fluid px-2">
    
    <!-- Breadcrumb -->
    <x-breadcrumb :items="[
        ['name' => 'Departments', 'url' => route('admin.departments.index')],
        ['name' => 'Edit Department', 'url' => '#']
    ]" />
    
    <!-- Page Header -->
    <x-page-header title="EDIT DEPARTMENT" description="Update department information">
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
                        <span style="font-size: 0.7rem; font-weight: 600;">Edit Department: {{ $department->name }}</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <form action="{{ route('admin.departments.update', $department->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-1">
                            <input type="hidden" name="campus_id" value="{{ $department->campus_id }}">
                            <label class="form-label" style="font-size: 0.65rem;">School/Faculty <span class="text-danger">*</span></label>
                            <select name="school_id" class="form-select form-select-sm rounded-sm @error('school_id') is-invalid @enderror" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;" required>
                                <option value="">Select School/Faculty</option>
                                @foreach($schools as $school)
                                <option value="{{ $school->id }}" {{ old('school_id', $department->school_id) == $school->id ? 'selected' : '' }}>
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
                                       value="{{ old('name', $department->name) }}" 
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
                                       value="{{ old('code', $department->code) }}" 
                                       style="font-size: 0.7rem; padding: 0.2rem 0.3rem;"
                                       required>
                                @error('code')
                                    <div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="mb-1">
                            <label class="form-label" style="font-size: 0.65rem;">Head of Department (HOD)</label>
                            <select name="hod_staff_id" class="form-select form-select-sm rounded-sm @error('hod_staff_id') is-invalid @enderror" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;">
                                <option value="">Select HOD</option>
                                @foreach($hodCandidates as $hod)
                                <option value="{{ $hod->id }}" {{ old('hod_staff_id', $department->hod_staff_id) == $hod->id ? 'selected' : '' }}>
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
                                <option value="{{ $unitHead->id }}" {{ old('unit_head_staff_id', $department->unit_head_staff_id) == $unitHead->id ? 'selected' : '' }}>
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
                                      style="font-size: 0.7rem; padding: 0.2rem 0.3rem;">{{ old('description', $department->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-1">
                            <label class="form-label" style="font-size: 0.65rem;">Status</label>
                            <select name="status" class="form-select form-select-sm rounded-sm @error('status') is-invalid @enderror" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;">
                                <option value="active" {{ old('status', $department->status) == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status', $department->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="d-flex justify-content-end gap-1 mt-2">
                            <a href="{{ route('admin.departments.index') }}" class="btn btn-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.25rem 0.5rem;">Cancel</a>
                            <button type="submit" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.25rem 0.5rem;">
                                <i class="ti ti-device-floppy me-1"></i> Update Department
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Right Column: Statistics & Info -->
        <div class="col-lg-5">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-chart-bar text-success me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Department Statistics</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="fw-bold text-info" style="font-size: 1rem;">{{ $department->staff->count() }}</div>
                            <div class="text-muted" style="font-size: 0.6rem;">Staff Member(s)</div>
                        </div>
                        <div class="col-6">
                            <div class="fw-bold text-primary" style="font-size: 0.9rem;">{{ $department->school->name ?? 'N/A' }}</div>
                            <div class="text-muted" style="font-size: 0.6rem;">Parent School</div>
                        </div>
                    </div>
                    <hr class="my-1">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="fw-bold text-success" style="font-size: 0.85rem;">
                                {{ $department->hod ? $department->hod->full_name : 'Not Assigned' }}
                            </div>
                            <div class="text-muted" style="font-size: 0.55rem;">Current HOD</div>
                        </div>
                        <div class="col-6">
                            <div class="fw-bold text-info" style="font-size: 0.85rem;">
                                {{ $department->unitHead ? $department->unitHead->full_name : 'Not Assigned' }}
                            </div>
                            <div class="text-muted" style="font-size: 0.55rem;">Current Unit Head</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card rounded-sm mt-2">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-info-circle text-info me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Department Information</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <x-alert-light class="mb-0 py-1 px-2 rounded-sm">

                        <div class="d-flex align-items-start gap-1">
                            <i class="ti ti-tips text-warning mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">About this Department</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">• Created: {{ $department->created_at->format('d M Y') }}</small><br>
                                <small class="text-muted" style="font-size: 0.6rem;">• Last updated: {{ $department->updated_at->format('d M Y') }}</small><br>
                                <small class="text-muted" style="font-size: 0.6rem;">• HOD reviews academic staff leave requests</small><br>
                                <small class="text-muted" style="font-size: 0.6rem;">• Unit Head reviews non-academic staff leave requests</small>
                            </div>
                        
</x-alert-light>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection