@extends('layouts.master')

@section('title', 'University Management')

@section('content')
<div class="container-fluid px-2">
    
    <!-- Breadcrumb -->
    <x-breadcrumb :items="[
        ['name' => 'Institute', 'url' => '#'],
        ['name' => 'Universities', 'url' => '#']
    ]" />
    
    <!-- Page Header -->
    <x-page-header title="UNIVERSITY MANAGEMENT" description="Manage universities, campuses, and academic institutions">
        <a href="{{ route('admin.universities.create') }}" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
            <i class="ti ti-plus me-1"></i> Add University
        </a>
    </x-page-header>
    
    <!-- Stats Cards - Clickable -->
    <div class="row g-2 mb-3">
        <x-stats-card value="{{ $totalUniversities ?? $universities->total() }}" label="Total Universities" subtext="↑ +{{ $newThisMonth ?? 0 }} this month" link="{{ route('admin.universities.index') }}" />
        <x-stats-card value="{{ $activeUniversities ?? 0 }}" label="Active Universities" subtext="active institutions" color="success" link="{{ route('admin.universities.index', ['status' => 'active']) }}" />
        <x-stats-card value="{{ $totalCampuses ?? 0 }}" label="Total Campuses" subtext="across all universities" color="info" link="{{ route('admin.campuses.index') }}" />
        <x-stats-card value="{{ $totalDepartments ?? 0 }}" label="Total Departments" subtext="academic departments" color="warning" link="{{ route('admin.departments.index') }}" />
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
    <x-search-card 
        placeholder="Search by university name or code..." 
        id="searchInput"
        showStatusFilter="true"
        :statusOptions="['active' => 'Active', 'inactive' => 'Inactive']"
    />
    
    <!-- Universities Table -->
    <div class="card rounded-sm">
        <div class="card-header py-1 px-2 bg-light">
            <div class="d-flex align-items-center">
                <i class="ti ti-building text-primary me-1" style="font-size: 0.8rem;"></i>
                <span style="font-size: 0.7rem; font-weight: 600;">Universities List</span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm mb-0" style="font-size: 0.75rem;" id="universitiesTable">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-2 py-1" width="50">SN</th>
                            <th class="py-1" width="80">Code</th>
                            <th class="py-1">Name</th>
                            <th class="py-1" width="100">Campuses</th>
                            <th class="py-1" width="80">Status</th>
                            <th class="pe-2 py-1" width="100">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($universities as $index => $university)
                        <tr data-name="{{ strtolower($university->name) }}" 
                            data-code="{{ strtolower($university->code) }}" 
                            data-status="{{ $university->status }}"
                            data-id="{{ $university->id }}"
                            class="clickable-row">
                            <td class="ps-2 py-1">{{ $universities->firstItem() + $index }}</td>
                            <td class="py-1"><strong>{{ $university->code }}</strong></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-light me-1 d-flex align-items-center justify-content-center" style="width: 24px; height: 24px;">
                                        <i class="ti ti-building" style="font-size: 0.7rem;"></i>
                                    </div>
                                    <div>
                                        <span class="fw-semibold hover-link">{{ $university->name }}</span>
                                        @if($university->address)
                                        <div class="text-muted" style="font-size: 0.6rem;">{{ Str::limit($university->address, 30) }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="py-1">
                                <a href="{{ route('admin.campuses.index', ['university_id' => $university->id]) }}" class="text-decoration-none">
                                    <span class="badge bg-info-subtle text-info rounded-sm hover-badge" style="font-size: 0.6rem; cursor: pointer;">
                                        <i class="ti ti-school me-1" style="font-size: 0.55rem;"></i>
                                        {{ $university->campuses->count() }} Campuses
                                    </span>
                                </a>
                            </td>
                            <td class="py-1">
                                @if($university->status == 'active')
                                    <span class="badge bg-success-subtle text-success rounded-sm" style="font-size: 0.6rem;">Active</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary rounded-sm" style="font-size: 0.6rem;">Inactive</span>
                                @endif
                            </td>
                            <td class="pe-2 py-1">
                                <div class="d-flex gap-1">
                                    <!-- Edit Button -->
                                    <a href="{{ route('admin.universities.edit', $university->id) }}" class="btn btn-sm btn-outline-primary rounded-sm" style="font-size: 0.6rem; padding: 0.15rem 0.4rem;" title="Edit University">
                                        <i class="ti ti-edit"></i>
                                    </a>
                                    <!-- Delete Button -->
                                    <button type="button" onclick="showDeleteModal({{ $university->id }}, '{{ addslashes($university->name) }}')" class="btn btn-sm btn-outline-danger rounded-sm" style="font-size: 0.6rem; padding: 0.15rem 0.4rem;" title="Delete University">
                                        <i class="ti ti-trash"></i>
                                    </button>
                                    <!-- View Campuses Button -->
                                    <a href="{{ route('admin.campuses.index', ['university_id' => $university->id]) }}" class="btn btn-sm btn-outline-secondary rounded-sm" style="font-size: 0.6rem; padding: 0.15rem 0.4rem;" title="View Campuses">
                                        <i class="ti ti-school"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-3 text-muted" style="font-size: 0.7rem;">
                                No universities found
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer py-1 px-2">
            <x-pagination :items="$universities" :perPageOptions="[10, 25, 50, 100]" />
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
                <p class="text-danger mb-0" style="font-size: 0.7rem;">This will also delete all associated campuses and departments. This action cannot be undone.</p>
            </div>
            <div class="modal-footer py-1 px-2">
                <button type="button" class="btn btn-secondary btn-sm rounded-sm" data-bs-dismiss="modal" style="font-size: 0.7rem;">Cancel</button>
                <a href="#" id="deleteConfirmBtn" class="btn btn-danger btn-sm rounded-sm" style="font-size: 0.7rem;">Delete</a>
            </div>
        </div>
    </div>
</div>

<style>
/* Hover effects for clickable elements */
.clickable-row {
    cursor: pointer;
    transition: background 0.2s ease;
}
.clickable-row:hover {
    background-color: #F8FAFC !important;
}

.hover-link:hover {
    text-decoration: underline;
    color: #3B82F6 !important;
}

.hover-badge:hover {
    background-color: #DBEAFE !important;
    transform: scale(1.02);
    transition: all 0.2s ease;
}

.d-flex.gap-1 {
    gap: 0.25rem;
}
</style>

<script>
let deleteId = '';
let deleteName = '';

function showDeleteModal(id, name) {
    deleteId = id;
    deleteName = name;
    document.getElementById('deleteItemName').innerText = name;
    document.getElementById('deleteConfirmBtn').href = '/admin/universities/' + id;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}

// Override the default confirmDelete function that might be called from old code
window.confirmDelete = function(id, name) {
    showDeleteModal(id, name);
};

// Make entire row clickable (except action buttons)
document.addEventListener('DOMContentLoaded', function() {
    const rows = document.querySelectorAll('.clickable-row');
    rows.forEach(row => {
        // Skip if clicking on action buttons or links
        row.addEventListener('click', function(e) {
            if (e.target.closest('.btn') || e.target.closest('a')) {
                return; // Don't navigate if clicking on action buttons
            }
            const universityId = this.dataset.id;
            if (universityId) {
                window.location.href = '/admin/universities/' + universityId;
            }
        });
    });
});

// Sophisticated Search and Filter Functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');
    const resetBtn = document.getElementById('resetFilters');
    const table = document.getElementById('universitiesTable');
    
    if (!table) return;
    
    const tbody = table.querySelector('tbody');
    const originalRows = Array.from(tbody.querySelectorAll('tr')).filter(row => !row.classList.contains('no-results') && row.cells.length === 6);
    
    function filterTable() {
        const searchTerm = searchInput ? searchInput.value.toLowerCase() : '';
        const statusValue = statusFilter ? statusFilter.value : '';
        
        let visibleCount = 0;
        
        originalRows.forEach(row => {
            const name = row.dataset.name || '';
            const code = row.dataset.code || '';
            const status = row.dataset.status || '';
            
            const matchesSearch = searchTerm === '' || 
                name.includes(searchTerm) || 
                code.includes(searchTerm);
            
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
                emptyRow.innerHTML = '<td colspan="6" class="text-center py-2 text-muted" style="font-size: 0.7rem;">No universities match your search</td>';
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
    
    // Handle URL parameters for status filter
    const urlParams = new URLSearchParams(window.location.search);
    const statusParam = urlParams.get('status');
    if (statusParam && statusFilter) {
        statusFilter.value = statusParam;
        filterTable();
    }
    
    // Initial filter to set up any URL parameters
    filterTable();
});
</script>
@endsection