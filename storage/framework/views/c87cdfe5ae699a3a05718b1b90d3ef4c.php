

<?php $__env->startSection('title', 'Manage Offices'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">Manage Offices</h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <a href="<?php echo e(route('registry.offices.create')); ?>" class="btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M12 5v14M5 12h14" />
                    </svg>
                    New Office
                </a>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-vcenter card-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Short Name</th>
                                <th>Department</th>
                                <th>Receives Letters</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $offices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $office): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><strong><?php echo e($office->name); ?></strong></td>
                                <td><?php echo e($office->short_name); ?></td>
                                <td><?php echo e($office->department->name ?? 'Central'); ?></td>
                                <td>
                                    <?php if($office->receives_letters): ?>
                                    <span class="badge bg-green">Yes</span>
                                    <?php else: ?>
                                    <span class="badge bg-secondary">No</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($office->is_active): ?>
                                    <span class="badge bg-green">Active</span>
                                    <?php else: ?>
                                    <span class="badge bg-red">Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="<?php echo e(route('registry.offices.edit', $office->id)); ?>" class="btn btn-sm btn-primary">Edit</a>
                                    <button type="button" class="btn btn-sm btn-danger" onclick="deleteOffice(<?php echo e($office->id); ?>)">Delete</button>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="6" class="text-center py-4">No offices found.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer">
                <?php echo e($offices->links()); ?>

            </div>
        </div>
    </div>
</div>

<form id="deleteForm" method="POST" style="display: none;">
    <?php echo csrf_field(); ?>
    <?php echo method_field('DELETE'); ?>
</form>

<script>
function deleteOffice(id) {
    if (confirm('Are you sure you want to delete this office?')) {
        const form = document.getElementById('deleteForm');
        form.action = `<?php echo e(url('registry/offices')); ?>/${id}`;
        form.submit();
    }
}
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\wamp64\www\unileave\resources\views/registry/offices/index.blade.php ENDPATH**/ ?>