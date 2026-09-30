@extends('layouts.master')

@section('title', 'Notifications')

@section('content')
<div class="container-fluid px-2">

    <x-breadcrumb :items="[
        ['name' => 'Notifications', 'url' => '#']
    ]" />

    <x-page-header title="NOTIFICATIONS" description="Everything sent to you, kept until you remove it">
        @if($counts['unread'] > 0)
        <form action="{{ route('notifications.mark-all-read') }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-outline-primary btn-sm rounded-sm" style="font-size: 0.7rem;">
                <i class="ti ti-checks me-1"></i> Mark all as read
            </button>
        </form>
        @endif
    </x-page-header>

    <x-alert-success />
    <x-alert-danger />

    <!-- Stats Cards -->
    <div class="row g-2 mb-3">
        <x-stats-card value="{{ $counts['all'] }}" label="Total" subtext="all notifications" color="primary" icon="bell" />
        <x-stats-card value="{{ $counts['unread'] }}" label="Unread" subtext="need your attention" color="warning" icon="mail" />
        <x-stats-card value="{{ $counts['read'] }}" label="Read" subtext="already seen" color="success" icon="mail-opened" />
        <x-stats-card value="{{ $counts['urgent'] }}" label="Urgent" subtext="high priority, unread" color="danger" icon="alert-triangle" />
    </div>

    <!-- Filter Tabs -->
    @php
        $filterTabIcons = ['all' => 'bell', 'unread' => 'mail', 'read' => 'mail-opened'];
    @endphp
    <ul class="nav nav-tabs mb-2">
        @foreach(['all' => 'All', 'unread' => 'Unread', 'read' => 'Read'] as $key => $label)
        <li class="nav-item">
            <a href="{{ route('notifications.index', ['filter' => $key]) }}"
               class="nav-link {{ $filter === $key ? 'active' : '' }}"
               style="font-size: 0.7rem;">
                <i class="ti ti-{{ $filterTabIcons[$key] }} me-1"></i> {{ $label }} ({{ $counts[$key] }})
            </a>
        </li>
        @endforeach
    </ul>

    <div class="row g-2">
        <!-- List -->
        <div class="col-lg-8 {{ $selected ? 'order-2 order-lg-1' : 'order-1' }}">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light d-flex align-items-center justify-content-between flex-wrap gap-1">
                    <div class="d-flex align-items-center gap-2">
                        <input type="checkbox" class="form-check-input" id="selectAllNotifs" onchange="toggleSelectAllNotifs(this)" {{ $notifications->isEmpty() ? 'disabled' : '' }}>
                        <i class="ti ti-bell text-primary" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">{{ ucfirst($filter) }} Notifications</span>
                    </div>
                    <div id="bulkActionBar" class="d-none align-items-center gap-2">
                        <span class="text-muted" id="bulkSelectedCount" style="font-size: 0.68rem;"></span>
                        <button type="button" class="btn btn-outline-danger btn-sm rounded-sm" style="font-size: 0.68rem; padding: 0.15rem 0.5rem;" onclick="bulkDeleteNotifications()">
                            <i class="ti ti-trash me-1"></i> Delete Selected
                        </button>
                    </div>
                </div>
                <div class="list-group list-group-flush" id="notificationList">
                    @forelse($notifications as $n)
                        <a href="{{ route('notifications.index', array_filter(['filter' => $filter, 'notification' => $n->id, 'page' => $notifications->currentPage()])) }}"
                           class="list-group-item list-group-item-action notif-row {{ $n->is_read ? '' : 'unread' }} {{ $selected && $selected->id === $n->id ? 'selected' : '' }}">
                            <div class="d-flex align-items-start gap-2">
                                <input type="checkbox" class="form-check-input notif-select flex-shrink-0" value="{{ $n->id }}" style="margin-top: 0.15rem;" onclick="event.stopPropagation();" onchange="updateBulkBar()">
                                <div class="notif-dot"></div>
                                <div class="flex-shrink-0">
                                    <i class="ti ti-{{ $n->type_icon }} {{ $n->type_color }}" style="font-size: 0.95rem;"></i>
                                </div>
                                <div class="flex-grow-1 min-w-0">
                                    <div class="notif-title text-truncate">{{ $n->title }}</div>
                                    <div class="text-muted text-truncate" style="font-size: 0.68rem;">{{ Str::limit($n->message, 70) }}</div>
                                    <div class="text-muted" style="font-size: 0.6rem;">{{ $n->time_ago }}</div>
                                </div>
                                <button type="button"
                                        class="btn btn-sm btn-link text-danger p-0 flex-shrink-0"
                                        title="Remove"
                                        onclick="event.preventDefault(); event.stopPropagation(); showDeleteModal('{{ $n->id }}', '{{ addslashes($n->title) }}', 'notification', '{{ route('notifications.destroy', $n->id) }}?filter={{ $filter }}')">
                                    <i class="ti ti-trash" style="font-size: 0.75rem;"></i>
                                </button>
                            </div>
                        </a>
                    @empty
                        <div class="text-center py-4 text-muted">
                            <i class="ti ti-bell-off mb-2 d-block" style="font-size: 1.5rem;"></i>
                            <p class="mb-0" style="font-size: 0.75rem;">No {{ $filter !== 'all' ? $filter : '' }} notifications</p>
                        </div>
                    @endforelse
                </div>
                @if($notifications->hasPages() || $notifications->total() > 0)
                <div class="card-footer py-1 px-2">
                    <x-pagination :items="$notifications" :perPageOptions="[15, 25, 50, 100]" />
                </div>
                @endif
            </div>
        </div>

        <!-- Detail -->
        <div class="col-lg-4 {{ $selected ? 'order-1 order-lg-2' : 'order-2' }}">
            <div class="card rounded-sm h-100">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-info-circle text-primary me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Notification Details</span>
                    </div>
                </div>
                @if($selected)
                    <div class="card-body p-2">
                        <div class="mb-2">
                            <div class="text-muted" style="font-size: 0.6rem;">Title</div>
                            <div style="font-size: 0.8rem; font-weight: 600;">{{ $selected->title }}</div>
                        </div>
                        <div class="mb-2">
                            <div class="text-muted" style="font-size: 0.6rem;">Priority</div>
                            <span class="badge {{ $selected->priority_badge }} rounded-pill" style="font-size: 0.65rem;">{{ ucfirst($selected->priority ?? 'normal') }}</span>
                        </div>
                        <div class="mb-2">
                            <div class="text-muted" style="font-size: 0.6rem;">Received</div>
                            <div style="font-size: 0.75rem;">{{ $selected->formatted_date }}</div>
                            <div class="text-muted" style="font-size: 0.65rem;">{{ $selected->time_ago }}</div>
                        </div>
                        <div class="mb-3">
                            <div class="text-muted" style="font-size: 0.6rem;">Status</div>
                            <div style="font-size: 0.75rem;"><i class="ti ti-check text-success"></i> Read</div>
                        </div>

                        <hr class="my-2">

                        <div class="mb-3">
                            <div class="text-muted mb-1" style="font-size: 0.6rem;">Message</div>
                            <div style="font-size: 0.8rem; white-space: pre-wrap;">{{ $selected->message }}</div>
                        </div>

                        <div class="d-flex gap-2">
                            @if($selected->action_url)
                            <a href="{{ $selected->action_url }}" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem;">
                                <i class="ti ti-external-link me-1"></i> {{ $selected->action_text ?: 'Open' }}
                            </a>
                            @endif
                            <button type="button"
                                    class="btn btn-outline-danger btn-sm rounded-sm"
                                    style="font-size: 0.7rem;"
                                    onclick="showDeleteModal('{{ $selected->id }}', '{{ addslashes($selected->title) }}', 'notification', '{{ route('notifications.destroy', $selected->id) }}?filter={{ $filter }}')">
                                <i class="ti ti-trash me-1"></i> Remove
                            </button>
                        </div>
                    </div>
                @else
                    <div class="card-body p-5 text-center text-muted d-flex flex-column align-items-center justify-content-center">
                        <i class="ti ti-mail-opened mb-2" style="font-size: 2rem;"></i>
                        <p class="mb-0" style="font-size: 0.8rem;">Select a notification from the list to view its full content</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <form id="bulkDeleteForm" action="{{ route('notifications.bulk-destroy') }}" method="POST" class="d-none">
        @csrf
        @method('DELETE')
        <input type="hidden" name="filter" value="{{ $filter }}">
    </form>
</div>

<script>
function getSelectedNotifIds() {
    return Array.from(document.querySelectorAll('.notif-select:checked')).map(cb => cb.value);
}

function updateBulkBar() {
    const ids = getSelectedNotifIds();
    const all = document.querySelectorAll('.notif-select');
    const bar = document.getElementById('bulkActionBar');
    const countEl = document.getElementById('bulkSelectedCount');
    const selectAll = document.getElementById('selectAllNotifs');

    if (ids.length > 0) {
        bar.classList.remove('d-none');
        bar.classList.add('d-flex');
        countEl.textContent = ids.length + ' selected';
    } else {
        bar.classList.add('d-none');
        bar.classList.remove('d-flex');
    }

    if (selectAll) {
        selectAll.checked = all.length > 0 && ids.length === all.length;
        selectAll.indeterminate = ids.length > 0 && ids.length < all.length;
    }
}

function toggleSelectAllNotifs(checkbox) {
    document.querySelectorAll('.notif-select').forEach(cb => { cb.checked = checkbox.checked; });
    updateBulkBar();
}

function bulkDeleteNotifications() {
    const ids = getSelectedNotifIds();
    if (ids.length === 0) {
        return;
    }

    confirmAction(
        `Delete ${ids.length} selected notification${ids.length === 1 ? '' : 's'}?`,
        function () {
            const form = document.getElementById('bulkDeleteForm');
            form.querySelectorAll('input[name="ids[]"]').forEach(el => el.remove());
            ids.forEach(id => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'ids[]';
                input.value = id;
                form.appendChild(input);
            });
            form.submit();
        },
        { title: 'Delete Notifications', warning: "They'll just drop off your list, not gone forever." }
    );
}
</script>

<style>
.notif-row {
    position: relative;
    border-left: 3px solid transparent;
    padding: 0.6rem 0.75rem;
}

.notif-row.unread {
    background-color: #EFF6FF;
    border-left: 3px solid #3B82F6;
}

.notif-row.selected {
    background-color: #F1F5F9;
    border-left: 3px solid #1E293B;
}

.notif-title {
    font-size: 0.78rem;
    font-weight: 400;
    color: #64748B;
}

.notif-row.unread .notif-title {
    font-weight: 700;
    color: #1E293B;
}

.notif-dot {
    display: none;
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: #3B82F6;
    flex-shrink: 0;
    margin-top: 5px;
}

.notif-row.unread .notif-dot {
    display: block;
}

.min-w-0 {
    min-width: 0;
}
</style>
@endsection
