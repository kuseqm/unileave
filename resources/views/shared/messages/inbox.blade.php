@extends('layouts.master')

@section('title', 'Inbox - Messages')

@section('content')
<div class="container-fluid px-2">
    <!-- Breadcrumb -->
    <x-breadcrumb :items="[
        ['name' => 'Messages', 'url' => '#'],
        ['name' => 'Inbox', 'url' => '#']
    ]" />

    <!-- Page Header -->
    <x-page-header title="INBOX" description="Your received messages">
        <a href="{{ route('messages.create') }}" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
            <i class="ti ti-plus me-1"></i> Compose
        </a>
    </x-page-header>

    <!-- Stats Cards - Using x-stats-card component -->
    <div class="row g-2 mb-3">
        <x-stats-card value="{{ $messages->total() }}" label="Total Messages" subtext="all messages" />
        <x-stats-card value="{{ $unreadCount }}" label="Unread" subtext="awaiting your attention" color="warning" />
        <x-stats-card value="{{ $messages->where('priority', 'urgent')->count() }}" label="Urgent" subtext="requires immediate action" color="danger" />
        <x-stats-card value="{{ $thisWeekCount }}" label="This Week" subtext="received this week" color="info" />
    </div>

    <!-- Search Card -->
    <x-search-card
        placeholder="Search by subject, message, or sender..."
        id="inboxSearchInput"
        name="search"
        :value="request('search')"
        :showCategoryFilter="true"
        :showTypeFilter="true"
        categoryLabel="Type"
        typeLabel="Priority"
        :categoryOptions="['general' => 'General', 'minute' => 'Minute', 'approval' => 'Approval', 'task' => 'Task', 'escalation' => 'Escalation', 'notification' => 'Notification']"
        :typeOptions="['low' => 'Low', 'normal' => 'Normal', 'high' => 'High', 'urgent' => 'Urgent']"
    >
        <div id="inboxSearchStats" class="text-muted" style="font-size: 0.65rem;">
            {{ $messages->total() }} messages total
        </div>
    </x-search-card>

    <div class="row g-2">
        <!-- Inbox Table - main column -->
        <div class="col-lg-8">
            <div class="card rounded-sm">
                <div id="inboxResults">
                    @include('shared.messages._inbox-results')
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
                        <div class="fw-semibold" style="font-size: 0.7rem;"><i class="ti ti-flag me-1 text-primary"></i> Understanding Priority</div>
                        <ul class="mb-0 ps-3" style="font-size: 0.65rem; color: #555;">
                            <li><span class="badge bg-danger-subtle text-danger rounded-sm">Urgent</span> - requires immediate action</li>
                            <li><span class="badge bg-warning-subtle text-warning rounded-sm">High</span> - important, act soon</li>
                            <li><span class="badge bg-secondary-subtle text-secondary rounded-sm">Normal/Low</span> - routine correspondence</li>
                        </ul>
                    </div>

                    <div class="mb-2">
                        <div class="fw-semibold" style="font-size: 0.7rem;"><i class="ti ti-link me-1 text-primary"></i> Quick Links</div>
                        <ul class="mb-0 ps-3" style="font-size: 0.65rem; color: #555;">
                            <li><a href="{{ route('messages.create') }}">Compose</a> - send a new message</li>
                            <li><a href="{{ route('messages.sent') }}">Sent</a> - messages you've sent</li>
                            <li><a href="{{ route('messages.drafts') }}">Drafts</a> - saved but not yet sent</li>
                        </ul>
                    </div>

                    <x-alert-light class="mb-0 py-1 px-2 rounded-sm">
                        <div class="d-flex align-items-start gap-1">
                            <i class="ti ti-tips text-warning mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">Tips</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">&bull; Select multiple messages to bulk delete</small>
                            </div>
                        </div>
                    </x-alert-light>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Delete Confirmation Modal -->
<div class="modal fade" id="bulkDeleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-sm">
            <div class="modal-header py-1 px-2">
                <h5 class="modal-title" style="font-size: 0.8rem;">Confirm Bulk Delete</h5>
                <button type="button" class="btn-close py-0" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body py-2 px-2">
                <p style="font-size: 0.75rem;">Are you sure you want to delete <strong id="bulkDeleteCount"></strong> selected message(s)?</p>
                <p class="text-danger mb-0" style="font-size: 0.7rem;">This action cannot be undone.</p>
            </div>
            <div class="modal-footer py-1 px-2">
                <button type="button" class="btn btn-secondary btn-sm rounded-sm" data-bs-dismiss="modal" style="font-size: 0.7rem;">Cancel</button>
                <button type="button" id="bulkDeleteConfirmBtn" class="btn btn-danger btn-sm rounded-sm" style="font-size: 0.7rem;">Delete All</button>
            </div>
        </div>
    </div>
</div>

<script>

    function toggleBulkDeleteBtn() {
        const checked = document.querySelectorAll('#inboxResults .message-checkbox:checked').length > 0;
        const btn = document.getElementById('bulkDeleteBtn');
        if (btn) {
            btn.style.display = checked ? 'inline-block' : 'none';
        }
    }

    // Delegated listeners on the stable results container so they survive
    // AJAX innerHTML swaps from initLiveSearch (checkboxes/form are recreated each load).
    const inboxResults = document.getElementById('inboxResults');
    if (inboxResults) {
        inboxResults.addEventListener('change', function (e) {
            if (e.target.id === 'selectAll') {
                inboxResults.querySelectorAll('.message-checkbox').forEach(cb => cb.checked = e.target.checked);
                toggleBulkDeleteBtn();
            } else if (e.target.classList.contains('message-checkbox')) {
                toggleBulkDeleteBtn();
            }
        });

        inboxResults.addEventListener('click', function (e) {
            if (e.target.closest('#bulkDeleteBtn')) {
                const checkedCount = inboxResults.querySelectorAll('.message-checkbox:checked').length;
                if (checkedCount === 0) {
                    alert('Please select at least one message to delete.');
                    return;
                }
                document.getElementById('bulkDeleteCount').innerText = checkedCount;
                new bootstrap.Modal(document.getElementById('bulkDeleteModal')).show();
            }
        });
    }

    // Bulk Delete Confirmation (form is inside inboxResults, so submit via delegation-safe lookup)
    document.getElementById('bulkDeleteConfirmBtn')?.addEventListener('click', function () {
        document.getElementById('bulkDeleteForm')?.submit();
    });

    document.addEventListener('DOMContentLoaded', function () {
        initLiveSearch({
            url: '{{ route('messages.inbox') }}',
            resultsSelector: '#inboxResults',
            statsSelector: '#inboxSearchStats',
            searchInputSelector: '#inboxSearchInput',
            suggestionsSelector: '#inboxSearchInputSuggestions',
            recentSearchesKey: 'inboxRecentSearches',
            statLabel: 'messages',
            perPage: 20,
            filterSelectors: ['#categoryFilter', '#typeFilter'],
            extraParams: () => ({
                type: document.getElementById('categoryFilter')?.value || '',
                priority: document.getElementById('typeFilter')?.value || '',
            }),
        });
    });
</script>

<style>
/* Table hover effect */
.table-hover tbody tr:hover {
    background-color: #fef2e8;
    cursor: pointer;
}

/* Unread message styling */
tbody tr.fw-bold td:not(.text-muted) {
    font-weight: 600;
}

/* Compact table */
.table td, .table th {
    padding: 0.5rem 0.5rem;
    vertical-align: middle;
}
</style>
@endsection