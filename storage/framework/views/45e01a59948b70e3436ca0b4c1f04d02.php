

<?php $__env->startSection('title', 'New Outgoing Letter'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-2">
    
    <!-- Breadcrumb -->
    <?php if (isset($component)) { $__componentOriginale19f62b34dfe0bfdf95075badcb45bc2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale19f62b34dfe0bfdf95075badcb45bc2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.breadcrumb','data' => ['items' => [
        ['name' => 'Central Registry', 'url' => '#'],
        ['name' => 'Outgoing Letters', 'url' => route('registry.outgoing.index')],
        ['name' => 'New Letter', 'url' => '#']
    ]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('breadcrumb'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['items' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
        ['name' => 'Central Registry', 'url' => '#'],
        ['name' => 'Outgoing Letters', 'url' => route('registry.outgoing.index')],
        ['name' => 'New Letter', 'url' => '#']
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'NEW OUTGOING LETTER','description' => 'Draft a new outgoing letter']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'NEW OUTGOING LETTER','description' => 'Draft a new outgoing letter']); ?>
        <a href="<?php echo e(route('registry.outgoing.index')); ?>" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
            <i class="ti ti-arrow-left me-1"></i> Cancel
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
    
    <!-- Form Card -->
    <div class="card rounded-sm">
        <div class="card-header py-1 px-2 bg-light">
            <div class="d-flex align-items-center">
                <i class="ti ti-mail-forward text-primary me-1" style="font-size: 0.8rem;"></i>
                <span style="font-size: 0.7rem; font-weight: 600;">Letter Details</span>
            </div>
        </div>
        <div class="card-body py-2 px-2">
            <?php if(isset($incomingLetter)): ?>
                <div class="alert alert-info py-1 px-2 mb-2 rounded-sm" style="font-size: 0.65rem;">
                    <i class="ti ti-info-circle me-1"></i> 
                    Responding to: <strong><?php echo e($incomingLetter->ref_number); ?></strong> - <?php echo e($incomingLetter->sender); ?>

                </div>
            <?php endif; ?>
            
            <form method="POST" action="<?php echo e(route('registry.outgoing.store')); ?>">
                <?php echo csrf_field(); ?>
                <?php if(isset($incomingLetter)): ?>
                    <input type="hidden" name="incoming_letter_id" value="<?php echo e($incomingLetter->id); ?>">
                <?php endif; ?>
                
                <div class="mb-2">
                    <label class="form-label text-muted" style="font-size: 0.6rem;">Recipient <span class="text-danger">*</span></label>
                    <input type="text" name="recipient" class="form-control form-control-sm <?php $__errorArgs = ['recipient'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('recipient')); ?>" style="font-size: 0.7rem;" required>
                    <?php $__errorArgs = ['recipient'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback" style="font-size: 0.6rem;"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                
                <div class="mb-2">
                    <label class="form-label text-muted" style="font-size: 0.6rem;">Subject <span class="text-danger">*</span></label>
                    <input type="text" name="subject" class="form-control form-control-sm <?php $__errorArgs = ['subject'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('subject', isset($incomingLetter) ? 'RE: ' . $incomingLetter->subject : '')); ?>" style="font-size: 0.7rem;" required>
                    <?php $__errorArgs = ['subject'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback" style="font-size: 0.6rem;"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                
                <div class="mb-2">
                    <label class="form-label text-muted" style="font-size: 0.6rem;">Content <span class="text-danger">*</span></label>
                    <textarea name="content" class="form-control form-control-sm <?php $__errorArgs = ['content'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" rows="8" style="font-size: 0.7rem; font-family: monospace;" required><?php echo e(old('content')); ?></textarea>
                    <small class="text-muted" style="font-size: 0.55rem;">You can use plain text or HTML formatting</small>
                    <?php $__errorArgs = ['content'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback" style="font-size: 0.6rem;"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                
                <div class="form-check mb-2">
                    <input type="checkbox" name="requires_print" class="form-check-input" id="requires_print" <?php echo e(old('requires_print') ? 'checked' : ''); ?>>
                    <label class="form-check-label text-muted" for="requires_print" style="font-size: 0.65rem;">
                        <i class="ti ti-printer me-1"></i> Requires physical printing (for recipients without email)
                    </label>
                </div>
                
                <hr class="my-2" style="border-color: #E8E2D5;">
                
                <div class="d-flex justify-content-end gap-1">
                    <a href="<?php echo e(route('registry.outgoing.index')); ?>" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.25rem 0.75rem;">
                        <i class="ti ti-x me-1"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.25rem 0.75rem;">
                        <i class="ti ti-device-floppy me-1"></i> Save as Draft
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\wamp64\www\unileave\resources\views/registry/outgoing/create.blade.php ENDPATH**/ ?>