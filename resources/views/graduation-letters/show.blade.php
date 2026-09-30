@extends('layouts.master')

@section('title', $typeLabel . ' - ' . $letter->letter_number)

@section('content')
<div class="container-fluid px-2">

    <x-breadcrumb :items="[
        ['name' => 'Graduates', 'url' => route('students.index')],
        ['name' => $letter->student->formatted_name, 'url' => route('students.show', $letter->student->id)],
        ['name' => $letter->letter_number, 'url' => '#']
    ]" />

    <x-page-header :title="strtoupper($typeLabel)" description="{{ $letter->letter_number }}">
        <div>
            <a href="{{ route('students.show', $letter->student->id) }}" class="btn btn-outline-secondary btn-sm rounded-sm">
                <i class="ti ti-arrow-left me-1"></i> Back
            </a>
            <a href="{{ route('graduation-letters.print', $letter->id) }}?embedded=1" target="_blank" class="btn btn-primary btn-sm rounded-sm">
                <i class="ti ti-printer me-1"></i> Print
            </a>
            <a href="{{ route('graduation-letters.download-pdf', $letter->id) }}" class="btn btn-success btn-sm rounded-sm">
                <i class="ti ti-download me-1"></i> PDF
            </a>
        </div>
    </x-page-header>

    <div class="row g-2">
        <div class="col-lg-8">
            <div class="card rounded-sm">
                <div class="card-body p-0">
                    <iframe src="{{ route('graduation-letters.preview', $letter->id) }}?embedded=1" title="{{ $letter->letter_number }}" style="width: 100%; height: 82vh; border: 0; border-radius: 0 0 4px 4px;"></iframe>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-info-circle text-primary me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Letter Status</span>
                    </div>
                </div>
                <div class="card-body p-2">
                    <div class="mb-2">
                        <div class="text-muted" style="font-size: 0.6rem;">Student</div>
                        <div style="font-size: 0.75rem;">{{ $letter->student->formatted_name }}</div>
                        <div class="text-muted" style="font-size: 0.65rem;">{{ $letter->student->registration_number }}</div>
                    </div>
                    <div class="mb-2">
                        <div class="text-muted" style="font-size: 0.6rem;">Issued On</div>
                        <div style="font-size: 0.75rem;">{{ $letter->created_at->format('d M Y, H:i') }}</div>
                    </div>
                    <div class="mb-2">
                        <div class="text-muted" style="font-size: 0.6rem;">Verified</div>
                        <div style="font-size: 0.75rem;">
                            @if($letter->verified_at)
                                <i class="ti ti-check text-success"></i> {{ $letter->verified_at->format('d M Y, H:i') }}
                            @else
                                <i class="ti ti-x text-muted"></i> Not yet scanned
                            @endif
                        </div>
                    </div>
                    <div>
                        <div class="text-muted" style="font-size: 0.6rem;">Verification Code</div>
                        <div class="font-monospace" style="font-size: 0.65rem;">{{ $letter->secure_code }}</div>
                    </div>
                </div>
            </div>

            <x-alert-light class="mt-2 mb-0 py-2 px-2 rounded-sm">
                <div class="d-flex align-items-start gap-1">
                    <i class="ti ti-shield-check text-success mt-0" style="font-size: 0.7rem;"></i>
                    <div>
                        <strong class="d-block" style="font-size: 0.65rem;">About This Letter</strong>
                        <small class="text-muted" style="font-size: 0.6rem;">Use Print or PDF to get a physical or shareable copy. The verification code lets anyone confirm this letter is authentic.</small>
                    </div>
                </div>
            </x-alert-light>
        </div>
    </div>
</div>

<style media="print">
    .sidebar-area, .topbar, .page-header, .btn, .breadcrumb, .sticky-banner {
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
</style>
@endsection
