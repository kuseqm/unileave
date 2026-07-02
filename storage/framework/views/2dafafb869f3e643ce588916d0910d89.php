<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['items', 'perPageOptions' => [10, 25, 50, 100]]));

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

foreach (array_filter((['items', 'perPageOptions' => [10, 25, 50, 100]]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php if($items->total() > 0): ?>
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div class="d-flex align-items-center gap-2">
        <span class="text-muted" style="font-size: 0.65rem;">Show</span>
        <select id="perPageSelect" class="form-select form-select-sm rounded-sm" style="width: 70px; font-size: 0.7rem; padding: 0.2rem 0.3rem;">
            <?php $__currentLoopData = $perPageOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($option); ?>" <?php echo e(request('per_page', $items->perPage()) == $option ? 'selected' : ''); ?>><?php echo e($option); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <span class="text-muted" style="font-size: 0.65rem;">entries</span>
    </div>
    
    <div>
        <?php if($items->lastPage() > 1): ?>
            <nav>
                <ul class="pagination pagination-sm mb-0">
                    
                    <?php if($items->currentPage() > 2): ?>
                        <li class="page-item">
                            <a class="page-link" href="<?php echo e($items->url(1)); ?>" style="padding: 0.2rem 0.4rem; font-size: 0.65rem;">«</a>
                        </li>
                    <?php endif; ?>
                    
                    
                    <?php if($items->onFirstPage()): ?>
                        <li class="page-item disabled">
                            <span class="page-link" style="padding: 0.2rem 0.4rem; font-size: 0.65rem;">‹</span>
                        </li>
                    <?php else: ?>
                        <li class="page-item">
                            <a class="page-link" href="<?php echo e($items->previousPageUrl()); ?>" style="padding: 0.2rem 0.4rem; font-size: 0.65rem;">‹</a>
                        </li>
                    <?php endif; ?>
                    
                    
                    <?php
                        $start = max(1, $items->currentPage() - 2);
                        $end = min($items->lastPage(), $items->currentPage() + 2);
                        
                        if ($start > 1) {
                            echo '<li class="page-item"><a class="page-link" href="' . $items->url(1) . '" style="padding: 0.2rem 0.5rem; font-size: 0.7rem;">1</a></li>';
                            if ($start > 2) echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                        }
                    ?>
                    
                    <?php for($i = $start; $i <= $end; $i++): ?>
                        <?php if($i == $items->currentPage()): ?>
                            <li class="page-item active">
                                <span class="page-link" style="padding: 0.2rem 0.5rem; font-size: 0.7rem;"><?php echo e($i); ?></span>
                            </li>
                        <?php else: ?>
                            <li class="page-item">
                                <a class="page-link" href="<?php echo e($items->url($i)); ?>" style="padding: 0.2rem 0.5rem; font-size: 0.7rem;"><?php echo e($i); ?></a>
                            </li>
                        <?php endif; ?>
                    <?php endfor; ?>
                    
                    <?php
                        if ($end < $items->lastPage()) {
                            if ($end < $items->lastPage() - 1) echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                            echo '<li class="page-item"><a class="page-link" href="' . $items->url($items->lastPage()) . '" style="padding: 0.2rem 0.5rem; font-size: 0.7rem;">' . $items->lastPage() . '</a></li>';
                        }
                    ?>
                    
                    
                    <?php if($items->hasMorePages()): ?>
                        <li class="page-item">
                            <a class="page-link" href="<?php echo e($items->nextPageUrl()); ?>" style="padding: 0.2rem 0.4rem; font-size: 0.65rem;">›</a>
                        </li>
                    <?php else: ?>
                        <li class="page-item disabled">
                            <span class="page-link" style="padding: 0.2rem 0.4rem; font-size: 0.65rem;">›</span>
                        </li>
                    <?php endif; ?>
                    
                    
                    <?php if($items->currentPage() < $items->lastPage() - 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="<?php echo e($items->url($items->lastPage())); ?>" style="padding: 0.2rem 0.4rem; font-size: 0.65rem;">»</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        <?php endif; ?>
    </div>
    
    <div class="text-muted" style="font-size: 0.65rem;">
        <i class="ti ti-database me-1"></i> 
        Showing <?php echo e($items->firstItem() ?? 0); ?> to <?php echo e($items->lastItem() ?? 0); ?> of <?php echo e($items->total()); ?> entries
    </div>
</div>

<style>
.pagination {
    margin-bottom: 0;
    flex-wrap: wrap;
    gap: 0.125rem;
}
.pagination .page-link {
    color: #475569;
    background-color: white;
    border: 1px solid #E2E8F0;
    border-radius: 0.25rem;
}
.pagination .page-link:hover {
    background-color: #F1F5F9;
    color: #1E293B;
}
.pagination .page-item.active .page-link {
    background-color: #3B82F6;
    border-color: #3B82F6;
    color: white;
}
.pagination .page-item.disabled .page-link {
    color: #94A3B8;
    background-color: #F8FAFC;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const perPageSelect = document.getElementById('perPageSelect');
    if (perPageSelect) {
        perPageSelect.addEventListener('change', function() {
            const url = new URL(window.location.href);
            url.searchParams.set('per_page', this.value);
            url.searchParams.set('page', 1); // Reset to first page
            window.location.href = url.toString();
        });
    }
});
</script>
<?php else: ?>
<div class="text-center py-2 text-muted" style="font-size: 0.7rem;">
    <i class="ti ti-database me-1"></i> No entries found
</div>
<?php endif; ?><?php /**PATH C:\wamp64\www\unileave\resources\views/components/pagination.blade.php ENDPATH**/ ?>