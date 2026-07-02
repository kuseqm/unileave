@extends('layouts.master')

@section('title', 'Edit Staff')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="ti ti-user-edit me-2"></i> Edit Staff: {{ $staff->first_name }} {{ $staff->last_name }}
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.staff.update', $staff->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">First Name *</label>
                                <input type="text" name="first_name" class="form-control @error('first_name') is-invalid @enderror" value="{{ old('first_name', $staff->first_name) }}" required>
                                @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Last Name *</label>
                                <input type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror" value="{{ old('last_name', $staff->last_name) }}" required>
                                @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Email *</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $staff->email) }}" required>
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Department *</label>
                            <select name="department_id" class="form-select @error('department_id') is-invalid @enderror" required>
                                @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" {{ $staff->department_id == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                                @endforeach
                            </select>
                            @error('department_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Role *</label>
                                <select name="role" class="form-select @error('role') is-invalid @enderror" required>
                                    <option value="staff" {{ $staffRole == 'staff' ? 'selected' : '' }}>Staff</option>
                                    <option value="hod" {{ $staffRole == 'hod' ? 'selected' : '' }}>Head of Department</option>
                                    <option value="hr-officer" {{ $staffRole == 'hr-officer' ? 'selected' : '' }}>HR Officer</option>
                                    <option value="admin" {{ $staffRole == 'admin' ? 'selected' : '' }}>Admin</option>
                                </select>
                                @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Status *</label>
                                <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                    <option value="active" {{ $staff->status == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ $staff->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    <option value="suspended" {{ $staff->status == 'suspended' ? 'selected' : '' }}>Suspended</option>
                                </select>
                                @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.staff') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Update Staff</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection