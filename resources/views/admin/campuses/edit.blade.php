@extends('layouts.master')

@section('title', 'Edit Campus')

@section('content')
<div class="container-fluid px-2">
    
    <!-- Breadcrumb -->
    <x-breadcrumb :items="[
        ['name' => 'Campuses', 'url' => route('admin.campuses.index')],
        ['name' => 'Edit Campus', 'url' => '#']
    ]" />
    
    <!-- Page Header -->
    <x-page-header title="EDIT CAMPUS" description="Update campus information">
        <a href="{{ route('admin.campuses.index') }}" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
            <i class="ti ti-arrow-left me-1"></i> Back
        </a>
    </x-page-header>
    
    <div class="row g-2">
        <!-- Main Form -->
        <div class="col-lg-7">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-map-pin text-primary me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Edit Campus: {{ $campus->name }}</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <form action="{{ route('admin.campuses.update', $campus->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-1">
                            <label class="form-label" style="font-size: 0.65rem;">University <span class="text-danger">*</span></label>
                            <select name="university_id" class="form-select form-select-sm rounded-sm @error('university_id') is-invalid @enderror" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;" required>
                                <option value="">Select University</option>
                                @foreach($universities as $uni)
                                <option value="{{ $uni->id }}" {{ old('university_id', $campus->university_id) == $uni->id ? 'selected' : '' }}>{{ $uni->name }}</option>
                                @endforeach
                            </select>
                            @error('university_id')
                                <div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="row g-1">
                            <div class="col-md-6 mb-1">
                                <label class="form-label" style="font-size: 0.65rem;">Campus Name <span class="text-danger">*</span></label>
                                <input type="text" 
                                       name="name" 
                                       class="form-control form-control-sm rounded-sm @error('name') is-invalid @enderror" 
                                       value="{{ old('name', $campus->name) }}" 
                                       style="font-size: 0.7rem; padding: 0.2rem 0.3rem;"
                                       required>
                                @error('name')
                                    <div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-1">
                                <label class="form-label" style="font-size: 0.65rem;">Campus Code <span class="text-danger">*</span></label>
                                <input type="text" 
                                       name="code" 
                                       class="form-control form-control-sm rounded-sm @error('code') is-invalid @enderror" 
                                       value="{{ old('code', $campus->code) }}" 
                                       style="font-size: 0.7rem; padding: 0.2rem 0.3rem;"
                                       required>
                                @error('code')
                                    <div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row g-1">
                            <div class="col-md-6 mb-1">
                                <label class="form-label" style="font-size: 0.65rem;">GPS Latitude</label>
                                <input type="text" 
                                       name="gps_lat" 
                                       class="form-control form-control-sm rounded-sm @error('gps_lat') is-invalid @enderror" 
                                       value="{{ old('gps_lat', $campus->gps_lat) }}" 
                                       placeholder="e.g., -6.7800"
                                       style="font-size: 0.7rem; padding: 0.2rem 0.3rem;">
                                @error('gps_lat')
                                    <div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-1">
                                <label class="form-label" style="font-size: 0.65rem;">GPS Longitude</label>
                                <input type="text" 
                                       name="gps_lng" 
                                       class="form-control form-control-sm rounded-sm @error('gps_lng') is-invalid @enderror" 
                                       value="{{ old('gps_lng', $campus->gps_lng) }}" 
                                       placeholder="e.g., 39.2020"
                                       style="font-size: 0.7rem; padding: 0.2rem 0.3rem;">
                                @error('gps_lng')
                                    <div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="mb-1">
                            <label class="form-label" style="font-size: 0.65rem;">Address</label>
                            <textarea name="address" 
                                      class="form-control form-control-sm rounded-sm @error('address') is-invalid @enderror" 
                                      rows="2"
                                      style="font-size: 0.7rem; padding: 0.2rem 0.3rem;">{{ old('address', $campus->address) }}</textarea>
                            @error('address')
                                <div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-1">
                            <label class="form-label" style="font-size: 0.65rem;">Status</label>
                            <select name="status" class="form-select form-select-sm rounded-sm @error('status') is-invalid @enderror" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;">
                                <option value="active" {{ old('status', $campus->status) == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status', $campus->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="d-flex justify-content-end gap-1 mt-2">
                            <a href="{{ route('admin.campuses.index') }}" class="btn btn-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.25rem 0.5rem;">Cancel</a>
                            <button type="submit" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.25rem 0.5rem;">
                                <i class="ti ti-device-floppy me-1"></i> Update Campus
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Right Column: Info & Stats -->
        <div class="col-lg-5">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-chart-bar text-success me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Campus Statistics</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="fw-bold text-info" style="font-size: 1rem;">{{ $campus->departments->count() }}</div>
                            <div class="text-muted" style="font-size: 0.6rem;">Department(s)</div>
                        </div>
                        <div class="col-6">
                            <div class="fw-bold text-success" style="font-size: 1rem;">{{ $campus->staff->count() }}</div>
                            <div class="text-muted" style="font-size: 0.6rem;">Staff Member(s)</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card rounded-sm mt-2">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-school text-primary me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Quick Actions</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <div class="d-grid gap-1">
                        <a href="{{ route('admin.departments.create') }}?campus_id={{ $campus->id }}" class="btn btn-sm btn-outline-primary rounded-sm" style="font-size: 0.7rem; padding: 0.25rem 0.5rem;">
                            <i class="ti ti-plus me-1"></i> Add Department
                        </a>
                        <a href="{{ route('staff.create') }}?campus_id={{ $campus->id }}" class="btn btn-sm btn-outline-success rounded-sm" style="font-size: 0.7rem; padding: 0.25rem 0.5rem;">
                            <i class="ti ti-user-plus me-1"></i> Add Staff Member
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection