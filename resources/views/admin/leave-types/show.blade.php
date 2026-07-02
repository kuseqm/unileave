@extends('layouts.master')

@section('title', 'Leave Type Details')

@section('content')
<div class="container-fluid px-2">
    
    <!-- Breadcrumb -->
    <x-breadcrumb :items="[
        ['name' => 'Leave Types', 'url' => route('admin.leave-types.index')],
        ['name' => $leaveType->code . ' - ' . $leaveType->name, 'url' => '#']
    ]" />
    
    <!-- Page Header -->
    <x-page-header title="{{ $leaveType->name }}" description="{{ $leaveType->code }} - Leave Type Details">
        <a href="{{ route('admin.leave-types.index') }}" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
            <i class="ti ti-arrow-left me-1"></i> Back
        </a>
        <a href="{{ route('admin.leave-types.edit', $leaveType->id) }}" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
            <i class="ti ti-edit me-1"></i> Edit
        </a>
    </x-page-header>
    
    <div class="row g-2">
        <!-- LEFT COLUMN: Details Card -->
        <div class="col-lg-7">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-info-circle text-primary me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Leave Type Information</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <div class="row g-1">
                        <div class="col-md-6">
                            <div class="mb-1">
                                <div class="text-muted" style="font-size: 0.6rem; text-transform: uppercase;">Leave Code</div>
                                <div class="fw-bold" style="font-size: 0.85rem;">{{ $leaveType->code }}</div>
                            </div>
                            <div class="mb-1">
                                <div class="text-muted" style="font-size: 0.6rem; text-transform: uppercase;">Leave Name</div>
                                <div style="font-size: 0.8rem;">{{ $leaveType->name }}</div>
                            </div>
                            <div class="mb-1">
                                <div class="text-muted" style="font-size: 0.6rem; text-transform: uppercase;">Status</div>
                                <div>
                                    @if($leaveType->status == 'active')
                                        <span class="badge bg-success-subtle text-success rounded-sm" style="font-size: 0.6rem;">Active</span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary rounded-sm" style="font-size: 0.6rem;">Inactive</span>
                                    @endif
                                </div>
                            </div>
                            <div class="mb-1">
                                <div class="text-muted" style="font-size: 0.6rem; text-transform: uppercase;">Sort Order</div>
                                <div style="font-size: 0.8rem;">{{ $leaveType->sort_order ?? 'N/A' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-1">
                                <div class="text-muted" style="font-size: 0.6rem; text-transform: uppercase;">Max Consecutive Days</div>
                                <div class="fw-semibold" style="font-size: 0.85rem;">{{ $leaveType->max_consecutive_days ?? 'Unlimited' }} days</div>
                            </div>
                            <div class="mb-1">
                                <div class="text-muted" style="font-size: 0.6rem; text-transform: uppercase;">Deduct from Balance</div>
                                <div>
                                    @if($leaveType->deduct_balance)
                                        <span class="text-success"><i class="ti ti-check me-1" style="font-size: 0.7rem;"></i> Yes</span>
                                    @else
                                        <span class="text-muted"><i class="ti ti-x me-1" style="font-size: 0.7rem;"></i> No</span>
                                    @endif
                                </div>
                            </div>
                            <div class="mb-1">
                                <div class="text-muted" style="font-size: 0.6rem; text-transform: uppercase;">VC Discretion Required</div>
                                <div>
                                    @if($leaveType->vc_discretion)
                                        <span class="text-warning"><i class="ti ti-alert-triangle me-1" style="font-size: 0.7rem;"></i> Yes</span>
                                    @else
                                        <span class="text-muted"><i class="ti ti-x me-1" style="font-size: 0.7rem;"></i> No</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-1 mt-1">
                        <div class="text-muted" style="font-size: 0.6rem; text-transform: uppercase;">Description</div>
                        <p class="mb-0" style="font-size: 0.75rem;">{{ $leaveType->description ?? 'No description provided.' }}</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- RIGHT COLUMN: Requirements Card -->
        <div class="col-lg-5">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-file-description text-primary me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Document Requirements</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <div class="mb-1">
                        <div class="d-flex justify-content-between align-items-center py-1">
                            <span style="font-size: 0.7rem;"><i class="ti ti-certificate me-2 text-primary"></i> Doctor's Certificate</span>
                            @if($leaveType->requires_certificate)
                                <span class="text-success" style="font-size: 0.7rem;"><i class="ti ti-check"></i> Required</span>
                            @else
                                <span class="text-muted" style="font-size: 0.7rem;"><i class="ti ti-x"></i> Not Required</span>
                            @endif
                        </div>
                        <div class="d-flex justify-content-between align-items-center py-1">
                            <span style="font-size: 0.7rem;"><i class="ti ti-file me-2 text-primary"></i> Letter of Request</span>
                            @if($leaveType->requires_letter)
                                <span class="text-success" style="font-size: 0.7rem;"><i class="ti ti-check"></i> Required</span>
                            @else
                                <span class="text-muted" style="font-size: 0.7rem;"><i class="ti ti-x"></i> Not Required</span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="alert alert-light border mb-0 py-1 px-2 rounded-sm">
                        <div class="d-flex align-items-start gap-1">
                            <i class="ti ti-help text-primary mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">Applicable For</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">This leave type applies to all staff members regardless of category.</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Usage Statistics Card -->
            <div class="card rounded-sm mt-2">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-chart-bar text-success me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Usage Statistics</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <div class="row text-center g-1">
                        <div class="col-4">
                            <div class="fw-bold text-primary" style="font-size: 1rem;">{{ $leaveType->leaveRequests?->count() ?? 0 }}</div>
                            <div class="text-muted" style="font-size: 0.6rem;">Total Requests</div>
                        </div>
                        <div class="col-4">
                            <div class="fw-bold text-success" style="font-size: 1rem;">{{ $leaveType->leaveRequests?->where('status', 'approved')->count() ?? 0 }}</div>
                            <div class="text-muted" style="font-size: 0.6rem;">Approved</div>
                        </div>
                        <div class="col-4">
                            <div class="fw-bold text-warning" style="font-size: 1rem;">{{ $leaveType->leaveRequests?->where('status', 'pending')->count() ?? 0 }}</div>
                            <div class="text-muted" style="font-size: 0.6rem;">Pending</div>
                        </div>
                    </div>
                    <hr class="my-1">
                    <div class="text-center">
                        <small class="text-muted" style="font-size: 0.6rem;">
                            <i class="ti ti-calendar me-1"></i>
                            Created: {{ $leaveType->created_at?->format('d M Y') ?? 'N/A' }}
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Recent Leave Requests Using This Type -->
    @if($leaveType->leaveRequests && $leaveType->leaveRequests->count() > 0)
    <div class="row g-2 mt-2">
        <div class="col-12">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-history text-info me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Recent Leave Requests ({{ $leaveType->code }})</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0" style="font-size: 0.75rem;">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-2 py-1">Staff</th>
                                    <th class="py-1">Dates</th>
                                    <th class="py-1">Days</th>
                                    <th class="py-1">Status</th>
                                    <th class="pe-2 py-1">Submitted</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($leaveType->leaveRequests->take(5) as $request)
                                <tr class="clickable-row" style="cursor: pointer;" onclick="window.location='{{ route('leave-requests.show', $request->id) }}'">
                                    <td class="ps-2 py-1">
                                        <span class="fw-semibold hover-link">{{ $request->staff?->first_name ?? 'N/A' }} {{ $request->staff?->last_name ?? '' }}</span>
                                        @if($request->staff?->staff_id)
                                            <div class="text-muted" style="font-size: 0.6rem;">{{ $request->staff->staff_id }}</div>
                                        @endif
                                    </td>
                                    <td class="py-1">
                                        @if($request->start_date && $request->end_date)
                                            {{ \Carbon\Carbon::parse($request->start_date)->format('d M Y') }} - {{ \Carbon\Carbon::parse($request->end_date)->format('d M Y') }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td class="py-1">{{ $request->days ?? 'N/A' }} day(s)</td>
                                    <td class="py-1">
                                        @if($request->status == 'approved')
                                            <span class="badge bg-success-subtle text-success rounded-sm" style="font-size: 0.6rem;">Approved</span>
                                        @elseif($request->status == 'pending')
                                            <span class="badge bg-warning-subtle text-warning rounded-sm" style="font-size: 0.6rem;">Pending</span>
                                        @elseif($request->status == 'rejected')
                                            <span class="badge bg-danger-subtle text-danger rounded-sm" style="font-size: 0.6rem;">Rejected</span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary rounded-sm" style="font-size: 0.6rem;">{{ $request->status ?? 'N/A' }}</span>
                                        @endif
                                    </td>
                                    <td class="pe-2 py-1">{{ $request->created_at?->format('d M Y') ?? 'N/A' }}</td>
                                 </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($leaveType->leaveRequests->count() > 5)
                <div class="card-footer py-1 px-2 text-center">
                    <a href="{{ route('leave-requests.status', ['leave_type_id' => $leaveType->id]) }}" class="small" style="font-size: 0.65rem;">
                        View all {{ $leaveType->leaveRequests->count() }} requests →
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endif
</div>

<style>
.clickable-row {
    transition: background 0.2s ease;
}
.clickable-row:hover {
    background-color: #F8FAFC !important;
}
.hover-link:hover {
    text-decoration: underline;
    color: #3B82F6 !important;
}
</style>
@endsection