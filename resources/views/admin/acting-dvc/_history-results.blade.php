    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm mb-0" style="font-size: 0.75rem;">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-2 py-1">Staff</th>
                        <th class="py-1">Role</th>
                        <th class="py-1">Period</th>
                        <th class="py-1">Status</th>
                        <th class="pe-2 py-1">Approved By</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($history as $assignment)
                    <tr>
                        <td class="ps-2 py-1">{{ $assignment->staff->first_name }} {{ $assignment->staff->last_name }}</td>
                        <td class="py-1">{{ strtoupper($assignment->role) }}</td>
                        <td class="py-1">{{ $assignment->start_date->format('d M Y') }} - {{ $assignment->end_date->format('d M Y') }}</td>
                        <td class="py-1">
                            @if($assignment->status == 'expired')
                                <span class="badge bg-secondary-subtle text-secondary rounded-sm">Expired</span>
                            @elseif($assignment->status == 'rejected')
                                <span class="badge bg-danger-subtle text-danger rounded-sm">Rejected</span>
                            @endif
                        </td>
                        <td class="pe-2 py-1">{{ $assignment->approvedBy->first_name ?? 'N/A' }} {{ $assignment->approvedBy->last_name ?? '' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-2 text-muted">No history</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer py-1 px-2">
        <x-pagination :items="$history" :perPageOptions="[20, 50, 100]" :ajax="true" />
    </div>
