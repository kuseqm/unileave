

<?php $__env->startSection('title', 'Edit Setting'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-2">
    
    <?php if (isset($component)) { $__componentOriginale19f62b34dfe0bfdf95075badcb45bc2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale19f62b34dfe0bfdf95075badcb45bc2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.breadcrumb','data' => ['items' => [
        ['name' => 'Settings', 'url' => route('settings.index')],
        ['name' => 'Edit', 'url' => '#']
    ]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('breadcrumb'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['items' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
        ['name' => 'Settings', 'url' => route('settings.index')],
        ['name' => 'Edit', 'url' => '#']
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'EDIT SETTING','description' => 'Modify setting configuration']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'EDIT SETTING','description' => 'Modify setting configuration']); ?>
        <a href="<?php echo e(route('settings.index')); ?>" class="btn btn-secondary btn-sm rounded-sm">
            <i class="ti ti-arrow-left me-1"></i> Back
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
    
    <div class="card rounded-sm">
        <div class="card-body">
            <form method="POST" action="<?php echo e(route('settings.update', $setting->id)); ?>">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">Key</label>
                        <input type="text" name="key" class="form-control <?php $__errorArgs = ['key'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                               value="<?php echo e(old('key', $setting->key)); ?>" required>
                        <small class="text-muted">Unique identifier (cannot be changed easily)</small>
                        <?php $__errorArgs = ['key'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Label</label>
                        <input type="text" name="label" class="form-control <?php $__errorArgs = ['label'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                               value="<?php echo e(old('label', $setting->label)); ?>">
                        <?php $__errorArgs = ['label'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">Type</label>
                        <select name="type" class="form-select <?php $__errorArgs = ['type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="settingType" required>
                            <option value="text" <?php echo e(old('type', $setting->type) == 'text' ? 'selected' : ''); ?>>Text</option>
                            <option value="textarea" <?php echo e(old('type', $setting->type) == 'textarea' ? 'selected' : ''); ?>>Textarea</option>
                            <option value="number" <?php echo e(old('type', $setting->type) == 'number' ? 'selected' : ''); ?>>Number</option>
                            <option value="boolean" <?php echo e(old('type', $setting->type) == 'boolean' ? 'selected' : ''); ?>>Boolean</option>
                            <option value="select" <?php echo e(old('type', $setting->type) == 'select' ? 'selected' : ''); ?>>Select Dropdown</option>
                            <option value="json" <?php echo e(old('type', $setting->type) == 'json' ? 'selected' : ''); ?>>JSON</option>
                        </select>
                        <?php $__errorArgs = ['type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">Group</label>
                        <input type="text" name="group" class="form-control <?php $__errorArgs = ['group'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                               value="<?php echo e(old('group', $setting->group)); ?>" required>
                        <?php $__errorArgs = ['group'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    
                    <div class="col-md-12 mb-3" id="optionsField" style="<?php echo e($setting->type == 'select' ? 'display:block' : 'display:none'); ?>">
                        <label class="form-label">Options (JSON)</label>
                        <textarea name="options" class="form-control <?php $__errorArgs = ['options'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                  rows="3" placeholder='{"option1":"Label 1","option2":"Label 2"}'><?php echo e(old('options', is_array($setting->options) ? json_encode($setting->options) : $setting->options)); ?></textarea>
                        <small class="text-muted">For select type only. JSON format: {"value":"Label"}</small>
                        <?php $__errorArgs = ['options'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Default Value</label>
                        <div id="valueInput">
                            <?php if($setting->type === 'boolean'): ?>
                            <select name="value" class="form-select">
                                <option value="true" <?php echo e($setting->value == 'true' ? 'selected' : ''); ?>>Yes (True)</option>
                                <option value="false" <?php echo e($setting->value == 'false' ? 'selected' : ''); ?>>No (False)</option>
                            </select>
                            <?php elseif($setting->type === 'textarea'): ?>
                            <textarea name="value" class="form-control" rows="3"><?php echo e(old('value', $setting->value)); ?></textarea>
                            <?php else: ?>
                            <input type="text" name="value" class="form-control" value="<?php echo e(old('value', $setting->value)); ?>">
                            <?php endif; ?>
                        </div>
                        <?php $__errorArgs = ['value'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                  rows="2"><?php echo e(old('description', $setting->description)); ?></textarea>
                        <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Role (Optional)</label>
                        <input type="text" name="role" class="form-control <?php $__errorArgs = ['role'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                               value="<?php echo e(old('role', $setting->role)); ?>" placeholder="Leave empty for global">
                        <small class="text-muted">e.g., admin, hod, dean</small>
                        <?php $__errorArgs = ['role'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Sort Order</label>
                        <input type="number" name="sort_order" class="form-control <?php $__errorArgs = ['sort_order'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                               value="<?php echo e(old('sort_order', $setting->sort_order)); ?>">
                        <?php $__errorArgs = ['sort_order'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <label class="form-check mt-4">
                            <input type="checkbox" name="is_encrypted" class="form-check-input" value="1" <?php echo e(old('is_encrypted', $setting->is_encrypted) ? 'checked' : ''); ?>>
                            <span class="form-check-label">Encrypt Value</span>
                        </label>
                    </div>
                </div>
                
                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">Update Setting</button>
                    <a href="<?php echo e(route('settings.index')); ?>" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const typeSelect = document.getElementById('settingType');
    const optionsField = document.getElementById('optionsField');
    const valueInput = document.getElementById('valueInput');
    
    typeSelect.addEventListener('change', function() {
        if (this.value === 'select') {
            optionsField.style.display = 'block';
            valueInput.innerHTML = '<input type="text" name="value" class="form-control" placeholder="Default selected value" value="<?php echo e(old('value', $setting->value)); ?>">';
        } else if (this.value === 'boolean') {
            optionsField.style.display = 'none';
            valueInput.innerHTML = `
                <select name="value" class="form-select">
                    <option value="true">Yes (True)</option>
                    <option value="false">No (False)</option>
                </select>
            `;
        } else if (this.value === 'textarea') {
            optionsField.style.display = 'none';
            valueInput.innerHTML = '<textarea name="value" class="form-control" rows="3"><?php echo e(old('value', $setting->value)); ?></textarea>';
        } else if (this.value === 'number') {
            optionsField.style.display = 'none';
            valueInput.innerHTML = '<input type="number" name="value" class="form-control" value="<?php echo e(old('value', $setting->value)); ?>">';
        } else if (this.value === 'json') {
            optionsField.style.display = 'none';
            valueInput.innerHTML = '<textarea name="value" class="form-control" rows="3"><?php echo e(old('value', $setting->value)); ?></textarea>';
        } else {
            optionsField.style.display = 'none';
            valueInput.innerHTML = '<input type="text" name="value" class="form-control" value="<?php echo e(old('value', $setting->value)); ?>">';
        }
    });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\wamp64\www\unileave\resources\views/settings/edit.blade.php ENDPATH**/ ?>