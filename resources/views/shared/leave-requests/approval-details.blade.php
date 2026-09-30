@extends('layouts.master')

@section('title', 'Approval Details - ' . ($leaveRequest->staff->full_name ?? 'Leave Request'))

@section('content')
<div class="container-fluid px-2">
    
    <!-- Breadcrumb -->
    <x-breadcrumb :items="[
        ['name' => 'Approvals History', 'url' => route('leave-requests.approvals-history')],
        ['name' => 'Approval Details', 'url' => '#']
    ]" />
    
    <!-- Page Header -->
    <x-page-header title="APPROVAL DETAILS" description="Complete leave request information">
        <div>
            <a href="{{ route('leave-requests.approvals-history') }}" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
                <i class="ti ti-arrow-left me-1"></i> Back to List
            </a>
            @if($leaveRequest->status === 'approved')
            <button type="button" onclick="window.print();" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
                <i class="ti ti-printer me-1"></i> Print Letter
            </button>
            <button type="button" id="downloadPdfBtn" class="btn btn-success btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
                <i class="ti ti-download me-1"></i> Download PDF
            </button>
            @endif
        </div>
    </x-page-header>
    
    <div class="row g-2">
        <!-- Main Details Column -->
        <div class="col-lg-7">
            
            <!-- ============================================ -->
            <!-- LEAVE REQUEST DETAILS CARD -->
            <!-- ============================================ -->
            <div class="card rounded-sm mb-2">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-info-circle text-primary me-1" style="font-size: 0.7rem;"></i>
                        <span style="font-weight: 600; font-size: 0.7rem;">Leave Request Details</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <div class="row g-1">
                        <div class="col-md-6 mb-1">
                            <div class="text-muted" style="font-size: 0.6rem;">Staff Name</div>
                            <div class="fw-semibold" style="font-size: 0.75rem;">
                                {{ $leaveRequest->staff->full_name ?? 'N/A' }}
                                <span class="text-muted" style="font-size: 0.65rem;">({{ $leaveRequest->staff->staff_id ?? 'No ID' }})</span>
                            </div>
                        </div>
                        <div class="col-md-6 mb-1">
                            <div class="text-muted" style="font-size: 0.6rem;">Department</div>
                            <div style="font-size: 0.75rem;">{{ $leaveRequest->staff->department->name ?? 'N/A' }}</div>
                        </div>
                    </div>
                    
                    <div class="row g-1">
                        <div class="col-md-6 mb-1">
                            <div class="text-muted" style="font-size: 0.6rem;">Leave Type</div>
                            <div class="fw-semibold" style="font-size: 0.75rem;">{{ $leaveRequest->leaveType->name ?? 'N/A' }}</div>
                        </div>
                        <div class="col-md-6 mb-1">
                            <div class="text-muted" style="font-size: 0.6rem;">Leave Code</div>
                            <div style="font-size: 0.75rem;">{{ $leaveRequest->leaveType->code ?? 'N/A' }}</div>
                        </div>
                    </div>
                    
                    <div class="row g-1">
                        <div class="col-md-6 mb-1">
                            <div class="text-muted" style="font-size: 0.6rem;">Start Date</div>
                            <div style="font-size: 0.75rem;">{{ \Carbon\Carbon::parse($leaveRequest->start_date)->format('d M Y') }}</div>
                        </div>
                        <div class="col-md-6 mb-1">
                            <div class="text-muted" style="font-size: 0.6rem;">End Date</div>
                            <div style="font-size: 0.75rem;">{{ \Carbon\Carbon::parse($leaveRequest->end_date)->format('d M Y') }}</div>
                        </div>
                    </div>
                    
                    <div class="row g-1">
                        <div class="col-md-6 mb-1">
                            <div class="text-muted" style="font-size: 0.6rem;">Total Days</div>
                            <div class="fw-semibold text-primary" style="font-size: 0.85rem;">{{ $leaveRequest->days }} day(s)</div>
                        </div>
                        <div class="col-md-6 mb-1">
                            <div class="text-muted" style="font-size: 0.6rem;">Half Day</div>
                            <div style="font-size: 0.75rem;">
                                @if($leaveRequest->half_day && $leaveRequest->half_day != 'full')
                                    <span class="badge bg-info-subtle text-info rounded-sm">{{ ucfirst($leaveRequest->half_day) }}</span>
                                @else
                                    <span class="text-muted">Full Day</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <div class="row g-1">
                        <div class="col-md-12 mb-1">
                            <div class="text-muted" style="font-size: 0.6rem;">Reason for Leave</div>
                            <div class="p-1 bg-light rounded-sm" style="font-size: 0.7rem;">{{ $leaveRequest->reason }}</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- ============================================ -->
            <!-- RETURN TO WORK CARD -->
            <!-- ============================================ -->
            <div class="card rounded-sm mb-2">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-calendar-return text-success me-1" style="font-size: 0.7rem;"></i>
                        <span style="font-weight: 600; font-size: 0.7rem;">Return to Work</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <div class="row g-1">
                        <div class="col-md-6 mb-1">
                            <div class="text-muted" style="font-size: 0.6rem;">Expected Return Date</div>
                            <div class="fw-semibold" style="font-size: 0.75rem;">
                                {{ $leaveRequest->expected_return_date ? \Carbon\Carbon::parse($leaveRequest->expected_return_date)->format('d M Y') : \Carbon\Carbon::parse($leaveRequest->end_date)->addDay()->format('d M Y') }}
                            </div>
                        </div>
                        <div class="col-md-6 mb-1">
                            <div class="text-muted" style="font-size: 0.6rem;">Actual Return Date</div>
                            <div class="fw-semibold" style="font-size: 0.75rem;">
                                @if($leaveRequest->actual_return_date)
                                    {{ \Carbon\Carbon::parse($leaveRequest->actual_return_date)->format('d M Y') }}
                                    @php
                                        $expected = $leaveRequest->expected_return_date ? \Carbon\Carbon::parse($leaveRequest->expected_return_date) : \Carbon\Carbon::parse($leaveRequest->end_date)->addDay();
                                        $actual = \Carbon\Carbon::parse($leaveRequest->actual_return_date);
                                        $lateDays = $expected->diffInDays($actual, false);
                                    @endphp
                                    @if($lateDays > 0)
                                        <span class="badge bg-danger-subtle text-danger ms-1">Late by {{ $lateDays }} days</span>
                                    @else
                                        <span class="badge bg-success-subtle text-success ms-1">On Time</span>
                                    @endif
                                @else
                                    <span class="text-muted">Not yet reported</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @if($leaveRequest->late_return_days > 0)
                    <div class="row g-1 mt-1">
                        <div class="col-md-12">
                            <x-alert-warning>
    <strong>Late Return:</strong> {{ $leaveRequest->late_return_days }} day(s) late
                                @if($leaveRequest->late_return_penalty > 0)
                                    <br>Penalty Applied: ${{ number_format($leaveRequest->late_return_penalty, 2) }}
                                @endif
                                @if($leaveRequest->late_return_reason)
                                    <br><strong>Reason:</strong> {{ $leaveRequest->late_return_reason }}
                                @endif
