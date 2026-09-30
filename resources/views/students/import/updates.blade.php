@extends('layouts.master')

@section('title', 'Recent Import Updates')

@section('content')
<div class="container-fluid px-2">

    <x-page-header title="RECENT IMPORT UPDATES" description="Every graduate who was updated during an import - click View for full details">
        <a href="{{ route('students.import.index') }}" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
            <i class="ti ti-arrow-left me-1"></i> Back to Imports
        </a>
    </x-page-header>

    @if (session('success'))
        <div class="alert alert-success py-2 px-2 rounded-sm mb-3" style="font-size: 0.75rem;">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger py-2 px-2 rounded-sm mb-3" style="font-size: 0.75rem;">{{ session('error') }}</div>
    @endif

    <x-search-card :action="route('students.import.updates')" :reset-url="route('students.import.updates')" name="q" id="q" :value="$q" placeholder="Search reg number, batch or name..." title="Search Updates" icon="ti-search" :auto-submit="true" />

    <ul class="nav nav-pills mb-3" style="font-size: 0.7rem;">
        @foreach(['pending' => 'Pending review', 'accepted' => 'Accepted', 'rejected' => 'Rejected', 'all' => 'All'] as $key => $label)
            <li class="nav-item">
                <a href="{{ route('students.import.updates', array_filter(['status' => $key, 'q' => $q])) }}"
                   class="nav-link rounded-sm py-1 px-2 {{ $status === $key ? 'active' : '' }}">
                    {{ $label }}
                    @if($key !== 'all')
                        <span class="badge {{ $status === $key ? 'bg-light text-dark' : 'bg-secondary' }} rounded-sm ms-1">{{ number_format($statusCounts[$key]) }}</span>
                    @endif
                </a>
            </li>
        @endforeach
    </ul>

    <div class="row g-2 mb-3">
        <div class="col-lg-8">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light d-flex align-items-center justify-content-between">
                    <span style="font-size: 0.7rem; font-weight: 600;">
                        <i class="ti ti-history-toggle text-primary me-1"></i>Updates found
                    </span>
                    <span class="text-muted" style="font-size: 0.62rem;">{{ $updates->total() }} records</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm mb-0 align-middle" style="font-size: 0.7rem;">
                        <thead class="table-light" style="position: sticky; top: 0;">
                    <tr>
                        <th>Reg No</th>
                        <th>Student</th>
                        <th>Programme</th>
                        <th>Batch</th>
                        <th>Status</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($updates as $update)
                    <tr>
                        <td><code>{{ $update->student?->registration_number ?? '—' }}</code></td>
                        <td>
                            <a href="{{ route('students.show', $update->student?->id ?? 0) }}" class="text-decoration-none fw-semibold">
                                {{ $update->student?->formatted_name ?? $update->full_name }}
                            </a>
                        </td>
                        <td>{{ $update->program?->degree_abbreviation ?? $update->program?->code ?? $update->program?->name ?? '—' }}</td>
                        <td>
                            @if($update->rosterImport)
                                <a href="{{ route('students.import.show', $update->roster_import_id) }}" class="text-decoration-none">
                                    <code>{{ $update->rosterImport->reference_number }}</code>
                                </a>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            @if($update->import_review_status === 'accepted')
                                <span class="badge bg-success-subtle text-success rounded-sm">Accepted</span>
                            @elseif($update->import_review_status === 'rejected')
                                <span class="badge bg-danger-subtle text-danger rounded-sm">Rejected</span>
                            @else
                                <span class="badge bg-warning-subtle text-warning rounded-sm">Pending</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="d-inline-flex gap-1">
                                <a href="{{ route('students.import.updates.show', $update->id) }}" class="btn btn-outline-primary btn-sm rounded-sm" style="font-size: 0.6rem; padding: 0.1rem 0.4rem;">
                                    <i class="ti ti-eye me-1"></i> View
                                </a>
                                @if($update->import_review_status !== 'accepted')
                                <form action="{{ route('students.import.updates.accept', $update->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-success btn-sm rounded-sm" style="font-size: 0.6rem; padding: 0.1rem 0.4rem;">
                                        <i class="ti ti-check me-1"></i> Accept
                                    </button>
                                </form>
                                @endif
                                @if($update->import_review_status !== 'rejected')
                                <form action="{{ route('students.import.updates.reject', $update->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="button" class="btn btn-outline-danger btn-sm rounded-sm" style="font-size: 0.6rem; padding: 0.1rem 0.4rem;" onclick="confirmSubmit(this, 'Reject this update? Every field it changed will be restored to its previous value.', {danger: true, title: 'Reject Update', confirmText: 'Reject'})">
                                        <i class="ti ti-x me-1"></i> Reject
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">No import updates found yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($updates->hasPages())
        <div class="p-2 border-top">
            <x-pagination :items="$updates" id="updates" pageParam="page" perPageParam="per_page" :perPageOptions="[50, 100, 200]" />
        </div>
        @endif
    </div>
</div>
<div class="col-lg-4">
    <div class="card rounded-sm">
        <div class="card-header py-1 px-2 bg-light">
            <span style="font-size: 0.7rem; font-weight: 600;">
                <i class="ti ti-info-circle text-info me-1"></i>About this page
            </span>
        </div>
        <div class="card-body p-2" style="font-size: 0.7rem;">
            <p class="mb-2 text-muted">This page lists every graduate record that was <strong>updated</strong> by an import, not newly created.</p>
            <ul class="ps-3 mb-2">
                <li>Click <strong>View</strong> to see the old vs new values and the original file.</li>
                <li><strong>Accept</strong> confirms the update is correct - it just records who reviewed it.</li>
                <li><strong>Reject</strong> restores every field the update changed back to what it was before.</li>
                <li>Every fresh update on the same graduate resets it back to <strong>Pending</strong>, even if it was already reviewed.</li>
                <li>The <strong>Batch</strong> link opens the import reference.</li>
                <li>“Updates in this file” counts how many records in that batch were changed.</li>
            </ul>
            <div class="border-top pt-2 mt-2">
                <span class="text-muted">Total updates in this view:</span>
                <span class="fw-semibold">{{ number_format($updates->total()) }}</span>
            </div>
        </div>
    </div>
</div>
</div>
</div>

<script>
    (function () {
        const input = document.getElementById('q');
        if (! input) return;

        let timeout;
        input.addEventListener('input', function () {
            clearTimeout(timeout);
            timeout = setTimeout(function () {
                input.closest('form')?.submit();
            }, 600);
        });
    })();
</script>
@endsection
