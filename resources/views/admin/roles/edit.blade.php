@extends('layouts.master')

@section('title', 'Edit Role Permissions')

@section('content')
<div class="container-fluid px-2">
    
    <!-- Breadcrumb -->
    <x-breadcrumb :items="[
        ['name' => 'Role Management', 'url' => route('admin.roles.index')],
        ['name' => 'Edit Role', 'url' => '#']
    ]" />
    
    <!-- Page Header -->
    <x-page-header title="EDIT ROLE PERMISSIONS" description="Manage permissions for role: {{ ucfirst($role->name) }}">
        <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
            <i class="ti ti-arrow-left me-1"></i> Back
        </a>
    </x-page-header>
    
    <div class="row g-2">
        <div class="col-lg-8">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-edit text-primary me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Edit Role: {{ ucfirst($role->name) }}</span>
                        <span id="checked-count" class="badge bg-primary ms-auto" style="font-size: 0.65rem;">{{ count($rolePermissions) }} checked</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <form action="{{ route('admin.roles.update', $role->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-1">
                            <label class="form-label" style="font-size: 0.65rem;">Permissions for "{{ ucfirst($role->name) }}"</label>
                            <x-permission-checklist :permissions="$permissions" :checked="$rolePermissions" />
                        </div>

                        <div class="d-flex justify-content-end gap-1 mt-2">
                            <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.25rem 0.5rem;">Cancel</a>
                            <button type="submit" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.25rem 0.5rem;">
                                <i class="ti ti-device-floppy me-1"></i> Update Permissions
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
                        <div class="fw-semibold" style="font-size: 0.7rem;"><i class="ti ti-clock me-1 text-primary"></i> What This Page Does</div>
                        <ul class="mb-0 ps-3" style="font-size: 0.65rem; color: #555;">
                            <li>1. Review "{{ ucfirst($role->name) }}"'s current permissions (pre-checked below)</li>
                            <li>2. Check or uncheck permissions to change what this role can do</li>
                            <li>3. Click "Update Permissions" to save</li>
                            <li>4. Changes apply immediately to every staff member who holds this role</li>
                        </ul>
                    </div>

                    <x-alert-light class="mb-0 py-1 px-2 rounded-sm">

                        <div class="d-flex align-items-start gap-1">
                            <i class="ti ti-tips text-warning mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">Important Notes</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">• This page only changes what the role <strong>can do</strong> — it does not assign the role to anyone</small><br>
                                <small class="text-muted" style="font-size: 0.6rem;">• To give this role to a staff member, use <a href="{{ route('admin.roles.assign') }}">Assign Role</a> instead</small><br>
                                <small class="text-muted" style="font-size: 0.6rem;">• Permissions are grouped by action (View, Manage, Approve, etc.) to make them easier to scan</small><br>
                                <small class="text-muted" style="font-size: 0.6rem;">• System roles can't be deleted, but their permissions can still be edited here</small>
                            </div>
                        
</x-alert-light>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function updateCheckedCount() {
        const count = document.querySelectorAll('.permission-checkbox:checked').length;
        const badge = document.getElementById('checked-count');
        if (badge) {
            badge.textContent = count + ' checked';
        }
    }

    document.querySelectorAll('.permission-checkbox').forEach(function (cb) {
        cb.addEventListener('change', updateCheckedCount);
    });

    updateCheckedCount();
</script>
@endsection