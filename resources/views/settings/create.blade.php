@extends('layouts.master')

@section('title', 'Create Setting')

@section('content')
<div class="container-fluid px-2">
    
    <x-breadcrumb :items="[
        ['name' => 'Settings', 'url' => route('settings.index')],
        ['name' => 'Create', 'url' => '#']
    ]" />
    
    <x-page-header title="CREATE SETTING" description="Add a new system configuration">
        <a href="{{ route('settings.index') }}" class="btn btn-secondary btn-sm rounded-sm">
            <i class="ti ti-arrow-left me-1"></i> Back
        </a>
    </x-page-header>
    
    <div class="row g-2">
        <!-- Form - main column -->
        <div class="col-lg-7">
        <div class="card rounded-sm">
            <div class="card-body">
            <form method="POST" action="{{ route('settings.store') }}">
                @csrf
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">Key</label>
                        <input type="text" name="key" class="form-control @error('key') is-invalid @enderror" 
                               value="{{ old('key') }}" placeholder="e.g., max_annual_leave" required>
                        <small class="text-muted">Unique identifier for this setting</small>
                        @error('key')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Label</label>
                        <input type="text" name="label" class="form-control @error('label') is-invalid @enderror" 
                               value="{{ old('label') }}" placeholder="e.g., Maximum Annual Leave">
                        <small class="text-muted">Human-readable name</small>
                        @error('label')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">Type</label>
                        <select name="type" class="form-select @error('type') is-invalid @enderror" id="settingType" required>
                            <option value="text" {{ old('type') == 'text' ? 'selected' : '' }}>Text</option>
                            <option value="textarea" {{ old('type') == 'textarea' ? 'selected' : '' }}>Textarea</option>
                            <option value="number" {{ old('type') == 'number' ? 'selected' : '' }}>Number</option>
                            <option value="boolean" {{ old('type') == 'boolean' ? 'selected' : '' }}>Boolean (Yes/No)</option>
                            <option value="select" {{ old('type') == 'select' ? 'selected' : '' }}>Select Dropdown</option>
                            <option value="json" {{ old('type') == 'json' ? 'selected' : '' }}>JSON</option>
                        </select>
                        @error('type')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">Group</label>
                        <input type="text" name="group" class="form-control @error('group') is-invalid @enderror" 
                               value="{{ old('group') }}" placeholder="e.g., leave_policy, general, notifications" required>
                        <small class="text-muted">Category grouping for display</small>
                        @error('group')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-12 mb-3" id="optionsField" style="display: none;">
                        <label class="form-label">Options (JSON)</label>
                        <textarea name="options" class="form-control @error('options') is-invalid @enderror" 
                                  rows="3" placeholder='{"option1":"Label 1","option2":"Label 2"}'></textarea>
                        <small class="text-muted">For select type only. JSON format: {"value":"Label"}</small>
                        @error('options')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Value</label>
                        <div id="valueInput">
                            <input type="text" name="value" class="form-control" value="{{ old('value') }}" placeholder="Default value">
                        </div>
                        @error('value')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control @error('description') is-invalid @enderror" 
                                  rows="2" placeholder="Explain what this setting does">{{ old('description') }}</textarea>
                        @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Role (Optional)</label>
                        <input type="text" name="role" class="form-control @error('role') is-invalid @enderror" 
                               value="{{ old('role') }}" placeholder="e.g., admin, hod, dean">
                        <small class="text-muted">Leave empty for global settings</small>
                        @error('role')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Sort Order</label>
                        <input type="number" name="sort_order" class="form-control @error('sort_order') is-invalid @enderror" 
                               value="{{ old('sort_order', 0) }}">
                        @error('sort_order')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <label class="form-check mt-4">
                            <input type="checkbox" name="is_encrypted" class="form-check-input" value="1" {{ old('is_encrypted') ? 'checked' : '' }}>
                            <span class="form-check-label">Encrypt Value</span>
                        </label>
                        <small class="text-muted d-block">Encrypt sensitive values</small>
                    </div>
                </div>
                
                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">Create Setting</button>
                    <a href="{{ route('settings.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
            </div>
        </div>
        </div>

        <!-- Information - narrow column -->
        <div class="col-lg-5">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-info-circle text-info me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">About Settings</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <div class="mb-2">
                        <div class="fw-semibold" style="font-size: 0.7rem;"><i class="ti ti-category me-1 text-primary"></i> Setting Types</div>
                        <ul class="mb-0 ps-3" style="font-size: 0.65rem; color: #555;">
                            <li><strong>Text/Number</strong> - a single value</li>
                            <li><strong>Boolean</strong> - a yes/no toggle</li>
                            <li><strong>Select</strong> - one choice from a fixed list (needs Options)</li>
                            <li><strong>JSON</strong> - structured configuration</li>
                        </ul>
                    </div>

                    <x-alert-light class="mb-0 py-1 px-2 rounded-sm">

                        <div class="d-flex align-items-start gap-1">
                            <i class="ti ti-tips text-warning mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">Tips</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">&bull; Group settings so they appear together on the settings list</small><br>
                                <small class="text-muted" style="font-size: 0.6rem;">&bull; Leave Role empty for a setting that applies globally</small><br>
                                <small class="text-muted" style="font-size: 0.6rem;">&bull; Encrypt Value for anything sensitive (API keys, secrets)</small>
                            </div>

</x-alert-light>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const typeSelect = document.getElementById('settingType');
    const optionsField = document.getElementById('optionsField');
    const valueInput = document.getElementById('valueInput');
    
    typeSelect.addEventListener('change', function() {
        if (this.value === 'select') {
            optionsField.style.display = 'block';
            valueInput.innerHTML = '<input type="text" name="value" class="form-control" placeholder="Default selected value">';
        } else if (this.value === 'boolean') {
            optionsField.style.display = 'none';
            valueInput.innerHTML = `
                <select name="value" class="form-select">
                    <option value="true">Yes (True)</option>
                    <option value="false">No (False)</option>
                </select>
            `;
        } else if (this.value === 'textarea') {
            optionsField.style.display = 'none';
            valueInput.innerHTML = '<textarea name="value" class="form-control" rows="3" placeholder="Text value"></textarea>';
        } else if (this.value === 'number') {
            optionsField.style.display = 'none';
            valueInput.innerHTML = '<input type="number" name="value" class="form-control" placeholder="Number value">';
        } else if (this.value === 'json') {
            optionsField.style.display = 'none';
            valueInput.innerHTML = '<textarea name="value" class="form-control" rows="3" placeholder=\'{"key":"value"}\'></textarea>';
        } else {
            optionsField.style.display = 'none';
            valueInput.innerHTML = '<input type="text" name="value" class="form-control" placeholder="Text value">';
        }
    });
</script>
@endsection