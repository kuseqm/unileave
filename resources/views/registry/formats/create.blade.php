@extends('layouts.master')

@section('title', 'Create Reference Format')

@section('content')
<div class="container-fluid px-2">
    
    <x-breadcrumb :items="[
        ['name' => 'Registry', 'url' => '#'],
        ['name' => 'Reference Formats', 'url' => route('registry.formats.index')],
        ['name' => 'Create Format', 'url' => '#']
    ]" />
    
    <x-page-header title="CREATE REFERENCE FORMAT" description="Define a new letter reference number format">
        <a href="{{ route('registry.formats.index') }}" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
            <i class="ti ti-arrow-left me-1"></i> Back
        </a>
    </x-page-header>
    
    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show py-1 px-2 mb-2 rounded-sm" style="font-size: 0.7rem;" role="alert">
        <i class="ti ti-alert-circle me-1"></i>
        <strong>Please fix the following errors:</strong>
        <ul class="mb-0 mt-1 ps-3">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close py-0" style="font-size: 0.6rem;" data-bs-dismiss="alert"></button>
    </div>
    @endif
    
    <div class="row g-2">
        <div class="col-lg-7">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <i class="ti ti-file-text me-1"></i>
                    <span style="font-weight: 600;">Format Configuration</span>
                </div>
                <div class="card-body py-2 px-2">
                    <form action="{{ route('registry.formats.store') }}" method="POST">
                        @csrf
                        
                        <!-- Basic Info -->
                        <div class="row g-1 mb-2">
                            <div class="col-md-6">
                                <label class="form-label" style="font-size: 0.65rem;">Format Name *</label>
                                <input type="text" name="name" class="form-control form-control-sm rounded-sm" value="{{ old('name') }}" style="font-size: 0.7rem;" required placeholder="e.g., University Standard Letter Format">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" style="font-size: 0.65rem;">Document Type *</label>
                                <select name="document_type" id="document_type" class="form-select form-select-sm rounded-sm" style="font-size: 0.7rem;" required>
                                    <option value="">Select Document Type</option>
                                    <option value="leave">Leave Request</option>
                                    <option value="permission">Permission Request</option>
                                    <option value="travel">Travel Permission</option>
                                    <option value="contract">Staff Contract</option>
                                    <option value="general">General Correspondence</option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- University -->
                        <div class="mb-2">
                            <label class="form-label" style="font-size: 0.65rem;">University *</label>
                            <select name="university_id" class="form-select form-select-sm rounded-sm" style="font-size: 0.7rem;" required>
                                <option value="">Select University</option>
                                @foreach($universities as $uni)
                                <option value="{{ $uni->id }}" data-code="{{ $uni->code }}">{{ $uni->name }} ({{ $uni->code ?? 'No Code' }})</option>
                                @endforeach
                            </select>
                            <small class="text-muted" style="font-size: 0.55rem;">The university code will be used for {university_code} placeholder</small>
                        </div>
                        
                        <!-- Format Pattern Builder -->
                        <div class="mb-2">
                            <label class="form-label" style="font-size: 0.65rem;">Format Pattern *</label>
                            <div class="row g-1 mb-1">
                                <div class="col-md-8">
                                    <input type="text" name="format_pattern" id="format_pattern" class="form-control form-control-sm rounded-sm" value="{{ old('format_pattern', '{university_code}/{entity_code}/{year}/{type_code}/VOL {volume}/{serial}') }}" style="font-size: 0.7rem; font-family: monospace;" required>
                                </div>
                                <div class="col-md-4">
                                    <select id="separator" class="form-select form-select-sm rounded-sm" style="font-size: 0.7rem;">
                                        <option value="/">/ (Slash)</option>
                                        <option value="-">- (Hyphen)</option>
                                        <option value="_">_ (Underscore)</option>
                                        <option value=" "> (Space)</option>
                                    </select>
                                </div>
                            </div>
                            <small class="text-muted" style="font-size: 0.55rem;">
                                <strong>Available placeholders:</strong> <code>{university_code}</code>, <code>{entity_code}</code>, <code>{year}</code>, <code>{serial}</code>, <code>{type}</code>, <code>{type_code}</code>, <code>{volume}</code>, <code>{action_type}</code>
                            </small>
                        </div>
                        
                        <!-- Preview -->
                        <div class="mb-2 p-2 bg-light rounded-sm">
                            <div class="text-muted" style="font-size: 0.6rem;">Preview:</div>
                            <div id="preview" class="fw-bold" style="font-size: 0.7rem; font-family: monospace;">---</div>
                        </div>
                        
                        <!-- Serial Settings -->
                        <div class="row g-1 mb-2">
                            <div class="col-md-4">
                                <label class="form-label" style="font-size: 0.65rem;">Serial Length</label>
                                <select name="serial_length" id="serial_length" class="form-select form-select-sm rounded-sm" style="font-size: 0.7rem;">
                                    <option value="3">3 digits (001)</option>
                                    <option value="4" selected>4 digits (0001)</option>
                                    <option value="5">5 digits (00001)</option>
                                    <option value="6">6 digits (000001)</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" style="font-size: 0.65rem;">Start Serial</label>
                                <input type="number" name="serial_start" class="form-control form-control-sm rounded-sm" value="{{ old('serial_start', 1) }}" style="font-size: 0.7rem;">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" style="font-size: 0.65rem;">Current Serial</label>
                                <input type="number" name="current_serial" class="form-control form-control-sm rounded-sm" value="{{ old('current_serial', 0) }}" style="font-size: 0.7rem;">
                            </div>
                        </div>
                        
                        <!-- Action Type (Optional) -->
                        <div class="mb-2">
                            <label class="form-label" style="font-size: 0.65rem;">Action Type (Optional)</label>
                            <select name="action_type" class="form-select form-select-sm rounded-sm" style="font-size: 0.7rem;">
                                <option value="">None</option>
                                <option value="APR">APR - Approval</option>
                                <option value="REJ">REJ - Rejection</option>
                                <option value="CAN">CAN - Cancellation</option>
                                <option value="RCL">RCL - Recall</option>
                            </select>
                            <small class="text-muted" style="font-size: 0.55rem;">Used with {action_type} placeholder</small>
                        </div>
                        
                        <!-- Entity Association (Optional) -->
                        <div class="mb-2">
                            <label class="form-label" style="font-size: 0.65rem;">Associate with (Optional)</label>
                            <div class="row g-1">
                                <div class="col-md-4">
                                    <select name="entity_type" id="entity_type" class="form-select form-select-sm rounded-sm" style="font-size: 0.7rem;">
                                        <option value="">None</option>
                                        <option value="department">Department</option>
                                        <option value="school">School/Faculty</option>
                                    </select>
                                </div>
                                <div class="col-md-8">
                                    <select name="entity_id" id="entity_id" class="form-select form-select-sm rounded-sm" style="font-size: 0.7rem;" disabled>
                                        <option value="">Select entity first</option>
                                    </select>
                                </div>
                            </div>
                            <small class="text-muted" style="font-size: 0.55rem;">Used with {entity_code} placeholder</small>
                        </div>
                        
                        <!-- File Management -->
                        <div class="row g-1 mb-2">
                            <div class="col-md-6">
                                <label class="form-label" style="font-size: 0.65rem;">Letters Per File (Capacity)</label>
                                <input type="number" name="file_capacity" class="form-control form-control-sm rounded-sm" value="{{ old('file_capacity', 100) }}" style="font-size: 0.7rem;">
                                <small class="text-muted" style="font-size: 0.55rem;">0 = unlimited, otherwise creates new file when reached</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" style="font-size: 0.65rem;">File Naming Pattern</label>
                                <input type="text" name="file_naming_pattern" class="form-control form-control-sm rounded-sm" value="{{ old('file_naming_pattern', '{prefix}-VOL-{number:03d}') }}" style="font-size: 0.7rem;">
                                <small class="text-muted" style="font-size: 0.55rem;">Placeholders: {prefix}, {number:03d}</small>
                            </div>
                        </div>
                        
                        <!-- Options -->
                        <div class="row g-2 mb-2">
                            <div class="col-md-6">
                                <div class="form-check form-switch">
                                    <input type="hidden" name="auto_create_new_file" value="0">
                                    <input type="checkbox" class="form-check-input" name="auto_create_new_file" value="1" id="autoCreate" checked>
                                    <label class="form-check-label" for="autoCreate" style="font-size: 0.65rem;">Auto-create new file when full</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-switch">
                                    <input type="hidden" name="track_physical_location" value="0">
                                    <input type="checkbox" class="form-check-input" name="track_physical_location" value="1" id="trackLocation" checked>
                                    <label class="form-check-label" for="trackLocation" style="font-size: 0.65rem;">Track physical file location</label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-check mb-2">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" class="form-check-input" name="is_active" value="1" id="isActive" checked>
                            <label class="form-check-label" for="isActive" style="font-size: 0.65rem;">Active (can be used for generating references)</label>
                        </div>
                        
                        <div class="d-flex gap-1 mt-2">
                            <button type="submit" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem;">
                                <i class="ti ti-device-floppy me-1"></i> Save Format
                            </button>
                            <a href="{{ route('registry.formats.index') }}" class="btn btn-secondary btn-sm rounded-sm" style="font-size: 0.7rem;">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Info Panel -->
        <div class="col-lg-5">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <i class="ti ti-info-circle me-1"></i>
                    <span style="font-weight: 600;">Format Pattern Guide</span>
                </div>
                <div class="card-body py-2 px-2">
                    <div class="alert alert-light border mb-2 py-1 px-2 rounded-sm">
                        <strong class="d-block mb-1" style="font-size: 0.7rem;">Available Placeholders:</strong>
                        <ul class="mb-0" style="font-size: 0.65rem;">
                            <li><code>{university_code}</code> - University code (e.g., SJUT, MU, UDSM)</li>
                            <li><code>{entity_code}</code> - Department/School code (e.g., SVAC, COMPSCI, ENG)</li>
                            <li><code>{year}</code> - Current year (2026)</li>
                            <li><code>{serial}</code> - Auto-incrementing serial number (0001, 0002)</li>
                            <li><code>{type}</code> - Document type abbreviation (LVE, PER, TRV, CTR, GEN)</li>
                            <li><code>{type_code}</code> - Numeric document type (013, 014, 015, 016, 017)</li>
                            <li><code>{volume}</code> - Volume number (VOL I, VOL II, VOL III)</li>
                            <li><code>{action_type}</code> - Action type (APR, REJ, CAN, RCL)</li>
                            <li><code>{prefix}</code> - Alias for {university_code}</li>
                        </ul>
                    </div>
                    
                    <div class="alert alert-light border mb-2 py-1 px-2 rounded-sm">
                        <strong class="d-block mb-1" style="font-size: 0.7rem;">Example Patterns:</strong>
                        <ul class="mb-0" style="font-size: 0.65rem;">
                            <li><code>{university_code}/{entity_code}/{year}/{type_code}/VOL {volume}/{serial}</code><br>
                                → <span class="text-muted">SJUT/SVAC/2026/013/VOL I/0001</span></li>
                            <li><code>{university_code}/{type}/{year}/{serial}</code><br>
                                → <span class="text-muted">SJUT/LVE/2026/0001</span></li>
                            <li><code>{entity_code}-{type_code}-{serial}</code><br>
                                → <span class="text-muted">SVAC-013-0001</span></li>
                            <li><code>HR/{year}/{serial}</code><br>
                                → <span class="text-muted">HR/2026/0001</span></li>
                        </ul>
                    </div>
                    
                    <div class="alert alert-light border mb-0 py-1 px-2 rounded-sm">
                        <strong class="d-block mb-1" style="font-size: 0.7rem;">Document Type Codes:</strong>
                        <ul class="mb-0" style="font-size: 0.65rem;">
                            <li><strong>Leave</strong> - Type: <code>LVE</code> | Code: <code>013</code></li>
                            <li><strong>Permission</strong> - Type: <code>PER</code> | Code: <code>014</code></li>
                            <li><strong>Travel</strong> - Type: <code>TRV</code> | Code: <code>015</code></li>
                            <li><strong>Contract</strong> - Type: <code>CTR</code> | Code: <code>016</code></li>
                            <li><strong>General</strong> - Type: <code>GEN</code> | Code: <code>017</code></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const formatPattern = document.getElementById('format_pattern');
    const preview = document.getElementById('preview');
    const serialLength = document.getElementById('serial_length');
    const documentType = document.getElementById('document_type');
    
    // Entity selection
    const entityType = document.getElementById('entity_type');
    const entityId = document.getElementById('entity_id');
    
    // Type code mapping
    const typeCodes = {
        'leave': '013',
        'permission': '014',
        'travel': '015',
        'contract': '016',
        'general': '017'
    };
    
    const typeAbbr = {
        'leave': 'LVE',
        'permission': 'PER',
        'travel': 'TRV',
        'contract': 'CTR',
        'general': 'GEN'
    };
    
    function updatePreview() {
        let pattern = formatPattern.value;
        const year = new Date().getFullYear();
        const serial = '0001';
        const universityCode = document.querySelector('select[name="university_id"] option:checked')?.dataset.code || 'SJUT';
        const entityCode = 'SVAC'; // Default example
        const volume = 'VOL I';
        const actionType = document.querySelector('select[name="action_type"]')?.value || 'APR';
        
        // Get document type values
        const docTypeValue = documentType.value;
        const typeAbbreviation = typeAbbr[docTypeValue] || 'LVE';
        const typeCode = typeCodes[docTypeValue] || '013';
        
        // Replace all placeholders
        pattern = pattern.replace(/{university_code}/g, universityCode);
        pattern = pattern.replace(/{entity_code}/g, entityCode);
        pattern = pattern.replace(/{year}/g, year);
        pattern = pattern.replace(/{serial}/g, serial);
        pattern = pattern.replace(/{type}/g, typeAbbreviation);
        pattern = pattern.replace(/{type_code}/g, typeCode);
        pattern = pattern.replace(/{volume}/g, volume);
        pattern = pattern.replace(/{action_type}/g, actionType);
        pattern = pattern.replace(/{prefix}/g, universityCode);
        
        preview.innerHTML = pattern || '---';
    }
    
    // Load entities based on type selection
    entityType.addEventListener('change', function() {
        const type = this.value;
        if (type) {
            entityId.disabled = false;
            // Fetch entities via AJAX
            fetch(`/registry/reference-formats/entities?type=${type}`)
                .then(response => response.json())
                .then(data => {
                    entityId.innerHTML = '<option value="">Select ' + type + '</option>';
                    data.forEach(item => {
                        entityId.innerHTML += `<option value="${item.id}" data-code="${item.code}">${item.name} (${item.code})</option>`;
                    });
                });
        } else {
            entityId.disabled = true;
            entityId.innerHTML = '<option value="">Select entity first</option>';
        }
    });
    
    // Update preview when entity is selected
    entityId.addEventListener('change', function() {
        const selected = this.options[this.selectedIndex];
        const code = selected?.dataset.code || 'DEPT';
        let pattern = formatPattern.value;
        pattern = pattern.replace(/{entity_code}/g, code);
        preview.innerHTML = pattern || '---';
    });
    
    // Add event listeners
    formatPattern.addEventListener('keyup', updatePreview);
    serialLength.addEventListener('change', updatePreview);
    documentType.addEventListener('change', updatePreview);
    document.querySelector('select[name="university_id"]')?.addEventListener('change', updatePreview);
    document.querySelector('select[name="action_type"]')?.addEventListener('change', updatePreview);
    
    // Initial preview
    updatePreview();
</script>
@endsection