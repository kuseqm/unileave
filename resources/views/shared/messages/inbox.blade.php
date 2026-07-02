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
    </div>

    <div class="card rounded-sm">
        <div class="card-header py-1 px-2 bg-light">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="d-flex align-items-center">
                    <i class="ti ti-mail text-primary me-1" style="font-size: 0.8rem;"></i>
                    <span style="font-size: 0.7rem; font-weight: 600;">Messages</span>
                    @if($unreadCount > 0)
                        <span class="badge bg-danger ms-2" style="font-size: 0.6rem;">{{ $unreadCount }} unread</span>
                    @endif
                </div>
                <div>
                    <form method="GET" action="{{ route('messages.search') }}" class="d-flex">
                        <input type="text" name="query" class="form-control form-control-sm me-1" placeholder="Search messages..." style="width: 200px; font-size: 0.7rem;">
                        <button type="submit" class="btn btn-sm btn-outline-secondary" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
                            <i class="ti ti-search"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            @if($messages->count() > 0)
                <div class="table-responsive">
                    <form id="bulkDeleteForm" action="{{ route('messages.bulk-delete') }}" method="POST">
                        @csrf
                        <table class="table table-hover mb-0" style="font-size: 0.75rem;">
                            <thead class="bg-light">
                                <tr>
                                    <th width="30" class="ps-2 py-1">
                                        <input type="checkbox" id="selectAll">
                                    </th>
                                    <th class="py-1">From / To</th>
                                    <th class="py-1">Subject</th>
                                    <th class="py-1">Priority</th>
                                    <th class="py-1">Date</th>
                                    <th class="pe-2 py-1" width="120">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($messages as $message)
                                    <tr class="{{ !$message->is_read ? 'fw-bold' : '' }}">
                                        <td class="ps-2 py-1">
                                            <input type="checkbox" name="message_ids[]" value="{{ $message->id }}" class="message-checkbox">
                                        </td>
                                        <td class="py-1">
                                            @if($message->sender_id == auth()->id())
                                                {{-- Message sent by current user --}}
                                                @if($message->recipient_ids && count($message->recipient_ids) > 1)
                                                    <div class="d-flex align-items-center">
                                                        <i class="ti ti-users text-primary me-1" title="Sent to multiple recipients" style="font-size: 0.7rem;"></i>
                                                        <span>
                                                            To: {{ count($message->recipient_ids) }} recipients
                                                        </span>
                                                    </div>
                                                    <small class="text-muted" style="font-size: 0.6rem;">
                                                        {{ $message->recipient_names }}
                                                    </small>
                                                @else
                                                    <div class="d-flex align-items-center">
                                                        <i class="ti ti-send text-success me-1" title="Sent message" style="font-size: 0.7rem;"></i>
                                                        <span>
                                                            To: {{ $message->receiver->first_name ?? '' }} {{ $message->receiver->last_name ?? '' }}
                                                        </span>
                                                    </div>
                                                    @if($message->receiver)
                                                        <small class="text-muted" style="font-size: 0.6rem;">{{ $message->receiver->email ?? '' }}</small>
                                                    @endif
                                                @endif
                                            @else
                                                {{-- Message received by current user --}}
                                                <div class="d-flex align-items-center">
                                                    <i class="ti ti-user text-info me-1" title="From" style="font-size: 0.7rem;"></i>
                                                    <span>
                                                        From: {{ $message->sender->first_name ?? '' }} {{ $message->sender->last_name ?? '' }}
                                                    </span>
                                                </div>
                                                <small class="text-muted" style="font-size: 0.6rem;">{{ $message->sender->email ?? '' }}</small>
                                            @endif
                                        </td>
                                        <td class="py-1">
                                            <a href="{{ route('messages.show', $message->id) }}" class="text-decoration-none {{ !$message->is_read ? 'fw-bold' : '' }}">
                                                {{ Str::limit($message->subject ?? 'No subject', 50) }}
                                            </a>
                                            @if($message->attachments && $message->attachments->count() > 0)
                                                <i class="ti ti-paperclip ms-1 text-muted" title="Has attachments" style="font-size: 0.7rem;"></i>
                                            @endif
                                            @if($message->priority == 'urgent')
                                                <i class="ti ti-alert text-danger ms-1" title="Urgent" style="font-size: 0.7rem;"></i>
                                            @endif
                                        </td>
                                        <td class="py-1">
                                            {!! $message->priority_badge ?? '<span class="badge bg-secondary" style="font-size: 0.6rem;">Normal</span>' !!}
                                        </td>
                                        <td class="py-1 text-muted">
                                            <div title="{{ $message->created_at->format('Y-m-d H:i:s') }}" style="font-size: 0.65rem;">
                                                {{ $message->created_at->diffForHumans() }}
                                            </div>
                                            @if($message->deadline && $message->deadline->isPast())
                                                <span class="badge bg-warning mt-1" style="font-size: 0.55rem;">Overdue</span>
                                            @endif
                                        </td>
                                        <td class="pe-2 py-1">
                                            <div class="d-flex gap-1">
                                                <a href="{{ route('messages.show', $message->id) }}" class="btn btn-sm btn-outline-primary rounded-sm" style="font-size: 0.6rem; padding: 0.2rem 0.4rem;" title="View">
                                                    <i class="ti ti-eye"></i>
                                                </a>
                                                <a href="{{ route('messages.create', ['reply_to' => $message->id]) }}" class="btn btn-sm btn-outline-success rounded-sm" style="font-size: 0.6rem; padding: 0.2rem 0.4rem;" title="Reply">
                                                    <i class="ti ti-corner-up-right"></i>
                                                </a>
                                                <button type="button" 
                                                        onclick="showDeleteModal({{ $message->id }}, '{{ addslashes($message->subject) }}')" 
                                                        class="btn btn-sm btn-outline-danger rounded-sm" 
                                                        style="font-size: 0.6rem; padding: 0.2rem 0.4rem;" 
                                                        title="Delete">
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </form>
                </div>
                <div class="card-footer py-1 px-2 bg-light">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <button type="button" class="btn btn-sm btn-danger" id="bulkDeleteBtn" style="display: none; font-size: 0.65rem; padding: 0.2rem 0.5rem;">
                            <i class="ti ti-trash me-1"></i> Delete Selected
                        </button>
                        <div class="ms-auto">
                            {{ $messages->links() }}
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="ti ti-inbox" style="font-size: 48px; color: #ccc;"></i>
                    <p class="mt-3 text-muted" style="font-size: 0.75rem;">No messages in your inbox</p>
                    <a href="{{ route('messages.create') }}" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
                        <i class="ti ti-plus me-1"></i> Compose Message
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Single Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-sm">
            <div class="modal-header py-1 px-2">
                <h5 class="modal-title" style="font-size: 0.8rem;">Confirm Delete</h5>
                <button type="button" class="btn-close py-0" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body py-2 px-2">
                <p style="font-size: 0.75rem;">Are you sure you want to delete message <strong id="deleteMessageSubject"></strong>?</p>
                <p class="text-danger mb-0" style="font-size: 0.7rem;">This action cannot be undone.</p>
            </div>
            <div class="modal-footer py-1 px-2">
                <button type="button" class="btn btn-secondary btn-sm rounded-sm" data-bs-dismiss="modal" style="font-size: 0.7rem;">Cancel</button>
                <a href="#" id="deleteConfirmBtn" class="btn btn-danger btn-sm rounded-sm" style="font-size: 0.7rem;">Delete</a>
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
    // Select all checkbox
    const selectAllCheckbox = document.getElementById('selectAll');
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function(e) {
            document.querySelectorAll('.message-checkbox').forEach(cb => cb.checked = e.target.checked);
            toggleBulkDeleteBtn();
        });
    }
    
    document.querySelectorAll('.message-checkbox').forEach(cb => {
        cb.addEventListener('change', toggleBulkDeleteBtn);
    });
    
    function toggleBulkDeleteBtn() {
        const checked = document.querySelectorAll('.message-checkbox:checked').length > 0;
        const btn = document.getElementById('bulkDeleteBtn');
        if (btn) {
            btn.style.display = checked ? 'inline-block' : 'none';
        }
    }
    
    // Single Delete Modal Function
    function showDeleteModal(id, subject) {
        document.getElementById('deleteMessageSubject').innerText = subject;
        document.getElementById('deleteConfirmBtn').href = '/messages/' + id;
        new bootstrap.Modal(document.getElementById('deleteModal')).show();
    }
    
    // Bulk Delete - Show modal with count
    const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
    if (bulkDeleteBtn) {
        bulkDeleteBtn.addEventListener('click', function() {
            const checkedCount = document.querySelectorAll('.message-checkbox:checked').length;
            if (checkedCount === 0) {
                alert('Please select at least one message to delete.');
                return;
            }
            document.getElementById('bulkDeleteCount').innerText = checkedCount;
            new bootstrap.Modal(document.getElementById('bulkDeleteModal')).show();
        });
    }
    
    // Bulk Delete Confirmation
    const bulkDeleteConfirmBtn = document.getElementById('bulkDeleteConfirmBtn');
    if (bulkDeleteConfirmBtn) {
        bulkDeleteConfirmBtn.addEventListener('click', function() {
            document.getElementById('bulkDeleteForm').submit();
        });
    }
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