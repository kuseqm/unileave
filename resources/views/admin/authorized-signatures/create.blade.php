@extends('layouts.master')

@section('title', 'Add Authorized Signature')

@section('content')
<div class="container-fluid px-2">

    <!-- Breadcrumb -->
    <x-breadcrumb :items="[
        ['name' => 'Authorized Signatures', 'url' => route('admin.authorized-signatures.index')],
        ['name' => 'Add Signature', 'url' => '#']
    ]" />

    <!-- Page Header -->
    <x-page-header title="ADD AUTHORIZED SIGNATURE" description="Upload a signature image that can be used to authorize ID cards">
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
                    <form action="{{ route('admin.authorized-signatures.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-1">
                            <label class="form-label" style="font-size: 0.65rem;">Full Name <span class="text-danger">*</span></label>
                            <input type="text"
                                   name="name"
                                   class="form-control form-control-sm rounded-sm @error('name') is-invalid @enderror"
                                   value="{{ old('name') }}"
                                   placeholder="e.g., Prof. John Doe, Vice Chancellor"
                                   style="font-size: 0.7rem; padding: 0.2rem 0.3rem;"
                                   required>
                            @error('name')
                                <div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-1">
                            <label class="form-label" style="font-size: 0.65rem;">Signature Image <span class="text-danger">*</span></label>
                            <input type="file"
                                   name="signature"
                                   accept="image/png,image/jpeg"
                                   class="form-control form-control-sm rounded-sm @error('signature') is-invalid @enderror"
                                   style="font-size: 0.7rem; padding: 0.2rem 0.3rem;"
                                   required>
                            @error('signature')
                                <div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>
                            @enderror
                            <small class="text-muted" style="font-size: 0.6rem;">PNG or JPG, max 2MB. A transparent background works best.</small>
                        </div>

                        <div class="d-flex justify-content-end gap-1 mt-2">
                            <a href="{{ route('admin.authorized-signatures.index') }}" class="btn btn-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.25rem 0.5rem;">Cancel</a>
                            <button type="submit" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.25rem 0.5rem;">
                                <i class="ti ti-device-floppy me-1"></i> Save Signature
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
                        <span style="font-size: 0.7rem; font-weight: 600;">About Authorized Signatures</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <x-alert-light class="mb-0 py-1 px-2 rounded-sm">

                        <div class="d-flex align-items-start gap-1">
                            <i class="ti ti-tips text-warning mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">Important Notes</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">• This signature will be selectable when generating ID cards<br>
                                • It cannot be deleted once assigned to an ID card<br>
                                • Use a clear, high-contrast scan for best print quality</small>
                            </div>
                        
</x-alert-light>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
