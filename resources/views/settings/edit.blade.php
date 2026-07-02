@extends('layouts.master')

@section('title', 'Edit Setting')

@section('content')
<div class="container-fluid px-2">
    
    <x-breadcrumb :items="[
        ['name' => 'Settings', 'url' => route('settings.index')],
        ['name' => 'Edit', 'url' => '#']
    ]" />
    
    <x-page-header title="EDIT SETTING" description="Modify setting configuration">
        <a href="{{ route('settings.index') }}" class="btn btn-secondary btn-sm rounded-sm">
            <i class="ti ti-arrow-left me-1"></i> Back
        </a>
    </x-page-header>
    
    <div class="card rounded-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('settings.update', $setting->id) }}">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">Key</label>
                        <input type="text" name="key" class="form-control @error('key') is-invalid @enderror" 
                               value="{{ old('key', $setting->key) }}" required>
                        <small class="text-muted">Unique identifier (cannot be changed easily)</small>
                        @error('key')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Label</label>
                        <input type="text" name="label" class="form-control @error('label') is-invalid @enderror" 
                               value="{{ old('label', $setting->label) }}">
                        @error('label')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">Type</label>
                        <select name="type" class="form-select @error('type') is-invalid @enderror" id="settingType" required>
                            <option value="text" {{ old('type', $setting->type) == 'text' ? 'selected' : '' }}>Text</option>
                            <option value="textarea" {{ old('type', $setting->type) == 'textarea' ? 'selected' : '' }}>Textarea</option>
                            <option value="number" {{ old('type', $setting->type) == 'number' ? 'selected' : '' }}>Number</option>
                            <option value="boolean" {{ old('type', $setting->type) == 'boolean' ? 'selected' : '' }}>Boolean</option>
                            <option value="select" {{ old('type', $setting->type) == 'select' ? 'selected' : '' }}>Select Dropdown</option>
                            <option value="json" {{ old('type', $setting->type) == 'json' ? 'selected' : '' }}>JSON</option>
                        </select>
                        @error('type')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">Group</label>
                        <input type="text" name="group" class="form-control @error('group') is-invalid @enderror" 
                               value="{{ old('group', $setting->group) }}" required>
                        @error('group')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-12 mb-3" id="optionsField" style="{{ $setting->type == 'select' ? 'display:block' : 'display:none' }}">
                        <label class="form-label">Options (JSON)</label>
                        <textarea name="options" class="form-control @error('options') is-invalid @enderror" 
                                  rows="3" placeholder='{"option1":"Label 1","option2":"Label 2"}'>{{ old('options', is_array($setting->options) ? json_encode($setting->options) : $setting->options) }}</textarea>
                        <small class="text-muted">For select type only. JSON format: {"value":"Label"}</small>
                        @error('options')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Default Value</label>
                        <div id="valueInput">
                            @if($setting->type === 'boolean')
                            <select name="value" class="form-select">
                                <option value="true" {{ $setting->value == 'true' ? 'selected' : '' }}>Yes (True)</option>
                                <option value="false" {{ $setting->value == 'false' ? 'selected' : '' }}>No (False)</option>
                            </select>
                            @elseif($setting->type === 'textarea')
                            <textarea name="value" class="form-control" rows="3">{{ old('value', $setting->value) }}</textarea>
                            @else
                            <input type="text" name="value" class="form-control" value="{{ old('value', $setting->value) }}">
                            @endif
                        </div>
                        @error('value')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control @error('description') is-invalid @enderror" 
                                  rows="2">{{ old('description', $setting->description) }}</textarea>
                        @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Role (Optional)</label>
                        <input type="text" name="role" class="form-control @error('role') is-invalid @enderror" 
                               value="{{ old('role', $setting->role) }}" placeholder="Leave empty for global">
                        <small class="text-muted">e.g., admin, hod, dean</small>
                        @error('role')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Sort Order</label>
                        <input type="number" name="sort_order" class="form-control @error('sort_order') is-invalid @enderror" 
                               value="{{ old('sort_order', $setting->sort_order) }}">
                        @error('sort_order')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <label class="form-check mt-4">
                            <input type="checkbox" name="is_encrypted" class="form-check-input" value="1" {{ old('is_encrypted', $setting->is_encrypted) ? 'checked' : '' }}>
                            <span class="form-check-label">Encrypt Value</span>
                        </label>
                    </div>
                </div>
                
                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">Update Setting</button>
                    <a href="{{ route('settings.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
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
            valueInput.innerHTML = '<input type="text" name="value" class="form-control" placeholder="Default selected value" value="{{ old('value', $setting->value) }}">';
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
            valueInput.innerHTML = '<textarea name="value" class="form-control" rows="3">{{ old('value', $setting->value) }}</textarea>';
        } else if (this.value === 'number') {
            optionsField.style.display = 'none';
            valueInput.innerHTML = '<input type="number" name="value" class="form-control" value="{{ old('value', $setting->value) }}">';
        } else if (this.value === 'json') {
            optionsField.style.display = 'none';
            valueInput.innerHTML = '<textarea name="value" class="form-control" rows="3">{{ old('value', $setting->value) }}</textarea>';
        } else {
            optionsField.style.display = 'none';
            valueInput.innerHTML = '<input type="text" name="value" class="form-control" value="{{ old('value', $setting->value) }}">';
        }
    });
</script>
@endsection