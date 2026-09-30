@props(['travelRequest', 'staff', 'letterContent' => null, 'handoverNotes' => null])

@php
    $startDate = $travelRequest->start_date ? \Carbon\Carbon::parse($travelRequest->start_date) : null;
    $endDate = $travelRequest->end_date ? \Carbon\Carbon::parse($travelRequest->end_date) : null;
    $actingStaff = $travelRequest->actingStaff;
    $purposeLabel = $travelRequest->travel_reason === 'official' ? 'official university business' : 'personal reasons';

    // Same convention as the leave letter: opening paragraph unnumbered
    // (just first-line indented), every paragraph after it numbered
    // starting at 2. Coverage of Duties comes first, right after the
    // opener (which already names the acting staff), as its own headed
    // list - not folded into the running numbers, where a second or third
    // line had no visible connection back to "Coverage of Duties". The
    // applicant's own "Letter Content" then follows, ending with the
    // closing paragraph.
    $n = 2;

    $contentLines = collect(preg_split('/\r\n|\r|\n/', $letterContent ?? ''))
        ->map(fn ($line) => trim($line))
        ->filter()
        ->values();

    $handoverLines = collect(preg_split('/\r\n|\r|\n/', $handoverNotes ?? ''))
        ->map(fn ($line) => trim($line))
        ->filter()
        ->values();

    // Lower-case roman numerals for the Coverage of Duties sub-list. Written
    // out by hand rather than left to CSS list-style-type: lower-roman -
    // this letter is also rendered to PDF via DomPDF, whose CSS support is
    // inconsistent enough elsewhere in this app that every other number on
    // this letter is already hand-written the same way, not left to the
    // browser/PDF engine's own list numbering.
    $toRomanNumeral = function (int $number): string {
        $map = ['m' => 1000, 'cm' => 900, 'd' => 500, 'cd' => 400, 'c' => 100, 'xc' => 90, 'l' => 50, 'xl' => 40, 'x' => 10, 'ix' => 9, 'v' => 5, 'iv' => 4, 'i' => 1];
        $result = '';
        foreach ($map as $numeral => $value) {
            while ($number >= $value) {
                $result .= $numeral;
                $number -= $value;
            }
        }

        return $result;
    };
@endphp

<div class="letter-body" style="font-family: Arial, Helvetica, sans-serif; font-size: 12pt; line-height: 1.2; text-align: justify;">

    <!-- Opening Statement (unnumbered, first-line indented) -->
    <p style="margin-bottom: 8px; text-indent: 2.5em; page-break-inside: avoid;">
        I am writing to formally apply for authorization to travel to <strong>{{ $travelRequest->destination ?? '—' }}</strong>
        @if($startDate && $endDate)
            from <strong>{{ $startDate->format('l, F j, Y') }}</strong> to <strong>{{ $endDate->format('l, F j, Y') }}</strong>
        @endif
        for {{ $purposeLabel }}
        @if($actingStaff)
            . I have asked <strong>{{ $actingStaff->full_name }}</strong> to cover my duties during my absence
        @endif
        .
    </p>

    {{-- Every numbered paragraph below gives the number its own fixed-width
         column (an inline-block span), so the text after it always starts
         at the same tab stop whether the number is "2." or "10." - a plain
         run of &nbsp; couldn't do that, since "10." simply takes more room
         than "2.". A wrapped second line is deliberately NOT indented to
         match - it returns to the paragraph's own left edge, same as the
         opening paragraph above. --}}
    @php $numberColumn = 'display: inline-block; width: 2.2em; vertical-align: top;'; @endphp

    <!-- Coverage of Duties: right after the opener, as its own headed list,
         so every line stays visibly grouped under it rather than reading as
         unrelated numbered points once there's more than one. -->
    @if($handoverLines->isNotEmpty())
        <p style="margin: 8px 0 4px; page-break-inside: avoid;"><span style="{{ $numberColumn }}">{{ $n++ }}.</span><strong>Coverage of Duties:</strong></p>
        @foreach($handoverLines as $index => $line)
            <p style="margin: 2px 0 2px 3.5em; page-break-inside: avoid;"><span style="display: inline-block; width: 1.6em; vertical-align: top;">{{ $toRomanNumeral($index + 1) }}.</span>{{ $line }}</p>
        @endforeach
    @endif

    <!-- Applicant's own letter content, continuing the numbered sequence -->
    @foreach($contentLines as $line)
        <p style="margin: 8px 0; page-break-inside: avoid;"><span style="{{ $numberColumn }}">{{ $n++ }}.</span>{{ $line }}</p>
    @endforeach

    <!-- Closing -->
    <p style="margin-top: 8px; margin-bottom: 0; page-break-inside: avoid;"><span style="{{ $numberColumn }}">{{ $n++ }}.</span>I kindly request your authorization for this travel.</p>

</div>
