

<?php $__env->startSection('title', 'Message Directory'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-2">
    
    <!-- Breadcrumb -->
    <?php if (isset($component)) { $__componentOriginale19f62b34dfe0bfdf95075badcb45bc2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale19f62b34dfe0bfdf95075badcb45bc2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.breadcrumb','data' => ['items' => [
        ['name' => 'Messages', 'url' => route('messages.inbox')],
        ['name' => 'Message Directory', 'url' => '#']

    ]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('breadcrumb'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['items' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
        ['name' => 'Messages', 'url' => route('messages.inbox')],
        ['name' => 'Message Directory', 'url' => '#']

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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'MESSAGE DIRECTORY','description' => 'Message metadata for audit & compliance (7-year retention)']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'MESSAGE DIRECTORY','description' => 'Message metadata for audit & compliance (7-year retention)']); ?>
        <a href="<?php echo e(route('messages.admin-list')); ?>" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
            <i class="ti ti-refresh me-1"></i> Refresh
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

    <!-- Stats Cards Row - Using your x-stats-card component -->
    <div class="row g-2 mb-3">
        <?php if (isset($component)) { $__componentOriginal8f216e051c231b98198765acd723fb77 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8f216e051c231b98198765acd723fb77 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stats-card','data' => ['value' => ''.e($messages->total()).'','label' => 'Total Messages','subtext' => 'all messages']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('stats-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => ''.e($messages->total()).'','label' => 'Total Messages','subtext' => 'all messages']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stats-card','data' => ['value' => ''.e($messages->whereNull('deleted_at')->count()).'','label' => 'Active','subtext' => 'active messages','color' => 'success']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('stats-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => ''.e($messages->whereNull('deleted_at')->count()).'','label' => 'Active','subtext' => 'active messages','color' => 'success']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stats-card','data' => ['value' => ''.e($messages->whereNotNull('deleted_at')->count()).'','label' => 'Soft Deleted','subtext' => 'awaiting 7-year retention','color' => 'warning']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('stats-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => ''.e($messages->whereNotNull('deleted_at')->count()).'','label' => 'Soft Deleted','subtext' => 'awaiting 7-year retention','color' => 'warning']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stats-card','data' => ['value' => ''.e(ucfirst($role)).'','label' => 'Your Role','subtext' => 'access level','color' => 'info']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('stats-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => ''.e(ucfirst($role)).'','label' => 'Your Role','subtext' => 'access level','color' => 'info']); ?>
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

    <!-- Filters -->
    <div class="card rounded-sm mb-3">
        <div class="card-body py-2 px-3">
            <div class="row g-2">
                <div class="col-md-4">
                    <select id="typeFilter" class="form-select form-select-sm">
                        <option value="all">All Types</option>
                        <option value="general">General</option>
                        <option value="approval">Approval</option>
                        <option value="task">Task</option>
                        <option value="minute">Minute</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <select id="statusFilter" class="form-select form-select-sm">
                        <option value="all">All Status</option>
                        <option value="active">Active</option>
                        <option value="deleted">Deleted (Soft)</option>
                        <option value="archived">Archived</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <input type="text" id="searchInput" class="form-control form-control-sm" placeholder="Search by subject or sender...">
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <!-- LEFT COLUMN: Message Directory Table (70%) -->
        <div class="col-lg-8">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <i class="ti ti-database text-primary me-1" style="font-size: 0.8rem;"></i>
                            <span style="font-size: 0.7rem; font-weight: 600;">Message Directory</span>
                            <span class="text-muted ms-2 small" style="font-size: 0.6rem;">(Content encrypted - view requires VC approval)</span>
                        </div>
                        <div class="text-muted small" style="font-size: 0.6rem;">
                            Showing <?php echo e($messages->firstItem() ?? 0); ?> to <?php echo e($messages->lastItem() ?? 0); ?> of <?php echo e($messages->total()); ?>

                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0" style="font-size: 0.75rem;" id="messageTable">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-2 py-1" width="50">#</th>
                                    <th class="py-1">From</th>
                                    <th class="py-1">To</th>
                                    <th class="py-1">Subject</th>
                                    <th class="py-1">Priority</th>
                                    <th class="py-1">Status</th>
                                    <th class="pe-2 py-1" width="100">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $messages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $message): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr data-type="<?php echo e($message->message_type); ?>" 
                                    data-priority="<?php echo e($message->priority); ?>"
                                    data-status="<?php echo e($message->trashed() ? 'deleted' : ($message->is_archived ? 'archived' : 'active')); ?>"
                                    data-subject="<?php echo e(strtolower($message->subject)); ?>"
                                    data-sender="<?php echo e(strtolower($message->sender->first_name ?? '')); ?>">
                                    <td class="ps-2 py-1"><?php echo e($messages->firstItem() + $index); ?></td>
                                    <td class="py-1">
                                        <?php echo e($message->sender->first_name ?? 'System'); ?> <?php echo e($message->sender->last_name ?? ''); ?>

                                        <div class="text-muted" style="font-size: 0.6rem;"><?php echo e($message->sender->email ?? ''); ?></div>
                                    </td>
                                    <td class="py-1">
                                        <?php if($message->recipient_names): ?>
                                            <span title="<?php echo e($message->recipient_names); ?>"><?php echo e(Str::limit($message->recipient_names, 30)); ?></span>
                                        <?php else: ?>
                                            <?php echo e($message->receiver->first_name ?? 'Unknown'); ?>

                                        <?php endif; ?>
                                    </td>
                                    <td class="py-1">
                                        <?php echo e(Str::limit($message->subject ?? 'No subject', 50)); ?>

                                        <?php if($message->trashed()): ?>
                                            <span class="badge bg-warning ms-1" style="font-size: 0.6rem;">Deleted</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-1"><?php echo $message->priority_badge; ?></td>
                                    <td class="py-1"><?php echo $message->status_badge; ?></td>
                                    <td class="pe-2 py-1">
                                        <div class="d-flex gap-1">
                                            <a href="<?php echo e(route('messages.metadata', $message->id)); ?>" class="btn btn-sm btn-outline-info rounded-sm" style="font-size: 0.6rem; padding: 0.15rem 0.4rem;" title="View Metadata & Audit">
                                                <i class="ti ti-history"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="7" class="text-center py-3 text-muted" style="font-size: 0.7rem;">
                                        <i class="ti ti-inbox" style="font-size: 48px;"></i>
                                        <p class="mt-2">No messages found</p>
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer py-1 px-2 bg-light">
                    <div class="d-flex justify-content-end">
                        <?php echo e($messages->links()); ?>

                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN: Explanation Panel (30%) -->
        <div class="col-lg-4">
            <div class="card rounded-sm border-0 shadow-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-info-circle text-primary me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">About Message Directory</span>
                    </div>
                </div>
                <div class="card-body p-2">
                    <div class="mb-2">
                        <i class="ti ti-database text-primary me-1" style="font-size: 0.7rem;"></i>
                        <strong class="small" style="font-size: 0.7rem;">What is this page?</strong>
                        <p class="text-muted mt-1 mb-0" style="font-size: 0.65rem;">This is the central message directory for audit and compliance purposes. All message metadata is stored for 7 years as required by law.</p>
                    </div>
                    
                    <div class="mb-2">
                        <i class="ti ti-lock text-warning me-1" style="font-size: 0.7rem;"></i>
                        <strong class="small" style="font-size: 0.7rem;">Content Privacy</strong>
                        <p class="text-muted mt-1 mb-0" style="font-size: 0.65rem;">Message content is encrypted. Only the sender and receiver can view content. Others must request access through VC approval.</p>
                    </div>
                    
                    <div class="mb-2">
                        <i class="ti ti-key text-info me-1" style="font-size: 0.7rem;"></i>
                        <strong class="small" style="font-size: 0.7rem;">Access Request Process</strong>
                        <p class="text-muted mt-1 mb-0" style="font-size: 0.65rem;">Click the <i class="ti ti-history"></i> icon to view message details. From there, HR can request access to content.</p>
                    </div>
                    
                    <div class="alert alert-info py-1 px-2 mb-0" style="font-size: 0.65rem;">
                        <i class="ti ti-tips me-1"></i>
                        <strong>Tip:</strong> Click the <i class="ti ti-history"></i> icon to view full metadata, audit trail, and access request options.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Filters only - no restore or request access functions here anymore
const typeFilter = document.getElementById('typeFilter');
const statusFilter = document.getElementById('statusFilter');
const searchInput = document.getElementById('searchInput');
const rows = document.querySelectorAll('#messageTable tbody tr');

function filterTable() {
    const type = typeFilter.value;
    const status = statusFilter.value;
    const search = searchInput.value.toLowerCase();
    
    rows.forEach(row => {
        let show = true;
        if (type !== 'all' && row.dataset.type !== type) show = false;
        if (status !== 'all' && row.dataset.status !== status) show = false;
        if (search && !row.dataset.subject.includes(search) && !row.dataset.sender.includes(search)) show = false;
        row.style.display = show ? '' : 'none';
    });
}

typeFilter.addEventListener('change', filterTable);
statusFilter.addEventListener('change', filterTable);
searchInput.addEventListener('keyup', filterTable);
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\wamp64\www\unileave\resources\views/shared/messages/admin-list.blade.php ENDPATH**/ ?>