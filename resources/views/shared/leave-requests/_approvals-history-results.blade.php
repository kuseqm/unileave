<div class="table-responsive">
    <table class="table table-sm mb-0" style="font-size: 0.7rem;">
        <thead class="bg-light">
            <tr>
                <th class="ps-2 py-1" width="50">#</th>
                <th class="py-1">Staff</th>
                <th class="py-1 text-center" width="70">Days</th>
                <th class="py-1 text-center" width="100">Status</th>
                <th class="pe-2 py-1 text-center" width="80">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($approvals as $index => $approval)
            @php
                $leaveRequest = $approval->leaveRequest;
            @endphp
            @if($leaveRequest)
            <tr>
                <td class="ps-2 py-1">{{ $approvals->firstItem() + $index }}</td>
                <td class="py-1">
                    <div class="fw-semibold">{{ $leaveRequest->staff->full_name ?? 'N/A' }}</div>
                    <div class="text-muted" style="font-size: 0.55rem;">{{ $leaveRequest->staff->staff_id ?? '' }}</div>
                </td>
                <td class="py-1 text-center">{{ $leaveRequest->days }} day(s)</td>
                <td class="py-1 text-center">
                    @if($approval->action == 'approved')
                        <span class="badge bg-success-subtle text-success rounded-pill" style="font-size: 0.6rem;">Approved</span>
                    @elseif($approval->action == 'rejected')
                        <span class="badge bg-danger-subtle text-danger rounded-pill" style="font-size: 0.6rem;">Rejected</span>
                    @else
                        <span class="badge bg-secondary-subtle text-secondary rounded-pill" style="font-size: 0.6rem;">{{ ucfirst($approval->action) }}</span>
                    @endif
                </td>
                <td class="pe-2 py-1 text-center">
                    <a href="{{ route('leave-requests.approval-details', $leaveRequest->id) }}" 
                       class="btn btn-sm btn-outline-info rounded-sm" 
                       style="padding: 0.15rem 0.4rem; font-size: 0.6rem;"
                       title="View Full Details">
                        <i class="ti ti-eye me-1"></i> View
                    </a>
                </td>
            </tr>
            @else
            <tr>
                <td colspan="5" class="text-center py-2 text-muted" style="font-size: 0.7rem;">
                    <i class="ti ti-alert-circle me-1"></i>
                    Request data not available
                </td>
            </tr>
            @endif
            @empty
            <tr>
                <td colspan="5" class="text-center py-4 text-muted" style="font-size: 0.7rem;">
                    <i class="ti ti-history mb-2 d-block" style="font-size: 1.5rem;"></i>
                    <p>No approval records found</p>
                    <small class="text-muted">When you approve or reject a leave request, it will appear here.</small>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="card-footer py-2 px-2 bg-light">
    <x-pagination :items="$approvals" :ajax="true" />
</div>
