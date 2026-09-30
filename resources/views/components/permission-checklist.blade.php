@props([
    'permissions',
    'checked' => [],
])

@php
    // Group by the verb prefix (view_, manage_, approve_, create_, ...) so 70+
    // permissions read as a checklist instead of a wall of checkboxes. The handful
    // of legacy space-named permissions (no underscore) get their own group.
    $grouped = $permissions->groupBy(function ($permission) {
        if (! str_contains($permission->name, '_')) {
            return 'Legacy';
        }

        $verb = explode('_', $permission->name)[0];

        return match ($verb) {
            'view' => 'View',
            'manage' => 'Manage',
            'approve' => 'Approve',
            'create' => 'Create',
            'edit' => 'Edit',
            'delete' => 'Delete',
            'export' => 'Export',
            'import' => 'Import',
            'apply' => 'Apply',
            'reject' => 'Reject',
            'cancel' => 'Cancel',
            'assign' => 'Assign',
            'reset' => 'Reset',
            'request' => 'Request',
            'accept' => 'Accept',
            'record' => 'Record',
            'verify' => 'Verify',
            'submit' => 'Submit',
            'prepare' => 'Prepare',
            'track' => 'Track',
            'delegate' => 'Delegate',
            'restore' => 'Restore',
            default => 'Other',
        };
    })->sortKeys();
@endphp

<div class="row g-2">
    @foreach($grouped as $groupName => $groupPermissions)
    <div class="col-md-6">
        <div class="card rounded-sm h-100">
            <div class="card-header py-1 px-2 bg-light">
                <span style="font-size: 0.65rem; font-weight: 600;">{{ $groupName }}</span>
            </div>
            <div class="card-body py-2 px-2">
                @foreach($groupPermissions as $permission)
                <div class="form-check">
                    <input type="checkbox"
                           name="permissions[]"
                           value="{{ $permission->name }}"
                           class="form-check-input permission-checkbox"
                           id="perm_{{ $permission->id }}"
                           {{ in_array($permission->name, $checked) ? 'checked' : '' }}>
                    <label class="form-check-label" for="perm_{{ $permission->id }}" style="font-size: 0.68rem;">
                        {{ ucfirst(str_replace('_', ' ', $permission->name)) }}
                    </label>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endforeach
</div>
