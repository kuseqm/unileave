@extends('layouts.master')

@section('title', 'Prepare Leave Documents')

@section('content')
<div class="container-fluid px-2">
    <x-breadcrumb :items="[
        ['name' => 'Dashboard', 'url' => route('dashboard')],
        ['name' => 'Prepare Leave Documents', 'url' => route('admin.leave-documents.index')]
    ]" />

    <x-page-header title="PREPARE LEAVE DOCUMENTS" description="Generate and manage formal leave approval letters.">
    </x-page-header>

    <x-alert-success />
    <x-alert-danger />

    <x-search-card
        placeholder="Search by staff name or ID..."
        id="leaveDocumentsSearchInput"
        name="search"
        :value="request('search')"
    >
        <div id="leaveDocumentsSearchStats" class="text-muted" style="font-size: 0.65rem;">
            {{ $leaveRequests->total() }} leave requests
        </div>
    </x-search-card>

    <div class="row g-2">
        <!-- Leave Document Prep List - main column -->
        <div class="col-lg-8">
            <div class="card border-0 rounded-sm shadow-sm">
                <div class="card-body p-2" id="leaveDocumentsResults">
                    @include('admin.leave-documents._results')
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
                        <div class="fw-semibold" style="font-size: 0.7rem;"><i class="ti ti-file-text me-1 text-primary"></i> About Leave Documents</div>
                        <p class="mb-0" style="font-size: 0.65rem; color: #555;">Once a leave request is fully approved, its formal approval letter can be prepared and generated here for the staff member's records.</p>
                    </div>

                    <div class="mb-2">
                        <div class="fw-semibold" style="font-size: 0.7rem;"><i class="ti ti-link me-1 text-primary"></i> Quick Links</div>
                        <ul class="mb-0 ps-3" style="font-size: 0.65rem; color: #555;">
                            <li><a href="{{ route('leave-requests.my-requests') }}">Leave Requests</a> - view underlying requests</li>
                        </ul>
                    </div>

                    <x-alert-light class="mb-0 py-1 px-2 rounded-sm">
                        <div class="d-flex align-items-start gap-1">
                            <i class="ti ti-tips text-warning mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">Tips</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">&bull; Only fully approved requests can have a letter prepared</small><br>
                                <small class="text-muted" style="font-size: 0.6rem;">&bull; Search by staff name or ID to find a specific request fast</small>
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
        url: '{{ route('admin.leave-documents.index') }}',
        resultsSelector: '#leaveDocumentsResults',
        statsSelector: '#leaveDocumentsSearchStats',
        searchInputSelector: '#leaveDocumentsSearchInput',
        suggestionsSelector: '#leaveDocumentsSearchInputSuggestions',
        recentSearchesKey: 'leaveDocumentsRecentSearches',
        statLabel: 'leave requests',
        perPage: 20,
    });
});
</script>
@endsection
