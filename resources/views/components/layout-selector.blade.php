@props([
    'layouts' => [
        'grid' => ['icon' => 'layout-grid', 'label' => 'Grid View'],
        'list' => ['icon' => 'layout-list', 'label' => 'List View'],
        'compact' => ['icon' => 'layout-distribute-vertical', 'label' => 'Compact View']
    ]
])

<div x-data="layoutSelector()" class="layout-selector">
    <div class="btn-group" role="group">
        <template x-for="(value, key) in layouts">
            <button type="button" 
                    @click="setLayout(key)"
                    class="btn btn-sm btn-outline-secondary"
                    :class="{ 'active': currentLayout === key }"
                    :title="value.label">
                <i :class="'ti ti-' + value.icon"></i>
                <span class="ms-1" x-text="value.label"></span>
            </button>
        </template>
    </div>
</div>

<script>
function layoutSelector() {
    return {
        layouts: {{ json_encode($layouts) }},
        currentLayout: localStorage.getItem('default-layout') || 'grid',
        
        init() {
            this.applyLayout();
        },
        
        setLayout(layout) {
            this.currentLayout = layout;
            localStorage.setItem('default-layout', layout);
            this.applyLayout();
            
            // Dispatch event for other components to listen
            window.dispatchEvent(new CustomEvent('layout-changed', { detail: { layout: layout } }));
        },
        
        applyLayout() {
            document.body.classList.remove('layout-grid', 'layout-list', 'layout-compact');
            document.body.classList.add(`layout-${this.currentLayout}`);
        }
    }
}
</script>