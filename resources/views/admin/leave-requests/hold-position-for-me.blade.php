@extends('layouts.master')

@section('title', 'Hold Position For Me')

@section('content')
<div class="container-fluid px-2">
    
    <!-- Breadcrumb -->
    <x-breadcrumb :items="[
        ['name' => 'Hold Position For Me', 'url' => '#']
    ]" />
    
    <!-- Page Header -->
    <x-page-header title="STAFF WHO HELD POSITION FOR ME" description="Staff who accepted to hold your position during leave">
    </x-page-header>
    
    <!-- Stats Cards -->
    <div class="row g-2 mb-3">
        <x-stats-card value="{{ $stats['total'] ?? 0 }}" label="Total Requests" subtext="all time" />
        <x-stats-card value="{{ $stats['accepted'] ?? 0 }}" label="Accepted" subtext="held position" color="success" />
        <x-stats-card value="{{ $stats['pending'] ?? 0 }}" label="Pending" subtext="awaiting response" color="warning" />
        <x-stats-card value="{{ $stats['declined'] ?? 0 }}" label="Declined" subtext="refused" color="danger" />
    </div>
    
    <!-- Hold Position History Table -->
    <div class="card rounded-sm">
        <div class="card-header py-1 px-2 bg-light">
            <div class="d-flex align-items-center">
                <i class="ti ti-user-check text-primary me-1" style="font-size: 0.8rem;"></i>
                <span style="font-size: 0.7rem; font-weight: 600;">Hold Position History</span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm mb-0" style="font-size: 0.75rem;">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-2 py-1" width="30">#</th>
                            <th class="py-1">Holder Name</th>
                            <th class="py-1">Leave Period</th>
                            <th class="py-1">Leave Type</th>
                            <th class="py-1">Status</th>
                            <th class="py-1">Responded</th>
                            <th class="pe-2 py-1">Comment</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($holdHistories as $index => $history)
                        <tr>
                            <td class="ps-2 py-1">{{ $holdHistories->firstItem() + $index }}</td>
                            <td class="py-1">
                                <div class="d-flex align-items-center gap-1">
                                    <div class="rounded-circle bg-light d-flex align-items-center justify-content-center" style="width: 24px; height: 24px;">
                                        <span class="fw-semibold" style="font-size: 0.6rem;">
                                            {{ strtoupper(substr($history->holder->first_name, 0, 1)) }}{{ strtoupper(substr($history->holder->last_name, 0, 1)) }}
                                        </span>
                                    </div>
                                    <div>
                                        <span class="fw-semibold">{{ $history->holder->first_name }} {{ $history->holder->last_name }}</span>
                                        <div class="text-muted" style="font-size: 0.55rem;">{{ $history->holder->staff_id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-1">
                                {{ \Carbon\Carbon::parse($history->leaveRequest->start_date)->format('d M Y') }}<br>
                                <small class="text-muted">to</small><br>
                                {{ \Carbon\Carbon::parse($history->leaveRequest->end_date)->format('d M Y') }}
                            </td>
                            <td class="py-1">
                                <span class="badge bg-info-subtle text-info rounded-sm" style="font-size: 0.55rem;">
                                    {{ $history->leaveRequest->leaveType->code ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="py-1">
                                <span class="badge bg-{{ $history->status_badge }}-subtle text-{{ $history->status_badge }} rounded-sm" style="font-size: 0.6rem;">
                                    {{ ucfirst($history->status) }}
                                </span>
                            </td>
                            <td class="py-1">
                                @if($history->responded_at)
                                    {{ $history->responded_at->format('d M Y') }}<br>
                                    <small class="text-muted">{{ $history->responded_at->format('H:i') }}</small>
                                @else
                                    <span class="text-muted">Pending</span>
                                @endif
                            </td>
                            <td class="pe-2 py-1">
                                <span class="text-muted" style="font-size: 0.6rem;">{{ Str::limit($history->comment ?? '-', 30) }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-3 text-muted" style="font-size: 0.7rem;">
                                No hold position history found
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer py-1 px-2">
            <x-pagination :items="$holdHistories" :perPageOptions="[10, 25, 50, 100]" />
        </div>
    </div>
</div>
@endsection