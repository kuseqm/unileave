@extends('layouts.master')

@section('title', 'Page Not Found')

@section('content')
<div class="container-fluid px-2">
    <div class="row justify-content-center mt-5">
        <div class="col-md-6 text-center">
            <div class="card rounded-sm">
                <div class="card-body py-5">
                    <i class="ti ti-error-404 text-primary" style="font-size: 4rem;"></i>
                    <h1 class="display-1 fw-bold text-muted">404</h1>
                    <h3 class="mb-3">Page Not Found</h3>
                    <p class="text-muted mb-4">The page you are looking for does not exist or has been moved.</p>
                    <a href="{{ url('/dashboard') }}" class="btn btn-primary btn-sm rounded-sm">
                        <i class="ti ti-home me-1"></i> Back to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection