    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm mb-0" style="font-size: 0.75rem;">
                <thead>
                    <tr>
                        <th class="ps-2 py-1">Staff</th>
                        <th class="py-1">Destination</th>
                        <th class="py-1">Travel Period</th>
                        <th class="py-1">Days</th>
                        <th class="py-1">Reason</th>
                        <th class="py-1">Current Level</th>
                        <th class="pe-2 py-1">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pendingRequests as $request)
                    <tr>
                        <td class="ps-2 py-1">
                            <strong>{{ $request->staff->first_name }} {{ $request->staff->last_name }}</strong>
                            <div class="text-muted" style="font-size: 0.6rem;">{{ $request->staff->staff_id }}</div>
                        </td>
                        <td class="py-1">{{ $request->destination }}</td>
                        <td class="py-1">
                            {{ \Carbon\Carbon::parse($request->start_date)->format('d M Y') }} -
                            {{ \Carbon\Carbon::parse($request->end_date)->format('d M Y') }}
                        </td>
                        <td class="py-1">{{ $request->days }}</td>
                        <td class="py-1">{{ ucfirst($request->travel_reason) }}</td>
                        <td class="py-1">
                            <span class="badge bg-info-subtle text-info rounded-sm" style="font-size: 0.6rem;">
                                {{ ucfirst(str_replace('_', ' ', $request->current_approval_level)) }}
                            </span>
                        </td>
                        <td class="pe-2 py-1">
                            <button type="button" class="btn btn-success btn-sm rounded-sm" data-bs-toggle="modal" data-bs-target="#approveModal{{ $request->id }}" style="padding: 0.1rem 0.4rem; font-size: 0.6rem;">
                                <i class="ti ti-check"></i> Approve
                            </button>
                            <button type="button" class="btn btn-danger btn-sm rounded-sm" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $request->id }}" style="padding: 0.1rem 0.4rem; font-size: 0.6rem;">
                                <i class="ti ti-x"></i> Reject
                            </button>
                        </td>
                    </tr>

                    <!-- Approve Modal -->
                    <div class="modal fade" id="approveModal{{ $request->id }}" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content rounded-sm">
                                <form action="{{ route('travel-permissions.approve', $request->id) }}" method="POST">
                                    @csrf
                                    <div class="modal-header py-1 px-2">
                                        <h5 class="modal-title" style="font-size: 0.8rem;">Approve Travel Request</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body py-2 px-2">
                                        <p style="font-size: 0.75rem;">Approve travel request to <strong>{{ $request->destination }}</strong> for <strong>{{ $request->staff->first_name }} {{ $request->staff->last_name }}</strong>?</p>
                                        <div class="mb-2">
                                            <label class="form-label" style="font-size: 0.65rem;">Comment (Optional)</label>
                                            <textarea name="comment" class="form-control form-control-sm rounded-sm" rows="2" style="font-size: 0.7rem;"></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer py-1 px-2">
                                        <button type="button" class="btn btn-secondary btn-sm rounded-sm" data-bs-dismiss="modal" style="font-size: 0.65rem;">Cancel</button>
                                        <button type="submit" class="btn btn-success btn-sm rounded-sm" style="font-size: 0.65rem;">Approve</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Reject Modal -->
                    <div class="modal fade" id="rejectModal{{ $request->id }}" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content rounded-sm">
                                <form action="{{ route('travel-permissions.reject', $request->id) }}" method="POST">
                                    @csrf
                                    <div class="modal-header py-1 px-2">
                                        <h5 class="modal-title" style="font-size: 0.8rem;">Reject Travel Request</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body py-2 px-2">
                                        <p style="font-size: 0.75rem;">Reject travel request to <strong>{{ $request->destination }}</strong> for <strong>{{ $request->staff->first_name }} {{ $request->staff->last_name }}</strong>?</p>
                                        <div class="mb-2">
                                            <label class="form-label" style="font-size: 0.65rem;">Rejection Reason <span class="text-danger">*</span></label>
                                            <textarea name="rejection_reason" class="form-control form-control-sm rounded-sm" rows="2" style="font-size: 0.7rem;" required></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer py-1 px-2">
                                        <button type="button" class="btn btn-secondary btn-sm rounded-sm" data-bs-dismiss="modal" style="font-size: 0.65rem;">Cancel</button>
                                        <button type="submit" class="btn btn-danger btn-sm rounded-sm" style="font-size: 0.65rem;">Reject</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-3 text-muted">No pending travel approvals found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer py-1 px-2">
        <x-pagination :items="$pendingRequests" :perPageOptions="[10, 25, 50, 100]" :ajax="true" />
    </div>
