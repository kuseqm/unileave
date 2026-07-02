

<?php $__env->startSection('title', 'Setting Details'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-2">
    
    <?php if (isset($component)) { $__componentOriginale19f62b34dfe0bfdf95075badcb45bc2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale19f62b34dfe0bfdf95075badcb45bc2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.breadcrumb','data' => ['items' => [
        ['name' => 'Settings', 'url' => route('settings.index')],
        ['name' => 'Details', 'url' => '#']
    ]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('breadcrumb'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['items' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
        ['name' => 'Settings', 'url' => route('settings.index')],
        ['name' => 'Details', 'url' => '#']
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'SETTING DETAILS','description' => 'View setting configuration']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'SETTING DETAILS','description' => 'View setting configuration']); ?>
        <div class="d-flex gap-2">
            <a href="<?php echo e(route('settings.index')); ?>" class="btn btn-secondary btn-sm rounded-sm">
                <i class="ti ti-arrow-left me-1"></i> Back
            </a>
            <a href="<?php echo e(route('settings.edit', $setting->id)); ?>" class="btn btn-primary btn-sm rounded-sm">
                <i class="ti ti-edit me-1"></i> Edit
            </a>
            <button type="button" class="btn btn-danger btn-sm rounded-sm" onclick="confirmDelete(<?php echo e($setting->id); ?>, '<?php echo e($setting->key); ?>')">
                <i class="ti ti-trash me-1"></i> Delete
            </button>
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
    
    <div class="card rounded-sm">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-sm" style="font-size: 0.75rem;">
                        <tr>
                            <th width="150">Key:</th>
                            <td><code><?php echo e($setting->key); ?></code></td>
                        </tr>
                        <tr>
                            <th>Label:</th>
                            <td><?php echo e($setting->label ?? '-'); ?></td>
                        </tr>
                        <tr>
                            <th>Group:</th>
                            <td><?php echo e($setting->group); ?></td>
                        </tr>
                        <tr>
                            <th>Type:</th>
                            <td><span class="badge bg-secondary-subtle text-secondary rounded-sm"><?php echo e($setting->type); ?></span></td>
                        </tr>
                        <tr>
                            <th>Current Value:</th>
                            <td>
                                <?php if($setting->type === 'boolean'): ?>
                                    <span class="badge bg-<?php echo e(setting($setting->key) ? 'success' : 'danger'); ?>-subtle rounded-sm">
                                        <?php echo e(setting($setting->key) ? 'Yes' : 'No'); ?>

                                    </span>
                                <?php elseif($setting->type === 'json'): ?>
                                    <pre class="bg-light p-1 rounded-sm mb-0" style="font-size: 0.65rem;"><?php echo e(json_encode(setting($setting->key, $setting->value, $setting->role), JSON_PRETTY_PRINT)); ?></pre>
                                <?php else: ?>
                                    <pre class="bg-light p-1 rounded-sm mb-0" style="font-size: 0.65rem;"><?php echo e(setting($setting->key, $setting->value, $setting->role) ?? '-'); ?></pre>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Default Value:</th>
                            <td><pre class="bg-light p-1 rounded-sm mb-0" style="font-size: 0.65rem;"><?php echo e($setting->value ?? '-'); ?></pre></td>
                        </tr>
                        <tr>
                            <th>Role:</th>
                            <td><?php echo e($setting->role ?? 'Global (All Roles)'); ?></td>
                        </tr>
                        <tr>
                            <th>Sort Order:</th>
                            <td><?php echo e($setting->sort_order); ?></td>
                        </tr>
                        <tr>
                            <th>Encrypted:</th>
                            <td><?php echo e($setting->is_encrypted ? 'Yes' : 'No'); ?></td>
                        </tr>
                        <tr>
                            <th>Description:</th>
                            <td><?php echo e($setting->description ?? '-'); ?></td>
                        </tr>
                        <tr>
                            <th>Created At:</th>
                            <td><?php echo e($setting->created_at ? $setting->created_at->format('d M Y H:i:s') : '-'); ?></td>
                        </tr>
                        <tr>
                            <th>Last Updated:</th>
                            <td><?php echo e($setting->updated_at ? $setting->updated_at->format('d M Y H:i:s') : '-'); ?></td>
                        </tr>
                    </table>
                </div>
                
                <?php if($setting->options): ?>
                <div class="col-md-6">
                    <div class="card rounded-sm">
                        <div class="card-header py-1 px-2 bg-light">
                            <i class="ti ti-list"></i> Options
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-sm mb-0" style="font-size: 0.7rem;">
                                <thead>
                                    <tr>
                                        <th class="ps-2">Value</th>
                                        <th class="pe-2">Label</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $options = is_array($setting->options) ? $setting->options : json_decode($setting->options, true);
                                    ?>
                                    <?php $__currentLoopData = $options; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $optValue => $optLabel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td class="ps-2"><code><?php echo e($optValue); ?></code></td>
                                        <td class="pe-2"><?php echo e($optLabel); ?></td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-sm">
            <div class="modal-header py-1 px-2">
                <h5 class="modal-title" style="font-size: 0.8rem;">Confirm Delete</h5>
                <button type="button" class="btn-close py-0" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body py-2 px-2">
                <p style="font-size: 0.75rem;">Are you sure you want to delete <strong id="deleteSettingName"></strong>?</p>
                <p class="text-danger mb-0" style="font-size: 0.7rem;">This action cannot be undone.</p>
            </div>
            <div class="modal-footer py-1 px-2">
                <button type="button" class="btn btn-secondary btn-sm rounded-sm" data-bs-dismiss="modal" style="font-size: 0.7rem;">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="btn btn-danger btn-sm rounded-sm" style="font-size: 0.7rem;">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(id, name) {
    document.getElementById('deleteSettingName').innerText = name;
    document.getElementById('deleteForm').action = '/settings/' + id;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\wamp64\www\unileave\resources\views/settings/show.blade.php ENDPATH**/ ?>