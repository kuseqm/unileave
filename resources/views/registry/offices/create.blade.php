@extends('layouts.app')

@section('title', 'New Office')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">New Office</h2>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <div class="row g-2">
            <!-- Form Card - main column -->
            <div class="col-lg-7">
                <div class="card">
                    <div class="card-body">
                        <form method="POST" action="{{ route('registry.offices.store') }}">
                            @csrf

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label required">Office Name</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Short Name</label>
                            <input type="text" name="short_name" class="form-control @error('short_name') is-invalid @enderror" value="{{ old('short_name') }}">
                            @error('short_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Department (leave blank for central offices)</label>
                        <select name="department_id" class="form-select">
                            <option value="">-- Central Office --</option>
                            @foreach(\App\Models\Department::orderBy('name')->get() as $dept)
                            <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Campus</label>
                            <select name="campus_id" class="form-select">
                                <option value="">Select Campus</option>
                                @foreach(\App\Models\Campus::where('status', 'active')->get() as $campus)
                                <option value="{{ $campus->id }}">{{ $campus->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Head of Office</label>
                            <select name="head_staff_id" class="form-select">
                                <option value="">Select Staff</option>
                                @foreach($staff as $s)
                                <option value="{{ $s->id }}">{{ $s->first_name ?? '' }} {{ $s->last_name ?? $s->name ?? '' }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email Alias</label>
                            <input type="email" name="email_alias" class="form-control" value="{{ old('email_alias') }}">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Sort Order</label>
                            <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', 0) }}">
                        </div>
                    </div>
                    
                    <div class="mb-3 form-check">
                        <input type="checkbox" name="receives_letters" class="form-check-input" id="receives_letters" checked>
                        <label class="form-check-label" for="receives_letters">This office receives letters</label>
                    </div>
                    
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('registry.offices.index') }}" class="btn btn-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary">Create Office</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Information - narrow column -->
            <div class="col-lg-5">
                <div class="card">
                    <div class="card-header">
                        <i class="ti ti-info-circle text-info me-1"></i>
                        <span class="fw-semibold">Information</span>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="fw-semibold mb-1"><i class="ti ti-flag me-1 text-primary"></i> About Offices</div>
                            <ul class="mb-0 ps-3 text-muted small">
                                <li>Leave Department blank for a central office that isn't tied to one unit</li>
                                <li>An office only appears on incoming letter forms if it "receives letters"</li>
                                <li>Sort order controls where the office appears in dropdown lists</li>
                            </ul>
                        </div>

                        <x-alert-light class="mb-0">
                            <strong class="d-block small">Tips</strong>
                            <small class="text-muted d-block">Set a Head of Office so assignment notifications reach the right person.</small>
                            <small class="text-muted d-block">The Email Alias is optional and used for reference only.</small>
                        </x-alert-light>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection