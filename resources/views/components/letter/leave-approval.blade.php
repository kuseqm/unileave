@props([
    'leaveRequest',
    'staff',
    'university',
    'letterNumber',
    'secureCode',
    'signatory' => null,
    'signatoryTitle' => null,
    'cc' => [],
    'additionalNotes' => null,
    'forPdf' => false,
    'downloadUrl' => null,
    'officeTitle' => null,
    'correspondenceNote' => null,
    'embedded' => false,
    'autoPrint' => false,
    'qrCodeImage' => null,
])

@php
    // Admin-configurable via the Letterhead settings page (University model)
    // - falls back to the original hardcoded pale green for a university
    // that hasn't set one.
    $bgColor = $university->header_footer_bg_color ?? '#e8f4e8';
    $watermarkText = $university->abbreviation ?? 'SJUT';
@endphp

<!DOCTYPE html>
<html>
<head>
    <title>Leave Approval Letter - {{ $letterNumber }}</title>
    <style>
        {{-- The letter always renders as a flat, edge-to-edge page - on
             screen, printed, or as a PDF - never boxed in its own "preview
             window" with a shadow, since that reads as a page floating on
             another page instead of one clean sheet. --}}
        {{-- No page margin at all - the header/footer bands bleed
             edge-to-edge with zero white border. The header flows normally
             at the top of page 1 only. The QR + correspondence footer
             (x-letter.page-bg-strip) is entirely position:fixed - see its
             own comment for the full history of why the QR image and the
             text live in two SEPARATE fixed elements rather than one
             shared wrapper. Reserving margin-box space for a CSS
             counter() page number fought with the full-bleed footer
             background (it started clipping the QR code), so page
             numbering below uses DomPDF's canvas text API instead - it
             draws independently of the box model, so it can't collide
             with it.

             Tried and confirmed NOT to work: a bigger @page margin-top for
             continuation pages only (via :first/:odd/:even). DomPDF parses
             and stores those per-page styles, but they only affect page
             decoration, not the actual reflow/pagination boundary - even
             an extreme 80mm value produced zero change in where page 2
             starts. So continuation pages get no CSS-driven top margin;
             instead every paragraph below is page-break-inside: avoid, so
             a break can only fall between paragraphs (where the existing
             8px gap gives a little breathing room) rather than mid-line. --}}
        @page {
            size: A4;
            margin: 0;
        }

        @media print {
            .no-print { display: none; }
            .watermark { opacity: 0.1; }
        }

        body {
            background: #fff;
            margin: 0;
            padding: 0;
            font-family: Arial, Helvetica, sans-serif;
        }

        /* No background here (was "background: white") - body already
           has one, and this one being opaque was blocking
           x-letter.page-bg-strip's green from showing through beneath
           letter content in the small band right above the footer.
           .letter-container's own box paints BEHIND its children
           regardless (normal stacking), but it comes AFTER page-bg-strip
           in the DOM, so an opaque background here painted right over
           page-bg-strip everywhere .letter-container's box extends.
           (Re-tested with this background restored after the QR moved
           fully into page-bg-strip - made no difference to the QR
           rendering either way, so this removal is purely about the
           color strip showing through, unrelated to the QR-in-fixed
           history documented on page-bg-strip.) */
        .letter-container {
            max-width: none;
            margin: 0 auto;
            box-shadow: none;
            position: relative;
            overflow: hidden;
        }
        
        /* Watermark styles now live inline in x-letter.watermark itself
           (rendered outside .letter-container - see that component's own
           comment for why). */

        /* Content wrapper - sits above the watermark and page-bg-strip,
           both earlier siblings in the DOM. */
        .letter-content {
            position: relative;
            z-index: 1;
            background: transparent;
        }
        
        /* Header band - flows normally at the top of page 1. DomPDF does
           not reliably keep a position:fixed element that contains nested
           tables (the logo|title table, plus the 4-column contact table
           inside it) - it rendered fine on screen but silently vanished
           from the actual downloaded PDF. */
        .header-section {
            background-color: {{ $bgColor }};
            padding: 10px 30px 8px 30px;
            margin: 0;
        }

        /* Plain original padding - no bottom reservation. An earlier
           attempt at this whole problem used a position:fixed text-only
           footer plus a bottom-padding reservation here to keep content
           from running under it; every reservation value tried (55/70/
           110px) introduced a NEW DomPDF artifact (a seam torn through
           the footer text, the text vanishing outright, or a needless
           page 2) - all reading as DomPDF's fixed-position/pagination
           math getting unstable near a 1-vs-2-page boundary. That whole
           approach is gone now (see x-letter.page-bg-strip instead) -
           nothing here needs reserved space any more. */
        .letter-body-section {
            padding: 10px 30px 10px 30px;
        }

        .no-print {
            text-align: center;
            margin-bottom: 20px;
            padding: 10px;
            background: #f0f0f0;
            border-radius: 5px;
        }
        
        .no-print button {
            padding: 8px 16px;
            margin: 0 5px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
        }
        
        .btn-print { background: #4CAF50; color: white; }
        .btn-pdf { background: #f44336; color: white; }
        .btn-close { background: #666; color: white; }
    </style>
</head>
<body>
    @unless($forPdf || $embedded)
    <div class="no-print">
        <button class="btn-print" onclick="window.print()">
            🖨️ Print Letter
        </button>
        <button class="btn-pdf" onclick="downloadPDF()">
            📄 Download PDF
        </button>
        <button class="btn-close" onclick="window.close()">
            ❌ Close
        </button>
    </div>
    @endunless

    {{-- Very first thing in <body>, before .letter-container - see its own
         comment for why placement matters (paints behind normal-flow
         content, not on top of it). Only rendered for the PDF - the live
         browser preview gets a second, normal-flow call to the same
         component further down, right where a footer visually belongs;
         see x-letter.page-bg-strip's own $forPdf comment. --}}
    @if($forPdf)
    <x-letter.page-bg-strip
        :forPdf="true"
        :color="$bgColor"
        :accentColor="$university->secondary_line_color ?? '#C030FC'"
        :textColor="$university->primary_line_color ?? '#183C90'"
        :correspondenceNote="$correspondenceNote ?? 'All correspondence should be addressed to the Vice Chancellor'"
        :qrCodeImage="$qrCodeImage"
    />
    @endif
    <x-letter.watermark :text="$watermarkText" />

    <div class="letter-container">

        <!-- Letter Content -->
        <div class="letter-content">
            <!-- Header + Reference Section with Background -->
            <div class="header-section">
                <x-letter.header :university="$university" :forPdf="$forPdf" :officeTitle="$officeTitle" />
                <x-letter.reference
                    :referenceNumber="$letterNumber"
                    :staffId="$staff->staff_id"
                    :secureCode="$secureCode"
                />
            </div>

            <!-- Recipient & Letter Body (White background) -->
            <div class="letter-body-section">
                <x-letter.recipient :staff="$staff" />
                <x-letter.subject subject="APPROVAL OF LEAVE REQUEST" />
                <x-letter.leave-body
                    :leaveRequest="$leaveRequest"
                    :staff="$staff"
                    :additionalNotes="$additionalNotes"
                />
                <x-letter.signature
                    :signatory="$signatory"
                    :signatoryTitle="$signatoryTitle"
                    :cc="$cc"
                    :signatureImage="$signatureImage"
                />
                @unless($forPdf)
                <x-letter.page-bg-strip
                    :forPdf="false"
                    :color="$bgColor"
                    :accentColor="$university->secondary_line_color ?? '#C030FC'"
                    :textColor="$university->primary_line_color ?? '#183C90'"
                    :correspondenceNote="$correspondenceNote ?? 'All correspondence should be addressed to the Vice Chancellor'"
                    :qrCodeImage="$qrCodeImage"
                />
                @endunless
            </div>
        </div>
    </div>

    <script>
        function downloadPDF() {
            window.location.href = '{{ $downloadUrl ?? route("letters.pdf", $leaveRequest->id) }}';
        }

        @if($autoPrint)
            // Reached via the show page's own Print button, which already
            // hid this page's toolbar (embedded=1) - go straight to the
            // print dialog instead of leaving the tab sitting there blank.
            window.onload = function () { window.print(); };
        @endif
    </script>

    @if($forPdf)
    {{-- Page number, page 2 onward only, never on page 1 - drawn straight
         onto the canvas at a fixed spot instead of through the normal box
         model, so it can never fight the full-bleed footer for space the
         way the CSS counter()/@bottom-center approach did (that clipped
         the QR code). $PAGE_NUM/$pdf are injected by DomPDF itself. --}}
    <script type="text/php">
        if (isset($pdf) && $PAGE_NUM > 1) {
            $pdf->page_text(288, 818, "{PAGE_NUM}", null, 9, [0.4, 0.4, 0.4]);
        }
    </script>
    @endif
</body>
</html>