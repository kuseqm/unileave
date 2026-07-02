

<?php $__env->startSection('title', 'Pending Approvals'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-2">
    
    <!-- Breadcrumb -->
    <?php if (isset($component)) { $__componentOriginale19f62b34dfe0bfdf95075badcb45bc2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale19f62b34dfe0bfdf95075badcb45bc2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.breadcrumb','data' => ['items' => [
        ['name' => 'Pending Approvals', 'url' => '#']
    ]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('breadcrumb'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['items' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'PENDING APPROVALS','description' => 'Review and process leave requests']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'PENDING APPROVALS','description' => 'Review and process leave requests']); ?>
        <!-- No action buttons needed -->
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
    
    <!-- Stats Cards - Use integer comparisons -->
    <div class="row g-2 mb-3">
        <?php if (isset($component)) { $__componentOriginal8f216e051c231b98198765acd723fb77 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8f216e051c231b98198765acd723fb77 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stats-card','data' => ['value' => ''.e($pendingRequests->total()).'','label' => 'Total Pending','subtext' => 'awaiting your action','color' => 'warning']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('stats-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => ''.e($pendingRequests->total()).'','label' => 'Total Pending','subtext' => 'awaiting your action','color' => 'warning']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stats-card','data' => ['value' => ''.e($pendingRequests->where('current_approval_level', 1)->count()).'','label' => 'HOD Level','subtext' => 'department review','color' => 'info']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('stats-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => ''.e($pendingRequests->where('current_approval_level', 1)->count()).'','label' => 'HOD Level','subtext' => 'department review','color' => 'info']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stats-card','data' => ['value' => ''.e($pendingRequests->where('current_approval_level', 2)->count()).'','label' => 'Dean Level','subtext' => 'school review','color' => 'primary']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('stats-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => ''.e($pendingRequests->where('current_approval_level', 2)->count()).'','label' => 'Dean Level','subtext' => 'school review','color' => 'primary']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stats-card','data' => ['value' => ''.e($pendingRequests->where('current_approval_level', 3)->count()).'','label' => 'HR Level','subtext' => 'HR review','color' => 'secondary']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('stats-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => ''.e($pendingRequests->where('current_approval_level', 3)->count()).'','label' => 'HR Level','subtext' => 'HR review','color' => 'secondary']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stats-card','data' => ['value' => ''.e($pendingRequests->where('current_approval_level', 4)->count()).'','label' => 'DVC Level','subtext' => 'university review','color' => 'success']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('stats-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => ''.e($pendingRequests->where('current_approval_level', 4)->count()).'','label' => 'DVC Level','subtext' => 'university review','color' => 'success']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stats-card','data' => ['value' => ''.e($pendingRequests->where('current_approval_level', 5)->count()).'','label' => 'HR Logistics','subtext' => 'final processing','color' => 'dark']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('stats-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => ''.e($pendingRequests->where('current_approval_level', 5)->count()).'','label' => 'HR Logistics','subtext' => 'final processing','color' => 'dark']); ?>
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
    
    <!-- Pending Approvals Table -->
    <div class="card rounded-sm">
        <div class="card-header py-1 px-2 bg-light">
            <div class="d-flex align-items-center">
                <i class="ti ti-clock text-primary me-1" style="font-size: 0.8rem;"></i>
                <span style="font-size: 0.7rem; font-weight: 600;">Requests Awaiting Approval</span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm mb-0" style="font-size: 0.75rem;">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-2 py-1">#</th>
                            <th class="py-1">Staff</th>
                            <th class="py-1">Leave Type</th>
                            <th class="py-1">Dates</th>
                            <th class="py-1">Days</th>
                            <th class="py-1">Reason</th>
                            <th class="py-1">Current Level</th>
                            <th class="pe-2 py-1">Actions</th>
                        <tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $pendingRequests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $request): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $role = auth()->user()->getRoleNames()->first();
                            $currentLevel = $request->current_approval_level;
                        ?>
                        <tr>
                            <td class="ps-2 py-1"><?php echo e($pendingRequests->firstItem() + $index); ?></td>
                            <td class="py-1">
                                <div class="d-flex align-items-center gap-1">
                                    <div class="rounded-circle bg-light d-flex align-items-center justify-content-center" style="width: 24px; height: 24px;">
                                        <span class="fw-semibold" style="font-size: 0.6rem;"><?php echo e(strtoupper(substr($request->staff->first_name, 0, 1))); ?><?php echo e(strtoupper(substr($request->staff->last_name, 0, 1))); ?></span>
                                    </div>
                                    <div>
                                        <span class="fw-semibold"><?php echo e($request->staff->first_name); ?> <?php echo e($request->staff->last_name); ?></span>
                                        <div class="text-muted" style="font-size: 0.6rem;"><?php echo e($request->staff->staff_id); ?></div>
                                    </div>
                                    <!-- Staff Dossier Button -->
                                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-sm" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#staffDossierModal<?php echo e($request->staff->id); ?>"
                                            title="View Staff Dossier" style="padding: 0.1rem 0.3rem;">
                                        <i class="ti ti-file-info" style="font-size: 0.65rem;"></i>
                                    </button>
                                </div>
                            </td>
                            <td class="py-1"><?php echo e($request->leaveType->name ?? 'N/A'); ?><br><small class="text-muted"><?php echo e($request->leaveType->code ?? ''); ?></small></td>
                            <td class="py-1"><?php echo e(\Carbon\Carbon::parse($request->start_date)->format('d M Y')); ?><br><small>to</small><br><?php echo e(\Carbon\Carbon::parse($request->end_date)->format('d M Y')); ?></td>
                            <td class="py-1"><?php echo e($request->days); ?> day(s)</td>
                            <td class="py-1"><?php echo e(\Str::limit($request->reason, 50)); ?></td>
                            <td class="py-1">
                                <?php
                                    $levelNames = [
                                        1 => 'HOD',
                                        2 => 'Dean',
                                        3 => 'HR',
                                        4 => 'DVC',
                                        5 => 'HR Logistics'
                                    ];
                                    $level = $request->current_approval_level;
                                ?>
                                <?php if($level == 1): ?>
                                    <span class="badge bg-info-subtle text-info rounded-sm" style="font-size: 0.6rem;">HOD</span>
                                <?php elseif($level == 2): ?>
                                    <span class="badge bg-primary-subtle text-primary rounded-sm" style="font-size: 0.6rem;">Dean</span>
                                <?php elseif($level == 3): ?>
                                    <span class="badge bg-secondary-subtle text-secondary rounded-sm" style="font-size: 0.6rem;">HR</span>
                                <?php elseif($level == 4): ?>
                                    <span class="badge bg-success-subtle text-success rounded-sm" style="font-size: 0.6rem;">DVC</span>
                                <?php elseif($level == 5): ?>
                                    <span class="badge bg-dark-subtle text-dark rounded-sm" style="font-size: 0.6rem;">HR Logistics</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary-subtle text-secondary rounded-sm" style="font-size: 0.6rem;"><?php echo e($levelNames[$level] ?? 'Unknown'); ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="pe-2 py-1">
                                <div class="d-flex gap-1">
                                    <!-- For HR Level 3 - Show HR Review Button -->
                                    <?php if($currentLevel == 3 && auth()->user()->hasRole('hr-officer')): ?>
                                        <a href="<?php echo e(route('leave-requests.hr-review', $request->id)); ?>" class="btn btn-sm btn-primary rounded-sm" title="HR Review" style="padding: 0.15rem 0.3rem;">
                                            <i class="ti ti-clipboard-list" style="font-size: 0.7rem;"></i>
                                        </a>
                                    <!-- For HR Logistics Level 5 - Show Logistics Button -->
                                    <?php elseif($currentLevel == 5 && auth()->user()->hasRole('hr-officer')): ?>
                                        <a href="<?php echo e(route('leave-requests.hr-logistics', $request->id)); ?>" class="btn btn-sm btn-info rounded-sm" title="HR Logistics" style="padding: 0.15rem 0.3rem;">
                                            <i class="ti ti-package" style="font-size: 0.7rem;"></i>
                                        </a>
                                    <!-- For DVC Level 4 - Show DVC Review Button -->
                                    <?php elseif($currentLevel == 4 && (auth()->user()->hasRole('dvca') || auth()->user()->hasRole('dvc-pfa'))): ?>
                                        <a href="<?php echo e(route('leave-requests.dvc-review', $request->id)); ?>" class="btn btn-sm btn-primary rounded-sm" title="DVC Review" style="padding: 0.15rem 0.3rem;">
                                            <i class="ti ti-crown" style="font-size: 0.7rem;"></i>
                                        </a>
                                    <?php else: ?>
                                        <!-- Regular Approve Button -->
                                        <button type="button" class="btn btn-sm btn-success rounded-sm" onclick="openApproveModal(<?php echo e($request->id); ?>)" title="Approve" style="padding: 0.15rem 0.3rem;">
                                            <i class="ti ti-check" style="font-size: 0.7rem;"></i>
                                        </button>
                                        <!-- Regular Reject Button -->
                                        <button type="button" class="btn btn-sm btn-danger rounded-sm" onclick="openRejectModal(<?php echo e($request->id); ?>)" title="Reject" style="padding: 0.15rem 0.3rem;">
                                            <i class="ti ti-x" style="font-size: 0.7rem;"></i>
                                        </button>
                                    <?php endif; ?>
                                    
                                    <!-- View Details Button (always visible) -->
                                    <a href="<?php echo e(route('leave-requests.show', $request->id)); ?>" class="btn btn-sm btn-outline-info rounded-sm" title="View Details" style="padding: 0.15rem 0.3rem;">
                                        <i class="ti ti-eye" style="font-size: 0.7rem;"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="8" class="text-center py-3 text-muted" style="font-size: 0.7rem;">
                                No pending approvals
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.pagination','data' => ['items' => $pendingRequests,'perPageOptions' => [10, 25, 50, 100]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('pagination'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['items' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($pendingRequests),'perPageOptions' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([10, 25, 50, 100])]); ?>
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

