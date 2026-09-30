@extends('layouts.master')

@section('title', 'Bulk Cleanup')

@section('content')
<div class="container-fluid px-2">

    <x-breadcrumb :items="[
        ['name' => 'Bulk Cleanup', 'url' => '#']
    ]" />

    <x-page-header title="BULK CLEANUP" description="Permanently delete Leave, Permission, or Travel requests in one pass - e.g. wiping demo/test data after a presentation">
    </x-page-header>

    <x-alert-success />
    <x-alert-danger />

    <x-alert-warning>
        <strong>This permanently deletes records.</strong> There is no undo. Double-check the list below before confirming.
    </x-alert-warning>

    <!-- Stats -->
    <div class="row g-2 mb-3">
        <x-stats-card value="{{ $counts['leave'] }}" label="Leave" subtext="total records" color="primary" icon="calendar" />
        <x-stats-card value="{{ $counts['permission'] }}" label="Permission" subtext="total records" color="warning" icon="clock" />
        <x-stats-card value="{{ $counts['travel'] }}" label="Travel" subtext="total records" color="secondary" icon="plane" />
        <x-stats-card value="{{ $counts['total'] }}" label="Total" subtext="all records" color="dark" icon="stack" />
    </div>

    <!-- Search -->
    <x-search-card
        placeholder="Search (reason / destination)..."
        id="cleanupSearchInput"
        name="search"
        :value="$search"
    >
        <div id="cleanupStats" class="text-muted" style="font-size: 0.65rem;">
            {{ $items->count() }} matching record(s)
        </div>
    </x-search-card>

    <div class="row g-2">
        <!-- Filters + results - main column -->
        <div class="col-lg-8">
            <!-- Filters -->
            <div class="card rounded-sm mb-2">
                <div class="card-body py-2 px-3">
                    <div class="row g-2 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label" style="font-size: 0.65rem;">Module</label>
                            <select id="moduleFilter" class="form-select form-select-sm rounded-sm" style="font-size: 0.7rem;">
                                <option value="" {{ ($module === '' || $module === 'all') ? 'selected' : '' }}>All</option>
                                <option value="leave" {{ $module === 'leave' ? 'selected' : '' }}>Leave</option>
                                <option value="permission" {{ $module === 'permission' ? 'selected' : '' }}>Permission</option>
                                <option value="travel" {{ $module === 'travel' ? 'selected' : '' }}>Travel</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" style="font-size: 0.65rem;">From (created at)</label>
                            <input type="datetime-local" id="dateFromFilter" value="{{ $from }}" class="form-control form-control-sm rounded-sm" style="font-size: 0.7rem;">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" style="font-size: 0.65rem;">To (created at)</label>
                            <input type="datetime-local" id="dateToFilter" value="{{ $to }}" class="form-control form-control-sm rounded-sm" style="font-size: 0.7rem;">
                        </div>
                        <div class="col-md-3 d-flex gap-1 align-items-end">
                            <button type="button" id="applyFilters" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem;">
                                <i class="ti ti-filter me-1"></i> Apply
                            </button>
                            <button type="button" id="resetFiltersBtn" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.7rem;">
                                Reset
                            </button>
                        </div>
                    </div>
                    <div class="d-flex gap-1 mt-2">
                        <button type="button" id="demoSeededLeave" class="btn btn-outline-warning btn-sm rounded-sm" style="font-size: 0.7rem;">
                            <i class="ti ti-sparkles me-1"></i> Show demo-seeded leave
                        </button>
                    </div>
                </div>
            </div>

            <form id="cleanupForm" action="{{ route('admin.cleanup.destroy') }}" method="POST" onsubmit="return confirmBulkDelete(event);">
                @csrf
                <div class="card rounded-sm">
                    <div class="card-header py-1 px-2 bg-light d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <i class="ti ti-trash text-danger me-1" style="font-size: 0.8rem;"></i>
                            <span style="font-size: 0.7rem; font-weight: 600;">Matching records</span>
                        </div>
                        <button type="submit" class="btn btn-danger btn-sm rounded-sm" style="font-size: 0.7rem;" id="deleteBtn" disabled>
                            <i class="ti ti-trash me-1"></i> Delete Selected (<span id="selectedCount">0</span>)
                        </button>
                    </div>
                    <div class="card-body p-0" id="cleanupResults">
                        @include('admin.cleanup._results')
                    </div>
                </div>
            </form>
        </div>

        <!-- Information - narrow column -->
        <div class="col-lg-4">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-danger-subtle">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-alert-triangle text-danger me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Before You Delete</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <div class="mb-2">
                        <small class="text-muted" style="font-size: 0.65rem;">This tool permanently removes Leave, Permission, or Travel requests and everything attached to them (letters, documents, approval history). There is no undo and no recovery.</small>
                    </div>

                    <x-alert-light class="mb-0 py-1 px-2 rounded-sm">

                        <div class="d-flex align-items-start gap-1">
                            <i class="ti ti-tips text-warning mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">Tips</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">&bull; Filter to a narrow date range before selecting "all" to avoid deleting the wrong records</small><br>
                                <small class="text-muted" style="font-size: 0.6rem;">&bull; "Show demo-seeded leave" isolates test data safe to remove after a demo</small>
                            </div>

