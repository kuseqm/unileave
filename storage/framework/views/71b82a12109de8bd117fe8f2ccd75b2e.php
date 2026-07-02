

<?php $__env->startSection('title', 'Incoming Letters'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-2">
    
    <!-- Breadcrumb -->
    <?php if (isset($component)) { $__componentOriginale19f62b34dfe0bfdf95075badcb45bc2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale19f62b34dfe0bfdf95075badcb45bc2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.breadcrumb','data' => ['items' => [
        ['name' => 'Central Registry', 'url' => '#'],
        ['name' => 'Incoming Letters', 'url' => '#']
    ]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('breadcrumb'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['items' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
        ['name' => 'Central Registry', 'url' => '#'],
        ['name' => 'Incoming Letters', 'url' => '#']
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'INCOMING LETTERS','description' => 'Manage all incoming correspondence']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'INCOMING LETTERS','description' => 'Manage all incoming correspondence']); ?>
        <div class="d-flex gap-1">
            <a href="<?php echo e(route('registry.incoming.create')); ?>" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
                <i class="ti ti-plus me-1"></i> New Incoming Letter
            </a>
        </div>
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
                        <option value="received" <?php echo e(request('status') == 'received' ? 'selected' : ''); ?>>Received</option>
                        <option value="assigned" <?php echo e(request('status') == 'assigned' ? 'selected' : ''); ?>>Assigned</option>
                        <option value="in_progress" <?php echo e(request('status') == 'in_progress' ? 'selected' : ''); ?>>In Progress</option>
                        <option value="replied" <?php echo e(request('status') == 'replied' ? 'selected' : ''); ?>>Replied</option>
                        <option value="closed" <?php echo e(request('status') == 'closed' ? 'selected' : ''); ?>>Closed</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="office_id" class="form-select form-select-sm" style="font-size: 0.7rem;">
                        <option value="">All Offices</option>
                        <?php $__currentLoopData = $offices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $office): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($office->id); ?>" <?php echo e(request('office_id') == $office->id ? 'selected' : ''); ?>><?php echo e($office->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Search by ref, sender, subject..." value="<?php echo e(request('search')); ?>" style="font-size: 0.7rem;">
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
                <i class="ti ti-list text-primary me-1" style="font-size: 0.8rem;"></i>
                <span style="font-size: 0.7rem; font-weight: 600;">Incoming Letters List</span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm mb-0" style="font-size: 0.7rem;">
                    <thead class="table-light">
                        <tr>
                            <th>Ref Number</th>
                            <th>Date Received</th>
                            <th>Sender</th>
                            <th>Office</th>
                            <th>Subject</th>
                            <th>Status</th>
                            <th>Priority</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $letters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $letter): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><strong><?php echo e($letter->ref_number); ?></strong></td>
                            <td><?php echo e($letter->date_received->format('d/m/Y')); ?></td>
                            <td><?php echo e(\Illuminate\Support\Str::limit($letter->sender, 25)); ?></td>
                            <td><?php echo e($letter->office->short_name ?? $letter->office->name); ?></td>
                            <td><?php echo e(\Illuminate\Support\Str::limit($letter->subject, 35)); ?></td>
                            <td>
                                <?php if($letter->status == 'received'): ?>
                                    <span class="badge bg-warning-subtle text-warning rounded-sm" style="font-size: 0.6rem;">Received</span>
                                <?php elseif($letter->status == 'assigned'): ?>
                                    <span class="badge bg-info-subtle text-info rounded-sm" style="font-size: 0.6rem;">Assigned</span>
                                <?php elseif($letter->status == 'closed'): ?>
                                    <span class="badge bg-success-subtle text-success rounded-sm" style="font-size: 0.6rem;">Closed</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary-subtle text-secondary rounded-sm" style="font-size: 0.6rem;"><?php echo e(ucfirst($letter->status)); ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($letter->priority == 'urgent'): ?>
                                    <span class="badge bg-danger-subtle text-danger rounded-sm" style="font-size: 0.6rem;">Urgent</span>
                                <?php elseif($letter->priority == 'confidential'): ?>
                                    <span class="badge bg-dark-subtle text-dark rounded-sm" style="font-size: 0.6rem;">Confidential</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary-subtle text-secondary rounded-sm" style="font-size: 0.6rem;">Normal</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="<?php echo e(route('registry.incoming.show', $letter->id)); ?>" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.6rem; padding: 0.15rem 0.4rem;">
                                    <i class="ti ti-eye"></i> View
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="8" class="text-center py-3 text-muted" style="font-size: 0.7rem;">No incoming letters found. <a href="<?php echo e(route('registry.incoming.create')); ?>">Create one</a></td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer py-1 px-2 bg-light">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted" style="font-size: 0.6rem;">Showing <?php echo e($letters->firstItem() ?? 0); ?> to <?php echo e($letters->lastItem() ?? 0); ?> of <?php echo e($letters->total()); ?> entries</div>
                <div>
                    <?php echo e($letters->appends(request()->query())->links('pagination::bootstrap-4')); ?>

                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\wamp64\www\unileave\resources\views/registry/incoming/index.blade.php ENDPATH**/ ?>