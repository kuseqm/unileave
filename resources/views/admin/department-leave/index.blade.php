@extends('layouts.master')

@section('title', 'Track Department Leave')

@section('content')
<div class="container-fluid px-2">
    <x-breadcrumb :items="[
        ['name' => 'Dashboard', 'url' => route('dashboard')],
        ['name' => 'Track Department Leave', 'url' => route('admin.department-leave.index')]
    ]" />

    <x-page-header title="TRACK DEPARTMENT LEAVE" description="Monitor leave requests within your department.">
    </x-page-header>

    <x-alert-success />
    <x-alert-danger />

    <x-search-card
        placeholder="Search by staff name or staff ID..."
        id="departmentLeaveSearchInput"
        name="search"
        :value="request('search')"
        :showStatusFilter="true"
        :statusOptions="['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected', 'cancelled' => 'Cancelled']"
    >
        <div id="departmentLeaveSearchStats" class="text-muted" style="font-size: 0.65rem;">
            {{ $requests->total() }} leave requests
        </div>
    </x-search-card>

    <div class="row g-2">
        <!-- Department Leave Requests - main column -->
        <div class="col-lg-8">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-calendar text-primary me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Department Leave Requests</span>
                    </div>
                </div>
                <div id="departmentLeaveResults">
                    @include('admin.department-leave._results')
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
                        <div class="fw-semibold" style="font-size: 0.7rem;"><i class="ti ti-calendar-stats me-1 text-primary"></i> About This Page</div>
                        <p class="mb-0" style="font-size: 0.65rem; color: #555;">This tracks leave requests from staff within your department, so you can see who is out or has requests pending at a glance.</p>
                    </div>

                    <div class="mb-2">
                        <div class="fw-semibold" style="font-size: 0.7rem;"><i class="ti ti-flag me-1 text-primary"></i> Understanding Status</div>
                        <ul class="mb-0 ps-3" style="font-size: 0.65rem; color: #555;">
                            <li><span class="badge bg-warning-subtle text-warning rounded-sm">Pending</span> - still moving through approval</li>
                            <li><span class="badge bg-success-subtle text-success rounded-sm">Approved</span> - granted</li>
                            <li><span class="badge bg-danger-subtle text-danger rounded-sm">Rejected</span> - denied at some stage</li>
                            <li><span class="badge bg-secondary-subtle text-secondary rounded-sm">Cancelled</span> - withdrawn</li>
                        </ul>
                    </div>

                    <x-alert-light class="mb-0 py-1 px-2 rounded-sm">
                        <div class="d-flex align-items-start gap-1">
                            <i class="ti ti-tips text-warning mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">Tips</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">&bull; Use the status filter to quickly find pending requests</small><br>
                                <small class="text-muted" style="font-size: 0.6rem;">&bull; This view is read-only tracking, not the approval screen</small>
                            </div>
                        </div>
                    </x-alert-light>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    initLiveSearch({
        url: '{{ route('admin.department-leave.index') }}',
        resultsSelector: '#departmentLeaveResults',
        statsSelector: '#departmentLeaveSearchStats',
        searchInputSelector: '#departmentLeaveSearchInput',
        suggestionsSelector: '#departmentLeaveSearchInputSuggestions',
        recentSearchesKey: 'departmentLeaveRecentSearches',
        statLabel: 'leave requests',
        perPage: 10,
        filterSelectors: ['#statusFilter'],
        extraParams: () => ({
            status: document.getElementById('statusFilter')?.value || '',
        }),
    });
});
</script>
@endsection
