@extends('layouts.master')

@section('title', 'Reference Formats')

@section('content')
<div class="container-fluid px-2">
    
    <!-- Breadcrumb -->
    <x-breadcrumb :items="[
        ['name' => 'Registry', 'url' => '#'],
        ['name' => 'Reference Formats', 'url' => '#']
    ]" />
    
    <!-- Page Header -->
    <x-page-header title="REFERENCE FORMATS" description="Manage letter reference number formats">
        <a href="{{ route('registry.formats.create') }}" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
            <i class="ti ti-plus me-1"></i> Add Format
        </a>
    </x-page-header>
    
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
    
    <div class="card rounded-sm">
        <div class="card-header py-1 px-2 bg-light">
            <div class="d-flex align-items-center">
                <i class="ti ti-file-text text-primary me-1" style="font-size: 0.8rem;"></i>
                <span style="font-size: 0.7rem; font-weight: 600;">Reference Formats List</span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm mb-0" style="font-size: 0.75rem;">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-2 py-1" width="50">#</th>
                            <th class="py-1">Name</th>
                            <th class="py-1">University</th>
                            <th class="py-1">Document Type</th>
                            <th class="py-1">Format Pattern</th>
                            <th class="py-1">Current Serial</th>
                            <th class="py-1">Status</th>
                            <th class="pe-2 py-1 text-center" width="100">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($formats as $index => $format)
                        <tr class="clickable-row" style="cursor: pointer;" onclick="window.location='{{ route('registry.formats.show', $format->id) }}'">
                            <td class="ps-2 py-1">{{ $formats->firstItem() + $index }}</td>
                            <td class="py-1">
                                <span class="fw-semibold hover-link">{{ $format->name }}</span>
                            </td>
                            <td class="py-1">{{ $format->university->name ?? 'N/A' }}</td>
                            <td class="py-1">
                                <span class="badge bg-info-subtle text-info rounded-sm" style="font-size: 0.6rem;">
                                    {{ ucfirst($format->document_type) }}
                                </span>
                            </td>
                            <td class="py-1">
                                <code style="font-size: 0.65rem;">{{ $format->format_pattern }}</code>
                            </td>
                            <td class="py-1">{{ $format->current_serial_formatted }}</td>
                            <td class="py-1">
                                @if($format->is_active)
                                    <span class="badge bg-success-subtle text-success rounded-pill" style="font-size: 0.6rem;">Active</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary rounded-pill" style="font-size: 0.6rem;">Inactive</span>
                                @endif
                            </td>
                            <td class="pe-2 py-1 text-center">
                                <div class="d-flex gap-1 justify-content-center">
                                    <!-- View Button -->
                                    <a href="{{ route('registry.formats.show', $format->id) }}" 
                                       class="btn btn-sm btn-outline-info rounded-sm" 
                                       onclick="event.stopPropagation();"
                                       style="padding: 0.15rem 0.4rem; font-size: 0.6rem;" 
                                       title="View Details">
                                        <i class="ti ti-eye"></i>
                                    </a>
                                    
                                    <!-- Edit Button -->
                                    <a href="{{ route('registry.formats.edit', $format->id) }}" 
                                       class="btn btn-sm btn-outline-primary rounded-sm" 
                                       onclick="event.stopPropagation();"
                                       style="padding: 0.15rem 0.4rem; font-size: 0.6rem;" 
                                       title="Edit Format">
                                        <i class="ti ti-edit"></i>
                                    </a>
                                    
                                    <!-- Delete Button - using global modal -->
                                    <button type="button" 
                                            onclick="event.stopPropagation(); showDeleteModal('{{ $format->id }}', '{{ addslashes($format->name) }}', 'reference_format', '{{ route('registry.formats.destroy', $format->id) }}')" 
                                            class="btn btn-sm btn-outline-danger rounded-sm" 
                                            style="padding: 0.15rem 0.4rem; font-size: 0.6rem;" 
                                            title="Delete Format">
                                        <i class="ti ti-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                <i class="ti ti-file-text mb-2 d-block" style="font-size: 1.5rem;"></i>
                                <p>No reference formats found</p>
                                <a href="{{ route('registry.formats.create') }}" class="btn btn-sm btn-primary rounded-sm">Create First Format</a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($formats->hasPages())
        <div class="card-footer py-1 px-2">
            {{ $formats->links() }}
        </div>
        @endif
    </div>
</div>

<style>
.clickable-row {
    transition: background 0.2s ease;
}
.clickable-row:hover {
    background-color: #F8FAFC !important;
}
.hover-link:hover {
    text-decoration: underline;
    color: #3B82F6 !important;
}
.d-flex.gap-1 {
    gap: 0.25rem;
}
</style>

<!-- Add the delete modal for reference_format type -->
<script>
// Add reference_format type to the global delete modal
document.addEventListener('DOMContentLoaded', function() {
    // Extend the showDeleteModal function to handle reference_format type
    const originalShowDeleteModal = window.showDeleteModal;
    
    window.showDeleteModal = function(itemId, itemName, itemType, itemRoute) {
        if (itemType === 'reference_format') {
            // Custom modal for reference format
            let modal = document.getElementById('deleteModal' + itemId);
            
            if (!modal) {
                const modalHtml = `
                    <div class="modal fade" id="deleteModal${itemId}" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content rounded-sm">
                                <div class="modal-header py-1 px-2 bg-danger-subtle">
                                    <h5 class="modal-title" style="font-size: 0.8rem;">
                                        <i class="ti ti-alert-triangle text-danger me-1"></i> Delete Reference Format
                                    </h5>
                                    <button type="button" class="btn-close py-0" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body py-2 px-2">
                                    <p style="font-size: 0.75rem;">Are you sure you want to delete <strong>${itemName}</strong>?</p>
                                    <div class="alert alert-warning py-1 px-2 mt-2 rounded-sm" style="font-size: 0.65rem;">
                                        <i class="ti ti-alert-circle me-1"></i>
                                        <strong>Warning:</strong> This format will be permanently deleted.
                                    </div>
                                    <div class="alert alert-danger py-1 px-2 mt-2 rounded-sm" style="font-size: 0.65rem;">
                                        <i class="ti ti-info-circle me-1"></i>
                                        Cannot delete if format has been used (current_serial > 0).
                                    </div>
                                </div>
                                <div class="modal-footer py-1 px-2">
                                    <button type="button" class="btn btn-secondary btn-sm rounded-sm" data-bs-dismiss="modal" style="font-size: 0.7rem;">Cancel</button>
                                    <form action="${itemRoute}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm rounded-sm" style="font-size: 0.7rem;">
                                            <i class="ti ti-trash me-1"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                document.body.insertAdjacentHTML('beforeend', modalHtml);
                modal = document.getElementById('deleteModal' + itemId);
            }
            
            const form = modal.querySelector('form');
            if (form) form.action = itemRoute;
            
            const messageSpan = modal.querySelector('.modal-body p');
            if (messageSpan) {
                messageSpan.innerHTML = 'Are you sure you want to delete <strong>' + itemName + '</strong>?';
            }
            
            new bootstrap.Modal(modal).show();
        } else if (originalShowDeleteModal) {
            originalShowDeleteModal(itemId, itemName, itemType, itemRoute);
        }
    };
});
</script>
@endsection