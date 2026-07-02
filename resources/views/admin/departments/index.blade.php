@extends('layouts.master')

@section('title', 'Department Management')

@section('content')
<div class="container-fluid px-2">
    
    <!-- Breadcrumb -->
    <x-breadcrumb :items="[
        ['name' => 'Admin', 'url' => route('admin.departments.index')],
        ['name' => 'Departments', 'url' => '#' ]
    ]" />
    
    <!-- Page Header -->
    <x-page-header title="DEPARTMENT MANAGEMENT" description="Manage academic departments and administrative units">
        <a href="{{ route('admin.departments.create') }}" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
            <i class="ti ti-plus me-1"></i> Add Department
        </a>
    </x-page-header>
    
    <!-- Stats Cards -->
    <div class="row g-2 mb-3">
        <x-stats-card value="{{ $departments->total() ?? 0 }}" label="Total Departments" subtext="↑ +{{ $newThisMonth ?? 0 }} this month" />
        <x-stats-card value="{{ $departments->where('status', 'active')->count() ?? 0 }}" label="Active Departments" subtext="active departments" color="success" />
        <x-stats-card value="{{ App\Models\School::count() }}" label="Schools/Faculties" subtext="parent institutions" color="info" />
        <x-stats-card value="{{ App\Models\User::count() }}" label="Total Staff" subtext="assigned to departments" color="warning" />
    </div>
    
    <!-- Alerts -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show py-1 px-2 mb-2 rounded-sm" role="alert">
        <i class="ti ti-check-circle me-1"></i> {{ session('success') }}
        <button type="button" class="btn-close py-0" data-bs-dismiss="alert"></button>
    </div>
    @endif
    
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show py-1 px-2 mb-2 rounded-sm" role="alert">
        <i class="ti ti-alert-circle me-1"></i> {{ session('error') }}
        <button type="button" class="btn-close py-0" data-bs-dismiss="alert"></button>
    </div>
    @endif
    
    <!-- Search Card Component -->
    <x-search-card placeholder="Search departments by name, code or school..." id="searchInput" />
    
    <!-- Departments Table -->
    <div class="card rounded-sm">
        <div class="card-header py-1 px-2 bg-light">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <i class="ti ti-building-community text-primary me-1"></i>
                    <span style="font-size: 0.7rem; font-weight: 600;">Departments List</span>
                </div>
                <div class="text-muted" style="font-size: 0.6rem;">
                    Showing {{ $departments->firstItem() ?? 0 }} to {{ $departments->lastItem() ?? 0 }} of {{ $departments->total() ?? 0 }} entries
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm mb-0" style="font-size: 0.75rem;" id="departmentsTable">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-2 py-1" width="50">#</th>
                            <th class="py-1">Department Name</th>
                            <th class="py-1">Code</th>
                            <th class="py-1">School/Faculty</th>
                            <th class="py-1">Campus</th>
                            <th class="py-1">University</th>
                            <th class="py-1">Status</th>
                            <th class="pe-2 py-1" width="120">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($departments as $index => $department)
                        <tr data-department-id="{{ $department->id }}"
                            data-name="{{ strtolower($department->name) }}"
                            data-code="{{ strtolower($department->code ?? '') }}"
                            data-school="{{ strtolower($department->school->name ?? '') }}"
                            data-school-id="{{ $department->school_id }}"
                            data-status="{{ $department->status }}">
                            <td class="ps-2 py-1">{{ $departments->firstItem() + $index }}</td>
                            <td class="py-1">
                                <span class="fw-semibold">{{ $department->name }}</span>
                            </td>
                            <td class="py-1">{{ $department->code ?? 'N/A' }}</td>
                            <td class="py-1">{{ $department->school->name ?? 'N/A' }}</td>
                            <td class="py-1">{{ $department->school->campus->name ?? 'N/A' }}</td>
                            <td class="py-1">{{ $department->school->campus->university->name ?? 'N/A' }}</td>
                            <td class="py-1">
                                @if($department->status == 'active')
                                    <span class="badge bg-success-subtle text-success rounded-sm" style="font-size: 0.6rem;">Active</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger rounded-sm" style="font-size: 0.6rem;">Inactive</span>
                                @endif
                            </td>
                            <td class="pe-2 py-1">
                                <div class="d-flex gap-1">
                                    <!-- View Button -->
                                    <a href="{{ route('admin.departments.show', $department->id) }}" 
                                       class="btn btn-sm btn-outline-info rounded-sm" 
                                       style="font-size: 0.6rem; padding: 0.15rem 0.4rem;" 
                                       title="View Department">
                                        <i class="ti ti-eye"></i>
                                    </a>
                                    
                                    <!-- Edit Button -->
                                    <a href="{{ route('admin.departments.edit', $department->id) }}" 
                                       class="btn btn-sm btn-outline-primary rounded-sm" 
                                       style="font-size: 0.6rem; padding: 0.15rem 0.4rem;" 
                                       title="Edit Department">
                                        <i class="ti ti-edit"></i>
                                    </a>
                                    
                                    <!-- Delete Button -->
                                    <button type="button" 
                                            onclick="showDeleteModal({{ $department->id }}, '{{ addslashes($department->name) }}')" 
                                            class="btn btn-sm btn-outline-danger rounded-sm" 
                                            style="font-size: 0.6rem; padding: 0.15rem 0.4rem;" 
                                            title="Delete Department">
                                        <i class="ti ti-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-3 text-muted">No departments found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Pagination Footer -->
        <div class="card-footer py-2 px-2 bg-light">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <span class="text-muted" style="font-size: 0.65rem;">Show:</span>
                    <select id="perPageSelect" class="custom-select-sm" style="font-size: 0.65rem; padding: 0.2rem 1.2rem 0.2rem 0.4rem; border: 1px solid #E8E2D5; border-radius: 4px; background: white;">
                        <option value="10" {{ $departments->perPage() == 10 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ $departments->perPage() == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ $departments->perPage() == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ $departments->perPage() == 100 ? 'selected' : '' }}>100</option>
                    </select>
                    <span class="text-muted" style="font-size: 0.65rem;">per page</span>
                </div>
                
                @if ($departments->hasPages())
                <div class="custom-pagination">
                    <div class="pagination-links">
                        @if ($departments->onFirstPage())
                            <span class="pagination-item disabled">«</span>
                        @else
                            <a href="{{ $departments->url(1) }}" class="pagination-item">«</a>
                        @endif
                        
                        @if ($departments->onFirstPage())
                            <span class="pagination-item disabled">‹</span>
                        @else
                            <a href="{{ $departments->previousPageUrl() }}" class="pagination-item">‹</a>
                        @endif
                        
                        @php
                            $currentPage = $departments->currentPage();
                            $lastPage = $departments->lastPage();
                            $start = max(1, $currentPage - 2);
                            $end = min($lastPage, $currentPage + 2);
                            
                            if ($start > 1) {
                                echo '<a href="' . $departments->url(1) . '" class="pagination-item">1</a>';
                                if ($start > 2) echo '<span class="pagination-item dots">...</span>';
                            }
                            
                            for ($i = $start; $i <= $end; $i++) {
                                if ($i == $currentPage) {
                                    echo '<span class="pagination-item active">' . $i . '</span>';
                                } else {
                                    echo '<a href="' . $departments->url($i) . '" class="pagination-item">' . $i . '</a>';
                                }
                            }
                            
                            if ($end < $lastPage) {
                                if ($end < $lastPage - 1) echo '<span class="pagination-item dots">...</span>';
                                echo '<a href="' . $departments->url($lastPage) . '" class="pagination-item">' . $lastPage . '</a>';
                            }
                        @endphp
                        
                        @if ($departments->hasMorePages())
                            <a href="{{ $departments->nextPageUrl() }}" class="pagination-item">›</a>
                        @else
                            <span class="pagination-item disabled">›</span>
                        @endif
                        
                        @if ($departments->hasMorePages())
                            <a href="{{ $departments->url($departments->lastPage()) }}" class="pagination-item">»</a>
                        @else
                            <span class="pagination-item disabled">»</span>
                        @endif
                    </div>
                </div>
                @endif
                
                <div class="text-muted" style="font-size: 0.6rem;">
                    Page {{ $departments->currentPage() }} of {{ $departments->lastPage() }}
                </div>
            </div>
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
                <p style="font-size: 0.75rem;">Are you sure you want to delete <strong id="deleteDepartmentName"></strong>?</p>
                <p class="text-danger mb-0" style="font-size: 0.7rem;">This action cannot be undone.</p>
            </div>
            <div class="modal-footer py-1 px-2">
                <button type="button" class="btn btn-secondary btn-sm rounded-sm" data-bs-dismiss="modal" style="font-size: 0.7rem;">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm rounded-sm" style="font-size: 0.7rem;">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
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
    min-width: 28px;
    height: 28px;
    padding: 0 0.4rem;
    font-size: 0.65rem;
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

