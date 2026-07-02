

<?php $__env->startSection('title', $definition->display_name); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-2">
    
    <!-- Back button + title (like in school edit) -->
    <div class="card rounded-sm mb-3">
        <div class="card-body py-2 px-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h1 class="h5 mb-0" style="color: #3B82F6; font-weight: 700;"><?php echo e(strtoupper($definition->display_name)); ?></h1>
                    <p class="text-muted mb-0" style="font-size: 0.7rem;"><?php echo e($definition->description); ?></p>
                </div>
                <div class="d-flex gap-2">
                    <a href="<?php echo e(route('reports.index')); ?>" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
                        <i class="ti ti-arrow-left me-1"></i> Back
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards Row (dynamic based on report data) -->
    <?php
        // Compute stats from $reportData
        $rows = $reportData['rows'] ?? collect();
        $totalRequests = $rows->count();
        
        // For Department Roster, use different stats
        $stats = $reportData['stats'] ?? [];
        $totalDepartments = $stats['total_departments'] ?? 0;
        $totalStaff = $stats['total_staff'] ?? 0;
        $departmentsWithHod = $stats['departments_with_hod'] ?? 0;
        
        // Regular report stats
        $pendingCount = method_exists($rows, 'where') ? $rows->where('status', 'Pending')->count() : 0;
        $approvedCount = method_exists($rows, 'where') ? $rows->where('status', 'Approved')->count() : 0;
        $rejectedCount = method_exists($rows, 'where') ? $rows->where('status', 'Rejected')->count() : 0;
    ?>

    <div class="row g-2 mb-3">
        <?php if($totalDepartments > 0): ?>
            <!-- Department Roster Stats -->
            <?php if (isset($component)) { $__componentOriginal8f216e051c231b98198765acd723fb77 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8f216e051c231b98198765acd723fb77 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stats-card','data' => ['value' => ''.e($totalDepartments).'','label' => 'Total Departments','subtext' => 'across institution']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('stats-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => ''.e($totalDepartments).'','label' => 'Total Departments','subtext' => 'across institution']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stats-card','data' => ['value' => ''.e($totalStaff).'','label' => 'Total Staff','subtext' => 'active employees','color' => 'success']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('stats-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => ''.e($totalStaff).'','label' => 'Total Staff','subtext' => 'active employees','color' => 'success']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stats-card','data' => ['value' => ''.e($departmentsWithHod).'','label' => 'Departments with HOD','subtext' => 'assigned heads','color' => 'info']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('stats-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => ''.e($departmentsWithHod).'','label' => 'Departments with HOD','subtext' => 'assigned heads','color' => 'info']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stats-card','data' => ['value' => ''.e($rows->count()).'','label' => 'Total Assignments','subtext' => 'staff records','color' => 'primary']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('stats-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => ''.e($rows->count()).'','label' => 'Total Assignments','subtext' => 'staff records','color' => 'primary']); ?>
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
        <?php else: ?>
            <!-- Regular Report Stats -->
            <?php if (isset($component)) { $__componentOriginal8f216e051c231b98198765acd723fb77 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8f216e051c231b98198765acd723fb77 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stats-card','data' => ['value' => ''.e($totalRequests).'','label' => 'Total Requests','subtext' => 'all time']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('stats-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => ''.e($totalRequests).'','label' => 'Total Requests','subtext' => 'all time']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stats-card','data' => ['value' => ''.e($pendingCount).'','label' => 'Pending','subtext' => 'awaiting approval','color' => 'warning']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('stats-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => ''.e($pendingCount).'','label' => 'Pending','subtext' => 'awaiting approval','color' => 'warning']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stats-card','data' => ['value' => ''.e($approvedCount).'','label' => 'Approved','subtext' => 'granted','color' => 'success']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('stats-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => ''.e($approvedCount).'','label' => 'Approved','subtext' => 'granted','color' => 'success']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stats-card','data' => ['value' => ''.e($rejectedCount).'','label' => 'Rejected','subtext' => 'denied','color' => 'danger']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('stats-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => ''.e($rejectedCount).'','label' => 'Rejected','subtext' => 'denied','color' => 'danger']); ?>
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
        <?php endif; ?>
    </div>

    <!-- Sophisticated Search Card -->
    <?php if (isset($component)) { $__componentOriginal07f9acc1f3a9849eaf94143334b32d90 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal07f9acc1f3a9849eaf94143334b32d90 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.search-card','data' => ['placeholder' => 'Search by staff name, department or leave type...','id' => 'reportSearchInput']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('search-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['placeholder' => 'Search by staff name, department or leave type...','id' => 'reportSearchInput']); ?>
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

    <!-- Graph + Explanation Cards (side by side) -->
    <div class="row g-2 mb-3">
        <!-- Graph Card -->
        <div class="col-lg-7">
            <div class="card rounded-sm h-100">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-chart-bar text-primary me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Chart View</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <?php if(isset($reportData['chart']) && $reportData['chart']): ?>
                    <canvas id="reportChart" style="height: 300px; width: 100%;"></canvas>
                    <?php elseif(isset($reportData['chart_data']) && !empty($reportData['chart_data']['labels'])): ?>
                    <canvas id="reportChart" style="height: 300px; width: 100%;"></canvas>
                    <?php else: ?>
                    <p class="text-muted text-center mb-0" style="font-size: 0.7rem;">No chart available for this report.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Explanation Card -->
        <div class="col-lg-5">
            <div class="card rounded-sm h-100">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-info-circle text-info me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">About this Report</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <p style="font-size: 0.7rem;"><?php echo e($definition->description); ?></p>
                    <?php if(isset($reportData['chart']) || isset($reportData['chart_data'])): ?>
                    <div class="mt-2">
                        <div class="fw-semibold" style="font-size: 0.65rem;">Chart Legend</div>
                        <div class="d-flex gap-3 mt-1">
                            <div><span class="badge bg-primary" style="width: 12px; height: 12px; display: inline-block;"></span> <span style="font-size: 0.6rem;">Value</span></div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Long Details Card (Data Table) -->
    <div class="card rounded-sm">
        <div class="card-header py-1 px-2 bg-light">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <i class="ti ti-table text-primary me-1" style="font-size: 0.8rem;"></i>
                    <span style="font-size: 0.7rem; font-weight: 600;">Detailed Records</span>
                </div>
                <div class="text-muted" style="font-size: 0.6rem;">
                    Showing <?php echo e($rows->count()); ?> records
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm mb-0" style="font-size: 0.7rem;" id="reportTable">
                    <thead class="bg-light">
                        <tr>
                            <?php $__currentLoopData = $rows->first() ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $col => $val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <th class="py-1"><?php echo e(ucwords(str_replace('_', ' ', $col))); ?></th>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <?php $__currentLoopData = $row; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <td class="py-1"><?php echo e($value); ?></td>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="100" class="text-center py-3 text-muted">No data found</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer py-1 px-2">
            <small class="text-muted">Export options: 
                <a href="<?php echo e(route('reports.export.pdf', $definition->name) . '?' . http_build_query(request()->query())); ?>" class="text-danger">PDF</a> | 
                <a href="<?php echo e(route('reports.export.excel', $definition->name) . '?' . http_build_query(request()->query())); ?>" class="text-success">Excel</a>
            </small>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    <?php if(isset($reportData['chart']) && $reportData['chart']): ?>
    const ctx = document.getElementById('reportChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($reportData['chart']['labels']); ?>,
            datasets: [{
                label: '<?php echo e($definition->chart_config["title"] ?? "Value"); ?>',
                data: <?php echo json_encode($reportData['chart']['series']); ?>,
                backgroundColor: 'rgba(54, 162, 235, 0.5)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            scales: { y: { beginAtZero: true } }
        }
    });
    <?php elseif(isset($reportData['chart_data']) && !empty($reportData['chart_data']['labels'])): ?>
    const ctx = document.getElementById('reportChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($reportData['chart_data']['labels']); ?>,
            datasets: <?php echo json_encode($reportData['chart_data']['datasets']); ?>

        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            scales: { y: { beginAtZero: true } }
        }
    });
    <?php endif; ?>

    // Search functionality for the table
    const searchInput = document.getElementById('reportSearchInput');
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            const term = this.value.toLowerCase();
            const rows = document.querySelectorAll('#reportTable tbody tr');
            rows.forEach(row => {
                const text = row.innerText.toLowerCase();
                row.style.display = text.includes(term) ? '' : 'none';
            });
        });
    }
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\wamp64\www\unileave\resources\views/reports/show.blade.php ENDPATH**/ ?>