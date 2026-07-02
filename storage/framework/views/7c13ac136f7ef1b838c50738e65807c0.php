

<?php $__env->startSection('title', 'Secure Message View'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-2">
    <!-- Breadcrumb -->
<?php if (isset($component)) { $__componentOriginale19f62b34dfe0bfdf95075badcb45bc2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale19f62b34dfe0bfdf95075badcb45bc2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.breadcrumb','data' => ['items' => [
    ['name' => 'Messages', 'url' => route('messages.inbox')],
    ['name' => 'Message Directory', 'url' => route('messages.admin-list')],
    ['name' => 'Secure View', 'url' => '#']
    ]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('breadcrumb'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['items' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
    ['name' => 'Messages', 'url' => route('messages.inbox')],
    ['name' => 'Message Directory', 'url' => route('messages.admin-list')],
    ['name' => 'Secure View', 'url' => '#']
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'SECURE MESSAGE VIEW','description' => 'Authorized access - Viewing encrypted message content']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'SECURE MESSAGE VIEW','description' => 'Authorized access - Viewing encrypted message content']); ?>
        <a href="<?php echo e(route('messages.metadata', $message->id)); ?>" class="btn btn-outline-secondary btn-sm rounded-sm">
            <i class="ti ti-arrow-left me-1"></i> Back to Metadata
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

    <div class="row">
        <div class="col-lg-8">
            <div class="card rounded-sm">
                <div class="card-header py-2 px-3 bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <i class="ti ti-mail me-1"></i>
                            <span class="fw-semibold"><?php echo e($message->subject); ?></span>
                        </div>
                        <div>
                            <span class="badge bg-success rounded-pill">Authorized Access</span>
                        </div>
                    </div>
                </div>
                
                <div class="card-body p-4">
                    <!-- Access Notice -->
                    <div class="alert alert-info small mb-3">
                        <i class="ti ti-shield me-1"></i>
                        <strong>Authorized Access</strong> - You are viewing this message with special authorization.
                        <?php if(isset($accessRequest) && $accessRequest): ?>
                            <br>Access granted via one-time code on <?php echo e($accessRequest->viewed_at ? $accessRequest->viewed_at->format('d M Y, H:i:s') : 'recently'); ?>.
                        <?php endif; ?>
                    </div>

                    <!-- Message Header -->
                    <div class="border-bottom pb-2 mb-3">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="text-muted small">From:</div>
                                <div class="fw-semibold"><?php echo e($message->sender->first_name ?? 'System'); ?> <?php echo e($message->sender->last_name ?? ''); ?></div>
                                <div class="text-muted small"><?php echo e($message->sender->email ?? ''); ?></div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted small">To:</div>
                                <div class="fw-semibold"><?php echo e($message->recipient_names ?? $message->receiver->first_name ?? 'Unknown'); ?></div>
                            </div>
                        </div>
                    </div>

                    <!-- Date Info -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="text-muted small">
                                <i class="ti ti-calendar me-1"></i> Sent: <?php echo e($message->created_at->format('d M Y, H:i:s')); ?>

                            </div>
                        </div>
                        <div class="col-md-6">
                            <?php if($message->read_at): ?>
                                <div class="text-muted small">
                                    <i class="ti ti-eye me-1"></i> Read: <?php echo e($message->read_at->format('d M Y, H:i:s')); ?>

                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Message Body -->
                    <div class="mb-4">
                        <strong>Message:</strong>
                        <div class="mt-2 p-3 bg-light rounded" style="line-height: 1.6;">
                            <?php echo nl2br(e($message->message)); ?>

                        </div>
                    </div>

                    <!-- Attachments -->
                    <?php if($message->attachments && $message->attachments->count() > 0): ?>
                        <div class="mb-3">
                            <strong><i class="ti ti-paperclip me-1"></i> Attachments:</strong>
                            <ul class="mt-1">
                                <?php $__currentLoopData = $message->attachments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attachment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li>
                                        <a href="<?php echo e(Storage::url($attachment->file_path)); ?>" target="_blank">
                                            <?php echo e($attachment->file_name); ?>

                                        </a>
                                        <span class="text-muted">(<?php echo e(number_format($attachment->file_size / 1024, 2)); ?> KB)</span>
                                    </li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <!-- Security Notice -->
                    <div class="alert alert-warning small mt-3">
                        <i class="ti ti-alert-triangle me-1"></i>
                        <strong>Security Notice:</strong> This access has been logged for audit purposes. Any unauthorized sharing of this content is prohibited.
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN: Context Panel (30%) -->
        <div class="col-lg-4">
            <div class="card rounded-sm border-0 shadow-sm">
                <div class="card-header py-2 px-3 bg-light">
                    <i class="ti ti-info-circle me-1"></i>
                    <span class="fw-semibold">Access Information</span>
                </div>
                <div class="card-body p-3">
                    <div class="mb-3">
                        <i class="ti ti-key text-success me-1"></i>
                        <strong class="small">Access Method</strong>
                        <p class="text-muted small mt-1 mb-0">You are viewing this message with authorized access granted by the Vice Chancellor.</p>
                    </div>
                    
                    <div class="mb-3">
                        <i class="ti ti-history text-info me-1"></i>
                        <strong class="small">Audit Trail</strong>
                        <p class="text-muted small mt-1 mb-0">This access has been recorded in the audit log for compliance purposes.</p>
                    </div>
                    
                    <div class="alert alert-secondary small py-2 px-2 mb-0">
                        <i class="ti ti-lock me-1"></i>
                        <strong>Need to request access?</strong>
                        <p class="mb-0 mt-1">HR officers can request access via the <a href="<?php echo e(route('messages.metadata', $message->id)); ?>">Metadata page</a>.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\wamp64\www\unileave\resources\views/shared/messages/secure-message-content.blade.php ENDPATH**/ ?>