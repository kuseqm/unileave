@props([
    'name',
    'id' => null,
    'label' => 'Letter Content',
    'rows' => 5,
    'placeholder' => '',
    'helpText' => null,
    'required' => false,
    'numberFrom' => 2,
    'showLabel' => true,
    'value' => null,
    // Some letter types (e.g. English Proficiency, Recommendation) never
    // number their paragraphs in the actual rendered letter - false here
    // keeps this preview honest instead of showing numbers that won't
    // appear on the real document.
    'numbered' => true,
])

@php
    $fieldId = $id ?? Str::slug($name);
@endphp

<div class="mb-1">
    @if($showLabel)
    <label class="form-label" style="font-size: 0.65rem;">{{ $label }} @if($required)<span class="text-danger">*</span>@endif</label>
    @endif
    <div class="row g-2">
        <div class="col-md-6">
            <textarea name="{{ $name }}" id="{{ $fieldId }}" rows="{{ $rows }}" class="form-control form-control-sm rounded-sm @error($name) is-invalid @enderror" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;" placeholder="{{ $placeholder }}" {{ $required ? 'required' : '' }}>{{ old($name, $value) }}</textarea>
            @error($name)
                <div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>
            @enderror
            <small class="text-muted" style="font-size: 0.55rem;">Press Enter to start a new paragraph - each one gets numbered automatically on the right, the way it will look on the real letter.</small>
        </div>
        <div class="col-md-6">
            <label class="form-label mb-0" style="font-size: 0.6rem; color: #888;">
                <i class="ti ti-eye me-1"></i>Preview
            </label>
            <div id="{{ $fieldId }}-preview" class="border rounded-sm p-2" style="font-family: Arial, Helvetica, sans-serif; font-size: 0.68rem; line-height: 1.4; background: #fff; min-height: {{ $rows * 22 }}px; text-align: justify;"></div>
        </div>
    </div>
    @if($helpText)
        <small class="text-muted d-block" style="font-size: 0.55rem;">{{ $helpText }}</small>
    @endif
</div>

<script>
(function () {
    function initNumberedField() {
        const textarea = document.getElementById('{{ $fieldId }}');
        const preview = document.getElementById('{{ $fieldId }}-preview');
        if (!textarea || !preview || textarea.dataset.numberedFieldReady) return;
        textarea.dataset.numberedFieldReady = '1';

        const numberFrom = {{ (int) $numberFrom }};
        const numbered = {{ $numbered ? 'true' : 'false' }};
        const placeholderHtml = numbered
            ? '<span class="text-muted" style="font-style: italic;">Your paragraphs will appear here as you type, numbered automatically...</span>'
            : '<span class="text-muted" style="font-style: italic;">Your paragraphs will appear here as you type...</span>';

        function escapeHtml(str) {
            const div = document.createElement('div');
            div.textContent = str;
            return div.innerHTML;
        }

        function render() {
            const lines = textarea.value.split('\n').map(function (l) { return l.trim(); }).filter(function (l) { return l.length > 0; });

            if (lines.length === 0) {
                preview.innerHTML = placeholderHtml;
                return;
            }

            let html = '';
            lines.forEach(function (line, index) {
                if (index === 0 || !numbered) {
                    // SJUT's own convention: the first paragraph carries no
                    // number, just a first-line indent, like a typewritten
                    // letter - every paragraph after it is numbered. Letter
                    // types that never number their paragraphs skip this
                    // entirely (numbered === false).
                    html += '<p style="margin: 0 0 8px 0; text-indent: 2em;">' + escapeHtml(line) + '</p>';
                } else {
                    const num = numberFrom + (index - 1);
                    html += '<p style="margin: 0 0 8px 0;">' + num + '.&nbsp;&nbsp;&nbsp;&nbsp;' + escapeHtml(line) + '</p>';
                }
            });
            preview.innerHTML = html;
        }

        textarea.addEventListener('input', render);
        render();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initNumberedField);
    } else {
        initNumberedField();
    }
})();
</script>
