

<?php $__env->startSection('title', 'Staff Management'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-2">
    
    <!-- Breadcrumb -->
    <?php if (isset($component)) { $__componentOriginale19f62b34dfe0bfdf95075badcb45bc2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale19f62b34dfe0bfdf95075badcb45bc2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.breadcrumb','data' => ['items' => [
        ['name' => 'Staff Management', 'url' => '#']
    ]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('breadcrumb'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['items' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
        ['name' => 'Staff Management', 'url' => '#']
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'STAFF MANAGEMENT','description' => 'Manage staff members, roles, and permissions']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'STAFF MANAGEMENT','description' => 'Manage staff members, roles, and permissions']); ?>
        
        <?php if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('hr_manager') || auth()->user()->hasRole('dean')): ?>
        <a href="<?php echo e(route('staff.create')); ?>" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
            <i class="ti ti-plus me-1"></i> Add Staff
        </a>
        <a href="<?php echo e(route('admin.import.staff')); ?>" class="btn btn-outline-primary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
            <i class="ti ti-file-import me-1"></i> Import
        </a>
        <?php endif; ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stats-card','data' => ['value' => ''.e($totalStaff ?? $staffMembers->total()).'','label' => 'Total Staff','subtext' => '↑ +'.e($newThisMonth ?? 0).' this month']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('stats-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => ''.e($totalStaff ?? $staffMembers->total()).'','label' => 'Total Staff','subtext' => '↑ +'.e($newThisMonth ?? 0).' this month']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stats-card','data' => ['value' => ''.e($academicStaff ?? 0).'','label' => 'Academic','subtext' => 'teaching staff','color' => 'primary']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('stats-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => ''.e($academicStaff ?? 0).'','label' => 'Academic','subtext' => 'teaching staff','color' => 'primary']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stats-card','data' => ['value' => ''.e($administrativeStaff ?? 0).'','label' => 'Administrative','subtext' => 'office staff','color' => 'primary']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('stats-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => ''.e($administrativeStaff ?? 0).'','label' => 'Administrative','subtext' => 'office staff','color' => 'primary']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stats-card','data' => ['value' => ''.e($supportingStaff ?? 0).'','label' => 'Supporting','subtext' => 'technical & support','color' => 'primary']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('stats-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => ''.e($supportingStaff ?? 0).'','label' => 'Supporting','subtext' => 'technical & support','color' => 'primary']); ?>
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
    
    <!-- Alerts -->
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
    
    <!-- Search Card -->
    <?php if (isset($component)) { $__componentOriginal07f9acc1f3a9849eaf94143334b32d90 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal07f9acc1f3a9849eaf94143334b32d90 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.search-card','data' => ['placeholder' => 'Search by name, email or staff ID...','id' => 'searchInput']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('search-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['placeholder' => 'Search by name, email or staff ID...','id' => 'searchInput']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal07f9acc1f3a9849eaf94143334b32d90)): ?>
