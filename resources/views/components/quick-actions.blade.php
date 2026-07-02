@props([
    'actions' => []
])

<div x-data="quickActions()" class="quick-actions">
    <div class="position-fixed" style="bottom: 80px; right: 20px; z-index: 999;">
        <button @click="toggleMenu" class="btn btn-primary rounded-circle shadow-lg" style="width: 56px; height: 56px;">
            <i class="ti ti-plus fs-3"></i>
        </button>
        
        <div x-show="isOpen" 
             x-transition.duration.200ms
             class="position-absolute bottom-100 end-0 mb-2 bg-white rounded shadow-lg p-2"
             style="min-width: 200px;">
            <div class="d-grid gap-2">
                <a href="{{ route('leave-requests.create') }}" class="btn btn-sm btn-outline-primary text-start">
                    <i class="ti ti-calendar-plus me-2"></i> Apply for Leave
                </a>
                <a href="{{ route('staff.create') }}" class="btn btn-sm btn-outline-primary text-start">
                    <i class="ti ti-user-plus me-2"></i> Add Staff
                </a>
                <a href="{{ route('reports.index') }}" class="btn btn-sm btn-outline-primary text-start">
                    <i class="ti ti-chart-bar me-2"></i> Generate Report
                </a>
                <hr class="my-1">
                <button @click="openQuickAction('settings')" class="btn btn-sm btn-outline-secondary text-start">
                    <i class="ti ti-settings me-2"></i> Quick Settings
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function quickActions() {
    return {
        isOpen: false,
        
        toggleMenu() {
            this.isOpen = !this.isOpen;
            if (this.isOpen) {
                setTimeout(() => {
                    document.addEventListener('click', this.closeMenu.bind(this));
                }, 100);
            } else {
                document.removeEventListener('click', this.closeMenu.bind(this));
            }
        },
        
        closeMenu(e) {
            if (!e.target.closest('.quick-actions')) {
                this.isOpen = false;
                document.removeEventListener('click', this.closeMenu.bind(this));
            }
        },
        
        openQuickAction(action) {
            console.log('Quick action:', action);
            this.isOpen = false;
        }
    }
}
</script>