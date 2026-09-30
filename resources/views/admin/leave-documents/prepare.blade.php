@extends('layouts.master')

@section('title', 'Prepare Leave Document')

@section('content')
<div class="container-fluid px-2">
    <x-breadcrumb :items="[
        ['name' => 'Prepare Leave Documents', 'url' => route('admin.leave-documents.index')],
        ['name' => 'Prepare', 'url' => route('admin.leave-documents.prepare', $leaveRequest->id)]
    ]" />

    <x-page-header title="PREPARE LEAVE DOCUMENT" description="{{ optional($leaveRequest->staff)->full_name }} - {{ optional($leaveRequest->leaveType)->name }}">
        <a href="{{ route('admin.leave-documents.index') }}" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">Back</a>
    </x-page-header>

    <x-alert-success />
    <x-alert-danger />

    <div class="row g-2">
        <div class="col-lg-8">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <span style="font-size: 0.7rem; font-weight: 600;">Letter Content</span>
                </div>
                <div class="card-body p-2">
                    <form method="POST" action="{{ route('admin.leave-documents.store', $leaveRequest->id) }}">
                        @csrf
                        <div class="mb-2">
                            <textarea name="content" rows="16" class="form-control form-control-sm rounded-sm @error('content') is-invalid @enderror" style="font-size: 0.75rem;" required>{{ old('content', $content) }}</textarea>
                            @error('content')
                            <div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-2">
                            <label class="form-label" style="font-size: 0.65rem;">Stage</label>
                            <select name="stage" class="form-select form-select-sm rounded-sm" style="font-size: 0.7rem;">
                                <option value="draft" {{ old('stage', $letter->stage ?? 'draft') == 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="generated" {{ old('stage', $letter->stage ?? 'draft') == 'generated' ? 'selected' : '' }}>Generated</option>
                            </select>
                        </div>
                        <div class="d-flex justify-content-end gap-1">
                            <button type="submit" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.25rem 0.5rem;">Save Document</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <span style="font-size: 0.7rem; font-weight: 600;">Leave Details</span>
                </div>
                <div class="card-body p-2" style="font-size: 0.7rem;">
                    <p><strong>Staff:</strong> {{ optional($leaveRequest->staff)->full_name }} ({{ optional($leaveRequest->staff)->staff_id }})</p>
                    <p><strong>Leave Type:</strong> {{ optional($leaveRequest->leaveType)->name }}</p>
                    <p><strong>From:</strong> {{ $leaveRequest->start_date?->format('d M Y') }}</p>
                    <p><strong>To:</strong> {{ $leaveRequest->end_date?->format('d M Y') }}</p>
                    <p><strong>Days:</strong> {{ $leaveRequest->days }}</p>
                    @if($letter)
                    <p><strong>Letter Number:</strong> {{ $letter->letter_number }}</p>
                    <p><strong>Secure Code:</strong> {{ $letter->secure_code }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
