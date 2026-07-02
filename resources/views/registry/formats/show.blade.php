@extends('layouts.master')

@section('title', 'Format Details - ' . $format->name)

@section('content')
<div class="container-fluid px-2">
    
    <x-breadcrumb :items="[
        ['name' => 'Registry', 'url' => '#'],
        ['name' => 'Reference Formats', 'url' => route('registry.formats.index')],
        ['name' => $format->name, 'url' => '#']
    ]" />
    
    <x-page-header title="FORMAT DETAILS" description="View reference format configuration">
        <div class="d-flex gap-1">
            <a href="{{ route('registry.formats.edit', $format->id) }}" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
                <i class="ti ti-edit me-1"></i> Edit
            </a>
            <button type="button" 
                    onclick="showDeleteModal('{{ $format->id }}', '{{ addslashes($format->name) }}', 'reference_format', '{{ route('registry.formats.destroy', $format->id) }}')" 
                    class="btn btn-danger btn-sm rounded-sm" 
                    style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
                <i class="ti ti-trash me-1"></i> Delete
            </button>
            <a href="{{ route('registry.formats.index') }}" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
                <i class="ti ti-arrow-left me-1"></i> Back
            </a>
        </div>
    </x-page-header>
    
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show py-1 px-2 mb-2 rounded-sm" style="font-size: 0.7rem;" role="alert">
        <i class="ti ti-check-circle me-1"></i> {{ session('success') }}
        <button type="button" class="btn-close py-0" style="font-size: 0.6rem;" data-bs-dismiss="alert"></button>
    </div>
    @endif
    
    <div class="row g-2">
        <!-- Format Information -->
        <div class="col-lg-6">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-info-circle text-primary me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-weight: 600; font-size: 0.7rem;">Format Information</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <table class="table table-sm table-borderless mb-0" style="font-size: 0.75rem;">
                        <tr>
                            <td class="text-muted ps-0" style="width: 35%;">Name:</td>
                            <td class="fw-semibold">{{ $format->name }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-0">University:</td>
                            <td>{{ $format->university->name ?? 'N/A' }} <span class="text-muted">({{ $format->university->code ?? 'No Code' }})</span></td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-0">Document Type:</td>
                            <td>
                                <span class="badge bg-info-subtle text-info rounded-sm" style="font-size: 0.6rem;">
                                    {{ $format->document_type_label }}
                                </span>
                                <span class="text-muted ms-1">({{ $format->document_type_abbreviation }} / {{ $format->document_type_code }})</span>
                            </td>
                        </tr>
                        @if($format->entity_type)
                        <tr>
                            <td class="text-muted ps-0">Associated Entity:</td>
                            <td>
                                <span class="badge bg-primary-subtle text-primary rounded-sm" style="font-size: 0.6rem;">
                                    {{ ucfirst($format->entity_type_label) }}
                                </span>
                                <span class="ms-1">{{ $format->entity_name }} ({{ $format->entity_code }})</span>
                            </td>
                        </tr>
                        @endif
                        @if($format->action_type)
                        <tr>
                            <td class="text-muted ps-0">Action Type:</td>
                            <td>
                                <span class="badge bg-warning-subtle text-warning rounded-sm" style="font-size: 0.6rem;">
                                    {{ $format->action_type }}
                                </span>
                            </td>
                        </tr>
                        @endif
                        <tr>
                            <td class="text-muted ps-0">Status:</td>
                            <td>
                                @if($format->is_active)
                                    <span class="badge bg-success-subtle text-success rounded-pill" style="font-size: 0.6rem;">Active</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary rounded-pill" style="font-size: 0.6rem;">Inactive</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Format Pattern & Preview -->
        <div class="col-lg-6">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-code text-success me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-weight: 600; font-size: 0.7rem;">Format Pattern</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <table class="table table-sm table-borderless mb-0" style="font-size: 0.75rem;">
                        <tr>
                            <td class="text-muted ps-0" style="width: 35%;">Pattern:</td>
                            <td><code class="bg-light p-1 rounded-sm">{{ $format->format_pattern }}</code></td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-0">Separator:</td>
                            <td><code>{{ $format->separator }}</code></td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-0">Example Output:</td>
                            <td>
                                <div class="bg-light p-1 rounded-sm font-monospace" style="font-size: 0.7rem;">
                                    {{ $exampleNumber ?? $format->generateNumber() }}
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-0">Next Number:</td>
                            <td>
                                <div class="bg-primary-subtle text-primary p-1 rounded-sm font-monospace" style="font-size: 0.7rem;">
                                    <i class="ti ti-arrow-right me-1"></i> {{ $nextNumber ?? $format->generateNumber() }}
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Serial Settings -->
        <div class="col-lg-4">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-list-numbers text-primary me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-weight: 600; font-size: 0.7rem;">Serial Settings</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <table class="table table-sm table-borderless mb-0" style="font-size: 0.75rem;">
                        <tr>
                            <td class="text-muted ps-0" style="width: 45%;">Serial Length:</td>
                            <td>{{ $format->serial_length }} digits</td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-0">Start Serial:</td>
                            <td>{{ $format->current_serial_formatted ? str_pad($format->serial_start, $format->serial_length, '0', STR_PAD_LEFT) : 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-0">Current Serial:</td>
                            <td class="fw-bold text-primary">{{ $format->current_serial_formatted }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-0">Next Serial:</td>
                            <td class="fw-bold text-success">{{ $format->next_serial_formatted }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- File Management -->
        <div class="col-lg-4">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-folder text-primary me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-weight: 600; font-size: 0.7rem;">File Management</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <table class="table table-sm table-borderless mb-0" style="font-size: 0.75rem;">
                        <tr>
                            <td class="text-muted ps-0" style="width: 45%;">Letters Per File:</td>
                            <td>{{ $format->file_capacity == 0 ? 'Unlimited' : number_format($format->file_capacity) }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-0">File Naming Pattern:</td>
                            <td><code>{{ $format->file_naming_pattern }}</code></td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-0">Auto-create new file:</td>
                            <td>
                                @if($format->auto_create_new_file)
                                    <span class="badge bg-success-subtle text-success rounded-pill">Yes</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary rounded-pill">No</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-0">Track Physical Location:</td>
                            <td>
                                @if($format->track_physical_location)
                                    <span class="badge bg-success-subtle text-success rounded-pill">Yes</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary rounded-pill">No</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Statistics -->
        <div class="col-lg-4">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-chart-bar text-success me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-weight: 600; font-size: 0.7rem;">Statistics</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <table class="table table-sm table-borderless mb-0" style="font-size: 0.75rem;">
                        <tr>
                            <td class="text-muted ps-0" style="width: 45%;">Total Letters Generated:</td>
                            <td class="fw-bold text-primary">{{ number_format($format->current_serial) }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-0">Total Files Created:</td>
                            <td>{{ number_format($format->letterFiles->count()) }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-0">Active File:</td>
                            <td>
                                @if($format->activeFile)
                                    <span class="badge bg-success-subtle text-success rounded-pill">{{ $format->activeFile->file_number }}</span>
                                    <span class="text-muted">({{ number_format($format->activeFile->current_serial) }} / {{ $format->file_capacity == 0 ? '∞' : number_format($format->file_capacity) }})</span>
                                @else
                                    <span class="text-muted">None</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Letter Files List -->
        <div class="col-12">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <i class="ti ti-files text-primary me-1" style="font-size: 0.8rem;"></i>
                            <span style="font-weight: 600; font-size: 0.7rem;">Letter Files ({{ $format->letterFiles->count() }})</span>
                        </div>
                        @if($format->is_active && $format->auto_create_new_file)
                        <a href="{{ route('registry.files.create', ['format_id' => $format->id]) }}" class="btn btn-sm btn-outline-primary rounded-sm" style="font-size: 0.6rem;">
                            <i class="ti ti-plus me-1"></i> Create New File
                        </a>
                        @endif
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($format->letterFiles->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm mb-0" style="font-size: 0.7rem;">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-2 py-1">File Number</th>
                                    <th class="py-1">Name</th>
                                    <th class="py-1">Serial Range</th>
                                    <th class="py-1">Current</th>
                                    <th class="py-1">Usage</th>
                                    <th class="py-1">Status</th>
                                    <th class="pe-2 py-1 text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($format->letterFiles as $file)
                                @php
                                    $usagePercent = $file->usage_percentage;
                                    $progressClass = $usagePercent >= 90 ? 'bg-danger' : ($usagePercent >= 70 ? 'bg-warning' : 'bg-success');
                                @endphp
                                <tr class="clickable-row" style="cursor: pointer;" onclick="window.location='{{ route('registry.files.show', $file->id) }}'">
                                    <td class="ps-2 py-1">
                                        <span class="fw-semibold hover-link">{{ $file->file_number }}</span>
                                    </td>
                                    <td class="py-1">{{ $file->file_name }}</td>
                                    <td class="py-1">{{ $file->start_serial_formatted }} - {{ $file->end_serial_formatted }}</td>
                                    <td class="py-1">{{ $file->current_serial_formatted }}</td>
                                    <td class="py-1" style="min-width: 100px;">
                                        <div class="d-flex align-items-center gap-1">
                                            <div class="progress flex-grow-1" style="height: 4px;">
                                                <div class="progress-bar {{ $progressClass }}" style="width: {{ $usagePercent }}%"></div>
                                            </div>
                                            <span class="text-muted" style="font-size: 0.55rem;">{{ $usagePercent }}%</span>
                                        </div>
                                    </td>
                                    <td class="py-1">
                                        @if($file->is_active && !$file->closed_at)
                                            <span class="badge bg-success-subtle text-success rounded-pill">Active</span>
                                        @elseif($file->isFull())
                                            <span class="badge bg-warning-subtle text-warning rounded-pill">Full</span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary rounded-pill">Closed</span>
                                        @endif
                                    </td>
                                    <td class="pe-2 py-1 text-center">
                                        <div class="d-flex gap-1 justify-content-center">
                                            <a href="{{ route('registry.files.show', $file->id) }}" 
                                               class="btn btn-sm btn-outline-info rounded-sm" 
                                               onclick="event.stopPropagation();"
                                               style="padding: 0.15rem 0.4rem; font-size: 0.6rem;" 
                                               title="View File">
                                                <i class="ti ti-eye"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-3 text-muted" style="font-size: 0.7rem;">
                        <i class="ti ti-folder mb-1 d-block" style="font-size: 1.2rem;"></i>
                        No letter files created yet. Files will be auto-created when letters are generated.
                    </div>
                    @endif
                </div>
            </div>
        </div>
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
.progress {
    background-color: #E2E8F0;
    border-radius: 4px;
    overflow: hidden;
}
.progress-bar {
    transition: width 0.3s ease;
}
</style>

<!-- Add the delete modal for reference_format type -->
<script>
// Extend the global delete modal for reference_format type
document.addEventListener('DOMContentLoaded', function() {
    const originalShowDeleteModal = window.showDeleteModal;
    
    window.showDeleteModal = function(itemId, itemName, itemType, itemRoute) {
        if (itemType === 'reference_format') {
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
                                        Cannot delete if format has been used (current_serial > 0). Deactivate it instead.
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