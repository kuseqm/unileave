@extends('layouts.master')

@section('title', 'Leave Balances Report')

@section('content')
<div class="container-fluid px-2">
    
    <x-breadcrumb :items="[
        ['name' => 'Reports', 'url' => route('reports.index')],
        ['name' => 'Leave Balances', 'url' => '#']
    ]" />
    
    <x-page-header title="LEAVE BALANCES REPORT" description="Staff leave balances">
        <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.7rem;">
            <i class="ti ti-arrow-left me-1"></i> Back
        </a>
    </x-page-header>
    
    <div class="card rounded-sm">
        <div class="card-header py-1 px-2 bg-light">
            <div class="d-flex align-items-center">
                <i class="ti ti-calendar text-primary me-1" style="font-size: 0.8rem;"></i>
                <span style="font-size: 0.7rem; font-weight: 600;">Leave Balances</span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm mb-0" style="font-size: 0.75rem;">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-2 py-1">Staff ID</th>
                            <th class="py-1">Name</th>
                            <th class="py-1">Department</th>
                            <th class="py-1">Annual Leave</th>
                            <th class="py-1">Sick Leave</th>
                            <th class="py-1">Compassionate</th>
                            <th class="pe-2 py-1">Maternity/Paternity</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($staff ?? [] as $staffMember)
                        @php
                            $annualBalance = $staffMember->leaveBalances->where('leave_type_id', 1)->first();
                            $sickBalance = $staffMember->leaveBalances->where('leave_type_id', 2)->first();
                            $compassionateBalance = $staffMember->leaveBalances->where('leave_type_id', 3)->first();
                            $maternityBalance = $staffMember->leaveBalances->where('leave_type_id', 4)->first();
                        @endphp
                        <tr>
                            <td class="ps-2 py-1">{{ $staffMember->staff_id }}</td>
                            <td class="py-1">
                                {{ $staffMember->first_name }} 
                                @if($staffMember->middle_name){{ $staffMember->middle_name }} @endif
                                {{ $staffMember->last_name }}
                            </td>
                            <td class="py-1">{{ $staffMember->department->name ?? 'N/A' }}</td>
                            <td class="py-1">
                                <span class="fw-semibold {{ ($annualBalance->balance ?? 0) < 5 ? 'text-danger' : 'text-success' }}">
                                    {{ $annualBalance->balance ?? 0 }}
                                </span>
                            </td>
                            <td class="py-1">{{ $sickBalance->balance ?? 0 }}</td>
                            <td class="py-1">{{ $compassionateBalance->balance ?? 0 }}</td>
                            <td class="pe-2 py-1">{{ $maternityBalance->balance ?? 0 }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-3 text-muted">No staff found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection