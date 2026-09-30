@extends('layouts.master')

@section('title', 'Letter Files')

@section('content')
<div class="container-fluid px-2">
    
    <!-- Breadcrumb -->
    <x-breadcrumb :items="[
        ['name' => 'Registry', 'url' => '#'],
        ['name' => 'Letter Files', 'url' => '#']
    ]" />
    
    <!-- Page Header -->
    <x-page-header title="LETTER FILES" description="Manage physical letter files and volumes">
        <a href="{{ route('registry.files.create') }}" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
            <i class="ti ti-plus me-1"></i> Add File
        </a>
    </x-page-header>
    
    <!-- Stats Cards -->
    @php
        $totalFiles = \App\Models\LetterFile::count();
        $activeFiles = \App\Models\LetterFile::active()->count();
        $fullFiles = \App\Models\LetterFile::where('is_active', true)->get()->filter(function($f) { return $f->isFull(); })->count();
        $totalLetters = \App\Models\Letter::count();
    @endphp
    
    <div class="row g-2 mb-3">
        <x-stats-card value="{{ $totalFiles }}" label="Total Files" subtext="all volumes" color="primary" icon="folder" />
        <x-stats-card value="{{ $activeFiles }}" label="Active Files" subtext="accepting letters" color="success" icon="folder-open" />
        <x-stats-card value="{{ $fullFiles }}" label="Full Files" subtext="at capacity" color="warning" icon="folder-filled" />
        <x-stats-card value="{{ number_format($totalLetters) }}" label="Total Letters" subtext="across all files" color="info" icon="files" />
    </div>
    
    <!-- Search Card -->
    <x-search-card
        placeholder="Search by file number or name..."
        id="filesSearchInput"
        name="search"
        :value="request('search')"
        :showStatusFilter="true"
        :showCategoryFilter="true"
        categoryLabel="Format"
        :statusOptions="['active' => 'Active', 'closed' => 'Closed']"
        :categoryOptions="$formats->pluck('name', 'id')"
    >
        <div id="filesSearchStats" class="text-muted" style="font-size: 0.65rem;">
            {{ $files->total() }} files total
        </div>
    </x-search-card>

    <div class="row g-2">
        <!-- Letter Files List - main column -->
        <div class="col-lg-8">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-files text-primary me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Letter Files List</span>
                    </div>
                </div>
                <div id="filesResults">
                    @include('registry.files._results')
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
                        <div class="fw-semibold" style="font-size: 0.7rem;"><i class="ti ti-flag me-1 text-primary"></i> File Status</div>
                        <ul class="mb-0 ps-3" style="font-size: 0.65rem; color: #555;">
                            <li><span class="badge bg-success-subtle text-success rounded-sm">Active</span> - still accepting letters</li>
                            <li><span class="badge bg-warning-subtle text-warning rounded-sm">Full</span> - reached its capacity</li>
                            <li><span class="badge bg-secondary-subtle text-secondary rounded-sm">Closed</span> - no longer receives letters</li>
                        </ul>
                    </div>

                    <div class="mb-2">
                        <div class="fw-semibold" style="font-size: 0.7rem;"><i class="ti ti-link me-1 text-primary"></i> Quick Links</div>
                        <ul class="mb-0 ps-3" style="font-size: 0.65rem; color: #555;">
                            <li><a href="{{ route('registry.files.create') }}">Add File</a> - open a new volume</li>
                            <li><a href="{{ route('registry.formats.index') }}">Reference Formats</a> - manage numbering formats</li>
                        </ul>
                    </div>

                    <x-alert-light class="mb-0 py-1 px-2 rounded-sm">
                        <div class="d-flex align-items-start gap-1">
                            <i class="ti ti-tips text-warning mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">Tips</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">&bull; A full file must be closed before opening a new volume</small><br>
                                <small class="text-muted" style="font-size: 0.6rem;">&bull; Closed files can be reopened if more letters need to be added</small>
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
        url: '{{ route('registry.files.index') }}',
        resultsSelector: '#filesResults',
        statsSelector: '#filesSearchStats',
        searchInputSelector: '#filesSearchInput',
        suggestionsSelector: '#filesSearchInputSuggestions',
        recentSearchesKey: 'filesRecentSearches',
        statLabel: 'files',
        perPage: 15,
        filterSelectors: ['#statusFilter', '#categoryFilter'],
        extraParams: () => ({
            status: document.getElementById('statusFilter')?.value || '',
            format_id: document.getElementById('categoryFilter')?.value || '',
        }),
    });
});
</script>

<style>
.clickable-row {
    transition: background 0.2s ease;
}
.clickable-row:hover {
    background-color: #F8FAFC !important;
}
.hover-link:hover {
    text-decoration: underline;
    color: #3B82F6 !important;
}
.progress {
    background-color: #E2E8F0;
    border-radius: 4px;
    overflow: hidden;
}
.progress-bar {
    transition: width 0.3s ease;
}
</style>

<!-- Close File Confirmation Modal -->
<div class="modal fade" id="closeFileModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-sm">
            <div class="modal-header py-1 px-2 bg-warning-subtle">
                <h5 class="modal-title" style="font-size: 0.8rem;">
                    <i class="ti ti-alert-triangle text-warning me-1"></i> Close File
                </h5>
                <button type="button" class="btn-close py-0" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body py-2 px-2">
                <p style="font-size: 0.75rem;">Are you sure you want to close <strong id="closeFileName"></strong>?</p>
                <x-alert-warning>
    <strong>Warning:</strong> Once closed, this file will no longer accept new letters.
</x-alert-warning>
                <x-alert-info>
    You can reopen the file later if needed.
</x-alert-info>
            </div>
            <div class="modal-footer py-1 px-2">
                <button type="button" class="btn btn-secondary btn-sm rounded-sm" data-bs-dismiss="modal" style="font-size: 0.7rem;">Cancel</button>
                <form action="" id="closeFileForm" method="POST" class="d-inline">
                    @csrf
                    @method('PUT')
                    <button type="submit" class="btn btn-warning btn-sm rounded-sm" style="font-size: 0.7rem;">
                        <i class="ti ti-lock me-1"></i> Close File
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Reopen File Confirmation Modal -->
<div class="modal fade" id="reopenFileModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-sm">
            <div class="modal-header py-1 px-2 bg-success-subtle">
                <h5 class="modal-title" style="font-size: 0.8rem;">
                    <i class="ti ti-alert-triangle text-success me-1"></i> Reopen File
                </h5>
                <button type="button" class="btn-close py-0" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body py-2 px-2">
                <p style="font-size: 0.75rem;">Are you sure you want to reopen <strong id="reopenFileName"></strong>?</p>
                <x-alert-success>
    The file will be active and can accept new letters again.
</x-alert-success>
            </div>
            <div class="modal-footer py-1 px-2">
                <button type="button" class="btn btn-secondary btn-sm rounded-sm" data-bs-dismiss="modal" style="font-size: 0.7rem;">Cancel</button>
                <form action="" id="reopenFileForm" method="POST" class="d-inline">
                    @csrf
                    @method('PUT')
                    <button type="submit" class="btn btn-success btn-sm rounded-sm" style="font-size: 0.7rem;">
                        <i class="ti ti-lock-open me-1"></i> Reopen File
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function showCloseFileModal(fileId, fileName, closeRoute) {
        document.getElementById('closeFileName').innerText = fileName;
        document.getElementById('closeFileForm').action = closeRoute;
        new bootstrap.Modal(document.getElementById('closeFileModal')).show();
    }
    
    function showReopenFileModal(fileId, fileName, reopenRoute) {
        document.getElementById('reopenFileName').innerText = fileName;
        document.getElementById('reopenFileForm').action = reopenRoute;
        new bootstrap.Modal(document.getElementById('reopenFileModal')).show();
    }
</script>
@endsection