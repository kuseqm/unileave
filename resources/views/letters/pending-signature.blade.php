@extends('layouts.master')

@section('title', 'Pending Signatures')

@section('content')
<div class="container-fluid px-2">
    
    <x-breadcrumb :items="[
        ['name' => 'Letters', 'url' => route('letters.index')],
        ['name' => 'Pending Signatures', 'url' => '#']
    ]" />
    
    <x-page-header title="PENDING SIGNATURES" description="Letters awaiting your digital signature">
    </x-page-header>
    
    <x-alert-success />
    
    <x-alert-danger />
    
    <!-- Stats Cards -->
    <div class="row g-2 mb-3">
        <x-stats-card value="{{ $stats['total'] }}" label="Awaiting Signature" subtext="ready for VC approval" color="warning" />
        <x-stats-card value="{{ $stats['this_week'] }}" label="This Week" subtext="approved by DVC in last 7 days" color="info" />
        <x-stats-card value="{{ $stats['this_month'] }}" label="This Month" subtext="approved by DVC in last 30 days" color="primary" />
        <x-stats-card value="{{ $stats['overdue'] }}" label="Overdue" subtext="waiting over 7 days" color="danger" />
    </div>
    
    <!-- Important Notice -->
    <x-alert-warning>
    <strong>Digital Signature Required:</strong> 
        By signing a letter, you confirm its authenticity and approve it as official.
        Your signature will be recorded with your name, timestamp, IP address, and a unique document hash.
        This is legally binding and cannot be undone.
</x-alert-warning>
    
    <div class="row g-2">
        <!-- Pending Signatures Table - main column -->
        <div class="col-lg-8">
            <div class="card rounded-sm">
        <div class="card-header py-1 px-2 bg-light">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center">
                    <i class="ti ti-pen text-primary me-1" style="font-size: 0.8rem;"></i>
                    <span style="font-size: 0.7rem; font-weight: 600;">Letters Awaiting Vice Chancellor Signature</span>
                </div>
                <div class="text-muted" style="font-size: 0.6rem;">
                    {{ $letters->total() }} letter(s) pending signature
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            @if($letters->count() > 0)
            <div class="table-responsive">
                <table class="table table-sm mb-0" style="font-size: 0.75rem;">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-2 py-1" width="50">#</th>
                            <th class="py-1">Letter Number</th>
                            <th class="py-1">Title</th>
                            <th class="py-1">From (Sender)</th>
                            <th class="py-1">To (Recipient)</th>
                            <th class="py-1">Department</th>
                            <th class="py-1">Approved By</th>
                            <th class="pe-2 py-1" width="100">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($letters as $index => $letter)
                        <tr>
                            <td class="ps-2 py-1">{{ $letters->firstItem() + $index }}</td>
                            <td class="py-1 fw-semibold">{{ $letter->letter_number ?? 'N/A' }}</td>
                            <td class="py-1">{{ Str::limit($letter->title, 35) }}</td>
                            <td class="py-1">{{ $letter->sender->full_name ?? 'N/A' }}</td>
                            <td class="py-1">{{ $letter->recipient->full_name ?? 'N/A' }}</td>
                            <td class="py-1">{{ $letter->recipient->department->name ?? 'N/A' }}</td>
                            <td class="py-1">
                                <div class="small">
                                    @if($letter->hod_approved_at)
                                    <span class="text-success">✓ HOD</span><br>
                                    @endif
                                    @if($letter->dean_approved_at)
                                    <span class="text-success">✓ Dean</span><br>
                                    @endif
                                    @if($letter->dvc_approved_at)
                                    <span class="text-success">✓ DVC</span>
                                    @endif
                                </div>
                            </td>
                            <td class="pe-2 py-1">
                                <div class="d-flex gap-1">
                                    <a href="{{ route('letters.show', $letter->id) }}" class="btn btn-sm btn-outline-info rounded-sm" style="font-size: 0.6rem; padding: 0.15rem 0.4rem;" title="View Letter">
                                        <i class="ti ti-eye"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-primary rounded-sm" style="font-size: 0.6rem; padding: 0.15rem 0.4rem;" title="Sign Letter" data-bs-toggle="modal" data-bs-target="#signModal{{ $letter->id }}">
                                        <i class="ti ti-pen"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-center py-4 text-muted" style="font-size: 0.7rem;">
                <i class="ti ti-check-circle fs-4"></i>
                <p class="mb-0 mt-2">No letters awaiting signature at this time.</p>
                <small>All letters have been signed. Great work!</small>
            </div>
            @endif
        </div>
        
        @if($letters->hasPages())
        <div class="card-footer py-2 px-2 bg-light">
            {{ $letters->links() }}
        </div>
        @endif
            </div>
        </div>

        <!-- Information - narrow column -->
        <div class="col-lg-4">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-info-circle text-info me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Information</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <div class="mb-2">
                        <div class="fw-semibold" style="font-size: 0.7rem;"><i class="ti ti-flag me-1 text-primary"></i> About Digital Signature</div>
                        <ul class="mb-0 ps-3" style="font-size: 0.65rem; color: #555;">
                            <li>Letters here have already cleared HOD, Dean, and DVC approval</li>
                            <li>Signing requires your PIN and is legally binding</li>
                            <li>Your name, timestamp, IP, and a document hash are recorded</li>
                        </ul>
                    </div>

                    <x-alert-light class="mb-0 py-1 px-2 rounded-sm">
                        <div class="d-flex align-items-start gap-1">
                            <i class="ti ti-tips text-warning mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">Tips</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">&bull; Double-check the letter content before signing</small><br>
                                <small class="text-muted" style="font-size: 0.6rem;">&bull; This action cannot be undone</small>
                            </div>
                        </div>
                    </x-alert-light>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Signature Modals for each letter -->
@foreach($letters as $letter)
<div class="modal fade" id="signModal{{ $letter->id }}" tabindex="-1">
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
    <strong>Letter:</strong> {{ $letter->letter_number }} - {{ Str::limit($letter->title, 50) }}
</x-alert-info>
                    
                    <x-alert-warning>
    By signing this letter, you confirm it is authentic and approved.
                        This action is legally binding and will be recorded.
</x-alert-warning>
                    
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
                            <li>Approval chain (HOD → Dean → DVC → VC)</li>
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
@endforeach

<script>
    // Auto-focus PIN input when modal opens
    document.querySelectorAll('[id^="signModal"]').forEach(modal => {
        modal.addEventListener('shown.bs.modal', function () {
            this.querySelector('input[name="signature_pin"]').focus();
        });
    });
</script>
@endsection