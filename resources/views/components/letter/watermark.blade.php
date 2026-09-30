@props([
    'text' => 'SJUT',
])

{{-- Direct child of <body>, a sibling of .letter-container rather than
     nested inside it - same root cause and fix as x-letter.page-bg-strip
     (see its own comment for the full story): .letter-container has
     position:relative + overflow:hidden, and DomPDF clips a position:fixed
     descendant to that ancestor's own content-box height instead of the
     true page. The watermark used to live inside .letter-container, so on
     any letter shorter than a full page it only tiled down to wherever
     the letter's own content ended - the same "content doesn't fill the
     page" symptom the footer had, just for the watermark texture instead
     of a color band. User spotted this directly: "the watermark hasn't
     covered the letter properly for short and long letters."

     Placed BEFORE .letter-container in the DOM (like page-bg-strip), so
     the real letter content - which comes after - paints on top of it
     rather than being hidden behind it (same proven ordering rule).
     top/left percentages are relative to this element's own box, which is
     now genuinely the full page (position:fixed; inset:0), not clipped -
     so the same 12x6 tiling math that used to stop partway down a short
     letter now correctly reaches every corner of every letter, short or
     long, one page or several (it repeats per page automatically, like
     every other position:fixed element on this letterhead). --}}
<div class="watermark" style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; pointer-events: none; z-index: 0; overflow: hidden;">
    @for($row = 0; $row < 12; $row++)
        @for($col = 0; $col < 6; $col++)
            <div class="watermark-text" style="position: absolute; top: {{ $row * 9 }}%; left: {{ $col * 20 - ($row % 2 === 0 ? 0 : 10) }}%; font-size: 16px; font-weight: bold; color: rgba(0, 0, 0, 0.045); white-space: nowrap; transform: rotate(-35deg); letter-spacing: 2px;">{{ $text }}</div>
        @endfor
    @endfor
</div>
