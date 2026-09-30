@extends('layouts.master')

@section('title', 'Edit University')

@section('content')
<div class="container-fluid px-2">
    
    <!-- Breadcrumb -->
    <x-breadcrumb :items="[
        ['name' => 'Universities', 'url' => route('admin.universities.index')],
        ['name' => 'Edit University', 'url' => '#']
    ]" />
    
    <!-- Page Header -->
    <x-page-header title="EDIT UNIVERSITY" description="Update university information and manage associated campuses">
        <a href="{{ route('admin.universities.index') }}" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
            <i class="ti ti-arrow-left me-1"></i> Back
        </a>
    </x-page-header>
    
    <div class="row g-2">
        <!-- Main Form -->
        <div class="col-lg-7">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-building text-primary me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">University Information</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <form action="{{ route('admin.universities.update', $university->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row g-1">
                            <div class="col-md-6 mb-1">
                                <label class="form-label" style="font-size: 0.65rem;">University Name <span class="text-danger">*</span></label>
                                <input type="text" 
                                       name="name" 
                                       class="form-control form-control-sm rounded-sm @error('name') is-invalid @enderror" 
                                       value="{{ old('name', $university->name) }}" 
                                       style="font-size: 0.7rem; padding: 0.2rem 0.3rem;"
                                       required>
                                @error('name')
                                    <div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-1">
                                <label class="form-label" style="font-size: 0.65rem;">Code <span class="text-danger">*</span></label>
                                <input type="text" 
                                       name="code" 
                                       class="form-control form-control-sm rounded-sm @error('code') is-invalid @enderror" 
                                       value="{{ old('code', $university->code) }}" 
                                       style="font-size: 0.7rem; padding: 0.2rem 0.3rem;"
                                       required>
                                @error('code')
                                    <div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>
                                @enderror
                                <small class="text-muted" style="font-size: 0.6rem;">Short code/abbreviation</small>
                            </div>
                        </div>
                        
                        <div class="row g-1">
                            <div class="col-md-6 mb-1">
                                <label class="form-label" style="font-size: 0.65rem;">GPS Latitude</label>
                                <input type="text" 
                                       name="gps_lat" 
                                       class="form-control form-control-sm rounded-sm @error('gps_lat') is-invalid @enderror" 
                                       value="{{ old('gps_lat', $university->gps_lat) }}" 
                                       placeholder="e.g., -6.7800"
                                       style="font-size: 0.7rem; padding: 0.2rem 0.3rem;">
                                @error('gps_lat')
                                    <div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>
                                @enderror
                                <small class="text-muted" style="font-size: 0.6rem;">Decimal format</small>
                            </div>
                            <div class="col-md-6 mb-1">
                                <label class="form-label" style="font-size: 0.65rem;">GPS Longitude</label>
                                <input type="text" 
                                       name="gps_lng" 
                                       class="form-control form-control-sm rounded-sm @error('gps_lng') is-invalid @enderror" 
                                       value="{{ old('gps_lng', $university->gps_lng) }}" 
                                       placeholder="e.g., 39.2020"
                                       style="font-size: 0.7rem; padding: 0.2rem 0.3rem;">
                                @error('gps_lng')
                                    <div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>
                                @enderror
                                <small class="text-muted" style="font-size: 0.6rem;">Decimal format</small>
                            </div>
                        </div>
                        
                        <div class="mb-1">
                            <label class="form-label" style="font-size: 0.65rem;">Address</label>
                            <textarea name="address" 
                                      class="form-control form-control-sm rounded-sm @error('address') is-invalid @enderror" 
                                      rows="2"
                                      style="font-size: 0.7rem; padding: 0.2rem 0.3rem;">{{ old('address', $university->address) }}</textarea>
                            @error('address')
                                <div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="row g-1">
                            <div class="col-md-6 mb-1">
                                <label class="form-label" style="font-size: 0.65rem;">Phone</label>
                                <input type="text" 
                                       name="phone" 
                                       class="form-control form-control-sm rounded-sm @error('phone') is-invalid @enderror" 
                                       value="{{ old('phone', $university->phone) }}"
                                       style="font-size: 0.7rem; padding: 0.2rem 0.3rem;">
                                @error('phone')
                                    <div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-1">
                                <label class="form-label" style="font-size: 0.65rem;">Email</label>
                                <input type="email" 
                                       name="email" 
                                       class="form-control form-control-sm rounded-sm @error('email') is-invalid @enderror" 
                                       value="{{ old('email', $university->email) }}"
                                       style="font-size: 0.7rem; padding: 0.2rem 0.3rem;">
                                @error('email')
                                    <div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="mb-1">
                            <label class="form-label" style="font-size: 0.65rem;">Status</label>
                            <select name="status" class="form-select form-select-sm rounded-sm @error('status') is-invalid @enderror" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;">
                                <option value="active" {{ old('status', $university->status) == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status', $university->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>
                            @enderror
                        </div>

                        @role('super-admin')
                        <div class="mb-1 mt-2 p-2 bg-light rounded-sm">
                            <label class="form-label d-block" style="font-size: 0.65rem;">Opt-in Add-ons</label>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="enabled_addons[]" value="academic_records" id="addon_academic_records"
                                    {{ in_array('academic_records', old('enabled_addons', $university->enabled_addons ?? [])) ? 'checked' : '' }}>
                                <label class="form-check-label" for="addon_academic_records" style="font-size: 0.7rem;">
                                    Academic Records (Students / Graduation Verification)
                                </label>
                            </div>
                            <small class="text-muted" style="font-size: 0.55rem;">Only super-admin sees or changes this - not self-service for the university's own admin.</small>
                        </div>
                        @endrole

                        <div class="d-flex justify-content-end gap-1 mt-2">
                            <a href="{{ route('admin.universities.index') }}" class="btn btn-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.25rem 0.5rem;">Cancel</a>
                            <button type="submit" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.25rem 0.5rem;">
                                <i class="ti ti-device-floppy me-1"></i> Update University
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Right Column: Associated Campuses -->
        <div class="col-lg-5">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-map-pin text-primary me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Associated Campuses</span>
                    </div>
                    <a href="{{ route('admin.campuses.create') }}?university_id={{ $university->id }}" class="btn btn-sm btn-outline-primary rounded-sm" style="font-size: 0.65rem; padding: 0.2rem 0.4rem;">
                        <i class="ti ti-plus me-1"></i> Add Campus
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0" style="font-size: 0.75rem;" id="campusesTable">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-2 py-1">Campus Name</th>
                                    <th class="py-1">Code</th>
                                    <th class="py-1">Status</th>
                                    <th class="pe-2 py-1" width="50"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($university->campuses as $campus)
                                <tr data-campus-id="{{ $campus->id }}" class="clickable-row">
                                    <td class="ps-2 py-1">
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-circle bg-light me-1 d-flex align-items-center justify-content-center" style="width: 24px; height: 24px;">
                                                <i class="ti ti-map-pin" style="font-size: 0.7rem;"></i>
                                            </div>
                                            <div>
                                                <span class="fw-semibold hover-link">{{ $campus->name }}</span>
                                                @if($campus->address)
                                                <div class="text-muted" style="font-size: 0.6rem;">{{ Str::limit($campus->address, 25) }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-1">{{ $campus->code }}</td>
                                    <td class="py-1">
                                        @if($campus->status == 'active')
                                            <span class="badge bg-success-subtle text-success rounded-sm" style="font-size: 0.6rem;">Active</span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary rounded-sm" style="font-size: 0.6rem;">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="pe-2 py-1">
                                        <a href="{{ route('admin.campuses.edit', $campus->id) }}" class="btn btn-sm btn-outline-primary rounded-sm" title="Edit Campus" style="padding: 0.15rem 0.3rem;">
                                            <i class="ti ti-edit" style="font-size: 0.7rem;"></i>
                                        </a>
                                    </td>
                                <tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-3 text-muted" style="font-size: 0.7rem;">
                                        No campuses found for this university
                                        <div class="mt-1">
                                            <a href="{{ route('admin.campuses.create') }}?university_id={{ $university->id }}" class="btn btn-sm btn-primary rounded-sm" style="font-size: 0.65rem;">
                                                <i class="ti ti-plus me-1"></i> Add First Campus
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer py-1 px-2">
                    <span class="text-muted" style="font-size: 0.65rem;">{{ $university->campuses->count() }} campus(es) total</span>
                </div>
            </div>
            
            <!-- Quick Stats Card - Clickable Stats -->
            <div class="card rounded-sm mt-2">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-chart-bar text-success me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Quick Statistics</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <div class="row text-center">
                        <div class="col-4">
                            <a href="{{ route('admin.campuses.index', ['university_id' => $university->id]) }}" class="text-decoration-none">
                                <div class="fw-bold text-primary" style="font-size: 1rem;">{{ $university->campuses->count() }}</div>
                                <div class="text-muted hover-link" style="font-size: 0.6rem;">Campuses</div>
                            </a>
                        </div>
                        <div class="col-4">
                            <a href="{{ route('admin.departments.index', ['university_id' => $university->id]) }}" class="text-decoration-none">
                                <div class="fw-bold text-info" style="font-size: 1rem;">{{ $university->campuses->sum(function($c) { return $c->departments->count(); }) }}</div>
                                <div class="text-muted hover-link" style="font-size: 0.6rem;">Departments</div>
                            </a>
                        </div>
                        <div class="col-4">
                            <a href="{{ route('staff.index', ['university_id' => $university->id]) }}" class="text-decoration-none">
                                <div class="fw-bold text-success" style="font-size: 1rem;">{{ $university->staff->count() ?? 0 }}</div>
                                <div class="text-muted hover-link" style="font-size: 0.6rem;">Staff</div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Hover effects for clickable elements */
.clickable-row {
    cursor: pointer;
    transition: background 0.2s ease;
}
.clickable-row:hover {
    background-color: #F8FAFC !important;
}

.hover-link:hover {
    text-decoration: underline;
    color: #3B82F6 !important;
}

/* Action buttons container */
.d-flex.gap-1 {
    gap: 0.25rem;
}
</style>

<script>
// Make entire campus row clickable (except action buttons)
document.addEventListener('DOMContentLoaded', function() {
    const rows = document.querySelectorAll('.clickable-row');
    rows.forEach(row => {
        row.addEventListener('click', function(e) {
            // Don't navigate if clicking on action buttons
            if (e.target.closest('.btn') || e.target.closest('a')) {
                return;
            }
            const campusId = this.dataset.campusId;
            if (campusId) {
                window.location.href = '/admin/campuses/' + campusId + '/edit';
            }
        });
    });
});
</script>
@endsection