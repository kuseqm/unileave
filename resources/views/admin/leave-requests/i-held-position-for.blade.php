@extends('layouts.master')

@section('title', 'I Held Position For')

@section('content')
<div class="container-fluid px-2">
    
    <!-- Breadcrumb -->
    <x-breadcrumb :items="[
        ['name' => 'I Held Position For', 'url' => '#']
    ]" />
    
    <!-- Page Header -->
    <x-page-header title="STAFF I HELD POSITION FOR" description="Staff whose position you held during their leave">
    </x-page-header>
    
    <!-- Stats Cards -->
    <div class="row g-2 mb-3">
        <x-stats-card value="{{ $stats['total'] ?? 0 }}" label="Total Requests" subtext="received" />
        <x-stats-card value="{{ $stats['accepted'] ?? 0 }}" label="Accepted" subtext="you accepted" color="success" />
        <x-stats-card value="{{ $stats['pending'] ?? 0 }}" label="Pending" subtext="awaiting response" color="warning" />
        <x-stats-card value="{{ $stats['declined'] ?? 0 }}" label="Declined" subtext="you declined" color="danger" />
    </div>
    
    <div class="row g-2">
        <!-- Hold Position History Table - main column -->
        <div class="col-lg-8">
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
                                    <th class="py-1">Requester Name</th>
                                    <th class="py-1">Leave Period</th>
                                    <th class="py-1">Leave Type</th>
                                    <th class="py-1">Your Response</th>
                                    <th class="py-1">Responded At</th>
                                    <th class="pe-2 py-1">Your Comment</th>
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
                                                    {{ strtoupper(substr($history->requester->first_name, 0, 1)) }}{{ strtoupper(substr($history->requester->last_name, 0, 1)) }}
                                                </span>
                                            </div>
                                            <div>
                                                <span class="fw-semibold">{{ $history->requester->first_name }} {{ $history->requester->last_name }}</span>
                                                <div class="text-muted" style="font-size: 0.55rem;">{{ $history->requester->staff_id }}</div>
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
                                            <span class="text-muted">Not responded</span>
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

        <!-- Information - narrow column -->
        <div class="col-lg-4">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-info-circle text-info me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Information</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <div class="mb-2">
                        <div class="fw-semibold" style="font-size: 0.7rem;"><i class="ti ti-flag me-1 text-primary"></i> Your Response</div>
                        <ul class="mb-0 ps-3" style="font-size: 0.65rem; color: #555;">
                            <li><span class="badge bg-warning-subtle text-warning rounded-sm">Pending</span> - you haven't responded yet</li>
                            <li><span class="badge bg-success-subtle text-success rounded-sm">Accepted</span> - you agreed to hold their position</li>
                            <li><span class="badge bg-danger-subtle text-danger rounded-sm">Declined</span> - you turned down the request</li>
                        </ul>
                    </div>

                    <div class="mb-2">
                        <div class="fw-semibold" style="font-size: 0.7rem;"><i class="ti ti-briefcase me-1 text-primary"></i> What This Means</div>
                        <p class="mb-0" style="font-size: 0.65rem; color: #555;">
                            This list is a record of colleagues who requested that you act in their position while they were on leave, and how you responded.
                        </p>
                    </div>

                    <x-alert-light class="mb-0 py-1 px-2 rounded-sm">
                        <div class="d-flex align-items-start gap-1">
                            <i class="ti ti-tips text-warning mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">Tip</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">&bull; Your comment on each response is kept for audit purposes and cannot be edited afterwards</small>
                            </div>
                        </div>
                    </x-alert-light>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection