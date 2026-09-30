@extends('layouts.master')

@section('title', 'Message Directory')

@section('content')
<div class="container-fluid px-2">
    
    <!-- Breadcrumb -->
    <x-breadcrumb :items="[
        ['name' => 'Messages', 'url' => route('messages.inbox')],
        ['name' => 'Message Directory', 'url' => '#']

    ]" />
    
    <!-- Page Header -->
    <x-page-header title="MESSAGE DIRECTORY" description="Message metadata for audit & compliance (7-year retention)">
        <a href="{{ route('messages.admin-list') }}" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
            <i class="ti ti-refresh me-1"></i> Refresh
        </a>
    </x-page-header>

    <!-- Stats Cards Row - Using your x-stats-card component -->
    <div class="row g-2 mb-3">
        <x-stats-card value="{{ $messages->total() }}" label="Total Messages" subtext="all messages" />
        <x-stats-card value="{{ $messages->whereNull('deleted_at')->count() }}" label="Active" subtext="active messages" color="success" />
        <x-stats-card value="{{ $messages->whereNotNull('deleted_at')->count() }}" label="Soft Deleted" subtext="awaiting 7-year retention" color="warning" />
        <x-stats-card value="{{ ucfirst($role) }}" label="Your Role" subtext="access level" color="info" />
    </div>

    <x-search-card
        placeholder="Search by subject or sender..."
        :showTypeFilter="true"
        typeLabel="Type"
        :typeOptions="['general' => 'General', 'approval' => 'Approval', 'task' => 'Task', 'minute' => 'Minute']"
        :showStatusFilter="true"
        statusLabel="Status"
        :statusOptions="['active' => 'Active', 'deleted' => 'Deleted (Soft)', 'archived' => 'Archived']"
    >
        <div id="messageAdminSearchStats" class="text-muted" style="font-size: 0.65rem;">
            {{ $messages->total() }} messages
        </div>
    </x-search-card>

    <div class="row g-3">
        <!-- LEFT COLUMN: Message Directory Table (70%) -->
        <div class="col-lg-8">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <i class="ti ti-database text-primary me-1" style="font-size: 0.8rem;"></i>
                            <span style="font-size: 0.7rem; font-weight: 600;">Message Directory</span>
                            <span class="text-muted ms-2 small" style="font-size: 0.6rem;">(Content encrypted - view requires VC approval)</span>
                        </div>
                        <div class="text-muted small" style="font-size: 0.6rem;">
                            Showing {{ $messages->firstItem() ?? 0 }} to {{ $messages->lastItem() ?? 0 }} of {{ $messages->total() }}
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0" style="font-size: 0.75rem;" id="messageTable">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-2 py-1" width="50">#</th>
                                    <th class="py-1">From</th>
                                    <th class="py-1">To</th>
                                    <th class="py-1">Subject</th>
                                    <th class="py-1">Priority</th>
                                    <th class="py-1">Status</th>
                                    <th class="pe-2 py-1" width="100">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($messages as $index => $message)
                                <tr data-type="{{ $message->message_type }}" 
                                    data-priority="{{ $message->priority }}"
                                    data-status="{{ $message->trashed() ? 'deleted' : ($message->is_archived ? 'archived' : 'active') }}"
                                    data-subject="{{ strtolower($message->subject) }}"
                                    data-sender="{{ strtolower($message->sender->first_name ?? '') }}">
                                    <td class="ps-2 py-1">{{ $messages->firstItem() + $index }}</td>
                                    <td class="py-1">
                                        {{ $message->sender->first_name ?? 'System' }} {{ $message->sender->last_name ?? '' }}
                                        <div class="text-muted" style="font-size: 0.6rem;">{{ $message->sender->email ?? '' }}</div>
                                    </td>
                                    <td class="py-1">
                                        @if($message->recipient_names)
                                            <span title="{{ $message->recipient_names }}">{{ Str::limit($message->recipient_names, 30) }}</span>
                                        @else
                                            {{ $message->receiver->first_name ?? 'Unknown' }}
                                        @endif
                                    </td>
                                    <td class="py-1">
                                        {{ Str::limit($message->subject ?? 'No subject', 50) }}
                                        @if($message->trashed())
                                            <span class="badge bg-warning ms-1" style="font-size: 0.6rem;">Deleted</span>
                                        @endif
                                    </td>
                                    <td class="py-1">{!! $message->priority_badge !!}</td>
                                    <td class="py-1">{!! $message->status_badge !!}</td>
                                    <td class="pe-2 py-1">
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('messages.metadata', $message->id) }}" class="btn btn-sm btn-outline-info rounded-sm" style="font-size: 0.6rem; padding: 0.15rem 0.4rem;" title="View Metadata & Audit">
                                                <i class="ti ti-history"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-3 text-muted" style="font-size: 0.7rem;">
                                        <i class="ti ti-inbox" style="font-size: 48px;"></i>
                                        <p class="mt-2">No messages found</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer py-1 px-2 bg-light">
                    <div class="d-flex justify-content-end">
                        {{ $messages->links() }}
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN: Explanation Panel (30%) -->
        <div class="col-lg-4">
            <div class="card rounded-sm border-0 shadow-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-info-circle text-primary me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">About Message Directory</span>
                    </div>
                </div>
                <div class="card-body p-2">
                    <div class="mb-2">
                        <i class="ti ti-database text-primary me-1" style="font-size: 0.7rem;"></i>
                        <strong class="small" style="font-size: 0.7rem;">What is this page?</strong>
                        <p class="text-muted mt-1 mb-0" style="font-size: 0.65rem;">This is the central message directory for audit and compliance purposes. All message metadata is stored for 7 years as required by law.</p>
                    </div>
                    
                    <div class="mb-2">
                        <i class="ti ti-lock text-warning me-1" style="font-size: 0.7rem;"></i>
                        <strong class="small" style="font-size: 0.7rem;">Content Privacy</strong>
                        <p class="text-muted mt-1 mb-0" style="font-size: 0.65rem;">Message content is encrypted. Only the sender and receiver can view content. Others must request access through VC approval.</p>
                    </div>
                    
                    <div class="mb-2">
                        <i class="ti ti-key text-info me-1" style="font-size: 0.7rem;"></i>
                        <strong class="small" style="font-size: 0.7rem;">Access Request Process</strong>
                        <p class="text-muted mt-1 mb-0" style="font-size: 0.65rem;">Click the <i class="ti ti-history"></i> icon to view message details. From there, HR can request access to content.</p>
                    </div>
                    
                    <x-alert-info>
    <strong>Tip:</strong> Click the <i class="ti ti-history"></i> icon to view full metadata, audit trail, and access request options.
</x-alert-info>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Filters only - no restore or request access functions here anymore
const typeFilter = document.getElementById('typeFilter');
const statusFilter = document.getElementById('statusFilter');
const searchInput = document.getElementById('searchInput');
const resetFilters = document.getElementById('resetFilters');
const rows = document.querySelectorAll('#messageTable tbody tr');

function filterTable() {
    const type = typeFilter.value;
    const status = statusFilter.value;
    const search = searchInput.value.toLowerCase();
    
    rows.forEach(row => {
        let show = true;
        if (type !== '' && row.dataset.type !== type) show = false;
        if (status !== '' && row.dataset.status !== status) show = false;
        if (search && !row.dataset.subject.includes(search) && !row.dataset.sender.includes(search)) show = false;
        row.style.display = show ? '' : 'none';
    });
}

typeFilter.addEventListener('change', filterTable);
statusFilter.addEventListener('change', filterTable);
searchInput.addEventListener('keyup', filterTable);

if (resetFilters) {
    resetFilters.addEventListener('click', function () {
        if (typeFilter) typeFilter.value = '';
        if (statusFilter) statusFilter.value = '';
        if (searchInput) searchInput.value = '';
        filterTable();
    });
}
</script>
@endsection