<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table table-sm mb-0" style="font-size: 0.75rem;">
            <thead class="bg-light">
                <tr>
                    <th class="ps-2 py-1">Staff</th>
                    <th class="py-1">Type</th>
                    <th class="py-1">Decision</th>
                    <th class="py-1">Date</th>
                    <th class="pe-2 py-1 text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($approvals as $log)
                @php $permission = $log->permissionRequest; @endphp
                <tr>
                    <td class="ps-2 py-1">
                        @if($permission)
                            <div class="fw-semibold">{{ $permission->staff->full_name ?? 'N/A' }}</div>
                            <div class="text-muted" style="font-size: 0.6rem;">{{ $permission->staff->staff_id ?? '' }}</div>
                        @else
                            <span class="text-muted">Request #{{ $log->record_id }} (deleted)</span>
                        @endif
                    </td>
                    <td class="py-1">{{ $permission->permission_type_label ?? '-' }}</td>
                    <td class="py-1">
                        @if($log->action === 'approve')
                            <span class="badge bg-success-subtle text-success rounded-pill">Approved</span>
                        @else
                            <span class="badge bg-danger-subtle text-danger rounded-pill">Rejected</span>
                        @endif
                    </td>
                    <td class="py-1 text-muted">{{ $log->created_at->format('d M Y, H:i') }}</td>
                    <td class="pe-2 py-1 text-center">
                        @if($permission)
                        <a href="{{ route('permissions.show', $permission->id) }}" class="btn btn-sm btn-outline-info rounded-sm" style="padding: 0.15rem 0.4rem; font-size: 0.6rem;">
                            <i class="ti ti-eye me-1"></i> View
                        </a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-4 text-muted" style="font-size: 0.7rem;">
                        <i class="ti ti-history mb-2 d-block" style="font-size: 1.5rem;"></i>
                        <p class="mb-0">No approval records found</p>
                        <small class="text-muted">When you approve or reject a permission request, it will appear here.</small>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@if($approvals->hasPages() || $approvals->total() > 0)
<div class="card-footer py-1 px-2">
    <x-pagination :items="$approvals" :ajax="true" />
</div>
@endif
