<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'value', 
    'label', 
    'subtext' => null, 
    'color' => 'primary',
    'icon' => null,
    'trend' => null,
    'trendValue' => null,
    'link' => null
]));

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

foreach (array_filter(([
    'value', 
    'label', 
    'subtext' => null, 
    'color' => 'primary',
    'icon' => null,
    'trend' => null,
    'trendValue' => null,
    'link' => null
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div class="col-6 col-md-3">
    <div class="card rounded-sm text-center h-100" style="background: white; transition: transform 0.2s ease, box-shadow 0.2s ease; <?php echo e($link ? 'cursor: pointer;' : ''); ?>">
        <div class="card-body py-2 px-2">
            <?php if($icon): ?>
            <div class="mb-1">
                <i class="ti ti-<?php echo e($icon); ?> text-<?php echo e($color); ?>" style="font-size: 1.2rem;"></i>
            </div>
            <?php endif; ?>
            <div class="text-<?php echo e($color); ?> fw-bold" style="font-size: 1.2rem;"><?php echo e($value); ?></div>
            <div class="text-muted" style="font-size: 0.65rem;"><?php echo e($label); ?></div>
            <?php if($subtext): ?>
            <div class="small mt-1"><span class="text-muted" style="font-size: 0.6rem;"><?php echo e($subtext); ?></span></div>
            <?php endif; ?>
            <?php if($trend): ?>
            <div class="small mt-1">
                <span class="text-<?php echo e($trend == 'up' ? 'success' : 'danger'); ?>" style="font-size: 0.6rem;">
                    <i class="ti ti-arrow-<?php echo e($trend == 'up' ? 'up' : 'down'); ?>"></i> <?php echo e($trendValue); ?>

                </span>
            </div>
            <?php endif; ?>
        </div>
        <?php if($link): ?>
        <a href="<?php echo e($link); ?>" class="stretched-link"></a>
        <?php endif; ?>
    </div>
</div>

<style>
/* Hover effect only for cards with links */
.col-6.col-md-3 .card:has(a.stretched-link) {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.col-6.col-md-3 .card:has(a.stretched-link):hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}
</style><?php /**PATH C:\wamp64\www\unileave\resources\views/components/stats-card.blade.php ENDPATH**/ ?>