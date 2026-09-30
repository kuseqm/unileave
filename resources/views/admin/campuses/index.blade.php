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
    <x-alert-success />
    
    <x-alert-danger />
    
    @php
        $uniqueUniversities = $campuses->pluck('university')->unique();
        $universityOptions = collect($uniqueUniversities)->filter()->mapWithKeys(fn($u) => [$u->id => $u->name])->all();
    @endphp

    <x-search-card
        placeholder="Search by campus name or code..."
        :showCategoryFilter="true"
        categoryLabel="University"
        :categoryOptions="$universityOptions"
        :showStatusFilter="true"
        :statusOptions="['active' => 'Active', 'inactive' => 'Inactive']"
    >
        <div id="campusesSearchStats" class="text-muted" style="font-size: 0.65rem;">
            {{ $campuses->total() }} campuses
        </div>
    </x-search-card>
    
    <div class="row g-2">
        <!-- Campuses Table - main column -->
        <div class="col-lg-8">
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
                                            :showDelete="true"
                                            deleteType="campus"
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

        <!-- Information - narrow column -->
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
                        <div class="fw-semibold" style="font-size: 0.7rem;"><i class="ti ti-map-pin me-1 text-primary"></i> About Campuses</div>
                        <p class="mb-0" style="font-size: 0.65rem; color: #555;">A campus belongs to a university and is the physical location grouping schools/faculties and departments. Deactivating a campus hides it from new assignments but keeps existing records intact.</p>
                    </div>

                    <div class="mb-2">
                        <div class="fw-semibold" style="font-size: 0.7rem;"><i class="ti ti-link me-1 text-primary"></i> Quick Links</div>
                        <ul class="mb-0 ps-3" style="font-size: 0.65rem; color: #555;">
                            <li><a href="{{ route('admin.universities.index') }}">Universities</a> - manage the parent institutions</li>
                            <li><a href="{{ route('admin.schools.index') }}">Schools & Faculties</a> - manage schools within campuses</li>
                            <li><a href="{{ route('admin.departments.index') }}">Departments</a> - manage departments</li>
                        </ul>
                    </div>

                    <x-alert-light class="mb-0 py-1 px-2 rounded-sm">
                        <div class="d-flex align-items-start gap-1">
                            <i class="ti ti-tips text-warning mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">Tips</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">&bull; Use the University filter to narrow long campus lists</small><br>
                                <small class="text-muted" style="font-size: 0.6rem;">&bull; A campus code should stay short and unique institution-wide</small>
                            </div>
                        </div>
                    </x-alert-light>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Sophisticated Search and Filter Functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const categoryFilter = document.getElementById('categoryFilter');
    const statusFilter = document.getElementById('statusFilter');
    const resetBtn = document.getElementById('resetFilters');
    const table = document.getElementById('campusesTable');
    
    if (!table) return;
    
    const tbody = table.querySelector('tbody');
    const originalRows = Array.from(tbody.querySelectorAll('tr')).filter(row => !row.classList.contains('no-results') && row.cells.length === 6);
    
    function filterTable() {
        const searchTerm = searchInput ? searchInput.value.toLowerCase() : '';
        const universityValue = categoryFilter ? categoryFilter.value : '';
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
    if (categoryFilter) categoryFilter.addEventListener('change', filterTable);
    if (statusFilter) statusFilter.addEventListener('change', filterTable);
    
    if (resetBtn) {
        resetBtn.addEventListener('click', function() {
            if (searchInput) searchInput.value = '';
            if (categoryFilter) categoryFilter.value = '';
            if (statusFilter) statusFilter.value = '';
            filterTable();
        });
    }
    
    // Initial filter
    filterTable();
});
</script>
@endsection