

<?php $__env->startSection('title', 'Department Management'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-2">
    
    <!-- Breadcrumb -->
    <?php if (isset($component)) { $__componentOriginale19f62b34dfe0bfdf95075badcb45bc2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale19f62b34dfe0bfdf95075badcb45bc2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.breadcrumb','data' => ['items' => [
        ['name' => 'Admin', 'url' => route('admin.departments.index')],
        ['name' => 'Departments', 'url' => '#' ]
    ]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('breadcrumb'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['items' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
        ['name' => 'Admin', 'url' => route('admin.departments.index')],
        ['name' => 'Departments', 'url' => '#' ]
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'DEPARTMENT MANAGEMENT','description' => 'Manage academic departments and administrative units']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'DEPARTMENT MANAGEMENT','description' => 'Manage academic departments and administrative units']); ?>
        <a href="<?php echo e(route('admin.departments.create')); ?>" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
            <i class="ti ti-plus me-1"></i> Add Department
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stats-card','data' => ['value' => ''.e($departments->total() ?? 0).'','label' => 'Total Departments','subtext' => '↑ +'.e($newThisMonth ?? 0).' this month']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('stats-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => ''.e($departments->total() ?? 0).'','label' => 'Total Departments','subtext' => '↑ +'.e($newThisMonth ?? 0).' this month']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stats-card','data' => ['value' => ''.e($departments->where('status', 'active')->count() ?? 0).'','label' => 'Active Departments','subtext' => 'active departments','color' => 'success']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('stats-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => ''.e($departments->where('status', 'active')->count() ?? 0).'','label' => 'Active Departments','subtext' => 'active departments','color' => 'success']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stats-card','data' => ['value' => ''.e(App\Models\School::count()).'','label' => 'Schools/Faculties','subtext' => 'parent institutions','color' => 'info']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('stats-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => ''.e(App\Models\School::count()).'','label' => 'Schools/Faculties','subtext' => 'parent institutions','color' => 'info']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stats-card','data' => ['value' => ''.e(App\Models\User::count()).'','label' => 'Total Staff','subtext' => 'assigned to departments','color' => 'warning']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('stats-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => ''.e(App\Models\User::count()).'','label' => 'Total Staff','subtext' => 'assigned to departments','color' => 'warning']); ?>
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
    
    <!-- Search Card Component -->
    <?php if (isset($component)) { $__componentOriginal07f9acc1f3a9849eaf94143334b32d90 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal07f9acc1f3a9849eaf94143334b32d90 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.search-card','data' => ['placeholder' => 'Search departments by name, code or school...','id' => 'searchInput']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('search-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['placeholder' => 'Search departments by name, code or school...','id' => 'searchInput']); ?>
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
    
    <!-- Departments Table -->
    <div class="card rounded-sm">
        <div class="card-header py-1 px-2 bg-light">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <i class="ti ti-building-community text-primary me-1"></i>
                    <span style="font-size: 0.7rem; font-weight: 600;">Departments List</span>
                </div>
                <div class="text-muted" style="font-size: 0.6rem;">
                    Showing <?php echo e($departments->firstItem() ?? 0); ?> to <?php echo e($departments->lastItem() ?? 0); ?> of <?php echo e($departments->total() ?? 0); ?> entries
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm mb-0" style="font-size: 0.75rem;" id="departmentsTable">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-2 py-1" width="50">#</th>
                            <th class="py-1">Department Name</th>
                            <th class="py-1">Code</th>
                            <th class="py-1">School/Faculty</th>
                            <th class="py-1">Campus</th>
                            <th class="py-1">University</th>
                            <th class="py-1">Status</th>
                            <th class="pe-2 py-1" width="120">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $department): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr data-department-id="<?php echo e($department->id); ?>"
                            data-name="<?php echo e(strtolower($department->name)); ?>"
                            data-code="<?php echo e(strtolower($department->code ?? '')); ?>"
                            data-school="<?php echo e(strtolower($department->school->name ?? '')); ?>"
                            data-school-id="<?php echo e($department->school_id); ?>"
                            data-status="<?php echo e($department->status); ?>">
                            <td class="ps-2 py-1"><?php echo e($departments->firstItem() + $index); ?></td>
                            <td class="py-1">
                                <span class="fw-semibold"><?php echo e($department->name); ?></span>
                            </td>
                            <td class="py-1"><?php echo e($department->code ?? 'N/A'); ?></td>
                            <td class="py-1"><?php echo e($department->school->name ?? 'N/A'); ?></td>
                            <td class="py-1"><?php echo e($department->school->campus->name ?? 'N/A'); ?></td>
                            <td class="py-1"><?php echo e($department->school->campus->university->name ?? 'N/A'); ?></td>
                            <td class="py-1">
                                <?php if($department->status == 'active'): ?>
                                    <span class="badge bg-success-subtle text-success rounded-sm" style="font-size: 0.6rem;">Active</span>
                                <?php else: ?>
                                    <span class="badge bg-danger-subtle text-danger rounded-sm" style="font-size: 0.6rem;">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td class="pe-2 py-1">
                                <div class="d-flex gap-1">
                                    <!-- View Button -->
                                    <a href="<?php echo e(route('admin.departments.show', $department->id)); ?>" 
                                       class="btn btn-sm btn-outline-info rounded-sm" 
                                       style="font-size: 0.6rem; padding: 0.15rem 0.4rem;" 
                                       title="View Department">
                                        <i class="ti ti-eye"></i>
                                    </a>
                                    
                                    <!-- Edit Button -->
                                    <a href="<?php echo e(route('admin.departments.edit', $department->id)); ?>" 
                                       class="btn btn-sm btn-outline-primary rounded-sm" 
                                       style="font-size: 0.6rem; padding: 0.15rem 0.4rem;" 
                                       title="Edit Department">
                                        <i class="ti ti-edit"></i>
                                    </a>
                                    
                                    <!-- Delete Button -->
                                    <button type="button" 
                                            onclick="showDeleteModal(<?php echo e($department->id); ?>, '<?php echo e(addslashes($department->name)); ?>')" 
                                            class="btn btn-sm btn-outline-danger rounded-sm" 
                                            style="font-size: 0.6rem; padding: 0.15rem 0.4rem;" 
                                            title="Delete Department">
                                        <i class="ti ti-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="8" class="text-center py-3 text-muted">No departments found</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Pagination Footer -->
        <div class="card-footer py-2 px-2 bg-light">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <span class="text-muted" style="font-size: 0.65rem;">Show:</span>
                    <select id="perPageSelect" class="custom-select-sm" style="font-size: 0.65rem; padding: 0.2rem 1.2rem 0.2rem 0.4rem; border: 1px solid #E8E2D5; border-radius: 4px; background: white;">
                        <option value="10" <?php echo e($departments->perPage() == 10 ? 'selected' : ''); ?>>10</option>
                        <option value="25" <?php echo e($departments->perPage() == 25 ? 'selected' : ''); ?>>25</option>
                        <option value="50" <?php echo e($departments->perPage() == 50 ? 'selected' : ''); ?>>50</option>
                        <option value="100" <?php echo e($departments->perPage() == 100 ? 'selected' : ''); ?>>100</option>
                    </select>
                    <span class="text-muted" style="font-size: 0.65rem;">per page</span>
                </div>
                
                <?php if($departments->hasPages()): ?>
                <div class="custom-pagination">
                    <div class="pagination-links">
                        <?php if($departments->onFirstPage()): ?>
                            <span class="pagination-item disabled">«</span>
                        <?php else: ?>
                            <a href="<?php echo e($departments->url(1)); ?>" class="pagination-item">«</a>
                        <?php endif; ?>
                        
                        <?php if($departments->onFirstPage()): ?>
                            <span class="pagination-item disabled">‹</span>
                        <?php else: ?>
                            <a href="<?php echo e($departments->previousPageUrl()); ?>" class="pagination-item">‹</a>
                        <?php endif; ?>
                        
                        <?php
                            $currentPage = $departments->currentPage();
                            $lastPage = $departments->lastPage();
                            $start = max(1, $currentPage - 2);
                            $end = min($lastPage, $currentPage + 2);
                            
                            if ($start > 1) {
                                echo '<a href="' . $departments->url(1) . '" class="pagination-item">1</a>';
                                if ($start > 2) echo '<span class="pagination-item dots">...</span>';
                            }
                            
                            for ($i = $start; $i <= $end; $i++) {
                                if ($i == $currentPage) {
                                    echo '<span class="pagination-item active">' . $i . '</span>';
                                } else {
                                    echo '<a href="' . $departments->url($i) . '" class="pagination-item">' . $i . '</a>';
                                }
                            }
                            
                            if ($end < $lastPage) {
                                if ($end < $lastPage - 1) echo '<span class="pagination-item dots">...</span>';
                                echo '<a href="' . $departments->url($lastPage) . '" class="pagination-item">' . $lastPage . '</a>';
                            }
                        ?>
                        
                        <?php if($departments->hasMorePages()): ?>
                            <a href="<?php echo e($departments->nextPageUrl()); ?>" class="pagination-item">›</a>
                        <?php else: ?>
                            <span class="pagination-item disabled">›</span>
                        <?php endif; ?>
                        
                        <?php if($departments->hasMorePages()): ?>
                            <a href="<?php echo e($departments->url($departments->lastPage())); ?>" class="pagination-item">»</a>
                        <?php else: ?>
                            <span class="pagination-item disabled">»</span>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <div class="text-muted" style="font-size: 0.6rem;">
                    Page <?php echo e($departments->currentPage()); ?> of <?php echo e($departments->lastPage()); ?>

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
                <p style="font-size: 0.75rem;">Are you sure you want to delete <strong id="deleteDepartmentName"></strong>?</p>
                <p class="text-danger mb-0" style="font-size: 0.7rem;">This action cannot be undone.</p>
            </div>
            <div class="modal-footer py-1 px-2">
                <button type="button" class="btn btn-secondary btn-sm rounded-sm" data-bs-dismiss="modal" style="font-size: 0.7rem;">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="btn btn-danger btn-sm rounded-sm" style="font-size: 0.7rem;">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
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
function showDeleteModal(id, name) {
    document.getElementById('deleteDepartmentName').innerText = name;
    document.getElementById('deleteForm').action = '/admin/departments/' + id;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}

// Search Functionality
const searchInput = document.getElementById('searchInput');
if (searchInput) {
    searchInput.addEventListener('keyup', function() {
        const searchTerm = this.value.toLowerCase();
        const rows = document.querySelectorAll('#departmentsTable tbody tr');
        rows.forEach(row => {
            if (row.cells.length >= 8) {
                const name = row.cells[1]?.innerText.toLowerCase() || '';
                const code = row.cells[2]?.innerText.toLowerCase() || '';
                const school = row.cells[3]?.innerText.toLowerCase() || '';
                const matches = name.includes(searchTerm) || code.includes(searchTerm) || school.includes(searchTerm);
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
<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\wamp64\www\unileave\resources\views/admin/departments/index.blade.php ENDPATH**/ ?>