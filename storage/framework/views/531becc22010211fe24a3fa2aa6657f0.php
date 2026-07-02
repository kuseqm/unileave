

<?php $__env->startSection('title', 'Acting DVC Assignments'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-2">
    
    <?php if (isset($component)) { $__componentOriginale19f62b34dfe0bfdf95075badcb45bc2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale19f62b34dfe0bfdf95075badcb45bc2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.breadcrumb','data' => ['items' => [
        ['name' => 'Admin', 'url' => '#'],
        ['name' => 'Acting DVC', 'url' => '#']
    ]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('breadcrumb'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['items' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
        ['name' => 'Admin', 'url' => '#'],
        ['name' => 'Acting DVC', 'url' => '#']
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'ACTING DVC ASSIGNMENTS','description' => 'Assign staff to act as DVC when the substantive DVC is on leave']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'ACTING DVC ASSIGNMENTS','description' => 'Assign staff to act as DVC when the substantive DVC is on leave']); ?>
        <a href="<?php echo e(route('admin.acting-dvc.create')); ?>" class="btn btn-primary btn-sm rounded-sm">
            <i class="ti ti-plus me-1"></i> New Assignment
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
    
    <?php if(session('success')): ?>
    <div class="alert alert-success alert-dismissible fade show py-1 px-2 mb-2 rounded-sm" role="alert">
        <i class="ti ti-check-circle me-1"></i> <?php echo e(session('success')); ?>

        <button type="button" class="btn-close py-0" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>
    
    <?php if(session('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show py-1 px-2 mb-2 rounded-sm" role="alert">
        <i class="ti ti-alert-circle me-1"></i> <?php echo e(session('error')); ?>

        <button type="button" class="btn-close py-0" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>
    
    <!-- Active Assignments -->
    <div class="card rounded-sm mb-3">
        <div class="card-header bg-success-subtle py-1 px-2">
            <i class="ti ti-user-check me-1"></i>
            <span style="font-weight: 600;">Active Assignments</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm mb-0" style="font-size: 0.75rem;">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-2 py-1">Staff</th>
                            <th class="py-1">Role</th>
                            <th class="py-1">Start Date</th>
                            <th class="py-1">End Date</th>
                            <th class="py-1">Status</th>
                            <th class="pe-2 py-1">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $activeAssignments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $assignment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td class="ps-2 py-1">
                                <?php echo e($assignment->staff->first_name); ?> <?php echo e($assignment->staff->last_name); ?>

                                <div class="text-muted" style="font-size: 0.6rem;"><?php echo e($assignment->staff->staff_id ?? ''); ?></div>
                            </td>
                            <td class="py-1">
                                <span class="badge bg-primary-subtle text-primary rounded-sm">
                                    <?php echo e(strtoupper($assignment->role)); ?>

                                </span>
                            </td>
                            <td class="py-1"><?php echo e($assignment->start_date->format('d M Y')); ?></td>
                            <td class="py-1"><?php echo e($assignment->end_date->format('d M Y')); ?></td>
                            <td class="py-1">
                                <span class="badge bg-success-subtle text-success rounded-sm">Active</span>
                            </td>
                            <td class="pe-2 py-1">
                                <form action="<?php echo e(route('admin.acting-dvc.destroy', $assignment->id)); ?>" method="POST" class="d-inline">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-sm" onclick="return confirm('End this assignment?')">
                                        <i class="ti ti-trash"></i> End
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" class="text-center py-2 text-muted">No active assignments</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Pending Assignments -->
    <?php if($pendingAssignments->count() > 0): ?>
    <div class="card rounded-sm mb-3">
        <div class="card-header bg-warning-subtle py-1 px-2">
            <i class="ti ti-clock me-1"></i>
            <span style="font-weight: 600;">Pending Assignments</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm mb-0" style="font-size: 0.75rem;">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-2 py-1">Staff</th>
                            <th class="py-1">Role</th>
                            <th class="py-1">Start Date</th>
                            <th class="py-1">End Date</th>
                            <th class="py-1">Reason</th>
                            <th class="pe-2 py-1">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $pendingAssignments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $assignment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td class="ps-2 py-1">
                                <?php echo e($assignment->staff->first_name); ?> <?php echo e($assignment->staff->last_name); ?>

                                <div class="text-muted" style="font-size: 0.6rem;"><?php echo e($assignment->staff->staff_id ?? ''); ?></div>
                            </td>
                            <td class="py-1">
                                <span class="badge bg-info-subtle text-info rounded-sm"><?php echo e(strtoupper($assignment->role)); ?></span>
                            </td>
                            <td class="py-1"><?php echo e($assignment->start_date->format('d M Y')); ?></td>
                            <td class="py-1"><?php echo e($assignment->end_date->format('d M Y')); ?></td>
                            <td class="py-1"><?php echo e(\Str::limit($assignment->reason, 50)); ?></td>
                            <td class="pe-2 py-1">
                                <a href="<?php echo e(route('admin.acting-dvc.show', $assignment->id)); ?>" class="btn btn-sm btn-outline-info rounded-sm">
                                    <i class="ti ti-eye"></i> Review
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- History -->
    <div class="card rounded-sm">
        <div class="card-header bg-light py-1 px-2">
            <i class="ti ti-history me-1"></i>
            <span style="font-weight: 600;">History</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm mb-0" style="font-size: 0.75rem;">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-2 py-1">Staff</th>
                            <th class="py-1">Role</th>
                            <th class="py-1">Period</th>
                            <th class="py-1">Status</th>
                            <th class="pe-2 py-1">Approved By</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $history; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $assignment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td class="ps-2 py-1"><?php echo e($assignment->staff->first_name); ?> <?php echo e($assignment->staff->last_name); ?></td>
                            <td class="py-1"><?php echo e(strtoupper($assignment->role)); ?></td>
                            <td class="py-1"><?php echo e($assignment->start_date->format('d M Y')); ?> - <?php echo e($assignment->end_date->format('d M Y')); ?></td>
                            <td class="py-1">
                                <?php if($assignment->status == 'expired'): ?>
                                    <span class="badge bg-secondary-subtle text-secondary rounded-sm">Expired</span>
                                <?php elseif($assignment->status == 'rejected'): ?>
                                    <span class="badge bg-danger-subtle text-danger rounded-sm">Rejected</span>
                                <?php endif; ?>
                            </td>
                            <td class="pe-2 py-1"><?php echo e($assignment->approvedBy->first_name ?? 'N/A'); ?> <?php echo e($assignment->approvedBy->last_name ?? ''); ?></td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="5" class="text-center py-2 text-muted">No history</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer py-1 px-2">
            <?php echo e($history->links('pagination::bootstrap-5')); ?>

        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\wamp64\www\unileave\resources\views/admin/acting-dvc/index.blade.php ENDPATH**/ ?>