@extends('layouts.master')

@section('title', 'Setting Details')

@section('content')
<div class="container-fluid px-2">
    
    <x-breadcrumb :items="[
        ['name' => 'Settings', 'url' => route('settings.index')],
        ['name' => 'Details', 'url' => '#']
    ]" />
    
    <x-page-header title="SETTING DETAILS" description="View setting configuration">
        <div class="d-flex gap-2">
            <a href="{{ route('settings.index') }}" class="btn btn-secondary btn-sm rounded-sm">
                <i class="ti ti-arrow-left me-1"></i> Back
            </a>
            <a href="{{ route('settings.edit', $setting->id) }}" class="btn btn-primary btn-sm rounded-sm">
                <i class="ti ti-edit me-1"></i> Edit
            </a>
            <button type="button" class="btn btn-danger btn-sm rounded-sm" onclick="confirmDelete({{ $setting->id }}, '{{ $setting->key }}')">
                <i class="ti ti-trash me-1"></i> Delete
            </button>
        </div>
    </x-page-header>
    
    <div class="card rounded-sm">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-sm" style="font-size: 0.75rem;">
                        <tr>
                            <th width="150">Key:</th>
                            <td><code>{{ $setting->key }}</code></td>
                        </tr>
                        <tr>
                            <th>Label:</th>
                            <td>{{ $setting->label ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Group:</th>
                            <td>{{ $setting->group }}</td>
                        </tr>
                        <tr>
                            <th>Type:</th>
                            <td><span class="badge bg-secondary-subtle text-secondary rounded-sm">{{ $setting->type }}</span></td>
                        </tr>
                        <tr>
                            <th>Current Value:</th>
                            <td>
                                @if($setting->type === 'boolean')
                                    <span class="badge bg-{{ setting($setting->key) ? 'success' : 'danger' }}-subtle rounded-sm">
                                        {{ setting($setting->key) ? 'Yes' : 'No' }}
                                    </span>
                                @elseif($setting->type === 'json')
                                    <pre class="bg-light p-1 rounded-sm mb-0" style="font-size: 0.65rem;">{{ json_encode(setting($setting->key, $setting->value, $setting->role), JSON_PRETTY_PRINT) }}</pre>
                                @else
                                    <pre class="bg-light p-1 rounded-sm mb-0" style="font-size: 0.65rem;">{{ setting($setting->key, $setting->value, $setting->role) ?? '-' }}</pre>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Default Value:</th>
                            <td><pre class="bg-light p-1 rounded-sm mb-0" style="font-size: 0.65rem;">{{ $setting->value ?? '-' }}</pre></td>
                        </tr>
                        <tr>
                            <th>Role:</th>
                            <td>{{ $setting->role ?? 'Global (All Roles)' }}</td>
                        </tr>
                        <tr>
                            <th>Sort Order:</th>
                            <td>{{ $setting->sort_order }}</td>
                        </tr>
                        <tr>
                            <th>Encrypted:</th>
                            <td>{{ $setting->is_encrypted ? 'Yes' : 'No' }}</td>
                        </tr>
                        <tr>
                            <th>Description:</th>
                            <td>{{ $setting->description ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Created At:</th>
                            <td>{{ $setting->created_at ? $setting->created_at->format('d M Y H:i:s') : '-' }}</td>
                        </tr>
                        <tr>
                            <th>Last Updated:</th>
                            <td>{{ $setting->updated_at ? $setting->updated_at->format('d M Y H:i:s') : '-' }}</td>
                        </tr>
                    </table>
                </div>
                
                @if($setting->options)
                <div class="col-md-6">
                    <div class="card rounded-sm">
                        <div class="card-header py-1 px-2 bg-light">
                            <i class="ti ti-list"></i> Options
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-sm mb-0" style="font-size: 0.7rem;">
                                <thead>
                                    <tr>
                                        <th class="ps-2">Value</th>
                                        <th class="pe-2">Label</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $options = is_array($setting->options) ? $setting->options : json_decode($setting->options, true);
                                    @endphp
                                    @foreach($options as $optValue => $optLabel)
                                    <tr>
                                        <td class="ps-2"><code>{{ $optValue }}</code></td>
                                        <td class="pe-2">{{ $optLabel }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
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
</script>
@endsection