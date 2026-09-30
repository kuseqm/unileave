@extends('layouts.master')

@section('title', $letter->title)

@section('content')
<div class="container-fluid px-2">
    
    <x-breadcrumb :items="[
        ['name' => 'Letters', 'url' => route('letters.index')],
        ['name' => $letter->title, 'url' => '#']
    ]" />
    
    <x-page-header title="VIEW LETTER" :description="'Reference: ' . ($letter->letter_number ?? 'Draft')">
        <div class="d-flex gap-1 flex-wrap">
            @if($letter->stage === 'draft')
                <a href="{{ route('letters.edit', $letter->id) }}" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
                    <i class="ti ti-edit me-1"></i> Edit
                </a>
                <form action="{{ route('letters.submit', $letter->id) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="button" class="btn btn-success btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;" onclick="confirmSubmit(this, 'Submit this letter for approval?', { danger: false, confirmText: 'Submit' })">
                        <i class="ti ti-send me-1"></i> Submit for Approval
                    </button>
                </form>
                <form action="{{ route('letters.destroy', $letter->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-danger btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;" onclick="confirmSubmit(this, 'Delete this letter? This cannot be undone.')">
                        <i class="ti ti-trash me-1"></i> Delete
                    </button>
                </form>
            @endif
            
            <!-- HOD Approval Button -->
            @if($letter->stage === 'pending_approval' && !$letter->hod_approved_at && auth()->user()->hasRole('hod') && auth()->user()->department_id === $letter->recipient->department_id)
                <form action="{{ route('letters.hod.approve', $letter->id) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="button" class="btn btn-success btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;" onclick="confirmSubmit(this, 'Approve this letter as Head of Department? This action will be recorded with your IP address.', { danger: false, confirmText: 'Approve' })">
                        <i class="ti ti-check me-1"></i> Approve as HOD
                    </button>
                </form>
            @endif
            
            <!-- Dean Approval Button -->
            @if($letter->stage === 'pending_approval' && $letter->hod_approved_at && !$letter->dean_approved_at && auth()->user()->hasRole('dean'))
                <form action="{{ route('letters.dean.approve', $letter->id) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="button" class="btn btn-success btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;" onclick="confirmSubmit(this, 'Approve this letter as Dean? This action will be recorded with your IP address.', { danger: false, confirmText: 'Approve' })">
                        <i class="ti ti-check me-1"></i> Approve as Dean
                    </button>
                </form>
            @endif
            
            <!-- DVC Approval Button -->
            @if($letter->stage === 'pending_approval' && $letter->hod_approved_at && $letter->dean_approved_at && !$letter->dvc_approved_at)
                @php
                    $expectedRole = $letter->recipient->staff_category === 'academic' ? 'dvca' : 'dvc-pfa';
                @endphp
                @if(auth()->user()->hasRole($expectedRole))
                    <form action="{{ route('letters.dvc.approve', $letter->id) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="button" class="btn btn-success btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;" onclick="confirmSubmit(this, 'Approve this letter as DVC? This action will be recorded with your IP address.', { danger: false, confirmText: 'Approve' })">
                            <i class="ti ti-check me-1"></i> Approve as DVC
                        </button>
                    </form>
                @endif
            @endif
            
            <!-- VC Signature Button (Opens Modal for PIN) -->
            @if($letter->stage === 'approved' && !$letter->vc_signed_at && auth()->user()->hasRole('vice-chancellor'))
                <button type="button" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;" data-bs-toggle="modal" data-bs-target="#vcSignatureModal">
                    <i class="ti ti-pen me-1"></i> Sign as VC
                </button>
            @endif
            
            <!-- Print & PDF for Signed Letters -->
            @if($letter->stage === 'signed')
                <a href="{{ route('letters.print', $letter->id) }}" target="_blank" class="btn btn-info btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
                    <i class="ti ti-printer me-1"></i> Print
                </a>
                <a href="{{ route('letters.pdf', $letter->id) }}" class="btn btn-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
                    <i class="ti ti-file-pdf me-1"></i> Download PDF
                </a>
            @endif
            
            <a href="{{ route('letters.index') }}" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
                <i class="ti ti-arrow-left me-1"></i> Back
            </a>
        </div>
    </x-page-header>
    
    <div class="row g-2">
        <div class="col-lg-8">
            <!-- Letter Preview Card -->
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <i class="ti ti-file-text text-primary me-1" style="font-size: 0.8rem;"></i>
                            <span style="font-size: 0.7rem; font-weight: 600;">Letter Preview</span>
                        </div>
                        <div>
                            @include('letters.partials.status-badge', ['status' => $letter->stage])
                        </div>
                    </div>
                </div>
                <div class="card-body p-3" style="background: #fff; font-family: 'Times New Roman', serif;">
                    
                    <!-- Letter Header -->
                    <div class="letter-header text-center mb-3">
                        <h2 style="font-family: 'Playfair Display', serif; font-size: 16pt; font-weight: 600; margin-bottom: 5px;">
                            {{ $university->name ?? 'ST JOHN\'S UNIVERSITY OF TANZANIA' }}
                        </h2>
                        <p style="font-size: 9pt; margin: 0;">{{ $university->address ?? 'P. O. Box 47, Dodoma, Tanzania' }}</p>
                        <p style="font-size: 9pt; margin: 0;">Tel: {{ $university->phone ?? '+255 67 70 86093' }} | Email: {{ $university->email ?? 'info@university.ac.tz' }}</p>
                        <hr style="margin: 8px 0;">
                    </div>
                    
                    <!-- Reference Section -->
                    <div style="margin-bottom: 20px;">
                        <div style="display: flex; justify-content: space-between; flex-wrap: wrap;">
                            <div><strong>Our Ref:</strong> {{ $letter->letter_number ?? 'Draft' }}</div>
                            <div><strong>Date:</strong> {{ $letter->created_at->format('d F Y') }}</div>
                        </div>
                        @if($letter->your_ref)
                        <div><strong>Your Ref:</strong> {{ $letter->your_ref }}</div>
                        @endif
                        @if($letter->secure_code)
                        <div><strong>Secure Code:</strong> <code>{{ $letter->secure_code }}</code></div>
                        @endif
                    </div>
                    
                    <!-- Recipient -->
                    <div style="margin-bottom: 20px;">
                        <p style="margin: 0;"><strong>To:</strong> {{ $letter->recipient->full_name ?? 'N/A' }}</p>
                        <p style="margin: 0;">{{ $letter->recipient->staff_id ?? 'N/A' }}</p>
                        <p style="margin: 0;">{{ $letter->recipient->department->name ?? 'N/A' }}</p>
                    </div>
                    
                    <!-- Subject -->
                    <div style="margin-bottom: 15px;">
                        <p><strong>RE: {{ $letter->title }}</strong></p>
                    </div>
                    
                    <!-- Letter Body -->
                    <div style="line-height: 1.5; font-size: 11pt; text-align: justify;">
                        {!! nl2br(e($letter->content)) !!}
                    </div>
                    
                    <!-- Signature -->
                    <div style="margin-top: 40px;">
                        <p>Yours sincerely,</p>
                        <br><br>
                        <p><strong>{{ $letter->signatory ?? $letter->sender->full_name ?? '_________________________' }}</strong></p>
                        <p>{{ $letter->signatory_title ?? $letter->sender->getRoleNames()->first() ?? 'Staff' }}</p>
                        @if($letter->vc_signed_at)
                        <p style="font-size: 8pt; color: #666; margin-top: 10px;">
                            Digitally signed on {{ $letter->vc_signed_at->format('d F Y H:i:s') }}<br>
                            Signature IP: {{ $letter->vc_ip_address }}
                        </p>
                        @endif
                    </div>
                    
                    <!-- CC -->
                    @if($letter->cc_recipients && count(json_decode($letter->cc_recipients, true)) > 0)
                    <div style="margin-top: 30px;">
                        <strong>CC:</strong>
                        <ul style="margin: 5px 0 0 20px;">
                            @foreach(json_decode($letter->cc_recipients, true) as $ccId)
                                @php $ccUser = App\Models\User::find($ccId); @endphp
                                @if($ccUser)
                                <li>{{ $ccUser->full_name }} ({{ $ccUser->staff_id ?? 'N/A' }})</li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                    @endif
                    
                    <!-- Footer -->
                    <div style="margin-top: 40px; border-top: 1px solid #ccc; padding-top: 10px; font-size: 8pt; text-align: center;">
                        <p>This is a computer-generated letter. No signature is required.</p>
                        @if($letter->secure_code && $letter->stage === 'signed')
                        <p>To verify authenticity, visit: {{ route('verify.letter', $letter->secure_code) }}</p>
                        @endif
                    </div>
                    
                </div>
            </div>
        </div>
        
        <!-- Info Panel -->
        <div class="col-lg-4">
            <!-- Letter Status Card -->
            <div class="card rounded-sm mb-2">
                <div class="card-header py-1 px-2 bg-light">
                    <i class="ti ti-info-circle me-1"></i>
                    <span style="font-weight: 600;">Letter Information</span>
                </div>
                <div class="card-body py-2 px-2">
                    <table class="table table-sm table-borderless mb-0" style="font-size: 0.7rem;">
                        <tr><td class="text-muted ps-0" style="width: 35%;">Created By:</td><td class="fw-semibold">{{ $letter->sender->full_name ?? 'N/A' }}</td></tr>
                        <tr><td class="text-muted ps-0">Created At:</td><td class="fw-semibold">{{ $letter->created_at->format('d M Y, H:i') }}</td></tr>
                        <tr><td class="text-muted ps-0">Last Updated:</td><td class="fw-semibold">{{ $letter->updated_at->format('d M Y, H:i') }}</td></tr>
                        @if($letter->submitted_at)
                        <tr><td class="text-muted ps-0">Submitted At:</td><td class="fw-semibold">{{ $letter->submitted_at->format('d M Y, H:i') }}</td></tr>
                        @endif
                        @if($letter->hod_approved_at)
                        <tr><td class="text-muted ps-0">HOD Approved:</td><td class="fw-semibold">{{ $letter->hod_approved_at->format('d M Y, H:i') }}</td>
                        </tr>
                        @endif
                        @if($letter->dean_approved_at)
                        <tr><td class="text-muted ps-0">Dean Approved:</td><td class="fw-semibold">{{ $letter->dean_approved_at->format('d M Y, H:i') }}</td>
                        </tr>
                        @endif
                        @if($letter->dvc_approved_at)
                        <tr><td class="text-muted ps-0">DVC Approved:</td><td class="fw-semibold">{{ $letter->dvc_approved_at->format('d M Y, H:i') }}</td>
                        </tr>
                        @endif
                        @if($letter->vc_signed_at)
                        <tr><td class="text-muted ps-0">VC Signed:</td><td class="fw-semibold">{{ $letter->vc_signed_at->format('d M Y, H:i') }}</td>
                        </tr>
                        @endif
                        @if($letter->file_number)
                        <tr><td class="text-muted ps-0">File Number:</td><td class="fw-semibold">{{ $letter->file_number }}</td></tr>
                        @endif
                    </table>
                </div>
            </div>
            
            <!-- Approval Timeline -->
            <div class="card rounded-sm mb-2">
                <div class="card-header py-1 px-2 bg-light">
                    <i class="ti ti-timeline me-1"></i>
                    <span style="font-weight: 600;">Approval Timeline</span>
                </div>
                <div class="card-body p-0">
                    <div class="p-2">
                        <div class="d-flex mb-2">
                            <div class="me-2">
                                <div class="rounded-circle {{ $letter->submitted_at ? 'bg-success' : 'bg-secondary' }} d-flex align-items-center justify-content-center" style="width: 24px; height: 24px;">
                                    <i class="ti ti-send text-white" style="font-size: 0.6rem;"></i>
                                </div>
                            </div>
                            <div>
                                <div class="fw-semibold" style="font-size: 0.7rem;">Submitted</div>
                                <div class="text-muted" style="font-size: 0.6rem;">{{ $letter->submitted_at ? $letter->submitted_at->format('d M Y, H:i') : 'Pending' }}</div>
                            </div>
                        </div>
                        
                        <div class="d-flex mb-2">
                            <div class="me-2">
                                <div class="rounded-circle {{ $letter->hod_approved_at ? 'bg-success' : 'bg-secondary' }} d-flex align-items-center justify-content-center" style="width: 24px; height: 24px;">
                                    <i class="ti ti-user-check text-white" style="font-size: 0.6rem;"></i>
                                </div>
                            </div>
                            <div>
                                <div class="fw-semibold" style="font-size: 0.7rem;">HOD Approval</div>
                                <div class="text-muted" style="font-size: 0.6rem;">{{ $letter->hod_approved_at ? $letter->hod_approved_at->format('d M Y, H:i') : 'Pending' }}</div>
                                @if($letter->hod_ip_address)
                                <div class="text-muted" style="font-size: 0.55rem;">IP: {{ $letter->hod_ip_address }}</div>
                                @endif
                            </div>
                        </div>
                        
                        <div class="d-flex mb-2">
                            <div class="me-2">
                                <div class="rounded-circle {{ $letter->dean_approved_at ? 'bg-success' : 'bg-secondary' }} d-flex align-items-center justify-content-center" style="width: 24px; height: 24px;">
                                    <i class="ti ti-building text-white" style="font-size: 0.6rem;"></i>
                                </div>
                            </div>
                            <div>
                                <div class="fw-semibold" style="font-size: 0.7rem;">Dean Approval</div>
                                <div class="text-muted" style="font-size: 0.6rem;">{{ $letter->dean_approved_at ? $letter->dean_approved_at->format('d M Y, H:i') : 'Pending' }}</div>
                                @if($letter->dean_ip_address)
                                <div class="text-muted" style="font-size: 0.55rem;">IP: {{ $letter->dean_ip_address }}</div>
                                @endif
                            </div>
                        </div>
                        
                        <div class="d-flex mb-2">
                            <div class="me-2">
                                <div class="rounded-circle {{ $letter->dvc_approved_at ? 'bg-success' : 'bg-secondary' }} d-flex align-items-center justify-content-center" style="width: 24px; height: 24px;">
                                    <i class="ti ti-crown text-white" style="font-size: 0.6rem;"></i>
                                </div>
                            </div>
                            <div>
                                <div class="fw-semibold" style="font-size: 0.7rem;">DVC Approval</div>
                                <div class="text-muted" style="font-size: 0.6rem;">{{ $letter->dvc_approved_at ? $letter->dvc_approved_at->format('d M Y, H:i') : 'Pending' }}</div>
                                @if($letter->dvc_ip_address)
                                <div class="text-muted" style="font-size: 0.55rem;">IP: {{ $letter->dvc_ip_address }}</div>
                                @endif
                            </div>
                        </div>
                        
                        <div class="d-flex">
                            <div class="me-2">
                                <div class="rounded-circle {{ $letter->vc_signed_at ? 'bg-success' : 'bg-secondary' }} d-flex align-items-center justify-content-center" style="width: 24px; height: 24px;">
                                    <i class="ti ti-pen text-white" style="font-size: 0.6rem;"></i>
                                </div>
                            </div>
                            <div>
                                <div class="fw-semibold" style="font-size: 0.7rem;">VC Signature</div>
                                <div class="text-muted" style="font-size: 0.6rem;">{{ $letter->vc_signed_at ? $letter->vc_signed_at->format('d M Y, H:i') : 'Pending' }}</div>
                                @if($letter->vc_ip_address)
                                <div class="text-muted" style="font-size: 0.55rem;">IP: {{ $letter->vc_ip_address }}</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Approval Progress -->
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <i class="ti ti-chart-donut me-1"></i>
                    <span style="font-weight: 600;">Approval Progress</span>
                </div>
                <div class="card-body py-2 px-2">
                    <div class="progress mb-2" style="height: 8px;">
                        @php
                            $progress = 0;
                            if ($letter->hod_approved_at) $progress += 25;
                            if ($letter->dean_approved_at) $progress += 25;
                            if ($letter->dvc_approved_at) $progress += 25;
                            if ($letter->vc_signed_at) $progress += 25;
                        @endphp
                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $progress }}%;" aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div class="text-center">
                        <small class="text-muted">{{ $progress }}% Complete</small>
                    </div>
                    <div class="mt-2 text-center">
                        <small class="text-muted">
                            Next: {{ $letter->next_approver_role }}
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- VC Signature Modal (Password Protected) -->
@if($letter->stage === 'approved' && !$letter->vc_signed_at && auth()->user()->hasRole('vice-chancellor'))
<div class="modal fade" id="vcSignatureModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-sm">
            <form action="{{ route('letters.vc.sign', $letter->id) }}" method="POST">
                @csrf
                <div class="modal-header py-2 px-3 border-bottom">
                    <h5 class="modal-title" style="font-size: 0.9rem;">
                        <i class="ti ti-pen me-2 text-primary"></i> Digital Signature Required
                    </h5>
                    <button type="button" class="btn-close py-0" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body py-3 px-3">
                    <x-alert-info>
    By signing this letter, you confirm it is authentic and approved.
                        This action is legally binding and will be recorded.
</x-alert-info>
                    
                    <div class="mb-2">
                        <label class="form-label" style="font-size: 0.7rem;">Enter Your Signature PIN</label>
                        <input type="password" name="signature_pin" class="form-control form-control-sm rounded-sm" required style="font-size: 0.7rem;" placeholder="Enter your digital signature PIN" autocomplete="off">
                        <small class="text-muted" style="font-size: 0.55rem;">Your PIN is secure and never stored in plain text.</small>
                    </div>
                    
                    <x-alert-secondary>
    <strong>What will be recorded:</strong>
                        <ul class="mb-0 mt-1 ps-3">
                            <li>Your name and role (Vice Chancellor)</li>
                            <li>Date and time of signature</li>
                            <li>IP address ({{ request()->ip() }})</li>
                            <li>Browser/device information</li>
                            <li>Document hash for verification</li>
                        </ul>
</x-alert-secondary>
                </div>
                <div class="modal-footer py-2 px-3">
                    <button type="button" class="btn btn-secondary btn-sm rounded-sm" data-bs-dismiss="modal" style="font-size: 0.7rem;">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem;">
                        <i class="ti ti-pen me-1"></i> Sign Digitally
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection