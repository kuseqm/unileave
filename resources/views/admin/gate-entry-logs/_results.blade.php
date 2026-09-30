<div class="table-responsive">
    <table class="table table-sm table-hover mb-0" style="font-size: 0.75rem;">
        <thead class="table-light">
            <tr>
                <th>#</th>
                <th>Staff</th>
                <th>Action</th>
                <th>Gate</th>
                <th>Method</th>
                <th>Reference</th>
                <th>Recorded At</th>
                <th>Recorded By</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($logs as $log)
            <tr>
                <td>{{ $log->id }}</td>
                <td>{{ optional($log->staff)->first_name }} {{ optional($log->staff)->last_name }}</td>
                <td><span class="badge bg-{{ $log->action == 'entry' ? 'success' : 'warning' }}">{{ ucfirst($log->action) }}</span></td>
                <td>{{ $log->gate_name }}</td>
                <td>{{ ucfirst(str_replace('_', ' ', $log->verification_method)) }}</td>
                <td>{{ $log->verification_reference }}</td>
                <td>{{ optional($log->recorded_at)->format('d M Y H:i') }}</td>
                <td>{{ optional($log->recorder)->first_name }} {{ optional($log->recorder)->last_name }}</td>
                <td>
                    <a href="{{ route('admin.gate-entry-logs.show', $log) }}" class="btn btn-link btn-sm p-0">View</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="text-center text-muted py-3">No gate logs found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="d-flex justify-content-center mt-2 mb-2">
    <x-pagination :items="$logs" :ajax="true" />
</div>
