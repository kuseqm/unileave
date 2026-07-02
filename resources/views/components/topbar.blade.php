@php
    $user = Auth::user();
    $unreadCount = App\Models\Notification::where('user_id', $user->id)->where('is_read', false)->count();
    $unreadMessageCount = App\Models\Message::where('receiver_id', $user->id)->where('is_read', false)->count();
@endphp

<div class="content-topbar">
    <div class="d-flex align-items-center justify-content-between w-100">
        
        <!-- LEFT SIDE: Leave Counter -->
        <div class="d-flex align-items-center">
            <x-leave-counter :user="Auth::user()" />
        </div>
        
        <!-- RIGHT SIDE: Messages, Notifications, User Dropdown -->
        <div class="d-flex align-items-center gap-3">
            <!-- Messages Icon -->
            <a href="{{ route('messages.index') }}" class="position-relative text-decoration-none">
                <i class="ti ti-message" style="font-size: 1.2rem; color: #64748B;"></i>
                @if($unreadMessageCount > 0)
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem;">
                    {{ $unreadMessageCount > 99 ? '99+' : $unreadMessageCount }}
                </span>
                @endif
            </a>

            <!-- Notifications Bell -->
            <div class="dropdown">
                <a class="position-relative" href="#" id="notificationsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="ti ti-bell" style="font-size: 1.2rem; color: #64748B;"></i>
                    @if($unreadCount > 0)
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem;">
                        {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                    </span>
                    @endif
                </a>
                <ul class="dropdown-menu dropdown-menu-end" id="notificationsList" style="width: 350px; max-height: 400px; overflow-y: auto;">
                    <li class="dropdown-header d-flex justify-content-between align-items-center">
                        <span>Notifications</span>
                        <a href="#" id="markAllRead" class="small">Mark all as read</a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li class="text-center py-3 text-muted" id="noNotifications">Loading...</li>
                </ul>
            </div>

            <!-- User Dropdown -->
            <div class="dropdown">
                <div class="user-info" data-bs-toggle="dropdown">
                    <div class="user-avatar" style="background-image: url('{{ $user->avatar ? Storage::url($user->avatar) : '' }}'); background-size: cover; background-position: center;">
                        @if(!$user->avatar)
                            {{ strtoupper(substr($user->first_name, 0, 1)) }}
                            @if($user->middle_name)
                                {{ strtoupper(substr($user->middle_name, 0, 1)) }}
                            @endif
                            {{ strtoupper(substr($user->last_name, 0, 1)) }}
                        @endif
                    </div>
                    <span class="user-name">
                        <span style="color: #3B82F6; font-weight: 700; text-transform: uppercase;">{{ $user->last_name }}</span>, 
                        {{ $user->first_name }}
                        @if($user->middle_name)
                            {{ $user->middle_name }}
                        @endif
                    </span>
                </div>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="{{ route('profile.show') }}">Profile</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger">Logout</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
        
    </div>
</div>

<!-- Acting Response Modal Container -->
<div id="acting-response-modal-container"></div>

<style>
.content-topbar {
    height: 60px;
    background: #fdf8f4 !important;  /* ← Changed to match sidebar */
    border-bottom: 1px solid #E8E2D5;
    display: flex;
    align-items: center;
    justify-content: flex-end;
    padding: 0 1.5rem;
    position: sticky;
    top: 0;
    z-index: 99;
    flex-shrink: 0;
}

/* Adjust dropdown menus to match the new theme */
.dropdown-menu {
    background: #fdf8f4 !important;
    border: 1px solid #E8E2D5;
    border-radius: 0 !important;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}

.dropdown-item {
    color: #475569;
    padding: 0.5rem 1rem;
    font-size: 0.85rem;
}

.dropdown-item:hover {
    background: #F2D2BD !important;
    color: #1E293B !important;
}

.dropdown-header {
    color: #1E293B;
    font-weight: 600;
    padding: 0.75rem 1rem;
}

.dropdown-divider {
    border-color: #E8E2D5;
}

.user-avatar {
    width: 36px;
    height: 36px;
    background: #EFF6FF;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #3B82F6;
    font-weight: 600;
    cursor: pointer;
    background-size: cover;
    background-position: center;
}

.user-info {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
}

.user-name {
    font-size: 0.75rem;
    font-weight: 500;
    color: #1E293B;
}

.notification-item {
    cursor: pointer;
    transition: background 0.2s;
}

.notification-item.unread {
    background-color: #FFF8F4 !important;  /* Slightly lighter than topbar */
}

.notification-item:hover {
    background-color: #F2D2BD !important;
}

.notification-item a {
    text-decoration: none;
    color: inherit;
}

.notification-item .dropdown-item {
    white-space: normal !important;
    word-wrap: break-word !important;
}

/* Modal animation */
.modal.fade .modal-dialog {
    transform: scale(0.8);
    transition: transform 0.2s ease-in-out;
}

.modal.show .modal-dialog {
    transform: scale(1);
}

/* Notification badge styles */
.badge {
    font-weight: 600;
}

/* Scrollbar for notifications */
#notificationsList::-webkit-scrollbar {
    width: 4px;
}

