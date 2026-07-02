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
    
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show py-1 px-2 mb-2 rounded-sm" role="alert">
        <i class="ti ti-check-circle me-1"></i> {{ session('success') }}
        <button type="button" class="btn-close py-0" data-bs-dismiss="alert"></button>
    </div>
    @endif
    
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
                        <tr>
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

<script>
function confirmDelete(id, name) {
    if(confirm('Are you sure you want to delete tenant "' + name + '"?')) {
        const form = document.createElement('form');
        form.action = '/admin/tenants/' + id;
        form.method = 'POST';
        form.innerHTML = '@csrf @method("DELETE")';
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endsection