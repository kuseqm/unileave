@extends('layouts.master')

@section('title', 'Blackout Period Details')

@section('content')
<div class="container-fluid px-2">
    
    <x-breadcrumb :items="[
        ['name' => 'Blackout Periods', 'url' => route('blackout.index')],
        ['name' => 'Blackout Details', 'url' => '#']
    ]" />
    
    <x-page-header title="BLACKOUT PERIOD DETAILS" description="View blackout period information">
        <div>
            @hasanyrole('admin|hr-officer|dean')
            <a href="{{ route('blackout.edit', $blackout->id) }}" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
                <i class="ti ti-edit me-1"></i> Edit
            </a>
            <button type="button" 
                    onclick="showDeleteModal('{{ $blackout->id }}', '{{ addslashes($blackout->name) }}', 'blackout', '{{ route('blackout.destroy', $blackout->id) }}')" 
                    class="btn btn-danger btn-sm rounded-sm" 
                    style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
                <i class="ti ti-trash me-1"></i> Delete
            </button>
            @endhasanyrole
            <a href="{{ route('blackout.index') }}" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
                <i class="ti ti-arrow-left me-1"></i> Back
            </a>
        </div>
    </x-page-header>
    
    <div class="row g-2">
        <!-- Main Details Column -->
        <div class="col-lg-7">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-info-circle text-primary me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Blackout Period Information</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <div class="row g-1">
                        <div class="col-md-12 mb-1">
                            <div class="text-muted" style="font-size: 0.6rem;">Name</div>
                            <div class="fw-semibold" style="font-size: 0.75rem;">{{ $blackout->name }}</div>
                        </div>
                    </div>
                    
                    <div class="row g-1">
                        <div class="col-md-6 mb-1">
                            <div class="text-muted" style="font-size: 0.6rem;">Start Date</div>
                            <div style="font-size: 0.75rem;">{{ \Carbon\Carbon::parse($blackout->start_date)->format('d M Y') }}</div>
                        </div>
                        <div class="col-md-6 mb-1">
                            <div class="text-muted" style="font-size: 0.6rem;">End Date</div>
                            <div style="font-size: 0.75rem;">{{ \Carbon\Carbon::parse($blackout->end_date)->format('d M Y') }}</div>
                        </div>
                    </div>
                    
                    <div class="row g-1">
                        <div class="col-md-6 mb-1">
                            <div class="text-muted" style="font-size: 0.6rem;">Duration</div>
                            <div class="fw-semibold" style="font-size: 0.75rem;">
                                {{ \Carbon\Carbon::parse($blackout->start_date)->diffInDays($blackout->end_date) + 1 }} day(s)
                            </div>
                        </div>
                        <div class="col-md-6 mb-1">
                            <div class="text-muted" style="font-size: 0.6rem;">Days Remaining</div>
                            <div class="fw-semibold" style="font-size: 0.75rem;">
                                @php
                                    $daysLeft = \Carbon\Carbon::parse($blackout->end_date)->diffInDays(now(), false);
                                @endphp
                                @if($daysLeft > 0)
                                    <span class="text-warning">{{ $daysLeft }} days</span>
                                @else
                                    <span class="text-muted">Expired</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <div class="row g-1">
                        <div class="col-md-6 mb-1">
                            <div class="text-muted" style="font-size: 0.6rem;">Restriction Type</div>
                            <div>
                                @if($blackout->restriction_type == 'hard')
                                    <span class="badge bg-danger-subtle text-danger rounded-sm">Hard Block</span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning rounded-sm">Soft Warning</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6 mb-1">
                            <div class="text-muted" style="font-size: 0.6rem;">Override Allowed</div>
                            <div>
                                @if($blackout->override_allowed)
                                    <span class="badge bg-success-subtle text-success rounded-sm">Yes</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary rounded-sm">No</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <div class="row g-1">
                        <div class="col-md-12 mb-1">
                            <div class="text-muted" style="font-size: 0.6rem;">Status</div>
                            <div>
                                @php
                                    $isActive = $daysLeft > 0 && \Carbon\Carbon::parse($blackout->start_date)->isPast();
                                    $isUpcoming = \Carbon\Carbon::parse($blackout->start_date)->isFuture();
                                @endphp
                                @if($isActive)
                                    <span class="badge bg-danger-subtle text-danger rounded-sm">Active</span>
                                @elseif($isUpcoming)
                                    <span class="badge bg-info-subtle text-info rounded-sm">Upcoming</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary rounded-sm">Expired</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <div class="row g-1">
                        <div class="col-md-12 mb-1">
                            <div class="text-muted" style="font-size: 0.6rem;">Created By</div>
                            <div style="font-size: 0.75rem;">
                                @php
                                    $creator = \App\Models\User::find($blackout->created_by);
                                @endphp
                                @if($creator)
                                    {{ $creator->first_name }} {{ $creator->last_name }} ({{ $creator->staff_id ?? '' }})
                                @else
                                    System
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <div class="row g-1">
                        <div class="col-md-12">
                            <div class="text-muted" style="font-size: 0.6rem;">Created At</div>
                            <div style="font-size: 0.7rem;">{{ \Carbon\Carbon::parse($blackout->created_at)->format('d M Y H:i') }}</div>
                        </div>
                    </div>
                    
                    @if($blackout->updated_at != $blackout->created_at)
                    <div class="row g-1 mt-1">
                        <div class="col-md-12">
                            <div class="text-muted" style="font-size: 0.6rem;">Last Updated</div>
                            <div style="font-size: 0.7rem;">{{ \Carbon\Carbon::parse($blackout->updated_at)->format('d M Y H:i') }}</div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Info Column -->
        <div class="col-lg-5">
            <!-- Status Card -->
            <div class="card rounded-sm mb-2">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-alert-circle text-warning me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Impact Assessment</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    @if($isActive)
                    <x-alert-danger :session="false">
    <strong>This blackout period is currently ACTIVE!</strong>
                        <small class="d-block mt-1">Staff cannot apply for leave during this period.</small>
