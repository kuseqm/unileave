@extends('layouts.master')

@section('title', 'Role Management')

@section('content')
<div class="container-fluid px-2">
    
    <!-- Breadcrumb -->
    <x-breadcrumb :items="[
        ['name' => 'Role Management', 'url' => '#']
    ]" />
    
    <!-- Page Header -->
    <x-page-header title="ROLE MANAGEMENT" description="Manage system roles and user assignments">
        <a href="{{ route('admin.roles.assign') }}" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
            <i class="ti ti-user-plus me-1"></i> Assign Role
        </a>
        <a href="{{ route('admin.roles.create') }}" class="btn btn-outline-primary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
            <i class="ti ti-plus me-1"></i> Create Role
        </a>
    </x-page-header>
    
    <!-- Stats Cards -->
    <div class="row g-2 mb-3">
        <x-stats-card value="{{ $totalRoles ?? $roles->total() }}" label="Total Roles" subtext="system roles" />
        <x-stats-card value="{{ $totalPermissions ?? 0 }}" label="Permissions" subtext="available" color="info" />
        <x-stats-card value="{{ $totalUsers ?? 0 }}" label="Total Users" subtext="registered" color="success" />
        <x-stats-card value="{{ $usersWithRoles ?? 0 }}" label="Users with Roles" subtext="assigned" color="warning" />
    </div>
    
    <!-- Alerts -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show py-1 px-2 mb-2 rounded-sm" style="font-size: 0.7rem;" role="alert">
        <i class="ti ti-check-circle me-1"></i> {{ session('success') }}
        <button type="button" class="btn-close py-0" style="font-size: 0.6rem;" data-bs-dismiss="alert"></button>
    </div>
    @endif
    
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show py-1 px-2 mb-2 rounded-sm" style="font-size: 0.7rem;" role="alert">
        <i class="ti ti-alert-circle me-1"></i> {{ session('error') }}
        <button type="button" class="btn-close py-0" style="font-size: 0.6rem;" data-bs-dismiss="alert"></button>
    </div>
    @endif
    
    <!-- Roles Table -->
    <div class="card rounded-sm mb-2">
        <div class="card-header py-1 px-2 bg-light">
            <div class="d-flex align-items-center">
                <i class="ti ti-shield text-primary me-1" style="font-size: 0.8rem;"></i>
                <span style="font-size: 0.7rem; font-weight: 600;">System Roles</span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm mb-0" style="font-size: 0.75rem;">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-2 py-1">Role Name</th>
                            <th class="py-1">Guard</th>
                            <th class="py-1">Users</th>
                            <th class="pe-2 py-1" width="180">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($roles as $role)
                        <tr>
                            <td class="ps-2 py-1">
                                <div class="d-flex align-items-center gap-1">
                                    <i class="ti ti-shield text-primary" style="font-size: 0.8rem;"></i>
                                    <span class="fw-semibold">{{ ucfirst($role->name) }}</span>
                                </div>
                            </td>
                            <td class="py-1">{{ $role->guard_name }}</td>
                            <td class="py-1">
                                <span class="badge bg-secondary-subtle text-secondary rounded-sm" style="font-size: 0.6rem;">{{ $role->users->count() }} users</span>
                            </td>
                            <td class="pe-2 py-1">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.roles.edit', $role->id) }}" class="btn btn-sm btn-outline-primary rounded-sm" title="Edit Permissions" style="padding: 0.15rem 0.3rem;">
                                        <i class="ti ti-edit" style="font-size: 0.7rem;"></i>
                                    </a>
                                    @if(!in_array($role->name, ['admin', 'staff', 'hod', 'dean', 'dvca', 'dvc-pfa', 'acting-dvc', 'hr-officer', 'hr-manager']))
                                    <button type="button" class="btn btn-sm btn-outline-danger rounded-sm" onclick="showDeleteModal('{{ $role->name }}', {{ $role->id }})" title="Delete Role" style="padding: 0.15rem 0.3rem;">
                                        <i class="ti ti-trash" style="font-size: 0.7rem;"></i>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer py-1 px-2">
            <x-pagination :items="$roles" :perPageOptions="[10, 25, 50, 100]" />
        </div>
    </div>
    
    <!-- Users by Role Table -->
    <div class="card rounded-sm">
        <div class="card-header py-1 px-2 bg-light">
            <div class="d-flex align-items-center">
                <i class="ti ti-users text-primary me-1" style="font-size: 0.8rem;"></i>
                <span style="font-size: 0.7rem; font-weight: 600;">Users by Role</span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm mb-0" style="font-size: 0.75rem;">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-2 py-1">Name</th>
                            <th class="py-1">Email</th>
                            <th class="py-1">Staff ID</th>
                            <th class="py-1">Current Role</th>
                            <th class="pe-2 py-1" width="100">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td class="ps-2 py-1">
                                <div class="d-flex align-items-center gap-1">
                                    <div class="rounded-circle bg-light d-flex align-items-center justify-content-center" style="width: 24px; height: 24px;">
                                        <span class="fw-semibold" style="font-size: 0.6rem;">
                                            {{ strtoupper(substr($user->first_name, 0, 1)) }}{{ strtoupper(substr($user->last_name, 0, 1)) }}
                                        </span>
                                    </div>
                                    <span class="fw-semibold">{{ $user->first_name }} {{ $user->last_name }}</span>
                                </div>
                            </td>
                            <td class="py-1">{{ $user->email }}</td>
                            <td class="py-1">{{ $user->staff_id }}</td>
                            <td class="py-1">
                                @foreach($user->roles as $role)
                                    <span class="badge bg-primary-subtle text-primary rounded-sm" style="font-size: 0.6rem;">{{ ucfirst($role->name) }}</span>
                                @endforeach
                            </td>
                            <td class="pe-2 py-1">
                                <button type="button" class="btn btn-sm btn-outline-warning rounded-sm" data-bs-toggle="modal" data-bs-target="#changeRoleModal" data-user-id="{{ $user->id }}" data-user-name="{{ $user->first_name }} {{ $user->last_name }}" data-user-role="{{ $user->roles->first()->name ?? '' }}" title="Change Role" style="padding: 0.15rem 0.3rem;">
                                    <i class="ti ti-edit" style="font-size: 0.7rem;"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Change Role Modal -->
<div class="modal fade" id="changeRoleModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-sm">
            <div class="modal-header py-1 px-2">
                <h5 class="modal-title" style="font-size: 0.8rem;">Change User Role</h5>
                <button type="button" class="btn-close py-0" style="font-size: 0.6rem;" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.roles.assign.store') }}" method="POST">
                @csrf
                <input type="hidden" name="user_id" id="modal_user_id">
                <div class="modal-body py-2 px-2">
                    <p style="font-size: 0.75rem;">Change role for: <strong id="modal_user_name"></strong></p>
                    <div class="mb-1">
                        <label class="form-label" style="font-size: 0.65rem;">Select New Role</label>
                        <select name="role" class="form-select form-select-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;" required>
                            @foreach($allRoles as $role)
                            <option value="{{ $role->name }}" id="role_option_{{ $role->name }}">{{ ucfirst($role->name) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer py-1 px-2">
                    <button type="button" class="btn btn-secondary btn-sm rounded-sm" data-bs-dismiss="modal" style="font-size: 0.7rem;">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem;">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Role Modal -->
<div class="modal fade" id="deleteRoleModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-sm">
            <div class="modal-header py-1 px-2">
                <h5 class="modal-title" style="font-size: 0.8rem;">Confirm Delete</h5>
                <button type="button" class="btn-close py-0" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body py-2 px-2">
                <p style="font-size: 0.75rem;">Are you sure you want to delete the role <strong id="deleteRoleName"></strong>?</p>
                <p class="text-danger mb-0" style="font-size: 0.7rem;">This action cannot be undone.</p>
            </div>
            <div class="modal-footer py-1 px-2">
                <button type="button" class="btn btn-secondary btn-sm rounded-sm" data-bs-dismiss="modal" style="font-size: 0.7rem;">Cancel</button>
                <a href="#" id="deleteRoleConfirmBtn" class="btn btn-danger btn-sm rounded-sm" style="font-size: 0.7rem;">Delete</a>
            </div>
        </div>
    </div>
</div>

<script>
let deleteRoleId = '';
let deleteRoleName = '';

function showDeleteModal(roleName, roleId) {
    deleteRoleId = roleId;
    deleteRoleName = roleName;
    document.getElementById('deleteRoleName').innerText = roleName;
    document.getElementById('deleteRoleConfirmBtn').href = `/admin/roles/delete/${roleId}`;
    new bootstrap.Modal(document.getElementById('deleteRoleModal')).show();
}

// Change Role Modal
const changeRoleModal = document.getElementById('changeRoleModal');
if (changeRoleModal) {
    changeRoleModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const userId = button.getAttribute('data-user-id');
        const userName = button.getAttribute('data-user-name');
        const userRole = button.getAttribute('data-user-role');
        
        document.getElementById('modal_user_id').value = userId;
        document.getElementById('modal_user_name').textContent = userName;
        
        // Pre-select current role
        if (userRole) {
            const roleSelect = document.querySelector('#changeRoleModal select[name="role"]');
            for(let i = 0; i < roleSelect.options.length; i++) {
                if(roleSelect.options[i].value === userRole) {
                    roleSelect.options[i].selected = true;
                    break;
                }
            }
        }
    });
}
</script>
@endsection