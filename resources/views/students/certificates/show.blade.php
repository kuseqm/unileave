@extends('layouts.master')

@section('title', 'Certificate Reference ' . ($batch->reference_number ?? ''))

@section('content')
@php
    $isDownload = $batch->downloaded_at && ! $batch->uploaded_at;
@endphp
<div class="container-fluid px-2">
    <x-page-header title="CERTIFICATE UPLOAD REFERENCE" description="Details for {{ $batch->reference_number ?? '—' }}">
        <a href="{{ route('students.certificates.index') }}" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
            <i class="ti ti-arrow-left me-1"></i> Back to History
        </a>
    </x-page-header>

    @if (session('success'))
        <div class="alert alert-success py-2 px-2 rounded-sm mb-3" style="font-size: 0.75rem;">{{ session('success') }}</div>
    @endif

    <div class="row g-2 mb-3">
        <div class="col-lg-8">
            <div class="card rounded-sm mb-2">
                <div class="card-header py-1 px-2 bg-light">
                    <span style="font-size: 0.7rem; font-weight: 600;"><i class="ti ti-certificate text-primary me-1"></i>Batch Details</span>
                </div>
                <div class="card-body py-2 px-2" style="font-size: 0.7rem;">
                    <div class="row g-2">
                        <div class="col-md-4"><span class="text-muted">Reference:</span> <code>{{ $batch->reference_number ?? '—' }}</code></div>
                        <div class="col-md-4"><span class="text-muted">Type:</span> {!! $isDownload ? '<span class="badge bg-secondary-subtle text-secondary rounded-sm">Download</span>' : '<span class="badge bg-primary-subtle text-primary rounded-sm">Upload</span>' !!}</div>
                        <div class="col-md-4"><span class="text-muted">Status:</span> {{ $batch->status === 'closed' ? 'Closed' : 'Open' }}</div>
                        <div class="col-md-4"><span class="text-muted">Campus:</span> {{ $batch->campus?->name ?? 'All / auto-detect' }}</div>
                        <div class="col-md-4"><span class="text-muted">Programme:</span> {{ $batch->program?->degree_abbreviation ?? $batch->program?->code ?? $batch->program?->name ?? '—' }}</div>
                        <div class="col-md-4"><span class="text-muted">Graduation Year:</span> {{ $batch->graduation_year ?? '—' }}</div>
                        <div class="col-md-4"><span class="text-muted">Downloaded:</span> {{ $batch->downloaded_at?->format('d M Y H:i') ?? '—' }}</div>
                        <div class="col-md-4"><span class="text-muted">Uploaded:</span> {{ $batch->uploaded_at?->format('d M Y H:i') ?? '—' }}</div>
                        <div class="col-md-4"><span class="text-muted">By:</span> {{ $batch->importedBy?->name ?? '—' }}</div>
                        <div class="col-md-6"><span class="text-muted">File:</span> {{ $batch->original_filename ?? '—' }}</div>
                        @if($batch->status === 'closed')
                        <div class="col-md-6"><span class="text-muted">Closed:</span> {{ $batch->closed_at?->format('d M Y H:i') }} @if($batch->closedBy) by {{ $batch->closedBy->name }} @endif</div>
                        @endif
                    </div>

                    <hr class="my-2" style="border-color: #e9ecef;">

                    <div class="row g-2 text-center">
                        <div class="col-3">
                            <div class="fw-bold text-success" style="font-size: 1rem;">{{ number_format($batch->updated_count) }}</div>
                            <div class="text-muted" style="font-size: 0.62rem;">Updated</div>
                        </div>
                        <div class="col-3">
                            <div class="fw-bold text-warning" style="font-size: 1rem;">{{ number_format($batch->skipped_count) }}</div>
                            <div class="text-muted" style="font-size: 0.62rem;">Skipped</div>
                        </div>
                        <div class="col-3">
                            <div class="fw-bold text-danger" style="font-size: 1rem;">{{ number_format($batch->not_found_count) }}</div>
                            <div class="text-muted" style="font-size: 0.62rem;">Not Found</div>
                        </div>
                        <div class="col-3">
                            <div class="fw-bold text-danger" style="font-size: 1rem;">{{ number_format($batch->failed_count) }}</div>
                            <div class="text-muted" style="font-size: 0.62rem;">Failed</div>
                        </div>
                    </div>

                    @if(! empty($batch->flagged))
                    <hr class="my-2" style="border-color: #e9ecef;">
                    <div class="fw-semibold mb-1"><i class="ti ti-flag text-danger me-1"></i>Flagged rows</div>
                    <ul class="mb-0 ps-3" style="max-height: 240px; overflow-y: auto;">
                        @foreach($batch->flagged as $flag)
                            <li>Row {{ $flag['row'] ?? '—' }} (<code>{{ $flag['registration_number'] ?? '' }}</code>) — {{ $flag['reason'] ?? '' }}</li>
                        @endforeach
                    </ul>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <span style="font-size: 0.7rem; font-weight: 600;">Actions</span>
                </div>
                <div class="card-body py-2 px-2 d-flex flex-column gap-1">
                    @if($batch->status !== 'closed')
                        <form action="{{ route('students.certificates.close', $batch->id) }}" method="POST">
                            @csrf
                            <button type="button" class="btn btn-outline-secondary btn-sm rounded-sm w-100" style="font-size: 0.7rem;" onclick="confirmSubmit(this, 'Close reference {{ $batch->reference_number }}? It will move to the Closed tab.', {danger: false, title: 'Close Reference', confirmText: 'Close'})">
                                <i class="ti ti-check me-1"></i> Close Reference
                            </button>
                        </form>
                    @else
                        <form action="{{ route('students.certificates.reopen', $batch->id) }}" method="POST">
                            @csrf
                            <button type="button" class="btn btn-outline-warning btn-sm rounded-sm w-100" style="font-size: 0.7rem;" onclick="confirmSubmit(this, 'Re-open reference {{ $batch->reference_number }}? It will move back to the Active tab.', {danger: false, title: 'Re-open Reference', confirmText: 'Re-open'})">
                                <i class="ti ti-arrow-back-up me-1"></i> Re-open Reference
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
