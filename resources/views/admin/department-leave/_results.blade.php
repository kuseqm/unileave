<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table table-sm table-hover mb-0" style="font-size: 0.75rem;">
            <thead class="table-light">
                <tr>
                    <th class="ps-2 py-1">Staff</th>
                    <th class="py-1">Type</th>
                    <th class="py-1">Dates</th>
                    <th class="py-1">Days</th>
                    <th class="py-1">Status</th>
                    <th class="pe-2 py-1"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($requests as $leave)
                <tr>
                    <td class="ps-2 py-1">{{ optional($leave->staff)->full_name }}</td>
                    <td class="py-1">{{ optional($leave->leaveType)->name }}</td>
                    <td class="py-1">{{ $leave->start_date?->format('d M Y') }} - {{ $leave->end_date?->format('d M Y') }}</td>
                    <td class="py-1">{{ $leave->days }}</td>
                    <td class="py-1"><span class="badge bg-{{ $leave->status == 'approved' ? 'success' : ($leave->status == 'rejected' ? 'danger' : ($leave->status == 'cancelled' ? 'secondary' : 'warning')) }}">{{ ucfirst($leave->status) }}</span></td>
                    <td class="pe-2 py-1">
                        <a href="{{ route('leave-requests.show', $leave->id) }}" class="btn btn-info btn-sm rounded-sm" style="font-size: 0.65rem; padding: 0.15rem 0.4rem;">View</a>
                        @if($leave->status == 'pending' && auth()->user()->can('approve_department_leave') && $leave->current_approval_level == \App\Models\LeaveRequest::LEVEL_HOD)
                        <form method="POST" action="{{ route('admin.department-leave.approve', $leave->id) }}" class="d-inline" onsubmit="return confirm('Approve this request?')">
                            @csrf
                            @method('POST')
                            <button type="submit" class="btn btn-success btn-sm rounded-sm" style="font-size: 0.65rem; padding: 0.15rem 0.4rem;">Approve</button>
                        </form>
                        <form method="POST" action="{{ route('admin.department-leave.reject', $leave->id) }}" class="d-inline" onsubmit="return confirm('Reject this request?')">
                            @csrf
                            @method('POST')
                            <button type="submit" class="btn btn-danger btn-sm rounded-sm" style="font-size: 0.65rem; padding: 0.15rem 0.4rem;">Reject</button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-3">No leave requests found in your department.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="card-footer py-1 px-2">
    <x-pagination :items="$requests" :ajax="true" />
</div>
