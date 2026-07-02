@extends('layouts.master')

@section('title', 'My Travel Requests')

@section('content')
<div class="container-fluid px-2">
    
    <x-breadcrumb :items="[
        ['name' => 'Dashboard', 'url' => route('dashboard')],
        ['name' => 'My Travel Requests', 'url' => '#']
    ]" />
    
    <x-page-header title="MY TRAVEL REQUESTS" description="View all your travel permission requests">
        <a href="{{ route('travel-permissions.create') }}" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
            <i class="ti ti-plus me-1"></i> New Travel Request
        </a>
    </x-page-header>
    
    <div class="card rounded-sm">
        <div class="card-header py-1 px-2 bg-light">
            <div class="d-flex align-items-center">
                <i class="ti ti-plane text-primary me-1" style="font-size: 0.8rem;"></i>
                <span style="font-size: 0.7rem; font-weight: 600;">Travel Permission Requests</span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm mb-0" style="font-size: 0.75rem;">
                    <thead>
                        <tr>
                            <th class="ps-2 py-1">Destination</th>
                            <th class="py-1">Travel Period</th>
                            <th class="py-1">Days</th>
                            <th class="py-1">Reason</th>
                            <th class="py-1">Status</th>
                            <th class="py-1">Acting Status</th>
                            <th class="pe-2 py-1">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($travelRequests as $request)
                        <tr>
                            <td class="ps-2 py-1"><strong>{{ $request->destination }}</strong></td>
                            <td class="py-1">
                                {{ \Carbon\Carbon::parse($request->start_date)->format('d M Y') }} - 
                                {{ \Carbon\Carbon::parse($request->end_date)->format('d M Y') }}
                            </td>
                            <td class="py-1">{{ $request->days }}</td>
                            <td class="py-1">{{ ucfirst($request->travel_reason) }}</td>
                            <td class="py-1">
                                @if($request->status == 'pending')
                                    <span class="badge bg-warning-subtle text-warning rounded-sm" style="font-size: 0.6rem;">Pending</span>
                                @elseif($request->status == 'approved')
                                    <span class="badge bg-success-subtle text-success rounded-sm" style="font-size: 0.6rem;">Approved</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger rounded-sm" style="font-size: 0.6rem;">Rejected</span>
                                @endif
                            </td>
                            <td class="py-1">
                                @if($request->acting_status == 'pending')
                                    <span class="badge bg-warning-subtle text-warning rounded-sm" style="font-size: 0.6rem;">Waiting</span>
                                @elseif($request->acting_status == 'accepted')
                                    <span class="badge bg-success-subtle text-success rounded-sm" style="font-size: 0.6rem;">Accepted</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger rounded-sm" style="font-size: 0.6rem;">Declined</span>
                                @endif
                            </td>
                            <td class="pe-2 py-1">
                                <a href="{{ route('travel-permissions.show', $request->id) }}" class="btn btn-outline-secondary btn-sm rounded-sm" style="padding: 0.1rem 0.4rem; font-size: 0.6rem;">
                                    <i class="ti ti-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-3 text-muted">No travel requests found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($travelRequests->hasPages())
        <div class="card-footer py-1 px-2">
            {{ $travelRequests->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
</div>
@endsection