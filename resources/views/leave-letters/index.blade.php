@extends('layouts.master')

@section('title', 'My Leave Letters')

@section('content')
<div class="container-fluid px-2">
    
    <x-breadcrumb :items="[
        ['name' => 'Dashboard', 'url' => route('dashboard')],
        ['name' => 'My Leave Letters', 'url' => '#']
    ]" />
    
    <x-page-header title="MY LEAVE LETTERS" description="Leave approval letters from your requests">
        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
            <i class="ti ti-arrow-left me-1"></i> Back
        </a>
    </x-page-header>
    
    <!-- Stats Cards -->
    <div class="row g-2 mb-3">
        <x-stats-card value="{{ $stats['total'] }}" label="Total Letters" subtext="all time" color="primary" icon="file-text" />
        <x-stats-card value="{{ $stats['draft'] }}" label="Draft" subtext="not finalized" color="secondary" icon="file" />
        <x-stats-card value="{{ $stats['generated'] }}" label="Generated" subtext="ready" color="success" icon="check" />
        <x-stats-card value="{{ $stats['printed'] }}" label="Printed" subtext="already printed" color="info" icon="printer" />
    </div>
    
    <!-- Letters Table -->
    <div class="card rounded-sm">
        <div class="card-header py-1 px-2 bg-light">
            <div class="d-flex align-items-center">
                <i class="ti ti-file-text text-primary me-1" style="font-size: 0.7rem;"></i>
                <span style="font-weight: 600; font-size: 0.7rem;">Leave Approval Letters</span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm mb-0" style="font-size: 0.7rem;">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-2 py-1">Letter No.</th>
                            <th class="py-1">Title</th>
                            <th class="py-1">Leave Type</th>
                            <th class="py-1">Status</th>
                            <th class="py-1">Date</th>
                            <th class="pe-2 py-1 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($letters as $letter)
                        <tr>
                            <td class="ps-2 py-1">
                                <span class="fw-semibold">{{ $letter->letter_number }}</span>
                            </td>
                            <td class="py-1">{{ Str::limit($letter->title, 40) }}</td>
                            <td class="py-1">{{ $letter->leaveRequest->leaveType->name ?? 'N/A' }}</td>
                            <td class="py-1">
                                @if($letter->stage == 'draft')
                                    <span class="badge bg-secondary-subtle text-secondary rounded-pill">Draft</span>
                                @elseif($letter->stage == 'generated')
                                    <span class="badge bg-success-subtle text-success rounded-pill">Generated</span>
                                @else
                                    <span class="badge bg-primary-subtle text-primary rounded-pill">{{ ucfirst($letter->stage) }}</span>
                                @endif
                            </td>
                            <td class="py-1">{{ $letter->created_at->format('d M Y') }}</td>
                            <td class="pe-2 py-1 text-center">
                                <div class="d-flex gap-1 justify-content-center">
                                    <a href="{{ route('leave-letters.show', $letter->id) }}" 
                                       class="btn btn-sm btn-outline-info rounded-sm" 
                                       title="View Letter">
                                        <i class="ti ti-eye"></i>
                                    </a>
                                    <a href="{{ route('leave-letters.print', $letter->id) }}" 
                                       target="_blank"
                                       class="btn btn-sm btn-outline-primary rounded-sm" 
                                       title="Print">
                                        <i class="ti ti-printer"></i>
                                    </a>
                                    <a href="{{ route('leave-letters.download-pdf', $letter->id) }}" 
                                       class="btn btn-sm btn-outline-success rounded-sm" 
                                       title="Download PDF">
                                        <i class="ti ti-download"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                <i class="ti ti-file-text mb-2 d-block" style="font-size: 1.5rem;"></i>
                                <p>No leave letters found</p>
                                <small>When you submit a leave request with letter content, it will appear here.</small>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($letters->hasPages())
        <div class="card-footer py-1 px-2">
            {{ $letters->links() }}
        </div>
        @endif
    </div>
</div>
@endsection