

<?php $__env->startSection('title', 'Inbox - Messages'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-2">
    <!-- Breadcrumb -->
    <?php if (isset($component)) { $__componentOriginale19f62b34dfe0bfdf95075badcb45bc2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale19f62b34dfe0bfdf95075badcb45bc2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.breadcrumb','data' => ['items' => [
        ['name' => 'Messages', 'url' => '#'],
        ['name' => 'Inbox', 'url' => '#']
    ]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('breadcrumb'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['items' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
        ['name' => 'Messages', 'url' => '#'],
        ['name' => 'Inbox', 'url' => '#']
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'INBOX','description' => 'Your received messages']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'INBOX','description' => 'Your received messages']); ?>
        <a href="<?php echo e(route('messages.create')); ?>" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
            <i class="ti ti-plus me-1"></i> Compose
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

    <!-- Stats Cards - Using x-stats-card component -->
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stats-card','data' => ['value' => ''.e($unreadCount).'','label' => 'Unread','subtext' => 'awaiting your attention','color' => 'warning']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('stats-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => ''.e($unreadCount).'','label' => 'Unread','subtext' => 'awaiting your attention','color' => 'warning']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stats-card','data' => ['value' => ''.e($messages->where('priority', 'urgent')->count()).'','label' => 'Urgent','subtext' => 'requires immediate action','color' => 'danger']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('stats-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => ''.e($messages->where('priority', 'urgent')->count()).'','label' => 'Urgent','subtext' => 'requires immediate action','color' => 'danger']); ?>
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

    <div class="card rounded-sm">
        <div class="card-header py-1 px-2 bg-light">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="d-flex align-items-center">
                    <i class="ti ti-mail text-primary me-1" style="font-size: 0.8rem;"></i>
                    <span style="font-size: 0.7rem; font-weight: 600;">Messages</span>
                    <?php if($unreadCount > 0): ?>
                        <span class="badge bg-danger ms-2" style="font-size: 0.6rem;"><?php echo e($unreadCount); ?> unread</span>
                    <?php endif; ?>
                </div>
                <div>
                    <form method="GET" action="<?php echo e(route('messages.search')); ?>" class="d-flex">
                        <input type="text" name="query" class="form-control form-control-sm me-1" placeholder="Search messages..." style="width: 200px; font-size: 0.7rem;">
                        <button type="submit" class="btn btn-sm btn-outline-secondary" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
                            <i class="ti ti-search"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <?php if($messages->count() > 0): ?>
                <div class="table-responsive">
                    <form id="bulkDeleteForm" action="<?php echo e(route('messages.bulk-delete')); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <table class="table table-hover mb-0" style="font-size: 0.75rem;">
                            <thead class="bg-light">
                                <tr>
                                    <th width="30" class="ps-2 py-1">
                                        <input type="checkbox" id="selectAll">
                                    </th>
                                    <th class="py-1">From / To</th>
                                    <th class="py-1">Subject</th>
                                    <th class="py-1">Priority</th>
                                    <th class="py-1">Date</th>
                                    <th class="pe-2 py-1" width="120">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $messages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $message): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr class="<?php echo e(!$message->is_read ? 'fw-bold' : ''); ?>">
                                        <td class="ps-2 py-1">
                                            <input type="checkbox" name="message_ids[]" value="<?php echo e($message->id); ?>" class="message-checkbox">
                                        </td>
                                        <td class="py-1">
                                            <?php if($message->sender_id == auth()->id()): ?>
                                                
                                                <?php if($message->recipient_ids && count($message->recipient_ids) > 1): ?>
                                                    <div class="d-flex align-items-center">
                                                        <i class="ti ti-users text-primary me-1" title="Sent to multiple recipients" style="font-size: 0.7rem;"></i>
                                                        <span>
                                                            To: <?php echo e(count($message->recipient_ids)); ?> recipients
                                                        </span>
                                                    </div>
                                                    <small class="text-muted" style="font-size: 0.6rem;">
                                                        <?php echo e($message->recipient_names); ?>

                                                    </small>
                                                <?php else: ?>
                                                    <div class="d-flex align-items-center">
                                                        <i class="ti ti-send text-success me-1" title="Sent message" style="font-size: 0.7rem;"></i>
                                                        <span>
                                                            To: <?php echo e($message->receiver->first_name ?? ''); ?> <?php echo e($message->receiver->last_name ?? ''); ?>

                                                        </span>
                                                    </div>
                                                    <?php if($message->receiver): ?>
                                                        <small class="text-muted" style="font-size: 0.6rem;"><?php echo e($message->receiver->email ?? ''); ?></small>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                
                                                <div class="d-flex align-items-center">
                                                    <i class="ti ti-user text-info me-1" title="From" style="font-size: 0.7rem;"></i>
                                                    <span>
                                                        From: <?php echo e($message->sender->first_name ?? ''); ?> <?php echo e($message->sender->last_name ?? ''); ?>

                                                    </span>
                                                </div>
                                                <small class="text-muted" style="font-size: 0.6rem;"><?php echo e($message->sender->email ?? ''); ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td class="py-1">
                                            <a href="<?php echo e(route('messages.show', $message->id)); ?>" class="text-decoration-none <?php echo e(!$message->is_read ? 'fw-bold' : ''); ?>">
                                                <?php echo e(Str::limit($message->subject ?? 'No subject', 50)); ?>

                                            </a>
                                            <?php if($message->attachments && $message->attachments->count() > 0): ?>
                                                <i class="ti ti-paperclip ms-1 text-muted" title="Has attachments" style="font-size: 0.7rem;"></i>
                                            <?php endif; ?>
                                            <?php if($message->priority == 'urgent'): ?>
                                                <i class="ti ti-alert text-danger ms-1" title="Urgent" style="font-size: 0.7rem;"></i>
                                            <?php endif; ?>
                                        </td>
                                        <td class="py-1">
                                            <?php echo $message->priority_badge ?? '<span class="badge bg-secondary" style="font-size: 0.6rem;">Normal</span>'; ?>

                                        </td>
                                        <td class="py-1 text-muted">
                                            <div title="<?php echo e($message->created_at->format('Y-m-d H:i:s')); ?>" style="font-size: 0.65rem;">
                                                <?php echo e($message->created_at->diffForHumans()); ?>

                                            </div>
                                            <?php if($message->deadline && $message->deadline->isPast()): ?>
                                                <span class="badge bg-warning mt-1" style="font-size: 0.55rem;">Overdue</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="pe-2 py-1">
                                            <div class="d-flex gap-1">
                                                <a href="<?php echo e(route('messages.show', $message->id)); ?>" class="btn btn-sm btn-outline-primary rounded-sm" style="font-size: 0.6rem; padding: 0.2rem 0.4rem;" title="View">
                                                    <i class="ti ti-eye"></i>
                                                </a>
                                                <a href="<?php echo e(route('messages.create', ['reply_to' => $message->id])); ?>" class="btn btn-sm btn-outline-success rounded-sm" style="font-size: 0.6rem; padding: 0.2rem 0.4rem;" title="Reply">
                                                    <i class="ti ti-corner-up-right"></i>
                                                </a>
                                                <button type="button" 
                                                        onclick="showDeleteModal(<?php echo e($message->id); ?>, '<?php echo e(addslashes($message->subject)); ?>')" 
                                                        class="btn btn-sm btn-outline-danger rounded-sm" 
                                                        style="font-size: 0.6rem; padding: 0.2rem 0.4rem;" 
                                                        title="Delete">
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </form>
                </div>
                <div class="card-footer py-1 px-2 bg-light">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <button type="button" class="btn btn-sm btn-danger" id="bulkDeleteBtn" style="display: none; font-size: 0.65rem; padding: 0.2rem 0.5rem;">
                            <i class="ti ti-trash me-1"></i> Delete Selected
                        </button>
                        <div class="ms-auto">
                            <?php echo e($messages->links()); ?>

                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="ti ti-inbox" style="font-size: 48px; color: #ccc;"></i>
                    <p class="mt-3 text-muted" style="font-size: 0.75rem;">No messages in your inbox</p>
                    <a href="<?php echo e(route('messages.create')); ?>" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
                        <i class="ti ti-plus me-1"></i> Compose Message
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Single Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-sm">
            <div class="modal-header py-1 px-2">
                <h5 class="modal-title" style="font-size: 0.8rem;">Confirm Delete</h5>
                <button type="button" class="btn-close py-0" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body py-2 px-2">
                <p style="font-size: 0.75rem;">Are you sure you want to delete message <strong id="deleteMessageSubject"></strong>?</p>
                <p class="text-danger mb-0" style="font-size: 0.7rem;">This action cannot be undone.</p>
            </div>
            <div class="modal-footer py-1 px-2">
                <button type="button" class="btn btn-secondary btn-sm rounded-sm" data-bs-dismiss="modal" style="font-size: 0.7rem;">Cancel</button>
                <a href="#" id="deleteConfirmBtn" class="btn btn-danger btn-sm rounded-sm" style="font-size: 0.7rem;">Delete</a>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Delete Confirmation Modal -->
