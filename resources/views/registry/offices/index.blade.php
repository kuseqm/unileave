@extends('layouts.master')

@section('title', 'Manage Offices')

@section('content')
<div class="container-fluid px-2">

    <!-- Breadcrumb -->
    <x-breadcrumb :items="[
        ['name' => 'Central Registry', 'url' => '#'],
        ['name' => 'Offices', 'url' => '#']
    ]" />

    <!-- Page Header -->
    <x-page-header title="MANAGE OFFICES" description="Offices that route and receive correspondence">
        <a href="{{ route('registry.offices.create') }}" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
            <i class="ti ti-plus me-1"></i> New Office
        </a>
    </x-page-header>

    <!-- Search Card -->
    <x-search-card
        placeholder="Search by name or short name..."
        id="officesSearchInput"
        name="search"
        :value="request('search')"
        :showStatusFilter="true"
        :statusOptions="['active' => 'Active', 'inactive' => 'Inactive']"
    >
        <div id="officesSearchStats" class="text-muted" style="font-size: 0.65rem;">
            {{ $offices->total() }} offices total
        </div>
    </x-search-card>

    <div class="row g-2">
        <!-- Offices List - main column -->
        <div class="col-lg-8">
            <div class="card rounded-sm">
                <div id="officesResults">
                    @include('registry.offices._results')
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
                        <div class="fw-semibold" style="font-size: 0.7rem;"><i class="ti ti-flag me-1 text-primary"></i> About Offices</div>
                        <ul class="mb-0 ps-3" style="font-size: 0.65rem; color: #555;">
                            <li>An office can be a central unit or tied to a specific department</li>
                            <li>Only offices marked to receive letters appear as an addressee option</li>
                            <li><span class="badge bg-secondary-subtle text-secondary rounded-sm">Inactive</span> offices are hidden from new letter forms</li>
                        </ul>
                    </div>

                    <div class="mb-2">
                        <div class="fw-semibold" style="font-size: 0.7rem;"><i class="ti ti-link me-1 text-primary"></i> Quick Links</div>
                        <ul class="mb-0 ps-3" style="font-size: 0.65rem; color: #555;">
                            <li><a href="{{ route('registry.offices.create') }}">New Office</a> - add a routing destination</li>
                            <li><a href="{{ route('registry.incoming.index') }}">Incoming Letters</a> - see letters by office</li>
                        </ul>
                    </div>

                    <x-alert-light class="mb-0 py-1 px-2 rounded-sm">
                        <div class="d-flex align-items-start gap-1">
                            <i class="ti ti-tips text-warning mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">Tips</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">&bull; Set a Head of Office so assignment notifications reach the right person</small><br>
                                <small class="text-muted" style="font-size: 0.6rem;">&bull; Sort order controls how offices are listed in dropdowns</small>
                            </div>
                        </div>
                    </x-alert-light>
                </div>
            </div>
        </div>
    </div>
</div>

<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<script>
function deleteOffice(id) {
    confirmAction('Are you sure you want to delete this office?', function () {
        const form = document.getElementById('deleteForm');
        form.action = `{{ url('registry/offices') }}/${id}`;
        form.submit();
    });
}

document.addEventListener('DOMContentLoaded', function () {
    initLiveSearch({
        url: '{{ route('registry.offices.index') }}',
        resultsSelector: '#officesResults',
        statsSelector: '#officesSearchStats',
        searchInputSelector: '#officesSearchInput',
        suggestionsSelector: '#officesSearchInputSuggestions',
        recentSearchesKey: 'officesRecentSearches',
        statLabel: 'offices',
        perPage: 20,
        filterSelectors: ['#statusFilter'],
        extraParams: () => ({
            status: document.getElementById('statusFilter')?.value || '',
        }),
    });
});
</script>
@endsection
