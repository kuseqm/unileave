@extends('layouts.master')

@section('title', 'Draft Messages')

@section('content')
<div class="container-fluid px-2">
    <!-- Breadcrumb -->
    <x-breadcrumb :items="[
        ['name' => 'Messages', 'url' => '#'],
        ['name' => 'Draft', 'url' => '#']
    ]" />
    <x-page-header title="DRAFTS" description="Saved draft messages">
        <a href="{{ route('messages.create') }}" class="btn btn-primary btn-sm"><i class="ti ti-plus me-1"></i> Compose</a>
    </x-page-header>

    <!-- Search Card -->
    <x-search-card placeholder="Search by subject or message..." id="draftsSearchInput" name="search" :value="request('search')">
        <div id="draftsSearchStats" class="text-muted" style="font-size: 0.65rem;">
            {{ $messages->total() }} drafts total
        </div>
    </x-search-card>

    <div class="row g-2">
        <!-- Drafts Table - main column -->
        <div class="col-lg-8">
            <div class="card rounded-sm">
                <div id="draftsResults">
                    @include('shared.messages._drafts-results')
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
                        <div class="fw-semibold" style="font-size: 0.7rem;"><i class="ti ti-link me-1 text-primary"></i> Quick Links</div>
                        <ul class="mb-0 ps-3" style="font-size: 0.65rem; color: #555;">
                            <li><a href="{{ route('messages.inbox') }}">Inbox</a> - messages you've received</li>
                            <li><a href="{{ route('messages.create') }}">Compose</a> - send a new message</li>
                        </ul>
                    </div>

                    <x-alert-light class="mb-0 py-1 px-2 rounded-sm">
                        <div class="d-flex align-items-start gap-1">
                            <i class="ti ti-tips text-warning mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">Tips</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">&bull; A draft is not sent until you submit it</small>
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
        url: '{{ route('messages.drafts') }}',
        resultsSelector: '#draftsResults',
        statsSelector: '#draftsSearchStats',
        searchInputSelector: '#draftsSearchInput',
        suggestionsSelector: '#draftsSearchInputSuggestions',
        recentSearchesKey: 'draftsRecentSearches',
        statLabel: 'drafts',
        perPage: 20,
    });
});
</script>
@endsection