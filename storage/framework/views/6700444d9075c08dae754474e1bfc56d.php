

<?php $__env->startSection('title', 'System Portal'); ?>

<?php $__env->startSection('systems'); ?>
    <?php $__currentLoopData = $systems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $system): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <a href="<?php echo e(isset($system['coming_soon']) && $system['coming_soon'] ? '#' : $system['url']); ?>" 
       class="system-card <?php echo e(isset($system['coming_soon']) && $system['coming_soon'] ? 'coming-soon' : ''); ?>"
       <?php if(isset($system['coming_soon']) && $system['coming_soon']): ?> onclick="return false;" <?php endif; ?>>
        <div class="icon-circle bg-<?php echo e($system['color']); ?>">
            <?php if($system['icon'] == 'calendar'): ?>
                <i class="ti ti-calendar"></i>
            <?php elseif($system['icon'] == 'mail'): ?>
                <i class="ti ti-mail"></i>
            <?php elseif($system['icon'] == 'clipboard'): ?>
                <i class="ti ti-clipboard"></i>
            <?php elseif($system['icon'] == 'users'): ?>
                <i class="ti ti-users"></i>
            <?php elseif($system['icon'] == 'settings'): ?>
                <i class="ti ti-settings"></i>
            <?php else: ?>
                <i class="ti ti-apps"></i>
            <?php endif; ?>
        </div>
        <div class="system-name"><?php echo e($system['name']); ?></div>
        <div class="system-desc"><?php echo e($system['description']); ?></div>
        <?php if(isset($system['coming_soon']) && $system['coming_soon']): ?>
            <span class="badge bg-secondary rounded-sm coming-badge">Coming Soon</span>
        <?php else: ?>
            <span class="btn-access btn-<?php echo e($system['color']); ?>">Access System</span>
        <?php endif; ?>
    </a>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.portal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\wamp64\www\unileave\resources\views/portal/dashboard.blade.php ENDPATH**/ ?>