<div x-data="notificationToast()" 
     x-init="listenForNotifications()" 
     class="notification-toast-container"
     style="position: fixed; top: 20px; right: 20px; z-index: 9999;">
    
    <template x-for="(notification, index) in notifications" :key="index">
        <div x-show="visible[index]" 
             x-transition.duration.300ms
             class="toast show mb-2" 
             role="alert"
             :class="'toast-' + notification.type">
            <div class="toast-header">
                <i class="ti me-1" :class="'ti-' + notification.icon"></i>
                <strong class="me-auto" x-text="notification.title"></strong>
                <small x-text="notification.time"></small>
                <button type="button" class="btn-close" @click="dismiss(index)"></button>
            </div>
            <div class="toast-body" x-text="notification.message"></div>
        </div>
    </template>
</div>

<style>
.toast-success { border-left: 4px solid #10B981; }
.toast-error { border-left: 4px solid #EF4444; }
.toast-warning { border-left: 4px solid #F59E0B; }
.toast-info { border-left: 4px solid #3B82F6; }
</style>

<script>
function notificationToast() {
    return {
        notifications: [],
        visible: [],
        nextId: 0,
        
        addNotification(type, title, message, duration = 5000) {
            const id = this.nextId++;
            const notification = {
                id: id,
                type: type,
                title: title,
                message: message,
                icon: this.getIcon(type),
                time: new Date().toLocaleTimeString()
            };
            this.notifications.push(notification);
            this.visible[id] = true;
            
            if (duration > 0) {
                setTimeout(() => this.dismiss(id), duration);
            }
        },
        
        dismiss(id) {
            this.visible[id] = false;
            setTimeout(() => {
                this.notifications = this.notifications.filter(n => n.id !== id);
            }, 300);
        },
        
        getIcon(type) {
            const icons = {
                success: 'check-circle',
                error: 'alert-circle',
                warning: 'alert-triangle',
                info: 'info-circle'
            };
            return icons[type] || 'bell';
        },
        
        listenForNotifications() {
            // Listen for Livewire events or AJAX notifications
            window.addEventListener('show-notification', (e) => {
                this.addNotification(e.detail.type, e.detail.title, e.detail.message);
            });
            
            // Listen for Laravel notification broadcasts
            Echo?.channel('notifications')?.listen('NewNotification', (e) => {
                this.addNotification(e.type, e.title, e.message);
            });
        }
    }
}
</script>