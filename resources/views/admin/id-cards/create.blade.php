@extends('layouts.master')

@section('title', 'Generate ID Card')

@section('content')
<div class="container-fluid px-2">

    <!-- Breadcrumb -->
    <x-breadcrumb :items="[
        ['name' => 'ID Cards', 'url' => route('admin.id-cards.index')],
        ['name' => 'Generate', 'url' => '#']
    ]" />

    <!-- Page Header -->
    <x-page-header title="GENERATE ID CARD" description="Issue a new identity card for a staff member">
        <a href="{{ route('admin.id-cards.index') }}" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
            <i class="ti ti-arrow-left me-1"></i> Back
        </a>
    </x-page-header>

    <div class="row g-2">
        <div class="col-lg-7">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-id text-primary me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Card Details</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <form action="{{ route('admin.id-cards.store') }}" method="POST" id="idCardForm">
                        @csrf

                        <div class="mb-1">
                            <label class="form-label" style="font-size: 0.65rem;">Staff Member <span class="text-danger">*</span></label>
                            <select name="staff_id" id="staff_id" class="form-select form-select-sm rounded-sm @error('staff_id') is-invalid @enderror" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;" required>
                                <option value="">Select Staff Member</option>
                                @foreach($staff as $member)
                                <option value="{{ $member->id }}"
                                        data-valid-until="{{ optional($member->id_card_valid_until)->format('Y-m-d') }}"
                                        {{ old('staff_id') == $member->id ? 'selected' : '' }}>
                                    {{ $member->staff_id }} — {{ $member->full_name }}
                                </option>
                                @endforeach
                            </select>
                            @error('staff_id')
                                <div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row g-1">
                            <div class="col-md-6 mb-1">
                                <label class="form-label" style="font-size: 0.65rem;">Issued On <span class="text-danger">*</span></label>
                                <input type="date"
                                       name="issued_at"
                                       class="form-control form-control-sm rounded-sm @error('issued_at') is-invalid @enderror"
                                       value="{{ old('issued_at', now()->format('Y-m-d')) }}"
                                       style="font-size: 0.7rem; padding: 0.2rem 0.3rem;"
                                       required>
                                @error('issued_at')
                                    <div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-1">
                                <label class="form-label" style="font-size: 0.65rem;">Valid Until <span class="text-danger">*</span></label>
                                <input type="date"
                                       name="valid_until"
                                       id="valid_until"
                                       class="form-control form-control-sm rounded-sm @error('valid_until') is-invalid @enderror"
                                       value="{{ old('valid_until') }}"
                                       style="font-size: 0.7rem; padding: 0.2rem 0.3rem;"
                                       required>
                                <small class="text-muted" style="font-size: 0.6rem;">Prefilled from the staff record — adjust if needed.</small>
                                @error('valid_until')
                                    <div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <x-alert-info>
    The staff member named above must sign below in person to confirm they are collecting this card. No one else should sign on their behalf.
</x-alert-info>

                        <x-signature-pad
                            name="signature_data"
                            id="signature-pad"
                            label="Staff Member's Signature"
                            help-text="Signed live at the point of collection by the staff member above — draw with mouse or touch."
                        />

                        <div class="d-flex justify-content-end gap-1 mt-2">
                            <a href="{{ route('admin.id-cards.index') }}" class="btn btn-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.25rem 0.5rem;">Cancel</a>
                            <button type="submit" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.25rem 0.5rem;">
                                <i class="ti ti-device-floppy me-1"></i> Generate ID Card
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-info-circle text-info me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Before You Generate</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <x-alert-light class="mb-0 py-1 px-2 rounded-sm">

                        <div class="d-flex align-items-start gap-1">
                            <i class="ti ti-tips text-warning mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">Important Notes</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">• Each staff member can have only one active ID card<br>
                                • The signature pad is required — it's what gets printed on the card<br>
                                • Only the staff member collecting the card should sign — no one else<br>
                                • The card can be previewed and printed after generation</small>
                            </div>
                        
</x-alert-light>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const staffSelect = document.getElementById('staff_id');
    const validUntilInput = document.getElementById('valid_until');

    staffSelect.addEventListener('change', function () {
        const option = staffSelect.options[staffSelect.selectedIndex];
        const validUntil = option ? option.dataset.validUntil : '';
        if (validUntil) {
            validUntilInput.value = validUntil;
        }
    });
});
</script>
@endsection
