

<?php $__env->startSection('title', 'My Leave Requests'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-2">
    
    <!-- Breadcrumb -->
    <?php if (isset($component)) { $__componentOriginale19f62b34dfe0bfdf95075badcb45bc2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale19f62b34dfe0bfdf95075badcb45bc2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.breadcrumb','data' => ['items' => [
        ['name' => 'My Leave Requests', 'url' => '#']
    ]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('breadcrumb'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['items' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
        ['name' => 'My Leave Requests', 'url' => '#']
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'MY LEAVE REQUESTS','description' => 'View and manage your leave requests']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'MY LEAVE REQUESTS','description' => 'View and manage your leave requests']); ?>
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
        <!-- Stats Cards -->
        <div class="row g-2 mb-3">
            <?php if (isset($component)) { $__componentOriginal8f216e051c231b98198765acd723fb77 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8f216e051c231b98198765acd723fb77 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stats-card','data' => ['value' => ''.e($stats['total'] ?? 0).'','label' => 'Total Requests','subtext' => 'all time']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('stats-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => ''.e($stats['total'] ?? 0).'','label' => 'Total Requests','subtext' => 'all time']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stats-card','data' => ['value' => ''.e($stats['pending'] ?? 0).'','label' => 'Pending','subtext' => 'awaiting approval','color' => 'warning']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('stats-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => ''.e($stats['pending'] ?? 0).'','label' => 'Pending','subtext' => 'awaiting approval','color' => 'warning']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stats-card','data' => ['value' => ''.e($stats['approved'] ?? 0).'','label' => 'Approved','subtext' => 'granted','color' => 'success']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('stats-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => ''.e($stats['approved'] ?? 0).'','label' => 'Approved','subtext' => 'granted','color' => 'success']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stats-card','data' => ['value' => ''.e($stats['rejected'] ?? 0).'','label' => 'Rejected','subtext' => 'denied','color' => 'danger']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('stats-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => ''.e($stats['rejected'] ?? 0).'','label' => 'Rejected','subtext' => 'denied','color' => 'danger']); ?>
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
    
    <!-- Leave Requests Table -->
    <div class="card rounded-sm">
        <div class="card-header py-1 px-2 bg-light">
            <div class="d-flex align-items-center">
                <i class="ti ti-calendar text-primary me-1" style="font-size: 0.8rem;"></i>
                <span style="font-size: 0.7rem; font-weight: 600;">Leave Requests</span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm mb-0" style="font-size: 0.75rem;">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-2 py-1">#</th>
                            <th class="py-1">Leave Type</th>
                            <th class="py-1">Dates</th>
                            <th class="py-1">Days</th>
                            <th class="py-1">Acting Staff</th>
                            <th class="py-1">Status</th>
                            <th class="py-1">Approval Level</th>
                            <th class="pe-2 py-1">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $leaveRequests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $request): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td class="ps-2 py-1"><?php echo e($leaveRequests->firstItem() + $index); ?></td>
                            <td class="py-1"><?php echo e($request->leaveType->name ?? 'N/A'); ?> (<?php echo e($request->leaveType->code ?? ''); ?>)</td>
                            <td class="py-1"><?php echo e(\Carbon\Carbon::parse($request->start_date)->format('d M Y')); ?> - <?php echo e(\Carbon\Carbon::parse($request->end_date)->format('d M Y')); ?></td>
                            <td class="py-1"><?php echo e($request->days); ?> day(s)</td>
                            <td class="py-1">
                                <?php if($request->actingStaff): ?>
                                    <?php echo e($request->actingStaff->first_name); ?> <?php if($request->actingStaff->middle_name): ?><?php echo e($request->actingStaff->middle_name); ?> <?php endif; ?><?php echo e($request->actingStaff->last_name); ?>

                                    <br>
                                    <?php if($request->acting_status == 'pending'): ?>
                                        <span class="badge bg-warning-subtle text-warning rounded-sm" style="font-size: 0.55rem;">Awaiting Response</span>
                                    <?php elseif($request->acting_status == 'accepted'): ?>
                                        <span class="badge bg-success-subtle text-success rounded-sm" style="font-size: 0.55rem;">Accepted</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger-subtle text-danger rounded-sm" style="font-size: 0.55rem;">Declined</span>
                                    <?php endif; ?>
                                <?php else: ?>
                                    N/A
                                <?php endif; ?>
                            </td>
                            <td class="py-1">
                                <?php if($request->status == 'pending'): ?>
                                    <span class="badge bg-warning-subtle text-warning rounded-sm" style="font-size: 0.6rem;">Pending</span>
                                <?php elseif($request->status == 'approved'): ?>
                                    <span class="badge bg-success-subtle text-success rounded-sm" style="font-size: 0.6rem;">Approved</span>
                                <?php elseif($request->status == 'rejected'): ?>
                                    <span class="badge bg-danger-subtle text-danger rounded-sm" style="font-size: 0.6rem;">Rejected</span>
                                <?php elseif($request->status == 'cancelled'): ?>
                                    <span class="badge bg-secondary-subtle text-secondary rounded-sm" style="font-size: 0.6rem;">Cancelled</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary-subtle text-secondary rounded-sm" style="font-size: 0.6rem;"><?php echo e(ucfirst($request->status)); ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="py-1"><?php echo e($request->approval_level_name); ?></td>
                            <td class="pe-2 py-1">
                                <?php if (isset($component)) { $__componentOriginalf9332b595ad3d3a806f9da4dda8769dd = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf9332b595ad3d3a806f9da4dda8769dd = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.action-buttons','data' => ['viewRoute' => ''.e(route('leave-requests.show', $request->id)).'','showCancel' => ''.e($request->status == 'pending' && $request->acting_status == 'pending').'','cancelRoute' => ''.e(route('leave-requests.cancel', $request->id)).'','cancelId' => ''.e($request->id).'','cancelName' => ''.e($request->leaveType->name ?? 'Leave Request').'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('action-buttons'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['viewRoute' => ''.e(route('leave-requests.show', $request->id)).'','showCancel' => ''.e($request->status == 'pending' && $request->acting_status == 'pending').'','cancelRoute' => ''.e(route('leave-requests.cancel', $request->id)).'','cancelId' => ''.e($request->id).'','cancelName' => ''.e($request->leaveType->name ?? 'Leave Request').'']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf9332b595ad3d3a806f9da4dda8769dd)): ?>
<?php $attributes = $__attributesOriginalf9332b595ad3d3a806f9da4dda8769dd; ?>
<?php unset($__attributesOriginalf9332b595ad3d3a806f9da4dda8769dd); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf9332b595ad3d3a806f9da4dda8769dd)): ?>
<?php $component = $__componentOriginalf9332b595ad3d3a806f9da4dda8769dd; ?>
<?php unset($__componentOriginalf9332b595ad3d3a806f9da4dda8769dd); ?>
<?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="8" class="text-center py-3 text-muted" style="font-size: 0.7rem;">
                                No leave requests found
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer py-1 px-2">
            <?php if (isset($component)) { $__componentOriginal41032d87daf360242eb88dbda6c75ed1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal41032d87daf360242eb88dbda6c75ed1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.pagination','data' => ['items' => $leaveRequests,'perPageOptions' => [10, 25, 50, 100]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('pagination'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['items' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($leaveRequests),'perPageOptions' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([10, 25, 50, 100])]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal41032d87daf360242eb88dbda6c75ed1)): ?>
<?php $attributes = $__attributesOriginal41032d87daf360242eb88dbda6c75ed1; ?>
<?php unset($__attributesOriginal41032d87daf360242eb88dbda6c75ed1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal41032d87daf360242eb88dbda6c75ed1)): ?>
<?php $component = $__componentOriginal41032d87daf360242eb88dbda6c75ed1; ?>
<?php unset($__componentOriginal41032d87daf360242eb88dbda6c75ed1); ?>
<?php endif; ?>
        </div>
    </div>
</div>

<script>
function cancelRequest(id) {
    if(confirm('Are you sure you want to cancel this leave request?')) {
        fetch(`/leave-requests/${id}/cancel`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json'
            }
        }).then(response => response.json()).then(data => {
            if(data.success) {
                location.reload();
            } else {
                alert('Failed to cancel request');
            }
        });
    }
}
function showCancelModal(id, name) {
    document.getElementById('cancelConfirmBtn').href = '/leave-requests/' + id + '/cancel';
    new bootstrap.Modal(document.getElementById('cancelModal')).show();
}

// Keep this for backward compatibility (if needed)
function cancelRequest(id) {
    showCancelModal(id, '');
}
</script>

<!-- Cancel Request Modal -->
<div class="modal fade" id="cancelModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-sm">
            <div class="modal-header py-1 px-2">
                <h5 class="modal-title" style="font-size: 0.8rem;">Cancel Leave Request</h5>
                <button type="button" class="btn-close py-0" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body py-2 px-2">
                <p style="font-size: 0.75rem;">Are you sure you want to cancel this leave request?</p>
                <p class="text-muted mb-0" style="font-size: 0.7rem;">This action cannot be undone.</p>
            </div>
            <div class="modal-footer py-1 px-2">
                <button type="button" class="btn btn-secondary btn-sm rounded-sm" data-bs-dismiss="modal" style="font-size: 0.7rem;">Close</button>
                <a href="#" id="cancelConfirmBtn" class="btn btn-warning btn-sm rounded-sm" style="font-size: 0.7rem;">Cancel Request</a>
            </div>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\wamp64\www\unileave\resources\views/shared/leave-requests/my-requests.blade.php ENDPATH**/ ?>