    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm mb-0" style="font-size: 0.75rem;">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-2 py-1">#</th>
                        <th class="py-1">Leave Type</th>
                        <th class="py-1">Dates</th>
                        <th class="py-1">Days</th>
                        <th class="py-1">Acting Staff</th>
                        <th class="py-1">Status</th>
                        <th class="py-1">Approval Level</th>
                        <th class="pe-2 py-1">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($leaveRequests as $index => $request)
                    <tr>
                        <td class="ps-2 py-1">{{ $leaveRequests->firstItem() + $index }}</td>
                        <td class="py-1">{{ $request->leaveType->name ?? 'N/A' }} ({{ $request->leaveType->code ?? '' }})</td>
                        <td class="py-1">{{ \Carbon\Carbon::parse($request->start_date)->format('d M Y') }} - {{ \Carbon\Carbon::parse($request->end_date)->format('d M Y') }}</td>
                        <td class="py-1">{{ $request->days }} day(s)</td>
                        <td class="py-1">
                            @if($request->actingStaff)
                                {{ $request->actingStaff->first_name }} @if($request->actingStaff->middle_name){{ $request->actingStaff->middle_name }} @endif{{ $request->actingStaff->last_name }}
                                <br>
                                @if($request->acting_status == 'pending')
                                    <span class="badge bg-warning-subtle text-warning rounded-sm" style="font-size: 0.55rem;">Awaiting Response</span>
                                @elseif($request->acting_status == 'accepted')
                                    <span class="badge bg-success-subtle text-success rounded-sm" style="font-size: 0.55rem;">Accepted</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger rounded-sm" style="font-size: 0.55rem;">Declined</span>
                                @endif
                            @else
                                N/A
                            @endif
                        </td>
                        <td class="py-1">
                            @if($request->status == 'pending')
                                <span class="badge bg-warning-subtle text-warning rounded-sm" style="font-size: 0.6rem;">Pending</span>
                            @elseif($request->status == 'approved')
                                <span class="badge bg-success-subtle text-success rounded-sm" style="font-size: 0.6rem;">Approved</span>
                            @elseif($request->status == 'rejected')
                                <span class="badge bg-danger-subtle text-danger rounded-sm" style="font-size: 0.6rem;">Rejected</span>
                            @elseif($request->status == 'cancelled')
                                <span class="badge bg-secondary-subtle text-secondary rounded-sm" style="font-size: 0.6rem;">Cancelled</span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary rounded-sm" style="font-size: 0.6rem;">{{ ucfirst($request->status) }}</span>
                            @endif
                        </td>
                        <td class="py-1">{{ $request->approval_level_name }}</td>
                        <td class="pe-2 py-1">
                            <x-action-buttons
                                viewRoute="{{ route('leave-requests.show', $request->id) }}"
                                showCancel="{{ $request->status == 'pending' && $request->acting_status == 'pending' }}"
                                cancelRoute="{{ route('leave-requests.cancel', $request->id) }}"
                                cancelId="{{ $request->id }}"
                                cancelName="{{ $request->leaveType->name ?? 'Leave Request' }}"
                            />
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-3 text-muted" style="font-size: 0.7rem;">
                            No leave requests found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer py-1 px-2">
        <x-pagination :items="$leaveRequests" :perPageOptions="[10, 25, 50, 100]" :ajax="true" />
    </div>
