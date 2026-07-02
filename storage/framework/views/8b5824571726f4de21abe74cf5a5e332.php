

<?php $__env->startSection('title', 'My Request Status'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-2">
    
    <!-- Breadcrumb -->
    <?php if (isset($component)) { $__componentOriginale19f62b34dfe0bfdf95075badcb45bc2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale19f62b34dfe0bfdf95075badcb45bc2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.breadcrumb','data' => ['items' => [
        ['name' => 'Dashboard', 'url' => route('dashboard')],
        ['name' => 'My Request Status', 'url' => '#']
    ]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('breadcrumb'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['items' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
        ['name' => 'Dashboard', 'url' => route('dashboard')],
        ['name' => 'My Request Status', 'url' => '#']
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'MY REQUEST STATUS','description' => 'Track the progress of your leave requests']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'MY REQUEST STATUS','description' => 'Track the progress of your leave requests']); ?>
        <a href="<?php echo e(route('leave-requests.create')); ?>" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
            <i class="ti ti-plus me-1"></i> New Request
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
    
    <!-- Stats Cards with Clickable Links (using built-in link parameter) -->
    <div class="row g-2 mb-3">
        <?php if (isset($component)) { $__componentOriginal8f216e051c231b98198765acd723fb77 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8f216e051c231b98198765acd723fb77 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stats-card','data' => ['value' => ''.e($stats['total']).'','label' => 'Total Requests','subtext' => 'all time','icon' => 'clipboard-list','link' => ''.e(route('leave-requests.status', ['filter' => 'all'])).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('stats-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => ''.e($stats['total']).'','label' => 'Total Requests','subtext' => 'all time','icon' => 'clipboard-list','link' => ''.e(route('leave-requests.status', ['filter' => 'all'])).'']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stats-card','data' => ['value' => ''.e($stats['pending']).'','label' => 'Pending','subtext' => 'awaiting approval','color' => 'warning','icon' => 'clock','link' => ''.e(route('leave-requests.status', ['filter' => 'pending'])).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('stats-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => ''.e($stats['pending']).'','label' => 'Pending','subtext' => 'awaiting approval','color' => 'warning','icon' => 'clock','link' => ''.e(route('leave-requests.status', ['filter' => 'pending'])).'']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stats-card','data' => ['value' => ''.e($stats['approved']).'','label' => 'Approved','subtext' => 'granted','color' => 'success','icon' => 'check','link' => ''.e(route('leave-requests.status', ['filter' => 'approved'])).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('stats-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => ''.e($stats['approved']).'','label' => 'Approved','subtext' => 'granted','color' => 'success','icon' => 'check','link' => ''.e(route('leave-requests.status', ['filter' => 'approved'])).'']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stats-card','data' => ['value' => ''.e($stats['rejected']).'','label' => 'Rejected','subtext' => 'denied','color' => 'danger','icon' => 'x','link' => ''.e(route('leave-requests.status', ['filter' => 'rejected'])).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('stats-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => ''.e($stats['rejected']).'','label' => 'Rejected','subtext' => 'denied','color' => 'danger','icon' => 'x','link' => ''.e(route('leave-requests.status', ['filter' => 'rejected'])).'']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stats-card','data' => ['value' => ''.e($stats['cancelled']).'','label' => 'Cancelled','subtext' => 'cancelled by you','color' => 'secondary','icon' => 'trash','link' => ''.e(route('leave-requests.status', ['filter' => 'cancelled'])).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('stats-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => ''.e($stats['cancelled']).'','label' => 'Cancelled','subtext' => 'cancelled by you','color' => 'secondary','icon' => 'trash','link' => ''.e(route('leave-requests.status', ['filter' => 'cancelled'])).'']); ?>
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
    
    <?php if(session('success')): ?>
    <div class="alert alert-success alert-dismissible fade show py-1 px-2 mb-2 rounded-sm" style="font-size: 0.7rem;" role="alert">
        <i class="ti ti-check-circle me-1"></i> <?php echo e(session('success')); ?>

        <button type="button" class="btn-close py-0" style="font-size: 0.6rem;" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>
    
    <?php if(session('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show py-1 px-2 mb-2 rounded-sm" style="font-size: 0.7rem;" role="alert">
        <i class="ti ti-alert-circle me-1"></i> <?php echo e(session('error')); ?>

        <button type="button" class="btn-close py-0" style="font-size: 0.6rem;" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>
    
    <!-- Requests List -->
    <div class="card rounded-sm">
        <div class="card-header py-2 px-3 bg-light">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <i class="ti ti-list text-primary me-1" style="font-size: 0.8rem;"></i>
                    <span style="font-size: 0.75rem; font-weight: 600;">My Leave Requests</span>
                </div>
                <div class="text-muted" style="font-size: 0.6rem;">
                    Showing <?php echo e($leaveRequests->firstItem() ?? 0); ?> to <?php echo e($leaveRequests->lastItem() ?? 0); ?> of <?php echo e($leaveRequests->total() ?? 0); ?>

                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <?php $__empty_1 = true; $__currentLoopData = $leaveRequests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $request): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="request-item p-3 border-bottom">
                <!-- Request Header -->
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-2">
                    <div>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <span class="fw-semibold" style="font-size: 0.85rem;">
                                <?php echo e($request->leaveType->name ?? 'Leave Request'); ?>

                            </span>
                            <span class="badge <?php echo e($request->status == 'pending' ? 'bg-warning-subtle text-warning' : 
                                ($request->status == 'approved' ? 'bg-success-subtle text-success' : 
                                ($request->status == 'rejected' ? 'bg-danger-subtle text-danger' : 'bg-secondary-subtle text-secondary'))); ?> rounded-sm px-2 py-1" style="font-size: 0.6rem;">
                                <?php echo e(ucfirst($request->status)); ?>

                            </span>
                            <?php if($request->current_approval_level): ?>
                            <span class="badge bg-info-subtle text-info rounded-sm px-2 py-1" style="font-size: 0.6rem;">
                                <?php
                                    $levelNames = [1 => 'HOD', 2 => 'Dean', 3 => 'HR', 4 => 'DVC', 5 => 'HR Logistics'];
                                ?>
                                <?php echo e($levelNames[$request->current_approval_level] ?? 'Processing'); ?>

                            </span>
                            <?php endif; ?>
                        </div>
                        <div class="text-muted" style="font-size: 0.65rem; margin-top: 4px;">
                            <i class="ti ti-calendar me-1"></i>
                            <?php echo e(\Carbon\Carbon::parse($request->start_date)->format('M d, Y')); ?> - 
                            <?php echo e(\Carbon\Carbon::parse($request->end_date)->format('M d, Y')); ?>

                            <span class="mx-1">•</span>
                            <i class="ti ti-clock me-1"></i><?php echo e($request->days); ?> days
                        </div>
                    </div>
                    <div>
                        <a href="<?php echo e(route('leave-requests.show', $request->id)); ?>" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.6rem; padding: 0.2rem 0.5rem;">
                            <i class="ti ti-eye me-1"></i> Details
                        </a>
                        <?php if($request->status == 'pending' && !$request->current_approval_level): ?>
                        <button onclick="cancelRequest(<?php echo e($request->id); ?>)" class="btn btn-outline-danger btn-sm rounded-sm" style="font-size: 0.6rem; padding: 0.2rem 0.5rem;">
                            <i class="ti ti-x me-1"></i> Cancel
                        </button>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Approval Tracker (using FULL tracker with details) -->
                <div class="mt-2">
                    <?php if (isset($component)) { $__componentOriginal80df03d7f16561326fefb1c7a022ccf1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal80df03d7f16561326fefb1c7a022ccf1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.approval-tracker-full','data' => ['leaveRequest' => $request]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('approval-tracker-full'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['leaveRequest' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($request)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal80df03d7f16561326fefb1c7a022ccf1)): ?>
<?php $attributes = $__attributesOriginal80df03d7f16561326fefb1c7a022ccf1; ?>
<?php unset($__attributesOriginal80df03d7f16561326fefb1c7a022ccf1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal80df03d7f16561326fefb1c7a022ccf1)): ?>
<?php $component = $__componentOriginal80df03d7f16561326fefb1c7a022ccf1; ?>
<?php unset($__componentOriginal80df03d7f16561326fefb1c7a022ccf1); ?>
<?php endif; ?>
                </div>
                
                <!-- Brief Timeline Summary -->
                <?php if($request->approvalLogs && $request->approvalLogs->isNotEmpty()): ?>
                <div class="mt-2 pt-2 border-top" style="border-color: #E8E2D5;">
                    <div class="d-flex flex-wrap gap-3 align-items-center">
                        <span class="text-muted" style="font-size: 0.55rem;">Latest activity:</span>
                        <?php $__currentLoopData = $request->approvalLogs->take(2); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="d-flex align-items-center gap-1">
                            <i class="ti ti-<?php echo e($log->action == 'approved' ? 'check-circle' : ($log->action == 'rejected' ? 'x-circle' : 'clock')); ?>" 
                               style="font-size: 0.6rem; color: <?php echo e($log->action == 'approved' ? '#10B981' : ($log->action == 'rejected' ? '#EF4444' : '#F59E0B')); ?>;"></i>
                            <span style="font-size: 0.6rem;">
                                <?php echo e(ucfirst($log->action)); ?> @ <?php echo e($log->created_at->format('M d, H:i')); ?>

                                <?php if($log->approver): ?>
                                by <?php echo e($log->approver->first_name ?? ''); ?>

                                <?php endif; ?>
                            </span>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php if($request->approvalLogs->count() > 2): ?>
                        <span class="text-muted" style="font-size: 0.55rem;">+<?php echo e($request->approvalLogs->count() - 2); ?> more</span>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="text-center py-4">
                <i class="ti ti-calendar-off" style="font-size: 2rem; color: #CBD5E1;"></i>
                <p class="text-muted mt-2" style="font-size: 0.75rem;">You haven't submitted any leave requests yet.</p>
                <a href="<?php echo e(route('leave-requests.create')); ?>" class="btn btn-primary btn-sm rounded-sm mt-2" style="font-size: 0.7rem;">
                    <i class="ti ti-plus me-1"></i> Submit Your First Request
                </a>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Pagination -->
        <?php if($leaveRequests->hasPages()): ?>
        <div class="card-footer py-2 px-3 bg-light">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <span class="text-muted" style="font-size: 0.65rem;">Show:</span>
                    <select id="perPageSelect" class="custom-select-sm" style="font-size: 0.65rem; padding: 0.2rem 1.2rem 0.2rem 0.4rem; border: 1px solid #E8E2D5; border-radius: 4px;">
                        <option value="10" <?php echo e($leaveRequests->perPage() == 10 ? 'selected' : ''); ?>>10</option>
                        <option value="25" <?php echo e($leaveRequests->perPage() == 25 ? 'selected' : ''); ?>>25</option>
                        <option value="50" <?php echo e($leaveRequests->perPage() == 50 ? 'selected' : ''); ?>>50</option>
                        <option value="100" <?php echo e($leaveRequests->perPage() == 100 ? 'selected' : ''); ?>>100</option>
                    </select>
                </div>
                
                <div>
                    <?php echo e($leaveRequests->appends(request()->query())->links('pagination::bootstrap-5')); ?>

                </div>
                
                <div class="text-muted" style="font-size: 0.6rem;">
                    Page <?php echo e($leaveRequests->currentPage()); ?> of <?php echo e($leaveRequests->lastPage()); ?>

                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<style>
.request-item {
    transition: background 0.2s ease;
}
.request-item:hover {
    background: #F8FAFC;
}
.custom-select-sm {
    font-size: 0.65rem;
    padding: 0.2rem 1.2rem 0.2rem 0.4rem;
    border: 1px solid #E8E2D5;
    border-radius: 4px;
    background: white;
    cursor: pointer;
}
.custom-select-sm:focus {
    outline: none;
    border-color: #3B82F6;
}
</style>

<script>
// Cancel Request Function
function cancelRequest(requestId) {
    if (confirm('Are you sure you want to cancel this leave request? This action cannot be undone.')) {
        fetch(`/leave-requests/${requestId}/cancel`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Failed to cancel request');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred');
        });
    }
}

// Per Page Change Handler
const perPageSelect = document.getElementById('perPageSelect');
if (perPageSelect) {
    perPageSelect.addEventListener('change', function() {
        const url = new URL(window.location.href);
        url.searchParams.set('per_page', this.value);
        url.searchParams.delete('page');
        window.location.href = url.toString();
    });
}
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\wamp64\www\unileave\resources\views/shared/leave-requests/status.blade.php ENDPATH**/ ?>