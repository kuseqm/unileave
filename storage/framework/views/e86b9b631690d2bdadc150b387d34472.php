<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['items' => []]));

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

foreach (array_filter((['items' => []]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<nav aria-label="breadcrumb">
    <ol class="breadcrumb mb-2" style="font-size: 0.75rem;">
        <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>" class="text-muted">Home</a></li>
        <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if($loop->last): ?>
                <li class="breadcrumb-item active"><?php echo e($item['name']); ?></li>
            <?php else: ?>
                <li class="breadcrumb-item"><a href="<?php echo e($item['url']); ?>" class="text-muted"><?php echo e($item['name']); ?></a></li>
            <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </ol>
</nav><?php /**PATH C:\wamp64\www\unileave\resources\views/components/breadcrumb.blade.php ENDPATH**/ ?>