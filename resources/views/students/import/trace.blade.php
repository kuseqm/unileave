@extends('layouts.master')

@section('title', 'Trace Student Import')

@section('content')
<div class="container-fluid px-2">
    <x-page-header
        title="Trace Student Import"
        description="Find the upload file and batch that imported a graduate">
        <a href="{{ route('students.import.index') }}" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
            <i class="ti ti-arrow-left me-1"></i> Back to Import
        </a>
    </x-page-header>

    <div class="card rounded-sm mb-3">
        <div class="card-header py-1 px-2 bg-light">
            <span style="font-size: 0.7rem; font-weight: 600;"><i class="ti ti-search text-muted me-1"></i>Search by Registration Number</span>
        </div>
        <div class="card-body py-2 px-2" style="font-size: 0.7rem;">
            <form action="{{ route('students.import.trace') }}" method="GET" class="d-flex gap-2">
                <input type="text" name="reg" class="form-control form-control-sm rounded-sm" value="{{ $reg }}" placeholder="e.g. 2009/0518" style="font-size: 0.7rem;" autocomplete="off" required>
                <button type="submit" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
                    <i class="ti ti-search me-1"></i> Trace
                </button>
            </form>
        </div>
    </div>

    @if($student)
    <div class="card rounded-sm">
        <div class="card-header py-1 px-2 bg-light d-flex align-items-center justify-content-between">
            <span style="font-size: 0.7rem; font-weight: 600;">Result for {{ $student->formatted_name }}</span>
            <a href="{{ route('students.show', $student->id) }}" class="btn btn-outline-info btn-sm rounded-sm" style="font-size: 0.6rem; padding: 0.1rem 0.4rem;">View Graduate</a>
        </div>
        <div class="card-body py-2 px-2" style="font-size: 0.7rem;">
            <div class="row g-2 mb-3">
                <div class="col-md-4"><span class="text-muted">Reg:</span> <code>{{ $student->registration_number }}</code></div>
                <div class="col-md-4"><span class="text-muted">Name:</span> {{ $student->formatted_name }}</div>
                <div class="col-md-4"><span class="text-muted">Latest grad year:</span> {{ $student->graduation_year ?? '—' }}</div>
                <div class="col-md-4"><span class="text-muted">Programme:</span> {{ $student->program?->name ?? '—' }}</div>
                <div class="col-md-4"><span class="text-muted">Campus:</span> {{ $student->campus?->name ?? '—' }}</div>
            </div>

            @if($student->rosterImport)
            <div class="alert alert-info py-2 px-2 mb-3" style="font-size: 0.65rem;">
                <div class="row g-2">
                    <div class="col-md-4"><span class="text-muted">Batch ref:</span> <a href="{{ route('students.import.show', $student->rosterImport->id) }}"><code>{{ $student->rosterImport->reference_number }}</code></a></div>
                    <div class="col-md-4"><span class="text-muted">File:</span> <strong>{{ $student->rosterImport->original_filename ?? '—' }}</strong></div>
                    <div class="col-md-4"><span class="text-muted">Uploaded:</span> {{ $student->rosterImport->uploaded_at?->format('d M Y H:i') ?? '—' }}</div>
                    <div class="col-md-4"><span class="text-muted">By:</span> {{ trim(($student->rosterImport->importedBy?->first_name ?? '').' '.($student->rosterImport->importedBy?->last_name ?? '')) }}</div>
                    <div class="col-md-4"><span class="text-muted">Status:</span> <span class="badge {{ $student->rosterImport->status === 'closed' ? 'bg-secondary-subtle text-secondary' : 'bg-primary-subtle text-primary' }} rounded-sm">{{ ucfirst($student->rosterImport->status) }}</span></div>
                </div>
            </div>
            @else
            <div class="alert alert-warning py-2 px-2 mb-3" style="font-size: 0.65rem;">
                <i class="ti ti-alert-triangle me-1"></i> No import batch is linked to this record.
            </div>
            @endif

            @if($student->graduations->isNotEmpty())
            <hr class="my-2" style="border-color: #e9ecef;">
            <h6 style="font-size: 0.7rem; font-weight: 600;">Graduation history</h6>
            <div class="table-responsive">
                <table class="table table-sm mb-0" style="font-size: 0.65rem;">
                    <thead class="table-light">
                        <tr>
                            <th>Year</th>
                            <th>Programme</th>
                            <th>Batch</th>
                            <th>File</th>
                            <th>When</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($student->graduations as $graduation)
                        <tr>
                            <td>{{ $graduation->graduation_year ?? '—' }}</td>
                            <td>{{ $graduation->program?->degree_abbreviation ?? $graduation->program?->code ?? $graduation->program?->name ?? '—' }}</td>
                            <td>
                                @if($graduation->rosterImport)
                                <a href="{{ route('students.import.show', $graduation->rosterImport->id) }}"><code>{{ $graduation->rosterImport->reference_number }}</code></a>
                                @else
                                <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>{{ $graduation->rosterImport?->original_filename ?? '—' }}</td>
                            <td>{{ $graduation->rosterImport?->uploaded_at?->format('d M Y H:i') ?? '—' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>
    @elseif($reg)
    <div class="alert alert-warning py-2 px-2" style="font-size: 0.7rem;">
        <i class="ti ti-alert-triangle me-1"></i> No student found with registration <code>{{ $reg }}</code>.
    </div>
    @endif
</div>
@endsection
