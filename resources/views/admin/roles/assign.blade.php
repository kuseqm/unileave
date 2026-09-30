@extends('layouts.master')

@section('title', 'Assign Role')

@section('content')
<div class="container-fluid px-2">
    
    <!-- Breadcrumb -->
    <x-breadcrumb :items="[
        ['name' => 'Role Management', 'url' => route('admin.roles.index')],
        ['name' => 'Assign Role', 'url' => '#']
    ]" />
    
    <!-- Page Header -->
    <x-page-header title="ASSIGN ROLE" description="Assign roles to staff members">
        <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
            <i class="ti ti-arrow-left me-1"></i> Back
        </a>
    </x-page-header>
    
    <div class="row g-2">
        <div class="col-lg-8">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-user-plus text-primary me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Assign Role to Staff</span>
                    </div>
                </div>
                <div class="card-body p-2">
                    <form method="POST" action="{{ route('admin.roles.assign.store') }}">
                        @csrf
                        
                        <div class="mb-2">
                            <label class="form-label" style="font-size: 0.7rem;">Select Staff Member</label>

                            <!-- Search -->
                            <div class="row g-1 align-items-end mb-2">
                                <div class="col">
                                    <input type="text" id="staffSearchInput" value="{{ request('search') }}" autocomplete="off" class="form-control form-control-sm rounded-sm" placeholder="Search by name, email or staff ID..." style="font-size: 0.7rem; padding: 0.2rem 0.3rem;">
                                </div>
                                @if(request('search'))
                                <div class="col-auto">
                                    <a href="{{ route('admin.roles.assign') }}" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.65rem; padding: 0.25rem 0.5rem;">
                                        <i class="ti ti-refresh me-1"></i> Reset
                                    </a>
                                </div>
                                @endif
                            </div>

                            <!-- Staff List with Pagination -->
                            <div class="staff-list-container border rounded-sm" style="max-height: 400px; overflow-y: auto;">
                                @forelse($users as $user)
                                <div class="staff-item d-flex align-items-center p-2 border-bottom" style="gap: 0.75rem;">
                                    <div class="form-check">
                                        <input type="radio" name="user_id" value="{{ $user->id }}" class="form-check-input" id="user_{{ $user->id }}" required>
                                    </div>
                                    <div class="flex-grow-1">
                                        <label for="user_{{ $user->id }}" class="d-block" style="cursor: pointer;">
                                            <div style="font-size: 0.75rem;">
                                                <span class="fw-semibold">{{ $user->last_name }}, {{ $user->first_name }}</span>
                                                @if($user->middle_name)
                                                    <span class="text-muted"> {{ $user->middle_name }}</span>
                                                @endif
                                            </div>
                                            <div class="text-muted" style="font-size: 0.6rem;">
                                                {{ $user->email }} | ID: {{ $user->staff_id }}
                                            </div>
                                        </label>
                                    </div>
                                    <div>
                                        @foreach($user->roles as $role)
                                            <span class="badge bg-primary-subtle text-primary rounded-sm" style="font-size: 0.55rem;">{{ $role->name }}</span>
                                        @endforeach
                                        @if($user->roles->isEmpty())
                                            <span class="badge bg-secondary-subtle text-secondary rounded-sm" style="font-size: 0.55rem;">No role</span>
                                        @endif
                                    </div>
                                </div>
                                @empty
                                <div class="text-center py-3 text-muted" style="font-size: 0.7rem;">
                                    No staff members found
                                </div>
                                @endforelse
                            </div>
                            
                            <!-- Pagination for staff list -->
                            <div class="mt-2 pt-2 border-top">
                                <x-pagination :items="$users" :perPageOptions="[10, 25, 50, 100]" />
                            </div>
                        </div>
                        
                        <div class="mb-2">
                            <label class="form-label" style="font-size: 0.7rem;">Select Role</label>
                            <select name="role" class="form-select form-select-sm rounded-sm" style="font-size: 0.7rem;" required>
                                <option value="">-- Select Role --</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->name }}">{{ ucfirst($role->name) }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="mt-3">
                            <button type="submit" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem;">
                                <i class="ti ti-check me-1"></i> Assign Role
                            </button>
                            <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.7rem;">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-info-circle text-primary me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Role Information</span>
                    </div>
                </div>
                <div class="card-body p-2">
                    @php
                        $roleDescriptions = [
                            'super-admin' => 'Full system control across all institutions',
                            'admin' => 'Full system access',
                            'vice-chancellor' => 'Institution-wide oversight',
                            'internal-auditor' => 'Read-only access for compliance',
                            'dvca' => 'Deputy Vice Chancellor Academic',
                            'dvc-pfa' => 'Deputy Vice Chancellor Planning, Finance & Admin',
                            'acting-dvc' => 'Temporary DVC assignment',
                            'dean' => 'Dean of School',
                            'hod' => 'Head of Department',
                            'deputy-hod' => 'Approves when HOD is on leave',
                            'dept-secretary' => 'Department administrative support',
                            'hr-officer' => 'HR management access',
                            'hr-manager' => 'Senior HR access, including staff removal and ID cards',
                            'staff' => 'Regular staff member',
                        ];
                    @endphp
                    <x-alert-info>
    <strong>Available Roles:</strong>
                        <ul class="mb-0 mt-1 ps-3">
                            @foreach($roles as $role)
                                <li><strong>{{ $role->name }}</strong> - {{ $roleDescriptions[$role->name] ?? ucfirst(str_replace('-', ' ', $role->name)) }}</li>
                            @endforeach
                        </ul>
</x-alert-info>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.staff-list-container {
    background: white;
}

.staff-item {
    transition: background 0.2s ease;
}

.staff-item:hover {
    background: #F8FAFC;
}

.staff-item .form-check-input:checked + label {
    font-weight: 600;
}
</style>

<script>
document.getElementById('staffSearchInput')?.addEventListener('keyup', (function () {
    let timer;
    return function (e) {
        if (e.key === 'Enter') return;
        clearTimeout(timer);
        const value = this.value;
        timer = setTimeout(() => {
            const url = new URL(window.location.href);
            if (value) {
                url.searchParams.set('search', value);
            } else {
                url.searchParams.delete('search');
            }
            url.searchParams.delete('page');
            window.location.href = url.toString();
        }, 500);
    };
})());
</script>
@endsection