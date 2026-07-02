

<?php $__env->startSection('title', 'Notification Settings'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-2">
    
    <?php if (isset($component)) { $__componentOriginale19f62b34dfe0bfdf95075badcb45bc2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale19f62b34dfe0bfdf95075badcb45bc2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.breadcrumb','data' => ['items' => [
        ['name' => 'Profile', 'url' => '#'],
        ['name' => 'Notifications', 'url' => '#']
    ]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('breadcrumb'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['items' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
        ['name' => 'Profile', 'url' => '#'],
        ['name' => 'Notifications', 'url' => '#']
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'NOTIFICATION SETTINGS','description' => 'Configure how you receive notifications']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'NOTIFICATION SETTINGS','description' => 'Configure how you receive notifications']); ?>
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
    
    <div class="row">
        <div class="col-md-6">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <i class="ti ti-bell me-1"></i>
                    <span style="font-weight: 600;">Notification Preferences</span>
                </div>
                <div class="card-body">
                    <form method="POST" action="<?php echo e(route('profile.notifications.update')); ?>">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PUT'); ?>
                        
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input type="hidden" name="email_notifications" value="0">
                                <input type="checkbox" class="form-check-input" name="email_notifications" value="1" id="emailNotifications" 
                                    <?php echo e(($emailNotifications && $emailNotifications->value == 'true') ? 'checked' : ''); ?>>
                                <label class="form-check-label" for="emailNotifications">
                                    Email Notifications
                                </label>
                            </div>
                            <small class="text-muted">Receive email notifications for leave approvals and updates</small>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input type="hidden" name="browser_notifications" value="0">
                                <input type="checkbox" class="form-check-input" name="browser_notifications" value="1" id="browserNotifications"
                                    <?php echo e(($browserNotifications && $browserNotifications->value == 'true') ? 'checked' : ''); ?>>
                                <label class="form-check-label" for="browserNotifications">
                                    Browser Notifications
                                </label>
                            </div>
                            <small class="text-muted">Receive browser popup notifications</small>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Reminder Days Before Leave</label>
                            <select name="reminder_days" class="form-select form-select-sm">
                                <option value="0" <?php echo e(($reminderDays && $reminderDays->value == '0') ? 'selected' : ''); ?>>No reminders</option>
                                <option value="1" <?php echo e(($reminderDays && $reminderDays->value == '1') ? 'selected' : ''); ?>>1 day before</option>
                                <option value="3" <?php echo e(($reminderDays && $reminderDays->value == '3') ? 'selected' : ''); ?>>3 days before</option>
                                <option value="7" <?php echo e(($reminderDays && $reminderDays->value == '7') ? 'selected' : ''); ?>>7 days before</option>
                                <option value="14" <?php echo e(($reminderDays && $reminderDays->value == '14') ? 'selected' : ''); ?>>14 days before</option>
                            </select>
                            <small class="text-muted">Get reminded before your leave starts</small>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-sm rounded-sm">
                            <i class="ti ti-device-floppy me-1"></i> Save Notification Settings
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <i class="ti ti-bell-ringing me-1"></i>
                    <span style="font-weight: 600;">Recent Notifications</span>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <?php
                            // Get notifications from your custom notifications table
                            $recentNotifications = \App\Models\Notification::where('user_id', auth()->id())
                                ->orderBy('created_at', 'desc')
                                ->limit(5)
                                ->get();
                        ?>
                        
                        <?php $__empty_1 = true; $__currentLoopData = $recentNotifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="list-group-item py-2 px-2 <?php echo e($notification->is_read ? '' : 'bg-light'); ?>">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <strong style="font-size: 0.7rem;"><?php echo e($notification->title); ?></strong>
                                    <p class="mb-0 text-muted" style="font-size: 0.65rem;"><?php echo e(Str::limit($notification->message, 100)); ?></p>
                                </div>
                                <small class="text-muted" style="font-size: 0.6rem;"><?php echo e($notification->created_at->diffForHumans()); ?></small>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="list-group-item py-3 text-center text-muted">
                            <i class="ti ti-bell-off fs-4"></i>
                            <p class="mb-0">No notifications yet</p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php if(\App\Models\Notification::where('user_id', auth()->id())->count() > 5): ?>
                <div class="card-footer py-1 px-2 text-center">
                    <a href="<?php echo e(route('notifications.index')); ?>" class="btn btn-sm btn-link" style="font-size: 0.7rem;">
                        View All Notifications
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\wamp64\www\unileave\resources\views/profile/notifications.blade.php ENDPATH**/ ?>