@php
    $old = fn ($field, $default = null) => old($field, $academicYear->$field ?? $default);
    $oldDate = fn ($field, $default = null) => old($field)
        ? old($field)
        : (isset($academicYear) && $academicYear->$field ? $academicYear->$field->format('Y-m-d') : $default);
@endphp

<div class="row g-2 mb-2">
    <div class="col-md-4">
        <label class="form-label" style="font-size: 0.7rem;">Label <span class="text-danger">*</span></label>
        <input type="text" name="label" value="{{ $old('label') }}" placeholder="e.g. 2026/2027" class="form-control form-control-sm rounded-sm @error('label') is-invalid @enderror" style="font-size: 0.75rem;" required>
        @error('label')<div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label" style="font-size: 0.7rem;">Academic Year Starts <span class="text-danger">*</span></label>
        <input type="date" name="start_date" value="{{ $oldDate('start_date') }}" class="form-control form-control-sm rounded-sm @error('start_date') is-invalid @enderror" style="font-size: 0.75rem;" required>
        @error('start_date')<div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label" style="font-size: 0.7rem;">Academic Year Ends <span class="text-danger">*</span></label>
        <input type="date" name="end_date" value="{{ $oldDate('end_date') }}" class="form-control form-control-sm rounded-sm @error('end_date') is-invalid @enderror" style="font-size: 0.75rem;" required>
        @error('end_date')<div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>@enderror
    </div>
</div>

<div class="card border-0 bg-light rounded-sm mb-2">
    <div class="card-body p-2">
        <div class="fw-semibold mb-2" style="font-size: 0.75rem;">Semester I</div>
        <div class="row g-2">
            <div class="col-md-4">
                <label class="form-label" style="font-size: 0.68rem;">Name</label>
                <input type="text" name="semester_1_name" value="{{ $old('semester_1_name', 'Semester I') }}" class="form-control form-control-sm rounded-sm" style="font-size: 0.75rem;" required>
            </div>
            <div class="col-md-4">
                <label class="form-label" style="font-size: 0.68rem;">Starts</label>
                <input type="date" name="semester_1_start" value="{{ $oldDate('semester_1_start') }}" class="form-control form-control-sm rounded-sm @error('semester_1_start') is-invalid @enderror" style="font-size: 0.75rem;" required>
                @error('semester_1_start')<div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label class="form-label" style="font-size: 0.68rem;">Ends</label>
                <input type="date" name="semester_1_end" value="{{ $oldDate('semester_1_end') }}" class="form-control form-control-sm rounded-sm @error('semester_1_end') is-invalid @enderror" style="font-size: 0.75rem;" required>
                @error('semester_1_end')<div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>@enderror
            </div>
        </div>
    </div>
</div>

<div class="card border-0 bg-light rounded-sm mb-2">
    <div class="card-body p-2">
        <div class="fw-semibold mb-2" style="font-size: 0.75rem;">Semester II</div>
        <div class="row g-2">
            <div class="col-md-4">
                <label class="form-label" style="font-size: 0.68rem;">Name</label>
                <input type="text" name="semester_2_name" value="{{ $old('semester_2_name', 'Semester II') }}" class="form-control form-control-sm rounded-sm" style="font-size: 0.75rem;" required>
            </div>
            <div class="col-md-4">
                <label class="form-label" style="font-size: 0.68rem;">Starts</label>
                <input type="date" name="semester_2_start" value="{{ $oldDate('semester_2_start') }}" class="form-control form-control-sm rounded-sm @error('semester_2_start') is-invalid @enderror" style="font-size: 0.75rem;" required>
                @error('semester_2_start')<div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label class="form-label" style="font-size: 0.68rem;">Ends</label>
                <input type="date" name="semester_2_end" value="{{ $oldDate('semester_2_end') }}" class="form-control form-control-sm rounded-sm @error('semester_2_end') is-invalid @enderror" style="font-size: 0.75rem;" required>
                @error('semester_2_end')<div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>@enderror
            </div>
        </div>
    </div>
</div>
