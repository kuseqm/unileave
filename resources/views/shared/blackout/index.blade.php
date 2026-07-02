@extends('layouts.master')

@section('title', 'Blackout Periods')

@section('content')
<div class="container-fluid px-2">
    
    <x-breadcrumb :items="[
        ['name' => 'Dashboard', 'url' => route('dashboard')],
        ['name' => 'Blackout Periods', 'url' => '#']
    ]" />
    
    <x-page-header title="BLACKOUT PERIODS" description="Periods when leave requests are restricted">
        @hasanyrole('admin|hr-officer|dean')
        <a href="{{ route('blackout.create') }}" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
            <i class="ti ti-plus me-1"></i> Add Blackout Period
        </a>
        @endhasanyrole
    </x-page-header>
    
    <!-- Stats Cards -->
    @php
        $totalBlackouts = \App\Models\BlackoutPeriod::count();
        $activeBlackouts = \App\Models\BlackoutPeriod::where('end_date', '>=', now())->count();
        $hardBlackouts = \App\Models\BlackoutPeriod::where('restriction_type', 'hard')->where('end_date', '>=', now())->count();
        $upcomingBlackouts = \App\Models\BlackoutPeriod::where('start_date', '>', now())->count();
    @endphp
    
    <div class="row g-2 mb-3">
        <x-stats-card value="{{ $totalBlackouts }}" label="Total Blackouts" subtext="all time" icon="ban" />
        <x-stats-card value="{{ $activeBlackouts }}" label="Active Blackouts" subtext="currently in effect" color="danger" icon="alert-circle" />
        <x-stats-card value="{{ $hardBlackouts }}" label="Hard Blocks" subtext="no leave allowed" color="danger" icon="x-circle" />
        <x-stats-card value="{{ $upcomingBlackouts }}" label="Upcoming" subtext="starting soon" color="info" icon="calendar" />
    </div>
    
    <div class="card rounded-sm">
        <div class="card-header py-1 px-2 bg-light">
            <div class="d-flex align-items-center">
                <i class="ti ti-ban text-danger me-1" style="font-size: 0.8rem;"></i>
                <span style="font-size: 0.7rem; font-weight: 600;">Blackout Periods List</span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm mb-0" style="font-size: 0.75rem;">
                    <thead>
                        <tr>
                            <th class="ps-2 py-1">Name</th>
                            <th class="py-1">Start Date</th>
                            <th class="py-1">End Date</th>
                            <th class="py-1">Days Left</th>
                            <th class="py-1">Restriction</th>
                            <th class="py-1">Override</th>
                            <th class="pe-2 py-1">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($blackoutPeriods as $blackout)
                        @php
                            $daysLeft = \Carbon\Carbon::parse($blackout->end_date)->diffInDays(now(), false);
                            $isActive = $daysLeft > 0 && \Carbon\Carbon::parse($blackout->start_date)->isPast();
                            $isUpcoming = \Carbon\Carbon::parse($blackout->start_date)->isFuture();
                        @endphp
                        <tr class="clickable-row" style="cursor: pointer;" onclick="window.location='{{ route('blackout.show', $blackout->id) }}'">
                            <td class="ps-2 py-1">
                                <span class="fw-semibold hover-link">{{ $blackout->name }}</span>
                                @if($isActive)
                                    <span class="badge bg-danger-subtle text-danger rounded-sm ms-1" style="font-size: 0.55rem;">Active</span>
                                @elseif($isUpcoming)
                                    <span class="badge bg-info-subtle text-info rounded-sm ms-1" style="font-size: 0.55rem;">Upcoming</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary rounded-sm ms-1" style="font-size: 0.55rem;">Expired</span>
                                @endif
                            </td>
                            <td class="py-1">{{ \Carbon\Carbon::parse($blackout->start_date)->format('d M Y') }}</td>
                            <td class="py-1">{{ \Carbon\Carbon::parse($blackout->end_date)->format('d M Y') }}</td>
                            <td class="py-1">
                                @if($daysLeft > 0)
                                    <span class="fw-semibold text-warning">{{ $daysLeft }} days</span>
                                @else
                                    <span class="text-muted">Expired</span>
                                @endif
                            </td>
                            <td class="py-1">
                                @if($blackout->restriction_type == 'hard')
                                    <span class="badge bg-danger-subtle text-danger rounded-sm">Hard Block</span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning rounded-sm">Soft Warning</span>
                                @endif
                            </td>
                            <td class="py-1">
                                @if($blackout->override_allowed)
                                    <span class="badge bg-success-subtle text-success rounded-sm">Allowed</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary rounded-sm">Not Allowed</span>
                                @endif
                            </td>
                            <td class="pe-2 py-1">
                                <div class="d-flex gap-1">
                                    <!-- View Button (Eye Icon) - All authenticated users can view -->
                                    <a href="{{ route('blackout.show', $blackout->id) }}" class="btn btn-outline-info btn-sm rounded-sm" style="padding: 0.1rem 0.4rem; font-size: 0.6rem;" title="View Details" onclick="event.stopPropagation();">
                                        <i class="ti ti-eye"></i>
                                    </a>
                                    
                                    <!-- Edit Button - Admin/HR/Dean only -->
                                    @hasanyrole('admin|hr-officer|dean')
                                    <a href="{{ route('blackout.edit', $blackout->id) }}" class="btn btn-outline-primary btn-sm rounded-sm" style="padding: 0.1rem 0.4rem; font-size: 0.6rem;" title="Edit Blackout" onclick="event.stopPropagation();">
                                        <i class="ti ti-edit"></i>
                                    </a>
                                    @endhasanyrole
                                    
                                    <!-- Delete Button - Admin/HR only with Standard Modal -->
                                    @hasanyrole('admin|hr-officer')
                                    <button type="button" 
                                            onclick="event.stopPropagation(); showDeleteModal('{{ $blackout->id }}', '{{ addslashes($blackout->name) }}', 'blackout', '{{ route('blackout.destroy', $blackout->id) }}')" 
                                            class="btn btn-outline-danger btn-sm rounded-sm" 
                                            style="padding: 0.1rem 0.4rem; font-size: 0.6rem;"
                                            title="Delete Blackout">
                                        <i class="ti ti-trash"></i>
                                    </button>
                                    @endhasanyrole
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-3 text-muted">No blackout periods found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer py-1 px-2">
            {{ $blackoutPeriods->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>

<style>
.clickable-row {
    transition: background 0.2s ease;
}
.clickable-row:hover {
    background-color: #F8FAFC !important;
}
.hover-link:hover {
    text-decoration: underline;
    color: #3B82F6 !important;
}
.d-flex.gap-1 {
    gap: 0.25rem;
}
</style>
@endsection