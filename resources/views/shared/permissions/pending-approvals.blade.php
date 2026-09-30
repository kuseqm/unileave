@extends('layouts.master')

@section('title', 'Pending Permission Approvals')

@section('content')
<div class="container-fluid px-2">
    
    <!-- Breadcrumb -->
    <x-breadcrumb :items="[
        ['name' => 'Dashboard', 'url' => route('dashboard')],
        ['name' => 'Pending Approvals', 'url' => '#']
    ]" />
    
    <!-- Page Header -->
    <x-page-header title="PENDING PERMISSION APPROVALS" description="Permission and travel requests awaiting your approval">
    </x-page-header>
    
    <!-- Stats Cards -->
    @php
        $totalPending = ($pendingRequests->total() ?? 0) + ($travelPending->count() ?? 0);
        $permissionPending = $pendingRequests->total() ?? 0;
        $travelPendingCount = $travelPending->count() ?? 0;
    @endphp
    
    <div class="row g-2 mb-3">
        <x-stats-card value="{{ $totalPending }}" label="Total Pending" subtext="awaiting your decision" color="warning" />
        <x-stats-card value="{{ $permissionPending }}" label="Short Permissions" subtext="awaiting approval" color="info" />
        <x-stats-card value="{{ $travelPendingCount }}" label="Travel Permissions" subtext="awaiting approval" color="primary" />
        <x-stats-card value="{{ $overdueCount ?? 0 }}" label="Overdue" subtext="pending over 48h" color="danger" />
    </div>
    
    <x-alert-success />
    
    <x-alert-danger />
    
    <!-- Search Card -->
    <x-search-card placeholder="Search by staff name or staff ID..." id="pendingPermissionsSearchInput" name="search" :value="request('search')">
        <div id="pendingPermissionsSearchStats" class="text-muted" style="font-size: 0.65rem;">
            {{ $pendingRequests->total() }} pending permissions total
        </div>
    </x-search-card>

    <div class="row g-2">
        <!-- Pending requests - main column -->
        <div class="col-lg-8">
            <!-- Short Permissions Pending -->
            <div class="card rounded-sm mb-2">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-clock text-warning me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Pending Short Permissions</span>
                    </div>
                </div>
                <div id="pendingPermissionsResults">
                    @include('shared.permissions._pending-approvals-results')
                </div>
            </div>

            <!-- Travel Permissions Pending -->
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-plane text-primary me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Pending Travel Approvals</span>
                        @if($travelPendingCount > 0)
                        <span class="badge bg-warning ms-2" style="font-size: 0.6rem;">{{ $travelPendingCount }}</span>
                        @endif
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($travelPending->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm mb-0" style="font-size: 0.75rem;">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-2 py-1">Staff</th>
                                    <th class="py-1">Destination</th>
                                    <th class="py-1">Travel Period</th>
                                    <th class="py-1">Days</th>
                                    <th class="py-1">Reason</th>
                                    <th class="py-1">Cost Coverage</th>
                                    <th class="pe-2 py-1">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($travelPending as $request)
                                <tr>
                                    <td class="ps-2 py-1">
                                        <span class="fw-semibold">{{ $request->staff->first_name }} {{ $request->staff->last_name }}</span>
                                        <div class="text-muted" style="font-size: 0.6rem;">{{ $request->staff->staff_id }}</div>
                                    </td>
                                    <td class="py-1">{{ $request->destination }}</td>
                                    <td class="py-1">
                                        {{ \Carbon\Carbon::parse($request->start_date)->format('d M Y') }}<br>
                                        <small class="text-muted">to {{ \Carbon\Carbon::parse($request->end_date)->format('d M Y') }}</small>
                                    </td>
                                    <td class="py-1">{{ $request->days }}</td>
                                    <td class="py-1">{{ ucfirst($request->travel_reason) }}</td>
                                    <td class="py-1">
                                        @php
                                            $coverageLabels = [
                                                'organizers_full' => 'Organizers Full',
                                                'organizers_partial' => 'Organizers Partial',
                                                'university' => 'University',
                                                'private' => 'Private'
                                            ];
                                        @endphp
                                        <span class="badge bg-secondary-subtle text-secondary rounded-sm" style="font-size: 0.55rem;">
                                            {{ $coverageLabels[$request->cost_coverage] ?? $request->cost_coverage }}
                                        </span>
                                    </td>
                                    <td class="pe-2 py-1">
                                        <a href="{{ route('travel-permissions.pending') }}" class="btn btn-primary btn-sm rounded-sm" style="padding: 0.2rem 0.4rem; font-size: 0.6rem;">
                                            <i class="ti ti-eye"></i> Review
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-3">
                        <i class="ti ti-plane" style="font-size: 1.5rem; color: #CBD5E1;"></i>
                        <p class="text-muted mt-1" style="font-size: 0.7rem;">No pending travel permission requests</p>
                    </div>
                    @endif
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
                    <x-alert-light class="mb-0 py-1 px-2 rounded-sm">

                        <div class="d-flex align-items-start gap-1">
                            <i class="ti ti-tips text-warning mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">Tips</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">&bull; Short permissions and travel requests are reviewed here together</small><br>
                                <small class="text-muted" style="font-size: 0.6rem;">&bull; Items over 48 hours old are flagged as overdue</small>
                            </div>

</x-alert-light>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.modal-content {
    border: none;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    initLiveSearch({
        url: '{{ route('permissions.pending') }}',
        resultsSelector: '#pendingPermissionsResults',
        statsSelector: '#pendingPermissionsSearchStats',
        searchInputSelector: '#pendingPermissionsSearchInput',
        suggestionsSelector: '#pendingPermissionsSearchInputSuggestions',
        recentSearchesKey: 'pendingPermissionsRecentSearches',
        statLabel: 'pending permissions',
        perPage: 10,
    });
});
</script>

<x-live-refresh :url="route('permissions.pending.heartbeat')" />
@endsection