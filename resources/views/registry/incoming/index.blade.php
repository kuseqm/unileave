@extends('layouts.master')

@section('title', 'Incoming Letters')

@section('content')
<div class="container-fluid px-2">
    
    <!-- Breadcrumb -->
    <x-breadcrumb :items="[
        ['name' => 'Central Registry', 'url' => '#'],
        ['name' => 'Incoming Letters', 'url' => '#']
    ]" />
    
    <!-- Page Header -->
    <x-page-header title="INCOMING LETTERS" description="Manage all incoming correspondence">
        <div class="d-flex gap-1">
            <a href="{{ route('registry.incoming.create') }}" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
                <i class="ti ti-plus me-1"></i> New Incoming Letter
            </a>
        </div>
    </x-page-header>
    
    <!-- Filter Card -->
    <div class="card rounded-sm mb-2">
        <div class="card-header py-1 px-2 bg-light">
            <div class="d-flex align-items-center">
                <i class="ti ti-filter text-primary me-1" style="font-size: 0.8rem;"></i>
                <span style="font-size: 0.7rem; font-weight: 600;">Filter Letters</span>
            </div>
        </div>
        <div class="card-body py-2 px-2">
            <form method="GET" class="row g-1">
                <div class="col-md-3">
                    <select name="status" class="form-select form-select-sm" style="font-size: 0.7rem;">
                        <option value="">All Status</option>
                        <option value="received" {{ request('status') == 'received' ? 'selected' : '' }}>Received</option>
                        <option value="assigned" {{ request('status') == 'assigned' ? 'selected' : '' }}>Assigned</option>
                        <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="replied" {{ request('status') == 'replied' ? 'selected' : '' }}>Replied</option>
                        <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="office_id" class="form-select form-select-sm" style="font-size: 0.7rem;">
                        <option value="">All Offices</option>
                        @foreach($offices as $office)
                        <option value="{{ $office->id }}" {{ request('office_id') == $office->id ? 'selected' : '' }}>{{ $office->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Search by ref, sender, subject..." value="{{ request('search') }}" style="font-size: 0.7rem;">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary btn-sm w-100" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">Filter</button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Letters Table Card -->
    <div class="card rounded-sm">
        <div class="card-header py-1 px-2 bg-light">
            <div class="d-flex align-items-center">
                <i class="ti ti-list text-primary me-1" style="font-size: 0.8rem;"></i>
                <span style="font-size: 0.7rem; font-weight: 600;">Incoming Letters List</span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm mb-0" style="font-size: 0.7rem;">
                    <thead class="table-light">
                        <tr>
                            <th>Ref Number</th>
                            <th>Date Received</th>
                            <th>Sender</th>
                            <th>Office</th>
                            <th>Subject</th>
                            <th>Status</th>
                            <th>Priority</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($letters as $letter)
                        <tr>
                            <td><strong>{{ $letter->ref_number }}</strong></td>
                            <td>{{ $letter->date_received->format('d/m/Y') }}</td>
                            <td>{{ \Illuminate\Support\Str::limit($letter->sender, 25) }}</td>
                            <td>{{ $letter->office->short_name ?? $letter->office->name }}</td>
                            <td>{{ \Illuminate\Support\Str::limit($letter->subject, 35) }}</td>
                            <td>
                                @if($letter->status == 'received')
                                    <span class="badge bg-warning-subtle text-warning rounded-sm" style="font-size: 0.6rem;">Received</span>
                                @elseif($letter->status == 'assigned')
                                    <span class="badge bg-info-subtle text-info rounded-sm" style="font-size: 0.6rem;">Assigned</span>
                                @elseif($letter->status == 'closed')
                                    <span class="badge bg-success-subtle text-success rounded-sm" style="font-size: 0.6rem;">Closed</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary rounded-sm" style="font-size: 0.6rem;">{{ ucfirst($letter->status) }}</span>
                                @endif
                            </td>
                            <td>
                                @if($letter->priority == 'urgent')
                                    <span class="badge bg-danger-subtle text-danger rounded-sm" style="font-size: 0.6rem;">Urgent</span>
                                @elseif($letter->priority == 'confidential')
                                    <span class="badge bg-dark-subtle text-dark rounded-sm" style="font-size: 0.6rem;">Confidential</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary rounded-sm" style="font-size: 0.6rem;">Normal</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('registry.incoming.show', $letter->id) }}" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.6rem; padding: 0.15rem 0.4rem;">
                                    <i class="ti ti-eye"></i> View
                                </a>
                            </td>
                        </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-3 text-muted" style="font-size: 0.7rem;">No incoming letters found. <a href="{{ route('registry.incoming.create') }}">Create one</a></td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer py-1 px-2 bg-light">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted" style="font-size: 0.6rem;">Showing {{ $letters->firstItem() ?? 0 }} to {{ $letters->lastItem() ?? 0 }} of {{ $letters->total() }} entries</div>
                <div>
                    {{ $letters->appends(request()->query())->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection