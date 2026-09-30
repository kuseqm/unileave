<x-_flash-styles />

@php
$useSession = $session ?? true;
$msg = $useSession ? (session('warning') ?? '') : ($message ?? $slot ?? '');
$icon = $icon ?? 'ti-alert-triangle';
@endphp

@if($msg)
    <div {{ $attributes->merge(['class' => 'flash-banner flash-banner--warning']) }} role="alert">
        <div class="flash-banner-icon"><i class="ti {{ $icon }}"></i></div>
        <div class="flash-banner-text">{!! $msg !!}</div>
        @if($dismissible ?? false)
        <button type="button" class="btn-close" style="font-size: 0.55rem;" aria-label="Dismiss" onclick="this.closest('.flash-banner').remove()"></button>
        @endif
    </div>
@endif
