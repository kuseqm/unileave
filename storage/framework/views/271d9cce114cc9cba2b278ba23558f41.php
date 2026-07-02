<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['title', 'description', 'backUrl' => null]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['title', 'description', 'backUrl' => null]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div class="card rounded-sm mb-2">
    <div class="card-body py-2 px-3">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h1 class="h5 mb-0" style="color: #3B82F6; font-weight: 700;"><?php echo e($title); ?></h1>
                <p class="text-muted mb-0" style="font-size: 0.7rem;"><?php echo e($description); ?></p>
            </div>
            <div class="d-flex gap-2">
                <?php if($backUrl): ?>
                <a href="<?php echo e($backUrl); ?>" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
                    <i class="ti ti-arrow-left me-1"></i> Back
                </a>
                <?php endif; ?>
                <?php echo e($slot); ?>

            </div>
        </div>
    </div>
</div>
<?php /**PATH C:\wamp64\www\unileave\resources\views/components/page-header.blade.php ENDPATH**/ ?>