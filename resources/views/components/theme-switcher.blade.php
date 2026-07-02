@props([
    'position' => 'dropdown', // dropdown, inline
    'showLabels' => true
])

<div x-data="themeSwitcher()" x-init="initTheme()" class="theme-switcher">
    @if($position === 'dropdown')
    <div class="dropdown">
        <button class="btn btn-sm btn-outline-secondary rounded-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="ti ti-sun-moon me-1"></i>
            @if($showLabels)
            <span x-text="currentThemeLabel">Theme</span>
            @endif
        </button>
        <div class="dropdown-menu dropdown-menu-end" style="min-width: 200px;">
            <div class="dropdown-header">Theme Mode</div>
            <button class="dropdown-item" @click="setTheme('light')">
                <i class="ti ti-sun me-2"></i> Light Mode
                <i x-show="currentTheme === 'light'" class="ti ti-check ms-auto text-success"></i>
            </button>
            <button class="dropdown-item" @click="setTheme('dark')">
                <i class="ti ti-moon me-2"></i> Dark Mode
                <i x-show="currentTheme === 'dark'" class="ti ti-check ms-auto text-success"></i>
            </button>
            <button class="dropdown-item" @click="setTheme('auto')">
                <i class="ti ti-device-laptop me-2"></i> Auto (Follow System)
                <i x-show="currentTheme === 'auto'" class="ti ti-check ms-auto text-success"></i>
            </button>
        </div>
    </div>
    @elseif($position === 'inline')
    <div class="btn-group w-100" role="group">
        <button @click="setTheme('light')" class="btn btn-sm" :class="currentTheme === 'light' ? 'btn-primary' : 'btn-outline-secondary'">
            <i class="ti ti-sun"></i> Light
        </button>
        <button @click="setTheme('dark')" class="btn btn-sm" :class="currentTheme === 'dark' ? 'btn-primary' : 'btn-outline-secondary'">
            <i class="ti ti-moon"></i> Dark
        </button>
        <button @click="setTheme('auto')" class="btn btn-sm" :class="currentTheme === 'auto' ? 'btn-primary' : 'btn-outline-secondary'">
            <i class="ti ti-device-laptop"></i> Auto
        </button>
    </div>
    @endif
</div>

<script>
function themeSwitcher() {
    return {
        currentTheme: localStorage.getItem('theme') || 'auto',
        
        get currentThemeLabel() {
            const labels = { light: 'Light', dark: 'Dark', auto: 'Auto' };
            return labels[this.currentTheme] || 'Theme';
        },
        
        initTheme() {
            this.applyTheme(this.currentTheme);
            
            // Listen for system preference changes
            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
                if (this.currentTheme === 'auto') {
                    this.applyTheme('auto');
                }
            });
        },
        
        setTheme(theme) {
            this.currentTheme = theme;
            localStorage.setItem('theme', theme);
            this.applyTheme(theme);
        },
        
        applyTheme(theme) {
            if (theme === 'dark') {
                document.documentElement.setAttribute('data-bs-theme', 'dark');
                document.body.classList.add('dark-theme');
            } else if (theme === 'light') {
                document.documentElement.setAttribute('data-bs-theme', 'light');
                document.body.classList.remove('dark-theme');
            } else {
                // Auto - follow system preference
                const isDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                if (isDark) {
                    document.documentElement.setAttribute('data-bs-theme', 'dark');
                    document.body.classList.add('dark-theme');
                } else {
                    document.documentElement.setAttribute('data-bs-theme', 'light');
                    document.body.classList.remove('dark-theme');
                }
            }
        }
    }
}
</script>