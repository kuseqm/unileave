<x-_flash-styles />

@props(['type' => 'info', 'icon' => null, 'title' => null, 'message' => null])

@php
$bannerClass = match($type) {
    'success' => 'success',
    'danger' => 'error',
    'error' => 'error',
    'warning' => 'warning',
    'secondary' => 'secondary',
    default => 'info',
};

$icon = $icon ?? match($type) {
    'success' => 'ti-check-circle',
    'danger' => 'ti-x-circle',
    'error' => 'ti-x-circle',
    'warning' => 'ti-alert-triangle',
    'secondary' => 'ti-info-circle',
    default => 'ti-info-circle',
};

$msg = $message ?? $slot ?? '';
@endphp

@if($msg)
    <div {{ $attributes->merge(['class' => 'flash-banner flash-banner--'.$bannerClass]) }} role="alert">
        <div class="flash-banner-icon"><i class="ti {{ $icon }}"></i></div>
        <div class="flash-banner-text">
            @if($title)
                <strong>{{ $title }}</strong>
                <div class="mt-1">{!! $msg !!}</div>
            @else
                {!! $msg !!}
            @endif
        </div>
    </div>
@endif
