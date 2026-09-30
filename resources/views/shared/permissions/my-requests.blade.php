@extends('layouts.master')

@section('title', 'My Permission Requests')

@section('content')
<div class="container-fluid px-2">
    
    <!-- Breadcrumb -->
    <x-breadcrumb :items="[
        ['name' => 'Dashboard', 'url' => route('dashboard')],
        ['name' => 'My Permission Requests', 'url' => '#']
    ]" />
    
    <!-- Page Header -->
    <x-page-header title="MY PERMISSION REQUESTS" description="View and manage your permission requests">
        <div>
            <a href="{{ route('permissions.create') }}" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
                <i class="ti ti-plus me-1"></i> Request Permission
            </a>
            <a href="{{ route('travel-permissions.create') }}" class="btn btn-outline-primary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
                <i class="ti ti-plane me-1"></i> Request Travel
            </a>
        </div>
    </x-page-header>
    
    <!-- Stats Cards -->
    <div class="row g-2 mb-3">
        <x-stats-card value="{{ $stats['total'] }}" label="Total Permissions" subtext="all time" />
        <x-stats-card value="{{ $stats['pending'] }}" label="Pending" subtext="awaiting approval" color="warning" />
        <x-stats-card value="{{ $stats['approved'] }}" label="Approved" subtext="granted" color="success" />
        <x-stats-card value="{{ $stats['rejected'] }}" label="Rejected" subtext="denied" color="danger" />
    </div>
    
    <x-alert-success />
    
    <x-alert-danger />
    
    <!-- Search Card -->
    <x-search-card
        placeholder="Search by type or reason..."
        id="myPermissionRequestsSearchInput"
        name="search"
        :value="request('search')"
        :showStatusFilter="true"
        :statusOptions="['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected', 'cancelled' => 'Cancelled']"
    >
        <div id="myPermissionRequestsSearchStats" class="text-muted" style="font-size: 0.65rem;">
            {{ $permissionRequests->total() }} requests total
        </div>
    </x-search-card>

    <div class="row g-2">
        <!-- Requests - main column -->
        <div class="col-lg-8">
            <!-- Short Permissions Table -->
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-clock text-primary me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Short Permissions</span>
                    </div>
                </div>
                <div id="myPermissionRequestsResults">
                    @include('shared.permissions._my-requests-results')
                </div>
            </div>

            <!-- Travel Permissions Section (Existing Travel Requests) -->
            @if(isset($travelRequests) && $travelRequests->count() > 0)
            <div class="card rounded-sm mt-2">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-plane text-primary me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Travel Permissions</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0" style="font-size: 0.75rem;">
                            <thead>
                                <tr>
                                    <th class="ps-2 py-1">Destination</th>
                                    <th class="py-1">Period</th>
                                    <th class="py-1">Days</th>
                                    <th class="py-1">Reason</th>
                                    <th class="py-1">Status</th>
                                    <th class="pe-2 py-1">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($travelRequests as $request)
                                <tr>
                                    <td class="ps-2 py-1">{{ $request->destination }}</td>
                                    <td class="py-1">
                                        {{ \Carbon\Carbon::parse($request->start_date)->format('d M') }} -
                                        {{ \Carbon\Carbon::parse($request->end_date)->format('d M') }}
                                    </td>
                                    <td class="py-1">{{ $request->days }}</td>
                                    <td class="py-1">{{ ucfirst($request->travel_reason) }}</td>
                                    <td class="py-1">
                                        @if($request->status == 'pending')
                                            <span class="badge bg-warning-subtle text-warning rounded-sm">Pending</span>
                                        @elseif($request->status == 'approved')
                                            <span class="badge bg-success-subtle text-success rounded-sm">Approved</span>
                                        @else
                                            <span class="badge bg-danger-subtle text-danger rounded-sm">Rejected</span>
                                        @endif
                                    </td>
                                    <td class="pe-2 py-1">
                                        <a href="{{ route('travel-permissions.show', $request->id) }}" class="btn btn-outline-secondary btn-sm rounded-sm" style="padding: 0.1rem 0.4rem;">
                                            <i class="ti ti-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
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
                        <div class="fw-semibold" style="font-size: 0.7rem;"><i class="ti ti-flag me-1 text-primary"></i> Understanding Status</div>
                        <ul class="mb-0 ps-3" style="font-size: 0.65rem; color: #555;">
                            <li><span class="badge bg-warning-subtle text-warning rounded-sm">Pending</span> - still moving through approval</li>
                            <li><span class="badge bg-success-subtle text-success rounded-sm">Approved</span> - granted</li>
                            <li><span class="badge bg-danger-subtle text-danger rounded-sm">Rejected</span> - denied at some stage</li>
                        </ul>
                    </div>

                    <div class="mb-2">
                        <div class="fw-semibold" style="font-size: 0.7rem;"><i class="ti ti-link me-1 text-primary"></i> Quick Links</div>
                        <ul class="mb-0 ps-3" style="font-size: 0.65rem; color: #555;">
                            <li><a href="{{ route('permissions.create') }}">Request Permission</a> - short time off (hours)</li>
                            <li><a href="{{ route('travel-permissions.create') }}">Request Travel</a> - official or personal travel</li>
                        </ul>
                    </div>

                    <x-alert-light class="mb-0 py-1 px-2 rounded-sm">

                        <div class="d-flex align-items-start gap-1">
                            <i class="ti ti-tips text-warning mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">Tips</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">Permissions do NOT deduct from your leave balance.</small>
                            </div>

</x-alert-light>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.request-item {
    transition: background 0.2s ease;
}
.request-item:hover {
    background: #F8FAFC;
}
.custom-select-sm {
    font-size: 0.65rem;
    padding: 0.2rem 1.2rem 0.2rem 0.4rem;
    border: 1px solid #E8E2D5;
    border-radius: 4px;
    background: white;
    cursor: pointer;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    initLiveSearch({
        url: '{{ route('permissions.my-requests') }}',
        resultsSelector: '#myPermissionRequestsResults',
        statsSelector: '#myPermissionRequestsSearchStats',
        searchInputSelector: '#myPermissionRequestsSearchInput',
        suggestionsSelector: '#myPermissionRequestsSearchInputSuggestions',
        recentSearchesKey: 'myPermissionRequestsRecentSearches',
        statLabel: 'requests',
        perPage: 10,
        filterSelectors: ['#statusFilter'],
        extraParams: () => ({
            status: document.getElementById('statusFilter')?.value || '',
        }),
    });
});
</script>

<x-live-refresh :url="route('permissions.my-requests.heartbeat')" />
@endsection