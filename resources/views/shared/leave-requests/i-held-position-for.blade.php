@extends('layouts.master')

@section('title', 'My Assignments')

@section('content')
<div class="container-fluid px-2">

    <!-- Breadcrumb -->
    <x-breadcrumb :items="[
        ['name' => 'My Assignments', 'url' => '#']
    ]" />

    <!-- Page Header -->
    <x-page-header title="MY ASSIGNMENTS" description="Leave requests where you are asked to hold position for others">
        <a href="{{ route('leave-requests.my-requests') }}" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
            <i class="ti ti-arrow-left me-1"></i> Back
        </a>
        <a href="{{ route('leave-requests.create') }}" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
            <i class="ti ti-plus me-1"></i> Apply for Leave
        </a>
    </x-page-header>

    <!-- Stats Cards -->
    @php
        $stats = [
            'total' => $actingRequests->total(),
            'unresponsive' => $unresponsiveCount,
            'accepted' => $holdHistories->where('status', 'accepted')->count(),
            'declined' => $holdHistories->where('status', 'declined')->count(),
        ];
    @endphp

    <div class="row g-2 mb-3">
        <x-stats-card value="{{ $stats['total'] }}" label="Awaiting Response" subtext="pending your reply" color="warning" />
        <x-stats-card value="{{ $stats['unresponsive'] }}" label="Overdue" subtext="pending over 48h" color="danger" />
        <x-stats-card value="{{ $stats['accepted'] }}" label="Accepted" subtext="recent history" color="success" />
        <x-stats-card value="{{ $stats['declined'] }}" label="Declined" subtext="recent history" color="secondary" />
    </div>

    <x-alert-success />

    <x-alert-danger />

    <!-- Search Card -->
    <x-search-card placeholder="Search by requester name..." id="assignmentsSearchInput" name="search" :value="request('search')">
        <div id="assignmentsSearchStats" class="text-muted" style="font-size: 0.65rem;">
            {{ $actingRequests->total() }} pending assignments total
        </div>
    </x-search-card>

    <div class="row g-2">
        <!-- Recent History - main column -->
        <div class="col-lg-8">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-history text-muted me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Recent History</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    @forelse($holdHistories as $history)
                    <div class="request-item p-3 border-bottom">
                        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-2">
                            <div>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="fw-semibold" style="font-size: 0.85rem;">
                                        {{ $history->requester->first_name ?? 'Unknown' }} {{ $history->requester->last_name ?? '' }}
                                    </span>
                                    <span class="badge {{
                                        $history->status == 'accepted' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger'
                                    }} rounded-sm px-2 py-1" style="font-size: 0.6rem;">
                                        {{ ucfirst($history->status) }}
                                    </span>
                                </div>
                                <div class="text-muted" style="font-size: 0.65rem; margin-top: 4px;">
                                    <i class="ti ti-calendar me-1"></i>
                                    {{ \Carbon\Carbon::parse($history->leaveRequest->start_date)->format('M d, Y') }} -
                                    {{ \Carbon\Carbon::parse($history->leaveRequest->end_date)->format('M d, Y') }}
                                </div>
                            </div>
                            <div>
                                <a href="{{ route('leave-requests.show', $history->leaveRequest->id) }}" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.6rem; padding: 0.2rem 0.5rem;">
                                    <i class="ti ti-eye me-1"></i> Details
                                </a>
                            </div>
                        </div>
                        @if($history->comment)
                        <div class="mt-2 pt-2 border-top" style="border-color: #E8E2D5;">
                            <div class="text-muted" style="font-size: 0.6rem;">Your Comment</div>
                            <div class="small" style="font-size: 0.65rem;">"{{ $history->comment }}"</div>
                        </div>
                        @endif
                    </div>
                    @empty
                    <div class="text-center py-4">
                        <i class="ti ti-history" style="font-size: 2rem; color: #CBD5E1;"></i>
                        <p class="text-muted mt-2" style="font-size: 0.75rem;">No history yet.</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Pending Your Response - narrow column -->
        <div class="col-lg-4">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-user-check text-primary me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Pending Your Response</span>
                    </div>
                </div>
                <div id="assignmentsResults">
                    @include('shared.leave-requests._i-held-position-for-results')
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function submitActingResponse(id, action) {
    document.getElementById('action_value_' + id).value = action;
    document.querySelector('#actingResponseModal' + id + ' form').submit();
}

document.addEventListener('DOMContentLoaded', function () {
    initLiveSearch({
        url: '{{ route('leave-requests.i-held-position-for') }}',
        resultsSelector: '#assignmentsResults',
        statsSelector: '#assignmentsSearchStats',
        searchInputSelector: '#assignmentsSearchInput',
        suggestionsSelector: '#assignmentsSearchInputSuggestions',
        recentSearchesKey: 'assignmentsRecentSearches',
        statLabel: 'pending assignments',
        perPage: 10,
    });
});
</script>

<style>
.request-item {
    transition: background 0.2s ease;
}
.request-item:hover {
    background: #F8FAFC;
}
</style>
@endsection
