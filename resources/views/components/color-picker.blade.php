@props([
    'label' => 'Primary Color',
    'colors' => [
        'blue' => '#3B82F6',
        'green' => '#10B981', 
        'purple' => '#8B5CF6',
        'red' => '#EF4444',
        'orange' => '#F59E0B',
        'teal' => '#14B8A6',
        'pink' => '#EC4899',
        'indigo' => '#6366F1',
        'cyan' => '#06B6D4',
        'amber' => '#FBBF24'
    ]
])

<div x-data="colorPicker()" x-init="initColor()" class="color-picker">
    <label class="form-label mb-2">{{ $label }}</label>
    <div class="d-flex gap-2 flex-wrap">
        <template x-for="(colorCode, colorName) in colors">
            <div @click="setColor(colorName)" 
                 :style="{ backgroundColor: colorCode }"
                 class="color-option"
                 :class="{ 'active': currentColor === colorName }"
                 :title="colorName">
                <i x-show="currentColor === colorName" class="ti ti-check text-white"></i>
            </div>
        </template>
    </div>
    <div class="mt-2">
        <div class="d-flex align-items-center gap-2">
            <span class="text-muted small">Custom:</span>
            <input type="color" x-model="customColor" @change="setCustomColor($event.target.value)" class="form-control form-control-color" style="width: 50px;">
            <span class="text-muted small" x-text="currentColorHex"></span>
        </div>
    </div>
</div>

<style>
.color-option {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px solid transparent;
}
.color-option:hover {
    transform: scale(1.1);
    box-shadow: 0 2px 8px rgba(0,0,0,0.2);
}
.color-option.active {
    border-color: #fff;
    box-shadow: 0 0 0 2px #1E293B;
}
</style>

<script>
function colorPicker() {
    return {
        colors: {{ json_encode($colors) }},
        currentColor: localStorage.getItem('primary-color') || 'blue',
        customColor: '#3B82F6',
        currentColorHex: '#3B82F6',
        
        initColor() {
            this.currentColorHex = this.colors[this.currentColor] || this.customColor;
            this.applyColor(this.currentColor);
        },
        
        setColor(colorName) {
            this.currentColor = colorName;
            this.currentColorHex = this.colors[colorName];
            localStorage.setItem('primary-color', colorName);
            this.applyColor(colorName);
        },
        
        setCustomColor(hex) {
            this.customColor = hex;
            this.currentColorHex = hex;
            localStorage.setItem('primary-color-hex', hex);
            document.documentElement.style.setProperty('--primary-color', hex);
        },
        
        applyColor(colorName) {
            const color = this.colors[colorName] || this.customColor;
            document.documentElement.style.setProperty('--primary-color', color);
        }
    }
}
</script>