@extends('layouts.master')

@section('title', 'Message Inbox')

@section('content')
<div class="container-fluid px-2">
    <!-- Breadcrumb -->
    <x-breadcrumb :items="[
        ['name' => 'Messages', 'url' => '#'],
        ['name' => 'Inbox', 'url' => '#']
    ]" />
    
    <x-page-header title="MESSAGE INBOX" description="Your messages and notifications">
        <div class="d-flex gap-2">
            <a href="{{ route('messages.create') }}" class="btn btn-primary btn-sm rounded-sm">
                <i class="ti ti-pencil me-1"></i> Compose
            </a>
            <button onclick="refreshInbox()" class="btn btn-outline-secondary btn-sm rounded-sm">
                <i class="ti ti-refresh me-1"></i> Refresh
            </button>
        </div>
    </x-page-header>

    <!-- Stats Row -->
    <div class="row g-2 mb-3">
        <div class="col-md-3">
            <div class="card rounded-sm border-0 shadow-sm">
                <div class="card-body py-2 px-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small">Total Messages</span>
                            <h5 class="mb-0">{{ $messages->total() }}</h5>
                        </div>
                        <div class="rounded-circle bg-primary bg-opacity-10 p-2">
                            <i class="ti ti-mail text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card rounded-sm border-0 shadow-sm">
                <div class="card-body py-2 px-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small">Unread</span>
                            <h5 class="mb-0 text-warning">{{ $unreadCount }}</h5>
                        </div>
                        <div class="rounded-circle bg-warning bg-opacity-10 p-2">
                            <i class="ti ti-mail-opened text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card rounded-sm border-0 shadow-sm">
                <div class="card-body py-2 px-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small">Urgent</span>
                            <h5 class="mb-0 text-danger">{{ $messages->where('priority', 'urgent')->count() }}</h5>
                        </div>
                        <div class="rounded-circle bg-danger bg-opacity-10 p-2">
                            <i class="ti ti-alert-circle text-danger"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card rounded-sm border-0 shadow-sm">
                <div class="card-body py-2 px-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small">Archived</span>
                            <h5 class="mb-0 text-secondary">{{ $messages->where('is_archived', true)->count() }}</h5>
                        </div>
                        <div class="rounded-circle bg-secondary bg-opacity-10 p-2">
                            <i class="ti ti-archive text-secondary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-search-card
        placeholder="Search messages..."
        :showTypeFilter="true"
        typeLabel="Type"
        :typeOptions="['general' => 'General', 'approval' => 'Approval', 'task' => 'Task', 'minute' => 'Minute']"
        :showCategoryFilter="true"
        categoryLabel="Priority"
        :categoryOptions="['urgent' => 'Urgent', 'high' => 'High', 'normal' => 'Normal', 'low' => 'Low']"
    >
        <div id="messageInboxSearchStats" class="text-muted" style="font-size: 0.65rem;">
            {{ $messages->total() }} messages
        </div>
    </x-search-card>

    <div class="row g-2">
        <!-- Messages List - main column -->
        <div class="col-lg-8">
            <div class="card rounded-sm border-0 shadow-sm">
                <div class="card-body p-0">
                    @forelse($messages as $msg)
                    <div class="message-item border-bottom p-3 {{ !$msg->is_read ? 'bg-light' : '' }}" data-type="{{ $msg->message_type }}" data-priority="{{ $msg->priority }}" data-subject="{{ strtolower($msg->subject) }}" data-sender="{{ strtolower($msg->sender->first_name ?? '') }}">
                        <div class="d-flex align-items-start justify-content-between">
                            <div class="d-flex align-items-start gap-3 flex-grow-1">
                                <!-- Avatar -->
                                <div class="avatar-circle flex-shrink-0">
                                    @if($msg->priority == 'urgent')
                                        <i class="ti ti-alert-circle text-danger"></i>
                                    @elseif($msg->priority == 'high')
                                        <i class="ti ti-chart-arrow-up text-warning"></i>
                                    @else
                                        <i class="ti ti-message-circle text-primary"></i>
                                    @endif
                                </div>

                                <!-- Content -->
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <span class="fw-semibold">{{ $msg->sender->first_name ?? 'System' }} {{ $msg->sender->last_name ?? '' }}</span>
                                        <span class="text-muted small">•</span>
                                        <span class="text-muted small">{{ $msg->created_at->diffForHumans() }}</span>

                                        @if(!$msg->is_read)
                                            <span class="badge bg-primary rounded-pill">New</span>
                                        @endif
                                        @if($msg->priority == 'urgent')
                                            <span class="badge bg-danger rounded-pill">Urgent</span>
                                        @endif
                                        @if($msg->has_attachments)
                                            <i class="ti ti-paperclip text-muted small"></i>
                                        @endif
                                    </div>

                                    <a href="{{ route('messages.show', $msg->id) }}" class="text-decoration-none">
                                        <div class="fw-medium text-dark">{{ $msg->subject }}</div>
                                        <div class="text-muted small text-truncate" style="max-width: 500px;">
                                            {{ Str::limit($msg->message, 100) }}
                                        </div>
                                    </a>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="btn-group">
                                <a href="{{ route('messages.show', $msg->id) }}" class="btn btn-sm btn-outline-primary" title="Read">
                                    <i class="ti ti-eye"></i>
                                </a>
                                <a href="{{ route('messages.create', ['reply_to' => $msg->id]) }}" class="btn btn-sm btn-outline-secondary" title="Reply">
                                    <i class="ti ti-corner-up-right"></i>
                                </a>
                                <button onclick="deleteMessage({{ $msg->id }})" class="btn btn-sm btn-outline-danger" title="Delete">
                                    <i class="ti ti-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-5">
                        <i class="ti ti-inbox text-muted" style="font-size: 48px;"></i>
                        <p class="text-muted mt-2">No messages in your inbox</p>
                        <a href="{{ route('messages.create') }}" class="btn btn-primary btn-sm">Compose your first message</a>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Pagination -->
            <div class="mt-3">
                {{ $messages->links() }}
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
                            <li><span class="badge bg-danger rounded-pill">Urgent</span> - requires immediate action</li>
                            <li><i class="ti ti-chart-arrow-up text-warning"></i> High - important, act soon</li>
                            <li><i class="ti ti-message-circle text-primary"></i> Normal/Low - routine correspondence</li>
                        </ul>
                    </div>

                    <div class="mb-2">
                        <div class="fw-semibold" style="font-size: 0.7rem;"><i class="ti ti-link me-1 text-primary"></i> Quick Links</div>
                        <ul class="mb-0 ps-3" style="font-size: 0.65rem; color: #555;">
                            <li><a href="{{ route('messages.create') }}">Compose</a> - send a new message</li>
                        </ul>
                    </div>

                    <x-alert-light class="mb-0 py-1 px-2 rounded-sm">
                        <div class="d-flex align-items-start gap-1">
                            <i class="ti ti-tips text-warning mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">Tips</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">&bull; Unread messages are highlighted</small><br>
                                <small class="text-muted" style="font-size: 0.6rem;">&bull; Use the reply icon to respond in one click</small>
                            </div>
                        </div>
                    </x-alert-light>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function deleteMessage(id) {
    confirmAction('Are you sure you want to delete this message?', function () {
        fetch(`/messages/${id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        })
        .then(response => {
            if(response.ok) {
                window.location.reload();
            } else {
                alert('Failed to delete message');
            }
        })
        .catch(error => console.error('Error:', error));
    });
}

function refreshInbox() {
    window.location.reload();
}

// Filter functionality
const typeFilter = document.getElementById('typeFilter');
const categoryFilter = document.getElementById('categoryFilter');
const searchInput = document.getElementById('searchInput');
const resetFilters = document.getElementById('resetFilters');
const messageItems = document.querySelectorAll('.message-item');

function filterMessages() {
    const type = typeFilter.value;
    const priority = categoryFilter.value;
    const search = searchInput.value.toLowerCase();
    
    messageItems.forEach(item => {
        let show = true;
        
        if(type !== '' && item.dataset.type !== type) show = false;
        if(priority !== '' && item.dataset.priority !== priority) show = false;
        if(search && !item.dataset.subject.includes(search) && !item.dataset.sender.includes(search)) show = false;
        
        item.style.display = show ? '' : 'none';
    });
}

typeFilter.addEventListener('change', filterMessages);
categoryFilter.addEventListener('change', filterMessages);
searchInput.addEventListener('keyup', filterMessages);

if (resetFilters) {
    resetFilters.addEventListener('click', function () {
        if (typeFilter) typeFilter.value = '';
        if (categoryFilter) categoryFilter.value = '';
        if (searchInput) searchInput.value = '';
        filterMessages();
    });
}
</script>

<style>
.avatar-circle {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.2rem;
}

.message-item {
    transition: all 0.2s ease;
    cursor: pointer;
}

.message-item:hover {
    background-color: #f8f9fa;
    transform: translateX(2px);
}

.btn-group .btn {
    padding: 0.2rem 0.5rem;
}
</style>
@endsection