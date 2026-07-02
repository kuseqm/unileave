

<?php $__env->startSection('title', 'Tenants'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-2">
    
    <?php if (isset($component)) { $__componentOriginale19f62b34dfe0bfdf95075badcb45bc2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale19f62b34dfe0bfdf95075badcb45bc2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.breadcrumb','data' => ['items' => [
        ['name' => 'Admin', 'url' => '#'],
        ['name' => 'Tenants', 'url' => '#']
    ]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('breadcrumb'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['items' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
        ['name' => 'Admin', 'url' => '#'],
        ['name' => 'Tenants', 'url' => '#']
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'TENANTS','description' => 'Manage multi-tenant institutions']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'TENANTS','description' => 'Manage multi-tenant institutions']); ?>
        <a href="<?php echo e(route('admin.tenants.create')); ?>" class="btn btn-primary btn-sm rounded-sm">
            <i class="ti ti-plus me-1"></i> Add Tenant
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
    
    <?php if(session('success')): ?>
    <div class="alert alert-success alert-dismissible fade show py-1 px-2 mb-2 rounded-sm" role="alert">
        <i class="ti ti-check-circle me-1"></i> <?php echo e(session('success')); ?>

        <button type="button" class="btn-close py-0" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>
    
    <div class="card rounded-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm mb-0" style="font-size: 0.75rem;">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-2 py-1">#</th>
                            <th class="py-1">Name</th>
                            <th class="py-1">Domain</th>
                            <th class="py-1">Database</th>
                            <th class="py-1">Status</th>
                            <th class="py-1">Created</th>
                            <th class="pe-2 py-1">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $tenants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $tenant): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td class="ps-2 py-1"><?php echo e($index + 1); ?></td>
                            <td class="py-1"><?php echo e($tenant->name); ?></td>
                            <td class="py-1"><?php echo e($tenant->domain ?? '-'); ?></td>
                            <td class="py-1"><?php echo e($tenant->database ?? '-'); ?></td>
                            <td class="py-1">
                                <?php if($tenant->status == 'active'): ?>
                                    <span class="badge bg-success-subtle text-success rounded-sm">Active</span>
                                <?php elseif($tenant->status == 'inactive'): ?>
                                    <span class="badge bg-danger-subtle text-danger rounded-sm">Inactive</span>
                                <?php else: ?>
                                    <span class="badge bg-warning-subtle text-warning rounded-sm">Pending</span>
                                <?php endif; ?>
                            </td>
                            <td class="py-1"><?php echo e($tenant->created_at->format('d M Y')); ?></td>
                            <td class="pe-2 py-1">
                                <div class="d-flex gap-1">
                                    <a href="<?php echo e(route('admin.tenants.settings', $tenant->id)); ?>" class="btn btn-sm btn-outline-info rounded-sm" title="Settings">
                                        <i class="ti ti-settings"></i>
                                    </a>
                                    <a href="<?php echo e(route('admin.tenants.edit', $tenant->id)); ?>" class="btn btn-sm btn-outline-primary rounded-sm" title="Edit">
                                        <i class="ti ti-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-danger rounded-sm" title="Delete" onclick="confirmDelete(<?php echo e($tenant->id); ?>, '<?php echo e($tenant->name); ?>')">
                                        <i class="ti ti-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" class="text-center py-3 text-muted">No tenants found</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(id, name) {
    if(confirm('Are you sure you want to delete tenant "' + name + '"?')) {
        const form = document.createElement('form');
        form.action = '/admin/tenants/' + id;
        form.method = 'POST';
        form.innerHTML = '<?php echo csrf_field(); ?> <?php echo method_field("DELETE"); ?>';
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\wamp64\www\unileave\resources\views/admin/tenants/index.blade.php ENDPATH**/ ?>