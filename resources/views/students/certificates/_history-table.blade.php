{{-- Vars: $batches, $emptyText --}}

<div class="table-responsive">
    <table class="table table-sm mb-0" style="font-size: 0.7rem;">
        <thead class="table-light">
            <tr>
                <th>Reference</th>
                <th>Type</th>
                <th>Programme</th>
                <th>When</th>
                <th class="text-center">Updated</th>
                <th class="text-center">Flagged</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($batches as $batch)
            @php
                $isDownload = $batch->downloaded_at && ! $batch->uploaded_at;
                $programme = $batch->program?->degree_abbreviation ?? $batch->program?->code ?? $batch->program?->name ?? '—';
                $campusCode = $batch->campus?->code ?? null;
                $programmeDisplay = $campusCode ? "{$programme} ({$campusCode})" : $programme;
            @endphp
            <tr>
                <td><code>{{ $batch->reference_number ?? '—' }}</code></td>
                <td>
                    @if($isDownload)
                        <span class="badge bg-secondary-subtle text-secondary rounded-sm"><i class="ti ti-download"></i> Download</span>
                    @else
                        <span class="badge bg-primary-subtle text-primary rounded-sm"><i class="ti ti-upload"></i> Upload</span>
                    @endif
                </td>
                <td>{{ $programmeDisplay }} @if($batch->graduation_year)<span class="text-muted">· {{ $batch->graduation_year }}</span>@endif</td>
                <td>{{ ($batch->uploaded_at ?? $batch->downloaded_at ?? $batch->created_at)->format('d M Y H:i') }}</td>
                <td class="text-center"><span class="badge bg-success rounded-sm">{{ $batch->updated_count }}</span></td>
                <td class="text-center">
                    @if($batch->flagged_count > 0)
                        <span class="badge bg-warning text-dark rounded-sm">{{ $batch->flagged_count }}</span>
                    @else
                        <span class="text-muted">—</span>
                    @endif
                </td>
                <td class="text-nowrap">
                    <a href="{{ route('students.certificates.show', $batch->id) }}" class="btn btn-outline-primary btn-sm rounded-sm" style="font-size: 0.6rem; padding: 0.1rem 0.4rem;">
                        <i class="ti ti-eye me-1"></i> View
                    </a>
                    @if($batch->status !== 'closed')
                        <form action="{{ route('students.certificates.close', $batch->id) }}" method="POST" class="d-inline ms-1">
                            @csrf
                            <button type="button" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.6rem; padding: 0.1rem 0.4rem;" onclick="confirmSubmit(this, 'Close reference {{ $batch->reference_number }}? It will move to the Closed tab.', {danger: false, title: 'Close Reference', confirmText: 'Close'})">
                                <i class="ti ti-check me-1"></i> Close
                            </button>
                        </form>
                    @else
                        <form action="{{ route('students.certificates.reopen', $batch->id) }}" method="POST" class="d-inline ms-1">
                            @csrf
                            <button type="button" class="btn btn-outline-warning btn-sm rounded-sm" style="font-size: 0.6rem; padding: 0.1rem 0.4rem;" onclick="confirmSubmit(this, 'Re-open reference {{ $batch->reference_number }}? It will move back to the Active tab.', {danger: false, title: 'Re-open Reference', confirmText: 'Re-open'})">
                                <i class="ti ti-arrow-back-up me-1"></i> Re-open
                            </button>
                        </form>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center text-muted py-3" style="font-size: 0.7rem;">{{ $emptyText }}</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
