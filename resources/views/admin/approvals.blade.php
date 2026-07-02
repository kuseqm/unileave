@extends('layouts.master')

@section('title', 'Pending Approvals')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Pending Approvals</h1>
            <p class="text-muted">Review and manage pending leave requests</p>
        </div>
    </div>
    
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-vcenter">
                    <thead>
                        <tr>
                            <th>Staff</th>
                            <th>Leave Type</th>
                            <th>Duration</th>
                            <th>Days</th>
                            <th>Reason</th>
                            <th>Submitted</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $pendingRequests = App\Models\LeaveRequest::with(['staff', 'leaveType'])->where('status', 'pending')->get();
                        @endphp
                        
                        @forelse($pendingRequests as $request)
                        <tr>
                            <td>{{ $request->staff->first_name }} {{ $request->staff->last_name }}<br>
                                <small class="text-muted">{{ $request->staff->staff_id ?? '' }}</small>
                            </td>
                            <td>{{ $request->leaveType->name ?? 'N/A' }}</td>
                            <td>{{ \Carbon\Carbon::parse($request->start_date)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($request->end_date)->format('M d, Y') }}</td>
                            <td>{{ $request->days }} days</td>
                            <td>{{ \Str::limit($request->reason, 50) }}</td>
                            <td>{{ $request->created_at->diffForHumans() }}</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-success" onclick="approveRequest({{ $request->id }})">
                                        <i class="ti ti-check"></i> Approve
                                    </button>
                                    <button class="btn btn-danger" onclick="rejectRequest({{ $request->id }})">
                                        <i class="ti ti-x"></i> Reject
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <i class="ti ti-check-circle fs-1 text-success mb-2 d-block"></i>
                                    No pending approvals
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
function approveRequest(id) {
    if(confirm('Approve this leave request?')) {
        // Add your approve logic here
        alert('Approve function - ID: ' + id);
    }
}

function rejectRequest(id) {
    if(confirm('Reject this leave request?')) {
        let reason = prompt('Please provide rejection reason:');
        if(reason) {
            alert('Reject - ID: ' + id + '\nReason: ' + reason);
        }
    }
}
</script>
@endsection