<?php $attributes = $__attributesOriginal07f9acc1f3a9849eaf94143334b32d90; ?>
<?php unset($__attributesOriginal07f9acc1f3a9849eaf94143334b32d90); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal07f9acc1f3a9849eaf94143334b32d90)): ?>
<?php $component = $__componentOriginal07f9acc1f3a9849eaf94143334b32d90; ?>
<?php unset($__componentOriginal07f9acc1f3a9849eaf94143334b32d90); ?>
<?php endif; ?>
    
    <!-- Staff Table -->
    <div class="card rounded-sm">
        <div class="card-header py-1 px-2 bg-light">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center">
                    <i class="ti ti-users text-primary me-1" style="font-size: 0.8rem;"></i>
                    <span style="font-size: 0.7rem; font-weight: 600;">Staff Directory</span>
                </div>
                <div class="text-muted" style="font-size: 0.6rem;">
                    Showing <?php echo e($staffMembers->firstItem() ?? 0); ?> to <?php echo e($staffMembers->lastItem() ?? 0); ?> of <?php echo e($staffMembers->total() ?? 0); ?> entries
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm mb-0" style="font-size: 0.75rem;" id="staffTable">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-2 py-1" width="50">SN</th>
                            <th class="py-1">Name</th>
                            <th class="py-1">Email</th>
                            <th class="py-1">Department</th>
                            <th class="py-1">Category</th>
                            <th class="py-1">Status</th>
                            
                            <?php if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('hr_manager') || auth()->user()->hasRole('dean')): ?>
                            <th class="pe-2 py-1" width="100">Actions</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $staffMembers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $staff): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr data-staff-id="<?php echo e($staff->id); ?>" 
                            data-name="<?php echo e(strtolower($staff->full_name)); ?>" 
                            data-email="<?php echo e(strtolower($staff->email)); ?>" 
                            data-staff-code="<?php echo e(strtolower($staff->staff_id ?? '')); ?>" 
                            data-category="<?php echo e($staff->staff_category); ?>" 
                            data-status="<?php echo e($staff->status); ?>">
                            <td class="ps-2 py-1"><?php echo e($staffMembers->firstItem() + $index); ?></td>
                            <td class="py-1">
                                <span class="fw-semibold"><?php echo e($staff->full_name); ?></span>
                            </td>
                            <td class="py-1"><?php echo e($staff->email); ?></td>
                            <td class="py-1"><?php echo e($staff->department->name ?? 'N/A'); ?></td>
                            <td class="py-1">
                                <?php if($staff->staff_category == 'academic'): ?>
                                    <span class="badge bg-primary-subtle text-primary rounded-sm" style="font-size: 0.6rem;">Academic</span>
                                <?php elseif($staff->staff_category == 'administrative'): ?>
                                    <span class="badge bg-info-subtle text-info rounded-sm" style="font-size: 0.6rem;">Administrative</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary-subtle text-secondary rounded-sm" style="font-size: 0.6rem;">Supporting</span>
                                <?php endif; ?>
                            </td>
                            <td class="py-1">
                                <?php if($staff->status == 'active'): ?>
                                    <span class="badge bg-success-subtle text-success rounded-sm" style="font-size: 0.6rem;">Active</span>
                                <?php elseif($staff->status == 'inactive'): ?>
                                    <span class="badge bg-warning-subtle text-warning rounded-sm" style="font-size: 0.6rem;">Inactive</span>
                                <?php else: ?>
                                    <span class="badge bg-danger-subtle text-danger rounded-sm" style="font-size: 0.6rem;">Suspended</span>
                                <?php endif; ?>
                            </td>
                            
                            <?php if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('hr_manager') || auth()->user()->hasRole('dean')): ?>
                            <td class="pe-2 py-1">
                                <div class="d-flex gap-1">
                                    <!-- View Button (always visible for everyone) -->
                                    <a href="<?php echo e(route('staff.show', $staff->id)); ?>" 
                                       class="btn btn-sm btn-outline-info rounded-sm" 
                                       style="font-size: 0.6rem; padding: 0.15rem 0.4rem;" 
                                       title="View Staff">
                                        <i class="ti ti-eye"></i>
                                    </a>
                                    
                                    <!-- Edit Button -->
                                    <a href="<?php echo e(route('staff.edit', $staff->id)); ?>" 
                                       class="btn btn-sm btn-outline-primary rounded-sm" 
                                       style="font-size: 0.6rem; padding: 0.15rem 0.4rem;" 
                                       title="Edit Staff">
                                        <i class="ti ti-edit"></i>
                                    </a>
                                    
                                    <!-- Delete Button -->
                                    <button type="button" 
                                            onclick="showDeleteModal(<?php echo e($staff->id); ?>, '<?php echo e(addslashes($staff->full_name)); ?>')" 
                                            class="btn btn-sm btn-outline-danger rounded-sm" 
                                            style="font-size: 0.6rem; padding: 0.15rem 0.4rem;" 
                                            title="Delete Staff">
                                        <i class="ti ti-trash"></i>
                                    </button>
                                </div>
                            </td>
                            <?php endif; ?>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <?php if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('hr_manager') || auth()->user()->hasRole('dean')): ?>
                            <td colspan="7" class="text-center py-3 text-muted" style="font-size: 0.7rem;">No staff members found</td>
                            <?php else: ?>
                            <td colspan="6" class="text-center py-3 text-muted" style="font-size: 0.7rem;">No staff members found</td>
                            <?php endif; ?>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Pagination Footer -->
        <div class="card-footer py-2 px-2 bg-light">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <!-- Per Page Dropdown -->
                <div class="d-flex align-items-center gap-2">
                    <span class="text-muted" style="font-size: 0.65rem;">Show:</span>
                    <select id="perPageSelect" class="custom-select-sm" style="font-size: 0.65rem; padding: 0.2rem 1.2rem 0.2rem 0.4rem; border: 1px solid #E8E2D5; border-radius: 4px; background: white;">
                        <option value="10" <?php echo e($staffMembers->perPage() == 10 ? 'selected' : ''); ?>>10</option>
                        <option value="25" <?php echo e($staffMembers->perPage() == 25 ? 'selected' : ''); ?>>25</option>
                        <option value="50" <?php echo e($staffMembers->perPage() == 50 ? 'selected' : ''); ?>>50</option>
                        <option value="100" <?php echo e($staffMembers->perPage() == 100 ? 'selected' : ''); ?>>100</option>
                    </select>
                    <span class="text-muted" style="font-size: 0.65rem;">per page</span>
                </div>
                
                <!-- Custom Pagination Links -->
                <?php if($staffMembers->hasPages()): ?>
                <div class="custom-pagination">
                    <div class="pagination-links">
                        
                        <?php if($staffMembers->onFirstPage()): ?>
                            <span class="pagination-item disabled">«</span>
                        <?php else: ?>
                            <a href="<?php echo e($staffMembers->url(1)); ?>" class="pagination-item">«</a>
                        <?php endif; ?>
                        
                        
                        <?php if($staffMembers->onFirstPage()): ?>
                            <span class="pagination-item disabled">‹</span>
                        <?php else: ?>
                            <a href="<?php echo e($staffMembers->previousPageUrl()); ?>" class="pagination-item">‹</a>
                        <?php endif; ?>
                        
                        
                        <?php
                            $currentPage = $staffMembers->currentPage();
                            $lastPage = $staffMembers->lastPage();
                            $start = max(1, $currentPage - 2);
                            $end = min($lastPage, $currentPage + 2);
                            
                            if ($start > 1) {
                                echo '<a href="' . $staffMembers->url(1) . '" class="pagination-item">1</a>';
                                if ($start > 2) echo '<span class="pagination-item dots">...</span>';
                            }
                            
                            for ($i = $start; $i <= $end; $i++) {
                                if ($i == $currentPage) {
                                    echo '<span class="pagination-item active">' . $i . '</span>';
                                } else {
                                    echo '<a href="' . $staffMembers->url($i) . '" class="pagination-item">' . $i . '</a>';
                                }
                            }
                            
                            if ($end < $lastPage) {
                                if ($end < $lastPage - 1) echo '<span class="pagination-item dots">...</span>';
                                echo '<a href="' . $staffMembers->url($lastPage) . '" class="pagination-item">' . $lastPage . '</a>';
                            }
                        ?>
                        
                        
                        <?php if($staffMembers->hasMorePages()): ?>
                            <a href="<?php echo e($staffMembers->nextPageUrl()); ?>" class="pagination-item">›</a>
                        <?php else: ?>
                            <span class="pagination-item disabled">›</span>
                        <?php endif; ?>
                        
                        
                        <?php if($staffMembers->hasMorePages()): ?>
                            <a href="<?php echo e($staffMembers->url($staffMembers->lastPage())); ?>" class="pagination-item">»</a>
                        <?php else: ?>
                            <span class="pagination-item disabled">»</span>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Results Info -->
                <div class="text-muted" style="font-size: 0.6rem;">
                    Page <?php echo e($staffMembers->currentPage()); ?> of <?php echo e($staffMembers->lastPage()); ?>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-sm">
            <div class="modal-header py-1 px-2">
                <h5 class="modal-title" style="font-size: 0.8rem;">Confirm Delete</h5>
                <button type="button" class="btn-close py-0" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body py-2 px-2">
                <p style="font-size: 0.75rem;">Are you sure you want to delete <strong id="deleteStaffName"></strong>?</p>
                <p class="text-danger mb-0" style="font-size: 0.7rem;">This action cannot be undone.</p>
            </div>
            <div class="modal-footer py-1 px-2">
                <button type="button" class="btn btn-secondary btn-sm rounded-sm" data-bs-dismiss="modal" style="font-size: 0.7rem;">Cancel</button>
                <a href="#" id="deleteConfirmBtn" class="btn btn-danger btn-sm rounded-sm" style="font-size: 0.7rem;">Delete</a>
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

/* Per page select styling */
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

/* Action buttons hover effects */
.btn-outline-info:hover i,
.btn-outline-primary:hover i,
.btn-outline-danger:hover i {
    color: white;
}
</style>

<script>
function showDeleteModal(id, name) {
    document.getElementById('deleteStaffName').innerText = name;
    document.getElementById('deleteConfirmBtn').href = '/staff/' + id;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}

// Search Functionality
const searchInput = document.getElementById('searchInput');
if (searchInput) {
    searchInput.addEventListener('keyup', function() {
        const searchTerm = this.value.toLowerCase();
        const rows = document.querySelectorAll('#staffTable tbody tr');
        rows.forEach(row => {
            if (row.cells.length >= 6) {
                const name = row.cells[1]?.innerText.toLowerCase() || '';
                const email = row.cells[2]?.innerText.toLowerCase() || '';
                const matches = name.includes(searchTerm) || email.includes(searchTerm);
                row.style.display = matches ? '' : 'none';
            }
        });
    });
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
<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\wamp64\www\unileave\resources\views/admin/staff/index.blade.php ENDPATH**/ ?>