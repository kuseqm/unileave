@php
    $old = fn ($field, $default = null) => old($field, $event->$field ?? $default);
    $oldDate = $event->event_date ?? null;
@endphp

<div class="mb-2">
    <label class="form-label" style="font-size: 0.7rem;">Title <span class="text-danger">*</span></label>
    <input type="text" name="title" value="{{ $old('title') }}" class="form-control form-control-sm rounded-sm @error('title') is-invalid @enderror" style="font-size: 0.75rem;" required>
    @error('title')<div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>@enderror
</div>

<div class="mb-2">
    <label class="form-label" style="font-size: 0.7rem;">Short Description <span class="text-danger">*</span></label>
    <textarea name="description" rows="2" class="form-control form-control-sm rounded-sm @error('description') is-invalid @enderror" style="font-size: 0.75rem;" required>{{ $old('description') }}</textarea>
    <div class="text-muted mt-1" style="font-size: 0.62rem;">Shown on the calendar and as the summary on the event page.</div>
    @error('description')<div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>@enderror
</div>

<div class="mb-2">
    <label class="form-label" style="font-size: 0.7rem;">Full Details (optional)</label>
    <textarea name="body" rows="5" class="form-control form-control-sm rounded-sm @error('body') is-invalid @enderror" style="font-size: 0.75rem;">{{ $old('body') }}</textarea>
    <div class="text-muted mt-1" style="font-size: 0.62rem;">Shown on the event's own page when someone clicks through from the calendar.</div>
    @error('body')<div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>@enderror
</div>

<div class="row g-2 mb-2">
    <div class="col-md-4">
        <label class="form-label" style="font-size: 0.7rem;">Date <span class="text-danger">*</span></label>
        <input type="date" name="event_date" value="{{ $oldDate ? \Carbon\Carbon::parse($oldDate)->format('Y-m-d') : old('event_date') }}" class="form-control form-control-sm rounded-sm @error('event_date') is-invalid @enderror" style="font-size: 0.75rem;" required>
        @error('event_date')<div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label" style="font-size: 0.7rem;">Start Time (optional)</label>
        <input type="text" name="start_time" value="{{ $old('start_time') }}" placeholder="e.g. 10:00 AM" class="form-control form-control-sm rounded-sm" style="font-size: 0.75rem;">
    </div>
    <div class="col-md-4">
        <label class="form-label" style="font-size: 0.7rem;">End Time (optional)</label>
        <input type="text" name="end_time" value="{{ $old('end_time') }}" placeholder="e.g. 1:00 PM" class="form-control form-control-sm rounded-sm" style="font-size: 0.75rem;">
    </div>
</div>

<div class="mb-2">
    <label class="form-label" style="font-size: 0.7rem;">Location (optional)</label>
    <input type="text" name="location" value="{{ $old('location') }}" class="form-control form-control-sm rounded-sm" style="font-size: 0.75rem;">
</div>

<div class="form-check form-switch">
    <input class="form-check-input" type="checkbox" role="switch" name="is_active" value="1" id="is_active" {{ $old('is_active', true) ? 'checked' : '' }}>
    <label class="form-check-label" for="is_active" style="font-size: 0.7rem;">Active (visible on the calendar)</label>
</div>
