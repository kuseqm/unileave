<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'placeholder' => 'Search...',
    'id' => 'searchInput',
    'showStatusFilter' => false,
    'showCategoryFilter' => false,
    'showTypeFilter' => false,
    'statusOptions' => [],
    'categoryOptions' => [],
    'typeOptions' => [],
    'onReset' => null
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
    'placeholder' => 'Search...',
    'id' => 'searchInput',
    'showStatusFilter' => false,
    'showCategoryFilter' => false,
    'showTypeFilter' => false,
    'statusOptions' => [],
    'categoryOptions' => [],
    'typeOptions' => [],
    'onReset' => null
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div class="card rounded-sm mb-2">
    <div class="card-header py-1 px-2 bg-light">
        <div class="d-flex align-items-center">
            <i class="ti ti-search text-muted me-1" style="font-size: 0.8rem;"></i>
            <span style="font-size: 0.7rem; font-weight: 600;">Sophisticated Search</span>
        </div>
    </div>
    <div class="card-body py-2 px-2">
        <div class="row g-1 align-items-end">
            <div class="col-md-4">
                <label class="form-label" style="font-size: 0.65rem;">Search</label>
                <input type="text" id="<?php echo e($id); ?>" class="form-control form-control-sm rounded-sm" placeholder="<?php echo e($placeholder); ?>" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;">
            </div>
            
            <?php if($showStatusFilter): ?>
            <div class="col-md-3">
                <label class="form-label" style="font-size: 0.65rem;">Status</label>
                <select id="statusFilter" class="form-select form-select-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;">
                    <option value="">All</option>
                    <?php $__currentLoopData = $statusOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($value); ?>"><?php echo e($label); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <?php endif; ?>
            
            <?php if($showCategoryFilter): ?>
            <div class="col-md-3">
                <label class="form-label" style="font-size: 0.65rem;">Category</label>
                <select id="categoryFilter" class="form-select form-select-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;">
                    <option value="">All</option>
                    <?php $__currentLoopData = $categoryOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($value); ?>"><?php echo e($label); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <?php endif; ?>
            
            <?php if($showTypeFilter): ?>
            <div class="col-md-3">
                <label class="form-label" style="font-size: 0.65rem;">Type</label>
                <select id="typeFilter" class="form-select form-select-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;">
                    <option value="">All</option>
                    <?php $__currentLoopData = $typeOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($value); ?>"><?php echo e($label); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <?php endif; ?>
            
            <div class="col-md-2">
                <button id="resetFilters" class="btn btn-outline-secondary btn-sm w-100 rounded-sm" style="font-size: 0.65rem; padding: 0.25rem 0.3rem;">
                    <i class="ti ti-refresh me-1"></i> Reset
                </button>
            </div>
        </div>
    </div>
</div><?php /**PATH C:\wamp64\www\unileave\resources\views/components/search-card.blade.php ENDPATH**/ ?>