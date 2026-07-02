@extends('layouts.master')

@section('title', 'New Outgoing Letter')

@section('content')
<div class="container-fluid px-2">
    
    <!-- Breadcrumb -->
    <x-breadcrumb :items="[
        ['name' => 'Central Registry', 'url' => '#'],
        ['name' => 'Outgoing Letters', 'url' => route('registry.outgoing.index')],
        ['name' => 'New Letter', 'url' => '#']
    ]" />
    
    <!-- Page Header -->
    <x-page-header title="NEW OUTGOING LETTER" description="Draft a new outgoing letter">
        <a href="{{ route('registry.outgoing.index') }}" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
            <i class="ti ti-arrow-left me-1"></i> Cancel
        </a>
    </x-page-header>
    
    <!-- Form Card -->
    <div class="card rounded-sm">
        <div class="card-header py-1 px-2 bg-light">
            <div class="d-flex align-items-center">
                <i class="ti ti-mail-forward text-primary me-1" style="font-size: 0.8rem;"></i>
                <span style="font-size: 0.7rem; font-weight: 600;">Letter Details</span>
            </div>
        </div>
        <div class="card-body py-2 px-2">
            @if(isset($incomingLetter))
                <div class="alert alert-info py-1 px-2 mb-2 rounded-sm" style="font-size: 0.65rem;">
                    <i class="ti ti-info-circle me-1"></i> 
                    Responding to: <strong>{{ $incomingLetter->ref_number }}</strong> - {{ $incomingLetter->sender }}
                </div>
            @endif
            
            <form method="POST" action="{{ route('registry.outgoing.store') }}">
                @csrf
                @if(isset($incomingLetter))
                    <input type="hidden" name="incoming_letter_id" value="{{ $incomingLetter->id }}">
                @endif
                
                <div class="mb-2">
                    <label class="form-label text-muted" style="font-size: 0.6rem;">Recipient <span class="text-danger">*</span></label>
                    <input type="text" name="recipient" class="form-control form-control-sm @error('recipient') is-invalid @enderror" value="{{ old('recipient') }}" style="font-size: 0.7rem;" required>
                    @error('recipient') <div class="invalid-feedback" style="font-size: 0.6rem;">{{ $message }}</div> @enderror
                </div>
                
                <div class="mb-2">
                    <label class="form-label text-muted" style="font-size: 0.6rem;">Subject <span class="text-danger">*</span></label>
                    <input type="text" name="subject" class="form-control form-control-sm @error('subject') is-invalid @enderror" value="{{ old('subject', isset($incomingLetter) ? 'RE: ' . $incomingLetter->subject : '') }}" style="font-size: 0.7rem;" required>
                    @error('subject') <div class="invalid-feedback" style="font-size: 0.6rem;">{{ $message }}</div> @enderror
                </div>
                
                <div class="mb-2">
                    <label class="form-label text-muted" style="font-size: 0.6rem;">Content <span class="text-danger">*</span></label>
                    <textarea name="content" class="form-control form-control-sm @error('content') is-invalid @enderror" rows="8" style="font-size: 0.7rem; font-family: monospace;" required>{{ old('content') }}</textarea>
                    <small class="text-muted" style="font-size: 0.55rem;">You can use plain text or HTML formatting</small>
                    @error('content') <div class="invalid-feedback" style="font-size: 0.6rem;">{{ $message }}</div> @enderror
                </div>
                
                <div class="form-check mb-2">
                    <input type="checkbox" name="requires_print" class="form-check-input" id="requires_print" {{ old('requires_print') ? 'checked' : '' }}>
                    <label class="form-check-label text-muted" for="requires_print" style="font-size: 0.65rem;">
                        <i class="ti ti-printer me-1"></i> Requires physical printing (for recipients without email)
                    </label>
                </div>
                
                <hr class="my-2" style="border-color: #E8E2D5;">
                
                <div class="d-flex justify-content-end gap-1">
                    <a href="{{ route('registry.outgoing.index') }}" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.25rem 0.75rem;">
                        <i class="ti ti-x me-1"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.25rem 0.75rem;">
                        <i class="ti ti-device-floppy me-1"></i> Save as Draft
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection