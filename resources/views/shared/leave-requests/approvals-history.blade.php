@extends('layouts.master')

@section('title', 'Approvals History')

@section('content')
<div class="container-fluid px-2">
    
    <!-- Breadcrumb -->
    <x-breadcrumb :items="[
        ['name' => 'Dashboard', 'url' => route('dashboard')],
        ['name' => 'Approvals History', 'url' => '#']
    ]" />
    
    <!-- Page Header -->
    <x-page-header title="APPROVALS HISTORY" description="Track all leave requests you have approved or rejected">
        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
            <i class="ti ti-arrow-left me-1"></i> Back to Dashboard
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
        id="leaveApprovalsSearchInput"
        name="search"
        :value="request('search')"
        :showCategoryFilter="true"
        categoryLabel="Action"
        :categoryOptions="['approved' => 'Approved', 'rejected' => 'Rejected']"
        :showDateRange="true"
        :dateFromValue="request('date_from')"
        :dateToValue="request('date_to')"
    >
        <div id="leaveApprovalsSearchStats" class="text-muted" style="font-size: 0.65rem;">
            {{ $approvals->total() }} records
        </div>
    </x-search-card>

    <div class="row g-2">
        <!-- Main Table Column -->
        <div class="col-lg-8">
            <!-- Approvals Table -->
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-history text-primary me-1" style="font-size: 0.7rem;"></i>
                        <span style="font-weight: 600; font-size: 0.7rem;">Your Approval Records</span>
                    </div>
                </div>
                <div id="leaveApprovalsResults">
                    @include('shared.leave-requests._approvals-history-results')
                </div>
            </div>
        </div>
        
        <!-- Info Card Column -->
        <div class="col-lg-4">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-info-circle text-info me-1" style="font-size: 0.7rem;"></i>
                        <span style="font-weight: 600; font-size: 0.7rem;">About Approvals History</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <x-alert-light class="mb-2 py-1 px-2 rounded-sm">

                        <div class="d-flex align-items-start gap-1">
                            <i class="ti ti-checklist text-primary mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">What is this page?</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">This page shows all leave requests where you were the approver - whether you approved or rejected them.</small>
                            </div>
                        
</x-alert-light>
                    </div>
                    
                    <x-alert-light class="mb-2 py-1 px-2 rounded-sm">

                        <div class="d-flex align-items-start gap-1">
                            <i class="ti ti-eye text-success mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">View Details</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">Click the "View" button to see complete leave request details, approval timeline, and the official letter.</small>
                            </div>
                        
</x-alert-light>
                    </div>
                    
                    <x-alert-light class="mb-0 py-1 px-2 rounded-sm">

                        <div class="d-flex align-items-start gap-1">
                            <i class="ti ti-file-text text-warning mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">Official Letter</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">Approved leave requests include an official letter that can be printed or downloaded as PDF.</small>
                            </div>
                        
</x-alert-light>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Custom Pagination Styles */
.custom-pagination {
    margin: 0;
}
.pagination-links {
    display: flex;
    gap: 0.25rem;
    align-items: center;
}
.pagination-item {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 28px;
    height: 28px;
    padding: 0 0.4rem;
    font-size: 0.65rem;
    color: #475569;
    background: white;
    border: 1px solid #E8E2D5;
    border-radius: 4px;
    text-decoration: none;
    transition: all 0.2s ease;
}
.pagination-item:hover:not(.disabled):not(.active) {
    background: #F1F5F9;
    border-color: #CBD5E1;
    color: #1E293B;
}
.pagination-item.active {
    background: #3B82F6;
    border-color: #3B82F6;
    color: white;
}
.pagination-item.disabled {
    opacity: 0.5;
    cursor: not-allowed;
    background: #F8FAFC;
}
.pagination-item.dots {
    border: none;
    background: transparent;
    cursor: default;
}
.custom-select-sm {
    font-size: 0.65rem;
    padding: 0.2rem 1.2rem 0.2rem 0.4rem;
    border: 1px solid #E8E2D5;
    border-radius: 4px;
    background: white;
    cursor: pointer;
}
.custom-select-sm:focus {
    outline: none;
    border-color: #3B82F6;
}
</style>

<script>
// Per Page Change Handler
const perPageSelect = document.getElementById('perPageSelect');
if (perPageSelect) {
    perPageSelect.addEventListener('change', function() {
        const url = new URL(window.location.href);
        url.searchParams.set('per_page', this.value);
        url.searchParams.delete('page');
        window.location.href = url.toString();
    });
}

document.addEventListener('DOMContentLoaded', function () {
    initLiveSearch({
        url: '{{ route('leave-requests.approvals-history') }}',
        resultsSelector: '#leaveApprovalsResults',
        statsSelector: '#leaveApprovalsSearchStats',
        searchInputSelector: '#leaveApprovalsSearchInput',
        suggestionsSelector: '#leaveApprovalsSearchInputSuggestions',
        recentSearchesKey: 'leaveApprovalsRecentSearches',
        statLabel: 'records',
        perPage: 10,
        filterSelectors: ['#categoryFilter', '#dateFromFilter', '#dateToFilter'],
        extraParams: () => ({
            action: document.getElementById('categoryFilter')?.value || '',
            date_from: document.getElementById('dateFromFilter')?.value || '',
            date_to: document.getElementById('dateToFilter')?.value || '',
        }),
    });
});
</script>
@endsection