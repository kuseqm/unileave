

<?php $__env->startSection('title', $forwardMessage ? 'Forward Message' : ($replyTo ? 'Reply to Message' : 'Compose Message')); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-2">
    <!-- Breadcrumb -->
<?php if (isset($component)) { $__componentOriginale19f62b34dfe0bfdf95075badcb45bc2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale19f62b34dfe0bfdf95075badcb45bc2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.breadcrumb','data' => ['items' => [
    ['name' => 'Messages', 'url' => route('messages.inbox')],
    ['name' => 'Compose', 'url' => '#']
    ]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('breadcrumb'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['items' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
    ['name' => 'Messages', 'url' => route('messages.inbox')],
    ['name' => 'Compose', 'url' => '#']
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => $forwardMessage ? 'FORWARD MESSAGE' : ($replyTo ? 'REPLY TO MESSAGE' : 'COMPOSE MESSAGE'),'description' => $forwardMessage ? 'Forward this message to new recipients' : ($replyTo ? 'Send a reply to the message' : 'Send a new message')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($forwardMessage ? 'FORWARD MESSAGE' : ($replyTo ? 'REPLY TO MESSAGE' : 'COMPOSE MESSAGE')),'description' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($forwardMessage ? 'Forward this message to new recipients' : ($replyTo ? 'Send a reply to the message' : 'Send a new message'))]); ?>
        <a href="<?php echo e(route('messages.inbox')); ?>" class="btn btn-outline-secondary btn-sm rounded-sm">
            <i class="ti ti-arrow-left me-1"></i> Back to Inbox
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

    <!-- ERROR DISPLAY SECTION -->
    <?php if($errors->any()): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Error!</strong> Please fix the following:
            <ul class="mb-0 mt-2">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo e(session('error')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row g-2">
        <div class="col-lg-8">
            <div class="card rounded-sm">
                <div class="card-body p-3">
                    <form action="<?php echo e(route('messages.store')); ?>" method="POST" enctype="multipart/form-data" id="messageForm">
                        <?php echo csrf_field(); ?>
                        
                        <!-- To: Field with AJAX Search -->
                        <div class="mb-2">
                            <label class="form-label fw-semibold">
                                To: 
                                <span class="text-muted" id="recipientCount"></span>
                            </label>
                            
                            <!-- Hidden input to ensure empty array is sent -->
                            <input type="hidden" name="recipient_ids" value="">
                            
                            <select name="recipient_ids[]" id="userSelect" class="form-select <?php $__errorArgs = ['recipient_ids'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" multiple="multiple" style="width: 100%;">
                                <?php
                                    // Get pre-selected recipients for Reply All or Reply
                                    $preselectedIds = [];
                                    if(!empty($replyAllRecipients)) {
                                        $preselectedIds = $replyAllRecipients;
                                    } elseif($selectedUser) {
                                        $preselectedIds = is_array($selectedUser) ? $selectedUser : [$selectedUser];
                                    } elseif(old('recipient_ids')) {
                                        $preselectedIds = is_array(old('recipient_ids')) ? old('recipient_ids') : [old('recipient_ids')];
                                    }
                                ?>
                                
                                <?php if(!empty($preselectedIds)): ?>
                                    <?php
                                        $preselectedUsers = \App\Models\User::whereIn('id', $preselectedIds)->get();
                                    ?>
                                    <?php $__currentLoopData = $preselectedUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($user->id); ?>" selected>
                                            <?php echo e($user->first_name); ?> <?php echo e($user->last_name); ?> (<?php echo e($user->staff_id ?? $user->email); ?>)
                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php endif; ?>
                            </select>
                            <small class="text-muted">Type at least 2 characters to search. Select multiple recipients (max 20).</small>
                            <?php $__errorArgs = ['recipient_ids'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <!-- Info Alerts for Reply/Reply All/Forward -->
                        <?php if(isset($replyTo) && $replyTo): ?>
                            <div class="alert alert-info py-1 px-2 small">
                                <i class="ti ti-corner-up-right me-1"></i> 
                                <strong>Replying to:</strong> <?php echo e($replyTo->sender->first_name); ?> <?php echo e($replyTo->sender->last_name); ?>

                                <?php if(!empty($replyAllRecipients)): ?>
                                    <br><small>Reply All mode: Also sending to other recipients on this conversation</small>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <?php if(isset($forwardMessage) && $forwardMessage): ?>
                            <div class="alert alert-secondary py-1 px-2 small">
                                <i class="ti ti-corner-up-left me-1"></i> 
                                <strong>Forwarding message from:</strong> <?php echo e($forwardMessage->sender->first_name); ?> <?php echo e($forwardMessage->sender->last_name); ?>

                                <br><small>Original sent: <?php echo e($forwardMessage->created_at ? $forwardMessage->created_at->format('d M Y, H:i') : 'Unknown'); ?></small>
                            </div>
                        <?php endif; ?>

                        <!-- Subject -->
                        <div class="mb-2">
                            <label class="form-label fw-semibold">Subject:</label>
                            <input type="text" name="subject" class="form-control <?php $__errorArgs = ['subject'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                   value="<?php echo e(old('subject', isset($subject) ? $subject : (isset($replyTo) ? 'Re: ' . $replyTo->subject : ''))); ?>" required>
                            <?php $__errorArgs = ['subject'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <!-- Priority, Type, Deadline -->
                        <div class="row g-2 mb-2">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Priority:</label>
                                <select name="priority" class="form-select">
                                    <option value="normal" <?php echo e(old('priority') == 'normal' ? 'selected' : ''); ?>>Normal</option>
                                    <option value="high" <?php echo e(old('priority') == 'high' ? 'selected' : ''); ?>>High</option>
                                    <option value="urgent" <?php echo e(old('priority') == 'urgent' ? 'selected' : ''); ?>>Urgent</option>
                                    <option value="low" <?php echo e(old('priority') == 'low' ? 'selected' : ''); ?>>Low</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Type:</label>
                                <select name="message_type" class="form-select">
                                    <option value="general" <?php echo e(old('message_type') == 'general' ? 'selected' : ''); ?>>General</option>
                                    <option value="approval" <?php echo e(old('message_type') == 'approval' ? 'selected' : ''); ?>>Approval Request</option>
                                    <option value="task" <?php echo e(old('message_type') == 'task' ? 'selected' : ''); ?>>Task Assignment</option>
                                    <option value="minute" <?php echo e(old('message_type') == 'minute' ? 'selected' : ''); ?>>Minute</option>
                                    <option value="notification" <?php echo e(old('message_type') == 'notification' ? 'selected' : ''); ?>>Notification</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Deadline:</label>
                                <input type="date" name="deadline" class="form-control" value="<?php echo e(old('deadline')); ?>">
                            </div>
                        </div>

                        <!-- Message Body -->
                        <div class="mb-2">
                            <label class="form-label fw-semibold">Message:</label>
                            <textarea name="message" rows="8" class="form-control <?php $__errorArgs = ['message'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required><?php echo e(old('message', isset($messageContent) ? $messageContent : '')); ?></textarea>
                            <?php $__errorArgs = ['message'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <!-- Attachments -->
                        <div class="mb-2">
                            <label class="form-label fw-semibold">Attachments:</label>
                            <input type="file" name="attachments[]" class="form-control" multiple>
                            <small class="text-muted">Max 10MB per file. Allowed: PDF, DOC, DOCX, JPG, PNG</small>
                        </div>

                        <!-- Buttons -->
                        <div class="d-flex justify-content-end gap-2 mt-3">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="ti ti-send me-1"></i> 
                                <?php echo e(isset($forwardMessage) ? 'Forward Message' : (isset($replyTo) ? 'Send Reply' : 'Send Message')); ?>

                            </button>
                            <button type="button" class="btn btn-secondary btn-sm" onclick="window.location.href='<?php echo e(route('messages.inbox')); ?>'">
                                <i class="ti ti-x me-1"></i> Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <i class="ti ti-tips me-1"></i> Tips
                </div>
                <div class="card-body p-2 small">
                    <ul class="mb-0 ps-3">
                        <li><strong>Search:</strong> Type name, email, or staff ID (min 2 characters)</li>
                        <li><strong>Select:</strong> Click on search results to add recipients</li>
                        <li><strong>Remove:</strong> Click the 'x' on a recipient tag to remove</li>
                        <li><strong>Reply:</strong> Sends only to original sender</li>
                        <li><strong>Reply All:</strong> Sends to all original recipients</li>
                        <li><strong>Forward:</strong> Send to new recipients with original message quoted</li>
                        <li>Use high/urgent priority for time-sensitive matters</li>
                        <li>Set deadlines to track response times</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include Select2 CSS and JS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function() {
    // Initialize Select2 with AJAX search
    $('#userSelect').select2({
        placeholder: "Search by name, email, or staff ID...",
        allowClear: true,
        width: '100%',
        minimumInputLength: 2,
        multiple: true,
        maximumSelectionLength: 20,
        ajax: {
            url: '<?php echo e(route("messages.users.search")); ?>',
            dataType: 'json',
            delay: 300,
            data: function(params) {
                return {
                    q: params.term
                };
            },
            processResults: function(data) {
                return {
                    results: data.results
                };
            },
            cache: true
        },
        templateResult: formatUser,
        templateSelection: formatUserSelection
    });
    
    // Format how each user appears in the dropdown
    function formatUser(user) {
        if (user.loading) {
            return user.text;
        }
        
        var $container = $(
            '<div><strong>' + user.text.split(' - ')[0] + '</strong><br>' +
            '<small class="text-muted">' + (user.text.split(' - ')[1] || '') + '</small></div>'
        );
        return $container;
    }
    
    // Format how selected user appears in the tags
    function formatUserSelection(user) {
        if (user.text) {
            return user.text.split(' - ')[0];
        }
        return user.id;
    }
    
    // Update recipient count
    function updateRecipientCount() {
        var count = $('#userSelect').val() ? $('#userSelect').val().length : 0;
        var maxRecipients = 20;
        $('#recipientCount').text(count > 0 ? '(' + count + '/20 selected)' : '');
        
        if (count >= maxRecipients) {
            $('#recipientCount').addClass('text-warning');
        } else {
            $('#recipientCount').removeClass('text-warning');
        }
    }
    
    $('#userSelect').on('select2:select select2:unselect', updateRecipientCount);
    updateRecipientCount();
    
    // Validate before submit
    $('#messageForm').on('submit', function(e) {
        var selected = $('#userSelect').val();
        console.log('Selected recipient IDs:', selected);
        if (!selected || selected.length === 0) {
            e.preventDefault();
            alert('Please select at least one recipient.');
            return false;
        }
        return true;
    });
});
</script>

<style>
/* Select2 custom styling */
.select2-container--default .select2-selection--multiple {
    border-color: #e8e2d5;
    border-radius: 0.375rem;
    min-height: 80px;
}

.select2-container--default .select2-selection--multiple .select2-selection__choice {
    background-color: #eecbc4;
    border: none;
    border-radius: 0.25rem;
    color: #1e293b;
    font-size: 0.8rem;
    padding: 4px 8px;
}

.select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
    color: #1e293b;
    margin-right: 6px;
}

.select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
    background-color: transparent;
    color: #dc2626;
}

.select2-dropdown {
    border-color: #e8e2d5;
    border-radius: 0.375rem;
}

.select2-search__field {
    border-color: #e8e2d5 !important;
    border-radius: 0.375rem !important;
}
</style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\wamp64\www\unileave\resources\views/shared/messages/compose.blade.php ENDPATH**/ ?>