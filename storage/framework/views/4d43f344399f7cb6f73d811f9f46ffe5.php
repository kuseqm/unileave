

<?php $__env->startSection('title', 'New Incoming Letter'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-2">
    
    <!-- Breadcrumb -->
    <?php if (isset($component)) { $__componentOriginale19f62b34dfe0bfdf95075badcb45bc2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale19f62b34dfe0bfdf95075badcb45bc2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.breadcrumb','data' => ['items' => [
        ['name' => 'Central Registry', 'url' => '#'],
        ['name' => 'Incoming Letters', 'url' => route('registry.incoming.index')],
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
        ['name' => 'Incoming Letters', 'url' => route('registry.incoming.index')],
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'NEW INCOMING LETTER','description' => 'Register a new incoming letter']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'NEW INCOMING LETTER','description' => 'Register a new incoming letter']); ?>
        <a href="<?php echo e(route('registry.incoming.index')); ?>" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
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
                <i class="ti ti-file-plus text-primary me-1" style="font-size: 0.8rem;"></i>
                <span style="font-size: 0.7rem; font-weight: 600;">Letter Information</span>
            </div>
        </div>
        <div class="card-body py-2 px-2">
            <form method="POST" action="<?php echo e(route('registry.incoming.store')); ?>" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                
                <div class="row g-1">
                    <div class="col-md-6 mb-2">
                        <label class="form-label text-muted" style="font-size: 0.6rem;">Sender <span class="text-danger">*</span></label>
                        <input type="text" name="sender" class="form-control form-control-sm <?php $__errorArgs = ['sender'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('sender')); ?>" style="font-size: 0.7rem;" required>
                        <?php $__errorArgs = ['sender'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback" style="font-size: 0.6rem;"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    
                    <div class="col-md-6 mb-2">
                        <label class="form-label text-muted" style="font-size: 0.6rem;">Date Received <span class="text-danger">*</span></label>
                        <input type="date" name="date_received" class="form-control form-control-sm <?php $__errorArgs = ['date_received'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('date_received', date('Y-m-d'))); ?>" style="font-size: 0.7rem;" required>
                        <?php $__errorArgs = ['date_received'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback" style="font-size: 0.6rem;"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>
                
                <div class="row g-1">
                    <div class="col-md-6 mb-2">
                        <label class="form-label text-muted" style="font-size: 0.6rem;">Addressee Office <span class="text-danger">*</span></label>
                        <select name="office_id" class="form-select form-select-sm <?php $__errorArgs = ['office_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" style="font-size: 0.7rem;" required>
                            <option value="">-- Select Office --</option>
                            <?php $__currentLoopData = $offices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $office): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($office->id); ?>" <?php echo e(old('office_id') == $office->id ? 'selected' : ''); ?>><?php echo e($office->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php $__errorArgs = ['office_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback" style="font-size: 0.6rem;"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    
                    <div class="col-md-6 mb-2">
                        <label class="form-label text-muted" style="font-size: 0.6rem;">Priority</label>
                        <select name="priority" class="form-select form-select-sm" style="font-size: 0.7rem;">
                            <option value="normal" <?php echo e(old('priority') == 'normal' ? 'selected' : ''); ?>>Normal</option>
                            <option value="urgent" <?php echo e(old('priority') == 'urgent' ? 'selected' : ''); ?>>Urgent</option>
                            <option value="confidential" <?php echo e(old('priority') == 'confidential' ? 'selected' : ''); ?>>Confidential</option>
                        </select>
                    </div>
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
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('subject')); ?>" style="font-size: 0.7rem;" required>
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
                    <label class="form-label text-muted" style="font-size: 0.6rem;">PDF Document</label>
                    <input type="file" name="pdf_file" class="form-control form-control-sm <?php $__errorArgs = ['pdf_file'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" accept=".pdf" style="font-size: 0.7rem;">
                    <small class="text-muted" style="font-size: 0.55rem;">Upload scanned letter (PDF only, max 10MB)</small>
                    <?php $__errorArgs = ['pdf_file'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback" style="font-size: 0.6rem;"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                
                <div class="mb-2">
                    <label class="form-label text-muted" style="font-size: 0.6rem;">Remarks (Optional)</label>
                    <textarea name="remarks" class="form-control form-control-sm" rows="3" style="font-size: 0.7rem;"><?php echo e(old('remarks')); ?></textarea>
                </div>
                
                <hr class="my-2" style="border-color: #E8E2D5;">
                
                <div class="d-flex justify-content-end gap-1">
                    <a href="<?php echo e(route('registry.incoming.index')); ?>" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.25rem 0.75rem;">
                        <i class="ti ti-x me-1"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.25rem 0.75rem;">
                        <i class="ti ti-device-floppy me-1"></i> Save Letter
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\wamp64\www\unileave\resources\views/registry/incoming/create.blade.php ENDPATH**/ ?>