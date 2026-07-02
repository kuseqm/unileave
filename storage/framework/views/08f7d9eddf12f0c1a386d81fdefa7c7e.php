

<?php $__env->startSection('title', 'Settings'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-2">
    
    <?php if (isset($component)) { $__componentOriginale19f62b34dfe0bfdf95075badcb45bc2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale19f62b34dfe0bfdf95075badcb45bc2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.breadcrumb','data' => ['items' => [
        ['name' => 'Settings', 'url' => '#']
    ]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('breadcrumb'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['items' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
        ['name' => 'Settings', 'url' => '#']
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'SETTINGS','description' => 'Configure system preferences']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'SETTINGS','description' => 'Configure system preferences']); ?>
        <?php if(auth()->check() && auth()->user()->hasAnyRole('admin|super-admin')): ?>
        <a href="<?php echo e(route('settings.create')); ?>" class="btn btn-primary btn-sm rounded-sm">
            <i class="ti ti-plus me-1"></i> New Setting
        </a>
        <?php endif; ?>
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
    
    <?php if(session('success')): ?>
    <div class="alert alert-success alert-dismissible fade show py-1 px-2 mb-2 rounded-sm" role="alert">
        <i class="ti ti-check-circle me-1"></i> <?php echo e(session('success')); ?>

        <button type="button" class="btn-close py-0" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>
    
    <?php if(session('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show py-1 px-2 mb-2 rounded-sm" role="alert">
        <i class="ti ti-alert-circle me-1"></i> <?php echo e(session('error')); ?>

        <button type="button" class="btn-close py-0" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>
    
    <!-- Tab Navigation -->
    <ul class="nav nav-tabs mb-3" id="settingsTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="settings-tab" data-bs-toggle="tab" data-bs-target="#settings" type="button" role="tab">
                <i class="ti ti-settings"></i> Settings
            </button>
        </li>
        <?php if(auth()->check() && auth()->user()->hasAnyRole('admin|super-admin')): ?>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="all-settings-tab" data-bs-toggle="tab" data-bs-target="#all-settings" type="button" role="tab">
                <i class="ti ti-list"></i> All Settings Definitions
            </button>
        </li>
        <?php endif; ?>
    </ul>
    
    <div class="tab-content">
        <!-- Settings Values Tab -->
        <div class="tab-pane fade show active" id="settings" role="tabpanel">
            <form method="POST" action="<?php echo e(route('settings.bulk-update')); ?>">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                
                <?php $__currentLoopData = $settings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group => $groupSettings): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="card rounded-sm mb-3">
                    <div class="card-header py-1 px-2 bg-light">
                        <i class="ti ti-settings me-1"></i>
                        <span style="font-weight: 600;"><?php echo e(ucfirst(str_replace('_', ' ', $group))); ?></span>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <?php $__currentLoopData = $groupSettings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $setting): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" style="font-size: 0.75rem;">
                                    <?php echo e($setting->label ?? ucfirst(str_replace('_', ' ', $setting->key))); ?>

                                    <?php if($setting->description): ?>
                                    <i class="ti ti-info-circle text-muted" data-bs-toggle="tooltip" title="<?php echo e($setting->description); ?>"></i>
                                    <?php endif; ?>
                                </label>
                                
                                <?php if($setting->type === 'boolean'): ?>
                                <div class="form-check form-switch">
                                    <input type="hidden" name="settings[<?php echo e($setting->key); ?>]" value="false">
                                    <input type="checkbox" 
                                           class="form-check-input" 
                                           name="settings[<?php echo e($setting->key); ?>]" 
                                           value="true"
                                           id="setting_<?php echo e($setting->key); ?>"
                                           <?php echo e(setting($setting->key, $setting->value, $setting->role) ? 'checked' : ''); ?>>
                                    <label class="form-check-label" style="font-size: 0.7rem;" for="setting_<?php echo e($setting->key); ?>">
                                        <?php echo e($setting->description ?? 'Enable this setting'); ?>

                                    </label>
                                </div>
                                
                                <?php elseif($setting->type === 'select' && $setting->options): ?>
                                <select class="form-select form-select-sm" name="settings[<?php echo e($setting->key); ?>]" style="font-size: 0.7rem;">
                                    <?php
                                        $options = is_array($setting->options) ? $setting->options : json_decode($setting->options, true);
                                    ?>
                                    <?php $__currentLoopData = $options; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $optValue => $optLabel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($optValue); ?>" <?php echo e(setting($setting->key, $setting->value, $setting->role) == $optValue ? 'selected' : ''); ?>>
                                        <?php echo e($optLabel); ?>

                                    </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                
                                <?php elseif($setting->type === 'number'): ?>
                                <input type="number" 
                                       class="form-control form-control-sm" 
                                       name="settings[<?php echo e($setting->key); ?>]" 
                                       value="<?php echo e(setting($setting->key, $setting->value, $setting->role)); ?>"
                                       style="font-size: 0.7rem;">
                                
                                <?php elseif($setting->type === 'textarea'): ?>
                                <textarea class="form-control form-control-sm" 
                                          name="settings[<?php echo e($setting->key); ?>]" 
                                          rows="2"
                                          style="font-size: 0.7rem;"><?php echo e(setting($setting->key, $setting->value, $setting->role)); ?></textarea>
                                
                                <?php elseif($setting->type === 'json'): ?>
                                <textarea class="form-control form-control-sm font-monospace" 
                                          name="settings[<?php echo e($setting->key); ?>]" 
                                          rows="3"
                                          style="font-size: 0.7rem;"><?php echo e(setting($setting->key, $setting->value, $setting->role)); ?></textarea>
                                
                                <?php else: ?>
                                <input type="text" 
                                       class="form-control form-control-sm" 
                                       name="settings[<?php echo e($setting->key); ?>]" 
                                       value="<?php echo e(setting($setting->key, $setting->value, $setting->role)); ?>"
                                       style="font-size: 0.7rem;">
                                <?php endif; ?>
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                
                <div class="card rounded-sm">
                    <div class="card-body">
                        <button type="submit" class="btn btn-primary btn-sm rounded-sm">
                            <i class="ti ti-device-floppy me-1"></i> Save All Settings
                        </button>
                        <button type="reset" class="btn btn-secondary btn-sm rounded-sm">
                            <i class="ti ti-refresh me-1"></i> Reset Form
                        </button>
                    </div>
                </div>
            </form>
        </div>
        
        <!-- All Settings Definitions Tab (Admin only) -->
        <?php if(auth()->check() && auth()->user()->hasAnyRole('admin|super-admin')): ?>
        <div class="tab-pane fade" id="all-settings" role="tabpanel">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light d-flex justify-content-between align-items-center">
                    <span><i class="ti ti-list me-1"></i> Settings Definitions</span>
                    <a href="<?php echo e(route('settings.create')); ?>" class="btn btn-sm btn-primary">
                        <i class="ti ti-plus"></i> Add Setting
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0" style="font-size: 0.75rem;">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-2 py-1">Key</th>
                                    <th class="py-1">Label</th>
                                    <th class="py-1">Group</th>
                                    <th class="py-1">Type</th>
                                    <th class="py-1">Role</th>
                                    <th class="py-1">Sort Order</th>
                                    <th class="pe-2 py-1">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $allSettings ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $setting): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr data-setting-id="<?php echo e($setting->id); ?>">
                                    <td class="ps-2 py-1"><code><?php echo e($setting->key); ?></code></td>
                                    <td class="py-1"><?php echo e($setting->label ?? '-'); ?></td>
                                    <td class="py-1"><?php echo e($setting->group); ?></td>
                                    <td class="py-1">
                                        <span class="badge bg-secondary-subtle text-secondary rounded-sm"><?php echo e($setting->type); ?></span>
                                    </td>
                                    <td class="py-1"><?php echo e($setting->role ?? 'Global'); ?></td>
                                    <td class="py-1"><?php echo e($setting->sort_order); ?></td>
                                    <td class="pe-2 py-1">
                                        <div class="d-flex gap-1">
                                            <a href="<?php echo e(route('settings.show', $setting->id)); ?>" class="btn btn-sm btn-outline-info rounded-sm" title="View">
                                                <i class="ti ti-eye"></i>
                                            </a>
                                            <a href="<?php echo e(route('settings.edit', $setting->id)); ?>" class="btn btn-sm btn-outline-primary rounded-sm" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger rounded-sm" title="Delete" 
                                                    onclick="confirmDelete(<?php echo e($setting->id); ?>, '<?php echo e($setting->key); ?>')">
                                                <i class="ti ti-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="7" class="text-center py-3 text-muted">No settings found</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
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

// Initialize tooltips
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\wamp64\www\unileave\resources\views/settings/index.blade.php ENDPATH**/ ?>