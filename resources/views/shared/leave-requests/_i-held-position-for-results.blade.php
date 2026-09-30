    <div class="card-body p-0">
        @forelse($actingRequests as $item)
        <div class="request-item p-2 border-bottom">
            <!-- Request Header -->
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="fw-semibold" style="font-size: 0.75rem;">
                    {{ $item->staff->first_name ?? 'Unknown' }} {{ $item->staff->last_name ?? '' }}
                </span>
                <span class="badge bg-warning-subtle text-warning rounded-sm px-2 py-1" style="font-size: 0.55rem;">
                    Awaiting Response
                </span>
            </div>
            <div class="text-muted mb-1" style="font-size: 0.6rem;">
                <i class="ti ti-calendar me-1"></i>
                {{ \Carbon\Carbon::parse($item->start_date)->format('M d') }} -
                {{ \Carbon\Carbon::parse($item->end_date)->format('M d, Y') }}
                <span class="mx-1">&middot;</span>
                {{ $item->leaveType->name ?? 'N/A' }}
            </div>
            <div class="text-muted mb-2" style="font-size: 0.6rem;">
                {{ Str::limit($item->reason, 60) }}
            </div>
            <div class="d-flex gap-1">
                <button type="button" class="btn btn-primary btn-sm rounded-sm flex-fill" style="font-size: 0.6rem; padding: 0.2rem 0.4rem;" data-bs-toggle="modal" data-bs-target="#actingResponseModal{{ $item->id }}">
                    <i class="ti ti-user-check me-1"></i> Respond
                </button>
                <a href="{{ route('leave-requests.show', $item->id) }}" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.6rem; padding: 0.2rem 0.4rem;">
                    <i class="ti ti-eye"></i>
                </a>
            </div>
        </div>

        <!-- Acting Response Modal -->
        <div class="modal fade" id="actingResponseModal{{ $item->id }}" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content rounded-sm">
                    <div class="modal-header py-2 px-3 bg-light">
                        <div class="d-flex align-items-center">
                            <i class="ti ti-user-check text-primary me-2" style="font-size: 1rem;"></i>
                            <div>
                                <h5 class="modal-title" style="font-size: 0.9rem; font-weight: 600;">Hold Position Request</h5>
                                <p class="text-muted mb-0" style="font-size: 0.65rem;">{{ $item->staff->first_name ?? '' }} {{ $item->staff->last_name ?? '' }} requests you to hold their position</p>
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('leave-requests.acting.process', $item->id) }}" method="POST">
                        @csrf
                        <div class="modal-body p-3">
                            <!-- Leave Details -->
                            <div class="bg-light p-2 rounded-sm mb-3">
                                <div class="row g-2">
                                    <div class="col-6">
                                        <div class="text-muted" style="font-size: 0.6rem;">Leave Type</div>
                                        <div class="fw-semibold" style="font-size: 0.75rem;">{{ $item->leaveType->name ?? 'N/A' }}</div>
                                    </div>
                                    <div class="col-6">
                                        <div class="text-muted" style="font-size: 0.6rem;">Duration</div>
                                        <div class="fw-semibold" style="font-size: 0.75rem;">{{ $item->days }} day(s)</div>
                                    </div>
                                    <div class="col-12">
                                        <div class="text-muted" style="font-size: 0.6rem;">Leave Period</div>
                                        <div class="fw-semibold" style="font-size: 0.75rem;">
                                            {{ \Carbon\Carbon::parse($item->start_date)->format('d M Y') }} -
                                            {{ \Carbon\Carbon::parse($item->end_date)->format('d M Y') }}
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="text-muted" style="font-size: 0.6rem;">Reason</div>
                                        <div class="small" style="font-size: 0.7rem;">{{ $item->reason }}</div>
                                    </div>
                                    @if($item->handover_notes)
                                    <div class="col-12">
                                        <div class="text-muted" style="font-size: 0.6rem;">What you'll be taking on</div>
                                        <div class="small" style="font-size: 0.7rem;">
                                            <x-numbered-paragraphs :text="$item->handover_notes" />
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Comment -->
                            <div class="mb-3">
                                <label class="form-label" style="font-size: 0.65rem;">Comment (Optional)</label>
                                <textarea name="comment" class="form-control form-control-sm rounded-sm" rows="2" style="font-size: 0.7rem;" placeholder="Add any remarks..."></textarea>
                            </div>

                            <!-- Action Buttons -->
                            <div class="d-flex gap-2">
                                <input type="hidden" name="action" id="action_value_{{ $item->id }}">
                                <button type="button" class="btn btn-success btn-sm flex-fill rounded-sm" onclick="submitActingResponse({{ $item->id }}, 'accept')" style="font-size: 0.7rem; padding: 0.4rem;">
                                    <i class="ti ti-check me-1"></i> Accept & Hold Position
                                </button>
                                <button type="button" class="btn btn-danger btn-sm flex-fill rounded-sm" onclick="submitActingResponse({{ $item->id }}, 'decline')" style="font-size: 0.7rem; padding: 0.4rem;">
                                    <i class="ti ti-x me-1"></i> Decline
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="text-center py-4">
            <i class="ti ti-user-check" style="font-size: 2rem; color: #CBD5E1;"></i>
            <p class="text-muted mt-2" style="font-size: 0.7rem;">No pending acting requests.</p>
        </div>
        @endforelse
    </div>
    <div class="card-footer py-1 px-2">
        <x-pagination :items="$actingRequests" :perPageOptions="[10, 25, 50, 100]" :ajax="true" />
    </div>
