@extends('layouts.master')

@section('title', 'Acting Staff Details')

@section('content')
<div class="container-fluid px-2">

    <x-breadcrumb :items="[
        ['name' => 'Who Is Holding My Position', 'url' => route('leave-requests.hold-position-for-me')],
        ['name' => 'Acting Staff Details', 'url' => '#']
    ]" />

    <x-page-header title="{{ strtoupper($history->holder->first_name . ' ' . $history->holder->last_name) }}" description="Acting staff details and their response to your request" :backUrl="route('leave-requests.hold-position-for-me')">
    </x-page-header>

    <x-alert-success />
    <x-alert-danger />

    <div class="row g-2">
        <div class="col-md-6">
            <div class="card rounded-sm">
                <div class="card-header py-2 px-3 d-flex justify-content-between align-items-center bg-light">
                    <span style="font-size: 0.8rem; font-weight: 600;">Staff Information</span>
                    <i class="ti ti-user text-primary" style="font-size: 0.8rem;"></i>
                </div>
                <div class="card-body p-2" style="font-size: 0.75rem;">
                    <div class="d-flex justify-content-between py-1 border-bottom" style="border-color: #E8E2D5;">
                        <span class="text-muted">Full Name</span>
                        <span class="fw-semibold">{{ $history->holder->first_name }} {{ $history->holder->last_name }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-1 border-bottom" style="border-color: #E8E2D5;">
                        <span class="text-muted">Staff ID</span>
                        <span class="fw-semibold">{{ $history->holder->staff_id ?? 'N/A' }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-1 border-bottom" style="border-color: #E8E2D5;">
                        <span class="text-muted">Department</span>
                        <span class="fw-semibold">{{ $history->holder->department->name ?? 'N/A' }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-1 border-bottom" style="border-color: #E8E2D5;">
                        <span class="text-muted">Category</span>
                        <span class="fw-semibold">{{ ucfirst($history->holder->staff_category ?? 'N/A') }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-1 border-bottom" style="border-color: #E8E2D5;">
                        <span class="text-muted">Email</span>
                        <span class="fw-semibold">{{ $history->holder->email ?? 'N/A' }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-1">
                        <span class="text-muted">Phone</span>
                        <span class="fw-semibold">{{ $history->holder->phone ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card rounded-sm">
                <div class="card-header py-2 px-3 d-flex justify-content-between align-items-center bg-light">
                    <span style="font-size: 0.8rem; font-weight: 600;">Acting Response</span>
                    <i class="ti ti-message-dots text-primary" style="font-size: 0.8rem;"></i>
                </div>
                <div class="card-body p-2" style="font-size: 0.75rem;">
                    <div class="mb-2">
                        <span class="badge {{ $history->status == 'accepted' ? 'bg-success-subtle text-success' : ($history->status == 'declined' ? 'bg-danger-subtle text-danger' : 'bg-warning-subtle text-warning') }} rounded-sm px-2 py-1" style="font-size: 0.6rem;">
                            {{ $history->status == 'pending' ? 'Awaiting Response' : ucfirst($history->status) }}
                        </span>
                    </div>
                    <div class="text-muted" style="font-size: 0.7rem;">
                        <span class="fw-semibold">Their Message:</span>
                        <p class="mb-0 mt-1" style="white-space: pre-line;">{{ $history->comment ?? 'No comment provided.' }}</p>
                    </div>
                    <div class="text-muted mt-2" style="font-size: 0.65rem;">
                        <i class="ti ti-calendar me-1"></i> Responded on {{ ($history->updated_at ?? $history->created_at)->format('M d, Y H:i') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-2 mt-2">
        <div class="col-12">
            <div class="card rounded-sm">
                <div class="card-header py-2 px-3 d-flex justify-content-between align-items-center bg-light">
                    <span style="font-size: 0.8rem; font-weight: 600;">Leave Request to Cover</span>
                    <i class="ti ti-file-invoice text-primary" style="font-size: 0.8rem;"></i>
                </div>
                <div class="card-body p-2" style="font-size: 0.75rem;">
                    @if($history->leaveRequest)
                    <div class="d-flex flex-wrap gap-3">
                        <div>
                            <span class="text-muted">Leave Type</span>
                            <div class="fw-semibold">{{ $history->leaveRequest->leaveType->name ?? 'Not specified' }}</div>
                        </div>
                        <div>
                            <span class="text-muted">Start Date</span>
                            <div class="fw-semibold">{{ \Carbon\Carbon::parse($history->leaveRequest->start_date)->format('M d, Y') }}</div>
                        </div>
                        <div>
                            <span class="text-muted">End Date</span>
                            <div class="fw-semibold">{{ \Carbon\Carbon::parse($history->leaveRequest->end_date)->format('M d, Y') }}</div>
                        </div>
                        <div>
                            <span class="text-muted">Days</span>
                            <div class="fw-semibold">{{ $history->leaveRequest->days ?? 'Not specified' }}</div>
                        </div>
                    </div>
                    @else
                    <div class="text-muted" style="font-size: 0.7rem;">
                        <i class="ti ti-info-circle me-1"></i> The leave request details for this acting role are not available.
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
