@extends('layouts.master')

@section('title', 'Acting DVC Assignment Details')

@section('content')
<div class="container-fluid px-2">
    
    <x-breadcrumb :items="[
        ['name' => 'Admin', 'url' => '#'],
        ['name' => 'Acting DVC', 'url' => route('admin.acting-dvc.index')],
        ['name' => 'Details', 'url' => '#']
    ]" />
    
    <x-page-header title="ACTING DVC ASSIGNMENT DETAILS" description="Review and manage assignment">
        <div class="d-flex gap-2">
            <a href="{{ route('admin.acting-dvc.index') }}" class="btn btn-secondary btn-sm rounded-sm">
                <i class="ti ti-arrow-left me-1"></i> Back
            </a>
            @if($assignment->status === 'pending')
            <a href="{{ route('admin.acting-dvc.approve', $assignment->id) }}" class="btn btn-success btn-sm rounded-sm" onclick="return confirm('Approve this assignment?')">
                <i class="ti ti-check me-1"></i> Approve
            </a>
            <button type="button" class="btn btn-danger btn-sm rounded-sm" onclick="rejectAssignment({{ $assignment->id }})">
                <i class="ti ti-x me-1"></i> Reject
            </button>
            @endif
        </div>
    </x-page-header>
    
    <div class="row">
        <div class="col-md-6">
            <div class="card rounded-sm">
                <div class="card-header">
                    <h4>Assignment Information</h4>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <th width="150">Staff:</th>
                            <td>{{ $assignment->staff->first_name }} {{ $assignment->staff->last_name }}</td>
                        </tr>
                        <tr>
                            <th>Role:</th>
                            <td>{{ strtoupper($assignment->role) }}</td>
                        </tr>
                        <tr>
                            <th>Start Date:</th>
                            <td>{{ $assignment->start_date->format('d M Y') }}</td>
                        </tr>
                        <tr>
                            <th>End Date:</th>
                            <td>{{ $assignment->end_date->format('d M Y') }}</td>
                        </tr>
                        <tr>
                            <th>Status:</th>
                            <td>
                                @if($assignment->status == 'pending')
                                    <span class="badge bg-warning">Pending</span>
                                @elseif($assignment->status == 'active')
                                    <span class="badge bg-success">Active</span>
                                @elseif($assignment->status == 'expired')
                                    <span class="badge bg-secondary">Expired</span>
                                @else
                                    <span class="badge bg-danger">Rejected</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Reason:</th>
                            <td>{{ $assignment->reason ?? 'No reason provided' }}</td>
                        </tr>
                        <tr>
                            <th>Approved By:</th>
                            <td>{{ $assignment->approvedBy->first_name ?? 'Not yet approved' }} {{ $assignment->approvedBy->last_name ?? '' }}</td>
                        </tr>
                        <tr>
                            <th>Approved At:</th>
                            <td>{{ $assignment->approved_at ? \Carbon\Carbon::parse($assignment->approved_at)->format('d M Y H:i') : 'Not yet' }}</td>
                        </tr>
                        <tr>
                            <th>Created At:</th>
                            <td>{{ $assignment->created_at->format('d M Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function rejectAssignment(id) {
    if(confirm('Reject this assignment?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/acting-dvc/${id}`;
        form.innerHTML = `@csrf @method('DELETE') <input type="hidden" name="status" value="rejected">`;
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endsection