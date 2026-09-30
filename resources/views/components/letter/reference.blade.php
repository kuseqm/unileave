@props([
    'referenceNumber' => null,
    'staffId' => null,
    'secureCode' => null,
    'date' => null,
    // Optional: other wording (e.g. Swahili), a ready-made date (may hold markup), a text size.
    'labels' => [],
    'dateHtml' => null,
    'fontSize' => '10pt',
])

@php
    $words = $labels + ['our_ref' => 'Our Ref:', 'your_ref' => 'Your Ref:', 'date' => 'Date:', 'code' => 'Letter Unique Code:'];

    /**
     * Match SJUT's own letterhead convention exactly, e.g.
     * "Thursday, January 8, 2026" - full weekday, month name, day with no
     * ordinal suffix, year.
     */
    if (! function_exists('formatLetterDate')) {
        function formatLetterDate($date)
        {
            $carbon = $date ? \Carbon\Carbon::parse($date) : now();

            return $carbon->format('l, F j, Y');
        }
    }
@endphp

<div class="letter-reference" style="font-family: Arial, Helvetica, sans-serif; margin-bottom: 10px; font-size: {{ $fontSize }};">

    {{-- Table, not flex - DomPDF's CSS 2.1 engine renders flex unreliably
         (it rendered fine on screen but left-aligned/stacked in the actual
         downloaded PDF). Left column: Our Ref / Your Ref. Right column,
         right-aligned: Date / Letter Unique Code. --}}
    <table style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="text-align: left; vertical-align: top;">
                <div><strong>{{ $words['our_ref'] }}</strong> {{ $referenceNumber ?? 'N/A' }}</div>
                <div style="margin-top: 5px;">
                    <strong>{{ $words['your_ref'] }}</strong>
                    @if($staffId)
                        {{ $staffId }}
                    @else
                        <span style="color: #999;">—</span>
                    @endif
                </div>
            </td>
            <td style="text-align: right; vertical-align: top;">
                <div><strong>{{ $words['date'] }}</strong> {!! $dateHtml ?? e(formatLetterDate($date)) !!}</div>
                <div style="margin-top: 5px;">
                    <strong>{{ $words['code'] }}</strong>
                    @if($secureCode)
                        <span style="background: #f0f0f0; padding: 2px 5px; font-family: monospace; font-size: 9pt; letter-spacing: 1px;">
                            {{ $secureCode }}
                        </span>
                    @else
                        <span style="color: #999;">—</span>
                    @endif
                </div>
            </td>
        </tr>
    </table>

</div>