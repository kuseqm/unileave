@extends('layouts.master')

@section('title', 'Leave Letter - ' . $letter->letter_number)

@section('content')
<div class="container-fluid px-2">
    
    <x-breadcrumb :items="[
        ['name' => 'My Leave Letters', 'url' => route('leave-letters.index')],
        ['name' => $letter->letter_number, 'url' => '#']
    ]" />
    
    <x-page-header title="LEAVE APPROVAL LETTER" description="{{ $letter->letter_number }}">
        <div>
            <a href="{{ route('leave-letters.index') }}" class="btn btn-outline-secondary btn-sm rounded-sm">
                <i class="ti ti-arrow-left me-1"></i> Back
            </a>
            <a href="{{ route('leave-letters.print', $letter->id) }}" target="_blank" class="btn btn-primary btn-sm rounded-sm">
                <i class="ti ti-printer me-1"></i> Print
            </a>
            <a href="{{ route('leave-letters.download-pdf', $letter->id) }}" class="btn btn-success btn-sm rounded-sm">
                <i class="ti ti-download me-1"></i> PDF
            </a>
        </div>
    </x-page-header>
    
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card rounded-sm" id="letterContent">
                <div class="card-body p-4">
                    <!-- Letter Header -->
                    <div class="text-center mb-4">
                        @if($university && $university->logo)
                            <img src="{{ Storage::url($university->logo) }}" alt="University Logo" style="max-height: 80px;">
                        @endif
                        <h3 class="mt-2 mb-0">{{ $university->name ?? 'University' }}</h3>
                        <p class="text-muted small">{{ $university->address ?? '' }}</p>
                        <hr>
                    </div>
                    
                    <!-- Letter Reference -->
                    <div class="mb-3">
                        <strong>Ref:</strong> {{ $letter->letter_number }}<br>
                        <strong>Date:</strong> {{ $letter->created_at->format('d M Y') }}
                    </div>
                    
                    <!-- Letter Subject -->
                    <div class="mb-3">
                        <strong>SUBJECT: {{ $letter->title }}</strong>
                    </div>
                    
                    <!-- Letter Content -->
                    <div class="mb-4">
                        {!! nl2br(e($letter->content)) !!}
                    </div>
                    
                    <!-- Leave Details -->
                    <div class="mb-4 p-2 bg-light rounded-sm">
                        <strong>Leave Details:</strong>
                        <ul class="mb-0 mt-1">
                            <li>Staff: {{ $letter->leaveRequest->staff->full_name ?? 'N/A' }}</li>
                            <li>Staff ID: {{ $letter->leaveRequest->staff->staff_id ?? 'N/A' }}</li>
                            <li>Leave Type: {{ $letter->leaveRequest->leaveType->name ?? 'N/A' }}</li>
                            <li>Period: {{ \Carbon\Carbon::parse($letter->leaveRequest->start_date)->format('d M Y') }} - {{ \Carbon\Carbon::parse($letter->leaveRequest->end_date)->format('d M Y') }}</li>
                            <li>Total Days: {{ $letter->leaveRequest->days }} days</li>
                        </ul>
                    </div>
                    
                    <!-- Signatory -->
                    <div class="text-end mt-5">
                        <p>Yours sincerely,</p>
                        <div class="mt-4">
                            <p><strong>{{ $letter->generated_by ?? 'Human Resources Department' }}</strong><br>
                            {{ $letter->generated_by_role ?? 'HR Officer' }}</p>
                        </div>
                    </div>
                    
                    <!-- Footer -->
                    <hr>
                    <div class="text-center small text-muted">
                        <i class="ti ti-lock"></i> Secure Document - Verification Code: {{ $letter->secure_code }}
                    </div>
                </div>
            </div>
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