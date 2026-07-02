@extends('layouts.master')

@section('title', 'Department Details')

@section('content')
<div class="container-fluid px-2">
    
    <!-- Breadcrumb -->
    <x-breadcrumb :items="[
        ['name' => 'Admin', 'url' => route('admin.departments.index')],
        ['name' => 'Departments', 'url' => route('admin.departments.index')],
        ['name' => $department->name, 'url' => '#' ]
    ]" />
    
    <!-- Page Header -->
    <x-page-header :title="$department->name" description="Department details and information">
        <div class="d-flex gap-2">
            <a href="{{ route('admin.departments.index') }}" class="btn btn-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
                <i class="ti ti-arrow-left me-1"></i> Back
            </a>
            @can('update', $department)
            <a href="{{ route('admin.departments.edit', $department->id) }}" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
                <i class="ti ti-edit me-1"></i> Edit Department
            </a>
            @endcan
        </div>
    </x-page-header>
    
    <div class="row">
        <!-- Main Info Card -->
        <div class="col-md-8">
            <div class="card rounded-sm mb-3">
                <div class="card-header py-1 px-2 bg-light">
                    <i class="ti ti-info-circle me-1"></i>
                    <span style="font-size: 0.7rem; font-weight: 600;">Department Information</span>
                </div>
                <div class="card-body p-2">
                    <table class="table table-sm" style="font-size: 0.75rem;">
                        <tr>
                            <th width="180">Department Name:</th>
                            <td>{{ $department->name }}</td>
                        </tr>
                        <tr>
                            <th>Department Code:</th>
                            <td><code>{{ $department->code ?? 'N/A' }}</code></td>
                        </tr>
                        <tr>
                            <th>School/Faculty:</th>
                            <td>{{ $department->school->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Campus:</th>
                            <td>{{ $department->school->campus->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>University:</th>
                            <td>{{ $department->school->campus->university->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Status:</th>
                            <td>
                                @if($department->status == 'active')
                                    <span class="badge bg-success-subtle text-success rounded-sm">Active</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger rounded-sm">Inactive</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Description:</th>
                            <td>{{ $department->description ?? 'No description provided.' }}</td>
                        </tr>
                        <tr>
                            <th>Created At:</th>
                            <td>{{ $department->created_at ? $department->created_at->format('F d, Y H:i A') : 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Last Updated:</th>
                            <td>{{ $department->updated_at ? $department->updated_at->format('F d, Y H:i A') : 'N/A' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Leadership Card -->
        <div class="col-md-4">
            <div class="card rounded-sm mb-3">
                <div class="card-header py-1 px-2 bg-light">
                    <i class="ti ti-users me-1"></i>
                    <span style="font-size: 0.7rem; font-weight: 600;">Department Leadership</span>
                </div>
                <div class="card-body p-2">
                    <table class="table table-sm" style="font-size: 0.75rem;">
                        <tr>
                            <th width="120">Head of Department:</th>
                            <td>
                                @if($department->hod)
                                    <strong>{{ $department->hod->full_name ?? $department->hod->name ?? 'N/A' }}</strong><br>
                                    <small class="text-muted">{{ $department->hod->email ?? '' }}</small>
                                @else
                                    <span class="text-muted">Not assigned</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Unit Head:</th>
                            <td>
                                @if($department->unitHead)
                                    <strong>{{ $department->unitHead->full_name ?? $department->unitHead->name ?? 'N/A' }}</strong><br>
                                    <small class="text-muted">{{ $department->unitHead->email ?? '' }}</small>
                                @else
                                    <span class="text-muted">Not assigned</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Staff Assigned Card -->
    <div class="card rounded-sm">
        <div class="card-header py-1 px-2 bg-light">
            <i class="ti ti-staff me-1"></i>
            <span style="font-size: 0.7rem; font-weight: 600;">Staff Assigned to this Department</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm mb-0" style="font-size: 0.75rem;">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-2 py-1">#</th>
                            <th class="py-1">Staff Name</th>
                            <th class="py-1">Email</th>
                            <th class="py-1">Staff Category</th>
                            <th class="py-1">Status</th>
                            <th class="pe-2 py-1">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($department->staff ?? [] as $index => $staff)
                        <tr>
                            <td class="ps-2 py-1">{{ $index + 1 }}</td>
                            <td class="py-1">{{ $staff->full_name ?? $staff->name ?? 'N/A' }}</td>
                            <td class="py-1">{{ $staff->email ?? 'N/A' }}</td>
                            <td class="py-1">{{ ucfirst($staff->staff_category ?? 'N/A') }}</td>
                            <td class="py-1">
                                @if(($staff->status ?? '') == 'active')
                                    <span class="badge bg-success-subtle text-success rounded-sm">Active</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger rounded-sm">Inactive</span>
                                @endif
                            </td>
                            <td class="pe-2 py-1">
                                <a href="{{ route('staff.show', $staff->id) }}" class="btn btn-sm btn-outline-info rounded-sm" style="font-size: 0.6rem; padding: 0.15rem 0.4rem;">
                                    <i class="ti ti-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-3 text-muted">No staff members assigned to this department</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection