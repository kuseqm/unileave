@extends('layouts.master')

@section('title', 'Access Denied')

@section('content')
<div class="container-fluid px-2">
    <div class="row justify-content-center mt-5">
        <div class="col-md-6 text-center">
            <div class="card rounded-sm">
                <div class="card-body py-5">
                    <i class="ti ti-shield-lock text-danger" style="font-size: 4rem;"></i>
                    <h1 class="display-1 fw-bold text-muted">403</h1>
                    <h3 class="mb-3">Access Denied</h3>
                    <p class="text-muted mb-2">You do not have permission to view this page or resource.</p>
                    <p class="text-muted mb-4" style="font-size: 0.75rem;">Huna ruhusa ya kufikia ukurasa au rasilimali hii.</p>
                    <a href="{{ url('/dashboard') }}" class="btn btn-primary btn-sm rounded-sm me-2">
                        <i class="ti ti-home me-1"></i> Back to Dashboard
                    </a>
                    <a href="{{ url()->previous() == url()->current() ? url('/dashboard') : url()->previous() }}" class="btn btn-outline-secondary btn-sm rounded-sm">
                        <i class="ti ti-arrow-left me-1"></i> Go Back
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
