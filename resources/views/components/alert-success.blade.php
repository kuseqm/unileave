<x-_flash-styles />

@php
$__successAlertRendered = request()->attributes->get('__success_alert_rendered', false);
if (! $__successAlertRendered) {
    request()->attributes->set('__success_alert_rendered', true);
}
@endphp

@if(! $__successAlertRendered && session('success_details'))
    @php($sd = session('success_details'))
    <div class="flash-banner flash-banner--success" role="alert">
        <div class="flash-banner-icon"><i class="ti ti-check"></i></div>
        <div class="flash-banner-text"><strong>{{ $sd['title'] ?? 'Success' }}.</strong> {{ collect($sd['lines'] ?? [])->pluck('text')->implode(' · ') }}</div>
        <button type="button" class="btn-close" style="font-size: 0.55rem;" aria-label="Dismiss" onclick="this.closest('.flash-banner').remove()"></button>
    </div>
@elseif(! $__successAlertRendered && (session('success') || session('status')))
    <div class="flash-banner flash-banner--success" role="alert">
        <div class="flash-banner-icon"><i class="ti ti-check"></i></div>
        <div class="flash-banner-text">{{ session('success') ?: session('status') }}</div>
        <button type="button" class="btn-close" style="font-size: 0.55rem;" aria-label="Dismiss" onclick="this.closest('.flash-banner').remove()"></button>
    </div>
@endif

@if(! $__successAlertRendered && (session('success_details') || session('success') || session('status')))
{{-- A form on a long page posts and redirects back to the same URL, and the
     browser (Firefox especially) restores the old scroll position - so the
     banner, at the top, ends up above the visible area and looks like it
     never appeared. Bring the reader back to the top so the result is seen. --}}
<script>
(function () {
    // The app scrolls inside .content-area, not the window.
    const showBanner = function () {
        window.scrollTo(0, 0);
        const area = document.querySelector('.content-area');
        if (area) { area.scrollTop = 0; }
    };
    showBanner();
    document.addEventListener('DOMContentLoaded', showBanner);
    window.addEventListener('load', function () { showBanner(); setTimeout(showBanner, 120); });
})();
</script>
@endif
