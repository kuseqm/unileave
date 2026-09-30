@extends('layouts.master')

@section('title', 'Edit Letter File')

@section('content')
<div class="container-fluid px-2">
    
    <!-- Breadcrumb -->
    <x-breadcrumb :items="[
        ['name' => 'Registry', 'url' => '#'],
        ['name' => 'Letter Files', 'url' => route('registry.files.index')],
        ['name' => 'Edit File', 'url' => '#']
    ]" />
    
    <!-- Page Header -->
    <x-page-header title="EDIT LETTER FILE" description="Update letter file information">
        <a href="{{ route('registry.files.index') }}" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
            <i class="ti ti-arrow-left me-1"></i> Back
        </a>
    </x-page-header>
    
    @if($errors->any())
    <x-alert-danger :session="false">
    <strong>Please fix the following errors:</strong>
        <ul class="mb-0 mt-1 ps-3">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close py-0" style="font-size: 0.6rem;" data-bs-dismiss="alert"></button>
</x-alert-danger>
    @endif
    
    <div class="row g-2">
        <div class="col-lg-8">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-folder text-primary me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">File Information</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <form action="{{ route('registry.files.update', $file->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <!-- Reference Format -->
                        <div class="mb-2">
                            <label class="form-label" style="font-size: 0.65rem;">Reference Format <span class="text-danger">*</span></label>
                            <select name="reference_format_id" id="reference_format_id" class="form-select form-select-sm rounded-sm" style="font-size: 0.7rem;" required>
                                <option value="">Select Format</option>
                                @foreach($formats as $format)
                                <option value="{{ $format->id }}" {{ old('reference_format_id', $file->reference_format_id) == $format->id ? 'selected' : '' }}>
                                    {{ $format->name }} ({{ $format->document_type_label }})
                                </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <!-- File Number -->
                        <div class="mb-2">
                            <label class="form-label" style="font-size: 0.65rem;">File Number <span class="text-danger">*</span></label>
                            <input type="text" name="file_number" class="form-control form-control-sm rounded-sm" value="{{ old('file_number', $file->file_number) }}" style="font-size: 0.7rem; font-family: monospace;" required>
                        </div>
                        
                        <!-- File Name -->
                        <div class="mb-2">
                            <label class="form-label" style="font-size: 0.65rem;">File Name <span class="text-danger">*</span></label>
                            <input type="text" name="file_name" class="form-control form-control-sm rounded-sm" value="{{ old('file_name', $file->file_name) }}" style="font-size: 0.7rem;" required>
                        </div>
                        
                        <!-- Serial Range -->
                        <div class="row g-1 mb-2">
                            <div class="col-md-6">
                                <label class="form-label" style="font-size: 0.65rem;">Start Serial <span class="text-danger">*</span></label>
                                <input type="number" name="start_serial" class="form-control form-control-sm rounded-sm" value="{{ old('start_serial', $file->start_serial) }}" style="font-size: 0.7rem;" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" style="font-size: 0.65rem;">End Serial</label>
                                <input type="number" name="end_serial" class="form-control form-control-sm rounded-sm" value="{{ old('end_serial', $file->end_serial) }}" style="font-size: 0.7rem;" placeholder="Leave empty for unlimited">
                            </div>
                        </div>
                        
                        <!-- Physical Location -->
                        <div class="mb-2">
                            <label class="form-label" style="font-size: 0.65rem;">Physical Location</label>
                            <input type="text" name="physical_location" class="form-control form-control-sm rounded-sm" value="{{ old('physical_location', $file->physical_location) }}" style="font-size: 0.7rem;">
                        </div>
                        
                        <!-- Notes -->
                        <div class="mb-2">
                            <label class="form-label" style="font-size: 0.65rem;">Notes</label>
                            <textarea name="notes" rows="2" class="form-control form-control-sm rounded-sm" style="font-size: 0.7rem;">{{ old('notes', $file->notes) }}</textarea>
                        </div>
                        
                        <!-- Options -->
                        <div class="form-check mb-2">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" class="form-check-input" name="is_active" value="1" id="isActive" {{ $file->is_active ? 'checked' : '' }}>
                            <label class="form-check-label" for="isActive" style="font-size: 0.65rem;">Active (can receive new letters)</label>
                        </div>
                        
                        <div class="d-flex gap-1 mt-2">
                            <button type="submit" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem;">
                                <i class="ti ti-device-floppy me-1"></i> Update File
                            </button>
                            <a href="{{ route('registry.files.index') }}" class="btn btn-secondary btn-sm rounded-sm" style="font-size: 0.7rem;">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Info Panel -->
        <div class="col-lg-4">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-info-circle text-info me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Current Status</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <x-alert-light class="mb-2 py-1 px-2 rounded-sm">

                        <div class="d-flex align-items-start gap-1">
                            <i class="ti ti-chart-bar text-primary mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">File Usage</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">
                                    Current: {{ $file->current_serial_formatted }} / 
                                    @if($file->end_serial) {{ $file->end_serial_formatted }} @else Unlimited @endif
                                    ({{ $file->usage_percentage }}% used)
                                </small>
                            </div>
                        
</x-alert-light>
                    </div>
                    
                    <x-alert-light class="mb-2 py-1 px-2 rounded-sm">

                        <div class="d-flex align-items-start gap-1">
                            <i class="ti ti-calendar text-warning mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">Dates</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">
                                    Created: {{ $file->created_at->format('d M Y') }}<br>
                                    Last Updated: {{ $file->updated_at->format('d M Y') }}
                                </small>
                            </div>
                        
</x-alert-light>
                    </div>
                    
                    <x-alert-light class="mb-0 py-1 px-2 rounded-sm">

                        <div class="d-flex align-items-start gap-1">
                            <i class="ti ti-lock text-danger mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">Actions</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">
                                    @if($file->is_active && !$file->closed_at && !$file->isFull())
                                    <button type="button" class="btn btn-sm btn-warning mt-1" onclick="showCloseModal()" style="font-size: 0.6rem;">
                                        <i class="ti ti-lock me-1"></i> Close File
                                    </button>
                                    @endif
                                </small>
                            </div>
                        
</x-alert-light>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Close File Confirmation Modal -->
<div class="modal fade" id="closeFileModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-sm">
            <div class="modal-header py-1 px-2 bg-warning-subtle">
                <h5 class="modal-title" style="font-size: 0.8rem;">
                    <i class="ti ti-alert-triangle text-warning me-1"></i> Close File
                </h5>
                <button type="button" class="btn-close py-0" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body py-2 px-2">
                <p style="font-size: 0.75rem;">Are you sure you want to close <strong>{{ $file->file_number }}</strong>?</p>
                <x-alert-warning>
    <strong>Warning:</strong> Once closed, this file will no longer accept new letters.
</x-alert-warning>
                <x-alert-info>
    You can reopen the file later if needed.
</x-alert-info>
            </div>
            <div class="modal-footer py-1 px-2">
                <button type="button" class="btn btn-secondary btn-sm rounded-sm" data-bs-dismiss="modal" style="font-size: 0.7rem;">Cancel</button>
                <form action="{{ route('registry.files.close', $file->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('PUT')
                    <button type="submit" class="btn btn-warning btn-sm rounded-sm" style="font-size: 0.7rem;">
                        <i class="ti ti-lock me-1"></i> Close File
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function showCloseModal() {
        new bootstrap.Modal(document.getElementById('closeFileModal')).show();
    }
</script>
@endsection