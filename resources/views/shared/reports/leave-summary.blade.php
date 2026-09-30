@extends('layouts.master')

@section('title', 'Leave Summary Report')

@section('content')
<div class="container-fluid px-2">
    
    <x-breadcrumb :items="[
        ['name' => 'Reports', 'url' => route('reports.index')],
        ['name' => 'Leave Summary', 'url' => '#']
    ]" />
    
    <x-page-header title="LEAVE SUMMARY REPORT" description="Leave requests summary based on your role">
        <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary btn-sm rounded-sm">
            <i class="ti ti-arrow-left me-1"></i> Back
        </a>
    </x-page-header>
    
    <div class="row g-2">
        <!-- Leave Summary Table - main column -->
        <div class="col-lg-8">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-file-text text-primary me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">
                            @if($role == 'staff')
                                My Leave Summary
                            @elseif($role == 'hod')
                                Department Leave Summary
                            @elseif($role == 'dean')
                                School Leave Summary
                            @elseif($role == 'dvca')
                                Academic Staff Leave Summary
                            @elseif($role == 'dvc-pfa')
                                Administrative Staff Leave Summary
                            @else
                                Department Leave Summary
                            @endif
                        </span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0" style="font-size: 0.75rem;">
                            <thead class="bg-light">
                                <tr>
                                    @if($role == 'staff')
                                        <th class="ps-2 py-1">Metric</th>
                                        <th class="pe-2 py-1">Value</th>
                                    @elseif($role == 'dvca' || $role == 'dvc-pfa')
                                        <th class="ps-2 py-1">Metric</th>
                                        <th class="pe-2 py-1">Value</th>
                                    @else
                                        <th class="ps-2 py-1">Department/School</th>
                                        <th class="py-1">Total Requests</th>
                                        <th class="py-1">Pending</th>
                                        <th class="pe-2 py-1">Approved</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @if($role == 'staff')
                                    <tr>
                                        <td class="ps-2 py-1">Total Requests</td>
                                        <td class="pe-2 py-1">{{ $data['total_requests'] ?? 0 }}</td>
                                    </tr>
                                    <tr>
                                        <td class="ps-2 py-1">Pending</td>
                                        <td class="pe-2 py-1 text-warning">{{ $data['pending'] ?? 0 }}</td>
                                    </tr>
                                    <tr>
                                        <td class="ps-2 py-1">Approved</td>
                                        <td class="pe-2 py-1 text-success">{{ $data['approved'] ?? 0 }}</td>
                                    </tr>
                                    <tr>
                                        <td class="ps-2 py-1">Rejected</td>
                                        <td class="pe-2 py-1 text-danger">{{ $data['rejected'] ?? 0 }}</td>
                                    </tr>

                                @elseif($role == 'dvca' || $role == 'dvc-pfa')
                                    <tr>
                                        <td class="ps-2 py-1">Total Staff</td>
                                        <td class="pe-2 py-1">{{ $data['total_staff'] ?? 0 }}</td>
                                    </tr>
                                    <tr>
                                        <td class="ps-2 py-1">Total Leave Requests</td>
                                        <td class="pe-2 py-1">{{ $data['total_requests'] ?? 0 }}</td>
                                    </tr>
                                    <tr>
                                        <td class="ps-2 py-1">Pending Approvals</td>
                                        <td class="pe-2 py-1 text-warning">{{ $data['pending'] ?? 0 }}</td>
                                    </tr>
                                    <tr>
                                        <td class="ps-2 py-1">Approved</td>
                                        <td class="pe-2 py-1 text-success">{{ $data['approved'] ?? 0 }}</td>
                                    </tr>

                                @else
                                    @forelse($data as $item)
                                    <tr>
                                        <td class="ps-2 py-1">{{ $item['name'] }}</td>
                                        <td class="py-1">{{ $item['total_requests'] }}</td>
                                        <td class="py-1">{{ $item['pending'] }}</td>
                                        <td class="pe-2 py-1">{{ $item['approved'] }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-3 text-muted">No data available</td>
                                    </tr>
                                    @endforelse
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Information - narrow column -->
        <div class="col-lg-4">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-info-circle text-info me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Information</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <div class="mb-2">
                        <div class="fw-semibold" style="font-size: 0.7rem;"><i class="ti ti-flag me-1 text-primary"></i> How to Read This Report</div>
                        <ul class="mb-0 ps-3" style="font-size: 0.65rem; color: #555;">
                            <li>Content shown adapts to your role - personal, departmental, or institution-wide</li>
                            <li>"Pending" counts requests still moving through approval</li>
                            <li>"Approved" counts requests fully granted</li>
                        </ul>
                    </div>

                    <x-alert-light class="mb-0 py-1 px-2 rounded-sm">
                        <div class="d-flex align-items-start gap-1">
                            <i class="ti ti-tips text-warning mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">Tips</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">&bull; Use this to spot units with high pending counts</small>
                            </div>
                        </div>
                    </x-alert-light>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection