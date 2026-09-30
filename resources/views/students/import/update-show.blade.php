@extends('layouts.master')

@section('title', 'Update Details')

@section('content')
@php
    $display = function ($value) {
        if (is_array($value)) {
            return json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }
        return (string) $value;
    };
    $batch = $studentGraduation->rosterImport;
@endphp
<div class="container-fluid px-2">

    <x-page-header title="IMPORT UPDATE DETAILS" description="Old and new values for this graduate's latest import update">
        <div class="d-flex gap-1">
            @if($studentGraduation->import_review_status !== 'accepted')
            <form action="{{ route('students.import.updates.accept', $studentGraduation->id) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-outline-success btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
                    <i class="ti ti-check me-1"></i> Accept
                </button>
            </form>
            @endif
            @if($studentGraduation->import_review_status !== 'rejected')
            <form action="{{ route('students.import.updates.reject', $studentGraduation->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Reject this update? Every field it changed will be restored to its previous value.');">
                @csrf
                <button type="submit" class="btn btn-outline-danger btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
                    <i class="ti ti-x me-1"></i> Reject
                </button>
            </form>
            @endif
            <a href="{{ route('students.import.updates') }}" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
                <i class="ti ti-arrow-left me-1"></i> Back to Updates
            </a>
        </div>
    </x-page-header>

    @if (session('success'))
        <div class="alert alert-success py-2 px-2 rounded-sm mb-3" style="font-size: 0.75rem;">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger py-2 px-2 rounded-sm mb-3" style="font-size: 0.75rem;">{{ session('error') }}</div>
    @endif

    <div class="row g-2 mb-3">
        <div class="col-lg-5">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <span style="font-size: 0.7rem; font-weight: 600;">
                        <i class="ti ti-file-info text-primary me-1"></i>File / Batch Details
                    </span>
                </div>
                <div class="card-body p-2" style="font-size: 0.7rem;">
                    <div class="mb-2">
                        <span class="text-muted">Reference:</span>
                        @if($batch)
                            <a href="{{ route('students.import.show', $batch->id) }}" class="text-decoration-none">
                                <code>{{ $batch->reference_number }}</code>
                            </a>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </div>
                    <div class="mb-2">
                        <span class="text-muted">Original file:</span>
                        <span class="fw-semibold">{{ $batch?->original_filename ?? '—' }}</span>
                    </div>
                    <div class="mb-2">
                        <span class="text-muted">Status:</span>
                        @if($batch)
                            @if($batch->status === 'closed')
                                <span class="badge bg-light text-muted border rounded-sm">Closed</span>
                            @else
                                <span class="badge bg-success-subtle text-success rounded-sm">Open</span>
                            @endif
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </div>
                    <div class="mb-2">
                        <span class="text-muted">Uploaded:</span>
                        <span>{{ $batch?->uploaded_at?->format('d M Y H:i') ?? '—' }}</span>
                    </div>
                    <div class="mb-2">
                        <span class="text-muted">Update time:</span>
                        <span>{{ $studentGraduation->updated_at->format('d M Y H:i') }}</span>
                    </div>
                    <div class="mb-2">
                        <span class="text-muted">Changed by:</span>
                        <span>{{ $studentGraduation->importedBy?->name ?? '—' }}</span>
                    </div>
                    <div class="mb-2">
                        <span class="text-muted">Review status:</span>
                        @if($studentGraduation->import_review_status === 'accepted')
                            <span class="badge bg-success-subtle text-success rounded-sm">Accepted</span>
                        @elseif($studentGraduation->import_review_status === 'rejected')
                            <span class="badge bg-danger-subtle text-danger rounded-sm">Rejected</span>
                        @else
                            <span class="badge bg-warning-subtle text-warning rounded-sm">Pending</span>
                        @endif
                        @if($studentGraduation->import_reviewed_by)
                            <span class="text-muted">by {{ $studentGraduation->importReviewedBy?->name }} on {{ $studentGraduation->import_reviewed_at?->format('d M Y H:i') }}</span>
                        @endif
                    </div>
                    <div class="mb-0">
                        <span class="text-muted">Updates in this file:</span>
                        <span class="badge bg-info rounded-sm">{{ number_format($updateCount) }}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-7">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <span style="font-size: 0.7rem; font-weight: 600;">
                        <i class="ti ti-user text-primary me-1"></i>Student
                    </span>
                </div>
                <div class="card-body p-2" style="font-size: 0.7rem;">
                    <div class="mb-2">
                        <span class="text-muted">Registration number:</span>
                        <code>{{ $studentGraduation->student?->registration_number ?? '—' }}</code>
                    </div>
                    <div class="mb-2">
                        <span class="text-muted">Name:</span>
                        <a href="{{ route('students.show', $studentGraduation->student?->id ?? 0) }}" class="text-decoration-none fw-semibold">
                            {{ $studentGraduation->student?->formatted_name ?? $studentGraduation->full_name }}
                        </a>
                    </div>
                    <div class="mb-2">
                        <span class="text-muted">Programme:</span>
                        <span>{{ $studentGraduation->program?->degree_abbreviation ?? $studentGraduation->program?->code ?? $studentGraduation->program?->name ?? '—' }}</span>
                    </div>
                    <div class="mb-2">
                        <span class="text-muted">Campus:</span>
                        <span>{{ $studentGraduation->campus?->name ?? '—' }}</span>
                    </div>
                    <div class="mb-0">
                        <span class="text-muted">Graduation year:</span>
                        <span>{{ $studentGraduation->graduation_year ?? '—' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card rounded-sm">
        <div class="card-header py-1 px-2 bg-light">
            <span style="font-size: 0.7rem; font-weight: 600;">
                <i class="ti ti-exchange text-primary me-1"></i>Changed Fields
            </span>
        </div>
        <div class="table-responsive">
            <table class="table table-sm mb-0" style="font-size: 0.7rem;">
                <thead class="table-light">
                    <tr>
                        <th style="width: 25%;">Field</th>
                        <th style="width: 37.5%;">Old value</th>
                        <th style="width: 37.5%;">New value</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($studentGraduation->import_diff ?? [] as $field => $change)
                        @if(in_array($field, ['raw_data', 'import_diff']))
                            @continue
                        @endif
                        @php
                            $old = is_array($change) ? ($change['old'] ?? null) : null;
                            $new = is_array($change) ? ($change['new'] ?? $change) : $change;
                            $oldText = $old !== null && $old !== '' ? $display($old) : '—';
                            $newText = $new !== null && $new !== '' ? $display($new) : '—';
                        @endphp
                        <tr>
                            <td class="fw-semibold">{{ $field }}</td>
                            <td><span class="text-decoration-line-through text-muted">{{ $oldText }}</span></td>
                            <td><span class="fw-semibold">{{ $newText }}</span></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted py-4">No changed fields recorded.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
