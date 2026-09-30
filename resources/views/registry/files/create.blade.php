@extends('layouts.master')

@section('title', 'Create Letter File')

@section('content')
<div class="container-fluid px-2">
    
    <!-- Breadcrumb -->
    <x-breadcrumb :items="[
        ['name' => 'Registry', 'url' => '#'],
        ['name' => 'Letter Files', 'url' => route('registry.files.index')],
        ['name' => 'Create File', 'url' => '#']
    ]" />
    
    <!-- Page Header -->
    <x-page-header title="CREATE LETTER FILE" description="Create a new physical letter file/volume">
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
                    <form action="{{ route('registry.files.store') }}" method="POST">
                        @csrf
                        
                        <!-- Reference Format -->
                        <div class="mb-2">
                            <label class="form-label" style="font-size: 0.65rem;">Reference Format <span class="text-danger">*</span></label>
                            <select name="reference_format_id" id="reference_format_id" class="form-select form-select-sm rounded-sm" style="font-size: 0.7rem;" required>
                                <option value="">Select Format</option>
                                @foreach($formats as $format)
                                <option value="{{ $format->id }}" {{ old('reference_format_id', $selectedFormat) == $format->id ? 'selected' : '' }}>
                                    {{ $format->name }} ({{ $format->document_type_label }})
                                </option>
                                @endforeach
                            </select>
                            <small class="text-muted" style="font-size: 0.55rem;">The reference format this file belongs to</small>
                        </div>
                        
                        <!-- File Number -->
                        <div class="mb-2">
                            <label class="form-label" style="font-size: 0.65rem;">File Number <span class="text-danger">*</span></label>
                            <input type="text" name="file_number" id="file_number" class="form-control form-control-sm rounded-sm" value="{{ old('file_number', $nextFileNumber) }}" style="font-size: 0.7rem; font-family: monospace;" required>
                            <small class="text-muted" style="font-size: 0.55rem;">Unique identifier for this file (e.g., SJUT-VOL-001)</small>
                        </div>
                        
                        <!-- File Name -->
                        <div class="mb-2">
                            <label class="form-label" style="font-size: 0.65rem;">File Name <span class="text-danger">*</span></label>
                            <input type="text" name="file_name" class="form-control form-control-sm rounded-sm" value="{{ old('file_name') }}" style="font-size: 0.7rem;" required placeholder="e.g., Volume 1: January - March 2026">
                            <small class="text-muted" style="font-size: 0.55rem;">Descriptive name for this file</small>
                        </div>
                        
                        <!-- Serial Range -->
                        <div class="row g-1 mb-2">
                            <div class="col-md-6">
                                <label class="form-label" style="font-size: 0.65rem;">Start Serial <span class="text-danger">*</span></label>
                                <input type="number" name="start_serial" id="start_serial" class="form-control form-control-sm rounded-sm" value="{{ old('start_serial', 1) }}" style="font-size: 0.7rem;" required>
                                <small class="text-muted" style="font-size: 0.55rem;">First serial number in this file</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" style="font-size: 0.65rem;">End Serial</label>
                                <input type="number" name="end_serial" id="end_serial" class="form-control form-control-sm rounded-sm" value="{{ old('end_serial') }}" style="font-size: 0.7rem;" placeholder="Leave empty for unlimited">
                                <small class="text-muted" style="font-size: 0.55rem;">Last serial number (optional)</small>
                            </div>
                        </div>
                        
                        <!-- Physical Location -->
                        <div class="mb-2">
                            <label class="form-label" style="font-size: 0.65rem;">Physical Location</label>
                            <input type="text" name="physical_location" class="form-control form-control-sm rounded-sm" value="{{ old('physical_location') }}" style="font-size: 0.7rem;" placeholder="e.g., Cabinet 3, Shelf B, Row 2">
                            <small class="text-muted" style="font-size: 0.55rem;">Where this file is physically stored</small>
                        </div>
                        
                        <!-- Notes -->
                        <div class="mb-2">
                            <label class="form-label" style="font-size: 0.65rem;">Notes</label>
                            <textarea name="notes" rows="2" class="form-control form-control-sm rounded-sm" style="font-size: 0.7rem;" placeholder="Additional notes about this file...">{{ old('notes') }}</textarea>
                        </div>
                        
                        <!-- Options -->
                        <div class="form-check mb-2">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" class="form-check-input" name="is_active" value="1" id="isActive" checked>
                            <label class="form-check-label" for="isActive" style="font-size: 0.65rem;">Active (can receive new letters)</label>
                        </div>
                        
                        <div class="d-flex gap-1 mt-2">
                            <button type="submit" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem;">
                                <i class="ti ti-device-floppy me-1"></i> Create File
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
                        <span style="font-size: 0.7rem; font-weight: 600;">About Letter Files</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <x-alert-light class="mb-2 py-1 px-2 rounded-sm">

                        <div class="d-flex align-items-start gap-1">
                            <i class="ti ti-folder text-primary mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">What is a Letter File?</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">A physical or logical grouping of letters (e.g., volume 1, volume 2).</small>
                            </div>
                        
</x-alert-light>
                    </div>
                    
                    <x-alert-light class="mb-2 py-1 px-2 rounded-sm">

                        <div class="d-flex align-items-start gap-1">
                            <i class="ti ti-list-numbers text-success mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">Serial Range</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">The range of serial numbers assigned to this file. Leave End Serial empty for unlimited.</small>
                            </div>
                        
</x-alert-light>
                    </div>
                    
                    <x-alert-light class="mb-0 py-1 px-2 rounded-sm">

                        <div class="d-flex align-items-start gap-1">
                            <i class="ti ti-automatic text-warning mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">Auto-creation</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">When a file reaches its capacity, a new file is automatically created if enabled in the format settings.</small>
                            </div>
                        
</x-alert-light>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const formatSelect = document.getElementById('reference_format_id');
    const fileNumberInput = document.getElementById('file_number');
    
    // Auto-generate file number when format changes
    formatSelect.addEventListener('change', function() {
        const formatId = this.value;
        if (formatId) {
            fetch(`/registry/files/next-file-number?format_id=${formatId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.file_number) {
                        fileNumberInput.value = data.file_number;
                    }
                })
                .catch(error => console.error('Error:', error));
        }
    });
</script>
@endsection