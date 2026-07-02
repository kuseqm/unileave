@extends('layouts.master')

@section('title', 'Create New Role')

@section('content')
<div class="container-fluid px-2">
    
    <!-- Breadcrumb -->
    <x-breadcrumb :items="[
        ['name' => 'Role Management', 'url' => route('admin.roles.index')],
        ['name' => 'Create Role', 'url' => '#']
    ]" />
    
    <!-- Page Header -->
    <x-page-header title="CREATE NEW ROLE" description="Create a new system role with permissions">
        <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
            <i class="ti ti-arrow-left me-1"></i> Back
        </a>
    </x-page-header>
    
    <div class="row g-2 justify-content-center">
        <div class="col-md-8">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-plus text-primary me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Role Information</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <form action="{{ route('admin.roles.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-1">
                            <label class="form-label" style="font-size: 0.65rem;">Role Name <span class="text-danger">*</span></label>
                            <input type="text" 
                                   name="name" 
                                   class="form-control form-control-sm rounded-sm @error('name') is-invalid @enderror" 
                                   placeholder="Enter role name (e.g., supervisor, manager)"
                                   style="font-size: 0.7rem; padding: 0.2rem 0.3rem;"
                                   required>
                            @error('name')
                                <div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>
                            @enderror
                            <small class="text-muted" style="font-size: 0.6rem;">Use lowercase letters and hyphens only (e.g., department-head)</small>
                        </div>
                        
                        <div class="mb-1">
                            <label class="form-label" style="font-size: 0.65rem;">Permissions</label>
                            <div class="row g-1">
                                @php
                                    $permissions = [
                                        'apply leave',
                                        'apply permission',
                                        'approve hod',
                                        'approve dean',
                                        'approve dvca',
                                        'approve dvc-pfa',
                                        'approve hr',
                                        'view reports',
                                        'import staff',
                                        'manage users',
                                    ];
                                @endphp
                                
                                @foreach($permissions as $permission)
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input type="checkbox" 
                                               name="permissions[]" 
                                               value="{{ $permission }}" 
                                               class="form-check-input"
                                               id="perm_{{ Str::slug($permission) }}">
                                        <label class="form-check-label" for="perm_{{ Str::slug($permission) }}" style="font-size: 0.7rem;">
                                            {{ ucfirst($permission) }}
                                        </label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-end gap-1 mt-2">
                            <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.25rem 0.5rem;">Cancel</a>
                            <button type="submit" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.25rem 0.5rem;">
                                <i class="ti ti-device-floppy me-1"></i> Create Role
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection