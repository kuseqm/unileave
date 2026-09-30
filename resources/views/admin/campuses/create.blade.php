@extends('layouts.master')

@section('title', 'Add Campus')

@section('content')
<div class="container-fluid px-2">
    
    <!-- Breadcrumb -->
    <x-breadcrumb :items="[
        ['name' => 'Campuses', 'url' => route('admin.campuses.index')],
        ['name' => 'Add Campus', 'url' => '#']
    ]" />
    
    <!-- Page Header -->
    <x-page-header title="ADD CAMPUS" description="Create a new campus location under a university">
        <a href="{{ route('admin.campuses.index') }}" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
            <i class="ti ti-arrow-left me-1"></i> Back
        </a>
    </x-page-header>
    
    <div class="row g-2">
        <!-- Main Form -->
        <div class="col-lg-7">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-map-pin text-primary me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Campus Information</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <form action="{{ route('admin.campuses.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-1">
                            <label class="form-label" style="font-size: 0.65rem;">University <span class="text-danger">*</span></label>
                            <select name="university_id" class="form-select form-select-sm rounded-sm @error('university_id') is-invalid @enderror" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;" required>
                                <option value="">Select University</option>
                                @foreach($universities as $uni)
                                <option value="{{ $uni->id }}" {{ $selectedUniversity == $uni->id ? 'selected' : '' }}>{{ $uni->name }}</option>
                                @endforeach
                            </select>
                            @error('university_id')
                                <div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="row g-1">
                            <div class="col-md-6 mb-1">
                                <label class="form-label" style="font-size: 0.65rem;">Campus Name <span class="text-danger">*</span></label>
                                <input type="text" 
                                       name="name" 
                                       class="form-control form-control-sm rounded-sm @error('name') is-invalid @enderror" 
                                       value="{{ old('name') }}" 
                                       placeholder="e.g., Main Campus"
                                       style="font-size: 0.7rem; padding: 0.2rem 0.3rem;"
                                       required>
                                @error('name')
                                    <div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-1">
                                <label class="form-label" style="font-size: 0.65rem;">Campus Code <span class="text-danger">*</span></label>
                                <input type="text" 
                                       name="code" 
                                       class="form-control form-control-sm rounded-sm @error('code') is-invalid @enderror" 
                                       value="{{ old('code') }}" 
                                       placeholder="e.g., MAIN"
                                       style="font-size: 0.7rem; padding: 0.2rem 0.3rem;"
                                       required>
                                @error('code')
                                    <div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>
                                @enderror
                                <small class="text-muted" style="font-size: 0.6rem;">Short code for the campus</small>
                            </div>
                        </div>
                        
                        <div class="row g-1">
                            <div class="col-md-6 mb-1">
                                <label class="form-label" style="font-size: 0.65rem;">GPS Latitude</label>
                                <input type="text" 
                                       name="gps_lat" 
                                       class="form-control form-control-sm rounded-sm @error('gps_lat') is-invalid @enderror" 
                                       value="{{ old('gps_lat') }}" 
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
                                       value="{{ old('gps_lng') }}" 
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
                                      style="font-size: 0.7rem; padding: 0.2rem 0.3rem;"
                                      placeholder="Full address of the campus">{{ old('address') }}</textarea>
                            @error('address')
                                <div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-1">
                            <label class="form-label" style="font-size: 0.65rem;">Status</label>
                            <select name="status" class="form-select form-select-sm rounded-sm @error('status') is-invalid @enderror" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;">
                                <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="d-flex justify-content-end gap-1 mt-2">
                            <a href="{{ route('admin.campuses.index') }}" class="btn btn-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.25rem 0.5rem;">Cancel</a>
                            <button type="submit" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.25rem 0.5rem;">
                                <i class="ti ti-device-floppy me-1"></i> Create Campus
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Right Column: Info Card -->
        <div class="col-lg-5">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-info-circle text-info me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Campus Information</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <x-alert-light class="mb-2 py-1 px-2 rounded-sm">

                        <div class="d-flex align-items-start gap-1">
                            <i class="ti ti-building text-primary mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">What is a Campus?</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">A campus is a physical location where university activities take place. A university can have multiple campuses.</small>
                            </div>
                        
</x-alert-light>
                    </div>
                    
                    <x-alert-light class="mb-0 py-1 px-2 rounded-sm">

                        <div class="d-flex align-items-start gap-1">
                            <i class="ti ti-tips text-warning mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">Important Notes</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">• Each campus must belong to a university<br>
                                • Campus code should be unique<br>
                                • GPS coordinates help with location<br>
                                • You can add departments after creating the campus</small>
                            </div>
                        
</x-alert-light>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Auto-generate code from name
document.querySelector('input[name="name"]')?.addEventListener('blur', function() {
    const codeField = document.querySelector('input[name="code"]');
    if (codeField && !codeField.value) {
        let code = this.value.substring(0, 4).toUpperCase().replace(/\s/g, '');
        codeField.value = code;
    }
});
</script>
@endsection