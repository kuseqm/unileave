@props([
    'letter',
    'university',
    'letterNumber',
    'secureCode',
    'signatory' => null,
    'signatoryTitle' => null,
    'cc' => [],
    'bodyComponent' => 'components.letter.body', // Can be overridden
    'bodyProps' => [], // Props to pass to body component
    'subject' => null,
    'recipient' => null,
    'departmentColor' => null // For future department color coding
])

@php
    // For future department color coding
    $headerBgColor = $departmentColor ?? $university->header_footer_bg_color ?? '#f0f8ff';
    $watermarkText = $university->abbreviation ?? 'UNIVERSITY';
    
    // Determine body component
    $bodyView = $bodyComponent;
    
    // Prepare body props
    $bodyData = array_merge($bodyProps, [
        'letter' => $letter,
        'staff' => $recipient ?? $letter->recipient ?? null,
        'additionalNotes' => $bodyProps['additionalNotes'] ?? null
    ]);
@endphp

<!DOCTYPE html>
<html>
<head>
    <title>Official Letter - {{ $letterNumber }}</title>
    <style>
        @media print {
            body { margin: 0; padding: 0; }
            .no-print { display: none; }
            .letter-container { margin: 0; padding: 20px; }
            .watermark { opacity: 0.1; }
        }
        
        body {
            background: #f5f5f5;
            margin: 0;
            padding: 20px;
            font-family: 'Times New Roman', Times, serif;
        }
        
        .letter-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            position: relative;
            overflow: hidden;
        }
        
        /* Watermark Styles */
        .watermark {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            pointer-events: none;
            z-index: 0;
            overflow: hidden;
        }
        
        .watermark-text {
            position: absolute;
            font-size: 80px;
            font-weight: bold;
            color: rgba(0, 0, 0, 0.03);
            white-space: nowrap;
            transform: rotate(-45deg);
            letter-spacing: 5px;
        }
        
        /* Multiple watermarks scattered */
        .watermark-text:nth-child(1) { top: 10%; left: -10%; }
        .watermark-text:nth-child(2) { top: 30%; left: 20%; }
        .watermark-text:nth-child(3) { top: 50%; left: -5%; }
        .watermark-text:nth-child(4) { top: 70%; left: 30%; }
        .watermark-text:nth-child(5) { top: 85%; left: 10%; }
        .watermark-text:nth-child(6) { top: 20%; left: 50%; }
        .watermark-text:nth-child(7) { top: 60%; left: 60%; }
        
        /* Content wrapper - sits above watermark */
        .letter-content {
            position: relative;
            z-index: 1;
            background: transparent;
        }
        
        /* Header section with background color (can be department-specific) */
        .header-section {
            background-color: {{ $headerBgColor }};
            padding: 20px 30px 15px 30px;
            margin: 0;
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
    
    <div class="letter-container">
        
        <!-- Watermark Layer -->
        <div class="watermark">
            <div class="watermark-text">{{ $watermarkText }}</div>
            <div class="watermark-text">{{ $watermarkText }}</div>
            <div class="watermark-text">{{ $watermarkText }}</div>
            <div class="watermark-text">{{ $watermarkText }}</div>
            <div class="watermark-text">{{ $watermarkText }}</div>
            <div class="watermark-text">{{ $watermarkText }}</div>
            <div class="watermark-text">{{ $watermarkText }}</div>
        </div>
        
        <!-- Letter Content (above watermark) -->
        <div class="letter-content">
            
            <!-- Header + Reference Section with Background -->
            <div class="header-section">
                <x-letter.header :university="$university" />
                <x-letter.reference 
                    :referenceNumber="$letterNumber" 
                    :staffId="$recipient->staff_id ?? $letter->recipient->staff_id ?? null"
                    :secureCode="$secureCode"
                />
            </div>
            
            <!-- Recipient & Letter Body -->
            <div style="padding: 20px 30px;">
                @if($recipient)
                    <x-letter.recipient :staff="$recipient" />
                @elseif($letter->recipient)
                    <x-letter.recipient :staff="$letter->recipient" />
                @endif
                
                @if($subject)
                    <x-letter.subject :subject="$subject" />
                @endif
                
                @include($bodyView, $bodyData)
                
                <x-letter.signature 
                    :signatory="$signatory" 
                    :signatoryTitle="$signatoryTitle"
                    :cc="$cc"
                />
            </div>
            
            <!-- Footer -->
            <x-letter.footer :verificationCode="$secureCode" />
            
        </div>
    </div>
    
    <script>
        function downloadPDF() {
            window.location.href = '{{ route("letters.pdf", $letter->id ?? 0) }}';
        }
    </script>
</body>
</html>