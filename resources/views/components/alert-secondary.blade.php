<x-_flash-styles />

@php($msg = $message ?? $slot ?? '')

@if($msg)
    <div class="flash-banner flash-banner--secondary" role="alert">
        <div class="flash-banner-icon"><i class="ti ti-info-circle"></i></div>
        <div class="flash-banner-text">{!! $msg !!}</div>
        @if($dismissible ?? false)
        <button type="button" class="btn-close" style="font-size: 0.55rem;" aria-label="Dismiss" onclick="this.closest('.flash-banner').remove()"></button>
        @endif
    </div>
@endif
