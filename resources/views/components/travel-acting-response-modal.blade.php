@props(['travelRequest'])

<div class="modal fade" id="travelActingResponseModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-sm">
            <div class="modal-header py-2 px-3 bg-light">
                <div class="d-flex align-items-center">
                    <i class="ti ti-plane text-primary me-2" style="font-size: 1rem;"></i>
                    <div>
                        <h5 class="modal-title" style="font-size: 0.9rem; font-weight: 600;">Travel Permission - Acting Request</h5>
                        <p class="text-muted mb-0" style="font-size: 0.65rem;">{{ $travelRequest->staff->first_name }} {{ $travelRequest->staff->last_name }} requests you to cover their duties</p>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="travelActingResponseForm" action="{{ route('travel-permissions.acting.process', $travelRequest->id) }}" method="POST">
                @csrf
                <div class="modal-body p-3">
                    <!-- Travel Details -->
                    <div class="bg-light p-2 rounded-sm mb-3">
                        <div class="row g-2">
                            <div class="col-6">
                                <div class="text-muted" style="font-size: 0.6rem;">Destination</div>
                                <div class="fw-semibold" style="font-size: 0.75rem;">{{ $travelRequest->destination }}</div>
                            </div>
                            <div class="col-6">
                                <div class="text-muted" style="font-size: 0.6rem;">Duration</div>
                                <div class="fw-semibold" style="font-size: 0.75rem;">{{ $travelRequest->days }} day(s)</div>
                            </div>
                            <div class="col-12">
                                <div class="text-muted" style="font-size: 0.6rem;">Travel Period</div>
                                <div class="fw-semibold" style="font-size: 0.75rem;">
                                    {{ \Carbon\Carbon::parse($travelRequest->start_date)->format('d M Y') }} - 
                                    {{ \Carbon\Carbon::parse($travelRequest->end_date)->format('d M Y') }}
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="text-muted" style="font-size: 0.6rem;">Reason</div>
                                <div class="small" style="font-size: 0.7rem;">{{ ucfirst($travelRequest->travel_reason) }} travel</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Comment -->
                    <div class="mb-3">
                        <label class="form-label" style="font-size: 0.65rem;">Comment (Optional)</label>
                        <textarea name="comment" class="form-control form-control-sm rounded-sm" rows="2" style="font-size: 0.7rem;" placeholder="Add any remarks..."></textarea>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="d-flex gap-2">
                        <input type="hidden" name="action" id="travel_action_value">
                        <button type="button" class="btn btn-success btn-sm flex-fill rounded-sm" onclick="submitTravelActingResponse('accept')" style="font-size: 0.7rem; padding: 0.4rem;">
                            <i class="ti ti-check me-1"></i> Accept & Cover Duties
                        </button>
                        <button type="button" class="btn btn-danger btn-sm flex-fill rounded-sm" onclick="submitTravelActingResponse('decline')" style="font-size: 0.7rem; padding: 0.4rem;">
                            <i class="ti ti-x me-1"></i> Decline
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function submitTravelActingResponse(action) {
    document.getElementById('travel_action_value').value = action;
    document.getElementById('travelActingResponseForm').submit();
}
</script>