@extends('layouts.master')

@section('title', 'Submit Leave on Behalf')

@section('content')
<div class="container-fluid px-2">
    <x-breadcrumb :items="[
        ['name' => 'Leave Requests', 'url' => route('leave-requests.my-requests')],
        ['name' => 'Submit on Behalf', 'url' => route('leave-requests.on-behalf')]
    ]" />

    <x-page-header title="SUBMIT LEAVE ON BEHALF" description="Select a staff member to apply leave for them.">
    </x-page-header>

    <x-alert-success />
    <x-alert-danger />

    <x-search-card
        placeholder="Search by name, staff ID or email..."
        id="onBehalfSearchInput"
        name="search"
        :value="request('search')"
    >
        <div id="onBehalfSearchStats" class="text-muted" style="font-size: 0.65rem;">
            {{ $staffMembers->total() }} staff total
        </div>
    </x-search-card>

    <div class="row g-2">
        <!-- Staff picker - main column -->
        <div class="col-lg-8">
            <div class="card border-0 rounded-sm shadow-sm">
                <div class="card-body p-2" id="onBehalfResults">
                    @include('shared.leave-requests._on-behalf-results')
                </div>
            </div>
        </div>

        <!-- Information - narrow column -->
        <div class="col-lg-4">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-info-circle text-info me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">How This Works</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <small class="text-muted d-block mb-2" style="font-size: 0.65rem;">Pick a staff member below, then fill in their leave request for them - useful when someone can't submit it themselves.</small>
                    <x-alert-light class="mb-0 py-1 px-2 rounded-sm">

                        <div class="d-flex align-items-start gap-1">
                            <i class="ti ti-tips text-warning mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">Note</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">The request still goes through the same approval chain as if they'd submitted it themselves.</small>
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
        url: '{{ route('leave-requests.on-behalf') }}',
        resultsSelector: '#onBehalfResults',
        statsSelector: '#onBehalfSearchStats',
        searchInputSelector: '#onBehalfSearchInput',
        suggestionsSelector: '#onBehalfSearchInputSuggestions',
        recentSearchesKey: 'onBehalfRecentSearches',
        statLabel: 'staff',
        perPage: 20,
    });
});
</script>
@endsection
