@extends('layouts.master')

@section('title', 'Import Reference ' . ($batch->reference_number ?? ''))

@section('content')
<div class="container-fluid px-2">
    <x-page-header title="IMPORT REFERENCE" description="Details for {{ $batch->reference_number ?? '—' }}">
        <a href="{{ route('students.import.index') }}" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
            <i class="ti ti-arrow-left me-1"></i> Back to History
        </a>
    </x-page-header>

    <div class="row g-2 mb-3">
        <div class="col-lg-8">
            <div class="card rounded-sm mb-2">
                <div class="card-header py-1 px-2 bg-light">
                    <span style="font-size: 0.7rem; font-weight: 600;">Batch Details</span>
                </div>
                <div class="card-body py-2 px-2" style="font-size: 0.7rem;">
                    <div class="row g-2">
                        <div class="col-md-4"><span class="text-muted">Reference:</span> <code>{{ $batch->reference_number ?? '—' }}</code></div>
                        <div class="col-md-4"><span class="text-muted">Type:</span> {!! $isDownload ? '<span class="badge bg-secondary-subtle text-secondary rounded-sm">Download</span>' : '<span class="badge bg-primary-subtle text-primary rounded-sm">Upload</span>' !!}</div>
                        <div class="col-md-4"><span class="text-muted">When:</span> {{ ($batch->uploaded_at ?? $batch->downloaded_at ?? $batch->created_at)->format('d M Y H:i') }}</div>
                        <div class="col-md-4"><span class="text-muted">By:</span> {{ trim(($batch->importedBy?->first_name ?? '').' '.($batch->importedBy?->last_name ?? '')) }}</div>
                        <div class="col-md-4"><span class="text-muted">Campus:</span> {{ $batch->campus?->name ?? '—' }} @if($batch->campus?->code)<code class="text-muted">({{ $batch->campus->code }})</code>@endif</div>
                        <div class="col-md-4"><span class="text-muted">Programme:</span> {{ $batch->program?->degree_abbreviation ?? $batch->program?->code ?? $batch->program?->name ?? '—' }} @if($batch->campus?->code)<span class="text-muted">({{ $batch->campus->code }})</span>@endif</div>
                        <div class="col-md-4"><span class="text-muted">Year:</span> {{ $batch->graduation_year ?? '—' }}</div>
                        <div class="col-md-4"><span class="text-muted">Status:</span> {{ $batch->status === 'closed' ? 'Closed' : 'Open' }}</div>
                        <div class="col-md-4"><span class="text-muted">File:</span> {{ $batch->original_filename ?? '—' }}</div>
                        @if($batch->originalProgram)
                        <div class="col-md-6"><span class="text-muted">Corrected from:</span> {{ $batch->originalProgram->name }}</div>
                        @endif
                    </div>

                    @if($batch->is_incomplete)
                    <div class="alert alert-danger py-1 px-2 mt-2 mb-0" style="font-size: 0.65rem;">
                        <i class="ti ti-alert-circle me-1"></i> Incomplete - {{ $batch->processed_rows }} of {{ $batch->total_rows }} rows imported
                    </div>
                    @endif

                    @if($batch->status === 'open' && ! $isDownload)
                    <hr class="my-2" style="border-color: #e9ecef;">
                    <div class="row g-2" style="font-size: 0.7rem;">
                        <div class="col-md-5">
                            <form action="{{ route('students.import.reassign-year', $batch->id) }}" method="POST" class="d-flex gap-1 align-items-end">
                                @csrf
                                <div class="flex-grow-1">
                                    <x-scrollable-dropdown
                                        name="graduation_year"
                                        id="reassign_graduation_year"
                                        label="Change Graduation Year"
                                        icon="ti-calendar-event"
                                        :options="$graduationYears"
                                        :required="true"
                                        value="{{ $batch->graduation_year }}"
                                        placeholder="Select year" />
                                </div>
                                <div>
                                    <button type="submit" class="btn btn-outline-warning btn-sm rounded-sm" style="font-size: 0.65rem; padding: 0.2rem 0.4rem;">Change Year</button>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-7">
                            <form action="{{ route('students.import.reassign-programme', $batch->id) }}" method="POST" class="d-flex gap-1 align-items-end">
                                @csrf
                                <div class="flex-grow-1">
                                    <x-scrollable-dropdown
                                        name="program_id"
                                        id="reassign_program_id"
                                        label="Change Programme"
                                        icon="ti-book"
                                        :options="$programmeOptions"
                                        :required="true"
                                        value="{{ $batch->program_id }}"
                                        placeholder="Select programme" />
                                </div>
                                <div>
                                    <button type="submit" class="btn btn-outline-primary btn-sm rounded-sm" style="font-size: 0.65rem; padding: 0.2rem 0.4rem;">Change Programme</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            @if(! empty($reviewItems))
            <div class="card rounded-sm" id="review">
                <div class="card-header py-1 px-2 bg-light d-flex align-items-center justify-content-between">
                    <span style="font-size: 0.7rem; font-weight: 600;">Rows to Review ({{ count($reviewItems) }})</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm mb-0" style="font-size: 0.7rem;">
                        <thead class="table-light">
                            <tr>
                                <th>Row</th>
                                <th>Registration Number</th>
                                <th>Reason</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reviewItems as $item)
                            <tr>
                                <td>{{ $item['row'] ?? '—' }}</td>
                                <td><code>{{ $item['registration_number'] ?? '—' }}</code></td>
                                <td>{{ $item['reason'] ?? '—' }}</td>
                                <td>
                                    @if($item['edit_url'])
                                    <a href="{{ $item['edit_url'] }}" class="btn btn-outline-primary btn-sm rounded-sm" style="font-size: 0.6rem; padding: 0.05rem 0.4rem;">
                                        <i class="ti ti-pencil me-1"></i> Edit
                                    </a>
                                    @else
                                    <span class="text-muted" style="font-size: 0.6rem;">—</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>

        <div class="col-lg-4">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <span style="font-size: 0.7rem; font-weight: 600;">Quick Summary</span>
                </div>
                <div class="card-body p-2" style="font-size: 0.7rem;">
                    <div class="row g-2 mb-2">
                        <div class="col-6"><span class="text-muted">Created:</span> <span class="badge bg-success rounded-sm">{{ $batch->created_count }}</span></div>
                        <div class="col-6"><span class="text-muted">Updated:</span> <span class="badge bg-info rounded-sm">{{ $batch->updated_count }}</span></div>
                        <div class="col-6"><span class="text-muted">Review:</span> <span class="badge bg-warning text-dark rounded-sm">{{ count($reviewItems) }}</span></div>
                        <div class="col-6"><span class="text-muted">Updates:</span> <a href="{{ route('students.import.updates') }}" class="text-decoration-none"><span class="badge bg-secondary rounded-sm">{{ number_format($updateCount) }}</span></a></div>
                        @if($batch->total_rows)
                        <div class="col-6"><span class="text-muted">Rows:</span> {{ $batch->total_rows }}</div>
                        <div class="col-6"><span class="text-muted">Processed:</span> {{ $batch->processed_rows }}</div>
                        @endif
                    </div>
                    <div class="d-grid gap-1">
                        <a href="{{ route('students.import.batch-students', $batch->id) }}" class="btn btn-outline-primary btn-sm rounded-sm" style="font-size: 0.65rem;">
                            <i class="ti ti-external-link me-1"></i> Open Graduates
                        </a>
                        @if($batch->status === 'open' && ! $isDownload)
                        <form action="{{ route('students.import.close', $batch->id) }}" method="POST">
                            @csrf
                            <button type="button" class="btn btn-outline-secondary btn-sm w-100 rounded-sm" style="font-size: 0.65rem;" onclick="confirmSubmit(this, 'Close reference {{ $batch->reference_number }}? This marks the batch as finished.', {danger: false, title: 'Close Reference', confirmText: 'Close Reference'})">
                                <i class="ti ti-check me-1"></i> Close
                            </button>
                        </form>
                        @endif
                        @if($batch->status === 'closed' && ! $isDownload)
                        <form action="{{ route('students.import.reopen', $batch->id) }}" method="POST">
                            @csrf
                            <button type="button" class="btn btn-outline-success btn-sm w-100 rounded-sm" style="font-size: 0.65rem;" onclick="confirmSubmit(this, 'Re-open reference {{ $batch->reference_number }}? It will move back to the Active tab.', {danger: false, title: 'Re-open Reference', confirmText: 'Re-open Reference'})">
                                <i class="ti ti-rotate-clockwise me-1"></i> Re-open
                            </button>
                        </form>
                        @endif
                        @if(! $isDownload && $batch->status === 'open')
                        <form action="{{ route('students.import.reset', $batch->id) }}" method="POST">
                            @csrf
                            <button type="button" class="btn btn-outline-danger btn-sm w-100 rounded-sm" style="font-size: 0.65rem;" onclick="confirmSubmit(this, 'RESET reference {{ $batch->reference_number }}? This permanently deletes the {{ $batch->created_count + $batch->updated_count }} graduates and cannot be undone.{{ $lettersAffected > 0 ? ' It will also delete '.$lettersAffected.' alumni letter(s) already issued to them.' : '' }}', {danger: true, title: 'Reset Reference', confirmText: 'Reset Everything'})">
                                <i class="ti ti-trash me-1"></i> Reset
                            </button>
                        </form>
                        @elseif(! $isDownload)
                        <div class="text-muted text-center" style="font-size: 0.62rem;">Re-open this reference to reset it.</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
