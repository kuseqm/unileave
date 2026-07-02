<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Leave Letter - {{ $letter->letter_number }}</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            margin: 0;
            padding: 20px;
        }
        .letter-container {
            max-width: 800px;
            margin: 0 auto;
        }
        .text-center { text-align: center; }
        .text-end { text-align: right; }
        .mb-4 { margin-bottom: 20px; }
        .mb-3 { margin-bottom: 15px; }
        .mb-2 { margin-bottom: 10px; }
        .mt-5 { margin-top: 30px; }
        .p-2 { padding: 10px; }
        .bg-light { background-color: #f5f5f5; }
        hr { margin: 15px 0; }
        img { max-height: 80px; }
        .small { font-size: 12px; }
        .text-muted { color: #666; }
    </style>
</head>
<body>
    <div class="letter-container">
        <!-- Letter Header -->
        <div class="text-center mb-4">
            @if($university && $university->logo)
                <img src="{{ public_path('storage/' . $university->logo) }}" alt="University Logo">
            @endif
            <h3>{{ $university->name ?? 'University' }}</h3>
            <p class="small text-muted">{{ $university->address ?? '' }}</p>
            <hr>
        </div>
        
        <!-- Letter Reference -->
        <div class="mb-3">
            <strong>Ref:</strong> {{ $letter->letter_number }}<br>
            <strong>Date:</strong> {{ $letter->created_at->format('d M Y') }}
        </div>
        
        <!-- Letter Subject -->
        <div class="mb-3">
            <strong>SUBJECT: {{ $letter->title }}</strong>
        </div>
        
        <!-- Letter Content -->
        <div class="mb-4">
            {!! nl2br(e($letter->content)) !!}
        </div>
        
        <!-- Leave Details -->
        <div class="mb-4 p-2 bg-light">
            <strong>Leave Details:</strong>
            <ul>
                <li>Staff: {{ $letter->leaveRequest->staff->full_name ?? 'N/A' }}</li>
                <li>Staff ID: {{ $letter->leaveRequest->staff->staff_id ?? 'N/A' }}</li>
                <li>Leave Type: {{ $letter->leaveRequest->leaveType->name ?? 'N/A' }}</li>
                <li>Period: {{ \Carbon\Carbon::parse($letter->leaveRequest->start_date)->format('d M Y') }} - {{ \Carbon\Carbon::parse($letter->leaveRequest->end_date)->format('d M Y') }}</li>
                <li>Total Days: {{ $letter->leaveRequest->days }} days</li>
            </ul>
        </div>
        
        <!-- Signatory -->
        <div class="text-end mt-5">
            <p>Yours sincerely,</p>
            <div style="margin-top: 40px;">
                <p><strong>{{ $letter->generated_by ?? 'Human Resources Department' }}</strong><br>
                {{ $letter->generated_by_role ?? 'HR Officer' }}</p>
            </div>
        </div>
        
        <!-- Footer -->
        <hr>
        <div class="text-center small text-muted">
            Secure Document - Verification Code: {{ $letter->secure_code }}
        </div>
    </div>
</body>
</html>