<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Leave Report</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; margin: 20px; }
        .header { text-align: center; margin-bottom: 30px; }
        .summary { margin-bottom: 30px; }
        .summary table { width: 100%; border-collapse: collapse; }
        .summary td { padding: 8px; border: 1px solid #ddd; }
        .data-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .data-table th, .data-table td { border: 1px solid #ddd; padding: 8px; text-align: left; font-size: 10px; }
        .data-table th { background-color: #f2f2f2; }
        .footer { text-align: center; margin-top: 30px; font-size: 10px; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <h2>University Leave Report</h2>
        <p>Generated on {{ now()->format('d F Y H:i') }}</p>
    </div>
    
    <div class="summary">
        <h3>Summary</h3>
        <table>
            <tr><td style="width: 50%;">Total Requests:</td><td>{{ $summary['total'] }}</td></tr>
            <tr><td>Approved:</td><td>{{ $summary['approved'] }}</td></tr>
            <tr><td>Pending:</td><td>{{ $summary['pending'] }}</td></tr>
            <tr><td>Rejected:</td><td>{{ $summary['rejected'] }}</td></tr>
            <tr><td>Total Days:</td><td>{{ $summary['total_days'] }}</td></tr>
        </table>
    </div>
    
    <h3>Detailed Report</h3>
    <table class="data-table">
        <thead>
            <tr>
                <th>Staff</th>
                <th>Department</th>
                <th>Leave Type</th>
                <th>Period</th>
                <th>Days</th>
                <th>Status</th>
                <th>Payment</th>
            </tr>
        </thead>
        <tbody>
            @foreach($requests as $request)
            <tr>
                <td>{{ $request->staff->full_name ?? 'N/A' }}</td>
                <td>{{ $request->staff->department->name ?? 'N/A' }}</td>
                <td>{{ $request->leaveType->name ?? 'N/A' }}</td>
                <td>{{ \Carbon\Carbon::parse($request->start_date)->format('d M Y') }} - {{ \Carbon\Carbon::parse($request->end_date)->format('d M Y') }}</td>
                <td>{{ $request->days }}</td>
                <td>{{ ucfirst($request->status) }}</td>
                <td>{{ ucfirst($request->payment_status ?? 'Paid') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <div class="footer">
        <p>This is a system-generated report. For inquiries, contact HR Department.</p>
    </div>
</body>
</html>