@props([
    'color' => '#e8f4e8',
    'height' => '105px',
    'correspondenceNote' => null,
    'qrCodeImage' => null,
    // Optional short address printed under "Scan to verify" (e.g. the
    // public verification page host) for readers who can't scan the QR.
    'verifyText' => null,
    // Most consumer printers physically cannot print into the outer few
    // mm of a page (their own hardware margin, independent of anything
    // set in CSS/DomPDF) - text/images sitting flush at the literal page
    // edge risk being clipped there. The color strip itself is fine right
    // to the edge (a slightly short colored band is invisible - nobody
    // notices a few missing mm of plain background), but real content
    // needs real clearance. ~15px (~4mm) is a conservative, commonly-cited
    // safe margin - this is the correspondence text's own offset.
    'safeBottom' => '15px',
    // The QR block sits ABOVE the correspondence text, not sharing its
    // baseline - both blocks are position:fixed with independent "bottom"
    // values (they don't stack relative to each other the way normal-flow
    // siblings would), so without a bigger offset here the QR's own
    // "Scan to verify" caption would sit at the same height as the pink
    // rule and visually collide with it. 48px clears the text block's own
    // ~30px height (border + padding + one line at 8pt) above safeBottom,
    // plus a small gap.
    'qrBottom' => '48px',
    // Admin-configurable via the Letterhead settings page (same
    // University fields the header's own crest lines already use) -
    // these happened to already be set to exactly these values for SJUT,
    // so wiring them up here is a no-op visually for the one real
    // university in use, but makes a genuinely different institution's
    // footer follow its own brand colors instead of SJUT's hardcoded
    // pink/blue.
    'accentColor' => '#C030FC',
    'textColor' => '#183C90',
    // Only the PDF render needs position:fixed at all - that whole
    // mechanism exists to solve DomPDF-specific print/pagination problems
    // (see the rest of this comment) that a real browser doesn't have.
    // User asked specifically about the live browser preview: a
    // position:fixed footer there behaves as a "sticky" element pinned to
    // the viewport while the rest of the letter scrolls underneath it -
    // technically correct CSS, but the user wants it to scroll normally
    // with the page instead, matching the header. When forPdf is false,
    // this renders a single, simple, ordinary normal-flow block instead
    // (see below) - the caller places THAT call at the end of the letter
    // body, not before .letter-container, since normal-flow content has
    // to sit where it visually belongs in the document, not at the top.
    'forPdf' => false,
])

{{-- THREE separate position:fixed elements, all anchored to the page
     bottom - not one element nesting the others. Rendered by the caller
     as the very first thing inside <body>, before .letter-container
     opens.

     1. The color-only strip: no text, no image, nothing that could ever
        hit a DomPDF rendering bug. Proven by direct testing to paint
        BEHIND later normal-flow content rather than covering it. Height
        (105px) is tuned tight, on purpose - just enough to cover the QR
        block (which sits at $qrBottom, higher up than the text) and the
        text block below it, with the strip's own top edge landing right
        where the QR's own top edge is. User asked for exactly this after
        seeing the CC list's last line sitting inside the green at a
        looser height (140px) - too tall and the green shows up behind
        letter content well above the footer that should stay white
        (confirmed at 300px/450px too, reached into the CC list and even
        mid-paragraph on a longer letter, back when the QR still lived in
        normal flow and a taller strip was the only way to reach it). If
        the QR block's own size/position ($qrBottom) ever changes, this
        height needs re-tuning to match - it's deliberately coupled to
        those values now, not an independent "generous" number.

     2 & 3. The QR image and the correspondence text (pink rule + note) -
        each in its OWN separate position:fixed div, not sharing one
        parent, and no longer needing to. This split matters: earlier in
        this investigation, an <img> combined with text inside ONE shared
        position:fixed wrapper (the original .pinned-footer design)
        reliably made the image vanish once preceded by enough
        page-break-inside:avoid content earlier on the page (the
        leave-details table + numbered paragraphs + the signature image)
        - reproduced multiple times, confirmed independent of whether the
        image was a PNG <img> (file path or base64) or inline <svg>
        markup, confirmed independent of .letter-container's own
        background. Giving the QR its own separate, single-purpose
        position:fixed div (not sharing a parent with the text block) was
        the one change that finally made it render reliably - verified
        against real production letters (Approval #38), not just a
        synthetic reproduction, and re-verified with .letter-container's
        background both present and absent to rule that variable out
        specifically. Not fully bisected beyond that - if this ever
        breaks again, re-nest QR and text into a single wrapper as the
        first thing to suspect, then re-read this whole comment before
        spending hours re-discovering the same history.

        Because BOTH are now genuinely position:fixed (not just the
        text), the QR no longer lives in normal flow at all, and no
        longer moves with how much letter content precedes it - it
        repeats on every page of a multi-page letter at the exact same
        spot, exactly like the text, completely independent of content
        length. This also means this whole footer group's total height
        is now fixed and predictable regardless of the letter - the
        earlier "how tall should this strip be" tension (a short strip
        leaves the QR floating disconnected on a short letter; a tall
        strip bleeds green into unrelated content on a long letter) no
        longer applies, since the QR isn't chasing content anymore.

     None of this applies when $forPdf is false (the live browser
     preview) - see the $forPdf prop comment above for the simpler
     normal-flow block used instead. --}}
@if($forPdf)
<div style="position: fixed; bottom: 0; left: 0; right: 0; height: {{ $height }}; background-color: {{ $color }};"></div>
@if($qrCodeImage)
<div style="position: fixed; bottom: {{ $qrBottom }}; right: 0; padding: 0 30px;">
    <img src="{{ $qrCodeImage }}" alt="Verification QR code" style="width: 42px; height: 42px;">
    <p style="margin: 2px 0 0 0; font-size: 6pt; color: #888; text-align: right;">Scan to verify</p>
    @if($verifyText)
    <p style="margin: 0; font-size: 6pt; color: #888; text-align: right;">or visit {{ $verifyText }}</p>
    @endif
</div>
@endif
@if($correspondenceNote)
<div style="position: fixed; bottom: {{ $safeBottom }}; left: 0; right: 0; font-family: Arial, Helvetica, sans-serif; padding: 4px 30px 0;">
    <div style="border-top: 3px solid {{ $accentColor }}; padding: 4px 0 0;">
        <p style="margin: 0; text-align: center; font-style: italic; font-size: 8pt; color: {{ $textColor }};">
            {{ $correspondenceNote }}
        </p>
    </div>
</div>
@endif
@else
{{-- Live browser preview only - a single, ordinary, normal-flow block.
     None of the position:fixed machinery above is needed here: a real
     browser doesn't have DomPDF's per-page repetition, its "white sliver"
     footer bug, or its image-in-fixed-position bug, so there's nothing to
     work around. Placed by the caller at the natural end of the letter
     body (after the signature), where a footer visually belongs, and it
     simply scrolls away with the rest of the page like everything else -
     no "sticky" behavior. --}}
<div style="margin: 12px -30px 0; padding: 10px 30px; background-color: {{ $color }};">
    @if($qrCodeImage)
    <div style="text-align: right; margin-bottom: 8px;">
        <img src="{{ $qrCodeImage }}" alt="Verification QR code" style="width: 42px; height: 42px;">
        <p style="margin: 2px 0 0 0; font-size: 6pt; color: #888;">Scan to verify</p>
        @if($verifyText)
        <p style="margin: 0; font-size: 6pt; color: #888;">or visit {{ $verifyText }}</p>
        @endif
    </div>
    @endif
    @if($correspondenceNote)
    <div style="font-family: Arial, Helvetica, sans-serif; border-top: 3px solid {{ $accentColor }}; padding: 4px 0 0;">
        <p style="margin: 0; text-align: center; font-style: italic; font-size: 8pt; color: {{ $textColor }};">
            {{ $correspondenceNote }}
        </p>
    </div>
    @endif
</div>
@endif
