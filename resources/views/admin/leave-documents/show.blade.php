@extends('layouts.master')

@section('title', 'Leave Document')

@section('content')
<div class="container-fluid px-2">
    <x-breadcrumb :items="[
        ['name' => 'Prepare Leave Documents', 'url' => route('admin.leave-documents.index')],
        ['name' => 'Document', 'url' => route('admin.leave-documents.show', $letter->id)]
    ]" />

    <x-page-header title="LEAVE DOCUMENT" description="{{ $letter->letter_number ?? 'Draft' }}">
        <a href="{{ route('admin.leave-documents.index') }}" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">Back</a>
        @if($letter->leaveRequest)
        <a href="{{ route('admin.leave-documents.prepare', $letter->leaveRequest->id) }}" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">Edit</a>
        @endif
    </x-page-header>

    <x-alert-success />
    <x-alert-danger />

    <div class="card rounded-sm">
        <div class="card-header py-1 px-2 bg-light d-flex justify-content-between align-items-center">
            <span style="font-size: 0.7rem; font-weight: 600;">Preview</span>
            <span class="badge bg-{{ $letter->stage == 'generated' ? 'success' : 'info' }}" style="font-size: 0.65rem;">{{ ucfirst($letter->stage) }}</span>
        </div>
        <div class="card-body p-3" style="font-size: 0.8rem;">
            {!! nl2br(e($letter->content)) !!}
        </div>
    </div>

    <div class="mt-2" style="font-size: 0.7rem;">
        <p><strong>Staff:</strong> {{ optional($letter->staff)->full_name }} ({{ optional($letter->staff)->staff_id }})</p>
        <p><strong>Prepared by:</strong> {{ $letter->generated_by }} ({{ $letter->generated_by_role }})</p>
        <p><strong>Secure code:</strong> {{ $letter->secure_code }}</p>
        @if($letter->verification_url)
        <p><strong>Verification URL:</strong> <a href="{{ $letter->verification_url }}" target="_blank">{{ $letter->verification_url }}</a></p>
        @endif
    </div>
</div>
@endsection
