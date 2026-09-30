@php
    $old = fn ($field, $default = null) => old($field, $member->$field ?? $default);
@endphp

<div class="row g-2">
    <div class="col-md-8">
        <div class="mb-2">
            <label class="form-label" style="font-size: 0.7rem;">Name <span class="text-danger">*</span></label>
            <input type="text" name="name" value="{{ $old('name') }}" class="form-control form-control-sm rounded-sm @error('name') is-invalid @enderror" style="font-size: 0.75rem;" required>
            @error('name')<div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>@enderror
        </div>

        <div class="mb-2">
            <label class="form-label" style="font-size: 0.7rem;">Role <span class="text-danger">*</span></label>
            <input type="text" name="role" value="{{ $old('role') }}" placeholder="e.g. Backend Developer" class="form-control form-control-sm rounded-sm @error('role') is-invalid @enderror" style="font-size: 0.75rem;" required>
            @error('role')<div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>@enderror
        </div>

        <div class="mb-2">
            <label class="form-label" style="font-size: 0.7rem;">Short Bio (optional)</label>
            <textarea name="bio" rows="3" class="form-control form-control-sm rounded-sm @error('bio') is-invalid @enderror" style="font-size: 0.75rem;">{{ $old('bio') }}</textarea>
            @error('bio')<div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>@enderror
        </div>

        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" role="switch" name="is_active" value="1" id="is_active" {{ $old('is_active', true) ? 'checked' : '' }}>
            <label class="form-check-label" for="is_active" style="font-size: 0.7rem;">Active (visible on the landing page)</label>
        </div>
    </div>
    <div class="col-md-4">
        <label class="form-label" style="font-size: 0.7rem;">Photo</label>
        @if(($member->photo_url ?? null))
            <div class="mb-2">
                <img src="{{ $member->photo_url }}" alt="{{ $member->name }}" style="width: 100%; aspect-ratio: 1; object-fit: cover; border-radius: 8px;">
            </div>
        @endif
        <input type="file" name="photo" accept="image/*" class="form-control form-control-sm rounded-sm @error('photo') is-invalid @enderror" style="font-size: 0.75rem;">
        <div class="text-muted mt-1" style="font-size: 0.62rem;">Square photos work best. Leave empty to keep the current photo.</div>
        @error('photo')<div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>@enderror
    </div>
</div>
