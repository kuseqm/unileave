<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $letter->letter_number ?? 'Letter' }} - {{ $letter->title }}</title>
    <style>
        @media print {
            body {
                margin: 0;
                padding: 0;
            }
            .no-print {
                display: none !important;
            }
            .print-container {
                margin: 0;
                padding: 0.5in;
            }
            .page-break {
                page-break-before: always;
            }
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Times New Roman', Times, serif;
            background: #fff;
            font-size: 11pt;
            line-height: 1.4;
        }
        
        .print-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background: white;
        }
        
        /* Letter Header */
        .letter-header {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .university-name {
            font-family: 'Playfair Display', 'Times New Roman', serif;
            font-size: 16pt;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        
        .office-title {
            font-size: 11pt;
            margin-bottom: 10px;
        }
        
        .contact-line {
            font-size: 8pt;
            margin: 2px 0;
        }
        
        hr.thick-line {
            border: none;
            border-top: 2px solid #000;
            margin: 8px 0 2px 0;
        }
        
        hr.thin-line {
            border: none;
            border-top: 1px solid #ccc;
            margin: 0 0 15px 0;
        }
        
        /* Reference Section */
        .reference-section {
            margin-bottom: 20px;
            font-size: 10pt;
        }
        
        .reference-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
        
        /* Recipient Section */
        .recipient-section {
            margin-bottom: 20px;
        }
        
        /* Subject */
        .subject {
            font-weight: bold;
            margin: 15px 0;
            text-decoration: underline;
        }
        
        /* Letter Body */
        .letter-body {
            text-align: justify;
            margin: 20px 0;
        }
        
        .letter-body p {
            margin-bottom: 10px;
        }
        
        /* Signature */
        .signature {
            margin-top: 40px;
        }
        
        /* CC Section */
        .cc-section {
            margin-top: 30px;
            font-size: 9pt;
        }
        
        /* Footer */
        .footer {
            margin-top: 40px;
            border-top: 1px solid #ccc;
            padding-top: 10px;
            font-size: 7pt;
            text-align: center;
        }
        
        /* No Print Button Bar */
        .no-print {
            background: #f0f0f0;
            padding: 10px;
            text-align: center;
            border-bottom: 1px solid #ccc;
            margin-bottom: 20px;
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
        
        /* Utility */
        .text-muted { color: #666; }
        .fw-bold { font-weight: bold; }
        .mt-2 { margin-top: 10px; }
        .mt-3 { margin-top: 15px; }
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
    
    <div class="print-container">
        
        <!-- LETTERHEAD -->
        <div class="letter-header">
            @if(isset($university->logo_path) && $university->logo_path)
                <div style="margin-bottom: 10px;">
                    <img src="{{ Storage::url($university->logo_path) }}" alt="Logo" style="max-height: 80px; width: auto;">
                </div>
            @endif
            <div class="university-name">{{ $university->name ?? 'ST JOHN\'S UNIVERSITY OF TANZANIA' }}</div>
            <div class="office-title">{{ $letter->sender->department->name ?? 'Office of the Vice Chancellor' }}</div>
            <hr class="thick-line">
            <hr class="thin-line">
            <div class="contact-line">Tel: {{ $university->phone ?? '+255 67 70 86093' }} | Email: {{ $university->email ?? 'info@university.ac.tz' }}</div>
            <div class="contact-line">P.O. Box {{ $university->postal_box ?? '47' }}, {{ $university->city ?? 'Dodoma' }}, {{ $university->country ?? 'Tanzania' }}</div>
        </div>
        
        <!-- REFERENCE SECTION -->
        <div class="reference-section">
            <div class="reference-row">
                <span><strong>Our Ref:</strong> {{ $letter->letter_number ?? 'N/A' }}</span>
                <span><strong>Date:</strong> {{ $letter->created_at->format('d F Y') }}</span>
            </div>
            @if($letter->your_ref)
            <div class="reference-row">
                <span><strong>Your Ref:</strong> {{ $letter->your_ref }}</span>
            </div>
            @endif
            @if($letter->secure_code)
            <div class="reference-row">
                <span><strong>Secure Code:</strong> <span style="font-family: monospace;">{{ $letter->secure_code }}</span></span>
            </div>
            @endif
        </div>
        
        <!-- RECIPIENT -->
        <div class="recipient-section">
            <p><strong>To:</strong> {{ $letter->recipient->full_name ?? 'N/A' }}</p>
            <p><strong>Staff ID:</strong> {{ $letter->recipient->staff_id ?? 'N/A' }}</p>
            <p><strong>Department:</strong> {{ $letter->recipient->department->name ?? 'N/A' }}</p>
            @if($letter->recipient->position)
            <p><strong>Position:</strong> {{ $letter->recipient->position }}</p>
            @endif
        </div>
        
        <!-- SUBJECT -->
        <div class="subject">
            RE: {{ $letter->title }}
        </div>
        
        <!-- LETTER BODY -->
        <div class="letter-body">
            {!! nl2br(e($letter->content)) !!}
        </div>
        
        <!-- SIGNATURE -->
        <div class="signature">
            <p>Yours sincerely,</p>
            <br><br><br>
            <p><strong>{{ $letter->signatory ?? $letter->sender->full_name ?? '_________________________' }}</strong></p>
            <p>{{ $letter->signatory_title ?? $letter->sender->getRoleNames()->first() ?? 'Authorized Signatory' }}</p>
        </div>
        
        <!-- CC (Carbon Copy) -->
        @if($letter->cc_recipients && count(json_decode($letter->cc_recipients, true)) > 0)
        <div class="cc-section">
            <strong>CC:</strong>
            <ul style="margin: 5px 0 0 20px;">
                @foreach(json_decode($letter->cc_recipients, true) as $ccId)
                    @php $ccUser = App\Models\User::find($ccId); @endphp
                    @if($ccUser)
                    <li>{{ $ccUser->full_name }} ({{ $ccUser->staff_id ?? 'N/A' }})</li>
                    @endif
                @endforeach
            </ul>
        </div>
        @endif
        
        <!-- FOOTER -->
        <div class="footer">
            <p>This is a computer-generated letter. No signature is required.</p>
            @if($letter->secure_code && $letter->stage === 'signed')
            <p class="mt-2">To verify authenticity, visit: {{ route('verify.letter', $letter->secure_code) }}</p>
            @endif
            <p class="mt-2">Generated on: {{ now()->format('d F Y H:i:s') }}</p>
        </div>
        
    </div>
    
    <script>
        function downloadPDF() {
            window.location.href = '{{ route("letters.pdf", $letter->id) }}';
        }
        
        // Auto-print when page loads (optional - uncomment if needed)
        // window.onload = function() { window.print(); };
    </script>
</body>
</html>