@props([
    'letter',
    'content',
    'university',
    'forPdf' => false,
    'downloadUrl' => null,
    'embedded' => false,
    'autoPrint' => false,
    'qrCodeImage' => null,
    'isDraft' => false,
])

{{-- Certificate / transcript verification letter - the header, footer, QR and
     watermark mechanics are the same as alumni-letter.blade.php (see
     leave-approval.blade.php for why they are shaped the way they are); only
     the body differs, and its wording comes from VerificationLetterWording. --}}
@php
    use App\Services\VerificationLetterWording;

    $bgColor = $university->header_footer_bg_color ?? '#e8f4e8';
    $signatory = $letter->signatory;
    $language = $letter->language;
    $wording = app(VerificationLetterWording::class);

    // The letterhead's office name and contact line follow whoever signs.
    $headerUniversity = clone $university;
    if ($signatory->phone) {
        $headerUniversity->phone = $signatory->phone;
        $headerUniversity->phone_secondary = null;
    }
    if ($signatory->email) {
        $headerUniversity->email = $signatory->email;
    }

    $recipientLines = collect([
        $letter->recipient_title,
        $letter->recipient_organisation,
        ...preg_split('/\r\n|\r|\n/', (string) $letter->recipient_address),
        $letter->recipient_city,
        $letter->recipient_country,
    ])->map(fn ($line) => trim((string) $line))->filter()->map(fn ($line) => mb_strtoupper($line));

    // DomPDF's core fonts have no curly apostrophe, so it prints "???".
    $plain = fn ($text) => str_replace('’', "'", (string) $text);
    $numberedCell = 'width: 34px; vertical-align: top; padding: 0;';
@endphp

<!DOCTYPE html>
<html>
<head>
    <title>{{ $letter->letter_number }}</title>
    <style>
        @page { size: A4; margin: 0; }
        @media print { .no-print { display: none; } .watermark { opacity: 0.1; } }

        body { background: #fff; margin: 0; padding: 0; font-family: Arial, Helvetica, sans-serif; }
        .letter-container { max-width: none; margin: 0 auto; box-shadow: none; position: relative; overflow: hidden; }
        .letter-content { position: relative; z-index: 1; background: transparent; }
        .header-section { background-color: {{ $bgColor }}; padding: 10px 30px 8px 30px; margin: 0; }
        .letter-body-section { padding: 10px 30px 10px 30px; font-size: 11pt; line-height: 1.35; color: #000; }
        .verification-table { width: 100%; border-collapse: collapse; margin: 6px 0 12px; font-size: 11pt; }
        .verification-table th, .verification-table td { border: 1px solid #000; padding: 2px 5px; vertical-align: top; text-align: left; }
        .verification-table th { background: #f0f0f0; font-weight: normal; }
        .no-print { text-align: center; margin-bottom: 20px; padding: 10px; background: #f0f0f0; border-radius: 5px; }
        .no-print button { padding: 8px 16px; margin: 0 5px; border: none; border-radius: 4px; cursor: pointer; font-size: 12px; }
        .btn-print { background: #4CAF50; color: white; }
        .btn-pdf { background: #f44336; color: white; }
        .btn-close { background: #666; color: white; }
        p.numbered { margin: 0 0 9px; text-align: justify; }
    </style>
</head>
<body>
    @unless($forPdf || $embedded)
    <div class="no-print">
        <button class="btn-print" onclick="window.print()">🖨️ Print Letter</button>
        @if($downloadUrl)<button class="btn-pdf" onclick="window.location.href='{{ $downloadUrl }}'">📄 Download PDF</button>@endif
        <button class="btn-close" onclick="window.close()">❌ Close</button>
    </div>
    @endunless

    @if($autoPrint)
    <script>window.onload = function () { window.print(); };</script>
    @endif

    @if($forPdf)
    <x-letter.page-bg-strip
        :forPdf="true"
        :color="$bgColor"
        :accentColor="$university->secondary_line_color ?? '#C030FC'"
        :textColor="$university->primary_line_color ?? '#183C90'"
        correspondenceNote="All correspondence should be addressed to the Vice Chancellor"
        :qrCodeImage="$qrCodeImage"
    />
    @endif
    <x-letter.watermark :text="$isDraft ? 'DRAFT' : ($university->abbreviation ?? 'SJUT')" />

    <div class="letter-container">
        <div class="letter-content">
            <div class="header-section">
                <x-letter.header :university="$headerUniversity" :forPdf="$forPdf" :officeTitle="$signatory->office_en" />
                <x-letter.reference
                    :referenceNumber="$letter->letter_number"
                    :staffId="$letter->received_reference"
                    :secureCode="$letter->secure_code"
                    :labels="$language === 'sw'
                        ? ['our_ref' => 'Kumb. Yetu:', 'your_ref' => 'Kumb. Yako:', 'date' => 'Tarehe:', 'code' => 'Msimbo wa Barua:']
                        : []"
                    :dateHtml="$wording->date($letter->letter_date, $language)"
                    fontSize="11pt"
                />
                <div style="height: 6px;"></div>
            </div>

            <div class="letter-body-section">
                <div style="margin-bottom: 12px;">
                    @foreach($recipientLines as $line)
                        {{ $line }}<br>
                    @endforeach
                    @if($letter->recipient_email){{ $letter->recipient_email }}<br>@endif
                    @if($letter->ufs)<div style="margin-top: 6px;"><strong>UFS:</strong> {!! nl2br(e($letter->ufs)) !!}</div>@endif
                </div>

                @if($content['subject'])<p style="text-align: center; font-weight: bold; text-decoration: underline; margin: 0 0 12px;">{{ $content['subject'] }}</p>@endif

                <p style="margin: 0 0 9px; text-align: justify; text-indent: 40px;">{!! $content['intro'] !!}</p>

                @foreach($content['before_table'] as $paragraph)
                    {{-- Only the line that carries the number is indented; the rest start at the margin. --}}
                    <p style="margin: 0 0 9px; text-align: justify;">{{ $paragraph['number'] }}.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{!! $paragraph['html'] !!}</p>
                @endforeach

                @if($content['table'])
                <table class="verification-table" @if($content['table']['small']) style="font-size: 10pt;" @endif>
                    <thead>
                        <tr>
                            @foreach($content['table']['headers'] as $header)
                                <th @if(isset($content['table']['widths'][$loop->index])) style="width: {{ $content['table']['widths'][$loop->index] }}px; white-space: nowrap;" @endif>{{ $header }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($content['table']['rows'] as $row)
                        <tr>
                            @foreach($row as $index => $cell)
                                <td style="@if(isset($content['table']['aligned'][$index]))text-align: {{ $content['table']['aligned'][$index] }};@endif @if(isset($content['table']['widths'][$index]) && $index > 0)white-space: nowrap;@endif">{!! $cell !!}</td>
                            @endforeach
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif

                @foreach($content['after_table'] as $paragraph)
                    @if(empty($paragraph['sub']))
                    <p style="margin: 0 0 9px; text-align: justify;">{{ $paragraph['number'] }}.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{!! $paragraph['html'] !!}</p>
                    @else
                    <table style="width: 100%; border-collapse: collapse; margin: 0 0 9px;"><tr>
                        <td style="{{ $numberedCell }}">{{ $paragraph['number'] }}.</td>
                        <td style="text-align: justify; padding: 0;">
                            @if(! empty($paragraph['sub']))
                            <table style="width: 100%; border-collapse: collapse;">
                                @foreach($paragraph['sub'] as $sub)
                                <tr>
                                    <td style="width: 44px; vertical-align: top; padding: 0 0 3px;">{{ $sub['label'] }}</td>
                                    <td style="padding: 0 0 3px; text-align: justify;">
                                        {!! $sub['html'] !!}
                                        @if(! empty($sub['children']))
                                        <table style="width: 100%; border-collapse: collapse; margin-top: 2px;">
                                            @foreach($sub['children'] as $child)
                                            <tr>
                                                <td style="width: 28px; vertical-align: top; padding: 0 0 2px;">{{ $child['label'] }}</td>
                                                <td style="padding: 0 0 2px; font-size: 11pt;">{!! $child['html'] !!}</td>
                                            </tr>
                                            @endforeach
                                        </table>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </table>
                            @endif
                        </td>
                    </tr></table>
                    @endif
                @endforeach

                @if($content['sign_off'])
                    <p style="margin: 14px 0 0;">{{ $content['sign_off'] }}</p>
                @endif

                <div style="margin-top: 42px; line-height: 1.3;">
                    <div style="width: 230px; border-top: 1px solid #000; margin-bottom: 3px;"></div>
                    {{ $plain($signatory->name) }}<br>
                    <strong>{{ $plain($signatory->title($language)) }}</strong><br>
                    @if($signatory->unit($language))<strong>{{ $plain($signatory->unit($language)) }}</strong>@endif
                </div>

                @unless($forPdf)
                <x-letter.page-bg-strip
                    :forPdf="false"
                    :color="$bgColor"
                    :accentColor="$university->secondary_line_color ?? '#C030FC'"
                    :textColor="$university->primary_line_color ?? '#183C90'"
                    correspondenceNote="All correspondence should be addressed to the Vice Chancellor"
                    :qrCodeImage="$qrCodeImage"
                />
                @endunless
            </div>
        </div>
    </div>

    @if($forPdf)
    <script type="text/php">
        if (isset($pdf) && $PAGE_NUM > 1) {
            $pdf->page_text(288, 818, "{PAGE_NUM}", null, 9, [0.4, 0.4, 0.4]);
        }
    </script>
    @endif
</body>
</html>
