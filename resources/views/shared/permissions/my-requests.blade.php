@extends('layouts.master')

@section('title', 'My Permission Requests')

@section('content')
<div class="container-fluid px-2">
    
    <!-- Breadcrumb -->
    <x-breadcrumb :items="[
        ['name' => 'Dashboard', 'url' => route('dashboard')],
        ['name' => 'My Permission Requests', 'url' => '#']
    ]" />
    
    <!-- Page Header -->
    <x-page-header title="MY PERMISSION REQUESTS" description="View and manage your permission requests">
        <div>
            <a href="{{ route('permissions.create') }}" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
                <i class="ti ti-plus me-1"></i> Request Permission
            </a>
            <a href="{{ route('travel-permissions.create') }}" class="btn btn-outline-primary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
                <i class="ti ti-plane me-1"></i> Request Travel
            </a>
        </div>
    </x-page-header>
    
    <!-- Stats Cards -->
    @php
        $permissionStats = [
            'total' => $permissionRequests->total(),
            'pending' => $permissionRequests->where('status', 'pending')->count(),
            'approved' => $permissionRequests->where('status', 'approved')->count(),
            'rejected' => $permissionRequests->where('status', 'rejected')->count(),
        ];
    @endphp
    
    <div class="row g-2 mb-3">
        <x-stats-card value="{{ $permissionStats['total'] }}" label="Total Permissions" subtext="all time" />
        <x-stats-card value="{{ $permissionStats['pending'] }}" label="Pending" subtext="awaiting approval" color="warning" />
        <x-stats-card value="{{ $permissionStats['approved'] }}" label="Approved" subtext="granted" color="success" />
        <x-stats-card value="{{ $permissionStats['rejected'] }}" label="Rejected" subtext="denied" color="danger" />
    </div>
    
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show py-1 px-2 mb-2 rounded-sm" style="font-size: 0.7rem;" role="alert">
        <i class="ti ti-check-circle me-1"></i> {{ session('success') }}
        <button type="button" class="btn-close py-0" style="font-size: 0.6rem;" data-bs-dismiss="alert"></button>
    </div>
    @endif
    
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show py-1 px-2 mb-2 rounded-sm" style="font-size: 0.7rem;" role="alert">
        <i class="ti ti-alert-circle me-1"></i> {{ session('error') }}
        <button type="button" class="btn-close py-0" style="font-size: 0.6rem;" data-bs-dismiss="alert"></button>
    </div>
    @endif
    
    <!-- Short Permissions Table -->
    <div class="card rounded-sm">
        <div class="card-header py-1 px-2 bg-light">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <i class="ti ti-clock text-primary me-1" style="font-size: 0.8rem;"></i>
                    <span style="font-size: 0.7rem; font-weight: 600;">Short Permissions</span>
                </div>
                <div class="text-muted" style="font-size: 0.6rem;">
                    Showing {{ $permissionRequests->firstItem() ?? 0 }} to {{ $permissionRequests->lastItem() ?? 0 }} of {{ $permissionRequests->total() ?? 0 }}
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            @forelse($permissionRequests as $request)
            <div class="request-item p-3 border-bottom">
                <!-- Request Header -->
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-2">
                    <div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="fw-semibold" style="font-size: 0.85rem;">
                                {{ str_replace('_', ' ', ucfirst($request->permission_type)) }}
                            </span>
                            <span class="badge {{ 
                                $request->status == 'pending' ? 'bg-warning-subtle text-warning' : 
                                ($request->status == 'approved' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger') 
                            }} rounded-sm px-2 py-1" style="font-size: 0.6rem;">
                                {{ ucfirst($request->status) }}
                            </span>
                        </div>
                        <div class="text-muted" style="font-size: 0.65rem; margin-top: 4px;">
                            <i class="ti ti-calendar me-1"></i>
                            {{ \Carbon\Carbon::parse($request->start_datetime)->format('M d, Y H:i') }} - 
                            {{ \Carbon\Carbon::parse($request->end_datetime)->format('M d, Y H:i') }}
                            <span class="mx-1">•</span>
                            <i class="ti ti-clock me-1"></i>{{ $request->hours }} hour(s)
                        </div>
                    </div>
                    <div>
                        <a href="{{ route('permissions.show', $request->id) }}" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.6rem; padding: 0.2rem 0.5rem;">
                            <i class="ti ti-eye me-1"></i> Details
                        </a>
                    </div>
                </div>
                
                <!-- Reason -->
                <div class="mt-2">
                    <div class="text-muted" style="font-size: 0.6rem;">Reason</div>
                    <div style="font-size: 0.7rem;">{{ Str::limit($request->reason, 150) }}</div>
                </div>
                
                <!-- Official Duty Description (if applicable) -->
                @if($request->permission_type == 'official_duty' && $request->official_duty_description)
                <div class="mt-2 pt-1">
                    <div class="text-muted" style="font-size: 0.6rem;">Official Duty Description</div>
                    <div style="font-size: 0.7rem;">{{ Str::limit($request->official_duty_description, 100) }}</div>
                </div>
                @endif
                
                <!-- Rejection Reason (if rejected) -->
                @if($request->status == 'rejected' && $request->rejection_reason)
                <div class="mt-2 pt-1">
                    <div class="text-muted text-danger" style="font-size: 0.6rem;">Rejection Reason</div>
                    <div class="text-danger" style="font-size: 0.7rem;">{{ $request->rejection_reason }}</div>
                </div>
                @endif
            </div>
            @empty
            <div class="text-center py-4">
                <i class="ti ti-clock" style="font-size: 2rem; color: #CBD5E1;"></i>
                <p class="text-muted mt-2" style="font-size: 0.75rem;">You haven't submitted any permission requests yet.</p>
                <a href="{{ route('permissions.create') }}" class="btn btn-primary btn-sm rounded-sm mt-2" style="font-size: 0.7rem;">
                    <i class="ti ti-plus me-1"></i> Request Permission
                </a>
            </div>
            @endforelse
        </div>
        
        <!-- Pagination -->
        @if($permissionRequests->hasPages())
        <div class="card-footer py-2 px-3 bg-light">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <span class="text-muted" style="font-size: 0.65rem;">Show:</span>
                    <select id="perPageSelect" class="custom-select-sm" style="font-size: 0.65rem; padding: 0.2rem 1.2rem 0.2rem 0.4rem; border: 1px solid #E8E2D5; border-radius: 4px;">
                        <option value="10" {{ $permissionRequests->perPage() == 10 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ $permissionRequests->perPage() == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ $permissionRequests->perPage() == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ $permissionRequests->perPage() == 100 ? 'selected' : '' }}>100</option>
                    </select>
                </div>
                
                <div>
                    {{ $permissionRequests->appends(request()->query())->links('pagination::bootstrap-5') }}
                </div>
                
                <div class="text-muted" style="font-size: 0.6rem;">
                    Page {{ $permissionRequests->currentPage() }} of {{ $permissionRequests->lastPage() }}
                </div>
            </div>
        </div>
        @endif
    </div>
    
    <!-- Travel Permissions Section (Existing Travel Requests) -->
    @if(isset($travelRequests) && $travelRequests->count() > 0)
    <div class="card rounded-sm mt-2">
        <div class="card-header py-1 px-2 bg-light">
            <div class="d-flex align-items-center">
                <i class="ti ti-plane text-primary me-1" style="font-size: 0.8rem;"></i>
                <span style="font-size: 0.7rem; font-weight: 600;">Travel Permissions</span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm mb-0" style="font-size: 0.75rem;">
                    <thead>
                        <tr>
                            <th class="ps-2 py-1">Destination</th>
                            <th class="py-1">Period</th>
                            <th class="py-1">Days</th>
                            <th class="py-1">Reason</th>
                            <th class="py-1">Status</th>
                            <th class="pe-2 py-1">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($travelRequests as $request)
                        <tr>
                            <td class="ps-2 py-1">{{ $request->destination }}</td>
                            <td class="py-1">
                                {{ \Carbon\Carbon::parse($request->start_date)->format('d M') }} - 
                                {{ \Carbon\Carbon::parse($request->end_date)->format('d M') }}
                            </td>
                            <td class="py-1">{{ $request->days }}</td>
                            <td class="py-1">{{ ucfirst($request->travel_reason) }}</td>
                            <td class="py-1">
                                @if($request->status == 'pending')
                                    <span class="badge bg-warning-subtle text-warning rounded-sm">Pending</span>
                                @elseif($request->status == 'approved')
                                    <span class="badge bg-success-subtle text-success rounded-sm">Approved</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger rounded-sm">Rejected</span>
                                @endif
                            </td>
                            <td class="pe-2 py-1">
                                <a href="{{ route('travel-permissions.show', $request->id) }}" class="btn btn-outline-secondary btn-sm rounded-sm" style="padding: 0.1rem 0.4rem;">
                                    <i class="ti ti-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>

<style>
.request-item {
    transition: background 0.2s ease;
}
.request-item:hover {
    background: #F8FAFC;
}
.custom-select-sm {
    font-size: 0.65rem;
    padding: 0.2rem 1.2rem 0.2rem 0.4rem;
    border: 1px solid #E8E2D5;
    border-radius: 4px;
    background: white;
    cursor: pointer;
}
</style>

<script>
// Per Page Change Handler
const perPageSelect = document.getElementById('perPageSelect');
if (perPageSelect) {
    perPageSelect.addEventListener('change', function() {
        const url = new URL(window.location.href);
        url.searchParams.set('per_page', this.value);
        url.searchParams.delete('page');
        window.location.href = url.toString();
    });
}
</script>
@endsection