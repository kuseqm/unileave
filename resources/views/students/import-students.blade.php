@extends('layouts.master')

@section('title', ($filter === 'updated' ? 'Updated ' : ($filter === 'created' ? 'New ' : '')) . 'Graduates — ' . $batch->reference_number)

@section('content')
<div class="container-fluid px-2">

    <x-page-header
        title="{{ strtoupper($filter === 'updated' ? 'Updated Graduates' : ($filter === 'created' ? 'New Graduates' : 'Graduates')) }}"
        description="Reference {{ $batch->reference_number }} — {{ $filter === 'updated' ? 'updated records' : ($filter === 'created' ? 'new records' : 'all records') }}">
        <a href="{{ route('students.import.index') }}" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
            <i class="ti ti-arrow-left me-1"></i> Back to Import
        </a>
    </x-page-header>

    <div class="card rounded-sm">
        <div class="card-header py-1 px-2 bg-light">
            <div class="d-flex align-items-center">
                <i class="ti ti-school text-primary me-1" style="font-size: 0.8rem;"></i>
                <span style="font-size: 0.7rem; font-weight: 600;">{{ $filter === 'updated' ? 'Updated Graduates' : ($filter === 'created' ? 'New Graduates' : 'Graduates') }}</span>
                <span class="text-muted ms-2 small">Showing {{ $students->firstItem() ?? 0 }} to {{ $students->lastItem() ?? 0 }} of {{ $students->total() }}</span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm mb-0" style="font-size: 0.75rem;">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-2 py-1">Reg. Number</th>
                            <th class="py-1">Full Name</th>
                            <th class="py-1">Programme</th>
                            <th class="py-1">Grad. Year</th>
                            <th class="py-1">Status</th>
                            @if($filter === 'updated')
                            <th class="py-1">Changes</th>
                            @endif
                            <th class="pe-2 py-1 text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $student)
                        <tr>
                            <td class="ps-2 py-1"><code style="font-size: 0.65rem;">{{ $student->registration_number }}</code></td>
                            <td class="py-1">{{ $student->formatted_name }}</td>
                            <td class="py-1" @if($student->program) title="{{ $student->program->name }}" @endif>{{ $student->program->code ?? $student->program->name ?? '—' }}</td>
                            <td class="py-1">{{ $student->graduation_year ?? '—' }}</td>
                            <td class="py-1">
                                @if($student->is_graduated)
                                    <span class="badge bg-success rounded-sm" style="font-size: 0.6rem;"><i class="ti ti-check"></i> Graduated</span>
                                @else
                                    <span class="badge bg-secondary rounded-sm" style="font-size: 0.6rem;">Not Graduated</span>
                                @endif
                            </td>
                            @if($filter === 'updated')
                            <td class="py-1" style="font-size: 0.65rem; min-width: 220px;">
                                @if(!empty($student->import_diff))
                                <details class="d-inline-block w-100">
                                    <summary class="fw-bold" style="cursor: pointer; color: #0d6efd;"><i class="ti ti-file-diff me-1"></i>View changes</summary>
                                    <div class="mt-1 p-1 border rounded-sm" style="background-color: #f8f9fa;">
                                        @foreach($student->import_diff as $field => $change)
                                        @php
                                            $oldValue = $change['old'] ?? '—';
                                            $newValue = $change['new'] ?? '—';
                                            $fieldLabel = str_replace('_', ' ', $field);
                                        @endphp
                                        <div class="row g-0 mb-1 align-items-center" style="font-size: 0.6rem;">
                                            <div class="col-3 text-capitalize text-muted" style="padding-right: 0.25rem;">{{ $fieldLabel }}</div>
                                            <div class="col-9">
                                                <div class="d-flex align-items-center">
                                                    <span class="flex-fill text-center text-nowrap" style="background-color: #f8d7da; color: #842029; padding: 0.1rem 0.3rem; border-radius: 0.15rem; overflow: hidden; text-overflow: ellipsis;">{{ $oldValue }}</span>
                                                    <i class="ti ti-arrow-right mx-1 text-muted" style="font-size: 0.5rem;"></i>
                                                    <span class="flex-fill text-center text-nowrap" style="background-color: #d1e7dd; color: #0f5132; padding: 0.1rem 0.3rem; border-radius: 0.15rem; overflow: hidden; text-overflow: ellipsis;">{{ $newValue }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </details>
                                @else
                                <span class="text-muted">—</span>
                                @endif
                            </td>
                            @endif
                            <td class="pe-2 py-1 text-end">
                                <a href="{{ route('students.show', $student->id) }}" class="btn btn-sm btn-outline-info rounded-sm" style="font-size: 0.6rem; padding: 0.15rem 0.4rem;" title="View">
                                    <i class="ti ti-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ $filter === 'updated' ? 7 : 6 }}" class="text-center py-3 text-muted" style="font-size: 0.7rem;">
                                <i class="ti ti-school" style="font-size: 48px;"></i>
                                <p class="mt-2">No records found</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer py-1 px-2">
            <x-pagination :items="$students" :perPageOptions="[25, 50, 100]" :ajax="false" />
        </div>
    </div>
</div>
@endsection
