    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm mb-0" style="font-size: 0.75rem;">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-2 py-1">Name</th>
                        <th class="py-1">Short Name</th>
                        <th class="py-1">Department</th>
                        <th class="py-1">Receives Letters</th>
                        <th class="py-1">Status</th>
                        <th class="pe-2 py-1">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($offices as $office)
                    <tr>
                        <td class="ps-2 py-1"><strong>{{ $office->name }}</strong></td>
                        <td class="py-1">{{ $office->short_name }}</td>
                        <td class="py-1">{{ $office->department->name ?? 'Central' }}</td>
                        <td class="py-1">
                            @if($office->receives_letters)
                            <span class="badge bg-success-subtle text-success rounded-sm" style="font-size: 0.6rem;">Yes</span>
                            @else
                            <span class="badge bg-secondary-subtle text-secondary rounded-sm" style="font-size: 0.6rem;">No</span>
                            @endif
                        </td>
                        <td class="py-1">
                            @if($office->is_active)
                            <span class="badge bg-success-subtle text-success rounded-sm" style="font-size: 0.6rem;">Active</span>
                            @else
                            <span class="badge bg-danger-subtle text-danger rounded-sm" style="font-size: 0.6rem;">Inactive</span>
                            @endif
                        </td>
                        <td class="pe-2 py-1">
                            <a href="{{ route('registry.offices.edit', $office->id) }}" class="btn btn-sm btn-outline-primary rounded-sm" style="font-size: 0.6rem; padding: 0.15rem 0.4rem;">Edit</a>
                            <button type="button" class="btn btn-sm btn-outline-danger rounded-sm" style="font-size: 0.6rem; padding: 0.15rem 0.4rem;" onclick="deleteOffice({{ $office->id }})">Delete</button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-3 text-muted" style="font-size: 0.7rem;">No offices found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer py-1 px-2">
        <x-pagination :items="$offices" :perPageOptions="[20, 50, 100]" :ajax="true" />
    </div>
