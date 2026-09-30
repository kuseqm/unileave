@extends('layouts.master')

@section('title', 'HR Logistics')

@section('content')
<div class="container-fluid px-2">
    
    <x-breadcrumb :items="[
        ['name' => 'HR Logistics', 'url' => '#']
    ]" />
    
    <x-page-header title="HR LOGISTICS" description="Final processing of approved leave requests">
    </x-page-header>
    
    <x-alert-success />
    
    <div class="row">
        <div class="col-lg-8">
            <!-- Leave Request Details -->
            <div class="card rounded-sm mb-3">
                <div class="card-header py-1 px-2 bg-light">
                    <i class="ti ti-file-text me-1"></i>
                    <span style="font-weight: 600;">Leave Request Details</span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="text-muted" style="font-size: 0.6rem;">Staff Name</div>
                            <div class="fw-semibold" style="font-size: 0.75rem;">{{ $leaveRequest->staff->full_name }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted" style="font-size: 0.6rem;">Staff ID</div>
                            <div class="fw-semibold" style="font-size: 0.75rem;">{{ $leaveRequest->staff->staff_id }}</div>
                        </div>
                        <div class="col-md-6 mt-2">
                            <div class="text-muted" style="font-size: 0.6rem;">Department</div>
                            <div style="font-size: 0.75rem;">{{ $leaveRequest->staff->department->name ?? 'N/A' }}</div>
                        </div>
                        <div class="col-md-6 mt-2">
                            <div class="text-muted" style="font-size: 0.6rem;">Leave Type</div>
                            <div style="font-size: 0.75rem;">{{ $leaveRequest->leaveType->name ?? 'N/A' }}</div>
                        </div>
                        <div class="col-md-6 mt-2">
                            <div class="text-muted" style="font-size: 0.6rem;">Leave Period</div>
                            <div style="font-size: 0.75rem;">{{ \Carbon\Carbon::parse($leaveRequest->start_date)->format('d M Y') }} - {{ \Carbon\Carbon::parse($leaveRequest->end_date)->format('d M Y') }}</div>
                        </div>
                        <div class="col-md-6 mt-2">
                            <div class="text-muted" style="font-size: 0.6rem;">Total Days</div>
                            <div style="font-size: 0.75rem;">{{ $leaveRequest->days }} day(s)</div>
                        </div>
                        <div class="col-12 mt-2">
                            <div class="text-muted" style="font-size: 0.6rem;">Reason</div>
                            <div class="p-1 bg-light rounded-sm" style="font-size: 0.7rem;">{{ $leaveRequest->reason }}</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Approval Letter Preview -->
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <i class="ti ti-file me-1"></i>
                    <span style="font-weight: 600;">Approval Letter</span>
                </div>
                <div class="card-body">
                    <div class="letter-preview p-3 border rounded-sm" style="background: #FDF8F0; font-family: 'Times New Roman', serif;">
                        <div class="text-center mb-3">
                            <h5>{{ $university->name ?? 'Saint Mary\'s University' }}</h5>
                            <p class="mb-0" style="font-size: 0.7rem;">Office of the Human Resources</p>
                            <hr>
                            <h6 style="font-size: 0.85rem;">LEAVE APPROVAL LETTER</h6>
                        </div>
                        
                        <div style="font-size: 0.75rem; line-height: 1.5;">
                            <p>Ref: SJUT/HR/{{ date('Y') }}/{{ str_pad(\App\Models\LeaveLetter::whereYear('created_at', date('Y'))->count() + 1, 4, '0', STR_PAD_LEFT) }}</p>
                            <p>Date: {{ now()->format('jS F Y') }}</p>
                            <br>
                            <p>To: <strong>{{ $leaveRequest->staff->full_name }}</strong><br>
                            {{ $leaveRequest->staff->staff_id }}<br>
                            {{ $leaveRequest->staff->department->name ?? 'N/A' }}</p>
                            <br>
                            <p>Dear <strong>{{ $leaveRequest->staff->first_name }}</strong>,</p>
                            <br>
                            <p>RE: LEAVE APPROVAL - {{ $leaveRequest->leaveType->name ?? 'N/A' }}</p>
                            <br>
                            <p>1. Your leave request has been <strong>APPROVED</strong> as follows:</p>
                            <br>
                            <table style="width: 100%; border-collapse: collapse;">
                                <tr>
                                    <td style="width: 40%;">Leave Type:</td>
                                    <td style="width: 60%;"><strong>{{ $leaveRequest->leaveType->name ?? 'N/A' }}</strong></td>
                                </tr>
                                <tr>
                                    <td>Start Date:</td>
                                    <td><strong>{{ \Carbon\Carbon::parse($leaveRequest->start_date)->format('jS F Y') }}</strong></td>
                                </tr>
                                <tr>
                                    <td>End Date:</td>
                                    <td><strong>{{ \Carbon\Carbon::parse($leaveRequest->end_date)->format('jS F Y') }}</strong></td>
                                </tr>
                                <tr>
                                    <td>Total Days:</td>
                                    <td><strong>{{ $leaveRequest->days }} day(s)</strong></td>
                                </tr>
                            </table>
                            <br>
                            <p>2. During your leave, <strong>{{ $leaveRequest->actingStaff->full_name ?? 'N/A' }}</strong> will hold your position.</p>
                            <br>
                            <p>3. Please ensure proper handover before proceeding on leave.</p>
                            <br>
                            <div class="mt-4">
                                <p>Kind regards,</p>
                                <br><br>
                                <p><strong>Human Resources Department</strong></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <button type="button" class="btn btn-sm btn-outline-info" onclick="window.print()">
                            <i class="ti ti-printer"></i> Print Letter
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <!-- Action Card -->
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <i class="ti ti-settings me-1"></i>
                    <span style="font-weight: 600;">Final Processing</span>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('leave-requests.hr-logistics.process', $leaveRequest->id) }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label class="form-label" style="font-size: 0.7rem;">HR Comments (Optional)</label>
                            <textarea name="hr_comment" class="form-control form-control-sm" rows="3" style="font-size: 0.7rem;" placeholder="Add any remarks about this leave..."></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input type="hidden" name="generate_letter" value="0">
                                <input type="checkbox" class="form-check-input" name="generate_letter" value="1" id="generateLetter" checked>
                                <label class="form-check-label" for="generateLetter" style="font-size: 0.7rem;">Generate Approval Letter</label>
                            </div>
                        </div>

                        <div id="letterSignatureBlock" class="mb-3">
                            <x-signature-pad
                                name="signature_data"
                                id="hr-logistics-signature"
                                label="Your Signature"
                                help-text="Sign to confirm and finalize this approval letter. This signature is printed on the letter."
                                :height="110"
                                :required="false"
                            />
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input type="hidden" name="send_notification" value="0">
                                <input type="checkbox" class="form-check-input" name="send_notification" value="1" id="sendNotification" checked>
                                <label class="form-check-label" for="sendNotification" style="font-size: 0.7rem;">Notify Staff</label>
                            </div>
                        </div>

                        <hr>

                        <button type="submit" class="btn btn-success btn-sm w-100">
                            <i class="ti ti-check"></i> Complete & Approve
                        </button>
                        <a href="{{ route('leave-requests.pending') }}" class="btn btn-secondary btn-sm w-100 mt-2">
                            <i class="ti ti-arrow-left"></i> Back to Pending
                        </a>
                    </form>
                </div>
            </div>
            
            <!-- Approval Timeline Summary -->
            <div class="card rounded-sm mt-3">
                <div class="card-header py-1 px-2 bg-light">
                    <i class="ti ti-history me-1"></i>
                    <span style="font-weight: 600;">Approval Summary</span>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @foreach($leaveRequest->approvalLogs as $log)
                        <div class="list-group-item py-1 px-2" style="font-size: 0.65rem;">
                            <div class="d-flex justify-content-between">
                                <span>{{ ucfirst($log->from_role) }}</span>
                                <span class="text-success">{{ ucfirst($log->action) }}</span>
                                <span class="text-muted">{{ $log->created_at->format('d M H:i') }}</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const generateLetter = document.getElementById('generateLetter');
    const signatureBlock = document.getElementById('letterSignatureBlock');
    const signatureData = document.getElementById('hr-logistics-signature-data');
    const form = generateLetter?.closest('form');

    function syncSignatureVisibility() {
        if (!signatureBlock) return;
        signatureBlock.style.display = generateLetter.checked ? '' : 'none';
    }

    generateLetter?.addEventListener('change', syncSignatureVisibility);
    syncSignatureVisibility();

    form?.addEventListener('submit', function (e) {
        if (generateLetter?.checked && !signatureData?.value) {
            e.preventDefault();
            alert('Please sign to finalize the approval letter, or uncheck "Generate Approval Letter".');
        }
    });
});
</script>
@endpush
@endsection