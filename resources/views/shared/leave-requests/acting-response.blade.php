<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Acting Staff Response - UniLeave</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Tabler Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css">
    
    <style>
        body {
            background: #FDF8F0;
            font-family: 'Inter', sans-serif;
        }
        .card {
            border-radius: 0.2rem;
            border: 1px solid #E8E2D5;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header py-2 px-3 bg-light">
                        <div class="d-flex align-items-center">
                            <i class="ti ti-user-check text-primary me-1" style="font-size: 1rem;"></i>
                            <span style="font-weight: 600;">Hold Position Request</span>
                        </div>
                    </div>
                    <div class="card-body py-3 px-3">
                        <p class="mb-2"><strong>{{ $leaveRequest->staff->first_name }} {{ $leaveRequest->staff->last_name }}</strong> has requested you to hold their position during their leave.</p>
                        
                        <div class="alert alert-light border py-2 px-2 mb-3">
                            <div class="small">
                                <strong>Leave Details:</strong><br>
                                Type: {{ $leaveRequest->leaveType->name ?? 'N/A' }}<br>
                                Duration: {{ \Carbon\Carbon::parse($leaveRequest->start_date)->format('d M Y') }} - {{ \Carbon\Carbon::parse($leaveRequest->end_date)->format('d M Y') }} ({{ $leaveRequest->days }} days)<br>
                                Reason: {{ $leaveRequest->reason }}
                            </div>
                        </div>
                        
                        <form action="{{ route('leave-requests.acting.process', $leaveRequest->id) }}" method="POST">
                            @csrf
                            <input type="hidden" name="action" id="action" value="">
                            
                            <div class="mb-2">
                                <label class="form-label" style="font-size: 0.7rem;">Comment (Optional)</label>
                                <textarea name="comment" class="form-control form-control-sm rounded-sm" rows="2" style="font-size: 0.7rem;"></textarea>
                            </div>
                            
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-success btn-sm rounded-sm" onclick="submitAction('accept')" style="font-size: 0.7rem; padding: 0.25rem 0.5rem;">
                                    <i class="ti ti-check me-1"></i> Accept & Hold Position
                                </button>
                                <button type="button" class="btn btn-danger btn-sm rounded-sm" onclick="submitAction('decline')" style="font-size: 0.7rem; padding: 0.25rem 0.5rem;">
                                    <i class="ti ti-x me-1"></i> Decline
                                </button>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer py-1 px-2 text-center">
                        <small class="text-muted">UniLeave - Leave Management System</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        function submitAction(action) {
            document.getElementById('action').value = action;
            document.querySelector('form').submit();
        }
    </script>
</body>
</html>