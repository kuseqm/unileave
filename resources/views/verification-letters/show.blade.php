@extends('layouts.master')

@section('title', $letter->letter_number)

@section('content')
<div class="container-fluid px-2">

    <x-page-header title="VERIFICATION LETTER" description="{{ $letter->letter_number }}">
        <a href="{{ route('verification-letters.index') }}" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
            <i class="ti ti-arrow-left me-1"></i> All Letters
        </a>
        <a href="{{ route('verification-letters.print', $letter->id) }}?embedded=1" target="_blank" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
            <i class="ti ti-printer me-1"></i> Print
        </a>
        <a href="{{ route('verification-letters.download-pdf', $letter->id) }}" class="btn btn-success btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
            <i class="ti ti-download me-1"></i> PDF
        </a>
    </x-page-header>

    <x-alert-success />
    <x-alert-danger />

    <div class="row g-2">
        <div class="col-lg-8">
            <div class="card rounded-sm">
                <div class="card-body p-0">
                    <iframe src="{{ route('verification-letters.letter', $letter->id) }}?embedded=1" title="{{ $letter->letter_number }}" style="width: 100%; height: 82vh; border: 0; border-radius: 0 0 4px 4px;"></iframe>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card rounded-sm mb-2">
                <div class="card-header py-1 px-2 bg-light"><span style="font-size: 0.7rem; font-weight: 600;"><i class="ti ti-info-circle text-primary me-1"></i>Letter Details</span></div>
                <div class="card-body p-2" style="font-size: 0.72rem;">
                    <div class="mb-2"><div class="text-muted" style="font-size: 0.6rem;">Addressed to</div>{{ $letter->recipient_title }}<div class="fw-semibold">{{ $letter->recipient_organisation }}</div></div>
                    @if($letter->received_reference)
                    <div class="mb-2"><div class="text-muted" style="font-size: 0.6rem;">Their reference</div>{{ $letter->received_reference }}@if($letter->received_date) <span class="text-muted">· {{ $letter->received_date->format('d M Y') }}</span>@endif</div>
                    @endif
                    <div class="mb-2">
                        <div class="text-muted" style="font-size: 0.6rem;">Graduates ({{ $letter->items->count() }})</div>
                        @foreach($letter->items as $item)
                            <div>{{ $item->name }} <code style="font-size: 0.62rem;">{{ $item->number }}</code></div>
                        @endforeach
                    </div>
                    <div class="mb-2"><div class="text-muted" style="font-size: 0.6rem;">Signed by</div>{{ $letter->signatory->name }}<div class="text-muted" style="font-size: 0.62rem;">{{ $letter->signatory->title_en }}</div></div>
                    <div class="mb-2"><div class="text-muted" style="font-size: 0.6rem;">Generated</div>{{ $letter->created_at->format('d M Y, H:i') }} by {{ $letter->generatedBy->full_name ?? '—' }}</div>
                    <div class="mb-2"><div class="text-muted" style="font-size: 0.6rem;">Verified by scan</div>
                        @if($letter->verified_at)<i class="ti ti-check text-success"></i> {{ $letter->verified_at->format('d M Y, H:i') }}@else<i class="ti ti-x text-muted"></i> Not yet scanned @endif
                    </div>
                    <div><div class="text-muted" style="font-size: 0.6rem;">Verification code</div><span class="font-monospace" style="font-size: 0.65rem;">{{ $letter->secure_code }}</span></div>
                </div>
            </div>

            <x-alert-light class="mb-0 py-2 px-2 rounded-sm">
                <small class="text-muted" style="font-size: 0.62rem;"><i class="ti ti-shield-check text-success me-1"></i>Print it or download the PDF and take the hard copy to {{ $letter->signatory->name }} to sign. The QR code and printed address let the receiver confirm the letter is genuine.</small>
            </x-alert-light>
        </div>
    </div>
</div>
@endsection
