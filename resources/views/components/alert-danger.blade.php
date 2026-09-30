<x-_flash-styles />

@php
$useSession = $session ?? true;
$icon = $icon ?? 'ti-x';
$__dangerSessionRendered = request()->attributes->get('__danger_session_alert_rendered', false);
if ($useSession && ! $__dangerSessionRendered) {
    request()->attributes->set('__danger_session_alert_rendered', true);
}
@endphp

@if($useSession && ! $__dangerSessionRendered && session('error'))
    <div {{ $attributes->merge(['class' => 'flash-banner flash-banner--error']) }} role="alert">
        <div class="flash-banner-icon"><i class="ti {{ $icon }}"></i></div>
        <div class="flash-banner-text">{{ session('error') }}</div>
        <button type="button" class="btn-close" style="font-size: 0.55rem;" aria-label="Dismiss" onclick="this.closest('.flash-banner').remove()"></button>
    </div>
@elseif(! $useSession && ($message ?? $slot ?? ''))
    <div {{ $attributes->merge(['class' => 'flash-banner flash-banner--error']) }} role="alert">
        <div class="flash-banner-icon"><i class="ti {{ $icon }}"></i></div>
        <div class="flash-banner-text">{!! $message ?? $slot !!}</div>
        @if($dismissible ?? false)
        <button type="button" class="btn-close" style="font-size: 0.55rem;" aria-label="Dismiss" onclick="this.closest('.flash-banner').remove()"></button>
        @endif
    </div>
@endif
