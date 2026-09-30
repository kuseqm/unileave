    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm mb-0" style="font-size: 0.75rem;">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-2 py-1">#</th>
                        <th class="py-1">Staff</th>
                        <th class="py-1">Leave Type</th>
                        <th class="py-1">Dates</th>
                        <th class="py-1">Days</th>
                        <th class="py-1">Reason</th>
                        <th class="py-1">Current Level</th>
                        <th class="pe-2 py-1">Actions</th>
                    <tr>
                </thead>
                <tbody>
                    @forelse($pendingRequests as $index => $request)
                    @php
                        $role = auth()->user()->getRoleNames()->first();
                        $currentLevel = $request->current_approval_level;
                    @endphp
                    <tr>
                        <td class="ps-2 py-1">{{ $pendingRequests->firstItem() + $index }}</td>
                        <td class="py-1">
                            <div class="d-flex align-items-center gap-1">
                                <div class="rounded-circle bg-light d-flex align-items-center justify-content-center" style="width: 24px; height: 24px;">
                                    <span class="fw-semibold" style="font-size: 0.6rem;">{{ strtoupper(substr($request->staff->first_name, 0, 1)) }}{{ strtoupper(substr($request->staff->last_name, 0, 1)) }}</span>
                                </div>
                                <div>
                                    <span class="fw-semibold">{{ $request->staff->first_name }} {{ $request->staff->last_name }}</span>
                                    <div class="text-muted" style="font-size: 0.6rem;">{{ $request->staff->staff_id }}</div>
                                </div>
                                <!-- Staff Dossier Button -->
                                <button type="button" class="btn btn-sm btn-outline-secondary rounded-sm"
                                        data-bs-toggle="modal"
                                        data-bs-target="#staffDossierModal{{ $request->staff->id }}"
                                        title="View Staff Dossier" style="padding: 0.1rem 0.3rem;">
                                    <i class="ti ti-file-info" style="font-size: 0.65rem;"></i>
                                </button>
                            </div>
                        </td>
                        <td class="py-1">{{ $request->leaveType->name ?? 'N/A' }}<br><small class="text-muted">{{ $request->leaveType->code ?? '' }}</small></td>
                        <td class="py-1">{{ \Carbon\Carbon::parse($request->start_date)->format('d M Y') }}<br><small>to</small><br>{{ \Carbon\Carbon::parse($request->end_date)->format('d M Y') }}</td>
                        <td class="py-1">{{ $request->days }} day(s)</td>
                        <td class="py-1">{{ \Str::limit($request->reason, 50) }}</td>
                        <td class="py-1">
                            @php
                                $levelNames = [
                                    1 => 'HOD',
                                    2 => 'Dean',
                                    3 => 'HR',
                                    4 => 'DVC',
                                    5 => 'HR Logistics'
                                ];
                                $level = $request->current_approval_level;
                            @endphp
                            @if($level == 1)
                                <span class="badge bg-info-subtle text-info rounded-sm" style="font-size: 0.6rem;">HOD</span>
                            @elseif($level == 2)
                                <span class="badge bg-primary-subtle text-primary rounded-sm" style="font-size: 0.6rem;">Dean</span>
                            @elseif($level == 3)
                                <span class="badge bg-secondary-subtle text-secondary rounded-sm" style="font-size: 0.6rem;">HR</span>
                            @elseif($level == 4)
                                <span class="badge bg-success-subtle text-success rounded-sm" style="font-size: 0.6rem;">DVC</span>
                            @elseif($level == 5)
                                <span class="badge bg-dark-subtle text-dark rounded-sm" style="font-size: 0.6rem;">HR Logistics</span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary rounded-sm" style="font-size: 0.6rem;">{{ $levelNames[$level] ?? 'Unknown' }}</span>
                            @endif
                        </td>
                        <td class="pe-2 py-1">
                            <div class="d-flex gap-1">
                                <!-- For HR Level 3 - Show HR Review Button -->
                                @if($currentLevel == 3 && auth()->user()->hasAnyRole(['hr-officer', 'hr-manager', 'admin']))
                                    <a href="{{ route('leave-requests.hr-review', $request->id) }}" class="btn btn-sm btn-primary rounded-sm" title="HR Review" style="padding: 0.15rem 0.3rem;">
                                        <i class="ti ti-clipboard-list" style="font-size: 0.7rem;"></i>
                                    </a>
                                <!-- For HR Logistics Level 5 - Show Logistics Button.
                                     This level has no "next level" for the generic
                                     Approve button to advance to, so anyone who hits
                                     it (including admin) must go through this
                                     dedicated page - not the generic button below. -->
                                @elseif($currentLevel == 5 && auth()->user()->hasAnyRole(['hr-officer', 'hr-manager', 'admin']))
                                    <a href="{{ route('leave-requests.hr-logistics', $request->id) }}" class="btn btn-sm btn-info rounded-sm" title="HR Logistics" style="padding: 0.15rem 0.3rem;">
                                        <i class="ti ti-package" style="font-size: 0.7rem;"></i>
                                    </a>
                                <!-- For DVC Level 4 - Show DVC Review Button -->
                                @elseif($currentLevel == 4 && auth()->user()->hasAnyRole(['dvca', 'dvc-pfa', 'admin']))
                                    <a href="{{ route('leave-requests.dvc-review', $request->id) }}" class="btn btn-sm btn-primary rounded-sm" title="DVC Review" style="padding: 0.15rem 0.3rem;">
                                        <i class="ti ti-crown" style="font-size: 0.7rem;"></i>
                                    </a>
                                @else
                                    <!-- Regular Approve Button -->
                                    <button type="button" class="btn btn-sm btn-success rounded-sm" onclick="openApproveModal({{ $request->id }})" title="Approve" style="padding: 0.15rem 0.3rem;">
                                        <i class="ti ti-check" style="font-size: 0.7rem;"></i>
                                    </button>
                                    <!-- Regular Reject Button -->
                                    <button type="button" class="btn btn-sm btn-danger rounded-sm" onclick="openRejectModal({{ $request->id }})" title="Reject" style="padding: 0.15rem 0.3rem;">
                                        <i class="ti ti-x" style="font-size: 0.7rem;"></i>
                                    </button>
                                @endif

                                <!-- View Details Button (always visible) -->
                                <a href="{{ route('leave-requests.show', $request->id) }}" class="btn btn-sm btn-outline-info rounded-sm" title="View Details" style="padding: 0.15rem 0.3rem;">
                                    <i class="ti ti-eye" style="font-size: 0.7rem;"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-3 text-muted" style="font-size: 0.7rem;">
                            No pending approvals
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer py-1 px-2">
        <x-pagination :items="$pendingRequests" :perPageOptions="[10, 25, 50, 100]" :ajax="true" />
    </div>

    <!-- Staff Dossier Modals -->
    @foreach($pendingRequests as $request)
    <div class="modal fade" id="staffDossierModal{{ $request->staff->id }}" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content rounded-sm">
                <div class="modal-header py-1 px-2 bg-light">
                    <h5 class="modal-title" style="font-size: 0.8rem;">Staff Dossier - {{ $request->staff->full_name }}</h5>
                    <button type="button" class="btn-close py-0" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-2">
                    <x-staff-dossier :staff="$request->staff" />
                </div>
                <div class="modal-footer py-1 px-2">
                    <button type="button" class="btn btn-secondary btn-sm rounded-sm" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    @endforeach