<div class="modal fade" id="bulkDeleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-sm">
            <div class="modal-header py-1 px-2">
                <h5 class="modal-title" style="font-size: 0.8rem;">Confirm Bulk Delete</h5>
                <button type="button" class="btn-close py-0" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body py-2 px-2">
                <p style="font-size: 0.75rem;">Are you sure you want to delete <strong id="bulkDeleteCount"></strong> selected message(s)?</p>
                <p class="text-danger mb-0" style="font-size: 0.7rem;">This action cannot be undone.</p>
            </div>
            <div class="modal-footer py-1 px-2">
                <button type="button" class="btn btn-secondary btn-sm rounded-sm" data-bs-dismiss="modal" style="font-size: 0.7rem;">Cancel</button>
                <button type="button" id="bulkDeleteConfirmBtn" class="btn btn-danger btn-sm rounded-sm" style="font-size: 0.7rem;">Delete All</button>
            </div>
        </div>
    </div>
</div>

<script>
    // Select all checkbox
    const selectAllCheckbox = document.getElementById('selectAll');
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function(e) {
            document.querySelectorAll('.message-checkbox').forEach(cb => cb.checked = e.target.checked);
            toggleBulkDeleteBtn();
        });
    }
    
    document.querySelectorAll('.message-checkbox').forEach(cb => {
        cb.addEventListener('change', toggleBulkDeleteBtn);
    });
    
    function toggleBulkDeleteBtn() {
        const checked = document.querySelectorAll('.message-checkbox:checked').length > 0;
        const btn = document.getElementById('bulkDeleteBtn');
        if (btn) {
            btn.style.display = checked ? 'inline-block' : 'none';
        }
    }
    
    // Single Delete Modal Function
    function showDeleteModal(id, subject) {
        document.getElementById('deleteMessageSubject').innerText = subject;
        document.getElementById('deleteConfirmBtn').href = '/messages/' + id;
        new bootstrap.Modal(document.getElementById('deleteModal')).show();
    }
    
    // Bulk Delete - Show modal with count
    const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
    if (bulkDeleteBtn) {
        bulkDeleteBtn.addEventListener('click', function() {
            const checkedCount = document.querySelectorAll('.message-checkbox:checked').length;
            if (checkedCount === 0) {
                alert('Please select at least one message to delete.');
                return;
            }
            document.getElementById('bulkDeleteCount').innerText = checkedCount;
            new bootstrap.Modal(document.getElementById('bulkDeleteModal')).show();
        });
    }
    
    // Bulk Delete Confirmation
    const bulkDeleteConfirmBtn = document.getElementById('bulkDeleteConfirmBtn');
    if (bulkDeleteConfirmBtn) {
        bulkDeleteConfirmBtn.addEventListener('click', function() {
            document.getElementById('bulkDeleteForm').submit();
        });
    }
</script>

<style>
/* Table hover effect */
.table-hover tbody tr:hover {
    background-color: #fef2e8;
    cursor: pointer;
}

/* Unread message styling */
tbody tr.fw-bold td:not(.text-muted) {
    font-weight: 600;
}

/* Compact table */
.table td, .table th {
    padding: 0.5rem 0.5rem;
    vertical-align: middle;
}
</style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\wamp64\www\unileave\resources\views/shared/messages/inbox.blade.php ENDPATH**/ ?>