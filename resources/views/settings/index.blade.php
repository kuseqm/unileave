@extends('layouts.master')

@section('title', 'Settings')

@section('content')
<div class="container-fluid px-2">
    
    <x-breadcrumb :items="[
        ['name' => 'Settings', 'url' => '#']
    ]" />
    
    <x-page-header title="SETTINGS" description="Configure system preferences">
        @hasanyrole('admin|super-admin')
        <a href="{{ route('settings.create') }}" class="btn btn-primary btn-sm rounded-sm">
            <i class="ti ti-plus me-1"></i> New Setting
        </a>
        @endhasanyrole
    </x-page-header>
    
    <x-alert-success />
    
    <x-alert-danger />
    
    <!-- Tab Navigation -->
    <ul class="nav nav-tabs mb-3" id="settingsTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="settings-tab" data-bs-toggle="tab" data-bs-target="#settings" type="button" role="tab">
                <i class="ti ti-settings"></i> General
            </button>
        </li>
        @if($canLetterhead)
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="letterhead-tab" data-bs-toggle="tab" data-bs-target="#letterhead-pane" type="button" role="tab">
                <i class="ti ti-palette"></i> Letter Branding
            </button>
        </li>
        @endif
        @if($canRegistry)
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="registry-tab" data-bs-toggle="tab" data-bs-target="#registry-pane" type="button" role="tab">
                <i class="ti ti-file-text"></i> Letter &amp; Registry
            </button>
        </li>
        @endif
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="hr-tab" data-bs-toggle="tab" data-bs-target="#hr-pane" type="button" role="tab">
                <i class="ti ti-users"></i> HR
            </button>
        </li>
        @hasanyrole('admin|super-admin')
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="all-settings-tab" data-bs-toggle="tab" data-bs-target="#all-settings" type="button" role="tab">
                <i class="ti ti-list"></i> All Settings Definitions
            </button>
        </li>
        @endhasanyrole
    </ul>

    <div class="tab-content">
        <!-- Settings Values Tab -->
        <div class="tab-pane fade show active" id="settings" role="tabpanel">
            <form method="POST" action="{{ route('settings.bulk-update') }}">
                @csrf
                @method('PUT')
                
                @foreach($settings as $group => $groupSettings)
                <div class="card rounded-sm mb-3">
                    <div class="card-header py-1 px-2 bg-light">
                        <i class="ti ti-settings me-1"></i>
                        <span style="font-weight: 600;">{{ ucfirst(str_replace('_', ' ', $group)) }}</span>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($groupSettings as $setting)
                            <div class="col-md-6 mb-3">
                                <label class="form-label" style="font-size: 0.75rem;">
                                    {{ $setting->label ?? ucfirst(str_replace('_', ' ', $setting->key)) }}
                                    @if($setting->description)
                                    <i class="ti ti-info-circle text-muted" data-bs-toggle="tooltip" title="{{ $setting->description }}"></i>
                                    @endif
                                </label>
                                
                                @if($setting->type === 'boolean')
                                <div class="form-check form-switch">
                                    <input type="hidden" name="settings[{{ $setting->key }}]" value="false">
                                    <input type="checkbox" 
                                           class="form-check-input" 
                                           name="settings[{{ $setting->key }}]" 
                                           value="true"
                                           id="setting_{{ $setting->key }}"
                                           {{ setting($setting->key, $setting->value, $setting->role) ? 'checked' : '' }}>
                                    <label class="form-check-label" style="font-size: 0.7rem;" for="setting_{{ $setting->key }}">
                                        {{ $setting->description ?? 'Enable this setting' }}
                                    </label>
                                </div>
                                
                                @elseif($setting->type === 'select' && $setting->options)
                                <select class="form-select form-select-sm" name="settings[{{ $setting->key }}]" style="font-size: 0.7rem;">
                                    @php
                                        $options = is_array($setting->options) ? $setting->options : json_decode($setting->options, true);
                                    @endphp
                                    @foreach($options as $optValue => $optLabel)
                                    <option value="{{ $optValue }}" {{ setting($setting->key, $setting->value, $setting->role) == $optValue ? 'selected' : '' }}>
                                        {{ $optLabel }}
                                    </option>
                                    @endforeach
                                </select>
                                
                                @elseif($setting->type === 'number')
                                <input type="number" 
                                       class="form-control form-control-sm" 
                                       name="settings[{{ $setting->key }}]" 
                                       value="{{ setting($setting->key, $setting->value, $setting->role) }}"
                                       style="font-size: 0.7rem;">
                                
                                @elseif($setting->type === 'textarea')
                                <textarea class="form-control form-control-sm" 
                                          name="settings[{{ $setting->key }}]" 
                                          rows="2"
                                          style="font-size: 0.7rem;">{{ setting($setting->key, $setting->value, $setting->role) }}</textarea>
                                
                                @elseif($setting->type === 'json')
                                <textarea class="form-control form-control-sm font-monospace" 
                                          name="settings[{{ $setting->key }}]" 
                                          rows="3"
                                          style="font-size: 0.7rem;">{{ setting($setting->key, $setting->value, $setting->role) }}</textarea>
                                
                                @else
                                <input type="text" 
                                       class="form-control form-control-sm" 
                                       name="settings[{{ $setting->key }}]" 
                                       value="{{ setting($setting->key, $setting->value, $setting->role) }}"
                                       style="font-size: 0.7rem;">
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endforeach
                
                <div class="card rounded-sm">
                    <div class="card-body">
                        <button type="submit" class="btn btn-primary btn-sm rounded-sm">
                            <i class="ti ti-device-floppy me-1"></i> Save All Settings
                        </button>
                        <button type="reset" class="btn btn-secondary btn-sm rounded-sm">
                            <i class="ti ti-refresh me-1"></i> Reset Form
                        </button>
                    </div>
                </div>
            </form>
        </div>

        @if($canLetterhead)
        <!-- Letter Branding Tab -->
        <div class="tab-pane fade" id="letterhead-pane" role="tabpanel">
            @include('admin.settings._letterhead-form', ['university' => $university])
        </div>
        @endif

        @if($canRegistry)
        <!-- Letter & Registry Tab -->
        <div class="tab-pane fade" id="registry-pane" role="tabpanel">
            @include('registry._settings-form', ['universities' => $registryUniversities, 'settings' => $registrySettings, 'selectedUniversity' => $registrySelectedUniversity])
        </div>
        @endif

        <!-- HR Tab -->
        <div class="tab-pane fade" id="hr-pane" role="tabpanel">
            @include('settings.hr._form', ['settings' => $hrSettings])
        </div>

        <!-- All Settings Definitions Tab (Admin only) -->
        @hasanyrole('admin|super-admin')
        <div class="tab-pane fade" id="all-settings" role="tabpanel">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light d-flex justify-content-between align-items-center">
                    <span><i class="ti ti-list me-1"></i> Settings Definitions</span>
                    <a href="{{ route('settings.create') }}" class="btn btn-sm btn-primary">
                        <i class="ti ti-plus"></i> Add Setting
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0" style="font-size: 0.75rem;">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-2 py-1">Key</th>
                                    <th class="py-1">Label</th>
                                    <th class="py-1">Group</th>
                                    <th class="py-1">Type</th>
                                    <th class="py-1">Role</th>
                                    <th class="py-1">Sort Order</th>
                                    <th class="pe-2 py-1">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($allSettings ?? [] as $setting)
                                <tr data-setting-id="{{ $setting->id }}">
                                    <td class="ps-2 py-1"><code>{{ $setting->key }}</code></td>
                                    <td class="py-1">{{ $setting->label ?? '-' }}</td>
                                    <td class="py-1">{{ $setting->group }}</td>
                                    <td class="py-1">
                                        <span class="badge bg-secondary-subtle text-secondary rounded-sm">{{ $setting->type }}</span>
                                    </td>
                                    <td class="py-1">{{ $setting->role ?? 'Global' }}</td>
                                    <td class="py-1">{{ $setting->sort_order }}</td>
                                    <td class="pe-2 py-1">
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('settings.show', $setting->id) }}" class="btn btn-sm btn-outline-info rounded-sm" title="View">
                                                <i class="ti ti-eye"></i>
                                            </a>
                                            <a href="{{ route('settings.edit', $setting->id) }}" class="btn btn-sm btn-outline-primary rounded-sm" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger rounded-sm" title="Delete" 
                                                    onclick="confirmDelete({{ $setting->id }}, '{{ $setting->key }}')">
                                                <i class="ti ti-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-3 text-muted">No settings found</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @endhasanyrole
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-sm">
            <div class="modal-header py-1 px-2">
                <h5 class="modal-title" style="font-size: 0.8rem;">Confirm Delete</h5>
                <button type="button" class="btn-close py-0" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body py-2 px-2">
                <p style="font-size: 0.75rem;">Are you sure you want to delete <strong id="deleteSettingName"></strong>?</p>
                <p class="text-danger mb-0" style="font-size: 0.7rem;">This action cannot be undone.</p>
            </div>
            <div class="modal-footer py-1 px-2">
                <button type="button" class="btn btn-secondary btn-sm rounded-sm" data-bs-dismiss="modal" style="font-size: 0.7rem;">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm rounded-sm" style="font-size: 0.7rem;">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(id, name) {
    document.getElementById('deleteSettingName').innerText = name;
    document.getElementById('deleteForm').action = '/settings/' + id;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}

// Initialize tooltips
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
@endsection