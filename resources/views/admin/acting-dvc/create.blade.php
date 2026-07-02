@extends('layouts.master')

@section('title', 'Create Acting DVC Assignment')

@section('content')
<div class="container-fluid px-2">
    
    <x-breadcrumb :items="[
        ['name' => 'Admin', 'url' => '#'],
        ['name' => 'Acting DVC', 'url' => route('admin.acting-dvc.index')],
        ['name' => 'Create', 'url' => '#']
    ]" />
    
    <x-page-header title="CREATE ACTING DVC ASSIGNMENT" description="Assign a staff member to act as DVC">
        <a href="{{ route('admin.acting-dvc.index') }}" class="btn btn-secondary btn-sm rounded-sm">
            <i class="ti ti-arrow-left me-1"></i> Back
        </a>
    </x-page-header>
    
    <div class="card rounded-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.acting-dvc.store') }}">
                @csrf
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">Staff Member</label>
                        <select name="staff_id" class="form-select @error('staff_id') is-invalid @enderror" required>
                            <option value="">Select Staff...</option>
                            @foreach($staff as $s)
                            <option value="{{ $s->id }}" {{ old('staff_id') == $s->id ? 'selected' : '' }}>
                                {{ $s->first_name }} {{ $s->last_name }} ({{ $s->staff_id ?? 'N/A' }})
                            </option>
                            @endforeach
                        </select>
                        @error('staff_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">Role</label>
                        <select name="role" class="form-select @error('role') is-invalid @enderror" required>
                            <option value="">Select Role...</option>
                            <option value="dvca" {{ old('role') == 'dvca' ? 'selected' : '' }}>DVCA (Academic)</option>
                            <option value="dvc-pfa" {{ old('role') == 'dvc-pfa' ? 'selected' : '' }}>DVC-PFA (Planning, Finance & Admin)</option>
                        </select>
                        @error('role')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">Start Date</label>
                        <input type="date" name="start_date" class="form-control @error('start_date') is-invalid @enderror" value="{{ old('start_date') }}" required>
                        @error('start_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">End Date</label>
                        <input type="date" name="end_date" class="form-control @error('end_date') is-invalid @enderror" value="{{ old('end_date') }}" required>
                        @error('end_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-12 mb-3">
                        <label class="form-label">Reason</label>
                        <textarea name="reason" class="form-control @error('reason') is-invalid @enderror" rows="3">{{ old('reason') }}</textarea>
                        <small class="text-muted">Optional - reason for acting assignment</small>
                        @error('reason')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">Create Assignment</button>
                    <a href="{{ route('admin.acting-dvc.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection