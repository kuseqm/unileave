@props([
    'id' => null,
    'name' => '',
    'route' => '',
    'title' => 'Confirm Delete',
    'message' => 'Are you sure you want to delete this item?',
    'warning' => null,
    'cascadeWarning' => null,
    'type' => 'default' // default, blackout, university, campus, department, leave_type, staff
])

<div class="modal fade" id="deleteModal{{ $id ?? '' }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-sm">
            <div class="modal-header py-1 px-2 bg-danger-subtle">
                <h5 class="modal-title" style="font-size: 0.8rem;">
                    <i class="ti ti-alert-triangle text-danger me-1"></i> {{ $title }}
                </h5>
                <button type="button" class="btn-close py-0" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body py-2 px-2">
                <p style="font-size: 0.75rem;">{{ $message }}</p>
                
                @if($warning)
                <div class="alert alert-warning py-1 px-2 mb-0 rounded-sm" style="font-size: 0.65rem;">
                    <i class="ti ti-alert-circle me-1"></i> {{ $warning }}
                </div>
                @endif
                
                @if($cascadeWarning)
                <div class="alert alert-danger py-1 px-2 mt-2 rounded-sm" style="font-size: 0.65rem;">
                    <i class="ti ti-trash me-1"></i> <strong>Cascade Deletion Warning:</strong><br>
                    {{ $cascadeWarning }}
                </div>
                @endif
                
                <!-- Type-specific warnings -->
                @if($type == 'blackout')
                <div class="alert alert-info py-1 px-2 mt-2 rounded-sm" style="font-size: 0.65rem;">
                    <i class="ti ti-calendar me-1"></i> <strong>Impact:</strong><br>
                    - Staff will be able to apply for leave during this period<br>
                    - No leave requests will be affected (only future applications)
                </div>
                @endif
                
                @if($type == 'university')
                <div class="alert alert-danger py-1 px-2 mt-2 rounded-sm" style="font-size: 0.65rem;">
                    <i class="ti ti-building me-1"></i> <strong>This will permanently delete:</strong><br>
                    - All campuses under this university<br>
                    - All departments under those campuses<br>
                    - All staff assignments to those departments
                </div>
                @endif
                
                @if($type == 'campus')
                <div class="alert alert-danger py-1 px-2 mt-2 rounded-sm" style="font-size: 0.65rem;">
                    <i class="ti ti-map-pin me-1"></i> <strong>This will permanently delete:</strong><br>
                    - All departments under this campus<br>
                    - All staff assignments to those departments
                </div>
                @endif
                
                @if($type == 'department')
                <div class="alert alert-danger py-1 px-2 mt-2 rounded-sm" style="font-size: 0.65rem;">
                    <i class="ti ti-building-community me-1"></i> <strong>This will permanently delete:</strong><br>
                    - All staff assignments to this department<br>
                    - Staff will need to be reassigned
                </div>
                @endif
                
                @if($type == 'leave_type')
                <div class="alert alert-warning py-1 px-2 mt-2 rounded-sm" style="font-size: 0.65rem;">
                    <i class="ti ti-calendar-stats me-1"></i> <strong>Note:</strong><br>
                    - Existing leave requests of this type will NOT be deleted<br>
                    - They will show as "Unknown Type"<br>
                    - Future applications cannot use this type
                </div>
                @endif
                
                @if($type == 'staff')
                <div class="alert alert-danger py-1 px-2 mt-2 rounded-sm" style="font-size: 0.65rem;">
                    <i class="ti ti-user-x me-1"></i> <strong>This will:</strong><br>
                    - Remove all access for this staff member<br>
                    - Archive their leave request history<br>
                    - Remove them from acting staff assignments
                </div>
                @endif
            </div>
            <div class="modal-footer py-1 px-2">
                <button type="button" class="btn btn-secondary btn-sm rounded-sm" data-bs-dismiss="modal" style="font-size: 0.7rem;">
                    <i class="ti ti-x me-1"></i> Cancel
                </button>
                <form action="{{ $route }}" method="POST" class="d-inline">
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

<script>
function showDeleteModal(itemId, itemName, itemType, itemRoute) {
    // Find the modal for this specific item
    const modal = document.getElementById('deleteModal' + itemId);
    if (modal) {
        // Update the form action
        const form = modal.querySelector('form');
        if (form) {
            form.action = itemRoute;
        }
        // Update the item name in message if needed
        const messageSpan = modal.querySelector('.modal-body p');
        if (messageSpan && itemName) {
            messageSpan.innerHTML = 'Are you sure you want to delete <strong>' + itemName + '</strong>?';
        }
        // Show the modal
        new bootstrap.Modal(modal).show();
    }
}
</script>