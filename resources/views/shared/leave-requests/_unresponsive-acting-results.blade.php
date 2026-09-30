    @php
        $view = $view ?? 'active';
    @endphp
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm mb-0" style="font-size: 0.75rem;">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-2 py-1">Staff on Leave</th>
                        <th class="py-1">Acting Staff (No Response)</th>
                        <th class="py-1">Leave Type</th>
                        <th class="py-1">Dates</th>
                        @if($view === 'dismissed')
                        <th class="py-1">Dismissed</th>
                        @else
                        <th class="py-1">Waiting Since</th>
                        @endif
                        <th class="pe-2 py-1">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($unresponsiveRequests as $request)
                    @php
                        $waitingSince = $request->acting_requested_at ?? $request->created_at;
                        $hoursWaiting = $waitingSince->diffInHours(now());
                        $reassignChoices = \App\Models\User::where('status', 'active')
                            ->where('department_id', $request->staff->department_id ?? null)
                            ->where('id', '!=', $request->staff_id)
                            ->where('id', '!=', $request->acting_staff_id)
                            ->orderBy('first_name')
                            ->get();
                    @endphp
                    <tr>
                        <td class="ps-2 py-1">
                            <span class="fw-semibold">{{ $request->staff->first_name ?? 'Unknown' }} {{ $request->staff->last_name ?? '' }}</span>
                            <div class="text-muted" style="font-size: 0.6rem;">{{ $request->staff->staff_id ?? '' }}</div>
                        </td>
                        <td class="py-1">
                            <span class="fw-semibold">{{ $request->actingStaff->first_name ?? 'Unknown' }} {{ $request->actingStaff->last_name ?? '' }}</span>
                            <div class="text-muted" style="font-size: 0.6rem;">{{ $request->actingStaff->staff_id ?? '' }}</div>
                        </td>
                        <td class="py-1">{{ $request->leaveType->name ?? 'N/A' }}</td>
                        <td class="py-1">{{ \Carbon\Carbon::parse($request->start_date)->format('d M Y') }} - {{ \Carbon\Carbon::parse($request->end_date)->format('d M Y') }}</td>
                        @if($view === 'dismissed')
                        <td class="py-1">
                            <span style="font-size: 0.65rem;">{{ $request->dismissedBy->first_name ?? 'Unknown' }} {{ $request->dismissedBy->last_name ?? '' }}</span>
                            <div class="text-muted" style="font-size: 0.6rem;">{{ $request->acting_escalation_dismissed_at?->format('d M Y, H:i') }}</div>
                            @if($request->acting_escalation_dismissal_reason)
                            <div class="text-muted fst-italic" style="font-size: 0.6rem;">"{{ Str::limit($request->acting_escalation_dismissal_reason, 60) }}"</div>
                            @endif
                        </td>
                        @else
                        <td class="py-1">
                            @if($hoursWaiting >= 168)
                                <span class="badge bg-danger-subtle text-danger rounded-sm" style="font-size: 0.6rem;">{{ floor($hoursWaiting / 24) }} days</span>
                            @elseif($hoursWaiting >= 72)
                                <span class="badge bg-warning-subtle text-warning rounded-sm" style="font-size: 0.6rem;">{{ floor($hoursWaiting / 24) }} days</span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary rounded-sm" style="font-size: 0.6rem;">{{ $hoursWaiting }} hrs</span>
                            @endif
                        </td>
                        @endif
                        <td class="pe-2 py-1">
                            <div class="d-flex gap-1">
                                @if($view === 'dismissed')
                                    <form action="{{ route('leave-requests.restore-unresponsive', $request->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-primary rounded-sm" style="padding: 0.15rem 0.4rem; font-size: 0.6rem;" title="Restore to active list">
                                            <i class="ti ti-restore"></i>
                                        </button>
                                    </form>
                                @else
                                    <button type="button" class="btn btn-sm btn-outline-primary rounded-sm" style="padding: 0.15rem 0.4rem; font-size: 0.6rem;" title="Reassign to someone else" data-bs-toggle="modal" data-bs-target="#reassignModal{{ $request->id }}">
                                        <i class="ti ti-user-share"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-success rounded-sm" style="padding: 0.15rem 0.4rem; font-size: 0.6rem;" title="Approve on their behalf" data-bs-toggle="modal" data-bs-target="#overrideModal{{ $request->id }}">
                                        <i class="ti ti-shield-check"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-sm" style="padding: 0.15rem 0.4rem; font-size: 0.6rem;" title="Dismiss (no action needed right now)" data-bs-toggle="modal" data-bs-target="#dismissModal{{ $request->id }}">
                                        <i class="ti ti-eye-off"></i>
                                    </button>
                                @endif
                                <a href="{{ route('leave-requests.show', $request->id) }}" class="btn btn-sm btn-outline-info rounded-sm" style="padding: 0.15rem 0.4rem; font-size: 0.6rem;" title="View Request">
                                    <i class="ti ti-eye"></i>
                                </a>
                            </div>
                        </td>
                    </tr>

                    @if($view !== 'dismissed')
                    <!-- Reassign Modal -->
                    <div class="modal fade" id="reassignModal{{ $request->id }}" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content rounded-sm">
                                <div class="modal-header py-2 px-3 bg-light">
                                    <div>
                                        <h5 class="modal-title" style="font-size: 0.9rem; font-weight: 600;">Reassign Acting Staff</h5>
                                        <p class="text-muted mb-0" style="font-size: 0.65rem;">
                                            {{ $request->actingStaff->first_name ?? 'The current acting staff' }} hasn't responded. Choose someone else to hold {{ $request->staff->first_name ?? 'this staff member' }}'s position.
                                        </p>
                                    </div>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form action="{{ route('leave-requests.reassign-acting', $request->id) }}" method="POST">
                                    @csrf
                                    <div class="modal-body p-3">
                                        <label class="form-label" style="font-size: 0.65rem;">New Acting Staff <span class="text-danger">*</span></label>
                                        <select name="new_acting_staff_id" class="form-select form-select-sm rounded-sm" style="font-size: 0.7rem;" required>
                                            <option value="">Select staff member...</option>
                                            @forelse($reassignChoices as $choice)
                                            <option value="{{ $choice->id }}">{{ $choice->first_name }} {{ $choice->last_name }} ({{ $choice->staff_id }})</option>
                                            @empty
                                            <option value="" disabled>No other active staff found in this department</option>
                                            @endforelse
                                        </select>
                                        <div class="text-muted mt-1" style="font-size: 0.6rem;">Only active staff in {{ $request->staff->first_name ?? 'the requester' }}'s department are shown.</div>
                                    </div>
                                    <div class="modal-footer py-2 px-3">
                                        <button type="button" class="btn btn-secondary btn-sm rounded-sm" data-bs-dismiss="modal" style="font-size: 0.7rem;">Cancel</button>
                                        <button type="submit" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem;" {{ $reassignChoices->isEmpty() ? 'disabled' : '' }}>
                                            <i class="ti ti-user-share me-1"></i> Reassign
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Override Approve Modal -->
                    <div class="modal fade" id="overrideModal{{ $request->id }}" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content rounded-sm">
                                <div class="modal-header py-2 px-3 bg-light">
                                    <div>
                                        <h5 class="modal-title" style="font-size: 0.9rem; font-weight: 600;">Approve on Behalf of Acting Staff</h5>
                                        <p class="text-muted mb-0" style="font-size: 0.65rem;">
                                            This skips {{ $request->actingStaff->first_name ?? 'the acting staff' }}'s response and forwards the request straight to HOD approval. Recorded as an administrative override, not a real acceptance.
                                        </p>
                                    </div>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form action="{{ route('leave-requests.override-acting', $request->id) }}" method="POST">
                                    @csrf
                                    <div class="modal-body p-3">
                                        <label class="form-label" style="font-size: 0.65rem;">Reason for Override <span class="text-danger">*</span></label>
                                        <textarea name="override_reason" class="form-control form-control-sm rounded-sm" rows="3" style="font-size: 0.7rem;" placeholder="e.g. Confirmed verbally with acting staff, system access unavailable to them..." required minlength="5"></textarea>
                                    </div>
                                    <div class="modal-footer py-2 px-3">
                                        <button type="button" class="btn btn-secondary btn-sm rounded-sm" data-bs-dismiss="modal" style="font-size: 0.7rem;">Cancel</button>
                                        <button type="submit" class="btn btn-success btn-sm rounded-sm" style="font-size: 0.7rem;">
                                            <i class="ti ti-shield-check me-1"></i> Approve on Their Behalf
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Dismiss Modal -->
                    <div class="modal fade" id="dismissModal{{ $request->id }}" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content rounded-sm">
                                <div class="modal-header py-2 px-3 bg-light">
                                    <div>
                                        <h5 class="modal-title" style="font-size: 0.9rem; font-weight: 600;">Dismiss from Escalation List</h5>
                                        <p class="text-muted mb-0" style="font-size: 0.65rem;">
                                            This just hides it from the active list — nothing changes about the request itself. You can restore it anytime from the Dismissed tab.
                                        </p>
                                    </div>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form action="{{ route('leave-requests.dismiss-unresponsive', $request->id) }}" method="POST">
                                    @csrf
                                    <div class="modal-body p-3">
                                        <label class="form-label" style="font-size: 0.65rem;">Reason <span class="text-danger">*</span></label>
                                        <textarea name="dismissal_reason" class="form-control form-control-sm rounded-sm" rows="3" style="font-size: 0.7rem;" placeholder="e.g. Handling this directly with both staff, no system action needed right now..." required minlength="5"></textarea>
                                    </div>
                                    <div class="modal-footer py-2 px-3">
                                        <button type="button" class="btn btn-secondary btn-sm rounded-sm" data-bs-dismiss="modal" style="font-size: 0.7rem;">Cancel</button>
                                        <button type="submit" class="btn btn-secondary btn-sm rounded-sm" style="font-size: 0.7rem;">
                                            <i class="ti ti-eye-off me-1"></i> Dismiss
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endif
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-3 text-muted" style="font-size: 0.7rem;">
                            @if($view === 'dismissed')
                                No dismissed requests.
                            @else
                                No unresponsive acting requests right now.
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer py-1 px-2">
        <x-pagination :items="$unresponsiveRequests" :perPageOptions="[10, 25, 50, 100]" :ajax="true" />
    </div>
