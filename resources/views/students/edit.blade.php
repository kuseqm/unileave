@extends('layouts.master')

@section('title', 'Edit - '.$student->formatted_name)

@section('content')
<div class="container-fluid px-2">

    <x-page-header title="{{ $student->is_graduated ? 'EDIT GRADUATE RECORD' : 'EDIT STUDENT RECORD' }}" description="{{ $student->registration_number }}">
        <a href="{{ route('students.show', $student->id) }}" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
            <i class="ti ti-arrow-left me-1"></i> Back to Record
        </a>
    </x-page-header>

    <x-alert-danger />

    @php
        // Every graduation-specific field below (Campus/Programme/Year,
        // Degree, Classification/GPA, dates, Certificate Number) is filled
        // from the ONE graduation being edited - never from the Student's
        // own cached "latest" fields, which is exactly what let an edit
        // meant for one graduation silently land on a different one.
        $source = $editing ?? $student;
    @endphp

    @if($graduations->count() > 1)
    <x-alert-light class="mb-2 py-2 px-2 rounded-sm">
        <div class="d-flex align-items-start gap-1">
            <i class="ti ti-versions text-warning mt-0" style="font-size: 0.9rem;"></i>
            <div>
                <strong class="d-block" style="font-size: 0.68rem;">This graduate has {{ $graduations->count() }} recorded graduations</strong>
                <small class="text-muted d-block mb-1" style="font-size: 0.62rem;">Everything below applies to the ONE you pick here - it will never change a different graduation.</small>
                <div class="d-flex flex-wrap gap-1">
                    @foreach($graduations as $g)
                        <a href="{{ route('students.edit', ['student' => $student->id, 'graduation' => $g->id]) }}"
                           class="btn btn-sm rounded-sm {{ $editing->id === $g->id ? 'btn-primary' : 'btn-outline-secondary' }}"
                           style="font-size: 0.62rem; padding: 0.15rem 0.5rem;">
                            {{ $g->graduation_year }} — {{ $g->program->code ?? $g->program->name ?? 'Programme' }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </x-alert-light>
    @endif

    <div class="row g-2">
        <div class="col-lg-8">
            <form action="{{ route('students.update', $student->id) }}" method="POST">
                @csrf
                @method('PUT')
                @if($editing)<input type="hidden" name="graduation_id" value="{{ $editing->id }}">@endif

                <div class="card rounded-sm mb-3">
                    <div class="card-header py-1 px-2 bg-light d-flex align-items-center">
                        <i class="ti ti-id text-primary me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Identity</span>
                    </div>
                    <div class="card-body p-3">
                        <div class="row g-2">
                            <div class="col-md-4">
                                <label class="form-label" style="font-size: 0.65rem;">Surname <span class="text-danger">*</span></label>
                                <input type="text" name="last_name" class="form-control form-control-sm rounded-sm @error('last_name') is-invalid @enderror" value="{{ old('last_name', $source->last_name) }}" required>
                                @error('last_name')<div class="invalid-feedback" style="font-size: 0.6rem;">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" style="font-size: 0.65rem;">First Name <span class="text-danger">*</span></label>
                                <input type="text" name="first_name" class="form-control form-control-sm rounded-sm @error('first_name') is-invalid @enderror" value="{{ old('first_name', $source->first_name) }}" required>
                                @error('first_name')<div class="invalid-feedback" style="font-size: 0.6rem;">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" style="font-size: 0.65rem;">Other Names</label>
                                <input type="text" name="middle_name" class="form-control form-control-sm rounded-sm @error('middle_name') is-invalid @enderror" value="{{ old('middle_name', $source->middle_name) }}">
                                @error('middle_name')<div class="invalid-feedback" style="font-size: 0.6rem;">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" style="font-size: 0.65rem;">Gender</label>
                                <select name="gender" class="form-select form-select-sm rounded-sm @error('gender') is-invalid @enderror">
                                    <option value="">—</option>
                                    <option value="M" @selected(old('gender', $source->gender) === 'M')>Male</option>
                                    <option value="F" @selected(old('gender', $source->gender) === 'F')>Female</option>
                                </select>
                                @error('gender')<div class="invalid-feedback" style="font-size: 0.6rem;">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card rounded-sm mb-3">
                    <div class="card-header py-1 px-2 bg-light d-flex align-items-center">
                        <i class="ti ti-school text-primary me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Campus, Programme &amp; Graduation Year</span>
                    </div>
                    <div class="card-body p-3">
                        <p class="text-muted mb-2" style="font-size: 0.65rem;">Correct these if the roster was uploaded with the wrong choice. Duration, study period and ceremony date follow automatically, unless you have typed your own values for them.</p>
                        <div class="row g-2">
                            <div class="col-md-6">
                                <x-scrollable-dropdown name="campus_id" id="campus_id" label="Campus" icon="ti-map-pin" :required="true" placeholder="Select Campus" :value="$source->campus_id" :options="$campusOptions" />
                            </div>
                            <div class="col-md-6">
                                <x-scrollable-dropdown name="graduation_year" id="graduation_year" label="Graduation Year" icon="ti-calendar" :required="true" placeholder="Select Year" :value="$source->graduation_year" :options="$graduationYearOptions" />
                            </div>
                            <div class="col-12">
                                <x-scrollable-dropdown name="program_id" id="program_id" label="Programme" icon="ti-book" :required="true" placeholder="Select Programme" :value="$source->program_id" :options="$programmeOptions" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card rounded-sm mb-3">
                    <div class="card-header py-1 px-2 bg-light d-flex align-items-center">
                        <i class="ti ti-certificate text-primary me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Degree &amp; Academic Result</span>
                    </div>
                    <div class="card-body p-3">
                        <p class="text-muted mb-2" style="font-size: 0.65rem;">The degree title and abbreviation come from the Programme automatically - type them below only if this graduate's certificate says something different. Classification and GPA are what the roster import doesn't collect.</p>
                        <div class="row g-2 mb-2">
                            <div class="col-md-8">
                                <label class="form-label" style="font-size: 0.65rem;">Degree Name</label>
                                <input type="text" name="degree_name" class="form-control form-control-sm rounded-sm @error('degree_name') is-invalid @enderror" value="{{ old('degree_name', $source->degree_name) }}" placeholder="{{ $source->program?->degree_name ?? 'e.g. Bachelor of Science in Information Technology' }}">
                                @error('degree_name')<div class="invalid-feedback" style="font-size: 0.6rem;">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" style="font-size: 0.65rem;">Abbreviation</label>
                                <input type="text" name="degree_abbreviation" class="form-control form-control-sm rounded-sm @error('degree_abbreviation') is-invalid @enderror" value="{{ old('degree_abbreviation', $source->degree_abbreviation) }}" placeholder="{{ $source->program?->degree_abbreviation ?? 'e.g. BScIT' }}">
                                @error('degree_abbreviation')<div class="invalid-feedback" style="font-size: 0.6rem;">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="row g-2">
                            <div class="col-md-4">
                                <label class="form-label" style="font-size: 0.65rem;">Classification</label>
                                <input type="text" name="classification" class="form-control form-control-sm rounded-sm @error('classification') is-invalid @enderror" value="{{ old('classification', $source->classification) }}" placeholder="e.g. Upper Second Class Honours">
                                @error('classification')<div class="invalid-feedback" style="font-size: 0.6rem;">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" style="font-size: 0.65rem;">GPA</label>
                                <input type="number" step="0.01" min="0" max="5" name="gpa" class="form-control form-control-sm rounded-sm @error('gpa') is-invalid @enderror" value="{{ old('gpa', $source->gpa) }}" placeholder="e.g. 3.85">
                                @error('gpa')<div class="invalid-feedback" style="font-size: 0.6rem;">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" style="font-size: 0.65rem;">Programme Duration (years)</label>
                                <input type="number" min="1" max="10" name="programme_duration_years" class="form-control form-control-sm rounded-sm @error('programme_duration_years') is-invalid @enderror" value="{{ old('programme_duration_years', $source->programme_duration_years) }}" placeholder="e.g. 3">
                                @error('programme_duration_years')<div class="invalid-feedback" style="font-size: 0.6rem;">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card rounded-sm mb-3">
                    <div class="card-header py-1 px-2 bg-light d-flex align-items-center">
                        <i class="ti ti-calendar text-primary me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Dates &amp; Identifiers (corrections)</span>
                    </div>
                    <div class="card-body p-3">
                        <p class="text-muted mb-2" style="font-size: 0.65rem;">Usually already correct from the roster import - only change these if something looks wrong.</p>
                        <div class="row g-2 mb-2">
                            <div class="col-md-4">
                                <label class="form-label" style="font-size: 0.65rem;">Start Academic Year</label>
                                <input type="text" name="programme_start_academic_year" class="form-control form-control-sm rounded-sm @error('programme_start_academic_year') is-invalid @enderror" value="{{ old('programme_start_academic_year', $source->programme_start_academic_year) }}" placeholder="e.g. 2022/2023">
                                @error('programme_start_academic_year')<div class="invalid-feedback" style="font-size: 0.6rem;">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" style="font-size: 0.65rem;">End Academic Year</label>
                                <input type="text" name="programme_end_academic_year" class="form-control form-control-sm rounded-sm @error('programme_end_academic_year') is-invalid @enderror" value="{{ old('programme_end_academic_year', $source->programme_end_academic_year) }}" placeholder="e.g. 2024/2025">
                                @error('programme_end_academic_year')<div class="invalid-feedback" style="font-size: 0.6rem;">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" style="font-size: 0.65rem;">Graduation Date</label>
                                <input type="date" name="graduation_date" class="form-control form-control-sm rounded-sm @error('graduation_date') is-invalid @enderror" value="{{ old('graduation_date', optional($source->graduation_date)->format('Y-m-d')) }}">
                                @error('graduation_date')<div class="invalid-feedback" style="font-size: 0.6rem;">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="mb-0">
                            <label class="form-label" style="font-size: 0.65rem;">Certificate Number</label>
                            <input type="text" name="certificate_barcode" class="form-control form-control-sm rounded-sm @error('certificate_barcode') is-invalid @enderror" value="{{ old('certificate_barcode', $source->certificate_barcode) }}" placeholder="Exact code printed on the physical certificate">
                            @error('certificate_barcode')<div class="invalid-feedback" style="font-size: 0.6rem;">{{ $message }}</div>@enderror
                            <small class="text-muted" style="font-size: 0.6rem;">Used later to match a scanned certificate back to this graduate.</small>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem;">
                    <i class="ti ti-device-floppy me-1"></i> Save Changes
                </button>
            </form>
        </div>

        <div class="col-lg-4">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-info-circle text-info me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Fixed</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2" style="font-size: 0.7rem;">
                    <div class="mb-0">
                        <div class="text-muted" style="font-size: 0.65rem;">Registration Number</div>
                        <div class="fw-semibold"><code>{{ $student->registration_number }}</code></div>
                    </div>
                    <hr>
                    <small class="text-muted">The Registration Number identifies this graduate and cannot be changed here. Everything else on this page can be corrected.</small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
