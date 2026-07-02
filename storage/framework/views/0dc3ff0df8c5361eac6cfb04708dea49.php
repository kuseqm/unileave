

<?php $__env->startSection('title', 'Blackout Periods'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-2">
    
    <?php if (isset($component)) { $__componentOriginale19f62b34dfe0bfdf95075badcb45bc2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale19f62b34dfe0bfdf95075badcb45bc2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.breadcrumb','data' => ['items' => [
        ['name' => 'Dashboard', 'url' => route('dashboard')],
        ['name' => 'Blackout Periods', 'url' => '#']
    ]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('breadcrumb'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['items' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
        ['name' => 'Dashboard', 'url' => route('dashboard')],
        ['name' => 'Blackout Periods', 'url' => '#']
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
    
    <?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'BLACKOUT PERIODS','description' => 'Periods when leave requests are restricted']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'BLACKOUT PERIODS','description' => 'Periods when leave requests are restricted']); ?>
        <?php if(auth()->check() && auth()->user()->hasAnyRole('admin|hr-officer|dean')): ?>
        <a href="<?php echo e(route('blackout.create')); ?>" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
            <i class="ti ti-plus me-1"></i> Add Blackout Period
        </a>
        <?php endif; ?>
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
        $totalBlackouts = \App\Models\BlackoutPeriod::count();
        $activeBlackouts = \App\Models\BlackoutPeriod::where('end_date', '>=', now())->count();
        $hardBlackouts = \App\Models\BlackoutPeriod::where('restriction_type', 'hard')->where('end_date', '>=', now())->count();
        $upcomingBlackouts = \App\Models\BlackoutPeriod::where('start_date', '>', now())->count();
    ?>
    
    <div class="row g-2 mb-3">
        <?php if (isset($component)) { $__componentOriginal8f216e051c231b98198765acd723fb77 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8f216e051c231b98198765acd723fb77 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stats-card','data' => ['value' => ''.e($totalBlackouts).'','label' => 'Total Blackouts','subtext' => 'all time','icon' => 'ban']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('stats-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => ''.e($totalBlackouts).'','label' => 'Total Blackouts','subtext' => 'all time','icon' => 'ban']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stats-card','data' => ['value' => ''.e($activeBlackouts).'','label' => 'Active Blackouts','subtext' => 'currently in effect','color' => 'danger','icon' => 'alert-circle']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('stats-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => ''.e($activeBlackouts).'','label' => 'Active Blackouts','subtext' => 'currently in effect','color' => 'danger','icon' => 'alert-circle']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stats-card','data' => ['value' => ''.e($hardBlackouts).'','label' => 'Hard Blocks','subtext' => 'no leave allowed','color' => 'danger','icon' => 'x-circle']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('stats-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => ''.e($hardBlackouts).'','label' => 'Hard Blocks','subtext' => 'no leave allowed','color' => 'danger','icon' => 'x-circle']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stats-card','data' => ['value' => ''.e($upcomingBlackouts).'','label' => 'Upcoming','subtext' => 'starting soon','color' => 'info','icon' => 'calendar']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('stats-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => ''.e($upcomingBlackouts).'','label' => 'Upcoming','subtext' => 'starting soon','color' => 'info','icon' => 'calendar']); ?>
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
    
    <div class="card rounded-sm">
        <div class="card-header py-1 px-2 bg-light">
            <div class="d-flex align-items-center">
                <i class="ti ti-ban text-danger me-1" style="font-size: 0.8rem;"></i>
                <span style="font-size: 0.7rem; font-weight: 600;">Blackout Periods List</span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm mb-0" style="font-size: 0.75rem;">
                    <thead>
                        <tr>
                            <th class="ps-2 py-1">Name</th>
                            <th class="py-1">Start Date</th>
                            <th class="py-1">End Date</th>
                            <th class="py-1">Days Left</th>
                            <th class="py-1">Restriction</th>
                            <th class="py-1">Override</th>
                            <th class="pe-2 py-1">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $blackoutPeriods; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $blackout): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $daysLeft = \Carbon\Carbon::parse($blackout->end_date)->diffInDays(now(), false);
                            $isActive = $daysLeft > 0 && \Carbon\Carbon::parse($blackout->start_date)->isPast();
                            $isUpcoming = \Carbon\Carbon::parse($blackout->start_date)->isFuture();
                        ?>
                        <tr class="clickable-row" style="cursor: pointer;" onclick="window.location='<?php echo e(route('blackout.show', $blackout->id)); ?>'">
                            <td class="ps-2 py-1">
                                <span class="fw-semibold hover-link"><?php echo e($blackout->name); ?></span>
                                <?php if($isActive): ?>
                                    <span class="badge bg-danger-subtle text-danger rounded-sm ms-1" style="font-size: 0.55rem;">Active</span>
                                <?php elseif($isUpcoming): ?>
                                    <span class="badge bg-info-subtle text-info rounded-sm ms-1" style="font-size: 0.55rem;">Upcoming</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary-subtle text-secondary rounded-sm ms-1" style="font-size: 0.55rem;">Expired</span>
                                <?php endif; ?>
                            </td>
                            <td class="py-1"><?php echo e(\Carbon\Carbon::parse($blackout->start_date)->format('d M Y')); ?></td>
                            <td class="py-1"><?php echo e(\Carbon\Carbon::parse($blackout->end_date)->format('d M Y')); ?></td>
                            <td class="py-1">
                                <?php if($daysLeft > 0): ?>
                                    <span class="fw-semibold text-warning"><?php echo e($daysLeft); ?> days</span>
                                <?php else: ?>
                                    <span class="text-muted">Expired</span>
                                <?php endif; ?>
                            </td>
                            <td class="py-1">
                                <?php if($blackout->restriction_type == 'hard'): ?>
                                    <span class="badge bg-danger-subtle text-danger rounded-sm">Hard Block</span>
                                <?php else: ?>
                                    <span class="badge bg-warning-subtle text-warning rounded-sm">Soft Warning</span>
                                <?php endif; ?>
                            </td>
                            <td class="py-1">
                                <?php if($blackout->override_allowed): ?>
                                    <span class="badge bg-success-subtle text-success rounded-sm">Allowed</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary-subtle text-secondary rounded-sm">Not Allowed</span>
                                <?php endif; ?>
                            </td>
                            <td class="pe-2 py-1">
                                <div class="d-flex gap-1">
                                    <!-- View Button (Eye Icon) - All authenticated users can view -->
                                    <a href="<?php echo e(route('blackout.show', $blackout->id)); ?>" class="btn btn-outline-info btn-sm rounded-sm" style="padding: 0.1rem 0.4rem; font-size: 0.6rem;" title="View Details" onclick="event.stopPropagation();">
                                        <i class="ti ti-eye"></i>
                                    </a>
                                    
                                    <!-- Edit Button - Admin/HR/Dean only -->
                                    <?php if(auth()->check() && auth()->user()->hasAnyRole('admin|hr-officer|dean')): ?>
                                    <a href="<?php echo e(route('blackout.edit', $blackout->id)); ?>" class="btn btn-outline-primary btn-sm rounded-sm" style="padding: 0.1rem 0.4rem; font-size: 0.6rem;" title="Edit Blackout" onclick="event.stopPropagation();">
                                        <i class="ti ti-edit"></i>
                                    </a>
                                    <?php endif; ?>
                                    
                                    <!-- Delete Button - Admin/HR only with Standard Modal -->
                                    <?php if(auth()->check() && auth()->user()->hasAnyRole('admin|hr-officer')): ?>
                                    <button type="button" 
                                            onclick="event.stopPropagation(); showDeleteModal('<?php echo e($blackout->id); ?>', '<?php echo e(addslashes($blackout->name)); ?>', 'blackout', '<?php echo e(route('blackout.destroy', $blackout->id)); ?>')" 
                                            class="btn btn-outline-danger btn-sm rounded-sm" 
                                            style="padding: 0.1rem 0.4rem; font-size: 0.6rem;"
                                            title="Delete Blackout">
                                        <i class="ti ti-trash"></i>
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" class="text-center py-3 text-muted">No blackout periods found</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer py-1 px-2">
            <?php echo e($blackoutPeriods->links('pagination::bootstrap-5')); ?>

        </div>
    </div>
</div>

<style>
.clickable-row {
    transition: background 0.2s ease;
}
.clickable-row:hover {
    background-color: #F8FAFC !important;
}
.hover-link:hover {
    text-decoration: underline;
    color: #3B82F6 !important;
}
.d-flex.gap-1 {
    gap: 0.25rem;
}
</style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\wamp64\www\unileave\resources\views/shared/blackout/index.blade.php ENDPATH**/ ?>