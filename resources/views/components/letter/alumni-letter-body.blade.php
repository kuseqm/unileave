@props(['bodyContent', 'numbered' => false])

<div class="letter-body" style="font-family: Arial, Helvetica, sans-serif; font-size: 11pt; line-height: 1.5;">
    @if($numbered)
        <x-numbered-paragraphs :text="$bodyContent" :numberFrom="2" />
    @else
        @php
            $lines = collect(preg_split('/\r\n|\r|\n/', $bodyContent ?? ''))
                ->map(fn ($line) => trim($line))
                ->filter()
                ->values();
        @endphp
        @foreach($lines as $line)
            <p style="margin: 0 0 10px 0;">{{ $line }}</p>
        @endforeach
    @endif
</div>