<!-- Approve Modal -->
<div class="modal fade" id="approveModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-sm">
            <div class="modal-header py-1 px-2">
                <h5 class="modal-title" style="font-size: 0.8rem;">Approve Leave Request</h5>
                <button type="button" class="btn-close py-0" data-bs-dismiss="modal"></button>
            </div>
            <form id="approveForm" method="POST">
                <?php echo csrf_field(); ?>
                <div class="modal-body py-2 px-2">
                    <p style="font-size: 0.75rem;">Are you sure you want to approve this leave request?</p>
                    <div class="mb-1">
                        <label class="form-label" style="font-size: 0.65rem;">Comment (Optional)</label>
                        <textarea name="comment" class="form-control form-control-sm rounded-sm" rows="2" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;"></textarea>
                    </div>
                </div>
                <div class="modal-footer py-1 px-2">
                    <button type="button" class="btn btn-secondary btn-sm rounded-sm" data-bs-dismiss="modal" style="font-size: 0.7rem;">Cancel</button>
                    <button type="submit" class="btn btn-success btn-sm rounded-sm" style="font-size: 0.7rem;">Approve</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-sm">
            <div class="modal-header py-1 px-2">
                <h5 class="modal-title" style="font-size: 0.8rem;">Reject Leave Request</h5>
                <button type="button" class="btn-close py-0" data-bs-dismiss="modal"></button>
            </div>
            <form id="rejectForm" method="POST">
                <?php echo csrf_field(); ?>
                <div class="modal-body py-2 px-2">
                    <p style="font-size: 0.75rem;">Are you sure you want to reject this leave request?</p>
                    <div class="mb-1">
                        <label class="form-label" style="font-size: 0.65rem;">Reason for Rejection <span class="text-danger">*</span></label>
                        <textarea name="rejection_reason" class="form-control form-control-sm rounded-sm" rows="3" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;" required></textarea>
                    </div>
                </div>
                <div class="modal-footer py-1 px-2">
                    <button type="button" class="btn btn-secondary btn-sm rounded-sm" data-bs-dismiss="modal" style="font-size: 0.7rem;">Cancel</button>
                    <button type="submit" class="btn btn-danger btn-sm rounded-sm" style="font-size: 0.7rem;">Reject</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Staff Dossier Modals -->
