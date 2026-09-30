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
    
    <div class="row g-2">
        <div class="col-lg-8">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-plus text-primary me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">New Role</span>
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
                            <x-permission-checklist :permissions="$permissions" />
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

        <div class="col-lg-4">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-info-circle text-info me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Information</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <div class="mb-2">
                        <div class="fw-semibold" style="font-size: 0.7rem;"><i class="ti ti-clock me-1 text-primary"></i> Process Steps</div>
                        <ul class="mb-0 ps-3" style="font-size: 0.65rem; color: #555;">
                            <li>1. Choose a role name (lowercase, hyphens only)</li>
                            <li>2. Select the permissions this role should have</li>
                            <li>3. Click "Create Role" to save it</li>
                            <li>4. Assign it to staff members afterwards from Assign Role</li>
                        </ul>
                    </div>

                    <x-alert-light class="mb-0 py-1 px-2 rounded-sm">

                        <div class="d-flex align-items-start gap-1">
                            <i class="ti ti-tips text-warning mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">Important Notes</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">• Creating a role here does <strong>not</strong> assign it to anyone yet</small><br>
                                <small class="text-muted" style="font-size: 0.6rem;">• To give this role to a staff member, use <a href="{{ route('admin.roles.assign') }}">Assign Role</a> instead</small><br>
                                <small class="text-muted" style="font-size: 0.6rem;">• You can always come back and change a role's permissions later from Role Management</small><br>
                                <small class="text-muted" style="font-size: 0.6rem;">• Permissions are grouped by action (View, Manage, Approve, etc.) to make them easier to scan</small>
                            </div>
                        
</x-alert-light>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection