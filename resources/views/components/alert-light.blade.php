<x-_flash-styles />

@php($msg = $message ?? $slot ?? '')

@if($msg)
    <div {{ $attributes->merge(['class' => 'flash-banner flash-banner--light']) }} role="alert">
        <div class="flash-banner-text">{!! $msg !!}</div>
    </div>
@endif
