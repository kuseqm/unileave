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
    
    <div class="row">
        <div class="col-md-6">
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
                            @if($users->hasPages())
                            <div class="mt-2 pt-2 border-top">
                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="text-muted" style="font-size: 0.6rem;">Show:</span>
                                        <select id="perPageSelect" class="custom-select-sm" style="font-size: 0.6rem; padding: 0.15rem 1rem 0.15rem 0.3rem;">
                                            <option value="10" {{ $users->perPage() == 10 ? 'selected' : '' }}>10</option>
                                            <option value="25" {{ $users->perPage() == 25 ? 'selected' : '' }}>25</option>
                                            <option value="50" {{ $users->perPage() == 50 ? 'selected' : '' }}>50</option>
                                            <option value="100" {{ $users->perPage() == 100 ? 'selected' : '' }}>100</option>
                                        </select>
                                    </div>
                                    
                                    <div class="custom-pagination">
                                        <div class="pagination-links">
                                            @if ($users->onFirstPage())
                                                <span class="pagination-item disabled">‹</span>
                                            @else
                                                <a href="{{ $users->previousPageUrl() }}" class="pagination-item">‹</a>
                                            @endif
                                            
                                            <span class="pagination-item active">{{ $users->currentPage() }}</span>
                                            
                                            @if ($users->hasMorePages())
                                                <a href="{{ $users->nextPageUrl() }}" class="pagination-item">›</a>
                                            @else
                                                <span class="pagination-item disabled">›</span>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <div class="text-muted" style="font-size: 0.55rem;">
                                        Page {{ $users->currentPage() }} of {{ $users->lastPage() }}
                                    </div>
                                </div>
                            </div>
                            @endif
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
        
        <div class="col-md-6">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-info-circle text-primary me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Role Information</span>
                    </div>
                </div>
                <div class="card-body p-2">
                    <div class="alert alert-info py-1 px-2 mb-0" style="font-size: 0.7rem;">
                        <i class="ti ti-info-circle me-1"></i>
                        <strong>Available Roles:</strong>
                        <ul class="mb-0 mt-1 ps-3">
                            <li><strong>admin</strong> - Full system access</li>
                            <li><strong>hr-officer</strong> - HR management access</li>
                            <li><strong>hod</strong> - Head of Department</li>
                            <li><strong>dean</strong> - Dean of School</li>
                            <li><strong>dvca</strong> - Deputy Vice Chancellor Academic</li>
                            <li><strong>dvc-pfa</strong> - Deputy Vice Chancellor PFA</li>
                            <li><strong>staff</strong> - Regular staff member</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Custom Pagination Styles */
.custom-pagination {
    margin: 0;
}

.pagination-links {
    display: flex;
    gap: 0.25rem;
    align-items: center;
}

.pagination-item {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 24px;
    height: 24px;
    padding: 0 0.3rem;
    font-size: 0.6rem;
    color: #475569;
    background: white;
    border: 1px solid #E8E2D5;
    border-radius: 4px;
    text-decoration: none;
    transition: all 0.2s ease;
}

.pagination-item:hover:not(.disabled):not(.active) {
    background: #F1F5F9;
    border-color: #CBD5E1;
    color: #1E293B;
}

.pagination-item.active {
    background: #3B82F6;
    border-color: #3B82F6;
    color: white;
}

.pagination-item.disabled {
    opacity: 0.5;
    cursor: not-allowed;
    background: #F8FAFC;
}

.custom-select-sm {
    font-size: 0.6rem;
    padding: 0.15rem 1rem 0.15rem 0.3rem;
    border: 1px solid #E8E2D5;
    border-radius: 4px;
    background: white;
    cursor: pointer;
}

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
// Per Page Change Handler
const perPageSelect = document.getElementById('perPageSelect');
if (perPageSelect) {
    perPageSelect.addEventListener('change', function() {
        const url = new URL(window.location.href);
        url.searchParams.set('per_page', this.value);
        url.searchParams.delete('page');
        window.location.href = url.toString();
    });
}
</script>
@endsection