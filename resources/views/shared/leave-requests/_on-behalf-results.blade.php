<div class="table-responsive">
    <table class="table table-sm table-hover mb-0" style="font-size: 0.75rem;">
        <thead class="table-light">
            <tr>
                <th>Staff</th>
                <th>Staff ID</th>
                <th>Department</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($staffMembers as $staff)
            <tr>
                <td>{{ $staff->first_name }} {{ $staff->middle_name }} {{ $staff->last_name }}</td>
                <td>{{ $staff->staff_id }}</td>
                <td>{{ optional($staff->department)->name }}</td>
                <td>
                    <a href="{{ route('leave-requests.create', ['behalf_staff_id' => $staff->id]) }}" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.65rem; padding: 0.15rem 0.4rem;">Apply</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="text-center text-muted py-3">No staff found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-2">
    <x-pagination :items="$staffMembers" :ajax="true" />
</div>