<?php $__currentLoopData = $pendingRequests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $request): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<div class="modal fade" id="staffDossierModal<?php echo e($request->staff->id); ?>" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-sm">
            <div class="modal-header py-1 px-2 bg-light">
                <h5 class="modal-title" style="font-size: 0.8rem;">Staff Dossier - <?php echo e($request->staff->full_name); ?></h5>
                <button type="button" class="btn-close py-0" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-2">
                <?php if (isset($component)) { $__componentOriginale9a49249344409f0c17bde4c21ee9078 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale9a49249344409f0c17bde4c21ee9078 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.staff-dossier','data' => ['staff' => $request->staff]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('staff-dossier'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['staff' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($request->staff)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale9a49249344409f0c17bde4c21ee9078)): ?>
<?php $attributes = $__attributesOriginale9a49249344409f0c17bde4c21ee9078; ?>
<?php unset($__attributesOriginale9a49249344409f0c17bde4c21ee9078); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale9a49249344409f0c17bde4c21ee9078)): ?>
<?php $component = $__componentOriginale9a49249344409f0c17bde4c21ee9078; ?>
<?php unset($__componentOriginale9a49249344409f0c17bde4c21ee9078); ?>
<?php endif; ?>
            </div>
            <div class="modal-footer py-1 px-2">
                <button type="button" class="btn btn-secondary btn-sm rounded-sm" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

<script>
function openApproveModal(id) {
    const form = document.getElementById('approveForm');
    form.action = `/leave-requests/${id}/approve`;
    new bootstrap.Modal(document.getElementById('approveModal')).show();
}

function openRejectModal(id) {
    const form = document.getElementById('rejectForm');
    form.action = `/leave-requests/${id}/reject`;
    new bootstrap.Modal(document.getElementById('rejectModal')).show();
}
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\wamp64\www\unileave\resources\views/shared/leave-requests/pending-approvals.blade.php ENDPATH**/ ?>