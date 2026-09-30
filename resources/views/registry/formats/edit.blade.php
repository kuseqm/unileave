@extends('layouts.master')

@section('title', 'Edit Reference Format')

@section('content')
<div class="container-fluid px-2">
    
    <x-breadcrumb :items="[
        ['name' => 'Registry', 'url' => '#'],
        ['name' => 'Reference Formats', 'url' => route('registry.formats.index')],
        ['name' => 'Edit Format', 'url' => '#']
    ]" />
    
    <x-page-header title="EDIT REFERENCE FORMAT" description="Modify letter reference number format">
        <a href="{{ route('registry.formats.index') }}" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
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
        <div class="col-lg-7">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-file-text text-primary me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Format Configuration</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <form action="{{ route('registry.formats.update', $format->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <!-- Basic Info -->
                        <div class="row g-1 mb-2">
                            <div class="col-md-6">
                                <label class="form-label" style="font-size: 0.65rem;">Format Name *</label>
                                <input type="text" name="name" class="form-control form-control-sm rounded-sm" value="{{ old('name', $format->name) }}" style="font-size: 0.7rem;" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" style="font-size: 0.65rem;">Document Type *</label>
                                <select name="document_type" id="document_type" class="form-select form-select-sm rounded-sm" style="font-size: 0.7rem;" required>
                                    <option value="leave" {{ $format->document_type == 'leave' ? 'selected' : '' }}>Leave Request</option>
                                    <option value="permission" {{ $format->document_type == 'permission' ? 'selected' : '' }}>Permission Request</option>
                                    <option value="travel" {{ $format->document_type == 'travel' ? 'selected' : '' }}>Travel Permission</option>
                                    <option value="contract" {{ $format->document_type == 'contract' ? 'selected' : '' }}>Staff Contract</option>
                                    <option value="general" {{ $format->document_type == 'general' ? 'selected' : '' }}>General Correspondence</option>
                                    <option value="graduation" {{ $format->document_type == 'graduation' ? 'selected' : '' }}>Graduation Confirmation Letter</option>
                                    <option value="verification" {{ $format->document_type == 'verification' ? 'selected' : '' }}>Certificate / Transcript Verification Letter</option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- University -->
                        <div class="mb-2">
                            <label class="form-label" style="font-size: 0.65rem;">University *</label>
                            <select name="university_id" id="university_id" class="form-select form-select-sm rounded-sm" style="font-size: 0.7rem;" required>
                                <option value="">Select University</option>
                                @foreach($universities as $uni)
                                <option value="{{ $uni->id }}" data-code="{{ $uni->code }}" {{ old('university_id', $format->university_id) == $uni->id ? 'selected' : '' }}>
                                    {{ $uni->name }} ({{ $uni->code ?? 'No Code' }})
                                </option>
                                @endforeach
                            </select>
                            <small class="text-muted" style="font-size: 0.55rem;">The university code will be used for {university_code} placeholder</small>
                        </div>
                        
                        <!-- Format Pattern Builder -->
                        <div class="mb-2">
                            <label class="form-label" style="font-size: 0.65rem;">Format Pattern *</label>
                            <div class="row g-1 mb-1">
                                <div class="col-md-8">
                                    <input type="text" name="format_pattern" id="format_pattern" class="form-control form-control-sm rounded-sm" value="{{ old('format_pattern', $format->format_pattern) }}" style="font-size: 0.7rem; font-family: monospace;" required>
                                </div>
                                <div class="col-md-4">
                                    <select id="separator" class="form-select form-select-sm rounded-sm" style="font-size: 0.7rem;">
                                        <option value="/" {{ $format->separator == '/' ? 'selected' : '' }}>/ (Slash)</option>
                                        <option value="-" {{ $format->separator == '-' ? 'selected' : '' }}>- (Hyphen)</option>
                                        <option value="_" {{ $format->separator == '_' ? 'selected' : '' }}>_ (Underscore)</option>
                                        <option value=" " {{ $format->separator == ' ' ? 'selected' : '' }}> (Space)</option>
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
                                    <option value="3" {{ $format->serial_length == 3 ? 'selected' : '' }}>3 digits (001)</option>
                                    <option value="4" {{ $format->serial_length == 4 ? 'selected' : '' }}>4 digits (0001)</option>
                                    <option value="5" {{ $format->serial_length == 5 ? 'selected' : '' }}>5 digits (00001)</option>
                                    <option value="6" {{ $format->serial_length == 6 ? 'selected' : '' }}>6 digits (000001)</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" style="font-size: 0.65rem;">Start Serial</label>
                                <input type="number" name="serial_start" class="form-control form-control-sm rounded-sm" value="{{ old('serial_start', $format->serial_start) }}" style="font-size: 0.7rem;">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" style="font-size: 0.65rem;">Current Serial</label>
                                <input type="number" name="current_serial" class="form-control form-control-sm rounded-sm" value="{{ old('current_serial', $format->current_serial) }}" style="font-size: 0.7rem;">
                            </div>
                        </div>
                        
                        <!-- Action Type (Optional) -->
                        <div class="mb-2">
                            <label class="form-label" style="font-size: 0.65rem;">Action Type (Optional)</label>
                            <select name="action_type" class="form-select form-select-sm rounded-sm" style="font-size: 0.7rem;">
                                <option value="">None</option>
                                <option value="APR" {{ $format->action_type == 'APR' ? 'selected' : '' }}>APR - Approval</option>
                                <option value="REJ" {{ $format->action_type == 'REJ' ? 'selected' : '' }}>REJ - Rejection</option>
                                <option value="CAN" {{ $format->action_type == 'CAN' ? 'selected' : '' }}>CAN - Cancellation</option>
                                <option value="RCL" {{ $format->action_type == 'RCL' ? 'selected' : '' }}>RCL - Recall</option>
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
                                        <option value="department" {{ $format->entity_type == 'department' ? 'selected' : '' }}>Department</option>
                                        <option value="school" {{ $format->entity_type == 'school' ? 'selected' : '' }}>School/Faculty</option>
                                    </select>
                                </div>
                                <div class="col-md-8">
                                    <select name="entity_id" id="entity_id" class="form-select form-select-sm rounded-sm" style="font-size: 0.7rem;" {{ !$format->entity_type ? 'disabled' : '' }}>
                                        <option value="">Select entity first</option>
                                        @if($format->entity_type == 'department')
                                            @foreach($departments as $dept)
                                            <option value="{{ $dept->id }}" data-code="{{ $dept->code }}" {{ $format->entity_id == $dept->id ? 'selected' : '' }}>
                                                {{ $dept->name }} ({{ $dept->code }})
                                            </option>
                                            @endforeach
                                        @elseif($format->entity_type == 'school')
                                            @foreach($schools as $sch)
                                            <option value="{{ $sch->id }}" data-code="{{ $sch->code }}" {{ $format->entity_id == $sch->id ? 'selected' : '' }}>
                                                {{ $sch->name }} ({{ $sch->code }})
                                            </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <small class="text-muted" style="font-size: 0.55rem;">Used with {entity_code} placeholder</small>
                        </div>
                        
                        <!-- File Management -->
                        <div class="row g-1 mb-2">
                            <div class="col-md-6">
                                <label class="form-label" style="font-size: 0.65rem;">Letters Per File (Capacity)</label>
                                <input type="number" name="file_capacity" class="form-control form-control-sm rounded-sm" value="{{ old('file_capacity', $format->file_capacity) }}" style="font-size: 0.7rem;">
                                <small class="text-muted" style="font-size: 0.55rem;">0 = unlimited, otherwise creates new file when reached</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" style="font-size: 0.65rem;">Starting Volume</label>
                                <input type="number" min="1" name="volume_start" class="form-control form-control-sm rounded-sm" value="{{ old('volume_start', $format->volume_start ?? 1) }}" style="font-size: 0.7rem;">
                                <small class="text-muted" style="font-size: 0.55rem;">For {volume} numbering: the volume the file is on now (4 = VOL IV)</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" style="font-size: 0.65rem;">File Naming Pattern</label>
                                <input type="text" name="file_naming_pattern" class="form-control form-control-sm rounded-sm" value="{{ old('file_naming_pattern', $format->file_naming_pattern) }}" style="font-size: 0.7rem;">
                                <small class="text-muted" style="font-size: 0.55rem;">Placeholders: {prefix}, {number:03d}</small>
                            </div>
                        </div>
                        
                        <!-- Options -->
                        <div class="row g-2 mb-2">
                            <div class="col-md-6">
                                <div class="form-check form-switch">
                                    <input type="hidden" name="auto_create_new_file" value="0">
                                    <input type="checkbox" class="form-check-input" name="auto_create_new_file" value="1" id="autoCreate" {{ $format->auto_create_new_file ? 'checked' : '' }}>
                                    <label class="form-check-label" for="autoCreate" style="font-size: 0.65rem;">Auto-create new file when full</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-switch">
                                    <input type="hidden" name="track_physical_location" value="0">
                                    <input type="checkbox" class="form-check-input" name="track_physical_location" value="1" id="trackLocation" {{ $format->track_physical_location ? 'checked' : '' }}>
                                    <label class="form-check-label" for="trackLocation" style="font-size: 0.65rem;">Track physical file location</label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-check mb-2">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" class="form-check-input" name="is_active" value="1" id="isActive" {{ $format->is_active ? 'checked' : '' }}>
                            <label class="form-check-label" for="isActive" style="font-size: 0.65rem;">Active (can be used for generating references)</label>
                        </div>
                        
                        <div class="d-flex gap-1 mt-2">
                            <button type="submit" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem;">
                                <i class="ti ti-device-floppy me-1"></i> Update Format
                            </button>
                            <a href="{{ route('registry.formats.index') }}" class="btn btn-secondary btn-sm rounded-sm" style="font-size: 0.7rem;">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-5">
            <div class="card rounded-sm mb-2">
                <div class="card-header py-1 px-2 bg-light">
                    <i class="ti ti-info-circle me-1"></i>
                    <span style="font-weight: 600;">Format Pattern Guide</span>
                </div>
                <div class="card-body py-2 px-2">
                    <x-alert-secondary>
    <strong class="d-block mb-1" style="font-size: 0.7rem;">Available Placeholders:</strong>
                        <ul class="mb-0" style="font-size: 0.65rem;">
                            <li><code>{university_code}</code> - University code (e.g., SJUT, MU, UDSM)</li>
                            <li><code>{entity_code}</code> - Department/School code (e.g., SVAC, COMPSCI)</li>
                            <li><code>{year}</code> - Current year (2026)</li>
                            <li><code>{serial}</code> - Auto-incrementing serial number</li>
                            <li><code>{type}</code> - Document type abbreviation (LVE, PER, TRV)</li>
                            <li><code>{type_code}</code> - Numeric document type (013, 014)</li>
                            <li><code>{volume}</code> - Volume number (VOL I, VOL II)</li>
                            <li><code>{action_type}</code> - Action type (APR, REJ, CAN)</li>
                        </ul>
</x-alert-secondary>
                    
                    <x-alert-light class="mb-0 py-1 px-2 rounded-sm">

                        <strong class="d-block mb-1" style="font-size: 0.7rem;">Example:</strong>
                        <code class="small">{university_code}/{entity_code}/{year}/{type_code}/VOL {volume}/{serial}</code>
                        <div class="text-muted mt-1 small">→ SJUT/SVAC/2026/013/VOL I/0001</div>
                    
</x-alert-light>
                </div>
            </div>
            
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <i class="ti ti-chart-bar text-success me-1"></i>
                    <span style="font-weight: 600;">Usage Statistics</span>
                </div>
                <div class="card-body py-2 px-2">
                    <x-alert-secondary>
    <strong class="d-block" style="font-size: 0.7rem;">Total Letters Generated:</strong>
                        <span class="fs-4 fw-bold text-primary">{{ number_format($format->current_serial) }}</span>
</x-alert-secondary>
                    <x-alert-secondary>
    <strong class="d-block" style="font-size: 0.7rem;">Active File:</strong>
                        @if($format->activeFile)
                            <span>{{ $format->activeFile->file_number }}</span>
                            <span class="text-muted">({{ number_format($format->activeFile->current_serial) }} / {{ $format->file_capacity == 0 ? '∞' : number_format($format->file_capacity) }})</span>
                        @else
                            <span class="text-muted">No active file</span>
                        @endif
</x-alert-secondary>
                    <x-alert-secondary>
    <strong class="d-block" style="font-size: 0.7rem;">Total Files:</strong>
                        <span>{{ number_format($format->letterFiles->count()) }}</span>
</x-alert-secondary>
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
    const universitySelect = document.getElementById('university_id');
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
        const currentSerial = parseInt(document.querySelector('input[name="current_serial"]')?.value) || 0;
        const nextSerial = (currentSerial + 1).toString().padStart(parseInt(serialLength.value) || 4, '0');
        const universityCode = universitySelect?.options[universitySelect.selectedIndex]?.dataset.code || 'SJUT';
        const entityCode = entityId?.options[entityId.selectedIndex]?.dataset.code || 'SVAC';
        const volume = 'VOL I';
        const actionType = document.querySelector('select[name="action_type"]')?.value || 'APR';
        
        const docTypeValue = documentType.value;
        const typeAbbreviation = typeAbbr[docTypeValue] || 'LVE';
        const typeCode = typeCodes[docTypeValue] || '013';
        
        // Replace all placeholders
        pattern = pattern.replace(/{university_code}/g, universityCode);
        pattern = pattern.replace(/{entity_code}/g, entityCode);
        pattern = pattern.replace(/{year}/g, year);
        pattern = pattern.replace(/{serial}/g, nextSerial);
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
            fetch(`/registry/reference-formats/entities?type=${type}`)
                .then(response => response.json())
                .then(data => {
                    entityId.innerHTML = '<option value="">Select ' + type + '</option>';
                    data.forEach(item => {
                        const selected = ({{ $format->entity_id }} == item.id) ? 'selected' : '';
                        entityId.innerHTML += `<option value="${item.id}" data-code="${item.code}" ${selected}>${item.name} (${item.code})</option>`;
                    });
                });
        } else {
            entityId.disabled = true;
            entityId.innerHTML = '<option value="">Select entity first</option>';
        }
    });
    
    // Add event listeners
    formatPattern.addEventListener('keyup', updatePreview);
    serialLength.addEventListener('change', updatePreview);
    documentType.addEventListener('change', updatePreview);
    universitySelect?.addEventListener('change', updatePreview);
    document.querySelector('select[name="action_type"]')?.addEventListener('change', updatePreview);
    document.querySelector('input[name="current_serial"]')?.addEventListener('change', updatePreview);
    
    // Initial preview
    updatePreview();
</script>
@endsection