@extends('layouts.master')

@section('title', 'Incoming Letter Details')

@section('content')
<div class="container-fluid px-2">
    
    <!-- Breadcrumb -->
    <x-breadcrumb :items="[
        ['name' => 'Central Registry', 'url' => '#'],
        ['name' => 'Incoming Letters', 'url' => route('registry.incoming.index')],
        ['name' => 'Letter Details', 'url' => '#']
    ]" />
    
    <!-- Page Header -->
    <x-page-header title="INCOMING LETTER DETAILS" :description="'Reference: ' . $letter->ref_number">
        <div class="d-flex gap-1">
            <a href="{{ route('registry.incoming.index') }}" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
                <i class="ti ti-arrow-left me-1"></i> Back
            </a>
            <a href="{{ route('registry.incoming.edit', $letter->id) }}" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
                <i class="ti ti-edit me-1"></i> Edit
            </a>
        </div>
    </x-page-header>
    
    <div class="row g-2">
        <!-- Main Details Column -->
        <div class="col-lg-7">
            <!-- Letter Information Card -->
            <div class="card rounded-sm mb-2">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-file-text text-primary me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Letter Information</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <div class="row g-1">
                        <div class="col-md-6">
                            <div class="text-muted" style="font-size: 0.6rem;">Reference Number</div>
                            <div class="fw-semibold" style="font-size: 0.75rem;">{{ $letter->ref_number }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted" style="font-size: 0.6rem;">Date Received</div>
                            <div style="font-size: 0.75rem;">{{ $letter->date_received->format('d M Y') }}</div>
                        </div>
                    </div>
                    
                    <div class="row g-1 mt-1">
                        <div class="col-12">
                            <div class="text-muted" style="font-size: 0.6rem;">Sender</div>
                            <div style="font-size: 0.75rem;">{{ $letter->sender }}</div>
                        </div>
                    </div>
                    
                    <div class="row g-1 mt-1">
                        <div class="col-md-6">
                            <div class="text-muted" style="font-size: 0.6rem;">Addressee Office</div>
                            <div style="font-size: 0.75rem;">{{ $letter->office->name }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted" style="font-size: 0.6rem;">Priority</div>
                            <div>
                                @if($letter->priority == 'urgent')
                                    <span class="badge bg-danger-subtle text-danger rounded-sm" style="font-size: 0.6rem;">Urgent</span>
                                @elseif($letter->priority == 'confidential')
                                    <span class="badge bg-dark-subtle text-dark rounded-sm" style="font-size: 0.6rem;">Confidential</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary rounded-sm" style="font-size: 0.6rem;">Normal</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-2">
                        <div class="text-muted" style="font-size: 0.6rem;">Subject</div>
                        <div class="p-1 bg-light rounded-sm" style="font-size: 0.7rem;">{{ $letter->subject }}</div>
                    </div>
                    
                    @if($letter->remarks)
                    <div class="mt-2">
                        <div class="text-muted" style="font-size: 0.6rem;">Remarks</div>
                        <div class="p-1 bg-light rounded-sm" style="font-size: 0.7rem;">{{ $letter->remarks }}</div>
                    </div>
                    @endif
                    
                    @if($letter->pdf_path)
                    <div class="mt-2">
                        <div class="text-muted" style="font-size: 0.6rem;">Attached Document</div>
                        <a href="{{ asset('storage/' . $letter->pdf_path) }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-sm mt-1" style="font-size: 0.65rem; padding: 0.15rem 0.4rem;">
                            <i class="ti ti-file-pdf me-1"></i> View PDF
                        </a>
                    </div>
                    @endif
                </div>
            </div>
            
            <!-- Response Letters Card -->
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-mail-forward text-primary me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Response Letters</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($letter->outgoingLetters->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm mb-0" style="font-size: 0.65rem;">
                                <thead class="table-light">
                                    <tr>
                                        <th>Ref Number</th>
                                        <th>Recipient</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($letter->outgoingLetters as $outgoing)
                                    <tr>
                                        <td><strong>{{ $outgoing->ref_number }}</strong></td>
                                        <td>{{ \Illuminate\Support\Str::limit($outgoing->recipient, 25) }}</td>
                                        <td>
                                            @if($outgoing->status == 'draft')
                                                <span class="badge bg-secondary-subtle text-secondary rounded-sm" style="font-size: 0.55rem;">Draft</span>
                                            @elseif($outgoing->status == 'dispatched')
                                                <span class="badge bg-success-subtle text-success rounded-sm" style="font-size: 0.55rem;">Dispatched</span>
                                            @else
                                                <span class="badge bg-warning-subtle text-warning rounded-sm" style="font-size: 0.55rem;">{{ ucfirst($outgoing->status) }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $outgoing->created_at->format('d/m/Y') }}</td>
                                        <td>
                                            <a href="{{ route('registry.outgoing.show', $outgoing->id) }}" class="btn btn-sm btn-primary rounded-sm" style="font-size: 0.55rem; padding: 0.1rem 0.3rem;">
                                                <i class="ti ti-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-3 text-muted" style="font-size: 0.65rem;">
                            <i class="ti ti-mail"></i> No response letters yet.
                        </div>
                    @endif
                </div>
                <div class="card-footer py-1 px-2 bg-light">
                    <a href="{{ route('registry.outgoing.create', ['incoming_id' => $letter->id]) }}" class="btn btn-success btn-sm rounded-sm w-100" style="font-size: 0.65rem; padding: 0.2rem 0.5rem;">
                        <i class="ti ti-plus me-1"></i> Create Response Letter
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Status & Assignment Column -->
        <div class="col-lg-5">
            <!-- Status Card -->
            <div class="card rounded-sm mb-2">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-info-circle text-primary me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Status</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <div class="row g-1">
                        <div class="col-12">
                            <div class="text-muted" style="font-size: 0.6rem;">Current Status</div>
                            <div class="mt-1">
                                @if($letter->status == 'received')
                                    <span class="badge bg-warning-subtle text-warning rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">Received</span>
                                @elseif($letter->status == 'assigned')
                                    <span class="badge bg-info-subtle text-info rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">Assigned</span>
                                @elseif($letter->status == 'in_progress')
                                    <span class="badge bg-primary-subtle text-primary rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">In Progress</span>
                                @elseif($letter->status == 'replied')
                                    <span class="badge bg-success-subtle text-success rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">Replied</span>
                                @elseif($letter->status == 'closed')
                                    <span class="badge bg-secondary-subtle text-secondary rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">Closed</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <hr class="my-2" style="border-color: #E8E2D5;">
                    
                    <form method="POST" action="{{ route('registry.incoming.status', $letter->id) }}">
                        @csrf
                        <div class="mb-2">
                            <label class="form-label text-muted" style="font-size: 0.6rem;">Update Status</label>
                            <select name="status" class="form-select form-select-sm" style="font-size: 0.7rem;">
                                <option value="received" {{ $letter->status == 'received' ? 'selected' : '' }}>Received</option>
                                <option value="assigned" {{ $letter->status == 'assigned' ? 'selected' : '' }}>Assigned</option>
                                <option value="in_progress" {{ $letter->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="replied" {{ $letter->status == 'replied' ? 'selected' : '' }}>Replied</option>
                                <option value="closed" {{ $letter->status == 'closed' ? 'selected' : '' }}>Closed</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm w-100 rounded-sm" style="font-size: 0.65rem; padding: 0.2rem 0.5rem;">
                            <i class="ti ti-refresh me-1"></i> Update Status
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- Assignment Card -->
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-user-check text-primary me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Assignment</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <div class="mb-2">
                        <div class="text-muted" style="font-size: 0.6rem;">Assigned To</div>
                        <div class="fw-semibold" style="font-size: 0.75rem;">
                            @if($letter->assignedTo)
                                {{ $letter->assignedTo->first_name ?? '' }} {{ $letter->assignedTo->last_name ?? $letter->assignedTo->name ?? '' }}
                            @else
                                <span class="text-muted">Not assigned</span>
                            @endif
                        </div>
                    </div>
                    
                    <form method="POST" action="{{ route('registry.incoming.assign', $letter->id) }}">
                        @csrf
                        <div class="mb-2">
                            <label class="form-label text-muted" style="font-size: 0.6rem;">Assign to Officer</label>
                            <select name="assigned_to" class="form-select form-select-sm" style="font-size: 0.7rem;">
                                <option value="">-- Select Officer --</option>
                                @foreach($availableOfficers as $officer)
                                <option value="{{ $officer->id }}" {{ $letter->assigned_to == $officer->id ? 'selected' : '' }}>
                                    {{ $officer->first_name ?? '' }} {{ $officer->last_name ?? $officer->name ?? '' }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm w-100 rounded-sm" style="font-size: 0.65rem; padding: 0.2rem 0.5rem;">
                            <i class="ti ti-user-plus me-1"></i> Assign
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection