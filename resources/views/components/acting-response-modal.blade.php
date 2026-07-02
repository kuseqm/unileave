@props(['leaveRequest', 'token'])

<div class="modal fade" id="actingResponseModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-sm">
            <div class="modal-header py-2 px-3 bg-light">
                <div class="d-flex align-items-center">
                    <i class="ti ti-user-check text-primary me-2" style="font-size: 1rem;"></i>
                    <div>
                        <h5 class="modal-title" style="font-size: 0.9rem; font-weight: 600;">Hold Position Request</h5>
                        <p class="text-muted mb-0" style="font-size: 0.65rem;">{{ $leaveRequest->staff->first_name }} {{ $leaveRequest->staff->last_name }} requests you to hold their position</p>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="actingResponseForm" action="{{ route('leave-requests.acting.process', $leaveRequest->id) }}" method="POST">
                @csrf
                <div class="modal-body p-3">
                    <!-- Leave Details -->
                    <div class="bg-light p-2 rounded-sm mb-3">
                        <div class="row g-2">
                            <div class="col-6">
                                <div class="text-muted" style="font-size: 0.6rem;">Leave Type</div>
                                <div class="fw-semibold" style="font-size: 0.75rem;">{{ $leaveRequest->leaveType->name ?? 'N/A' }}</div>
                            </div>
                            <div class="col-6">
                                <div class="text-muted" style="font-size: 0.6rem;">Duration</div>
                                <div class="fw-semibold" style="font-size: 0.75rem;">{{ $leaveRequest->days }} day(s)</div>
                            </div>
                            <div class="col-12">
                                <div class="text-muted" style="font-size: 0.6rem;">Leave Period</div>
                                <div class="fw-semibold" style="font-size: 0.75rem;">
                                    {{ \Carbon\Carbon::parse($leaveRequest->start_date)->format('d M Y') }} - 
                                    {{ \Carbon\Carbon::parse($leaveRequest->end_date)->format('d M Y') }}
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="text-muted" style="font-size: 0.6rem;">Reason</div>
                                <div class="small" style="font-size: 0.7rem;">{{ $leaveRequest->reason }}</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="mb-3">
                        <label class="form-label" style="font-size: 0.65rem;">Comment (Optional)</label>
                        <textarea name="comment" class="form-control form-control-sm rounded-sm" rows="2" style="font-size: 0.7rem;" placeholder="Add any remarks..."></textarea>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <input type="hidden" name="action" id="action_value">
                        <button type="button" class="btn btn-success btn-sm flex-fill rounded-sm" onclick="submitActingResponse('accept')" style="font-size: 0.7rem; padding: 0.4rem;">
                            <i class="ti ti-check me-1"></i> Accept & Hold Position
                        </button>
                        <button type="button" class="btn btn-danger btn-sm flex-fill rounded-sm" onclick="submitActingResponse('decline')" style="font-size: 0.7rem; padding: 0.4rem;">
                            <i class="ti ti-x me-1"></i> Decline
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function submitActingResponse(action) {
    document.getElementById('action_value').value = action;
    document.getElementById('actingResponseForm').submit();
}
</script>