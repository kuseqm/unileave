@extends('layouts.master')

@section('title', 'Tenants')

@section('content')
<div class="container-fluid px-2">
    
    <x-breadcrumb :items="[
        ['name' => 'Admin', 'url' => '#'],
        ['name' => 'Tenants', 'url' => '#']
    ]" />
    
    <x-page-header title="TENANTS" description="Manage multi-tenant institutions">
        <a href="{{ route('admin.tenants.create') }}" class="btn btn-primary btn-sm rounded-sm">
            <i class="ti ti-plus me-1"></i> Add Tenant
        </a>
    </x-page-header>
    
    <x-alert-success />
    <x-alert-danger />
    
    <!-- Stats Cards -->
    <div class="row g-2 mb-3">
        <x-stats-card value="{{ $stats['total'] }}" label="Total Tenants" subtext="institutions" color="primary" icon="building" />
        <x-stats-card value="{{ $stats['active'] }}" label="Active" subtext="running" color="success" icon="check" />
        <x-stats-card value="{{ $stats['inactive'] }}" label="Inactive" subtext="disabled" color="danger" icon="x" />
        <x-stats-card value="{{ $stats['pending'] }}" label="Pending" subtext="awaiting setup" color="warning" icon="clock" />
    </div>
    
    <x-search-card
        placeholder="Search by name or domain..."
        :showStatusFilter="true"
        :statusOptions="['active' => 'Active', 'inactive' => 'Inactive', 'pending' => 'Pending']"
    >
        <div class="text-muted" style="font-size: 0.65rem;">
            {{ $stats['total'] }} tenants
        </div>
    </x-search-card>
    
    <div class="row g-2">
        <!-- Tenants Table - main column -->
        <div class="col-lg-8">
            <div class="card rounded-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0" style="font-size: 0.75rem;">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-2 py-1">#</th>
                                    <th class="py-1">Name</th>
                                    <th class="py-1">Domain</th>
                                    <th class="py-1">Database</th>
                                    <th class="py-1">Status</th>
                                    <th class="py-1">Created</th>
                                    <th class="pe-2 py-1">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tenants as $index => $tenant)
                                <tr data-name="{{ strtolower($tenant->name) }}" data-domain="{{ strtolower($tenant->domain ?? '') }}" data-status="{{ $tenant->status }}">
                                    <td class="ps-2 py-1">{{ $index + 1 }}</td>
                                    <td class="py-1">{{ $tenant->name }}</td>
                                    <td class="py-1">{{ $tenant->domain ?? '-' }}</td>
                                    <td class="py-1">{{ $tenant->database ?? '-' }}</td>
                                    <td class="py-1">
                                        @if($tenant->status == 'active')
                                            <span class="badge bg-success-subtle text-success rounded-sm">Active</span>
                                        @elseif($tenant->status == 'inactive')
                                            <span class="badge bg-danger-subtle text-danger rounded-sm">Inactive</span>
                                        @else
                                            <span class="badge bg-warning-subtle text-warning rounded-sm">Pending</span>
                                        @endif
                                    </td>
                                    <td class="py-1">{{ $tenant->created_at->format('d M Y') }}</td>
                                    <td class="pe-2 py-1">
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('admin.tenants.settings', $tenant->id) }}" class="btn btn-sm btn-outline-info rounded-sm" title="Settings">
                                                <i class="ti ti-settings"></i>
                                            </a>
                                            <a href="{{ route('admin.tenants.edit', $tenant->id) }}" class="btn btn-sm btn-outline-primary rounded-sm" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger rounded-sm" title="Delete" onclick="confirmDelete({{ $tenant->id }}, '{{ $tenant->name }}')">
                                                <i class="ti ti-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-3 text-muted">No tenants found</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
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
                        <div class="fw-semibold" style="font-size: 0.7rem;"><i class="ti ti-building me-1 text-primary"></i> About Tenants</div>
                        <p class="mb-0" style="font-size: 0.65rem; color: #555;">A tenant is a separate institution running its own instance of the system, identified by its own domain and database. Deleting a tenant does not remove its underlying database.</p>
                    </div>

                    <div class="mb-2">
                        <div class="fw-semibold" style="font-size: 0.7rem;"><i class="ti ti-flag me-1 text-primary"></i> Understanding Status</div>
                        <ul class="mb-0 ps-3" style="font-size: 0.65rem; color: #555;">
                            <li><span class="badge bg-success-subtle text-success rounded-sm">Active</span> - running and accessible</li>
                            <li><span class="badge bg-warning-subtle text-warning rounded-sm">Pending</span> - awaiting setup</li>
                            <li><span class="badge bg-danger-subtle text-danger rounded-sm">Inactive</span> - disabled</li>
                        </ul>
                    </div>

                    <x-alert-light class="mb-0 py-1 px-2 rounded-sm">
                        <div class="d-flex align-items-start gap-1">
                            <i class="ti ti-tips text-warning mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">Tips</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">&bull; Use Settings to configure a tenant after it's created</small><br>
                                <small class="text-muted" style="font-size: 0.6rem;">&bull; Deleting a tenant is permanent - double-check before confirming</small>
                            </div>
                        </div>
                    </x-alert-light>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(id, name) {
    confirmAction('Are you sure you want to delete tenant "' + name + '"?', function () {
        const form = document.createElement('form');
        form.action = '/admin/tenants/' + id;
        form.method = 'POST';
        form.innerHTML = '@csrf @method("DELETE")';
        document.body.appendChild(form);
        form.submit();
    });
}

// Search and status filter
const searchInput = document.getElementById('searchInput');
const statusFilter = document.getElementById('statusFilter');
const resetFilters = document.getElementById('resetFilters');
const rows = document.querySelectorAll('tbody tr');

function filterTable() {
    const search = searchInput ? searchInput.value.toLowerCase() : '';
    const status = statusFilter ? statusFilter.value : '';

    rows.forEach(row => {
        const name = row.dataset.name || '';
        const domain = row.dataset.domain || '';
        const rowStatus = row.dataset.status || '';
        const matchesSearch = !search || name.includes(search) || domain.includes(search);
        const matchesStatus = !status || rowStatus === status;
        row.style.display = (matchesSearch && matchesStatus) ? '' : 'none';
    });
}

if (searchInput) searchInput.addEventListener('keyup', filterTable);
if (statusFilter) statusFilter.addEventListener('change', filterTable);

if (resetFilters) {
    resetFilters.addEventListener('click', function () {
        if (searchInput) searchInput.value = '';
        if (statusFilter) statusFilter.value = '';
        filterTable();
    });
}
</script>
@endsection