#notificationsList::-webkit-scrollbar-track {
    background: #F5F0EB;
}

#notificationsList::-webkit-scrollbar-thumb {
    background: #E8E2D5;
    border-radius: 2px;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .content-topbar {
        padding: 0 0.75rem;
        height: 55px;
    }
    
    .user-name {
        font-size: 0.65rem;
        max-width: 100px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    
    .user-avatar {
        width: 30px;
        height: 30px;
        font-size: 0.7rem;
    }
    
    #notificationsList {
        width: 280px !important;
    }
}

@media (max-width: 576px) {
    .user-name {
        display: none;
    }
    
    .content-topbar {
        padding: 0 0.5rem;
        height: 50px;
    }
    
    #notificationsList {
        width: 250px !important;
        max-height: 300px !important;
    }
}
</style>

<script>
// Load notifications function
function loadNotifications() {
    fetch('/notifications/list')
        .then(response => response.json())
        .then(data => {
            const badge = document.querySelector('#notificationsDropdown + .dropdown-menu')?.previousElementSibling?.querySelector('.badge');
            const list = document.getElementById('notificationsList');
            
            if (!list) return;
            
            // Update badge
            if (badge && data.unread_count > 0) {
                badge.style.display = 'inline-block';
                badge.innerText = data.unread_count > 99 ? '99+' : data.unread_count;
            } else if (badge) {
                badge.style.display = 'none';
            }
            
            // Clear existing items except header and divider
            const itemsToRemove = list.querySelectorAll('.notification-item');
            itemsToRemove.forEach(item => item.remove());
            
            const noNotif = list.querySelector('#noNotifications');
            if (noNotif) noNotif.remove();
            
            if (data.notifications.length === 0) {
                const emptyMsg = document.createElement('li');
                emptyMsg.id = 'noNotifications';
                emptyMsg.className = 'text-center py-3 text-muted small';
                emptyMsg.innerText = 'No notifications';
                list.appendChild(emptyMsg);
            } else {
                data.notifications.forEach(n => {
                    const item = document.createElement('li');
                    item.className = `notification-item ${n.is_read ? '' : 'unread'}`;
                    item.setAttribute('data-id', n.id);
                    item.setAttribute('data-type', n.type);
                    item.setAttribute('data-action-url', n.action_url);
                    item.innerHTML = `
                        <a href="#" class="dropdown-item text-wrap py-2 notification-link" data-url="${n.action_url || '#'}" data-type="${n.type}">
                            <div class="d-flex">
                                <div class="flex-shrink-0 me-2">
                                    <i class="ti ti-${n.icon} ${n.icon_color}"></i>
                                </div>
                                <div>
                                    <div class="small fw-semibold">${escapeHtml(n.title)}</div>
                                    <div class="small text-muted">${escapeHtml(n.message)}</div>
                                    <div class="small text-muted mt-1">${escapeHtml(n.time_ago)}</div>
                                </div>
                            </div>
                        </a>
                    `;
                    list.appendChild(item);
                });
            }
        })
        .catch(error => console.error('Error loading notifications:', error));
}

// Handle notification click - check if it's an acting request to open modal
function handleNotificationClick(url, type, notificationId) {
    // Mark as read first
    fetch(`/notifications/${notificationId}/read`, { 
        method: 'POST',
        headers: { 
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
            'Content-Type': 'application/json'
        }
    }).catch(() => {});
    
    // Check if it's an acting request
    if (type === 'acting_request' && url.includes('/acting/')) {
        // Open modal instead of redirecting
        openActingResponseModal(url);
        return;
    }
    
    // Otherwise redirect normally
    window.location.href = url;
}

// Open acting response modal
function openActingResponseModal(url) {
    // Add modal parameter to URL
    const modalUrl = url + (url.includes('?') ? '&' : '?') + 'modal=1';
    
    fetch(modalUrl, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'text/html'
        }
    })
    .then(response => response.text())
    .then(html => {
        const container = document.getElementById('acting-response-modal-container');
        container.innerHTML = html;
        
        // Show the modal
        const modal = new bootstrap.Modal(document.getElementById('actingResponseModal'));
        modal.show();
        
        // Remove modal from DOM when hidden
        document.getElementById('actingResponseModal')?.addEventListener('hidden.bs.modal', function() {
            container.innerHTML = '';
        });
    })
    .catch(error => {
        console.error('Error loading modal:', error);
        // Fallback to normal redirect
        window.location.href = url;
    });
}

// Mark single notification as read when clicked
document.addEventListener('click', function(e) {
    // Handle notification item click
    const notificationItem = e.target.closest('.notification-item');
    if (notificationItem) {
        e.preventDefault();
        const id = notificationItem.getAttribute('data-id');
        const type = notificationItem.getAttribute('data-type');
        const url = notificationItem.getAttribute('data-action-url');
        
        if (type === 'acting_request' && url) {
            // Open modal for acting requests
            openActingResponseModal(url);
            
            // Still mark as read
            fetch(`/notifications/${id}/read`, { 
                method: 'POST',
                headers: { 
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    'Content-Type': 'application/json'
                }
            }).catch(() => {});
            
            // Remove unread class
            notificationItem.classList.remove('unread');
        } else if (url && url !== '#') {
            // Normal notification - mark as read and redirect
            fetch(`/notifications/${id}/read`, { 
                method: 'POST',
                headers: { 
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    'Content-Type': 'application/json'
                }
            }).then(() => {
                notificationItem.classList.remove('unread');
                window.location.href = url;
            }).catch(() => {
                window.location.href = url;
            });
        }
    }
});

// Handle notification link clicks (for dynamically added links)
document.addEventListener('click', function(e) {
    const link = e.target.closest('.notification-link');
    if (link) {
        e.preventDefault();
        const url = link.getAttribute('data-url');
        const type = link.getAttribute('data-type');
        const notificationItem = link.closest('.notification-item');
        const id = notificationItem?.getAttribute('data-id');
        
        if (type === 'acting_request' && url) {
            openActingResponseModal(url);
            if (id) {
                fetch(`/notifications/${id}/read`, { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '' } }).catch(() => {});
            }
        } else if (url && url !== '#') {
            window.location.href = url;
        }
    }
});

// Mark all as read
document.getElementById('markAllRead')?.addEventListener('click', function(e) {
    e.preventDefault();
    fetch('/notifications/mark-all-read', { 
        method: 'POST', 
        headers: { 
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
            'Content-Type': 'application/json'
        } 
    }).then(() => {
        loadNotifications();
        const badge = document.querySelector('#notificationsDropdown + .dropdown-menu')?.previousElementSibling?.querySelector('.badge');
        if (badge) badge.style.display = 'none';
    });
});

// Load unread message count
function loadUnreadMessageCount() {
    fetch('/messages/unread-count')
        .then(response => response.json())
        .then(data => {
            const badge = document.querySelector('a[href*="messages.index"] .badge');
            if (badge && data.count > 0) {
                badge.style.display = 'inline-block';
                badge.innerText = data.count > 99 ? '99+' : data.count;
            } else if (badge) {
                badge.style.display = 'none';
            }
        })
        .catch(error => console.error('Error loading message count:', error));
}

// Escape HTML to prevent XSS
function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Load on page load
document.addEventListener('DOMContentLoaded', function() {
    loadNotifications();
    loadUnreadMessageCount();
});

// Reload every 30 seconds
setInterval(function() {
    loadNotifications();
    loadUnreadMessageCount();
}, 30000);
</script>