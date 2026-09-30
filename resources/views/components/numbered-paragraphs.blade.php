@props(['text', 'numberFrom' => 2])

@php
    $lines = collect(preg_split('/\r\n|\r|\n/', $text ?? ''))
        ->map(fn ($line) => trim($line))
        ->filter()
        ->values();
    $n = $numberFrom;
@endphp

@if($lines->isEmpty())
    <span class="text-muted">—</span>
@else
    @foreach($lines as $index => $line)
        <p class="mb-1" style="{{ $index === 0 ? 'text-indent: 1.5em;' : '' }}">
            @if($index > 0){{ $n++ }}.&nbsp;&nbsp; @endif{{ $line }}
        </p>
    @endforeach
@endif
