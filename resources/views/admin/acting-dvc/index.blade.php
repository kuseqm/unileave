@extends('layouts.master')

@section('title', 'Acting DVC Assignments')

@section('content')
<div class="container-fluid px-2">
    
    <x-breadcrumb :items="[
        ['name' => 'Admin', 'url' => '#'],
        ['name' => 'Acting DVC', 'url' => '#']
    ]" />
    
    <x-page-header title="ACTING DVC ASSIGNMENTS" description="Assign staff to act as DVC when the substantive DVC is on leave">
        <a href="{{ route('admin.acting-dvc.create') }}" class="btn btn-primary btn-sm rounded-sm">
            <i class="ti ti-plus me-1"></i> New Assignment
        </a>
    </x-page-header>
    
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show py-1 px-2 mb-2 rounded-sm" role="alert">
        <i class="ti ti-check-circle me-1"></i> {{ session('success') }}
        <button type="button" class="btn-close py-0" data-bs-dismiss="alert"></button>
    </div>
    @endif
    
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show py-1 px-2 mb-2 rounded-sm" role="alert">
        <i class="ti ti-alert-circle me-1"></i> {{ session('error') }}
        <button type="button" class="btn-close py-0" data-bs-dismiss="alert"></button>
    </div>
    @endif
    
    <!-- Active Assignments -->
    <div class="card rounded-sm mb-3">
        <div class="card-header bg-success-subtle py-1 px-2">
            <i class="ti ti-user-check me-1"></i>
            <span style="font-weight: 600;">Active Assignments</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm mb-0" style="font-size: 0.75rem;">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-2 py-1">Staff</th>
                            <th class="py-1">Role</th>
                            <th class="py-1">Start Date</th>
                            <th class="py-1">End Date</th>
                            <th class="py-1">Status</th>
                            <th class="pe-2 py-1">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($activeAssignments as $assignment)
                        <tr>
                            <td class="ps-2 py-1">
                                {{ $assignment->staff->first_name }} {{ $assignment->staff->last_name }}
                                <div class="text-muted" style="font-size: 0.6rem;">{{ $assignment->staff->staff_id ?? '' }}</div>
                            </td>
                            <td class="py-1">
                                <span class="badge bg-primary-subtle text-primary rounded-sm">
                                    {{ strtoupper($assignment->role) }}
                                </span>
                            </td>
                            <td class="py-1">{{ $assignment->start_date->format('d M Y') }}</td>
                            <td class="py-1">{{ $assignment->end_date->format('d M Y') }}</td>
                            <td class="py-1">
                                <span class="badge bg-success-subtle text-success rounded-sm">Active</span>
                            </td>
                            <td class="pe-2 py-1">
                                <form action="{{ route('admin.acting-dvc.destroy', $assignment->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-sm" onclick="return confirm('End this assignment?')">
                                        <i class="ti ti-trash"></i> End
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-2 text-muted">No active assignments</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Pending Assignments -->
    @if($pendingAssignments->count() > 0)
    <div class="card rounded-sm mb-3">
        <div class="card-header bg-warning-subtle py-1 px-2">
            <i class="ti ti-clock me-1"></i>
            <span style="font-weight: 600;">Pending Assignments</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm mb-0" style="font-size: 0.75rem;">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-2 py-1">Staff</th>
                            <th class="py-1">Role</th>
                            <th class="py-1">Start Date</th>
                            <th class="py-1">End Date</th>
                            <th class="py-1">Reason</th>
                            <th class="pe-2 py-1">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendingAssignments as $assignment)
                        <tr>
                            <td class="ps-2 py-1">
                                {{ $assignment->staff->first_name }} {{ $assignment->staff->last_name }}
                                <div class="text-muted" style="font-size: 0.6rem;">{{ $assignment->staff->staff_id ?? '' }}</div>
                            </td>
                            <td class="py-1">
                                <span class="badge bg-info-subtle text-info rounded-sm">{{ strtoupper($assignment->role) }}</span>
                            </td>
                            <td class="py-1">{{ $assignment->start_date->format('d M Y') }}</td>
                            <td class="py-1">{{ $assignment->end_date->format('d M Y') }}</td>
                            <td class="py-1">{{ \Str::limit($assignment->reason, 50) }}</td>
                            <td class="pe-2 py-1">
                                <a href="{{ route('admin.acting-dvc.show', $assignment->id) }}" class="btn btn-sm btn-outline-info rounded-sm">
                                    <i class="ti ti-eye"></i> Review
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
    
    <!-- History -->
    <div class="card rounded-sm">
        <div class="card-header bg-light py-1 px-2">
            <i class="ti ti-history me-1"></i>
            <span style="font-weight: 600;">History</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm mb-0" style="font-size: 0.75rem;">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-2 py-1">Staff</th>
                            <th class="py-1">Role</th>
                            <th class="py-1">Period</th>
                            <th class="py-1">Status</th>
                            <th class="pe-2 py-1">Approved By</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($history as $assignment)
                        <tr>
                            <td class="ps-2 py-1">{{ $assignment->staff->first_name }} {{ $assignment->staff->last_name }}</td>
                            <td class="py-1">{{ strtoupper($assignment->role) }}</td>
                            <td class="py-1">{{ $assignment->start_date->format('d M Y') }} - {{ $assignment->end_date->format('d M Y') }}</td>
                            <td class="py-1">
                                @if($assignment->status == 'expired')
                                    <span class="badge bg-secondary-subtle text-secondary rounded-sm">Expired</span>
                                @elseif($assignment->status == 'rejected')
                                    <span class="badge bg-danger-subtle text-danger rounded-sm">Rejected</span>
                                @endif
                            </td>
                            <td class="pe-2 py-1">{{ $assignment->approvedBy->first_name ?? 'N/A' }} {{ $assignment->approvedBy->last_name ?? '' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-2 text-muted">No history</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer py-1 px-2">
            {{ $history->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection