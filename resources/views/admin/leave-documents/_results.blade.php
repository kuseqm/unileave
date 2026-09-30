<div class="table-responsive">
    <table class="table table-sm table-hover mb-0" style="font-size: 0.75rem;">
        <thead class="table-light">
            <tr>
                <th>Staff</th>
                <th>Leave Type</th>
                <th>Dates</th>
                <th>Days</th>
                <th>Status</th>
                <th>Letter</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($leaveRequests as $leave)
            <tr>
                <td>{{ optional($leave->staff)->full_name }}</td>
                <td>{{ optional($leave->leaveType)->name }}</td>
                <td>{{ $leave->start_date?->format('d M Y') }} - {{ $leave->end_date?->format('d M Y') }}</td>
                <td>{{ $leave->days }}</td>
                <td><span class="badge bg-{{ $leave->status == 'approved' ? 'success' : 'warning' }}">{{ ucfirst($leave->status) }}</span></td>
                <td>
                    @if($leave->latestLetter)
                        <span class="badge bg-{{ $leave->latestLetter->stage == 'generated' ? 'success' : 'info' }}">{{ ucfirst($leave->latestLetter->stage) }}</span>
                    @else
                        <span class="text-muted">Not prepared</span>
                    @endif
                </td>
                <td>
                    <a href="{{ route('admin.leave-documents.prepare', $leave->id) }}" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.65rem; padding: 0.15rem 0.4rem;">
                        {{ $leave->latestLetter ? 'Edit' : 'Prepare' }}
                    </a>
                    @if($leave->latestLetter)
                    <a href="{{ route('admin.leave-documents.show', $leave->latestLetter->id) }}" class="btn btn-info btn-sm rounded-sm" style="font-size: 0.65rem; padding: 0.15rem 0.4rem;">View</a>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center text-muted py-3">No leave requests require document preparation.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="d-flex justify-content-center mt-2 mb-2">
    <x-pagination :items="$leaveRequests" :ajax="true" />
</div>
