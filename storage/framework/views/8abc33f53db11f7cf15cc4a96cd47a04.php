

<?php $__env->startSection('title', 'My Assignments'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-2">
    
    <!-- Breadcrumb -->
    <?php if (isset($component)) { $__componentOriginale19f62b34dfe0bfdf95075badcb45bc2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale19f62b34dfe0bfdf95075badcb45bc2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.breadcrumb','data' => ['items' => [
        ['name' => 'My Assignments', 'url' => '#']
    ]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('breadcrumb'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['items' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
        ['name' => 'My Assignments', 'url' => '#']
    ])]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale19f62b34dfe0bfdf95075badcb45bc2)): ?>
<?php $attributes = $__attributesOriginale19f62b34dfe0bfdf95075badcb45bc2; ?>
<?php unset($__attributesOriginale19f62b34dfe0bfdf95075badcb45bc2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale19f62b34dfe0bfdf95075badcb45bc2)): ?>
<?php $component = $__componentOriginale19f62b34dfe0bfdf95075badcb45bc2; ?>
<?php unset($__componentOriginale19f62b34dfe0bfdf95075badcb45bc2); ?>
<?php endif; ?>
    
    <!-- Page Header -->
    <?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'MY ASSIGNMENTS','description' => 'Leave requests where you held position for others']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'MY ASSIGNMENTS','description' => 'Leave requests where you held position for others']); ?>
        <a href="<?php echo e(route('leave-requests.create')); ?>" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
            <i class="ti ti-plus me-1"></i> Apply for Leave
        </a>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e)): ?>
<?php $attributes = $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e; ?>
<?php unset($__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e)): ?>
<?php $component = $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e; ?>
<?php unset($__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e); ?>
<?php endif; ?>
    
    <!-- Stats Cards -->
    <?php
        $stats = [
            'total' => $holdHistories->total(),
            'pending' => $holdHistories->where('status', 'pending')->count(),
            'accepted' => $holdHistories->where('status', 'accepted')->count(),
            'declined' => $holdHistories->where('status', 'declined')->count(),
        ];
    ?>
    
    <div class="row g-2 mb-3">
        <?php if (isset($component)) { $__componentOriginal8f216e051c231b98198765acd723fb77 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8f216e051c231b98198765acd723fb77 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stats-card','data' => ['value' => ''.e($stats['total']).'','label' => 'Total Assignments','subtext' => 'all time']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('stats-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => ''.e($stats['total']).'','label' => 'Total Assignments','subtext' => 'all time']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8f216e051c231b98198765acd723fb77)): ?>
<?php $attributes = $__attributesOriginal8f216e051c231b98198765acd723fb77; ?>
<?php unset($__attributesOriginal8f216e051c231b98198765acd723fb77); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8f216e051c231b98198765acd723fb77)): ?>
<?php $component = $__componentOriginal8f216e051c231b98198765acd723fb77; ?>
<?php unset($__componentOriginal8f216e051c231b98198765acd723fb77); ?>
<?php endif; ?>
        <?php if (isset($component)) { $__componentOriginal8f216e051c231b98198765acd723fb77 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8f216e051c231b98198765acd723fb77 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stats-card','data' => ['value' => ''.e($stats['pending']).'','label' => 'Pending','subtext' => 'awaiting response','color' => 'warning']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('stats-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => ''.e($stats['pending']).'','label' => 'Pending','subtext' => 'awaiting response','color' => 'warning']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8f216e051c231b98198765acd723fb77)): ?>
<?php $attributes = $__attributesOriginal8f216e051c231b98198765acd723fb77; ?>
<?php unset($__attributesOriginal8f216e051c231b98198765acd723fb77); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8f216e051c231b98198765acd723fb77)): ?>
<?php $component = $__componentOriginal8f216e051c231b98198765acd723fb77; ?>
<?php unset($__componentOriginal8f216e051c231b98198765acd723fb77); ?>
<?php endif; ?>
        <?php if (isset($component)) { $__componentOriginal8f216e051c231b98198765acd723fb77 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8f216e051c231b98198765acd723fb77 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stats-card','data' => ['value' => ''.e($stats['accepted']).'','label' => 'Accepted','subtext' => 'you accepted','color' => 'success']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('stats-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => ''.e($stats['accepted']).'','label' => 'Accepted','subtext' => 'you accepted','color' => 'success']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8f216e051c231b98198765acd723fb77)): ?>
<?php $attributes = $__attributesOriginal8f216e051c231b98198765acd723fb77; ?>
<?php unset($__attributesOriginal8f216e051c231b98198765acd723fb77); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8f216e051c231b98198765acd723fb77)): ?>
<?php $component = $__componentOriginal8f216e051c231b98198765acd723fb77; ?>
<?php unset($__componentOriginal8f216e051c231b98198765acd723fb77); ?>
<?php endif; ?>
        <?php if (isset($component)) { $__componentOriginal8f216e051c231b98198765acd723fb77 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8f216e051c231b98198765acd723fb77 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stats-card','data' => ['value' => ''.e($stats['declined']).'','label' => 'Declined','subtext' => 'you declined','color' => 'danger']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('stats-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => ''.e($stats['declined']).'','label' => 'Declined','subtext' => 'you declined','color' => 'danger']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8f216e051c231b98198765acd723fb77)): ?>
<?php $attributes = $__attributesOriginal8f216e051c231b98198765acd723fb77; ?>
<?php unset($__attributesOriginal8f216e051c231b98198765acd723fb77); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8f216e051c231b98198765acd723fb77)): ?>
<?php $component = $__componentOriginal8f216e051c231b98198765acd723fb77; ?>
<?php unset($__componentOriginal8f216e051c231b98198765acd723fb77); ?>
<?php endif; ?>
    </div>
    
    <?php if(session('success')): ?>
    <div class="alert alert-success alert-dismissible fade show py-1 px-2 mb-2 rounded-sm" style="font-size: 0.7rem;" role="alert">
        <i class="ti ti-check-circle me-1"></i> <?php echo e(session('success')); ?>

        <button type="button" class="btn-close py-0" style="font-size: 0.6rem;" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>
    
    <?php if(session('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show py-1 px-2 mb-2 rounded-sm" style="font-size: 0.7rem;" role="alert">
        <i class="ti ti-alert-circle me-1"></i> <?php echo e(session('error')); ?>

        <button type="button" class="btn-close py-0" style="font-size: 0.6rem;" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>
    
    <!-- My Assignments Table -->
    <div class="card rounded-sm">
        <div class="card-header py-1 px-2 bg-light">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <i class="ti ti-user-check text-primary me-1" style="font-size: 0.8rem;"></i>
                    <span style="font-size: 0.7rem; font-weight: 600;">My Assignments</span>
                </div>
                <div class="text-muted" style="font-size: 0.6rem;">
                    Showing <?php echo e($holdHistories->firstItem() ?? 0); ?> to <?php echo e($holdHistories->lastItem() ?? 0); ?> of <?php echo e($holdHistories->total() ?? 0); ?>

                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <?php $__empty_1 = true; $__currentLoopData = $holdHistories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $history): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="request-item p-3 border-bottom">
                <!-- Request Header -->
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-2">
                    <div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="fw-semibold" style="font-size: 0.85rem;">
                                <?php echo e($history->requester->first_name ?? 'Unknown'); ?> <?php echo e($history->requester->last_name ?? ''); ?>

                            </span>
                            <span class="badge <?php echo e($history->status == 'pending' ? 'bg-warning-subtle text-warning' : 
                                ($history->status == 'accepted' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger')); ?> rounded-sm px-2 py-1" style="font-size: 0.6rem;">
                                <?php echo e(ucfirst($history->status)); ?>

                            </span>
                        </div>
                        <div class="text-muted" style="font-size: 0.65rem; margin-top: 4px;">
                            <i class="ti ti-calendar me-1"></i>
                            <?php echo e(\Carbon\Carbon::parse($history->leaveRequest->start_date)->format('M d, Y')); ?> - 
                            <?php echo e(\Carbon\Carbon::parse($history->leaveRequest->end_date)->format('M d, Y')); ?>

                            <span class="mx-1">•</span>
                            <i class="ti ti-clock me-1"></i><?php echo e($history->leaveRequest->days); ?> days
                        </div>
                    </div>
                    <div>
                        <a href="<?php echo e(route('leave-requests.show', $history->leaveRequest->id)); ?>" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.6rem; padding: 0.2rem 0.5rem;">
                            <i class="ti ti-eye me-1"></i> Details
                        </a>
                    </div>
                </div>
                
                <!-- Leave Type and Reason -->
                <div class="mt-2">
                    <div class="row g-2">
                        <div class="col-md-4">
                            <div class="text-muted" style="font-size: 0.6rem;">Leave Type</div>
                            <div style="font-size: 0.7rem;"><?php echo e($history->leaveRequest->leaveType->name ?? 'N/A'); ?></div>
                        </div>
                        <div class="col-md-8">
                            <div class="text-muted" style="font-size: 0.6rem;">Reason</div>
                            <div style="font-size: 0.7rem;"><?php echo e(Str::limit($history->leaveRequest->reason, 100)); ?></div>
                        </div>
                    </div>
                </div>
                
                <!-- Response Comment if any -->
                <?php if($history->comment): ?>
                <div class="mt-2 pt-2 border-top" style="border-color: #E8E2D5;">
                    <div class="text-muted" style="font-size: 0.6rem;">Your Comment</div>
                    <div class="small" style="font-size: 0.65rem;">"<?php echo e($history->comment); ?>"</div>
                </div>
                <?php endif; ?>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="text-center py-4">
                <i class="ti ti-user-check" style="font-size: 2rem; color: #CBD5E1;"></i>
                <p class="text-muted mt-2" style="font-size: 0.75rem;">No assignments found.</p>
                <p class="text-muted" style="font-size: 0.65rem;">When you accept to hold someone's position, it will appear here.</p>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Pagination -->
        <?php if($holdHistories->hasPages()): ?>
        <div class="card-footer py-2 px-3 bg-light">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <span class="text-muted" style="font-size: 0.65rem;">Show:</span>
                    <select id="perPageSelect" class="custom-select-sm" style="font-size: 0.65rem; padding: 0.2rem 1.2rem 0.2rem 0.4rem; border: 1px solid #E8E2D5; border-radius: 4px;">
                        <option value="10" <?php echo e($holdHistories->perPage() == 10 ? 'selected' : ''); ?>>10</option>
                        <option value="25" <?php echo e($holdHistories->perPage() == 25 ? 'selected' : ''); ?>>25</option>
                        <option value="50" <?php echo e($holdHistories->perPage() == 50 ? 'selected' : ''); ?>>50</option>
                        <option value="100" <?php echo e($holdHistories->perPage() == 100 ? 'selected' : ''); ?>>100</option>
                    </select>
                </div>
                
                <div>
                    <?php echo e($holdHistories->appends(request()->query())->links('pagination::bootstrap-5')); ?>

                </div>
                
                <div class="text-muted" style="font-size: 0.6rem;">
                    Page <?php echo e($holdHistories->currentPage()); ?> of <?php echo e($holdHistories->lastPage()); ?>

                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
// Per Page Change Handler
const perPageSelect = document.getElementById('perPageSelect');
if (perPageSelect) {
    perPageSelect.addEventListener('change', function() {
        const url = new URL(window.location.href);
        url.searchParams.set('per_page', this.value);
        url.searchParams.delete('page');
        window.location.href = url.toString();
    });
}
</script>

<style>
.request-item {
    transition: background 0.2s ease;
}
.request-item:hover {
    background: #F8FAFC;
}
.custom-select-sm {
    font-size: 0.65rem;
    padding: 0.2rem 1.2rem 0.2rem 0.4rem;
    border: 1px solid #E8E2D5;
    border-radius: 4px;
    background: white;
    cursor: pointer;
}
</style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\wamp64\www\unileave\resources\views/shared/leave-requests/i-held-position-for.blade.php ENDPATH**/ ?>