.pagination-item.dots {
    border: none;
    background: transparent;
    cursor: default;
}

.custom-select-sm {
    font-size: 0.65rem;
    padding: 0.2rem 1.2rem 0.2rem 0.4rem;
    border: 1px solid #E8E2D5;
    border-radius: 4px;
    background: white;
    cursor: pointer;
}

.custom-select-sm:focus {
    outline: none;
    border-color: #3B82F6;
}
</style>

<script>
function showDeleteModal(id, name) {
    document.getElementById('deleteDepartmentName').innerText = name;
    document.getElementById('deleteForm').action = '/admin/departments/' + id;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}

// Search Functionality
const searchInput = document.getElementById('searchInput');
if (searchInput) {
    searchInput.addEventListener('keyup', function() {
        const searchTerm = this.value.toLowerCase();
        const rows = document.querySelectorAll('#departmentsTable tbody tr');
        rows.forEach(row => {
            if (row.cells.length >= 8) {
                const name = row.cells[1]?.innerText.toLowerCase() || '';
                const code = row.cells[2]?.innerText.toLowerCase() || '';
                const school = row.cells[3]?.innerText.toLowerCase() || '';
                const matches = name.includes(searchTerm) || code.includes(searchTerm) || school.includes(searchTerm);
                row.style.display = matches ? '' : 'none';
            }
        });
    });
}

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