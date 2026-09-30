@props([
    'correspondenceNote' => 'All correspondence should be addressed to the Vice Chancellor',
])

{{-- The verification QR/link used to live here too, but DomPDF would not
     reliably render an <img> inside this position:fixed band (see the
     .footer-section wrapper in each letter template) once the header's own
     table+image existed earlier on the page - it vanished from the real
     PDF while looking fine on screen. That block now lives in normal
     document flow instead - see verification-block.blade.php, placed
     right after the signature in each letter template. This footer stays
     text-only, which DomPDF has never had trouble with here. --}}
@if($correspondenceNote)
<div class="letter-footer" style="font-family: Arial, Helvetica, sans-serif; margin-top: 6px; border-top: 2px solid #C030FC; padding-top: 4px;">
    <p style="margin: 0; text-align: center; font-style: italic; font-size: 8pt; color: #183C90;">
        {{ $correspondenceNote }}
    </p>
</div>
@endif
