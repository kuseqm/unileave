<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'viewRoute' => null,
    'editRoute' => null,
    'deleteRoute' => null,
    'cancelRoute' => null,
    'cancelId' => null,
    'cancelName' => null,
    'deleteId' => null,
    'deleteName' => null,
    'viewId' => null,
    'showCancel' => false,
    'showDelete' => false,
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
    'viewRoute' => null,
    'editRoute' => null,
    'deleteRoute' => null,
    'cancelRoute' => null,
    'cancelId' => null,
    'cancelName' => null,
    'deleteId' => null,
    'deleteName' => null,
    'viewId' => null,
    'showCancel' => false,
    'showDelete' => false,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div class="btn-group" role="group">
    <?php if($viewRoute): ?>
    <a href="<?php echo e($viewRoute); ?>" class="btn btn-sm btn-outline-info rounded-sm" title="View" style="padding: 0.15rem 0.3rem;">
        <i class="ti ti-eye" style="font-size: 0.7rem;"></i>
    </a>
    <?php endif; ?>
    
    <?php if($editRoute): ?>
    <a href="<?php echo e($editRoute); ?>" class="btn btn-sm btn-outline-primary rounded-sm" title="Edit" style="padding: 0.15rem 0.3rem;">
        <i class="ti ti-edit" style="font-size: 0.7rem;"></i>
    </a>
    <?php endif; ?>
    
    <?php if($showCancel && $cancelRoute): ?>
    <button type="button" class="btn btn-sm btn-outline-warning rounded-sm" onclick="showCancelModal('<?php echo e($cancelId); ?>', '<?php echo e($cancelName); ?>')" title="Cancel Request" style="padding: 0.15rem 0.3rem;">
        <i class="ti ti-x" style="font-size: 0.7rem;"></i>
    </button>
    <?php endif; ?>
    
    <?php if($showDelete && $deleteRoute): ?>
    <button type="button" class="btn btn-sm btn-outline-danger rounded-sm" onclick="showDeleteModal('<?php echo e($deleteId); ?>', '<?php echo e($deleteName); ?>')" title="Delete" style="padding: 0.15rem 0.3rem;">
        <i class="ti ti-trash" style="font-size: 0.7rem;"></i>
    </button>
    <?php endif; ?>
</div><?php /**PATH C:\wamp64\www\unileave\resources\views/components/action-buttons.blade.php ENDPATH**/ ?>