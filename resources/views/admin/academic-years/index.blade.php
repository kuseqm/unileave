@extends('layouts.master')

@section('title', 'Academic Years')

@section('content')
<div class="container-fluid px-2">
    <x-breadcrumb :items="[
        ['name' => 'Dashboard', 'url' => route('dashboard')],
        ['name' => 'Academic Years', 'url' => route('admin.academic-years.index')]
    ]" />

    <x-page-header title="ACADEMIC YEARS" description="The active academic year drives the leave-year window shown across the system. Only one year can be active at a time.">
        <a href="{{ route('admin.academic-years.create') }}" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
            <i class="ti ti-plus me-1"></i> New Academic Year
        </a>
    </x-page-header>

    <x-alert-success />
    <x-alert-danger />

    <div class="row g-2">
        <!-- Main Column -->
        <div class="col-lg-8">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-calendar-stats text-primary me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Academic Years</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0" style="font-size: 0.75rem;">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-2">Label</th>
                                    <th style="width: 190px;">Window</th>
                                    <th style="width: 220px;">Current Semester</th>
                                    <th style="width: 90px;">Status</th>
                                    <th class="pe-2" style="width: 160px;"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($academicYears as $y)
                                <tr>
                                    <td class="ps-2"><strong>{{ $y->label }}</strong></td>
                                    <td class="text-muted">{{ $y->start_date->format('d M Y') }} &rarr; {{ $y->end_date->format('d M Y') }}</td>
                                    <td>
                                        @if($y->is_current)
                                        <form method="POST" action="{{ route('admin.academic-years.set-semester', $y->id) }}" class="d-flex gap-1">
                                            @csrf
                                            @method('PUT')
                                            <select name="current_semester" class="form-select form-select-sm rounded-sm" style="font-size: 0.7rem;" onchange="this.form.submit()">
                                                <option value="1" {{ $y->current_semester === 1 ? 'selected' : '' }}>{{ $y->semester_1_name }}</option>
                                                <option value="2" {{ $y->current_semester === 2 ? 'selected' : '' }}>{{ $y->semester_2_name }}</option>
                                            </select>
                                        </form>
                                        @else
                                            <span class="text-muted">&mdash;</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($y->is_current)
                                            <span class="badge bg-success-subtle text-success rounded-pill">Active</span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary rounded-pill">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="pe-2">
                                        @unless($y->is_current)
                                        <form method="POST" action="{{ route('admin.academic-years.activate', $y->id) }}" class="d-inline" onsubmit="return confirm('Make {{ $y->label }} the active academic year? This will deactivate the current one.')">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="btn btn-outline-success btn-sm rounded-sm" style="font-size: 0.65rem; padding: 0.15rem 0.4rem;">
                                                <i class="ti ti-power"></i> Activate
                                            </button>
                                        </form>
                                        @endunless
                                        <a href="{{ route('admin.academic-years.edit', $y->id) }}" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.65rem; padding: 0.15rem 0.4rem;">
                                            <i class="ti ti-edit"></i>
                                        </a>
                                        @unless($y->is_current)
                                        <form method="POST" action="{{ route('admin.academic-years.destroy', $y->id) }}" class="d-inline" onsubmit="return confirm('Delete {{ $y->label }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm rounded-sm" style="font-size: 0.65rem; padding: 0.15rem 0.4rem;">
                                                <i class="ti ti-trash"></i>
                                            </button>
                                        </form>
                                        @endunless
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-3">No academic years yet.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer py-1 px-2">
                    <x-pagination :items="$academicYears" />
                </div>
            </div>
        </div>

        <!-- Info Column -->
        <div class="col-lg-4">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-info-circle text-info me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Information</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <x-alert-light class="mb-2 py-1 px-2 rounded-sm">
                        <div class="d-flex align-items-start gap-1">
                            <i class="ti ti-link text-primary mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">The Leave Year IS the Academic Year</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">Leave balances, the badge shown throughout the system, and the login screen all read from whichever academic year is marked Active below.</small>
                            </div>
                        </div>
                    </x-alert-light>

                    <x-alert-light class="mb-2 py-1 px-2 rounded-sm">
                        <div class="d-flex align-items-start gap-1">
                            <i class="ti ti-power text-success mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">Only One Active At A Time</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">Activating a year immediately deactivates whichever one was active before - this is a deliberate, explicit admin action, not something the system does on its own.</small>
                            </div>
                        </div>
                    </x-alert-light>

                    <x-alert-light class="mb-2 py-1 px-2 rounded-sm">
                        <div class="d-flex align-items-start gap-1">
                            <i class="ti ti-calendar-event text-warning mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">Switching Semesters</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">Use the dropdown on the active row to move between Semester I and II - it takes effect immediately, without needing to edit the whole year.</small>
                            </div>
                        </div>
                    </x-alert-light>

                    <x-alert-light class="mb-0 py-1 px-2 rounded-sm">
                        <div class="d-flex align-items-start gap-1">
                            <i class="ti ti-trash-off text-danger mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">The Active Year Can't Be Deleted</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">Activate a different year first if you need to remove one - this protects the leave-year window from disappearing out from under active leave requests.</small>
                            </div>
                        </div>
                    </x-alert-light>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
