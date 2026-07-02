@props([
    'leaveRequest',
    'staff',
    'university',
    'letterNumber',
    'secureCode',
    'signatory' => null,
    'signatoryTitle' => null,
    'cc' => [],
    'additionalNotes' => null
])

@php
    // Force the color you want for both header and footer
    $bgColor = '#e8f4e8'; // Very pale green
    $watermarkText = $university->abbreviation ?? 'SJUT';
@endphp

<!DOCTYPE html>
<html>
<head>
    <title>Leave Approval Letter - {{ $letterNumber }}</title>
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
        
        /* Header section with background color */
        .header-section {
            background-color: {{ $bgColor }};
            padding: 20px 30px 15px 30px;
            margin: 0;
        }
        
        /* Footer section with same background color */
        .footer-section {
            background-color: {{ $bgColor }};
            padding: 10px 30px;
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
                    :staffId="$staff->staff_id"
                    :secureCode="$secureCode"
                />
            </div>
            
            <!-- Recipient & Letter Body (White background) -->
            <div style="padding: 20px 30px;">
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
                />
            </div>
            
            <!-- Footer Section with Same Background Color -->
            <div class="footer-section">
                <x-letter.footer :verificationCode="$secureCode" />
            </div>
            
        </div>
    </div>
    
    <script>
        function downloadPDF() {
            window.location.href = '{{ route("letters.pdf", $leaveRequest->id) }}';
        }
    </script>
</body>
</html>