</x-alert-danger>
                    @endif
                    
                    <x-alert-light class="mb-2 py-1 px-2 rounded-sm">

                        <div class="d-flex align-items-start gap-1">
                            <i class="ti ti-users text-primary mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">Affected Staff</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">All staff members in the university are affected by this blackout period.</small>
                            </div>
                        
</x-alert-light>
                    </div>
                    
                    @if($blackout->restriction_type == 'hard')
                    <x-alert-light class="mb-2 py-1 px-2 rounded-sm">

                        <div class="d-flex align-items-start gap-1">
                            <i class="ti ti-ban text-danger mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">Hard Block Impact</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">• No leave applications will be accepted</small><br>
                                <small class="text-muted" style="font-size: 0.6rem;">• Pending requests may be affected</small><br>
                                <small class="text-muted" style="font-size: 0.6rem;">• Only Admin/HR can override (if allowed)</small>
                            </div>
                        
</x-alert-light>
                    </div>
                    @else
                    <x-alert-light class="mb-2 py-1 px-2 rounded-sm">

                        <div class="d-flex align-items-start gap-1">
                            <i class="ti ti-info-circle text-warning mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">Soft Warning Impact</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">• Staff will see a warning message</small><br>
                                <small class="text-muted" style="font-size: 0.6rem;">• Leave applications are still allowed</small><br>
                                <small class="text-muted" style="font-size: 0.6rem;">• Approvers are notified of the blackout</small>
                            </div>
                        
</x-alert-light>
                    </div>
                    @endif
                    
                    @if($blackout->override_allowed)
                    <x-alert-light class="mb-0 py-1 px-2 rounded-sm">

                        <div class="d-flex align-items-start gap-1">
                            <i class="ti ti-user-check text-success mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">Override Available</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">Admin and HR officers can approve leave requests during this period if necessary.</small>
                            </div>
                        
</x-alert-light>
                    </div>
                    @endif
                </div>
            </div>
            
            <!-- Quick Actions Card -->
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-tools text-primary me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Quick Actions</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <div class="d-grid gap-1">
                        @hasanyrole('admin|hr-officer|dean')
                        <a href="{{ route('blackout.edit', $blackout->id) }}" class="btn btn-outline-primary btn-sm rounded-sm" style="font-size: 0.65rem;">
                            <i class="ti ti-edit me-1"></i> Edit This Blackout
                        </a>
                        <button type="button" 
                                onclick="showDeleteModal('{{ $blackout->id }}', '{{ addslashes($blackout->name) }}', 'blackout', '{{ route('blackout.destroy', $blackout->id) }}')" 
                                class="btn btn-outline-danger btn-sm rounded-sm" 
                                style="font-size: 0.65rem;">
                            <i class="ti ti-trash me-1"></i> Delete This Blackout
                        </button>
                        @endhasanyrole
                        <a href="{{ route('blackout.create') }}" class="btn btn-outline-success btn-sm rounded-sm" style="font-size: 0.65rem;">
                            <i class="ti ti-plus me-1"></i> Create New Blackout
                        </a>
                        <a href="{{ route('blackout.index') }}" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.65rem;">
                            <i class="ti ti-list me-1"></i> View All Blackouts
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection