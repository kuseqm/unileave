@props([
    'value', 
    'label', 
    'subtext' => null, 
    'color' => 'primary',
    'icon' => null,
    'trend' => null,
    'trendValue' => null,
    'link' => null
])

<div class="col-6 col-md-3">
    <div class="card rounded-sm text-center h-100" style="background: white; transition: transform 0.2s ease, box-shadow 0.2s ease; {{ $link ? 'cursor: pointer;' : '' }}">
        <div class="card-body py-2 px-2">
            @if($icon)
            <div class="mb-1">
                <i class="ti ti-{{ $icon }} text-{{ $color }}" style="font-size: 1.2rem;"></i>
            </div>
            @endif
            <div class="text-{{ $color }} fw-bold" style="font-size: 1.2rem;">{{ $value }}</div>
            <div class="text-muted" style="font-size: 0.65rem;">{{ $label }}</div>
            @if($subtext)
            <div class="small mt-1"><span class="text-muted" style="font-size: 0.6rem;">{{ $subtext }}</span></div>
            @endif
            @if($trend)
            <div class="small mt-1">
                <span class="text-{{ $trend == 'up' ? 'success' : 'danger' }}" style="font-size: 0.6rem;">
                    <i class="ti ti-arrow-{{ $trend == 'up' ? 'up' : 'down' }}"></i> {{ $trendValue }}
                </span>
            </div>
            @endif
        </div>
        @if($link)
        <a href="{{ $link }}" class="stretched-link"></a>
        @endif
    </div>
</div>

<style>
/* Hover effect only for cards with links */
.col-6.col-md-3 .card:has(a.stretched-link) {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.col-6.col-md-3 .card:has(a.stretched-link):hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}
</style>