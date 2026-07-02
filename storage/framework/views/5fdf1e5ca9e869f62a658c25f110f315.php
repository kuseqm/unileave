

<?php $__env->startSection('title', 'Approvals History'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-2">
    
    <!-- Breadcrumb -->
    <?php if (isset($component)) { $__componentOriginale19f62b34dfe0bfdf95075badcb45bc2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale19f62b34dfe0bfdf95075badcb45bc2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.breadcrumb','data' => ['items' => [
        ['name' => 'Dashboard', 'url' => route('dashboard')],
        ['name' => 'Approvals History', 'url' => '#']
    ]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('breadcrumb'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['items' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
        ['name' => 'Dashboard', 'url' => route('dashboard')],
        ['name' => 'Approvals History', 'url' => '#']
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'APPROVALS HISTORY','description' => 'Track all leave requests you have approved or rejected']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'APPROVALS HISTORY','description' => 'Track all leave requests you have approved or rejected']); ?>
        <a href="<?php echo e(route('dashboard')); ?>" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
            <i class="ti ti-arrow-left me-1"></i> Back to Dashboard
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
    
    <!-- Stats Cards -->
    <div class="row g-2 mb-3">
        <?php if (isset($component)) { $__componentOriginal8f216e051c231b98198765acd723fb77 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8f216e051c231b98198765acd723fb77 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stats-card','data' => ['value' => ''.e($stats['total']).'','label' => 'Total Decisions','subtext' => 'all time','color' => 'primary','icon' => 'history']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('stats-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => ''.e($stats['total']).'','label' => 'Total Decisions','subtext' => 'all time','color' => 'primary','icon' => 'history']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stats-card','data' => ['value' => ''.e($stats['approved']).'','label' => 'Approved','subtext' => 'you approved','color' => 'success','icon' => 'check']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('stats-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => ''.e($stats['approved']).'','label' => 'Approved','subtext' => 'you approved','color' => 'success','icon' => 'check']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stats-card','data' => ['value' => ''.e($stats['rejected']).'','label' => 'Rejected','subtext' => 'you rejected','color' => 'danger','icon' => 'x']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('stats-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => ''.e($stats['rejected']).'','label' => 'Rejected','subtext' => 'you rejected','color' => 'danger','icon' => 'x']); ?>
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
    
    <div class="row g-2">
        <!-- Main Table Column -->
        <div class="col-lg-8">
            <!-- Filters -->
            <div class="card rounded-sm mb-2">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-filter text-primary me-1" style="font-size: 0.7rem;"></i>
                        <span style="font-size: 0.65rem; font-weight: 600;">Filter Approvals</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <form method="GET" class="row g-2 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label" style="font-size: 0.6rem;">Action</label>
                            <select name="action" class="form-select form-select-sm">
                                <option value="">All Actions</option>
                                <option value="approved" <?php echo e(request('action') == 'approved' ? 'selected' : ''); ?>>Approved</option>
                                <option value="rejected" <?php echo e(request('action') == 'rejected' ? 'selected' : ''); ?>>Rejected</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" style="font-size: 0.6rem;">From Date</label>
                            <input type="date" name="date_from" class="form-control form-control-sm" value="<?php echo e(request('date_from')); ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" style="font-size: 0.6rem;">To Date</label>
                            <input type="date" name="date_to" class="form-control form-control-sm" value="<?php echo e(request('date_to')); ?>">
                        </div>
                        <div class="col-md-3">
                            <div class="d-flex gap-1">
                                <button type="submit" class="btn btn-primary btn-sm w-100">Filter</button>
                                <a href="<?php echo e(route('leave-requests.approvals-history')); ?>" class="btn btn-secondary btn-sm w-100">Reset</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Approvals Table -->
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-history text-primary me-1" style="font-size: 0.7rem;"></i>
                        <span style="font-weight: 600; font-size: 0.7rem;">Your Approval Records</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0" style="font-size: 0.7rem;">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-2 py-1" width="50">#</th>
                                    <th class="py-1">Staff</th>
                                    <th class="py-1 text-center" width="70">Days</th>
                                    <th class="py-1 text-center" width="100">Status</th>
                                    <th class="pe-2 py-1 text-center" width="80">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $approvals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $approval): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <?php
                                    $leaveRequest = $approval->leaveRequest;
                                ?>
                                <?php if($leaveRequest): ?>
                                <tr>
                                    <td class="ps-2 py-1"><?php echo e($approvals->firstItem() + $index); ?></td>
                                    <td class="py-1">
                                        <div class="fw-semibold"><?php echo e($leaveRequest->staff->full_name ?? 'N/A'); ?></div>
                                        <div class="text-muted" style="font-size: 0.55rem;"><?php echo e($leaveRequest->staff->staff_id ?? ''); ?></div>
                                    </td>
                                    <td class="py-1 text-center"><?php echo e($leaveRequest->days); ?> day(s)</td>
                                    <td class="py-1 text-center">
                                        <?php if($approval->action == 'approved'): ?>
                                            <span class="badge bg-success-subtle text-success rounded-pill" style="font-size: 0.6rem;">Approved</span>
                                        <?php elseif($approval->action == 'rejected'): ?>
                                            <span class="badge bg-danger-subtle text-danger rounded-pill" style="font-size: 0.6rem;">Rejected</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary-subtle text-secondary rounded-pill" style="font-size: 0.6rem;"><?php echo e(ucfirst($approval->action)); ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="pe-2 py-1 text-center">
                                        <a href="<?php echo e(route('leave-requests.approval-details', $leaveRequest->id)); ?>" 
                                           class="btn btn-sm btn-outline-info rounded-sm" 
                                           style="padding: 0.15rem 0.4rem; font-size: 0.6rem;"
                                           title="View Full Details">
                                            <i class="ti ti-eye me-1"></i> View
                                        </a>
                                    </td>
                                </tr>
                                <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center py-2 text-muted" style="font-size: 0.7rem;">
                                        <i class="ti ti-alert-circle me-1"></i>
                                        Request data not available
                                    </td>
                                </tr>
                                <?php endif; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted" style="font-size: 0.7rem;">
                                        <i class="ti ti-history mb-2 d-block" style="font-size: 1.5rem;"></i>
                                        <p>No approval records found</p>
                                        <small class="text-muted">When you approve or reject a leave request, it will appear here.</small>
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Pagination Footer -->
                <?php if($approvals->hasPages() || $approvals->total() > 0): ?>
                <div class="card-footer py-2 px-2 bg-light">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <!-- Per Page Dropdown -->
                        <div class="d-flex align-items-center gap-2">
                            <span class="text-muted" style="font-size: 0.65rem;">Show:</span>
                            <select id="perPageSelect" class="custom-select-sm" style="font-size: 0.65rem; padding: 0.2rem 1.2rem 0.2rem 0.4rem; border: 1px solid #E8E2D5; border-radius: 4px; background: white;">
                                <option value="10" <?php echo e($approvals->perPage() == 10 ? 'selected' : ''); ?>>10</option>
                                <option value="25" <?php echo e($approvals->perPage() == 25 ? 'selected' : ''); ?>>25</option>
                                <option value="50" <?php echo e($approvals->perPage() == 50 ? 'selected' : ''); ?>>50</option>
                                <option value="100" <?php echo e($approvals->perPage() == 100 ? 'selected' : ''); ?>>100</option>
                            </select>
                            <span class="text-muted" style="font-size: 0.65rem;">per page</span>
                        </div>
                        
                        <!-- Pagination Links -->
                        <?php if($approvals->hasPages()): ?>
                        <div class="custom-pagination">
                            <div class="pagination-links">
                                
                                <?php if($approvals->onFirstPage()): ?>
                                    <span class="pagination-item disabled">«</span>
                                <?php else: ?>
                                    <a href="<?php echo e($approvals->url(1)); ?>" class="pagination-item">«</a>
                                <?php endif; ?>
                                
                                
                                <?php if($approvals->onFirstPage()): ?>
                                    <span class="pagination-item disabled">‹</span>
                                <?php else: ?>
                                    <a href="<?php echo e($approvals->previousPageUrl()); ?>" class="pagination-item">‹</a>
                                <?php endif; ?>
                                
                                
                                <?php
                                    $currentPage = $approvals->currentPage();
                                    $lastPage = $approvals->lastPage();
                                    $start = max(1, $currentPage - 2);
                                    $end = min($lastPage, $currentPage + 2);
                                    
                                    if ($start > 1) {
                                        echo '<a href="' . $approvals->url(1) . '" class="pagination-item">1</a>';
                                        if ($start > 2) echo '<span class="pagination-item dots">...</span>';
                                    }
                                    
                                    for ($i = $start; $i <= $end; $i++) {
                                        if ($i == $currentPage) {
                                            echo '<span class="pagination-item active">' . $i . '</span>';
                                        } else {
                                            echo '<a href="' . $approvals->url($i) . '" class="pagination-item">' . $i . '</a>';
                                        }
                                    }
                                    
                                    if ($end < $lastPage) {
                                        if ($end < $lastPage - 1) echo '<span class="pagination-item dots">...</span>';
                                        echo '<a href="' . $approvals->url($lastPage) . '" class="pagination-item">' . $lastPage . '</a>';
                                    }
                                ?>
                                
                                
                                <?php if($approvals->hasMorePages()): ?>
                                    <a href="<?php echo e($approvals->nextPageUrl()); ?>" class="pagination-item">›</a>
                                <?php else: ?>
                                    <span class="pagination-item disabled">›</span>
                                <?php endif; ?>
                                
                                
                                <?php if($approvals->hasMorePages()): ?>
                                    <a href="<?php echo e($approvals->url($approvals->lastPage())); ?>" class="pagination-item">»</a>
                                <?php else: ?>
                                    <span class="pagination-item disabled">»</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Results Info -->
                        <div class="text-muted" style="font-size: 0.6rem;">
                            Showing <?php echo e($approvals->firstItem() ?? 0); ?> to <?php echo e($approvals->lastItem() ?? 0); ?> of <?php echo e($approvals->total() ?? 0); ?> entries
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Info Card Column -->
        <div class="col-lg-4">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-info-circle text-info me-1" style="font-size: 0.7rem;"></i>
                        <span style="font-weight: 600; font-size: 0.7rem;">About Approvals History</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <div class="alert alert-light border mb-2 py-1 px-2 rounded-sm">
                        <div class="d-flex align-items-start gap-1">
                            <i class="ti ti-checklist text-primary mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">What is this page?</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">This page shows all leave requests where you were the approver - whether you approved or rejected them.</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert alert-light border mb-2 py-1 px-2 rounded-sm">
                        <div class="d-flex align-items-start gap-1">
                            <i class="ti ti-eye text-success mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">View Details</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">Click the "View" button to see complete leave request details, approval timeline, and the official letter.</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert alert-light border mb-0 py-1 px-2 rounded-sm">
                        <div class="d-flex align-items-start gap-1">
                            <i class="ti ti-file-text text-warning mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">Official Letter</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">Approved leave requests include an official letter that can be printed or downloaded as PDF.</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Custom Pagination Styles */
.custom-pagination {
    margin: 0;
}
.pagination-links {
    display: flex;
    gap: 0.25rem;
    align-items: center;
}
.pagination-item {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 28px;
    height: 28px;
    padding: 0 0.4rem;
    font-size: 0.65rem;
    color: #475569;
    background: white;
    border: 1px solid #E8E2D5;
    border-radius: 4px;
    text-decoration: none;
    transition: all 0.2s ease;
}
.pagination-item:hover:not(.disabled):not(.active) {
    background: #F1F5F9;
    border-color: #CBD5E1;
    color: #1E293B;
}
.pagination-item.active {
    background: #3B82F6;
    border-color: #3B82F6;
    color: white;
}
.pagination-item.disabled {
    opacity: 0.5;
    cursor: not-allowed;
    background: #F8FAFC;
}
.pagination-item.dots {
    border: none;
    background: transparent;
    cursor: default;
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
<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\wamp64\www\unileave\resources\views/shared/leave-requests/approvals-history.blade.php ENDPATH**/ ?>