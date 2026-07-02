

<?php $__env->startSection('title', 'Incoming Letter Details'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-2">
    
    <!-- Breadcrumb -->
    <?php if (isset($component)) { $__componentOriginale19f62b34dfe0bfdf95075badcb45bc2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale19f62b34dfe0bfdf95075badcb45bc2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.breadcrumb','data' => ['items' => [
        ['name' => 'Central Registry', 'url' => '#'],
        ['name' => 'Incoming Letters', 'url' => route('registry.incoming.index')],
        ['name' => 'Letter Details', 'url' => '#']
    ]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('breadcrumb'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['items' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
        ['name' => 'Central Registry', 'url' => '#'],
        ['name' => 'Incoming Letters', 'url' => route('registry.incoming.index')],
        ['name' => 'Letter Details', 'url' => '#']
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'INCOMING LETTER DETAILS','description' => 'Reference: ' . $letter->ref_number]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'INCOMING LETTER DETAILS','description' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('Reference: ' . $letter->ref_number)]); ?>
        <div class="d-flex gap-1">
            <a href="<?php echo e(route('registry.incoming.index')); ?>" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
                <i class="ti ti-arrow-left me-1"></i> Back
            </a>
            <a href="<?php echo e(route('registry.incoming.edit', $letter->id)); ?>" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
                <i class="ti ti-edit me-1"></i> Edit
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
    
    <div class="row g-2">
        <!-- Main Details Column -->
        <div class="col-lg-7">
            <!-- Letter Information Card -->
            <div class="card rounded-sm mb-2">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-file-text text-primary me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Letter Information</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <div class="row g-1">
                        <div class="col-md-6">
                            <div class="text-muted" style="font-size: 0.6rem;">Reference Number</div>
                            <div class="fw-semibold" style="font-size: 0.75rem;"><?php echo e($letter->ref_number); ?></div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted" style="font-size: 0.6rem;">Date Received</div>
                            <div style="font-size: 0.75rem;"><?php echo e($letter->date_received->format('d M Y')); ?></div>
                        </div>
                    </div>
                    
                    <div class="row g-1 mt-1">
                        <div class="col-12">
                            <div class="text-muted" style="font-size: 0.6rem;">Sender</div>
                            <div style="font-size: 0.75rem;"><?php echo e($letter->sender); ?></div>
                        </div>
                    </div>
                    
                    <div class="row g-1 mt-1">
                        <div class="col-md-6">
                            <div class="text-muted" style="font-size: 0.6rem;">Addressee Office</div>
                            <div style="font-size: 0.75rem;"><?php echo e($letter->office->name); ?></div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted" style="font-size: 0.6rem;">Priority</div>
                            <div>
                                <?php if($letter->priority == 'urgent'): ?>
                                    <span class="badge bg-danger-subtle text-danger rounded-sm" style="font-size: 0.6rem;">Urgent</span>
                                <?php elseif($letter->priority == 'confidential'): ?>
                                    <span class="badge bg-dark-subtle text-dark rounded-sm" style="font-size: 0.6rem;">Confidential</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary-subtle text-secondary rounded-sm" style="font-size: 0.6rem;">Normal</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-2">
                        <div class="text-muted" style="font-size: 0.6rem;">Subject</div>
                        <div class="p-1 bg-light rounded-sm" style="font-size: 0.7rem;"><?php echo e($letter->subject); ?></div>
                    </div>
                    
                    <?php if($letter->remarks): ?>
                    <div class="mt-2">
                        <div class="text-muted" style="font-size: 0.6rem;">Remarks</div>
                        <div class="p-1 bg-light rounded-sm" style="font-size: 0.7rem;"><?php echo e($letter->remarks); ?></div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if($letter->pdf_path): ?>
                    <div class="mt-2">
                        <div class="text-muted" style="font-size: 0.6rem;">Attached Document</div>
                        <a href="<?php echo e(asset('storage/' . $letter->pdf_path)); ?>" target="_blank" class="btn btn-sm btn-outline-primary rounded-sm mt-1" style="font-size: 0.65rem; padding: 0.15rem 0.4rem;">
                            <i class="ti ti-file-pdf me-1"></i> View PDF
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Response Letters Card -->
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-mail-forward text-primary me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Response Letters</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <?php if($letter->outgoingLetters->count() > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-sm mb-0" style="font-size: 0.65rem;">
                                <thead class="table-light">
                                    <tr>
                                        <th>Ref Number</th>
                                        <th>Recipient</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $letter->outgoingLetters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $outgoing): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><strong><?php echo e($outgoing->ref_number); ?></strong></td>
                                        <td><?php echo e(\Illuminate\Support\Str::limit($outgoing->recipient, 25)); ?></td>
                                        <td>
                                            <?php if($outgoing->status == 'draft'): ?>
                                                <span class="badge bg-secondary-subtle text-secondary rounded-sm" style="font-size: 0.55rem;">Draft</span>
                                            <?php elseif($outgoing->status == 'dispatched'): ?>
                                                <span class="badge bg-success-subtle text-success rounded-sm" style="font-size: 0.55rem;">Dispatched</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning-subtle text-warning rounded-sm" style="font-size: 0.55rem;"><?php echo e(ucfirst($outgoing->status)); ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo e($outgoing->created_at->format('d/m/Y')); ?></td>
                                        <td>
                                            <a href="<?php echo e(route('registry.outgoing.show', $outgoing->id)); ?>" class="btn btn-sm btn-primary rounded-sm" style="font-size: 0.55rem; padding: 0.1rem 0.3rem;">
                                                <i class="ti ti-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-3 text-muted" style="font-size: 0.65rem;">
                            <i class="ti ti-mail"></i> No response letters yet.
                        </div>
                    <?php endif; ?>
                </div>
                <div class="card-footer py-1 px-2 bg-light">
                    <a href="<?php echo e(route('registry.outgoing.create', ['incoming_id' => $letter->id])); ?>" class="btn btn-success btn-sm rounded-sm w-100" style="font-size: 0.65rem; padding: 0.2rem 0.5rem;">
                        <i class="ti ti-plus me-1"></i> Create Response Letter
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Status & Assignment Column -->
        <div class="col-lg-5">
            <!-- Status Card -->
            <div class="card rounded-sm mb-2">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-info-circle text-primary me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Status</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <div class="row g-1">
                        <div class="col-12">
                            <div class="text-muted" style="font-size: 0.6rem;">Current Status</div>
                            <div class="mt-1">
                                <?php if($letter->status == 'received'): ?>
                                    <span class="badge bg-warning-subtle text-warning rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">Received</span>
                                <?php elseif($letter->status == 'assigned'): ?>
                                    <span class="badge bg-info-subtle text-info rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">Assigned</span>
                                <?php elseif($letter->status == 'in_progress'): ?>
                                    <span class="badge bg-primary-subtle text-primary rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">In Progress</span>
                                <?php elseif($letter->status == 'replied'): ?>
                                    <span class="badge bg-success-subtle text-success rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">Replied</span>
                                <?php elseif($letter->status == 'closed'): ?>
                                    <span class="badge bg-secondary-subtle text-secondary rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">Closed</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <hr class="my-2" style="border-color: #E8E2D5;">
                    
                    <form method="POST" action="<?php echo e(route('registry.incoming.status', $letter->id)); ?>">
                        <?php echo csrf_field(); ?>
                        <div class="mb-2">
                            <label class="form-label text-muted" style="font-size: 0.6rem;">Update Status</label>
                            <select name="status" class="form-select form-select-sm" style="font-size: 0.7rem;">
                                <option value="received" <?php echo e($letter->status == 'received' ? 'selected' : ''); ?>>Received</option>
                                <option value="assigned" <?php echo e($letter->status == 'assigned' ? 'selected' : ''); ?>>Assigned</option>
                                <option value="in_progress" <?php echo e($letter->status == 'in_progress' ? 'selected' : ''); ?>>In Progress</option>
                                <option value="replied" <?php echo e($letter->status == 'replied' ? 'selected' : ''); ?>>Replied</option>
                                <option value="closed" <?php echo e($letter->status == 'closed' ? 'selected' : ''); ?>>Closed</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm w-100 rounded-sm" style="font-size: 0.65rem; padding: 0.2rem 0.5rem;">
                            <i class="ti ti-refresh me-1"></i> Update Status
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- Assignment Card -->
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-user-check text-primary me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Assignment</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <div class="mb-2">
                        <div class="text-muted" style="font-size: 0.6rem;">Assigned To</div>
                        <div class="fw-semibold" style="font-size: 0.75rem;">
                            <?php if($letter->assignedTo): ?>
                                <?php echo e($letter->assignedTo->first_name ?? ''); ?> <?php echo e($letter->assignedTo->last_name ?? $letter->assignedTo->name ?? ''); ?>

                            <?php else: ?>
                                <span class="text-muted">Not assigned</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <form method="POST" action="<?php echo e(route('registry.incoming.assign', $letter->id)); ?>">
                        <?php echo csrf_field(); ?>
                        <div class="mb-2">
                            <label class="form-label text-muted" style="font-size: 0.6rem;">Assign to Officer</label>
                            <select name="assigned_to" class="form-select form-select-sm" style="font-size: 0.7rem;">
                                <option value="">-- Select Officer --</option>
                                <?php $__currentLoopData = $availableOfficers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $officer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($officer->id); ?>" <?php echo e($letter->assigned_to == $officer->id ? 'selected' : ''); ?>>
                                    <?php echo e($officer->first_name ?? ''); ?> <?php echo e($officer->last_name ?? $officer->name ?? ''); ?>

                                </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm w-100 rounded-sm" style="font-size: 0.65rem; padding: 0.2rem 0.5rem;">
                            <i class="ti ti-user-plus me-1"></i> Assign
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\wamp64\www\unileave\resources\views/registry/incoming/show.blade.php ENDPATH**/ ?>