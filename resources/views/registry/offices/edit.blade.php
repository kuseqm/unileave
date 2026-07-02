@extends('layouts.app')

@section('title', 'Edit Office')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">Edit Office: {{ $office->name }}</h2>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('registry.offices.update', $office->id) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label required">Office Name</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $office->name) }}" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Short Name</label>
                            <input type="text" name="short_name" class="form-control" value="{{ old('short_name', $office->short_name) }}">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Department</label>
                        <select name="department_id" class="form-select">
                            <option value="">-- Central Office --</option>
                            @foreach(\App\Models\Department::orderBy('name')->get() as $dept)
                            <option value="{{ $dept->id }}" {{ old('department_id', $office->department_id) == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Campus</label>
                            <select name="campus_id" class="form-select">
                                <option value="">Select Campus</option>
                                @foreach(\App\Models\Campus::where('status', 'active')->get() as $campus)
                                <option value="{{ $campus->id }}" {{ old('campus_id', $office->campus_id) == $campus->id ? 'selected' : '' }}>{{ $campus->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Head of Office</label>
                            <select name="head_staff_id" class="form-select">
                                <option value="">Select Staff</option>
                                @foreach($staff as $s)
                                <option value="{{ $s->id }}" {{ old('head_staff_id', $office->head_staff_id) == $s->id ? 'selected' : '' }}>
                                    {{ $s->first_name ?? '' }} {{ $s->last_name ?? $s->name ?? '' }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Email Alias</label>
                        <input type="email" name="email_alias" class="form-control" value="{{ old('email_alias', $office->email_alias) }}">
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Sort Order</label>
                            <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $office->sort_order) }}">
                        </div>
                        
                        <div class="col-md-4 mb-3 form-check mt-4">
                            <input type="checkbox" name="receives_letters" class="form-check-input" id="receives_letters" {{ $office->receives_letters ? 'checked' : '' }}>
                            <label class="form-check-label" for="receives_letters">Receives Letters</label>
                        </div>
                        
                        <div class="col-md-4 mb-3 form-check mt-4">
                            <input type="checkbox" name="is_active" class="form-check-input" id="is_active" {{ $office->is_active ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Active</label>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('registry.offices.index') }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Update Office</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection