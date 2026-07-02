@props([
    'position' => 'right' // left, right, top, bottom
])

<div x-data="settingsPanel()" class="settings-panel">
    <!-- Settings Toggle Button -->
    <button @click="togglePanel" class="settings-toggle-btn" title="Settings">
        <i class="ti ti-settings"></i>
    </button>
    
    <!-- Settings Panel -->
    <div x-show="isOpen" 
         x-transition:enter="transform transition duration-300"
         x-transition:enter-start="translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transform transition duration-300"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="translate-x-full"
         class="settings-panel-content"
         style="position: fixed; top: 0; {{ $position }}: 0; width: 320px; height: 100vh; background: white; z-index: 1050; box-shadow: -2px 0 8px rgba(0,0,0,0.1); overflow-y: auto;">
        
        <div class="p-3">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">
                    <i class="ti ti-settings me-2"></i> Settings
                </h5>
                <button @click="togglePanel" class="btn-close"></button>
            </div>
            
            <div class="border-bottom mb-3"></div>
            
            <!-- Theme Section -->
            <div class="mb-4">
                <h6 class="mb-2">Appearance</h6>
                <div class="d-flex gap-2 mb-3">
                    <button @click="setTheme('light')" class="btn btn-outline-secondary" :class="{ 'active': currentTheme === 'light' }">
                        <i class="ti ti-sun"></i> Light
                    </button>
                    <button @click="setTheme('dark')" class="btn btn-outline-secondary" :class="{ 'active': currentTheme === 'dark' }">
                        <i class="ti ti-moon"></i> Dark
                    </button>
                    <button @click="setTheme('auto')" class="btn btn-outline-secondary" :class="{ 'active': currentTheme === 'auto' }">
                        <i class="ti ti-device-laptop"></i> Auto
                    </button>
                </div>
            </div>
            
            <!-- Color Section -->
            <div class="mb-4">
                <x-color-picker label="Primary Color" :colors="[
                    'blue' => '#3B82F6',
                    'green' => '#10B981',
                    'purple' => '#8B5CF6',
                    'red' => '#EF4444',
                    'orange' => '#F59E0B'
                ]" />
            </div>
            
            <!-- Layout Section -->
            <div class="mb-4">
                <h6 class="mb-2">Layout</h6>
                <div class="d-flex gap-2">
                    <button @click="setLayout('compact')" class="btn btn-outline-secondary" :class="{ 'active': currentLayout === 'compact' }">
                        <i class="ti ti-layout"></i> Compact
                    </button>
                    <button @click="setLayout('comfortable')" class="btn btn-outline-secondary" :class="{ 'active': currentLayout === 'comfortable' }">
                        <i class="ti ti-layout-2"></i> Comfortable
                    </button>
                </div>
            </div>
            
            <!-- Sidebar Section -->
            <div class="mb-4">
                <div class="form-check form-switch">
                    <input type="checkbox" class="form-check-input" x-model="sidebarCollapsed" @change="toggleSidebar">
                    <label class="form-check-label">Collapsed Sidebar</label>
                </div>
            </div>
            
            <!-- Font Size Section -->
            <div class="mb-4">
                <h6 class="mb-2">Font Size</h6>
                <div class="d-flex gap-2">
                    <button @click="setFontSize('small')" class="btn btn-outline-secondary" :class="{ 'active': currentFontSize === 'small' }">Small</button>
                    <button @click="setFontSize('medium')" class="btn btn-outline-secondary" :class="{ 'active': currentFontSize === 'medium' }">Medium</button>
                    <button @click="setFontSize('large')" class="btn btn-outline-secondary" :class="{ 'active': currentFontSize === 'large' }">Large</button>
                </div>
            </div>
            
            <!-- Animation Section -->
            <div class="mb-4">
                <div class="form-check form-switch">
                    <input type="checkbox" class="form-check-input" x-model="animationsEnabled" @change="toggleAnimations">
                    <label class="form-check-label">Enable Animations</label>
                </div>
            </div>
            
            <div class="border-bottom mb-3"></div>
            
            <button @click="resetToDefaults" class="btn btn-sm btn-secondary w-100">
                <i class="ti ti-refresh me-1"></i> Reset to Defaults
            </button>
        </div>
    </div>
    
    <!-- Overlay -->
    <div x-show="isOpen" x-transition.opacity class="settings-overlay" style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 1040;" @click="togglePanel"></div>
</div>

<style>
.settings-toggle-btn {
    position: fixed;
    right: 20px;
    bottom: 20px;
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: var(--primary-color, #3B82F6);
    color: white;
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
    box-shadow: 0 2px 8px rgba(0,0,0,0.2);
    transition: transform 0.2s;
}
.settings-toggle-btn:hover {
    transform: scale(1.05);
}
</style>

<script>
function settingsPanel() {
    return {
        isOpen: false,
        currentTheme: localStorage.getItem('theme') || 'auto',
        currentLayout: localStorage.getItem('layout') || 'comfortable',
        currentFontSize: localStorage.getItem('font-size') || 'medium',
        sidebarCollapsed: localStorage.getItem('sidebar-collapsed') === 'true',
        animationsEnabled: localStorage.getItem('animations-enabled') !== 'false',
        
        togglePanel() {
            this.isOpen = !this.isOpen;
        },
        
        setTheme(theme) {
            this.currentTheme = theme;
            localStorage.setItem('theme', theme);
            this.applyTheme(theme);
        },
        
        setLayout(layout) {
            this.currentLayout = layout;
            localStorage.setItem('layout', layout);
            this.applyLayout(layout);
        },
        
        setFontSize(size) {
            this.currentFontSize = size;
            localStorage.setItem('font-size', size);
            this.applyFontSize(size);
        },
        
        toggleSidebar() {
            localStorage.setItem('sidebar-collapsed', this.sidebarCollapsed);
            document.getElementById('sidebarArea')?.classList.toggle('collapsed', this.sidebarCollapsed);
        },
        
        toggleAnimations() {
            localStorage.setItem('animations-enabled', this.animationsEnabled);
            this.applyAnimations();
        },
        
        applyTheme(theme) {
            if (theme === 'dark') {
                document.documentElement.setAttribute('data-bs-theme', 'dark');
            } else if (theme === 'light') {
                document.documentElement.setAttribute('data-bs-theme', 'light');
            } else {
                const isDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                document.documentElement.setAttribute('data-bs-theme', isDark ? 'dark' : 'light');
            }
        },
        
        applyLayout(layout) {
            document.body.classList.toggle('compact-layout', layout === 'compact');
        },
        
        applyFontSize(size) {
            const sizes = { small: '12px', medium: '14px', large: '16px' };
            document.documentElement.style.setProperty('--base-font-size', sizes[size]);
        },
        
        applyAnimations() {
            document.body.classList.toggle('disable-animations', !this.animationsEnabled);
        },
        
        resetToDefaults() {
            this.setTheme('auto');
            this.setLayout('comfortable');
            this.setFontSize('medium');
            this.sidebarCollapsed = false;
            this.animationsEnabled = true;
            localStorage.clear();
            location.reload();
        }
    }
}
</script>