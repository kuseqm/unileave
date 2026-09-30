@props([
    'name' => 'signature_data',
    'id' => null,
    'label' => 'Signature',
    'helpText' => 'Signed live at the point of signing — draw with mouse or touch.',
    'height' => 130,
    'required' => true,
])

@php
    $padId = $id ?? Str::slug($name);
@endphp

<div class="mb-1">
    <label class="form-label" style="font-size: 0.65rem;">{{ $label }} @if($required)<span class="text-danger">*</span>@endif</label>
    <div class="row g-1">
        <div class="col-md-8">
            <canvas id="{{ $padId }}-canvas"
                    class="border rounded-sm w-100 @error($name) is-invalid @enderror"
                    style="height: {{ $height }}px; touch-action: none; background: #fff;"></canvas>
            <input type="hidden" name="{{ $name }}" id="{{ $padId }}-data">
            @error($name)
                <div class="invalid-feedback d-block" style="font-size: 0.65rem;">{{ $message }}</div>
            @enderror
            <button type="button" id="{{ $padId }}-clear" class="btn btn-outline-secondary btn-sm rounded-sm mt-1" style="font-size: 0.65rem; padding: 0.15rem 0.4rem;">
                <i class="ti ti-eraser me-1"></i> Clear
            </button>
        </div>
        <div class="col-md-4">
            <label class="form-label" style="font-size: 0.65rem;">Preview</label>
            <div id="{{ $padId }}-preview" class="border rounded-sm d-none align-items-center justify-content-center" style="height: {{ $height }}px; padding: 4px;">
                <img id="{{ $padId }}-preview-img" src="" style="max-width: 100%; max-height: 100%;">
            </div>
        </div>
    </div>
    @if($helpText)
    <small class="text-muted" style="font-size: 0.6rem;">{{ $helpText }}</small>
    @endif
</div>

@once
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
@endonce
<script>
document.addEventListener('DOMContentLoaded', function () {
    const canvas = document.getElementById('{{ $padId }}-canvas');
    if (!canvas || canvas.dataset.signaturePadReady) return;
    canvas.dataset.signaturePadReady = '1';

    // canvas.offsetWidth can be 0, or a small pre-layout value, at the moment
    // this script first runs - before the page's CSS/fonts/grid have fully
    // settled. Sizing the drawing buffer off that stale value silently
    // produces a near-zero-width canvas that captures little to no visible
    // ink even though it displays at the correct CSS size. A ResizeObserver
    // re-sizes the buffer every time the element's real layout size changes,
    // so it's always correct by the time the user actually draws - not just
    // once, at a moment that might be too early.
    let lastWidth = 0;
    let lastHeight = 0;
    function sizeCanvas(width, height) {
        if (width < 20 || (width === lastWidth && height === lastHeight)) return;
        lastWidth = width;
        lastHeight = height;

        const ratio = Math.max(window.devicePixelRatio || 1, 1);
        canvas.width = width * ratio;
        canvas.height = height * ratio;
        canvas.getContext('2d').scale(ratio, ratio);
    }
    sizeCanvas(canvas.offsetWidth, canvas.offsetHeight);
    const resizeObserver = new ResizeObserver(function (entries) {
        const box = entries[0].contentRect;
        sizeCanvas(Math.round(box.width), Math.round(box.height));
    });
    resizeObserver.observe(canvas);
    // Resizing the buffer clears any ink already drawn, so stop watching the
    // instant the user actually starts signing.
    canvas.addEventListener('pointerdown', function () {
        resizeObserver.disconnect();
    }, {once: true});

    // Blue ink, not black - the same reason a paper form asks for "blue or
    // black pen": a blue signature is unmistakably an original, not a
    // photocopy or scan reprinted in black and white.
    const pad = new SignaturePad(canvas, { backgroundColor: '#fff', penColor: '#1a3d8f' });
    const dataInput = document.getElementById('{{ $padId }}-data');
    const preview = document.getElementById('{{ $padId }}-preview');
    const previewImg = document.getElementById('{{ $padId }}-preview-img');

    function updatePreview() {
        if (!pad.isEmpty()) {
            const dataUrl = pad.toDataURL();
            dataInput.value = dataUrl;
            previewImg.src = dataUrl;
            preview.classList.remove('d-none');
            preview.classList.add('d-flex');
        } else {
            dataInput.value = '';
            preview.classList.add('d-none');
            preview.classList.remove('d-flex');
        }
    }

    pad.addEventListener('endStroke', updatePreview);

    document.getElementById('{{ $padId }}-clear').addEventListener('click', function () {
        pad.clear();
        updatePreview();
    });

    canvas.closest('form')?.addEventListener('submit', function (e) {
        if ({{ $required ? 'true' : 'false' }} && pad.isEmpty()) {
            e.preventDefault();
            alert('Please provide a signature before submitting.');
        }
    });
});
</script>
