@php
    $editMode = isset($policy);
@endphp

<div class="row g-2">
    <div class="col-md-4 mb-3">
        <label class="form-label" style="font-size: 0.65rem;">Policy Code</label>
        <input type="text" name="code" value="{{ old('code', $policy->code ?? '') }}" class="form-control form-control-sm rounded-sm @error('code') is-invalid @enderror" placeholder="e.g. HANDBOOK-2026" required>
        @error('code')<div class="invalid-feedback" style="font-size:0.6rem;">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-8 mb-3">
        <label class="form-label" style="font-size: 0.65rem;">Title</label>
        <input type="text" name="title" value="{{ old('title', $policy->title ?? '') }}" class="form-control form-control-sm rounded-sm @error('title') is-invalid @enderror" placeholder="Policy title" required>
        @error('title')<div class="invalid-feedback" style="font-size:0.6rem;">{{ $message }}</div>@enderror
    </div>
</div>

<div class="row g-2">
    <div class="col-md-4 mb-3">
        <label class="form-label" style="font-size: 0.65rem;">Category</label>
        <select name="category" class="form-select form-select-sm rounded-sm @error('category') is-invalid @enderror" required>
            @foreach(App\Models\Policy::CATEGORIES as $key => $label)
            <option value="{{ $key }}" {{ old('category', $policy->category ?? '') == $key ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        @error('category')<div class="invalid-feedback" style="font-size:0.6rem;">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-3 mb-3">
        <label class="form-label" style="font-size: 0.65rem;">Scope</label>
        <select name="scope" class="form-select form-select-sm rounded-sm @error('scope') is-invalid @enderror" required>
            @foreach(App\Models\Policy::SCOPES as $key => $label)
            <option value="{{ $key }}" {{ old('scope', $policy->scope ?? 'university-wide') == $key ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        @error('scope')<div class="invalid-feedback" style="font-size:0.6rem;">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-2 mb-3">
        <label class="form-label" style="font-size: 0.65rem;">Scope ID</label>
        <input type="number" name="scope_id" value="{{ old('scope_id', $policy->scope_id ?? '') }}" class="form-control form-control-sm rounded-sm @error('scope_id') is-invalid @enderror" placeholder="Optional">
        @error('scope_id')<div class="invalid-feedback" style="font-size:0.6rem;">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-3 mb-3">
        <label class="form-label" style="font-size: 0.65rem;">Status</label>
        @if($editMode && !in_array($policy->status, [App\Models\Policy::STATUS_DRAFT, App\Models\Policy::STATUS_PENDING]))
            <input type="text" class="form-control form-control-sm rounded-sm" value="{{ ucfirst($policy->status) }}" disabled>
            <input type="hidden" name="status" value="{{ $policy->status }}">
        @else
            <select name="status" class="form-select form-select-sm rounded-sm @error('status') is-invalid @enderror">
                @foreach(App\Models\Policy::STATUSES as $key => $label)
                    @if(in_array($key, [App\Models\Policy::STATUS_DRAFT, App\Models\Policy::STATUS_PENDING]))
                    <option value="{{ $key }}" {{ old('status', $policy->status ?? App\Models\Policy::STATUS_DRAFT) == $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endif
                @endforeach
            </select>
            @error('status')<div class="invalid-feedback" style="font-size:0.6rem;">{{ $message }}</div>@enderror
        @endif
    </div>
</div>

<div class="row g-2">
    <div class="col-md-6 mb-3">
        <label class="form-label" style="font-size: 0.65rem;">Effective Date</label>
        <input type="date" name="effective_date" value="{{ old('effective_date', optional($policy->effective_date ?? null)->format('Y-m-d')) }}" class="form-control form-control-sm rounded-sm @error('effective_date') is-invalid @enderror">
        @error('effective_date')<div class="invalid-feedback" style="font-size:0.6rem;">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label" style="font-size: 0.65rem;">Review Due Date</label>
        <input type="date" name="review_due_date" value="{{ old('review_due_date', optional($policy->review_due_date ?? null)->format('Y-m-d')) }}" class="form-control form-control-sm rounded-sm @error('review_due_date') is-invalid @enderror">
        @error('review_due_date')<div class="invalid-feedback" style="font-size:0.6rem;">{{ $message }}</div>@enderror
    </div>
</div>

<div class="mb-3">
    <label class="form-label" style="font-size: 0.65rem;">Description</label>
    <textarea name="description" rows="2" class="form-control form-control-sm rounded-sm @error('description') is-invalid @enderror" placeholder="Short summary of the policy">{{ old('description', $policy->description ?? '') }}</textarea>
    @error('description')<div class="invalid-feedback" style="font-size:0.6rem;">{{ $message }}</div>@enderror
</div>

<div class="mb-3">
    <label class="form-label" style="font-size: 0.65rem;">Content</label>
    <textarea name="content" rows="12" class="form-control form-control-sm rounded-sm @error('content') is-invalid @enderror" placeholder="Full policy text / HTML content">{{ old('content', $policy->content ?? '') }}</textarea>
    @error('content')<div class="invalid-feedback" style="font-size:0.6rem;">{{ $message }}</div>@enderror
</div>

<div class="card rounded-sm mb-3">
    <div class="card-header py-1 px-2 bg-light d-flex justify-content-between align-items-center">
        <span style="font-size: 0.7rem; font-weight: 600;">Numbered Sections / Clauses</span>
        <button type="button" id="add-section" class="btn btn-outline-primary btn-sm rounded-sm" style="font-size: 0.65rem;">+ Add Section</button>
    </div>
    <div class="card-body p-2">
        <div id="sections-container">
            @php
                $sections = old('sections', $policy->sections ?? []);
            @endphp
            @forelse($sections as $index => $section)
            <div class="section-row border rounded-sm p-2 mb-2 bg-white" data-index="{{ $index }}">
                <div class="row g-2">
                    <div class="col-md-2">
                        <label class="form-label" style="font-size: 0.6rem;">Number</label>
                        <input type="text" name="sections[{{ $index }}][number]" value="{{ $section['number'] ?? '' }}" class="form-control form-control-sm rounded-sm" placeholder="1.0">
                    </div>
                    <div class="col-md-9">
                        <label class="form-label" style="font-size: 0.6rem;">Title</label>
                        <input type="text" name="sections[{{ $index }}][title]" value="{{ $section['title'] ?? '' }}" class="form-control form-control-sm rounded-sm" placeholder="Section title">
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                        <button type="button" class="btn btn-outline-danger btn-sm rounded-sm remove-section w-100" style="font-size: 0.6rem;">X</button>
                    </div>
                </div>
                <div class="mt-2">
                    <label class="form-label" style="font-size: 0.6rem;">Content</label>
                    <textarea name="sections[{{ $index }}][content]" rows="3" class="form-control form-control-sm rounded-sm" placeholder="Section content">{{ $section['content'] ?? '' }}</textarea>
                </div>
            </div>
            @empty
            <p class="text-muted text-center mb-0" style="font-size: 0.65rem;" id="no-sections">No sections added yet.</p>
            @endforelse
        </div>
    </div>
</div>

<template id="section-template">
    <div class="section-row border rounded-sm p-2 mb-2 bg-white" data-index="__INDEX__">
        <div class="row g-2">
            <div class="col-md-2">
                <label class="form-label" style="font-size: 0.6rem;">Number</label>
                <input type="text" name="sections[__INDEX__][number]" class="form-control form-control-sm rounded-sm" placeholder="1.0">
            </div>
            <div class="col-md-9">
                <label class="form-label" style="font-size: 0.6rem;">Title</label>
                <input type="text" name="sections[__INDEX__][title]" class="form-control form-control-sm rounded-sm" placeholder="Section title">
            </div>
            <div class="col-md-1 d-flex align-items-end">
                <button type="button" class="btn btn-outline-danger btn-sm rounded-sm remove-section w-100" style="font-size: 0.6rem;">X</button>
            </div>
        </div>
        <div class="mt-2">
            <label class="form-label" style="font-size: 0.6rem;">Content</label>
            <textarea name="sections[__INDEX__][content]" rows="3" class="form-control form-control-sm rounded-sm" placeholder="Section content"></textarea>
        </div>
    </div>
</template>

<script>
(function() {
    const container = document.getElementById('sections-container');
    const template = document.getElementById('section-template');
    const noSections = document.getElementById('no-sections');

    function reindex() {
        const rows = container.querySelectorAll('.section-row');
        rows.forEach((row, i) => {
            row.dataset.index = i;
            row.querySelectorAll('input, textarea').forEach(el => {
                const name = el.getAttribute('name');
                if (name) {
                    el.setAttribute('name', name.replace(/sections\[\d+\]/, 'sections[' + i + ']'));
                }
            });
        });
        if (noSections) noSections.style.display = rows.length ? 'none' : 'block';
    }

    document.getElementById('add-section').addEventListener('click', function() {
        if (noSections) noSections.style.display = 'none';
        const html = template.innerHTML.replace(/__INDEX__/g, container.querySelectorAll('.section-row').length);
        const div = document.createElement('div');
        div.innerHTML = html.trim();
        container.appendChild(div.firstChild);
    });

    container.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-section')) {
            e.target.closest('.section-row').remove();
            reindex();
        }
    });
})();
</script>

<div class="mb-3">
    <label class="form-label" style="font-size: 0.65rem;">Policy Document (PDF/DOC)</label>
    <input type="file" name="document" class="form-control form-control-sm rounded-sm @error('document') is-invalid @enderror" accept=".pdf,.doc,.docx">
    @error('document')<div class="invalid-feedback" style="font-size:0.6rem;">{{ $message }}</div>@enderror
    @if($editMode && $policy->document_path)
    <div class="mt-1 form-check">
        <input class="form-check-input" type="checkbox" name="remove_document" id="remove_document" value="1">
        <label class="form-check-label" for="remove_document" style="font-size:0.65rem;">Remove current document: <a href="{{ asset('storage/'.$policy->document_path) }}" target="_blank">View file</a></label>
    </div>
    @endif
</div>
