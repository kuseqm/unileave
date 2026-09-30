@extends('layouts.master')

@section('title', 'Edit Authorized Signature')

@section('content')
<div class="container-fluid px-2">

    <!-- Breadcrumb -->
    <x-breadcrumb :items="[
        ['name' => 'Authorized Signatures', 'url' => route('admin.authorized-signatures.index')],
        ['name' => 'Edit Signature', 'url' => '#']
    ]" />

    <!-- Page Header -->
    <x-page-header title="EDIT AUTHORIZED SIGNATURE" description="Update the name or signature image">
        <a href="{{ route('admin.authorized-signatures.index') }}" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
            <i class="ti ti-arrow-left me-1"></i> Back
        </a>
    </x-page-header>

    <div class="row g-2">
        <div class="col-lg-7">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-signature text-primary me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Signature Information</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <form action="{{ route('admin.authorized-signatures.update', $signature->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="mb-1">
                            <label class="form-label" style="font-size: 0.65rem;">Full Name <span class="text-danger">*</span></label>
                            <input type="text"
                                   name="name"
                                   class="form-control form-control-sm rounded-sm @error('name') is-invalid @enderror"
                                   value="{{ old('name', $signature->name) }}"
                                   style="font-size: 0.7rem; padding: 0.2rem 0.3rem;"
                                   required>
                            @error('name')
                                <div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>
                            @enderror
                        </div>

                        @if($signature->signature_path)
                        <div class="mb-1">
                            <label class="form-label" style="font-size: 0.65rem;">Current Signature</label>
                            <div>
                                <img src="{{ Storage::url($signature->signature_path) }}" alt="{{ $signature->name }}" style="height: 48px; max-width: 160px; object-fit: contain;" class="border rounded-sm p-1">
                            </div>
                        </div>
                        @endif

                        <div class="mb-1">
                            <label class="form-label" style="font-size: 0.65rem;">Replace Signature Image</label>
                            <input type="file"
                                   name="signature"
                                   accept="image/png,image/jpeg"
                                   class="form-control form-control-sm rounded-sm @error('signature') is-invalid @enderror"
                                   style="font-size: 0.7rem; padding: 0.2rem 0.3rem;">
                            @error('signature')
                                <div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>
                            @enderror
                            <small class="text-muted" style="font-size: 0.6rem;">Leave empty to keep the current image.</small>
                        </div>

                        <div class="d-flex justify-content-end gap-1 mt-2">
                            <a href="{{ route('admin.authorized-signatures.index') }}" class="btn btn-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.25rem 0.5rem;">Cancel</a>
                            <button type="submit" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.25rem 0.5rem;">
                                <i class="ti ti-device-floppy me-1"></i> Update Signature
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
