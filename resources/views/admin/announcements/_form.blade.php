@php
    $old = fn ($field, $default = null) => old($field, $announcement->$field ?? $default);
@endphp

<div class="mb-2">
    <label class="form-label" style="font-size: 0.7rem;">Title <span class="text-danger">*</span></label>
    <input type="text" id="announcement-title" name="title" value="{{ $old('title') }}" class="form-control form-control-sm rounded-sm @error('title') is-invalid @enderror" style="font-size: 0.75rem;" required>
    @error('title')<div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>@enderror
</div>

<div class="mb-2">
    <label class="form-label" style="font-size: 0.7rem;">Body <span class="text-danger">*</span></label>
    <textarea id="announcement-body" name="body" rows="4" class="form-control form-control-sm rounded-sm @error('body') is-invalid @enderror" style="font-size: 0.75rem;" required>{{ $old('body') }}</textarea>
    @error('body')<div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>@enderror
</div>

<div class="row g-2 mb-2">
    <div class="col-md-4">
        <label class="form-label" style="font-size: 0.7rem;">Category</label>
        <select id="announcement-category" name="category" class="form-select form-select-sm rounded-sm" style="font-size: 0.75rem;">
            @foreach($categories as $c)
                <option value="{{ $c }}" {{ $old('category', 'general') == $c ? 'selected' : '' }}>{{ ucfirst($c) }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label" style="font-size: 0.7rem;">Show From (optional)</label>
        <input type="date" name="starts_at" value="{{ $old('starts_at') ? \Carbon\Carbon::parse($old('starts_at'))->format('Y-m-d') : '' }}" class="form-control form-control-sm rounded-sm" style="font-size: 0.75rem;">
    </div>
    <div class="col-md-4">
        <label class="form-label" style="font-size: 0.7rem;">Show Until (optional)</label>
        <input type="date" name="expires_at" value="{{ $old('expires_at') ? \Carbon\Carbon::parse($old('expires_at'))->format('Y-m-d') : '' }}" class="form-control form-control-sm rounded-sm @error('expires_at') is-invalid @enderror" style="font-size: 0.75rem;">
        @error('expires_at')<div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>@enderror
    </div>
</div>

<div class="d-flex gap-3">
    <div class="form-check form-switch">
        <input class="form-check-input" type="checkbox" role="switch" name="is_active" value="1" id="is_active" {{ $old('is_active', true) ? 'checked' : '' }}>
        <label class="form-check-label" for="is_active" style="font-size: 0.7rem;">Active (visible on landing page)</label>
    </div>
    <div class="form-check form-switch">
        <input class="form-check-input" type="checkbox" role="switch" name="is_pinned" value="1" id="is_pinned" {{ $old('is_pinned') ? 'checked' : '' }}>
        <label class="form-check-label" for="is_pinned" style="font-size: 0.7rem;">Pinned (shown first)</label>
    </div>
</div>
