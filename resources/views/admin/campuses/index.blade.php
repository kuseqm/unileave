@extends('layouts.master')

@section('title', 'Campus Management')

@section('content')
<div class="container-fluid px-2">
    
    <!-- Breadcrumb -->
    <x-breadcrumb :items="[
        ['name' => 'Institute', 'url' => '#'],
        ['name' => 'Campuses', 'url' => '#']
    ]" />
    
    <!-- Page Header -->
    <x-page-header title="CAMPUS MANAGEMENT" description="Manage university campuses and locations">
        <a href="{{ route('admin.campuses.create') }}" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
            <i class="ti ti-plus me-1"></i> Add Campus
        </a>
    </x-page-header>
    
    <!-- Stats Cards -->
    <div class="row g-2 mb-3">
        <x-stats-card value="{{ $totalCampuses }}" label="Total Campuses" subtext="↑ +{{ $newThisMonth }} this month" />
        <x-stats-card value="{{ $activeCampuses }}" label="Active Campuses" subtext="active locations" color="success" />
        <x-stats-card value="{{ $totalUniversities }}" label="Universities" subtext="parent institutions" color="info" />
        <x-stats-card value="{{ $totalDepartments }}" label="Departments" subtext="across campuses" color="warning" />
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
    
    <!-- Custom Search Card with University Filter -->
    <div class="card rounded-sm mb-2">
        <div class="card-header py-1 px-2 bg-light">
            <div class="d-flex align-items-center">
                <i class="ti ti-search text-muted me-1" style="font-size: 0.8rem;"></i>
                <span style="font-size: 0.7rem; font-weight: 600;">Sophisticated Search</span>
            </div>
        </div>
        <div class="card-body py-2 px-2">
            <div class="row g-1 align-items-end">
                <div class="col-md-4">
                    <label class="form-label" style="font-size: 0.65rem;">Search</label>
                    <input type="text" id="searchInput" class="form-control form-control-sm rounded-sm" placeholder="Search by campus name or code..." style="font-size: 0.7rem; padding: 0.2rem 0.3rem;">
                </div>
                <div class="col-md-3">
                    <label class="form-label" style="font-size: 0.65rem;">University</label>
                    <select id="universityFilter" class="form-select form-select-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;">
                        <option value="">All Universities</option>
                        @php
                            $uniqueUniversities = $campuses->pluck('university')->unique();
                        @endphp
                        @foreach($uniqueUniversities as $uni)
                            @if($uni)
                            <option value="{{ $uni->id }}">{{ $uni->name }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
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
    
    <!-- Campuses Table -->
    <div class="card rounded-sm">
        <div class="card-header py-1 px-2 bg-light">
            <div class="d-flex align-items-center">
                <i class="ti ti-map-pin text-primary me-1" style="font-size: 0.8rem;"></i>
                <span style="font-size: 0.7rem; font-weight: 600;">Campuses List</span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm mb-0" style="font-size: 0.75rem;" id="campusesTable">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-2 py-1" width="50">SN</th>
                            <th class="py-1" width="80">Code</th>
                            <th class="py-1">Campus Name</th>
                            <th class="py-1" width="150">University</th>
                            <th class="py-1" width="80">Status</th>
                            <th class="pe-2 py-1" width="80">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($campuses as $index => $campus)
                        <tr data-name="{{ strtolower($campus->name) }}" 
                            data-code="{{ strtolower($campus->code) }}" 
                            data-university="{{ $campus->university_id }}"
                            data-status="{{ $campus->status }}">
                            <td class="ps-2 py-1">{{ $campuses->firstItem() + $index }}</td>
                            <td class="py-1"><strong>{{ $campus->code }}</strong></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-light me-1 d-flex align-items-center justify-content-center" style="width: 24px; height: 24px;">
                                        <i class="ti ti-map-pin" style="font-size: 0.7rem;"></i>
                                    </div>
                                    <div>
                                        <span class="fw-semibold">{{ $campus->name }}</span>
                                        <div class="text-muted" style="font-size: 0.6rem;">Created: {{ $campus->created_at->format('d M Y') }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $campus->university->name ?? 'N/A' }}</td>
                            <td>
                                @if($campus->status == 'active')
                                    <span class="badge bg-success-subtle text-success rounded-sm" style="font-size: 0.6rem;">Active</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary rounded-sm" style="font-size: 0.6rem;">Inactive</span>
                                @endif
                            </td>
                            <td class="pe-2 py-1">
                                <x-action-buttons 
                                    editRoute="{{ route('admin.campuses.edit', $campus->id) }}"
                                    deleteRoute="{{ route('admin.campuses.destroy', $campus->id) }}"
                                    deleteId="{{ $campus->id }}"
                                    deleteName="{{ $campus->name }}"
                                />
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-3 text-muted" style="font-size: 0.7rem;">
                                No campuses found
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer py-1 px-2">
            <x-pagination :items="$campuses" :perPageOptions="[10, 25, 50, 100]" />
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
                <p class="text-danger mb-0" style="font-size: 0.7rem;">This action cannot be undone.</p>
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
    document.getElementById('deleteConfirmBtn').href = '/admin/campuses/' + id;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}

// Sophisticated Search and Filter Functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const universityFilter = document.getElementById('universityFilter');
    const statusFilter = document.getElementById('statusFilter');
    const resetBtn = document.getElementById('resetFilters');
    const table = document.getElementById('campusesTable');
    
    if (!table) return;
    
    const tbody = table.querySelector('tbody');
    const originalRows = Array.from(tbody.querySelectorAll('tr')).filter(row => !row.classList.contains('no-results') && row.cells.length === 6);
    
    function filterTable() {
        const searchTerm = searchInput ? searchInput.value.toLowerCase() : '';
        const universityValue = universityFilter ? universityFilter.value : '';
        const statusValue = statusFilter ? statusFilter.value : '';
        
        let visibleCount = 0;
        
        originalRows.forEach(row => {
            const name = row.dataset.name || '';
            const code = row.dataset.code || '';
            const university = row.dataset.university || '';
            const status = row.dataset.status || '';
            
            const matchesSearch = searchTerm === '' || 
                name.includes(searchTerm) || 
                code.includes(searchTerm);
            
            const matchesUniversity = universityValue === '' || university === universityValue;
            const matchesStatus = statusValue === '' || status === statusValue;
            
            if (matchesSearch && matchesUniversity && matchesStatus) {
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
                emptyRow.innerHTML = '<td colspan="6" class="text-center py-2 text-muted" style="font-size: 0.7rem;">No campuses match your search<\/td>';
                tbody.appendChild(emptyRow);
            }
        } else if (noResultsRow) {
            noResultsRow.remove();
        }
    }
    
    if (searchInput) searchInput.addEventListener('keyup', filterTable);
    if (universityFilter) universityFilter.addEventListener('change', filterTable);
    if (statusFilter) statusFilter.addEventListener('change', filterTable);
    
    if (resetBtn) {
        resetBtn.addEventListener('click', function() {
            if (searchInput) searchInput.value = '';
            if (universityFilter) universityFilter.value = '';
            if (statusFilter) statusFilter.value = '';
            filterTable();
        });
    }
    
    // Initial filter
    filterTable();
});
</script>
@endsection