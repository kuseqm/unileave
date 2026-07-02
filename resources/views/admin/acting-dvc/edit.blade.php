@extends('layouts.master')

@section('title', 'Edit Acting DVC Assignment')

@section('content')
<div class="container-fluid px-2">
    
    <x-breadcrumb :items="[
        ['name' => 'Admin', 'url' => '#'],
        ['name' => 'Acting DVC', 'url' => route('admin.acting-dvc.index')],
        ['name' => 'Edit', 'url' => '#']
    ]" />
    
    <x-page-header title="EDIT ACTING DVC ASSIGNMENT" description="Modify assignment details">
        <a href="{{ route('admin.acting-dvc.index') }}" class="btn btn-secondary btn-sm rounded-sm">
            <i class="ti ti-arrow-left me-1"></i> Back
        </a>
    </x-page-header>
    
    <div class="card rounded-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.acting-dvc.update', $assignment->id) }}">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">Staff Member</label>
                        <select name="staff_id" class="form-select @error('staff_id') is-invalid @enderror" required>
                            <option value="">Select Staff...</option>
                            @foreach($staff as $s)
                            <option value="{{ $s->id }}" {{ old('staff_id', $assignment->staff_id) == $s->id ? 'selected' : '' }}>
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
                            <option value="dvca" {{ old('role', $assignment->role) == 'dvca' ? 'selected' : '' }}>DVCA (Academic)</option>
                            <option value="dvc-pfa" {{ old('role', $assignment->role) == 'dvc-pfa' ? 'selected' : '' }}>DVC-PFA (Planning, Finance & Admin)</option>
                        </select>
                        @error('role')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">Start Date</label>
                        <input type="date" name="start_date" class="form-control @error('start_date') is-invalid @enderror" value="{{ old('start_date', $assignment->start_date->format('Y-m-d')) }}" required>
                        @error('start_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">End Date</label>
                        <input type="date" name="end_date" class="form-control @error('end_date') is-invalid @enderror" value="{{ old('end_date', $assignment->end_date->format('Y-m-d')) }}" required>
                        @error('end_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select @error('status') is-invalid @enderror">
                            <option value="pending" {{ old('status', $assignment->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="active" {{ old('status', $assignment->status) == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="expired" {{ old('status', $assignment->status) == 'expired' ? 'selected' : '' }}>Expired</option>
                            <option value="rejected" {{ old('status', $assignment->status) == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                        @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-12 mb-3">
                        <label class="form-label">Reason</label>
                        <textarea name="reason" class="form-control @error('reason') is-invalid @enderror" rows="3">{{ old('reason', $assignment->reason) }}</textarea>
                        @error('reason')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">Update Assignment</button>
                    <a href="{{ route('admin.acting-dvc.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection