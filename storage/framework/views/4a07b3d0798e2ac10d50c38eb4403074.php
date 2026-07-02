

<?php $__env->startSection('title', 'Pending Permission Approvals'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-2">
    
    <!-- Breadcrumb -->
    <?php if (isset($component)) { $__componentOriginale19f62b34dfe0bfdf95075badcb45bc2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale19f62b34dfe0bfdf95075badcb45bc2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.breadcrumb','data' => ['items' => [
        ['name' => 'Dashboard', 'url' => route('dashboard')],
        ['name' => 'Pending Approvals', 'url' => '#']
    ]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('breadcrumb'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['items' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
        ['name' => 'Dashboard', 'url' => route('dashboard')],
        ['name' => 'Pending Approvals', 'url' => '#']
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'PENDING PERMISSION APPROVALS','description' => 'Permission and travel requests awaiting your approval']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'PENDING PERMISSION APPROVALS','description' => 'Permission and travel requests awaiting your approval']); ?>
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
        $totalPending = ($pendingRequests->total() ?? 0) + ($travelPending->count() ?? 0);
        $permissionPending = $pendingRequests->total() ?? 0;
        $travelPendingCount = $travelPending->count() ?? 0;
    ?>
    
    <div class="row g-2 mb-3">
        <?php if (isset($component)) { $__componentOriginal8f216e051c231b98198765acd723fb77 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8f216e051c231b98198765acd723fb77 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stats-card','data' => ['value' => ''.e($totalPending).'','label' => 'Total Pending','subtext' => 'awaiting your decision','color' => 'warning']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('stats-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => ''.e($totalPending).'','label' => 'Total Pending','subtext' => 'awaiting your decision','color' => 'warning']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stats-card','data' => ['value' => ''.e($permissionPending).'','label' => 'Short Permissions','subtext' => 'awaiting approval','color' => 'info']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('stats-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => ''.e($permissionPending).'','label' => 'Short Permissions','subtext' => 'awaiting approval','color' => 'info']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stats-card','data' => ['value' => ''.e($travelPendingCount).'','label' => 'Travel Permissions','subtext' => 'awaiting approval','color' => 'primary']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('stats-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => ''.e($travelPendingCount).'','label' => 'Travel Permissions','subtext' => 'awaiting approval','color' => 'primary']); ?>
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
    
    <!-- Short Permissions Pending -->
    <div class="card rounded-sm mb-2">
        <div class="card-header py-1 px-2 bg-light">
            <div class="d-flex align-items-center">
                <i class="ti ti-clock text-warning me-1" style="font-size: 0.8rem;"></i>
                <span style="font-size: 0.7rem; font-weight: 600;">Pending Short Permissions</span>
                <?php if($permissionPending > 0): ?>
                <span class="badge bg-warning ms-2" style="font-size: 0.6rem;"><?php echo e($permissionPending); ?></span>
                <?php endif; ?>
            </div>
        </div>
        <div class="card-body p-0">
            <?php if($pendingRequests->count() > 0): ?>
            <div class="table-responsive">
                <table class="table table-sm mb-0" style="font-size: 0.75rem;">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-2 py-1">Staff</th>
                            <th class="py-1">Type</th>
                            <th class="py-1">Date & Time</th>
                            <th class="py-1">Hours</th>
                            <th class="py-1">Reason</th>
                            <th class="pe-2 py-1">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $pendingRequests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $request): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td class="ps-2 py-1">
                                <span class="fw-semibold"><?php echo e($request->staff->first_name); ?> <?php echo e($request->staff->last_name); ?></span>
                                <div class="text-muted" style="font-size: 0.6rem;"><?php echo e($request->staff->staff_id); ?></div>
                            </td>
                            <td class="py-1">
                                <span class="badge bg-info-subtle text-info rounded-sm" style="font-size: 0.6rem;">
                                    <?php echo e(str_replace('_', ' ', ucfirst($request->permission_type))); ?>

                                </span>
                            </td>
                            <td class="py-1">
                                <?php echo e(\Carbon\Carbon::parse($request->start_datetime)->format('d M Y H:i')); ?><br>
                                <small class="text-muted">to <?php echo e(\Carbon\Carbon::parse($request->end_datetime)->format('d M Y H:i')); ?></small>
                            </td>
                            <td class="py-1"><?php echo e($request->hours); ?> h</td>
                            <td class="py-1"><?php echo e(Str::limit($request->reason, 50)); ?></td>
                            <td class="pe-2 py-1">
                                <button class="btn btn-success btn-sm rounded-sm" data-bs-toggle="modal" data-bs-target="#approvePermModal<?php echo e($request->id); ?>" style="padding: 0.2rem 0.4rem; font-size: 0.6rem;">
                                    <i class="ti ti-check"></i> Approve
                                </button>
                                <button class="btn btn-danger btn-sm rounded-sm" data-bs-toggle="modal" data-bs-target="#rejectPermModal<?php echo e($request->id); ?>" style="padding: 0.2rem 0.4rem; font-size: 0.6rem;">
                                    <i class="ti ti-x"></i> Reject
                                </button>
                            </td>
                        </tr>
                        
                        <!-- Approve Modal -->
                        <div class="modal fade" id="approvePermModal<?php echo e($request->id); ?>" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content rounded-sm">
                                    <form action="<?php echo e(route('permissions.approve', $request->id)); ?>" method="POST">
                                        <?php echo csrf_field(); ?>
                                        <div class="modal-header py-1 px-2">
                                            <h5 class="modal-title" style="font-size: 0.8rem;">Approve Permission Request</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body py-2 px-2">
                                            <p style="font-size: 0.75rem;">Approve permission request from <strong><?php echo e($request->staff->first_name); ?> <?php echo e($request->staff->last_name); ?></strong>?</p>
                                            <p class="text-muted" style="font-size: 0.65rem;">
                                                Type: <?php echo e(str_replace('_', ' ', ucfirst($request->permission_type))); ?><br>
                                                Duration: <?php echo e($request->hours); ?> hours
                                            </p>
                                        </div>
                                        <div class="modal-footer py-1 px-2">
                                            <button type="button" class="btn btn-secondary btn-sm rounded-sm" data-bs-dismiss="modal" style="font-size: 0.65rem;">Cancel</button>
                                            <button type="submit" class="btn btn-success btn-sm rounded-sm" style="font-size: 0.65rem;">Approve</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Reject Modal -->
                        <div class="modal fade" id="rejectPermModal<?php echo e($request->id); ?>" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content rounded-sm">
                                    <form action="<?php echo e(route('permissions.reject', $request->id)); ?>" method="POST">
                                        <?php echo csrf_field(); ?>
                                        <div class="modal-header py-1 px-2">
                                            <h5 class="modal-title" style="font-size: 0.8rem;">Reject Permission Request</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body py-2 px-2">
                                            <p style="font-size: 0.75rem;">Reject permission request from <strong><?php echo e($request->staff->first_name); ?> <?php echo e($request->staff->last_name); ?></strong>?</p>
                                            <div class="mb-2">
                                                <label class="form-label" style="font-size: 0.65rem;">Reason <span class="text-danger">*</span></label>
                                                <textarea name="rejection_reason" class="form-control form-control-sm rounded-sm" rows="2" style="font-size: 0.7rem;" required></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer py-1 px-2">
                                            <button type="button" class="btn btn-secondary btn-sm rounded-sm" data-bs-dismiss="modal" style="font-size: 0.65rem;">Cancel</button>
                                            <button type="submit" class="btn btn-danger btn-sm rounded-sm" style="font-size: 0.65rem;">Reject</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="text-center py-3">
                <i class="ti ti-clock" style="font-size: 1.5rem; color: #CBD5E1;"></i>
                <p class="text-muted mt-1" style="font-size: 0.7rem;">No pending short permission requests</p>
            </div>
            <?php endif; ?>
        </div>
        <?php if($pendingRequests->hasPages()): ?>
        <div class="card-footer py-1 px-2">
            <?php echo e($pendingRequests->links('pagination::bootstrap-5')); ?>

        </div>
        <?php endif; ?>
    </div>
    
    <!-- Travel Permissions Pending -->
    <div class="card rounded-sm">
        <div class="card-header py-1 px-2 bg-light">
            <div class="d-flex align-items-center">
                <i class="ti ti-plane text-primary me-1" style="font-size: 0.8rem;"></i>
                <span style="font-size: 0.7rem; font-weight: 600;">Pending Travel Approvals</span>
                <?php if($travelPendingCount > 0): ?>
                <span class="badge bg-warning ms-2" style="font-size: 0.6rem;"><?php echo e($travelPendingCount); ?></span>
                <?php endif; ?>
            </div>
        </div>
        <div class="card-body p-0">
            <?php if($travelPending->count() > 0): ?>
            <div class="table-responsive">
                <table class="table table-sm mb-0" style="font-size: 0.75rem;">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-2 py-1">Staff</th>
                            <th class="py-1">Destination</th>
                            <th class="py-1">Travel Period</th>
                            <th class="py-1">Days</th>
                            <th class="py-1">Reason</th>
                            <th class="py-1">Cost Coverage</th>
                            <th class="pe-2 py-1">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $travelPending; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $request): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td class="ps-2 py-1">
                                <span class="fw-semibold"><?php echo e($request->staff->first_name); ?> <?php echo e($request->staff->last_name); ?></span>
                                <div class="text-muted" style="font-size: 0.6rem;"><?php echo e($request->staff->staff_id); ?></div>
                            </td>
                            <td class="py-1"><?php echo e($request->destination); ?></td>
                            <td class="py-1">
                                <?php echo e(\Carbon\Carbon::parse($request->start_date)->format('d M Y')); ?><br>
                                <small class="text-muted">to <?php echo e(\Carbon\Carbon::parse($request->end_date)->format('d M Y')); ?></small>
                            </td>
                            <td class="py-1"><?php echo e($request->days); ?></td>
                            <td class="py-1"><?php echo e(ucfirst($request->travel_reason)); ?></td>
                            <td class="py-1">
                                <?php
                                    $coverageLabels = [
                                        'organizers_full' => 'Organizers Full',
                                        'organizers_partial' => 'Organizers Partial',
                                        'university' => 'University',
                                        'private' => 'Private'
                                    ];
                                ?>
                                <span class="badge bg-secondary-subtle text-secondary rounded-sm" style="font-size: 0.55rem;">
                                    <?php echo e($coverageLabels[$request->cost_coverage] ?? $request->cost_coverage); ?>

                                </span>
                            </td>
                            <td class="pe-2 py-1">
                                <a href="<?php echo e(route('travel-permissions.pending')); ?>" class="btn btn-primary btn-sm rounded-sm" style="padding: 0.2rem 0.4rem; font-size: 0.6rem;">
                                    <i class="ti ti-eye"></i> Review
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="text-center py-3">
                <i class="ti ti-plane" style="font-size: 1.5rem; color: #CBD5E1;"></i>
                <p class="text-muted mt-1" style="font-size: 0.7rem;">No pending travel permission requests</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.modal-content {
    border: none;
}
</style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\wamp64\www\unileave\resources\views/shared/permissions/pending-approvals.blade.php ENDPATH**/ ?>