@extends('layouts.master')

@section('title', 'New Incoming Letter')

@section('content')
<div class="container-fluid px-2">
    
    <!-- Breadcrumb -->
    <x-breadcrumb :items="[
        ['name' => 'Central Registry', 'url' => '#'],
        ['name' => 'Incoming Letters', 'url' => route('registry.incoming.index')],
        ['name' => 'New Letter', 'url' => '#']
    ]" />
    
    <!-- Page Header -->
    <x-page-header title="NEW INCOMING LETTER" description="Register a new incoming letter">
        <a href="{{ route('registry.incoming.index') }}" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
            <i class="ti ti-arrow-left me-1"></i> Cancel
        </a>
    </x-page-header>
    
    <!-- Form Card -->
    <div class="card rounded-sm">
        <div class="card-header py-1 px-2 bg-light">
            <div class="d-flex align-items-center">
                <i class="ti ti-file-plus text-primary me-1" style="font-size: 0.8rem;"></i>
                <span style="font-size: 0.7rem; font-weight: 600;">Letter Information</span>
            </div>
        </div>
        <div class="card-body py-2 px-2">
            <form method="POST" action="{{ route('registry.incoming.store') }}" enctype="multipart/form-data">
                @csrf
                
                <div class="row g-1">
                    <div class="col-md-6 mb-2">
                        <label class="form-label text-muted" style="font-size: 0.6rem;">Sender <span class="text-danger">*</span></label>
                        <input type="text" name="sender" class="form-control form-control-sm @error('sender') is-invalid @enderror" value="{{ old('sender') }}" style="font-size: 0.7rem;" required>
                        @error('sender') <div class="invalid-feedback" style="font-size: 0.6rem;">{{ $message }}</div> @enderror
                    </div>
                    
                    <div class="col-md-6 mb-2">
                        <label class="form-label text-muted" style="font-size: 0.6rem;">Date Received <span class="text-danger">*</span></label>
                        <input type="date" name="date_received" class="form-control form-control-sm @error('date_received') is-invalid @enderror" value="{{ old('date_received', date('Y-m-d')) }}" style="font-size: 0.7rem;" required>
                        @error('date_received') <div class="invalid-feedback" style="font-size: 0.6rem;">{{ $message }}</div> @enderror
                    </div>
                </div>
                
                <div class="row g-1">
                    <div class="col-md-6 mb-2">
                        <label class="form-label text-muted" style="font-size: 0.6rem;">Addressee Office <span class="text-danger">*</span></label>
                        <select name="office_id" class="form-select form-select-sm @error('office_id') is-invalid @enderror" style="font-size: 0.7rem;" required>
                            <option value="">-- Select Office --</option>
                            @foreach($offices as $office)
                            <option value="{{ $office->id }}" {{ old('office_id') == $office->id ? 'selected' : '' }}>{{ $office->name }}</option>
                            @endforeach
                        </select>
                        @error('office_id') <div class="invalid-feedback" style="font-size: 0.6rem;">{{ $message }}</div> @enderror
                    </div>
                    
                    <div class="col-md-6 mb-2">
                        <label class="form-label text-muted" style="font-size: 0.6rem;">Priority</label>
                        <select name="priority" class="form-select form-select-sm" style="font-size: 0.7rem;">
                            <option value="normal" {{ old('priority') == 'normal' ? 'selected' : '' }}>Normal</option>
                            <option value="urgent" {{ old('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                            <option value="confidential" {{ old('priority') == 'confidential' ? 'selected' : '' }}>Confidential</option>
                        </select>
                    </div>
                </div>
                
                <div class="mb-2">
                    <label class="form-label text-muted" style="font-size: 0.6rem;">Subject <span class="text-danger">*</span></label>
                    <input type="text" name="subject" class="form-control form-control-sm @error('subject') is-invalid @enderror" value="{{ old('subject') }}" style="font-size: 0.7rem;" required>
                    @error('subject') <div class="invalid-feedback" style="font-size: 0.6rem;">{{ $message }}</div> @enderror
                </div>
                
                <div class="mb-2">
                    <label class="form-label text-muted" style="font-size: 0.6rem;">PDF Document</label>
                    <input type="file" name="pdf_file" class="form-control form-control-sm @error('pdf_file') is-invalid @enderror" accept=".pdf" style="font-size: 0.7rem;">
                    <small class="text-muted" style="font-size: 0.55rem;">Upload scanned letter (PDF only, max 10MB)</small>
                    @error('pdf_file') <div class="invalid-feedback" style="font-size: 0.6rem;">{{ $message }}</div> @enderror
                </div>
                
                <div class="mb-2">
                    <label class="form-label text-muted" style="font-size: 0.6rem;">Remarks (Optional)</label>
                    <textarea name="remarks" class="form-control form-control-sm" rows="3" style="font-size: 0.7rem;">{{ old('remarks') }}</textarea>
                </div>
                
                <hr class="my-2" style="border-color: #E8E2D5;">
                
                <div class="d-flex justify-content-end gap-1">
                    <a href="{{ route('registry.incoming.index') }}" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.25rem 0.75rem;">
                        <i class="ti ti-x me-1"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.25rem 0.75rem;">
                        <i class="ti ti-device-floppy me-1"></i> Save Letter
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection