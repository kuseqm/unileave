@extends('layouts.master')

@section('title', 'Server Error')

@section('content')
<div class="container-fluid px-2">
    <div class="row justify-content-center mt-5">
        <div class="col-md-6 text-center">
            <div class="card rounded-sm">
                <div class="card-body py-5">
                    <i class="ti ti-server-2 text-warning" style="font-size: 4rem;"></i>
                    <h1 class="display-1 fw-bold text-muted">500</h1>
                    <h3 class="mb-3">Server Error</h3>
                    <p class="text-muted mb-2">Something went wrong on our side. We are looking into it.</p>
                    <p class="text-muted mb-4" style="font-size: 0.75rem;">Tatizo limetokea. Tafadhali jaribu tena baadaye au wasiliana na msimamizi.</p>
                    <a href="{{ url('/dashboard') }}" class="btn btn-primary btn-sm rounded-sm me-2">
                        <i class="ti ti-home me-1"></i> Back to Dashboard
                    </a>
                    <a href="{{ url()->previous() }}" class="btn btn-outline-secondary btn-sm rounded-sm">
                        <i class="ti ti-arrow-left me-1"></i> Go Back
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
