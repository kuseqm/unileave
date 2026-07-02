<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $definition->display_name }}</title>
    <style>
        @page {
            margin: 2cm 1.5cm;
            header: html_header;
            footer: html_footer;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #2c3e50;
        }
        /* Header */
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 3px solid #3498db;
            padding-bottom: 10px;
        }
        .logo {
            max-width: 100px;
            margin-bottom: 5px;
        }
        .report-title {
            font-size: 24px;
            font-weight: bold;
            color: #2c3e50;
            margin: 5px 0;
            letter-spacing: 1px;
        }
        .report-subtitle {
            font-size: 13px;
            color: #7f8c8d;
        }
        /* Verification Box */
        .verification-box {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 12px;
            margin: 15px 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .verification-code {
            font-size: 18px;
            font-weight: bold;
            font-family: monospace;
            letter-spacing: 2px;
            background: #fff;
            padding: 8px 15px;
            border-radius: 6px;
            border: 1px dashed #3498db;
            color: #2c3e50;
        }
        .qr-code {
            text-align: right;
        }
        .qr-code img {
            width: 80px;
            height: 80px;
        }
        /* Report Info */
        .report-info {
            background: #ecf0f1;
            padding: 10px 12px;
            margin: 15px 0;
            border-radius: 6px;
            font-size: 10px;
            border-left: 4px solid #3498db;
        }
        /* Table */
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th {
            background-color: #3498db;
            color: white;
            padding: 8px 6px;
            text-align: left;
            font-size: 10px;
            font-weight: 600;
        }
        td {
            border-bottom: 1px solid #e2e8f0;
            padding: 6px;
            vertical-align: top;
            font-size: 10px;
        }
        tr:nth-child(even) {
            background-color: #f9f9ff;
        }
        /* Badges */
        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 20px;
            font-size: 9px;
            font-weight: bold;
        }
        .badge-success { background: #d4edda; color: #155724; }
        .badge-warning { background: #fff3cd; color: #856404; }
        .badge-danger { background: #f8d7da; color: #721c24; }
        .badge-info { background: #d1ecf1; color: #0c5460; }
        /* Footer */
        .footer {
            text-align: center;
            font-size: 9px;
            color: #95a5a6;
            margin-top: 20px;
            border-top: 1px solid #ecf0f1;
            padding-top: 8px;
        }
        /* Watermark (optional) */
        .watermark {
            position: fixed;
            bottom: 50%;
            right: 0;
            opacity: 0.1;
            transform: rotate(-45deg);
            font-size: 60px;
            color: #ccc;
            z-index: -1;
        }
    </style>
</head>
<body>

    <htmlpageheader name="header">
        <div class="header">
            @if(file_exists(public_path('images/logo.png')))
            <img src="{{ public_path('images/logo.png') }}" class="logo" alt="Logo">
            @endif
            <div class="report-title">{{ config('app.name', 'University Leave System') }}</div>
            <div class="report-subtitle">{{ $definition->display_name }}</div>
        </div>
    </htmlpageheader>

    <htmlpagefooter name="footer">
        <div class="footer">
            Generated: {{ now()->format('d M Y H:i:s') }} | Page {PAGENO} of {nbpg} | Report ID: {{ $verificationCode ?? 'N/A' }}
        </div>
    </htmlpagefooter>

    <!-- Verification Box -->
    <div class="verification-box">
        <div>
            <strong style="font-size: 11px;">🔐 VERIFICATION CODE</strong><br>
            <span class="verification-code">{{ $verificationCode ?? 'N/A' }}</span>
            <div style="font-size: 9px; color:#7f8c8d; margin-top: 5px;">
                Use this code to verify authenticity at<br>
                <strong>{{ url('/verify/' . $verificationCode) }}</strong>
            </div>
        </div>
        <div class="qr-code">
            <img src="{{ $qrCode }}" alt="QR Code" width="80" height="80">
        </div>
    </div>

    <!-- Report Info -->
    <div class="report-info">
        <strong>📅 Generation Date:</strong> {{ now()->format('l, d F Y H:i:s') }}<br>
        <strong>📊 Period:</strong> 
        @if(!empty($filters['date_from']) && !empty($filters['date_to']))
            {{ \Carbon\Carbon::parse($filters['date_from'])->format('d M Y') }} – {{ \Carbon\Carbon::parse($filters['date_to'])->format('d M Y') }}
        @else
            All time
        @endif
        @if(!empty($filters['department_id']))
            <br><strong>🏢 Department:</strong> {{ \App\Models\Department::find($filters['department_id'])->name ?? 'N/A' }}
        @endif
        @if(!empty($filters['leave_type_id']))
            <br><strong>📌 Leave Type:</strong> {{ \App\Models\LeaveType::find($filters['leave_type_id'])->name ?? 'N/A' }}
        @endif
        @if(!empty($filters['status']))
            <br><strong>⚙️ Status:</strong> {{ ucfirst($filters['status']) }}
        @endif
    </div>

    <!-- Data Table -->
    <table>
        <thead>
            <tr>
                @foreach($reportData['rows']->first() ?? [] as $col => $val)
                    <th>{{ ucwords(str_replace('_', ' ', $col)) }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse($reportData['rows'] as $row)
            <tr>
                @foreach($row as $key => $value)
                    <td>
                        @if(is_string($value) && in_array(strtolower($value), ['pending']))
                            <span class="badge badge-warning">Pending</span>
                        @elseif(is_string($value) && in_array(strtolower($value), ['approved']))
                            <span class="badge badge-success">Approved</span>
                        @elseif(is_string($value) && in_array(strtolower($value), ['rejected']))
                            <span class="badge badge-danger">Rejected</span>
                        @else
                            {{ $value }}
                        @endif
                    </td>
                @endforeach
            </tr>
            @empty
            <tr><td colspan="100" style="text-align: center; padding: 20px;">No data available</td></tr>
            @endforelse
        </tbody>
    </table>

    <!-- Chart Summary (if chart exists) -->
    @if(isset($reportData['chart']) && $reportData['chart'])
    <div style="margin-top: 25px; background: #f9f9ff; padding: 10px; border-radius: 6px;">
        <strong>📈 Chart Summary ({{ $definition->chart_config['title'] ?? 'Data Distribution' }})</strong>
        <ul style="margin-top: 8px; padding-left: 20px;">
            @foreach($reportData['chart']['labels'] as $idx => $label)
                <li>{{ $label }}: <strong>{{ $reportData['chart']['series'][$idx] ?? 0 }}</strong></li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Watermark (optional, remove if not needed) -->
    <div class="watermark">CONFIDENTIAL</div>
</body>
</html>