</x-alert-warning>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            
            <!-- ============================================ -->
            <!-- TRAVEL & CONTACT CARD -->
            <!-- ============================================ -->
            <div class="card rounded-sm mb-2">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-plane text-info me-1" style="font-size: 0.7rem;"></i>
                        <span style="font-weight: 600; font-size: 0.7rem;">Travel & Contact Information</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <div class="row g-1">
                        <div class="col-md-6 mb-1">
                            <div class="text-muted" style="font-size: 0.6rem;">Travel Destination</div>
                            <div style="font-size: 0.75rem;">{{ $leaveRequest->travel_destination ?? 'Not specified' }}</div>
                        </div>
                        <div class="col-md-6 mb-1">
                            <div class="text-muted" style="font-size: 0.6rem;">Leave Address</div>
                            <div style="font-size: 0.75rem;">{{ $leaveRequest->leave_address ?? 'Not specified' }}</div>
                        </div>
                    </div>
                    <div class="row g-1">
                        <div class="col-md-6 mb-1">
                            <div class="text-muted" style="font-size: 0.6rem;">Contact During Leave</div>
                            <div style="font-size: 0.75rem;">{{ $leaveRequest->contact_number_during_leave ?? 'Not specified' }}</div>
                        </div>
                        <div class="col-md-6 mb-1">
                            <div class="text-muted" style="font-size: 0.6rem;">Benefit to University</div>
                            <div style="font-size: 0.75rem;">{{ $leaveRequest->benefit_to_university ?? 'N/A' }}</div>
                        </div>
                    </div>
                    <div class="row g-1">
                        <div class="col-md-6 mb-1">
                            <div class="text-muted" style="font-size: 0.6rem;">Cost Coverage</div>
                            <div>
                                @if($leaveRequest->cost_coverage)
                                    <span class="badge bg-primary-subtle text-primary rounded-sm">
                                        {{ str_replace('_', ' ', ucfirst($leaveRequest->cost_coverage)) }}
                                    </span>
                                @else
                                    <span class="text-muted">Not specified</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6 mb-1">
                            <div class="text-muted" style="font-size: 0.6rem;">First Time Applicant</div>
                            <div>
                                @if($leaveRequest->is_first_time_applicant)
                                    <span class="badge bg-info-subtle text-info rounded-sm">Yes</span>
                                @else
                                    <span class="text-muted">No</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- ============================================ -->
            <!-- EMERGENCY CONTACT CARD -->
            <!-- ============================================ -->
            <div class="card rounded-sm mb-2">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-phone-call text-danger me-1" style="font-size: 0.7rem;"></i>
                        <span style="font-weight: 600; font-size: 0.7rem;">Emergency Contact</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <div class="row g-1">
                        <div class="col-md-6 mb-1">
                            <div class="text-muted" style="font-size: 0.6rem;">Contact Name</div>
                            <div class="fw-semibold" style="font-size: 0.75rem;">{{ $leaveRequest->emergency_contact_name ?? 'Not specified' }}</div>
                        </div>
                        <div class="col-md-6 mb-1">
                            <div class="text-muted" style="font-size: 0.6rem;">Contact Phone</div>
                            <div style="font-size: 0.75rem;">{{ $leaveRequest->emergency_contact_phone ?? 'Not specified' }}</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- ============================================ -->
            <!-- HANDOVER NOTES CARD -->
            <!-- ============================================ -->
            @if($leaveRequest->handover_notes)
            <div class="card rounded-sm mb-2">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-file-description text-warning me-1" style="font-size: 0.7rem;"></i>
                        <span style="font-weight: 600; font-size: 0.7rem;">Handover Notes</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <div style="font-size: 0.7rem;">{{ $leaveRequest->handover_notes }}</div>
                </div>
            </div>
            @endif
            
            <!-- ============================================ -->
            <!-- SUPPORTING DOCUMENTS CARD -->
            <!-- ============================================ -->
            <div class="card rounded-sm mb-2">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-file-text text-primary me-1" style="font-size: 0.7rem;"></i>
                        <span style="font-weight: 600; font-size: 0.7rem;">Supporting Documents</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    @php
                        $documents = $leaveRequest->documents;
                    @endphp
                    @if($documents && $documents->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($documents as $doc)
                            <div class="d-flex justify-content-between align-items-center py-1 border-bottom">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="ti ti-file-{{ $doc->file_type == 'application/pdf' ? 'pdf' : 'description' }} text-primary" style="font-size: 0.8rem;"></i>
                                    <div>
                                        <div style="font-size: 0.7rem;">{{ $doc->file_name }}</div>
                                        <div class="text-muted" style="font-size: 0.55rem;">
                                            {{ number_format($doc->file_size / 1024, 1) }} KB • Uploaded {{ $doc->created_at->format('d M Y') }}
                                        </div>
                                    </div>
                                </div>
                                <a href="{{ route('leave-requests.documents.download', $doc->id) }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-sm" style="font-size: 0.55rem;">
                                    <i class="ti ti-download me-1"></i> View
                                </a>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-2 text-muted" style="font-size: 0.65rem;">
                            <i class="ti ti-file mb-1 d-block"></i>
                            No supporting documents uploaded
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- ============================================ -->
            <!-- FINANCIAL DETAILS CARD (HR/Admin/DVC/VC only) -->
            <!-- ============================================ -->
            @hasanyrole('admin|hr-officer|dvca|dvc-pfa|vice-chancellor')
            <div class="card rounded-sm mb-2">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-currency-dollar text-success me-1" style="font-size: 0.7rem;"></i>
                        <span style="font-weight: 600; font-size: 0.7rem;">Financial Details</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <div class="row g-1">
                        <div class="col-md-6 mb-1">
                            <div class="text-muted" style="font-size: 0.6rem;">Leave Type</div>
                            <div>
                                @if($leaveRequest->is_paid_leave ?? true)
                                    <span class="badge bg-success-subtle text-success rounded-sm">Paid Leave</span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning rounded-sm">Unpaid Leave</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6 mb-1">
                            <div class="text-muted" style="font-size: 0.6rem;">Salary Impact</div>
                            <div class="fw-semibold" style="font-size: 0.75rem;">
                                @if($leaveRequest->salary_impact && $leaveRequest->salary_impact > 0)
                                    ${{ number_format($leaveRequest->salary_impact, 2) }}
                                @else
                                    No impact
                                @endif
                            </div>
                        </div>
                    </div>
                    @if($leaveRequest->allowances)
                    <div class="row g-1 mt-1">
                        <div class="col-md-12">
                            <div class="text-muted" style="font-size: 0.6rem;">Allowances</div>
                            <div style="font-size: 0.7rem;">{{ $leaveRequest->allowances }}</div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endhasanyrole
            
            <!-- ============================================ -->
            <!-- DEPENDENTS CARD -->
            <!-- ============================================ -->
            @if($leaveRequest->dependents_count > 0 || $leaveRequest->dependents_info)
            <div class="card rounded-sm mb-2">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-users text-info me-1" style="font-size: 0.7rem;"></i>
                        <span style="font-weight: 600; font-size: 0.7rem;">Dependents Information</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <div class="row g-1">
                        <div class="col-md-6 mb-1">
                            <div class="text-muted" style="font-size: 0.6rem;">Number of Dependents</div>
                            <div class="fw-semibold" style="font-size: 0.75rem;">{{ $leaveRequest->dependents_count ?? 0 }}</div>
                        </div>
                    </div>
                    @if($leaveRequest->dependents_info)
                    <div class="row g-1 mt-1">
                        <div class="col-md-12">
                            <div class="text-muted" style="font-size: 0.6rem;">Dependents Details</div>
                            <div style="font-size: 0.7rem;">{{ $leaveRequest->dependents_info }}</div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endif
            
            <!-- ============================================ -->
            <!-- ACTING STAFF CARD -->
            <!-- ============================================ -->
            <div class="card rounded-sm mb-2">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-user-check text-success me-1" style="font-size: 0.7rem;"></i>
                        <span style="font-weight: 600; font-size: 0.7rem;">Acting Staff (Position Holder)</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <div class="row g-1">
                        <div class="col-md-6 mb-1">
                            <div class="text-muted" style="font-size: 0.6rem;">Name</div>
                            <div class="fw-semibold" style="font-size: 0.75rem;">
                                {{ $leaveRequest->actingStaff->full_name ?? 'Not specified' }}
                            </div>
                        </div>
                        <div class="col-md-6 mb-1">
                            <div class="text-muted" style="font-size: 0.6rem;">Status</div>
                            <div>
                                @if($leaveRequest->acting_status == 'accepted')
                                    <span class="badge bg-success-subtle text-success rounded-sm">Accepted</span>
                                @elseif($leaveRequest->acting_status == 'pending')
                                    <span class="badge bg-warning-subtle text-warning rounded-sm">Pending</span>
                                @elseif($leaveRequest->acting_status == 'declined')
                                    <span class="badge bg-danger-subtle text-danger rounded-sm">Declined</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @if($leaveRequest->acting_responded_at)
                    <div class="row g-1 mt-1">
                        <div class="col-md-12">
                            <div class="text-muted" style="font-size: 0.6rem;">Responded At</div>
                            <div style="font-size: 0.7rem;">{{ \Carbon\Carbon::parse($leaveRequest->acting_responded_at)->format('d M Y H:i') }}</div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            
            <!-- ============================================ -->
            <!-- APPROVAL TIMELINE CARD -->
            <!-- ============================================ -->
            <div class="card rounded-sm mb-2">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-timeline text-primary me-1" style="font-size: 0.7rem;"></i>
                        <span style="font-weight: 600; font-size: 0.7rem;">Approval Timeline</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <!-- Visual Timeline -->
                    <div class="timeline-horizontal mb-3">
                        @php
                            $stages = ['Acting', 'HOD', 'Dean', 'HR', 'DVC', 'Final'];
                            $completedLevels = [];
                            foreach ($leaveRequest->approvalLogs->where('action', 'approved') as $log) {
                                $fromRole = strtolower($log->from_role);
                                if ($fromRole == 'hod') $completedLevels[1] = true;
                                if ($fromRole == 'dean') $completedLevels[2] = true;
                                if ($fromRole == 'hr') $completedLevels[3] = true;
                                if (in_array($fromRole, ['dvca', 'dvc-pfa', 'dvc'])) $completedLevels[4] = true;
                                if ($fromRole == 'hr_logistics') $completedLevels[5] = true;
                            }
                            $isFullyApproved = $leaveRequest->status == 'approved';
                        @endphp
                        
                        <div class="d-flex justify-content-between">
                            @foreach($stages as $i => $stage)
                                @php
                                    $levelNum = $i;
                                    $isCompleted = ($levelNum == 0 && $leaveRequest->acting_status == 'accepted') ||
                                                   ($levelNum == 1 && (isset($completedLevels[1]) || $isFullyApproved)) ||
                                                   ($levelNum == 2 && (isset($completedLevels[2]) || $isFullyApproved)) ||
                                                   ($levelNum == 3 && (isset($completedLevels[3]) || $isFullyApproved)) ||
                                                   ($levelNum == 4 && (isset($completedLevels[4]) || $isFullyApproved)) ||
                                                   ($levelNum == 5 && $isFullyApproved);
                                @endphp
                                <div class="text-center" style="flex: 1;">
                                    <div class="rounded-circle mx-auto d-flex align-items-center justify-content-center {{ $isCompleted ? 'bg-success' : 'bg-secondary' }}" style="width: 28px; height: 28px;">
                                        <i class="ti ti-{{ $isCompleted ? 'check' : 'circle' }} text-white" style="font-size: 0.7rem;"></i>
                                    </div>
                                    <div class="mt-1" style="font-size: 0.55rem;">{{ $stage }}</div>
                                </div>
                                @if(!$loop->last)
                                    <div class="flex-grow-1" style="flex: 0.5;"></div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    
                    <!-- Detailed Approval Logs -->
                    <div class="approval-timeline">
                        @php
                            $allLogs = $leaveRequest->approvalLogs->sortBy('created_at');
                        @endphp
                        
                        @foreach($allLogs as $log)
                        <div class="d-flex align-items-start gap-2 py-1 border-bottom">
                            <div class="rounded-circle p-1 d-flex align-items-center justify-content-center" style="width: 24px; height: 24px; background: {{ $log->action == 'approved' ? '#D1FAE5' : '#FEE2E2' }};">
                                <i class="ti ti-{{ $log->action == 'approved' ? 'check' : 'x' }}" style="font-size: 0.6rem; color: {{ $log->action == 'approved' ? '#059669' : '#DC2626' }};"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between flex-wrap">
                                    <span class="fw-semibold" style="font-size: 0.7rem;">
                                        {{ ucfirst($log->action) }} by {{ $log->approver->full_name ?? 'System' }}
                                    </span>
                                    <span class="text-muted" style="font-size: 0.55rem;">{{ $log->created_at->format('d M Y H:i') }}</span>
                                </div>
                                <div class="text-muted" style="font-size: 0.6rem;">From: {{ ucfirst($log->from_role) }} → To: {{ ucfirst($log->to_role) }}</div>
                                @if($log->comment)
                                    <div class="small text-muted mt-0" style="font-size: 0.55rem;">"{{ $log->comment }}"</div>
                                @endif
                            </div>
                        </div>
                        @endforeach
                        
                        @if($leaveRequest->acting_responded_at)
                        <div class="d-flex align-items-start gap-2 py-1 border-bottom">
                            <div class="rounded-circle p-1 d-flex align-items-center justify-content-center" style="width: 24px; height: 24px; background: {{ $leaveRequest->acting_status == 'accepted' ? '#D1FAE5' : '#FEE2E2' }};">
                                <i class="ti ti-{{ $leaveRequest->acting_status == 'accepted' ? 'check' : 'x' }}" style="font-size: 0.6rem; color: {{ $leaveRequest->acting_status == 'accepted' ? '#059669' : '#DC2626' }};"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between flex-wrap">
                                    <span class="fw-semibold" style="font-size: 0.7rem;">
                                        {{ ucfirst($leaveRequest->acting_status) }} by Acting Staff
                                    </span>
                                    <span class="text-muted" style="font-size: 0.55rem;">{{ \Carbon\Carbon::parse($leaveRequest->acting_responded_at)->format('d M Y H:i') }}</span>
                                </div>
                                <div class="text-muted" style="font-size: 0.6rem;">From: Acting Staff → To: HOD</div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- ============================================ -->
            <!-- AUDIT TRAIL CARD (VC/Admin only) -->
            <!-- ============================================ -->
            @hasanyrole('vice-chancellor|admin')
            <div class="card rounded-sm mb-2">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-shield-lock text-danger me-1" style="font-size: 0.7rem;"></i>
                        <span style="font-weight: 600; font-size: 0.7rem;">VC & Admin - Audit Information</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <div class="row g-1">
                        <div class="col-md-6 mb-1">
                            <div class="text-muted" style="font-size: 0.6rem;">Request ID</div>
                            <div style="font-size: 0.75rem;">#{{ $leaveRequest->id }}</div>
                        </div>
                        <div class="col-md-6 mb-1">
                            <div class="text-muted" style="font-size: 0.6rem;">Created At</div>
                            <div style="font-size: 0.75rem;">{{ $leaveRequest->created_at->format('d M Y H:i:s') }}</div>
                        </div>
                    </div>
                    <div class="row g-1">
                        <div class="col-md-6 mb-1">
                            <div class="text-muted" style="font-size: 0.6rem;">Last Updated</div>
                            <div style="font-size: 0.75rem;">{{ $leaveRequest->updated_at->format('d M Y H:i:s') }}</div>
                        </div>
                        <div class="col-md-6 mb-1">
                            <div class="text-muted" style="font-size: 0.6rem;">Tenant ID</div>
                            <div style="font-size: 0.75rem;">{{ $leaveRequest->tenant_id ?? 'N/A' }}</div>
                        </div>
                    </div>
                    @if($leaveRequest->rejection_reason)
                    <x-alert-danger :session="false">
    <strong>Rejection Reason:</strong> {{ $leaveRequest->rejection_reason }}
</x-alert-danger>
                    @endif
                </div>
            </div>
            @endhasanyrole
        </div>
        
        <!-- ============================================ -->
        <!-- RIGHT COLUMN - LETTER & INFO -->
        <!-- ============================================ -->
        <div class="col-lg-5">
            
            <!-- Status Card -->
            <div class="card rounded-sm mb-2">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-flag text-primary me-1" style="font-size: 0.7rem;"></i>
                        <span style="font-weight: 600; font-size: 0.7rem;">Request Status</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2 text-center">
                    @if($leaveRequest->status == 'approved')
                        <div class="badge bg-success-subtle text-success rounded-sm p-2" style="font-size: 0.8rem;">
                            <i class="ti ti-check-circle me-1"></i> FULLY APPROVED
                        </div>
                        <div class="text-muted mt-1" style="font-size: 0.6rem;">
                            Finalized on {{ $leaveRequest->updated_at->format('d M Y') }}
                        </div>
                    @elseif($leaveRequest->status == 'rejected')
                        <div class="badge bg-danger-subtle text-danger rounded-sm p-2" style="font-size: 0.8rem;">
                            <i class="ti ti-x-circle me-1"></i> REJECTED
                        </div>
                        <div class="text-muted mt-1" style="font-size: 0.6rem;">
                            Rejected on {{ $leaveRequest->updated_at->format('d M Y') }}
                        </div>
                    @else
                        <div class="badge bg-warning-subtle text-warning rounded-sm p-2" style="font-size: 0.8rem;">
                            <i class="ti ti-clock me-1"></i> PENDING
                        </div>
                        <div class="text-muted mt-1" style="font-size: 0.6rem;">
                            Current Level: 
                            @php
                                $levels = [1 => 'HOD', 2 => 'Dean', 3 => 'HR', 4 => 'DVC', 5 => 'HR Logistics'];
                            @endphp
                            {{ $levels[$leaveRequest->current_approval_level] ?? 'Processing' }}
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- ============================================ -->
            <!-- PAYMENT INFORMATION CARD - NEW ADDITION -->
            <!-- ============================================ -->
            @if($leaveRequest->payment_status)
            <div class="card rounded-sm mb-2">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-currency-dollar text-primary me-1" style="font-size: 0.7rem;"></i>
                        <span style="font-weight: 600; font-size: 0.7rem;">💰 Payment Information</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <div class="row g-1">
                        <div class="col-md-12 mb-1">
                            <div class="text-muted" style="font-size: 0.6rem;">Payment Status</div>
                            @if($leaveRequest->payment_status == 'paid')
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-success-subtle text-success rounded-pill" style="font-size: 0.7rem;">
                                        <i class="ti ti-check-circle me-1"></i> Fully Paid
                                    </span>
                                    <span class="text-muted" style="font-size: 0.6rem;">100% of salary</span>
                                </div>
                            @elseif($leaveRequest->payment_status == 'partial')
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-warning-subtle text-warning rounded-pill" style="font-size: 0.7rem;">
                                        <i class="ti ti-alert-triangle me-1"></i> Partially Paid
                                    </span>
                                    <span class="text-muted" style="font-size: 0.6rem;">{{ $leaveRequest->payment_percentage }}% of salary</span>
                                </div>
                            @else
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-danger-subtle text-danger rounded-pill" style="font-size: 0.7rem;">
                                        <i class="ti ti-x-circle me-1"></i> Unpaid
                                    </span>
                                    <span class="text-muted" style="font-size: 0.6rem;">No salary during leave</span>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    @if($leaveRequest->payment_notes)
                    <div class="mt-2">
                        <div class="text-muted" style="font-size: 0.6rem;">Decision Reason:</div>
                        <div class="p-1 bg-light rounded-sm" style="font-size: 0.65rem;">{{ $leaveRequest->payment_notes }}</div>
                    </div>
                    @endif
                    
                    @if($leaveRequest->payment_decided_by)
                    <div class="text-muted mt-1" style="font-size: 0.55rem;">
                        <i class="ti ti-user-check me-1"></i>
                        Decided by: {{ $leaveRequest->paymentDecidedBy->full_name ?? 'HR Officer' }} 
                        on {{ \Carbon\Carbon::parse($leaveRequest->payment_decision_at)->format('d M Y') }}
                    </div>
                    @endif
                    
                    <!-- Financial Impact Calculator -->
                    @if($leaveRequest->staff->basic_salary && $leaveRequest->payment_status != 'paid')
                    <div class="mt-2 p-2 bg-light rounded-sm">
                        <div class="text-muted" style="font-size: 0.55rem;">Financial Impact:</div>
                        <div class="small">
                            Daily rate: TZS {{ number_format($leaveRequest->staff->basic_salary / 30, 2) }}<br>
                            @if($leaveRequest->payment_status == 'partial')
                            Total deduction: TZS {{ number_format(($leaveRequest->staff->basic_salary / 30) * $leaveRequest->days * ((100 - $leaveRequest->payment_percentage) / 100), 2) }}
                            @else
                            Total deduction: TZS {{ number_format(($leaveRequest->staff->basic_salary / 30) * $leaveRequest->days, 2) }}
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- ============================================ -->
            <!-- TRAVEL ALLOWANCE CARD -->
            <!-- ============================================ -->
            @if($leaveRequest->travel_allowance_decided_by)
            <div class="card rounded-sm mb-2">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-plane-departure text-info me-1" style="font-size: 0.7rem;"></i>
                        <span style="font-weight: 600; font-size: 0.7rem;">✈️ Travel Allowance</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    @if($leaveRequest->travel_allowance_granted)
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-success-subtle text-success rounded-pill" style="font-size: 0.7rem;">
                                <i class="ti ti-check-circle me-1"></i> Granted
                            </span>
                            <span class="text-muted" style="font-size: 0.6rem;">TZS {{ number_format($leaveRequest->travel_allowance_amount, 2) }}</span>
                        </div>
                    @else
                        <span class="badge bg-secondary-subtle text-secondary rounded-pill" style="font-size: 0.7rem;">
                            <i class="ti ti-x-circle me-1"></i> Not Granted
                        </span>
                    @endif

                    @if($leaveRequest->travel_allowance_notes)
                    <div class="mt-2">
                        <div class="text-muted" style="font-size: 0.6rem;">Notes:</div>
                        <div class="p-1 bg-light rounded-sm" style="font-size: 0.65rem;">{{ $leaveRequest->travel_allowance_notes }}</div>
                    </div>
                    @endif

                    <div class="text-muted mt-1" style="font-size: 0.55rem;">
                        <i class="ti ti-user-check me-1"></i>
                        Decided by: {{ $leaveRequest->travelAllowanceDecidedBy->full_name ?? 'HR Officer' }}
                        on {{ \Carbon\Carbon::parse($leaveRequest->travel_allowance_decided_at)->format('d M Y') }}
                    </div>
                </div>
            </div>
            @endif

            <!-- Official Letter Card (Only for Approved) -->
            @if($leaveRequest->status === 'approved')
            <div class="card rounded-sm mb-2" id="letterCard">
                <div class="card-header py-1 px-2 bg-light d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-file-text text-primary me-1" style="font-size: 0.7rem;"></i>
                        <span style="font-weight: 600; font-size: 0.7rem;">Official Approval Letter</span>
                    </div>
                    <div>
                        <button type="button" onclick="window.print();" class="btn btn-sm btn-outline-primary rounded-sm" style="font-size: 0.55rem; padding: 0.1rem 0.3rem;">
                            <i class="ti ti-printer"></i> Print
                        </button>
                        <button type="button" onclick="downloadPDF();" class="btn btn-sm btn-outline-success rounded-sm" style="font-size: 0.55rem; padding: 0.1rem 0.3rem;">
                            <i class="ti ti-download"></i> PDF
                        </button>
                    </div>
                </div>
                <div class="card-body p-2" id="letterContent">
                    @php
                        $university = $leaveRequest->staff->university;
                        // A request can carry two letters - HR's own approval
                        // letter and the applicant's separately-signed
                        // application letter - so this card (labeled
                        // "Official Approval Letter") must pin the type
                        // explicitly rather than take whichever was created
                        // last.
                        $letter = $leaveRequest->letters()->where('type', 'approval')->latest()->first();
                    @endphp
                    
                    <div class="letter-container" style="font-family: 'Times New Roman', serif; font-size: 0.75rem; line-height: 1.4;">
                        @if($letter)
                            <!-- Letter Header -->
                            <div class="text-center mb-2">
                                @if($university && $university->logo)
                                    <img src="{{ Storage::url($university->logo) }}" alt="University Logo" style="max-height: 60px; max-width: 100%;">
                                @endif
                                <h5 style="font-size: 0.9rem; margin: 0.2rem 0;">{{ $university->name ?? 'University' }}</h5>
                                <p style="font-size: 0.6rem; margin: 0;">{{ $university->address ?? '' }}</p>
                                <hr style="margin: 0.2rem 0;">
                            </div>
                            
                            <!-- Letter Reference -->
                            <div class="mb-2">
                                <strong>Ref:</strong> {{ $letter->letter_number ?? 'N/A' }}<br>
                                <strong>Date:</strong> {{ $letter->created_at->format('d M Y') }}
                            </div>
                            
                            <!-- Letter Subject -->
                            <div class="mb-2">
                                <strong>SUBJECT: APPROVAL OF LEAVE REQUEST</strong>
                            </div>
                            
                            <!-- Letter Body -->
                            <div class="mb-2">
                                <p>Dear {{ $leaveRequest->staff->full_name ?? 'Staff Member' }},</p>
                                <p>We are pleased to inform you that your request for <strong>{{ $leaveRequest->leaveType->name ?? 'Leave' }}</strong> has been <strong>APPROVED</strong> as follows:</p>
                                
                                <ul style="margin: 0.2rem 0; padding-left: 1rem;">
                                    <li><strong>Leave Type:</strong> {{ $leaveRequest->leaveType->name ?? 'N/A' }}</li>
                                    <li><strong>Period:</strong> {{ \Carbon\Carbon::parse($leaveRequest->start_date)->format('d M Y') }} to {{ \Carbon\Carbon::parse($leaveRequest->end_date)->format('d M Y') }}</li>
                                    <li><strong>Total Days:</strong> {{ $leaveRequest->days }} day(s)</li>
                                    <li><strong>Acting Staff:</strong> {{ $leaveRequest->actingStaff->full_name ?? 'Not specified' }}</li>
                                </ul>
                                
                                @if($leaveRequest->handover_notes)
                                    <p><strong>Handover Notes:</strong><br>{{ $leaveRequest->handover_notes }}</p>
                                @endif
                                
                                <p>Please ensure proper handover with your designated acting staff before proceeding on leave.</p>
                                <p>We wish you a pleasant break and look forward to your return on {{ \Carbon\Carbon::parse($leaveRequest->expected_return_date ?? $leaveRequest->end_date)->addDay()->format('d M Y') }}.</p>
                            </div>
                            
                            <!-- Signatory -->
                            <div class="text-end mt-3">
                                <p>Yours sincerely,</p>
                                <div class="mt-2" style="margin-top: 2rem !important;">
                                    <p><strong>{{ $letter->generated_by ?? 'Human Resources Department' }}</strong><br>
                                    {{ $letter->generated_by_role ?? 'HR Officer' }}</p>
                                </div>
                            </div>
                            
                            <!-- Footer -->
                            <hr style="margin: 0.2rem 0;">
                            <div class="text-center" style="font-size: 0.55rem;">
                                <i class="ti ti-lock"></i> Secure Document - Verification Code: {{ $letter->secure_code ?? 'N/A' }}
                            </div>
                        @else
                            <x-alert-info>
    Letter will be generated upon HR Logistics completion.
</x-alert-info>
                        @endif
                    </div>
                </div>
            </div>
            @endif
            
            <!-- Quick Information Card -->
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-tips text-warning me-1" style="font-size: 0.7rem;"></i>
                        <span style="font-weight: 600; font-size: 0.7rem;">Quick Information</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    
                    <!-- Your Decision / Your Role Section -->
                    <x-alert-light class="mb-2 py-1 px-2 rounded-sm">

                        <div class="d-flex align-items-start gap-1">
                            <i class="ti ti-user-check text-success mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                @php
                                    $user = auth()->user();
                                    $role = $user->getRoleNames()->first();
                                    
                                    // Check if user approved this request
                                    $userApproval = $leaveRequest->approvalLogs
                                        ->where('approver_staff_id', $user->id)
                                        ->where('action', 'approved')
                                        ->first();
                                    
                                    // Check if user rejected this request
                                    $userRejection = $leaveRequest->approvalLogs
                                        ->where('approver_staff_id', $user->id)
                                        ->where('action', 'rejected')
                                        ->first();
                                    
                                    // Check if user is the requester
                                    $isRequester = ($leaveRequest->staff_id == $user->id);
                                    
                                    // Check if user is the acting staff
                                    $isActingStaff = ($leaveRequest->acting_staff_id == $user->id);
                                    
                                    // Determine the message
                                    if ($userApproval) {
                                        $actionText = 'Approved';
                                        $actionIcon = 'check-circle';
                                        $actionColor = 'success';
                                        $actionDate = $userApproval->created_at;
                                        $actionBy = 'You approved';
                                    } elseif ($userRejection) {
                                        $actionText = 'Rejected';
                                        $actionIcon = 'x-circle';
                                        $actionColor = 'danger';
                                        $actionDate = $userRejection->created_at;
                                        $actionBy = 'You rejected';
                                    } elseif ($isRequester) {
                                        $actionText = 'Submitted';
                                        $actionIcon = 'file';
                                        $actionColor = 'primary';
                                        $actionDate = $leaveRequest->created_at;
                                        $actionBy = 'You requested';
                                    } elseif ($isActingStaff && $leaveRequest->acting_responded_at) {
                                        $actionText = ucfirst($leaveRequest->acting_status);
                                        $actionIcon = $leaveRequest->acting_status == 'accepted' ? 'check' : 'x';
                                        $actionColor = $leaveRequest->acting_status == 'accepted' ? 'success' : 'danger';
                                        $actionDate = $leaveRequest->acting_responded_at;
                                        $actionBy = 'You responded';
                                    } else {
                                        $actionText = 'Viewed';
                                        $actionIcon = 'eye';
                                        $actionColor = 'secondary';
                                        $actionDate = now();
                                        $actionBy = 'You viewed';
                                    }
                                @endphp
                                
                                <strong class="d-block" style="font-size: 0.65rem;">Your Role: {{ ucfirst($role) }}</strong>
                                <div class="mt-1">
                                    <i class="ti ti-{{ $actionIcon }} text-{{ $actionColor }} me-1" style="font-size: 0.65rem;"></i>
                                    <span class="text-{{ $actionColor }} fw-semibold" style="font-size: 0.65rem;">
                                        {{ $actionBy }} this request
                                    </span>
                                    <span class="text-muted" style="font-size: 0.6rem;">
                                        on {{ \Carbon\Carbon::parse($actionDate)->format('d M Y') }}
                                    </span>
                                </div>
                            </div>
                        
</x-alert-light>
                    </div>
                    
                    <!-- Document Access -->
                    <x-alert-light class="mb-2 py-1 px-2 rounded-sm">

                        <div class="d-flex align-items-start gap-1">
                            <i class="ti ti-file me-1 text-primary"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">Document Access</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">
                                    @hasanyrole('admin|hr-officer|dvca|dvc-pfa|vice-chancellor')
                                        You have full access to all documents and financial details.
                                    @elseif($isRequester)
                                        You can view your own documents and the approval letter.
                                    @elseif($isActingStaff)
                                        You can view the leave details and acting status.
                                    @else
                                        You can view basic request details and the approval letter.
                                    @endhasanyrole
                                </small>
                            </div>
                        
</x-alert-light>
                    </div>
                    
                    <!-- Need Help -->
                    <x-alert-light class="mb-0 py-1 px-2 rounded-sm">

                        <div class="d-flex align-items-start gap-1">
                            <i class="ti ti-clock text-primary mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">Need Help?</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">
                                    Contact HR department for any questions about this request.
                                </small>
                            </div>
                        
</x-alert-light>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style media="print">
    .sidebar-area, .topbar, .page-header, .btn, .breadcrumb, .card-header .btn, .sticky-banner {
        display: none !important;
    }
    .content-area {
        margin: 0 !important;
        padding: 0 !important;
    }
    .main-content {
        padding: 0 !important;
    }
    .card {
        border: none !important;
    }
    .card-header {
        display: none !important;
    }
    .letter-container {
        font-size: 12pt !important;
    }
    body {
        background: white !important;
    }
    .container-fluid {
        padding: 0 !important;
    }
</style>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js" integrity="sha512-GsLlZN/3F2ErC5ifS5QtgpiJtWd43JWSuIgh7mbzZ8zBps+dvLusV+eNQATqgA/HdeKFVgA5v3S/cIrLF7QnIg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script>
function downloadPDF() {
    const element = document.getElementById('letterContent');
    const opt = {
        margin: [0.5, 0.5, 0.5, 0.5],
        filename: 'Leave_Approval_Letter_{{ $leaveRequest->id }}.pdf',
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2, letterRendering: true },
        jsPDF: { unit: 'in', format: 'a4', orientation: 'portrait' }
    };
    html2pdf().set(opt).from(element).save();
}
</script>
@endsection