    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm mb-0" style="font-size: 0.75rem;">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-2 py-1" width="50">#</th>
                        <th class="py-1">File Number</th>
                        <th class="py-1">Name</th>
                        <th class="py-1">Format</th>
                        <th class="py-1">Serial Range</th>
                        <th class="py-1" width="100">Usage</th>
                        <th class="py-1" width="80">Status</th>
                        <th class="pe-2 py-1 text-center" width="120">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($files as $index => $file)
                    <tr class="clickable-row" style="cursor: pointer;" onclick="window.location='{{ route('registry.files.show', $file->id) }}'">
                        <td class="ps-2 py-1">{{ $files->firstItem() + $index }}</td>
                        <td class="py-1">
                            <span class="fw-semibold font-monospace hover-link">{{ $file->file_number }}</span>
                        </td>
                        <td class="py-1">{{ Str::limit($file->file_name, 30) }}</td>
                        <td class="py-1">{{ $file->referenceFormat->name ?? 'N/A' }}</td>
                        <td class="py-1 font-monospace">{{ $file->start_serial_formatted }} - {{ $file->end_serial_formatted }}</td>
                        <td class="py-1">
                            <div class="d-flex align-items-center gap-1">
                                <div class="progress flex-grow-1" style="height: 4px;">
                                    <div class="progress-bar {{ $file->usage_percentage >= 90 ? 'bg-danger' : ($file->usage_percentage >= 70 ? 'bg-warning' : 'bg-success') }}"
                                         style="width: {{ $file->usage_percentage }}%"></div>
                                </div>
                                <span class="text-muted" style="font-size: 0.55rem;">{{ $file->usage_percentage }}%</span>
                            </div>
                        </td>
                        <td class="py-1">
                            @if($file->is_active && !$file->closed_at)
                                <span class="badge bg-success-subtle text-success rounded-pill">Active</span>
                            @elseif($file->isFull())
                                <span class="badge bg-warning-subtle text-warning rounded-pill">Full</span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary rounded-pill">Closed</span>
                            @endif
                        </td>
                        <td class="pe-2 py-1 text-center">
                            <div class="d-flex gap-1 justify-content-center">
                                <!-- View Button -->
                                <a href="{{ route('registry.files.show', $file->id) }}"
                                   class="btn btn-sm btn-outline-info rounded-sm"
                                   onclick="event.stopPropagation();"
                                   style="padding: 0.15rem 0.4rem; font-size: 0.6rem;"
                                   title="View File">
                                    <i class="ti ti-eye"></i>
                                </a>

                                <!-- Edit Button -->
                                <a href="{{ route('registry.files.edit', $file->id) }}"
                                   class="btn btn-sm btn-outline-primary rounded-sm"
                                   onclick="event.stopPropagation();"
                                   style="padding: 0.15rem 0.4rem; font-size: 0.6rem;"
                                   title="Edit File">
                                    <i class="ti ti-edit"></i>
                                </a>

                                <!-- Close Button (only for active non-full files) -->
                                @if($file->is_active && !$file->closed_at && !$file->isFull())
                                <button type="button"
                                        onclick="event.stopPropagation(); showCloseFileModal('{{ $file->id }}', '{{ addslashes($file->file_number) }}', '{{ route('registry.files.close', $file->id) }}')"
                                        class="btn btn-sm btn-outline-warning rounded-sm"
                                        style="padding: 0.15rem 0.4rem; font-size: 0.6rem;"
                                        title="Close File">
                                    <i class="ti ti-lock"></i>
                                </button>
                                @endif

                                <!-- Reopen Button (only for closed files that aren't full) -->
                                @if($file->closed_at && !$file->isFull())
                                <button type="button"
                                        onclick="event.stopPropagation(); showReopenFileModal('{{ $file->id }}', '{{ addslashes($file->file_number) }}', '{{ route('registry.files.reopen', $file->id) }}')"
                                        class="btn btn-sm btn-outline-success rounded-sm"
                                        style="padding: 0.15rem 0.4rem; font-size: 0.6rem;"
                                        title="Reopen File">
                                    <i class="ti ti-lock-open"></i>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">
                            <i class="ti ti-folder mb-2 d-block" style="font-size: 1.5rem;"></i>
                            <p>No letter files found</p>
                            <a href="{{ route('registry.files.create') }}" class="btn btn-sm btn-primary rounded-sm">Create First File</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer py-1 px-2">
        <x-pagination :items="$files" :perPageOptions="[15, 25, 50, 100]" :ajax="true" />
    </div>
