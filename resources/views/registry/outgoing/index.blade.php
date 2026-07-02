@extends('layouts.master')

@section('title', 'Outgoing Letters')

@section('content')
<div class="container-fluid px-2">
    
    <!-- Breadcrumb -->
    <x-breadcrumb :items="[
        ['name' => 'Central Registry', 'url' => '#'],
        ['name' => 'Outgoing Letters', 'url' => '#']
    ]" />
    
    <!-- Page Header -->
    <x-page-header title="OUTGOING LETTERS" description="Manage all outgoing correspondence">
        <a href="{{ route('registry.outgoing.create') }}" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
            <i class="ti ti-plus me-1"></i> New Outgoing Letter
        </a>
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
                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="pending_approval" {{ request('status') == 'pending_approval' ? 'selected' : '' }}>Pending Approval</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="dispatched" {{ request('status') == 'dispatched' ? 'selected' : '' }}>Dispatched</option>
                    </select>
                </div>
                <div class="col-md-7">
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Search by ref number, recipient, subject..." value="{{ request('search') }}" style="font-size: 0.7rem;">
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
                <i class="ti ti-mail-forward text-primary me-1" style="font-size: 0.8rem;"></i>
                <span style="font-size: 0.7rem; font-weight: 600;">Outgoing Letters List</span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm mb-0" style="font-size: 0.7rem;">
                    <thead class="table-light">
                        <tr>
                            <th>Ref Number</th>
                            <th>Recipient</th>
                            <th>Subject</th>
                            <th>Status</th>
                            <th>Dispatched Via</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($letters as $letter)
                        <tr>
                            <td><strong>{{ $letter->ref_number }}</strong></td>
                            <td>{{ \Illuminate\Support\Str::limit($letter->recipient, 25) }}</td>
                            <td>{{ \Illuminate\Support\Str::limit($letter->subject, 30) }}</td>
                            <td>
                                @if($letter->status == 'draft')
                                    <span class="badge bg-secondary-subtle text-secondary rounded-sm" style="font-size: 0.6rem;">Draft</span>
                                @elseif($letter->status == 'pending_approval')
                                    <span class="badge bg-warning-subtle text-warning rounded-sm" style="font-size: 0.6rem;">Pending</span>
                                @elseif($letter->status == 'approved')
                                    <span class="badge bg-info-subtle text-info rounded-sm" style="font-size: 0.6rem;">Approved</span>
                                @elseif($letter->status == 'dispatched')
                                    <span class="badge bg-success-subtle text-success rounded-sm" style="font-size: 0.6rem;">Dispatched</span>
                                @endif
                            </td>
                            <td>
                                @if($letter->dispatched_via == 'email')
                                    <span class="badge bg-primary-subtle text-primary rounded-sm" style="font-size: 0.6rem;">Email</span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning rounded-sm" style="font-size: 0.6rem;">Print</span>
                                @endif
                            </td>
                            <td>{{ $letter->created_at->format('d/m/Y') }}</td>
                            <td>
                                <a href="{{ route('registry.outgoing.show', $letter->id) }}" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.6rem; padding: 0.15rem 0.4rem;">
                                    <i class="ti ti-eye"></i> View
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-3 text-muted" style="font-size: 0.7rem;">No outgoing letters found. <a href="{{ route('registry.outgoing.create') }}">Create one</a></td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer py-1 px-2 bg-light">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted" style="font-size: 0.6rem;">Showing {{ $letters->firstItem() ?? 0 }} to {{ $letters->lastItem() ?? 0 }} of {{ $letters->total() }} entries</div>
                <div>{{ $letters->appends(request()->query())->links('pagination::bootstrap-4') }}</div>
            </div>
        </div>
    </div>
</div>
@endsection