</x-alert-light>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Delete Confirmation Modal -->
<div class="modal fade" id="bulkDeleteConfirmModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-sm">
            <div class="modal-header py-1 px-2 bg-danger-subtle">
                <h5 class="modal-title" style="font-size: 0.8rem;">
                    <i class="ti ti-alert-triangle text-danger me-1"></i> Confirm Permanent Delete
                </h5>
                <button type="button" class="btn-close py-0" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body py-2 px-2">
                <p style="font-size: 0.75rem;">Permanently delete <strong><span id="bulkDeleteCount">0</span> record(s)</strong>?</p>
                <x-alert-danger :session="false">
    This cannot be undone.
</x-alert-danger>
            </div>
            <div class="modal-footer py-1 px-2">
                <button type="button" class="btn btn-secondary btn-sm rounded-sm" data-bs-dismiss="modal" style="font-size: 0.7rem;">
                    <i class="ti ti-x me-1"></i> Cancel
                </button>
                <button type="button" class="btn btn-danger btn-sm rounded-sm" style="font-size: 0.7rem;" id="bulkDeleteConfirmBtn">
                    <i class="ti ti-trash me-1"></i> Delete
                </button>
            </div>
        </div>
    </div>
</div>

<script>
const deleteBtn = document.getElementById('deleteBtn');
const selectedCount = document.getElementById('selectedCount');
const cleanupForm = document.getElementById('cleanupForm');

function updateState() {
    const checked = document.querySelectorAll('.row-check:checked').length;
    selectedCount.textContent = checked;
    deleteBtn.disabled = checked === 0;
}

document.addEventListener('change', function (e) {
    if (e.target.id === 'selectAll') {
        document.querySelectorAll('.row-check').forEach(cb => cb.checked = e.target.checked);
        updateState();
    }
    if (e.target.matches('.row-check')) {
        updateState();
    }
});

function confirmBulkDelete(event) {
    event.preventDefault();
    const checked = document.querySelectorAll('.row-check:checked').length;
    document.getElementById('bulkDeleteCount').textContent = checked;
    new bootstrap.Modal(document.getElementById('bulkDeleteConfirmModal')).show();
    return false;
}

document.getElementById('bulkDeleteConfirmBtn').addEventListener('click', function () {
    bootstrap.Modal.getInstance(document.getElementById('bulkDeleteConfirmModal'))?.hide();
    cleanupForm.submit();
});

const liveSearch = initLiveSearch({
    url: '{{ route('admin.cleanup.index') }}',
    resultsSelector: '#cleanupResults',
    searchInputSelector: '#cleanupSearchInput',
    statsSelector: '#cleanupStats',
    statLabel: 'matching record(s)',
    filterSelectors: ['#moduleFilter', '#dateFromFilter', '#dateToFilter'],
    extraParams: () => ({
        module: document.getElementById('moduleFilter').value || 'all',
        from: document.getElementById('dateFromFilter').value,
        to: document.getElementById('dateToFilter').value,
    }),
    onUpdate: function () {
        updateState();
    },
});

document.getElementById('applyFilters')?.addEventListener('click', () => liveSearch.load(1));

document.getElementById('resetFiltersBtn')?.addEventListener('click', function () {
    document.getElementById('moduleFilter').value = '';
    document.getElementById('dateFromFilter').value = '';
    document.getElementById('dateToFilter').value = '';
    document.getElementById('cleanupSearchInput').value = '';
    liveSearch.load(1);
});

document.getElementById('demoSeededLeave')?.addEventListener('click', function () {
    document.getElementById('moduleFilter').value = 'leave';
    document.getElementById('cleanupSearchInput').value = 'Demo seed';
    document.getElementById('dateFromFilter').value = '';
    document.getElementById('dateToFilter').value = '';
    liveSearch.load(1);
});
</script>
@endsection
