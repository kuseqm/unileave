    <div class="card-body p-0">
        @if($pendingRequests->count() > 0)
        <div class="table-responsive">
            <table class="table table-sm mb-0" style="font-size: 0.75rem;">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-2 py-1">Staff</th>
                        <th class="py-1">Type</th>
                        <th class="py-1">Date & Time</th>
                        <th class="py-1">Hours</th>
                        <th class="py-1">Reason</th>
                        <th class="pe-2 py-1">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pendingRequests as $request)
                    @php
                        // The same list mixes HOD-stage and HR-stage rows (an admin
                        // viewer sees both), so each row's action routes to whichever
                        // stage it's actually pending at.
                        $isHodStage = $request->hod_status === 'pending';
                        $approveRoute = $isHodStage ? route('permissions.hod.approve', $request->id) : route('permissions.hr.approve', $request->id);
                        $rejectRoute = $isHodStage ? route('permissions.hod.reject', $request->id) : route('permissions.hr.reject', $request->id);
                        $commentField = $isHodStage ? 'hod_comment' : 'hr_comment';
                    @endphp
                    <tr>
                        <td class="ps-2 py-1">
                            <span class="fw-semibold">{{ $request->staff->first_name }} {{ $request->staff->last_name }}</span>
                            <div class="text-muted" style="font-size: 0.6rem;">{{ $request->staff->staff_id }}</div>
                        </td>
                        <td class="py-1">
                            <span class="badge bg-info-subtle text-info rounded-sm" style="font-size: 0.6rem;">
                                {{ str_replace('_', ' ', ucfirst($request->permission_type)) }}
                            </span>
                        </td>
                        <td class="py-1">
                            {{ \Carbon\Carbon::parse($request->start_datetime)->format('d M Y H:i') }}<br>
                            <small class="text-muted">to {{ \Carbon\Carbon::parse($request->end_datetime)->format('d M Y H:i') }}</small>
                        </td>
                        <td class="py-1">{{ $request->hours }} h</td>
                        <td class="py-1">{{ Str::limit($request->reason, 50) }}</td>
                        <td class="pe-2 py-1">
                            <button class="btn btn-success btn-sm rounded-sm" data-bs-toggle="modal" data-bs-target="#approvePermModal{{ $request->id }}" style="padding: 0.2rem 0.4rem; font-size: 0.6rem;">
                                <i class="ti ti-check"></i> Approve
                            </button>
                            <button class="btn btn-danger btn-sm rounded-sm" data-bs-toggle="modal" data-bs-target="#rejectPermModal{{ $request->id }}" style="padding: 0.2rem 0.4rem; font-size: 0.6rem;">
                                <i class="ti ti-x"></i> Reject
                            </button>
                        </td>
                    </tr>

                    <!-- Approve Modal -->
                    <div class="modal fade" id="approvePermModal{{ $request->id }}" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content rounded-sm">
                                <form action="{{ $approveRoute }}" method="POST">
                                    @csrf
                                    <div class="modal-header py-1 px-2">
                                        <h5 class="modal-title" style="font-size: 0.8rem;">Approve Permission Request</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body py-2 px-2">
                                        <p style="font-size: 0.75rem;">Approve permission request from <strong>{{ $request->staff->first_name }} {{ $request->staff->last_name }}</strong>?</p>
                                        <p class="text-muted" style="font-size: 0.65rem;">
                                            Type: {{ str_replace('_', ' ', ucfirst($request->permission_type)) }}<br>
                                            Duration: {{ $request->hours }} hours
                                        </p>
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
                    <div class="modal fade" id="rejectPermModal{{ $request->id }}" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content rounded-sm">
                                <form action="{{ $rejectRoute }}" method="POST">
                                    @csrf
                                    <div class="modal-header py-1 px-2">
                                        <h5 class="modal-title" style="font-size: 0.8rem;">Reject Permission Request</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body py-2 px-2">
                                        <p style="font-size: 0.75rem;">Reject permission request from <strong>{{ $request->staff->first_name }} {{ $request->staff->last_name }}</strong>?</p>
                                        <div class="mb-2">
                                            <label class="form-label" style="font-size: 0.65rem;">Reason <span class="text-danger">*</span></label>
                                            <textarea name="{{ $commentField }}" class="form-control form-control-sm rounded-sm" rows="2" style="font-size: 0.7rem;" required></textarea>
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
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-3">
            <i class="ti ti-clock" style="font-size: 1.5rem; color: #CBD5E1;"></i>
            <p class="text-muted mt-1" style="font-size: 0.7rem;">No pending short permission requests</p>
        </div>
        @endif
    </div>
    <div class="card-footer py-1 px-2">
        <x-pagination :items="$pendingRequests" :perPageOptions="[10, 25, 50, 100]" :ajax="true" />
    </div>
