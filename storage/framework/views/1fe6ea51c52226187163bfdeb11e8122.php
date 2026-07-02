

<?php $__env->startSection('title', 'Message Metadata & Audit'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-2">
    
    <!-- Breadcrumb -->
    <?php if (isset($component)) { $__componentOriginale19f62b34dfe0bfdf95075badcb45bc2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale19f62b34dfe0bfdf95075badcb45bc2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.breadcrumb','data' => ['items' => [
        ['name' => 'Messages', 'url' => route('messages.inbox')],
        ['name' => 'Message Directory', 'url' => route('messages.admin-list')],
        ['name' => 'Message Metadata', 'url' => '#']
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
        ['name' => 'Message Metadata', 'url' => '#']
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'MESSAGE METADATA & AUDIT TRAIL','description' => 'Non-repudiation record - Content is encrypted']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'MESSAGE METADATA & AUDIT TRAIL','description' => 'Non-repudiation record - Content is encrypted']); ?>
        <a href="<?php echo e(route('messages.admin-list')); ?>" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
            <i class="ti ti-arrow-left me-1"></i> Back to Directory
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

    <div class="row g-3">
        <!-- LEFT COLUMN: Message Metadata (70%) -->
        <div class="col-lg-8">
            <!-- Message Metadata Card -->
            <div class="card rounded-sm mb-3">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-info-circle text-primary me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Message Metadata</span>
                    </div>
                </div>
                <div class="card-body p-2">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-2">
                                <span class="text-muted small" style="font-size: 0.65rem;">Message ID:</span>
                                <div class="small" style="font-size: 0.7rem;">#<?php echo e($message->id); ?></div>
                            </div>
                            <div class="mb-2">
                                <span class="text-muted small" style="font-size: 0.65rem;">Message Number:</span>
                                <div class="small" style="font-size: 0.7rem;"><?php echo e($message->message_number); ?></div>
                            </div>
                            <div class="mb-2">
                                <span class="text-muted small" style="font-size: 0.65rem;">Subject:</span>
                                <div class="small" style="font-size: 0.7rem;"><?php echo e($message->subject); ?></div>
                            </div>
                            <div class="mb-2">
                                <span class="text-muted small" style="font-size: 0.65rem;">Priority:</span>
                                <div><?php echo $message->priority_badge; ?></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-2">
                                <span class="text-muted small" style="font-size: 0.65rem;">From:</span>
                                <div class="small" style="font-size: 0.7rem;"><?php echo e($message->sender->first_name ?? 'System'); ?> <?php echo e($message->sender->last_name ?? ''); ?></div>
                                <div class="text-muted" style="font-size: 0.6rem;"><?php echo e($message->sender->email ?? ''); ?></div>
                            </div>
                            <div class="mb-2">
                                <span class="text-muted small" style="font-size: 0.65rem;">To:</span>
                                <div class="small" style="font-size: 0.7rem;"><?php echo e($message->recipient_names ?? $message->receiver->first_name ?? 'Unknown'); ?></div>
                            </div>
                            <div class="mb-2">
                                <span class="text-muted small" style="font-size: 0.65rem;">Status:</span>
                                <div><?php echo $message->status_badge; ?></div>
                            </div>
                        </div>
                    </div>
                    
                    <hr class="my-2">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-2">
                                <span class="text-muted small" style="font-size: 0.65rem;">Created:</span>
                                <div class="small" style="font-size: 0.7rem;"><?php echo e($message->created_at->format('d M Y, H:i:s')); ?></div>
                            </div>
                            <div class="mb-2">
                                <span class="text-muted small" style="font-size: 0.65rem;">Delivered:</span>
                                <div class="small" style="font-size: 0.7rem;"><?php echo e($message->delivered_at ? $message->delivered_at->format('d M Y, H:i:s') : 'N/A'); ?></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-2">
                                <span class="text-muted small" style="font-size: 0.65rem;">Read:</span>
                                <div class="small" style="font-size: 0.7rem;"><?php echo e($message->read_at ? $message->read_at->format('d M Y, H:i:s') : 'Unread'); ?></div>
                            </div>
                            <div class="mb-2">
                                <span class="text-muted small" style="font-size: 0.65rem;">Replied:</span>
                                <div class="small" style="font-size: 0.7rem;"><?php echo e($message->replied_at ? $message->replied_at->format('d M Y, H:i:s') : 'No'); ?></div>
                            </div>
                        </div>
                    </div>
                    
                    <?php if($message->deleted_at): ?>
                        <div class="alert alert-warning py-1 px-2 mb-0 mt-2" style="font-size: 0.65rem;">
                            <i class="ti ti-trash me-1"></i> Soft deleted on <?php echo e($message->deleted_at->format('d M Y, H:i:s')); ?>

                        </div>
                    <?php endif; ?>
                    
                    <!-- Content Access Warning -->
                    <div class="alert alert-warning mt-3 mb-0 py-1 px-2" style="font-size: 0.65rem;">
                        <i class="ti ti-lock me-1"></i>
                        <strong>Content Encrypted</strong> - The actual message content is encrypted and cannot be viewed here.
                    </div>
                </div>
            </div>
            
            <!-- Access Requests History -->
            <div class="card rounded-sm mb-3">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-key text-primary me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Access Request History</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <?php if($message->accessRequests->count() > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-sm mb-0" style="font-size: 0.75rem;">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="py-1">Requested By</th>
                                        <th class="py-1">Date</th>
                                        <th class="py-1">Reason</th>
                                        <th class="py-1">Status</th>
                                        <th class="py-1">Code Used</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $message->accessRequests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $request): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td class="py-1"><?php echo e($request->requestedBy->first_name ?? ''); ?> <?php echo e($request->requestedBy->last_name ?? ''); ?></td>
                                        <td class="py-1"><?php echo e($request->created_at->format('d M Y')); ?></td>
                                        <td class="py-1"><?php echo e(Str::limit($request->reason, 40)); ?></td>
                                        <td class="py-1">
                                            <span class="badge bg-<?php echo e($request->status == 'approved' ? 'success' : ($request->status == 'pending' ? 'warning' : 'danger')); ?> rounded-sm" style="font-size: 0.6rem;">
                                                <?php echo e(ucfirst($request->status)); ?>

                                            </span>
                                        </td>
                                        <td class="py-1">
                                            <?php if($request->viewed_at): ?>
                                                <i class="ti ti-check-circle text-success"></i> Used
                                            <?php elseif($request->status == 'approved'): ?>
                                                <i class="ti ti-clock text-warning"></i> Pending
                                            <?php else: ?>
                                                —
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-3">
                            <p class="text-muted mb-0" style="font-size: 0.7rem;">No access requests for this message.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Audit Log -->
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-history text-primary me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Activity Timeline</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <?php if($message->auditLogs->count() > 0): ?>
                        <div class="timeline p-2">
                            <?php $__currentLoopData = $message->auditLogs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="timeline-item mb-2 pb-2 border-bottom">
                                    <div class="d-flex gap-2">
                                        <div class="timeline-icon">
                                            <?php if($log->action == 'accessed_via_code'): ?>
                                                <i class="ti ti-key text-warning"></i>
                                            <?php elseif($log->action == 'soft_deleted'): ?>
                                                <i class="ti ti-trash text-danger"></i>
                                            <?php elseif($log->action == 'viewed'): ?>
                                                <i class="ti ti-eye text-info"></i>
                                            <?php elseif($log->action == 'replied'): ?>
                                                <i class="ti ti-corner-up-right text-success"></i>
                                            <?php else: ?>
                                                <i class="ti ti-check-circle text-success"></i>
                                            <?php endif; ?>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="fw-semibold" style="font-size: 0.7rem;"><?php echo e(ucfirst(str_replace('_', ' ', $log->action))); ?></div>
                                            <div class="text-muted" style="font-size: 0.6rem;"><?php echo e($log->user->first_name ?? 'System'); ?> <?php echo e($log->user->last_name ?? ''); ?> • <?php echo e($log->created_at->format('d M Y, H:i:s')); ?></div>
                                            <?php if($log->ip_address): ?>
                                                <div class="text-muted" style="font-size: 0.6rem;">IP: <?php echo e($log->ip_address); ?></div>
                                            <?php endif; ?>
                                            <?php if($log->one_time_code_used): ?>
                                                <div class="text-muted" style="font-size: 0.6rem;">One-time code used: <code><?php echo e($log->one_time_code_used); ?></code></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-3">
                            <p class="text-muted mb-0" style="font-size: 0.7rem;">No activity recorded for this message.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN: Actions Panel (30%) -->
        <div class="col-lg-4">
            <!-- Actions Card -->
            <div class="card rounded-sm border-0 shadow-sm mb-3">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-settings text-primary me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Actions</span>
                    </div>
                </div>
                <div class="card-body p-2">
                    <!-- Restore Button (only for soft-deleted messages) -->
                    <?php if($message->trashed()): ?>
                        <div class="mb-2">
                            <button onclick="restoreMessage(<?php echo e($message->id); ?>)" class="btn btn-outline-success btn-sm w-100 rounded-sm" style="font-size: 0.7rem; padding: 0.3rem 0.5rem;">
                                <i class="ti ti-reload me-1"></i> Restore Message
                            </button>
                            <p class="text-muted mt-1 mb-0" style="font-size: 0.6rem;">Restore this message from soft delete.</p>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Request Access Button (HR only, for active messages) -->
                    <?php if(auth()->user()->hasRole('hr_manager') && !$message->trashed()): ?>
                        <div class="mb-2">
                            <button onclick="requestAccess(<?php echo e($message->id); ?>)" class="btn btn-outline-warning btn-sm w-100 rounded-sm" style="font-size: 0.7rem; padding: 0.3rem 0.5rem;">
                                <i class="ti ti-key me-1"></i> Request Access to Content
                            </button>
                            <p class="text-muted mt-1 mb-0" style="font-size: 0.6rem;">Request VC approval to view encrypted message content.</p>
                        </div>
                    <?php endif; ?>
                    
                    <!-- View Content (Only for sender/receiver or VC-approved) -->
                    <?php if($message->canViewContent(auth()->user())): ?>
                        <div class="mb-2">
                            <a href="<?php echo e(route('messages.secure-content', $message->id)); ?>" class="btn btn-primary btn-sm w-100 rounded-sm" style="font-size: 0.7rem; padding: 0.3rem 0.5rem;">
                                <i class="ti ti-eye me-1"></i> View Message Content
                            </a>
                            <p class="text-muted mt-1 mb-0" style="font-size: 0.6rem;">You have permission to view the actual message content.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- About This Page -->
            <div class="card rounded-sm border-0 shadow-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-info-circle text-primary me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">About This Page</span>
                    </div>
                </div>
                <div class="card-body p-2">
                    <div class="mb-2">
                        <i class="ti ti-shield text-primary me-1" style="font-size: 0.7rem;"></i>
                        <strong class="small" style="font-size: 0.7rem;">Non-Repudiation</strong>
                        <p class="text-muted mt-1 mb-0" style="font-size: 0.65rem;">This page provides complete metadata and audit trail for compliance purposes.</p>
                    </div>
                    
                    <div class="mb-2">
                        <i class="ti ti-key text-info me-1" style="font-size: 0.7rem;"></i>
                        <strong class="small" style="font-size: 0.7rem;">Access Request Process</strong>
                        <p class="text-muted mt-1 mb-0" style="font-size: 0.65rem;">HR can request access to message content. VC approves and generates a one-time code valid for 24 hours.</p>
                    </div>
                    
                    <div class="alert alert-info py-1 px-2 mb-0" style="font-size: 0.65rem;">
                        <i class="ti ti-tips me-1"></i>
                        <strong>Note:</strong> All timestamps are in your local timezone. Data is retained for 7 years as required by law.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function restoreMessage(id) {
    if(confirm('Restore this message? It will become active again.')) {
        fetch(`/messages/${id}/restore`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>' }
        }).then(() => window.location.href = '<?php echo e(route("messages.admin-list")); ?>');
    }
}

function requestAccess(id) {
    let reason = prompt('Please provide a detailed reason for accessing this message content (min 10 characters):');
    if (reason && reason.length >= 10) {
        fetch(`/messages/${id}/request-access`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ reason: reason })
        }).then(response => response.json())
          .then(data => alert(data.message || 'Request sent to VC for approval'))
          .catch(() => alert('Request sent to VC for approval'));
    } else if (reason) {
        alert('Reason must be at least 10 characters');
    }
}
</script>

<style>
.timeline-icon {
    width: 28px;
    text-align: center;
}
.timeline-item:last-child {
    border-bottom: none !important;
}
</style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\wamp64\www\unileave\resources\views/shared/messages/metadata.blade.php ENDPATH**/ ?>