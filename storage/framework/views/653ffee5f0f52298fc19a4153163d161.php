

<?php $__env->startSection('title', 'Outgoing Letters'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-2">
    
    <!-- Breadcrumb -->
    <?php if (isset($component)) { $__componentOriginale19f62b34dfe0bfdf95075badcb45bc2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale19f62b34dfe0bfdf95075badcb45bc2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.breadcrumb','data' => ['items' => [
        ['name' => 'Central Registry', 'url' => '#'],
        ['name' => 'Outgoing Letters', 'url' => '#']
    ]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('breadcrumb'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['items' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
        ['name' => 'Central Registry', 'url' => '#'],
        ['name' => 'Outgoing Letters', 'url' => '#']
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'OUTGOING LETTERS','description' => 'Manage all outgoing correspondence']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'OUTGOING LETTERS','description' => 'Manage all outgoing correspondence']); ?>
        <a href="<?php echo e(route('registry.outgoing.create')); ?>" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
            <i class="ti ti-plus me-1"></i> New Outgoing Letter
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
    
    <!-- Filter Card -->
    <div class="card rounded-sm mb-2">
        <div class="card-header py-1 px-2 bg-light">
            <div class="d-flex align-items-center">
                <i class="ti ti-filter text-primary me-1" style="font-size: 0.8rem;"></i>
                <span style="font-size: 0.7rem; font-weight: 600;">Filter Letters</span>
            </div>
        </div>
        <div class="card-body py-2 px-2">
            <form method="GET" class="row g-1">
                <div class="col-md-3">
                    <select name="status" class="form-select form-select-sm" style="font-size: 0.7rem;">
                        <option value="">All Status</option>
                        <option value="draft" <?php echo e(request('status') == 'draft' ? 'selected' : ''); ?>>Draft</option>
                        <option value="pending_approval" <?php echo e(request('status') == 'pending_approval' ? 'selected' : ''); ?>>Pending Approval</option>
                        <option value="approved" <?php echo e(request('status') == 'approved' ? 'selected' : ''); ?>>Approved</option>
                        <option value="dispatched" <?php echo e(request('status') == 'dispatched' ? 'selected' : ''); ?>>Dispatched</option>
                    </select>
                </div>
                <div class="col-md-7">
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Search by ref number, recipient, subject..." value="<?php echo e(request('search')); ?>" style="font-size: 0.7rem;">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary btn-sm w-100" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">Filter</button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Letters Table Card -->
    <div class="card rounded-sm">
        <div class="card-header py-1 px-2 bg-light">
            <div class="d-flex align-items-center">
                <i class="ti ti-mail-forward text-primary me-1" style="font-size: 0.8rem;"></i>
                <span style="font-size: 0.7rem; font-weight: 600;">Outgoing Letters List</span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm mb-0" style="font-size: 0.7rem;">
                    <thead class="table-light">
                        <tr>
                            <th>Ref Number</th>
                            <th>Recipient</th>
                            <th>Subject</th>
                            <th>Status</th>
                            <th>Dispatched Via</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $letters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $letter): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><strong><?php echo e($letter->ref_number); ?></strong></td>
                            <td><?php echo e(\Illuminate\Support\Str::limit($letter->recipient, 25)); ?></td>
                            <td><?php echo e(\Illuminate\Support\Str::limit($letter->subject, 30)); ?></td>
                            <td>
                                <?php if($letter->status == 'draft'): ?>
                                    <span class="badge bg-secondary-subtle text-secondary rounded-sm" style="font-size: 0.6rem;">Draft</span>
                                <?php elseif($letter->status == 'pending_approval'): ?>
                                    <span class="badge bg-warning-subtle text-warning rounded-sm" style="font-size: 0.6rem;">Pending</span>
                                <?php elseif($letter->status == 'approved'): ?>
                                    <span class="badge bg-info-subtle text-info rounded-sm" style="font-size: 0.6rem;">Approved</span>
                                <?php elseif($letter->status == 'dispatched'): ?>
                                    <span class="badge bg-success-subtle text-success rounded-sm" style="font-size: 0.6rem;">Dispatched</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($letter->dispatched_via == 'email'): ?>
                                    <span class="badge bg-primary-subtle text-primary rounded-sm" style="font-size: 0.6rem;">Email</span>
                                <?php else: ?>
                                    <span class="badge bg-warning-subtle text-warning rounded-sm" style="font-size: 0.6rem;">Print</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo e($letter->created_at->format('d/m/Y')); ?></td>
                            <td>
                                <a href="<?php echo e(route('registry.outgoing.show', $letter->id)); ?>" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.6rem; padding: 0.15rem 0.4rem;">
                                    <i class="ti ti-eye"></i> View
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" class="text-center py-3 text-muted" style="font-size: 0.7rem;">No outgoing letters found. <a href="<?php echo e(route('registry.outgoing.create')); ?>">Create one</a></td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer py-1 px-2 bg-light">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted" style="font-size: 0.6rem;">Showing <?php echo e($letters->firstItem() ?? 0); ?> to <?php echo e($letters->lastItem() ?? 0); ?> of <?php echo e($letters->total()); ?> entries</div>
                <div><?php echo e($letters->appends(request()->query())->links('pagination::bootstrap-4')); ?></div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\wamp64\www\unileave\resources\views/registry/outgoing/index.blade.php ENDPATH**/ ?>