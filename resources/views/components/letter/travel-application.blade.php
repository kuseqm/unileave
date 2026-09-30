@props([
    'travelRequest',
    'staff',
    'university',
    'letterContent' => null,
    'handoverNotes' => null,
    'signatureImage' => null,
    'forPdf' => false,
    'embedded' => false,
    'isPreview' => false,
    'autoPrint' => false,
    'downloadUrl' => null,
    'qrCodeImage' => null,
    'secureCode' => null,
])

@php
    // Same full-bleed header/footer color and watermark treatment as the
    // leave application letter, for a consistent letterhead across letter
    // types. Admin-configurable via the Letterhead settings page.
    $bgColor = $university->header_footer_bg_color ?? '#e8f4e8';
    $watermarkText = $university->abbreviation ?? 'SJUT';
    $signatoryTitle = $staff->job_title ?: ($staff->department->name ?? '');
@endphp

<!DOCTYPE html>
<html>
<head>
    <title>Travel Permission Application Letter</title>
    <style>
        {{-- Mirrors x-letter.leave-application's layout exactly - see that
             component for the full reasoning behind the fixed footer /
             flowing header / watermark choices; this is the same
             letterhead, just a travel-specific body. --}}
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

        /* No background here (was "background: white") - see
           leave-approval.blade.php's matching CSS comment: an opaque
           background here blocked x-letter.page-bg-strip's green from
           showing through in the small band right above the footer. */
        .letter-container {
            max-width: none;
            margin: 0 auto;
            box-shadow: none;
            position: relative;
            overflow: hidden;
        }

        /* Watermark styles now live inline in x-letter.watermark itself
           (rendered outside .letter-container - see its own comment). */

        .letter-content {
            position: relative;
            z-index: 1;
            background: transparent;
        }

        .header-section {
            background-color: {{ $bgColor }};
            padding: 10px 30px 8px 30px;
            margin: 0;
        }

        /* Plain original padding - no bottom reservation needed. See
           leave-approval.blade.php's matching CSS comment for the full
           story of why (a reservation + a position:fixed text footer was
           tried first and proven unstable in DomPDF); replaced by
           x-letter.page-bg-strip instead, which needs nothing reserved
           here. */
        .letter-body-section {
            padding: 10px 30px 10px 30px;
        }

        .preview-banner {
            position: fixed;
            top: 40px;
            left: -60px;
            width: 320px;
            transform: rotate(-35deg);
            text-align: center;
            background: rgba(200, 30, 30, 0.85);
            color: #fff;
            font-size: 9pt;
            font-weight: bold;
            letter-spacing: 1px;
            padding: 3px 0;
            z-index: 2;
            pointer-events: none;
        }

        .no-print {
            text-align: center;
            margin-bottom: 20px;
            padding: 10px;
            background: #f0f0f0;
            border-radius: 5px;
        }
    </style>
</head>
<body>

    @if($forPdf)
    <x-letter.page-bg-strip
        :forPdf="true"
        :color="$bgColor"
        :accentColor="$university->secondary_line_color ?? '#C030FC'"
        :textColor="$university->primary_line_color ?? '#183C90'"
        :correspondenceNote="'All correspondence should be addressed to the Human Resources Manager'"
        :qrCodeImage="$qrCodeImage"
    />
    @endif
    <x-letter.watermark :text="$watermarkText" />

    <div class="letter-container">

        @if($isPreview)
            <div class="preview-banner">NOT YET SUBMITTED</div>
        @endif

        <div class="letter-content">
            <div class="header-section">
                <x-letter.header :university="$university" :forPdf="$forPdf" :officeTitle="$staff->department->name ?? null" />
                <x-letter.reference :staffId="$staff->staff_id" :secureCode="$secureCode" />
            </div>

            <div class="letter-body-section">
                <x-letter.recipient :customAddress="'The Human Resources Manager'" />
                <x-letter.subject subject="APPLICATION FOR TRAVEL PERMISSION" />
                <x-letter.travel-application-body
                    :travelRequest="$travelRequest"
                    :staff="$staff"
                    :letterContent="$letterContent"
                    :handoverNotes="$handoverNotes"
                />
                <x-letter.signature
                    :signatory="$staff->full_name"
                    :signatoryTitle="$signatoryTitle"
                    :signatureImage="$signatureImage"
                />
                @unless($forPdf)
                <x-letter.page-bg-strip
                    :forPdf="false"
                    :color="$bgColor"
                    :accentColor="$university->secondary_line_color ?? '#C030FC'"
                    :textColor="$university->primary_line_color ?? '#183C90'"
                    :correspondenceNote="'All correspondence should be addressed to the Human Resources Manager'"
                    :qrCodeImage="$qrCodeImage"
                />
                @endunless
            </div>
        </div>
    </div>

    <script>
        @if($autoPrint)
            window.onload = function () { window.print(); };
        @endif
    </script>

</body>
</html>
