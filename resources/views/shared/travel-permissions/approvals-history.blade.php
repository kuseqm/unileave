@extends('layouts.master')

@section('title', 'Travel Approvals History')

@section('content')
<div class="container-fluid px-2">

    <x-breadcrumb :items="[
        ['name' => 'Travel Permissions', 'url' => route('travel-permissions.my-requests')],
        ['name' => 'Approvals History', 'url' => '#']
    ]" />

    <x-page-header title="TRAVEL APPROVALS HISTORY" description="Track all travel requests you have approved or rejected">
        <a href="{{ route('travel-permissions.pending') }}" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
            <i class="ti ti-arrow-left me-1"></i> Back to Pending
        </a>
    </x-page-header>

    <!-- Stats Cards -->
    <div class="row g-2 mb-3">
        <x-stats-card value="{{ $stats['total'] }}" label="Total Decisions" subtext="all time" color="primary" icon="history" />
        <x-stats-card value="{{ $stats['approved'] }}" label="Approved" subtext="you approved" color="success" icon="check" />
        <x-stats-card value="{{ $stats['rejected'] }}" label="Rejected" subtext="you rejected" color="danger" icon="x" />
        <x-stats-card value="{{ $stats['this_month'] }}" label="This Month" subtext="decisions this month" color="info" icon="calendar" />
    </div>

    <x-search-card
        placeholder="Search by staff name or ID..."
        id="travelApprovalsSearchInput"
        name="search"
        :value="request('search')"
        :showCategoryFilter="true"
        categoryLabel="Action"
        :categoryOptions="['approved' => 'Approved', 'rejected' => 'Rejected']"
    >
        <div id="travelApprovalsSearchStats" class="text-muted" style="font-size: 0.65rem;">
            {{ $approvals->total() }} records
        </div>
    </x-search-card>

    <div class="row g-2">
        <!-- Approval records - main column -->
        <div class="col-lg-8">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-history text-primary me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Your Approval Records</span>
                    </div>
                </div>
                <div id="travelApprovalsResults">
                    @include('shared.travel-permissions._approvals-history-results')
                </div>
            </div>
        </div>

        <!-- Information - narrow column -->
        <div class="col-lg-4">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-info-circle text-info me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">About Approvals History</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <x-alert-light class="mb-0 py-1 px-2 rounded-sm">

                        <div class="d-flex align-items-start gap-1">
                            <i class="ti ti-tips text-warning mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">Tips</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">&bull; Use the Action filter to see only approvals or only rejections</small><br>
                                <small class="text-muted" style="font-size: 0.6rem;">&bull; This is a permanent record - decisions here can't be changed</small>
                            </div>

</x-alert-light>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    initLiveSearch({
        url: '{{ route('travel-permissions.approvals-history') }}',
        resultsSelector: '#travelApprovalsResults',
        statsSelector: '#travelApprovalsSearchStats',
        searchInputSelector: '#travelApprovalsSearchInput',
        suggestionsSelector: '#travelApprovalsSearchInputSuggestions',
        recentSearchesKey: 'travelApprovalsRecentSearches',
        statLabel: 'records',
        perPage: 10,
        filterSelectors: ['#categoryFilter'],
        extraParams: () => ({
            action: document.getElementById('categoryFilter')?.value || '',
        }),
    });
});
</script>
@endsection
