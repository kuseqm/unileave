@extends('layouts.master')

@section('title', 'University Policies')

@section('content')
<div class="container-fluid px-2">
    <x-breadcrumb :items="[
        ['name' => 'Dashboard', 'url' => route('dashboard')],
        ['name' => 'University Policies', 'url' => '#']
    ]" />

    <x-page-header title="UNIVERSITY POLICIES" description="Manage handbooks, examination regulations, and institutional policies" backUrl="{{ route('dashboard') }}">
        @can('create_policies')
        <a href="{{ route('policies.create') }}" class="btn btn-primary btn-sm rounded-sm"><i class="ti ti-plus me-1"></i> Add Policy</a>
        @endcan
    </x-page-header>

    <x-alert-success />
    <x-alert-danger />

    <div class="row g-2 mb-3">
        <x-stats-card value="{{ $stats['total'] }}" label="Total Policies" color="primary" icon="book" />
        <x-stats-card value="{{ $stats['active'] }}" label="Active" color="success" icon="check" />
        <x-stats-card value="{{ $stats['approved'] + $stats['pending'] }}" label="Pending / Approved" color="info" icon="clock" />
        <x-stats-card value="{{ $stats['archived'] }}" label="Archived / Revoked" color="secondary" icon="archive" />
    </div>

    <div class="card rounded-sm mb-3">
        <div class="card-header py-1 px-2 bg-light d-flex justify-content-between align-items-center">
            <span style="font-size: 0.7rem; font-weight: 600;"><i class="ti ti-search me-1"></i> Search & Filter</span>
            <span class="text-muted" style="font-size: 0.65rem;">{{ $policies->total() }} policies found</span>
        </div>
        <div class="card-body py-2 px-2">
            <form method="GET" action="{{ route('policies.index') }}">
                <div class="row g-1 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label" style="font-size: 0.65rem;">Search</label>
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm rounded-sm" placeholder="Search title, code, description...">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label" style="font-size: 0.65rem;">Status</label>
                        <select name="status" class="form-select form-select-sm rounded-sm">
                            <option value="">All</option>
                            @foreach($statuses as $key => $label)
                            <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label" style="font-size: 0.65rem;">Category</label>
                        <select name="category" class="form-select form-select-sm rounded-sm">
                            <option value="">All</option>
                            @foreach($categories as $key => $label)
                            <option value="{{ $key }}" {{ request('category') == $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary btn-sm w-100 rounded-sm">Filter</button>
                    </div>
                    <div class="col-md-2">
                        <a href="{{ route('policies.index') }}" class="btn btn-outline-secondary btn-sm w-100 rounded-sm">Reset</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-2">
        <!-- Policies List - main column -->
        <div class="col-lg-8">
            @include('policies._results')
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
                        <div class="fw-semibold" style="font-size: 0.7rem;"><i class="ti ti-flag me-1 text-primary"></i> Understanding Status</div>
                        <ul class="mb-0 ps-3" style="font-size: 0.65rem; color: #555;">
                            <li><span class="badge bg-secondary">Draft</span> - being written, not yet submitted</li>
                            <li><span class="badge bg-warning text-dark">Pending</span> - awaiting approval</li>
                            <li><span class="badge bg-info text-dark">Approved</span> - cleared, awaiting effective date</li>
                            <li><span class="badge bg-success">Active</span> - currently in force</li>
                            <li><span class="badge bg-dark">Archived</span> / <span class="badge bg-danger">Revoked</span> - no longer in force</li>
                        </ul>
                    </div>

                    <div class="mb-2">
                        <div class="fw-semibold" style="font-size: 0.7rem;"><i class="ti ti-link me-1 text-primary"></i> Quick Links</div>
                        <ul class="mb-0 ps-3" style="font-size: 0.65rem; color: #555;">
                            @can('create_policies')
                            <li><a href="{{ route('policies.create') }}">Add Policy</a> - create a new policy</li>
                            @endcan
                        </ul>
                    </div>

                    <x-alert-light class="mb-0 py-1 px-2 rounded-sm">
                        <div class="d-flex align-items-start gap-1">
                            <i class="ti ti-tips text-warning mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">Tips</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">&bull; Open a policy to view its Rules and Version History</small><br>
                                <small class="text-muted" style="font-size: 0.6rem;">&bull; Only draft, pending, archived, or revoked policies can be edited directly</small>
                            </div>
                        </div>
                    </x-alert-light>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
