@props([
    'student',
    'university',
    'letterNumber',
    'secureCode',
    'title',
    'subject' => null,
    'recipientAddress' => null,
    'recipientHeading' => null,
    'bodyContent',
    'numbered' => false,
    'officeTitle' => null,
    'signatory' => null,
    'signatoryTitle' => null,
    'signatureImage' => null,
    'forPdf' => false,
    'downloadUrl' => null,
    'correspondenceNote' => null,
    'embedded' => false,
    'autoPrint' => false,
    'qrCodeImage' => null,
])

{{-- Generalized from the original graduation-confirmation.blade.php to
     cover every single-student alumni letter type (Confirmation,
     Introduction, English Proficiency, Recommendation) - same
     header/footer/QR mechanics throughout, see leave-approval.blade.php
     for the full history of why the layout is shaped this way. --}}
@php
    $bgColor = $university->header_footer_bg_color ?? '#e8f4e8';
    $watermarkText = $university->abbreviation ?? 'SJUT';
@endphp

<!DOCTYPE html>
<html>
<head>
    <title>{{ $title }} - {{ $letterNumber }}</title>
    <style>
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

        .letter-container {
            max-width: none;
            margin: 0 auto;
            box-shadow: none;
            position: relative;
            overflow: hidden;
        }

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
        <button class="btn-print" onclick="window.print()">🖨️ Print Letter</button>
        <button class="btn-pdf" onclick="downloadPDF()">📄 Download PDF</button>
        <button class="btn-close" onclick="window.close()">❌ Close</button>
    </div>
    @endunless

    @if($forPdf)
    <x-letter.page-bg-strip
        :forPdf="true"
        :color="$bgColor"
        :accentColor="$university->secondary_line_color ?? '#C030FC'"
        :textColor="$university->primary_line_color ?? '#183C90'"
        :correspondenceNote="$correspondenceNote ?? 'All correspondence should be addressed to the Vice Chancellor'"
        :qrCodeImage="$qrCodeImage"
        :verifyText="preg_replace('#^https?://#', '', route('verify.index'))"
    />
    @endif
    <x-letter.watermark :text="$watermarkText" />

    <div class="letter-container">
        <div class="letter-content">
            <div class="header-section">
                <x-letter.header :university="$university" :forPdf="$forPdf" :officeTitle="$officeTitle" />
                <x-letter.reference
                    :referenceNumber="$letterNumber"
                    :staffId="$student->registration_number"
                    :secureCode="$secureCode"
                />
            </div>

            <div class="letter-body-section">
                <x-letter.recipient :customAddress="$recipientAddress" :heading="$recipientHeading" />
                @if($subject)
                <x-letter.subject :subject="$subject" />
                @endif
                <x-letter.alumni-letter-body :bodyContent="$bodyContent" :numbered="$numbered" />
                <x-letter.signature
                    :signatory="$signatory"
                    :signatoryTitle="$signatoryTitle"
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
                    :verifyText="preg_replace('#^https?://#', '', route('verify.index'))"
                />
                @endunless
            </div>
        </div>
    </div>

    <script>
        function downloadPDF() {
            window.location.href = '{{ $downloadUrl }}';
        }

        @if($autoPrint)
            window.onload = function () { window.print(); };
        @endif
    </script>

    @if($forPdf)
    <script type="text/php">
        if (isset($pdf) && $PAGE_NUM > 1) {
            $pdf->page_text(288, 818, "{PAGE_NUM}", null, 9, [0.4, 0.4, 0.4]);
        }
    </script>
    @endif
</body>
</html>
