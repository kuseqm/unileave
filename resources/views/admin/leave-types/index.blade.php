@extends('layouts.master')

@section('title', 'Leave Types')

@section('content')
<div class="container-fluid px-2">
    
    <!-- Breadcrumb -->
    <x-breadcrumb :items="[
        ['name' => 'Leave Management', 'url' => '#'],
        ['name' => 'Leave Types', 'url' => '#']
    ]" />
    
    <!-- Page Header -->
    <x-page-header title="LEAVE TYPES" description="Manage leave types (LC1 - LC11)">
        <a href="{{ route('admin.leave-types.create') }}" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
            <i class="ti ti-plus me-1"></i> Add Leave Type
        </a>
    </x-page-header>
    
    <!-- Stats Cards -->
    <div class="row g-2 mb-3">
        <x-stats-card value="{{ $totalLeaveTypes ?? $leaveTypes->total() }}" label="Total Leave Types" subtext="all types" />
        <x-stats-card value="{{ $activeLeaveTypes ?? 0 }}" label="Active Types" subtext="currently active" color="success" />
        <x-stats-card value="{{ $certificateRequired ?? 0 }}" label="Require Certificate" subtext="need medical cert" color="info" />
        <x-stats-card value="{{ $deductBalance ?? 0 }}" label="Deduct Balance" subtext="reduce leave balance" color="warning" />
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
    
    <!-- Sophisticated Search Card -->
    <div class="card rounded-sm mb-2">
        <div class="card-header py-1 px-2 bg-light">
            <div class="d-flex align-items-center">
                <i class="ti ti-search text-muted me-1" style="font-size: 0.8rem;"></i>
                <span style="font-size: 0.7rem; font-weight: 600;">Sophisticated Search</span>
            </div>
        </div>
        <div class="card-body py-2 px-2">
            <div class="row g-1 align-items-end">
                <div class="col-md-6">
                    <label class="form-label" style="font-size: 0.65rem;">Search</label>
                    <input type="text" id="searchInput" class="form-control form-control-sm rounded-sm" placeholder="Search by leave type code or name..." style="font-size: 0.7rem; padding: 0.2rem 0.3rem;">
                </div>
                <div class="col-md-4">
                    <label class="form-label" style="font-size: 0.65rem;">Status</label>
                    <select id="statusFilter" class="form-select form-select-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;">
                        <option value="">All</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button id="resetFilters" class="btn btn-outline-secondary btn-sm w-100 rounded-sm" style="font-size: 0.65rem; padding: 0.25rem 0.3rem;">
                        <i class="ti ti-refresh me-1"></i> Reset
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Leave Types Table -->
    <div class="card rounded-sm">
        <div class="card-header py-1 px-2 bg-light">
            <div class="d-flex align-items-center">
                <i class="ti ti-tags text-primary me-1" style="font-size: 0.8rem;"></i>
                <span style="font-size: 0.7rem; font-weight: 600;">Leave Types List</span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm mb-0" style="font-size: 0.75rem;" id="leaveTypesTable">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-2 py-1" width="50">SN</th>
                            <th class="py-1" width="80">Code</th>
                            <th class="py-1">Leave Type</th>
                            <th class="py-1" width="100">Max Days</th>
                            <th class="py-1" width="80">Status</th>
                            <th class="pe-2 py-1" width="120">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($leaveTypes as $index => $type)
                        <tr data-code="{{ strtolower($type->code) }}" 
                            data-name="{{ strtolower($type->name) }}" 
                            data-status="{{ $type->status }}">
                            <td class="ps-2 py-1">{{ $leaveTypes->firstItem() + $index }}</td>
                            <td class="py-1"><strong>{{ $type->code }}</strong></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-light me-1 d-flex align-items-center justify-content-center" style="width: 24px; height: 24px;">
                                        <i class="ti ti-tag" style="font-size: 0.7rem;"></i>
                                    </div>
                                    <div>
                                        <span class="fw-semibold">{{ $type->name }}</span>
                                        @if($type->description)
                                        <div class="text-muted" style="font-size: 0.6rem;">{{ Str::limit($type->description, 35) }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($type->max_consecutive_days)
                                    {{ $type->max_consecutive_days }} days
                                @else
                                    <span class="text-muted">Unlimited</span>
                                @endif
                            </td>
                            <td>
                                @if($type->status == 'active')
                                    <span class="badge bg-success-subtle text-success rounded-sm" style="font-size: 0.6rem;">Active</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary rounded-sm" style="font-size: 0.6rem;">Inactive</span>
                                @endif
                            </td>
                            <td class="pe-2 py-1">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.leave-types.show', $type->id) }}" class="btn btn-sm btn-outline-info rounded-sm" title="View" style="padding: 0.15rem 0.3rem;">
                                        <i class="ti ti-eye" style="font-size: 0.7rem;"></i>
                                    </a>
                                    <a href="{{ route('admin.leave-types.edit', $type->id) }}" class="btn btn-sm btn-outline-primary rounded-sm" title="Edit" style="padding: 0.15rem 0.3rem;">
                                        <i class="ti ti-edit" style="font-size: 0.7rem;"></i>
                                    </a>
                                    @if($type->is_configurable)
                                    <button type="button" class="btn btn-sm btn-outline-danger rounded-sm" onclick="showDeleteModal({{ $type->id }}, '{{ $type->name }}')" title="Delete" style="padding: 0.15rem 0.3rem;">
                                        <i class="ti ti-trash" style="font-size: 0.7rem;"></i>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-3 text-muted" style="font-size: 0.7rem;">
                                No leave types found
                            </table>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer py-1 px-2">
            <x-pagination :items="$leaveTypes" :perPageOptions="[10, 25, 50, 100]" />
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-sm">
            <div class="modal-header py-1 px-2">
                <h5 class="modal-title" style="font-size: 0.8rem;">Confirm Delete</h5>
                <button type="button" class="btn-close py-0" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body py-2 px-2">
                <p style="font-size: 0.75rem;">Are you sure you want to delete <strong id="deleteItemName"></strong>?</p>
                <p class="text-danger mb-0" style="font-size: 0.7rem;">This action cannot be undone if there are existing leave requests.</p>
            </div>
            <div class="modal-footer py-1 px-2">
                <button type="button" class="btn btn-secondary btn-sm rounded-sm" data-bs-dismiss="modal" style="font-size: 0.7rem;">Cancel</button>
                <a href="#" id="deleteConfirmBtn" class="btn btn-danger btn-sm rounded-sm" style="font-size: 0.7rem;">Delete</a>
            </div>
        </div>
    </div>
</div>

<script>
let deleteId = '';
let deleteName = '';

function showDeleteModal(id, name) {
    deleteId = id;
    deleteName = name;
    document.getElementById('deleteItemName').innerText = name;
    document.getElementById('deleteConfirmBtn').href = '/admin/leave-types/' + id + '/delete';
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}

// Sophisticated Search and Filter Functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');
    const resetBtn = document.getElementById('resetFilters');
    const table = document.getElementById('leaveTypesTable');
    
    if (!table) return;
    
    const tbody = table.querySelector('tbody');
    const originalRows = Array.from(tbody.querySelectorAll('tr')).filter(row => !row.classList.contains('no-results') && row.cells.length === 6);
    
    function filterTable() {
        const searchTerm = searchInput ? searchInput.value.toLowerCase() : '';
        const statusValue = statusFilter ? statusFilter.value : '';
        
        let visibleCount = 0;
        
        originalRows.forEach(row => {
            const code = row.dataset.code || '';
            const name = row.dataset.name || '';
            const status = row.dataset.status || '';
            
            const matchesSearch = searchTerm === '' || 
                code.includes(searchTerm) || 
                name.includes(searchTerm);
            
            const matchesStatus = statusValue === '' || status === statusValue;
            
            if (matchesSearch && matchesStatus) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });
        
        // Show/hide no results message
        let noResultsRow = tbody.querySelector('.no-results');
        if (visibleCount === 0 && originalRows.length > 0) {
            if (!noResultsRow) {
                const emptyRow = document.createElement('tr');
                emptyRow.className = 'no-results';
                emptyRow.innerHTML = '<td colspan="6" class="text-center py-2 text-muted" style="font-size: 0.7rem;">No leave types match your search<\/td>';
                tbody.appendChild(emptyRow);
            }
        } else if (noResultsRow) {
            noResultsRow.remove();
        }
    }
    
    if (searchInput) searchInput.addEventListener('keyup', filterTable);
    if (statusFilter) statusFilter.addEventListener('change', filterTable);
    
    if (resetBtn) {
        resetBtn.addEventListener('click', function() {
            if (searchInput) searchInput.value = '';
            if (statusFilter) statusFilter.value = '';
            filterTable();
        });
    }
    
    // Initial filter
    filterTable();
});
</script>
@endsection