

<?php $__env->startSection('title', 'Vice Chancellor Dashboard'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-2">
    
    <!-- Breadcrumb -->
    <?php if (isset($component)) { $__componentOriginale19f62b34dfe0bfdf95075badcb45bc2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale19f62b34dfe0bfdf95075badcb45bc2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.breadcrumb','data' => ['items' => [
        ['name' => 'VC Dashboard', 'url' => '#']
    ]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('breadcrumb'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['items' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
        ['name' => 'VC Dashboard', 'url' => '#']
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'VICE CHANCELLOR DASHBOARD','description' => 'University Leave Management Overview']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'VICE CHANCELLOR DASHBOARD','description' => 'University Leave Management Overview']); ?>
        <div>
            <button type="button" onclick="window.print();" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
                <i class="ti ti-printer me-1"></i> Print
            </button>
            <a href="<?php echo e(route('reports.index')); ?>" class="btn btn-outline-primary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
                <i class="ti ti-chart-bar me-1"></i> Full Reports
            </a>
        </div>
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
    
    <!-- Filter Bar -->
    <div class="card rounded-sm mb-3">
        <div class="card-header py-1 px-2 bg-light">
            <div class="d-flex align-items-center">
                <i class="ti ti-filter text-primary me-1"></i>
                <span style="font-weight: 600; font-size: 0.7rem;">Filters</span>
            </div>
        </div>
        <div class="card-body py-2 px-2">
            <form method="GET" action="<?php echo e(route('vc.dashboard')); ?>" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label" style="font-size: 0.6rem;">Date Range</label>
                    <select name="date_range" class="form-select form-select-sm">
                        <option value="all" <?php echo e(($currentFilters['date_range'] ?? 'this_year') == 'all' ? 'selected' : ''); ?>>All Time</option>
                        <option value="this_year" <?php echo e(($currentFilters['date_range'] ?? 'this_year') == 'this_year' ? 'selected' : ''); ?>>This Year</option>
                        <option value="last_year" <?php echo e(($currentFilters['date_range'] ?? '') == 'last_year' ? 'selected' : ''); ?>>Last Year</option>
                        <option value="this_month" <?php echo e(($currentFilters['date_range'] ?? '') == 'this_month' ? 'selected' : ''); ?>>This Month</option>
                        <option value="last_month" <?php echo e(($currentFilters['date_range'] ?? '') == 'last_month' ? 'selected' : ''); ?>>Last Month</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label" style="font-size: 0.6rem;">Department</label>
                    <select name="department_id" class="form-select form-select-sm">
                        <option value="">All Departments</option>
                        <?php $__currentLoopData = $departments ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dept): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($dept->id); ?>" <?php echo e(($currentFilters['department_id'] ?? '') == $dept->id ? 'selected' : ''); ?>><?php echo e($dept->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label" style="font-size: 0.6rem;">Leave Type</label>
                    <select name="leave_type_id" class="form-select form-select-sm">
                        <option value="">All Leave Types</option>
                        <?php $__currentLoopData = $leaveTypes ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($type->id); ?>" <?php echo e(($currentFilters['leave_type_id'] ?? '') == $type->id ? 'selected' : ''); ?>><?php echo e($type->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <div class="d-flex gap-1">
                        <button type="submit" class="btn btn-primary btn-sm w-100">Apply Filters</button>
                        <a href="<?php echo e(route('vc.dashboard')); ?>" class="btn btn-secondary btn-sm w-100">Reset</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Export Buttons -->
    <div class="d-flex justify-content-end gap-1 mb-2">
        <a href="<?php echo e(route('vc.reports.excel', request()->query())); ?>" class="btn btn-success btn-sm rounded-sm" style="font-size: 0.7rem;">
            <i class="ti ti-file-spreadsheet me-1"></i> Export Excel
        </a>
        <a href="<?php echo e(route('vc.reports.pdf', request()->query())); ?>" class="btn btn-danger btn-sm rounded-sm" style="font-size: 0.7rem;">
            <i class="ti ti-file-text me-1"></i> Export PDF
        </a>
    </div>
    
    <!-- Stats Cards -->
    <div class="row g-2 mb-3">
        <div class="col-md-3">
            <div class="card rounded-sm text-center h-100">
                <div class="card-body py-2">
                    <div class="text-primary fw-bold" style="font-size: 1.5rem;"><?php echo e(number_format($totalRequests ?? 0)); ?></div>
                    <div class="text-muted" style="font-size: 0.65rem;">Total Leave Requests</div>
                    <div class="small text-muted">Filtered period</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card rounded-sm text-center h-100">
                <div class="card-body py-2">
                    <div class="text-success fw-bold" style="font-size: 1.5rem;"><?php echo e($activeOnLeave ?? 0); ?></div>
                    <div class="text-muted" style="font-size: 0.65rem;">Active Staff on Leave</div>
                    <div class="small text-muted">Currently away</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card rounded-sm text-center h-100">
                <div class="card-body py-2">
                    <div class="text-warning fw-bold" style="font-size: 1.5rem;"><?php echo e($pendingApprovals ?? 0); ?></div>
                    <div class="text-muted" style="font-size: 0.65rem;">Pending Approvals</div>
                    <div class="small text-muted">Awaiting decision</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card rounded-sm text-center h-100">
                <div class="card-body py-2">
                    <div class="text-info fw-bold" style="font-size: 1.5rem;"><?php echo e($avgApprovalTime ?? 0); ?></div>
                    <div class="text-muted" style="font-size: 0.65rem;">Avg Approval Time</div>
                    <div class="small text-muted">Days to finalize</div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row g-2">
        <!-- Left Column -->
        <div class="col-lg-8">
            
            <!-- Pending Approvals by Level Card -->
            <div class="card rounded-sm mb-2">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-clock text-warning me-1"></i>
                        <span style="font-weight: 600; font-size: 0.7rem;">Pending Approvals by Level</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <div class="row text-center">
                        <div class="col">
                            <div class="fw-bold" style="font-size: 1.2rem;"><?php echo e($pendingByLevel['hod'] ?? 0); ?></div>
                            <div class="text-muted" style="font-size: 0.6rem;">HOD</div>
                            <div class="progress mt-1" style="height: 4px;">
                                <div class="progress-bar bg-primary" style="width: <?php echo e(($pendingApprovals ?? 0) > 0 ? (($pendingByLevel['hod'] ?? 0) / ($pendingApprovals ?? 1)) * 100 : 0); ?>%"></div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="fw-bold" style="font-size: 1.2rem;"><?php echo e($pendingByLevel['dean'] ?? 0); ?></div>
                            <div class="text-muted" style="font-size: 0.6rem;">Dean</div>
                            <div class="progress mt-1" style="height: 4px;">
                                <div class="progress-bar bg-info" style="width: <?php echo e(($pendingApprovals ?? 0) > 0 ? (($pendingByLevel['dean'] ?? 0) / ($pendingApprovals ?? 1)) * 100 : 0); ?>%"></div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="fw-bold" style="font-size: 1.2rem;"><?php echo e($pendingByLevel['hr'] ?? 0); ?></div>
                            <div class="text-muted" style="font-size: 0.6rem;">HR</div>
                            <div class="progress mt-1" style="height: 4px;">
                                <div class="progress-bar bg-warning" style="width: <?php echo e(($pendingApprovals ?? 0) > 0 ? (($pendingByLevel['hr'] ?? 0) / ($pendingApprovals ?? 1)) * 100 : 0); ?>%"></div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="fw-bold" style="font-size: 1.2rem;"><?php echo e($pendingByLevel['dvc'] ?? 0); ?></div>
                            <div class="text-muted" style="font-size: 0.6rem;">DVC</div>
                            <div class="progress mt-1" style="height: 4px;">
                                <div class="progress-bar bg-danger" style="width: <?php echo e(($pendingApprovals ?? 0) > 0 ? (($pendingByLevel['dvc'] ?? 0) / ($pendingApprovals ?? 1)) * 100 : 0); ?>%"></div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="fw-bold" style="font-size: 1.2rem;"><?php echo e($pendingByLevel['hr_final'] ?? 0); ?></div>
                            <div class="text-muted" style="font-size: 0.6rem;">HR Final</div>
                            <div class="progress mt-1" style="height: 4px;">
                                <div class="progress-bar bg-success" style="width: <?php echo e(($pendingApprovals ?? 0) > 0 ? (($pendingByLevel['hr_final'] ?? 0) / ($pendingApprovals ?? 1)) * 100 : 0); ?>%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Monthly Trends Chart -->
            <div class="card rounded-sm mb-2">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-chart-line text-primary me-1"></i>
                        <span style="font-weight: 600; font-size: 0.7rem;">Leave Trends (Last 12 Months)</span>
                    </div>
                </div>
                <div class="card-body p-2">
                    <canvas id="trendsChart" height="200" style="width: 100%;"></canvas>
                </div>
            </div>
            
            <!-- Department Comparison -->
            <div class="card rounded-sm mb-2">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-building text-primary me-1"></i>
                        <span style="font-weight: 600; font-size: 0.7rem;">Department Comparison (<?php echo e($currentYear ?? date('Y')); ?> vs <?php echo e($previousYear ?? date('Y')-1); ?>)</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0" style="font-size: 0.7rem;">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-2 py-1">Department</th>
                                    <th class="py-1 text-center"><?php echo e($currentYear ?? date('Y')); ?></th>
                                    <th class="py-1 text-center"><?php echo e($previousYear ?? date('Y')-1); ?></th>
                                    <th class="pe-2 py-1 text-center">Change</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $departmentCompare ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dept): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td class="ps-2 py-1"><?php echo e($dept['name'] ?? 'N/A'); ?></td>
                                    <td class="py-1 text-center"><?php echo e($dept['current'] ?? 0); ?></td>
                                    <td class="py-1 text-center"><?php echo e($dept['previous'] ?? 0); ?></td>
                                    <td class="pe-2 py-1 text-center">
                                        <?php if(($dept['change'] ?? 0) > 0): ?>
                                            <span class="text-danger">+<?php echo e($dept['change']); ?>%</span>
                                        <?php elseif(($dept['change'] ?? 0) < 0): ?>
                                            <span class="text-success"><?php echo e($dept['change']); ?>%</span>
                                        <?php else: ?>
                                            <span class="text-muted">0%</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="4" class="text-center py-2 text-muted">No department data available</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Monthly Heatmap -->
            <div class="card rounded-sm mb-2">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-calendar text-primary me-1"></i>
                        <span style="font-weight: 600; font-size: 0.7rem;">Monthly Distribution (<?php echo e($currentYear ?? date('Y')); ?>)</span>
                    </div>
                </div>
                <div class="card-body p-2">
                    <canvas id="heatmapChart" height="150" style="width: 100%;"></canvas>
                </div>
            </div>
            
            <!-- Recent Approvals -->
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-history text-primary me-1"></i>
                        <span style="font-weight: 600; font-size: 0.7rem;">Recent Approvals</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0" style="font-size: 0.7rem;">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-2 py-1">Staff</th>
                                    <th class="py-1">Leave Type</th>
                                    <th class="py-1">Period</th>
                                    <th class="py-1 text-center">Days</th>
                                    <th class="pe-2 py-1 text-center">Approved</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $recentApprovals ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $approval): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td class="ps-2 py-1"><?php echo e($approval->staff->full_name ?? 'N/A'); ?></td>
                                    <td class="py-1"><?php echo e($approval->leaveType->name ?? 'N/A'); ?></td>
                                    <td class="py-1"><?php echo e(\Carbon\Carbon::parse($approval->start_date)->format('d M')); ?> - <?php echo e(\Carbon\Carbon::parse($approval->end_date)->format('d M Y')); ?></td>
                                    <td class="py-1 text-center"><?php echo e($approval->days); ?></td>
                                    <td class="pe-2 py-1 text-center"><?php echo e($approval->updated_at->format('d M Y')); ?></td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-2 text-muted">No recent approvals</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Right Column -->
        <div class="col-lg-4">
            
            <!-- Staff Category Distribution -->
            <div class="card rounded-sm mb-2">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-users text-primary me-1"></i>
                        <span style="font-weight: 600; font-size: 0.7rem;">Leave by Staff Category</span>
                    </div>
                </div>
                <div class="card-body p-2">
                    <canvas id="categoryChart" height="150" style="width: 100%;"></canvas>
                    <div class="row text-center mt-2">
                        <div class="col-4">
                            <div class="fw-bold text-primary"><?php echo e(number_format($categoryStats['academic'] ?? 0)); ?></div>
                            <div class="text-muted" style="font-size: 0.55rem;">Academic</div>
                        </div>
                        <div class="col-4">
                            <div class="fw-bold text-info"><?php echo e(number_format($categoryStats['administrative'] ?? 0)); ?></div>
                            <div class="text-muted" style="font-size: 0.55rem;">Administrative</div>
                        </div>
                        <div class="col-4">
                            <div class="fw-bold text-secondary"><?php echo e(number_format($categoryStats['supporting'] ?? 0)); ?></div>
                            <div class="text-muted" style="font-size: 0.55rem;">Supporting</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Payment Status Summary -->
            <div class="card rounded-sm mb-2">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-currency-dollar text-primary me-1"></i>
                        <span style="font-weight: 600; font-size: 0.7rem;">Payment Status Summary</span>
                    </div>
                </div>
                <div class="card-body p-2">
                    <canvas id="paymentChart" height="150" style="width: 100%;"></canvas>
                    <div class="row text-center mt-2">
                        <div class="col-4">
                            <div class="fw-bold text-success"><?php echo e($paymentStatsPercentage['paid'] ?? 0); ?>%</div>
                            <div class="text-muted" style="font-size: 0.55rem;">Paid</div>
                            <div class="small text-muted"><?php echo e(number_format($paymentStats['paid'] ?? 0)); ?> requests</div>
                        </div>
                        <div class="col-4">
                            <div class="fw-bold text-warning"><?php echo e($paymentStatsPercentage['partial'] ?? 0); ?>%</div>
                            <div class="text-muted" style="font-size: 0.55rem;">Partial</div>
                            <div class="small text-muted"><?php echo e(number_format($paymentStats['partial'] ?? 0)); ?> requests</div>
                        </div>
                        <div class="col-4">
                            <div class="fw-bold text-danger"><?php echo e($paymentStatsPercentage['unpaid'] ?? 0); ?>%</div>
                            <div class="text-muted" style="font-size: 0.55rem;">Unpaid</div>
                            <div class="small text-muted"><?php echo e(number_format($paymentStats['unpaid'] ?? 0)); ?> requests</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Top Leave Types -->
            <div class="card rounded-sm mb-2">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-tags text-primary me-1"></i>
                        <span style="font-weight: 600; font-size: 0.7rem;">Top Leave Types</span>
                    </div>
                </div>
                <div class="card-body p-2">
                    <?php $__currentLoopData = $topLeaveTypes ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span style="font-size: 0.7rem;"><?php echo e($type->name); ?></span>
                        <div class="d-flex align-items-center gap-2">
                            <div class="progress" style="width: 100px; height: 4px;">
                                <div class="progress-bar bg-primary" style="width: <?php echo e(($type->leave_requests_count > 0 && ($topLeaveTypes->first()->leave_requests_count ?? 1) > 0) ? min(100, ($type->leave_requests_count / $topLeaveTypes->first()->leave_requests_count) * 100) : 0); ?>%"></div>
                            </div>
                            <span class="text-muted" style="font-size: 0.65rem;"><?php echo e($type->leave_requests_count); ?></span>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
            
            <!-- Alerts & Action Items -->
            <div class="card rounded-sm mb-2">
                <div class="card-header py-1 px-2 bg-danger-subtle">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-alert-triangle text-danger me-1"></i>
                        <span style="font-weight: 600; font-size: 0.7rem; color: #DC2626;">Alerts & Action Items</span>
                    </div>
                </div>
                <div class="card-body p-2">
                    <?php $__empty_1 = true; $__currentLoopData = $alerts ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $alert): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="alert alert-<?php echo e($alert['type']); ?> py-1 px-2 mb-2 rounded-sm" style="font-size: 0.65rem;">
                        <i class="<?php echo e($alert['icon']); ?> me-1"></i>
                        <strong><?php echo e($alert['title']); ?></strong>
                        <div class="mt-1"><?php echo e($alert['message']); ?></div>
                        <?php if(isset($alert['action_url']) && $alert['action_url'] != '#'): ?>
                        <div class="mt-1">
                            <a href="<?php echo e($alert['action_url']); ?>" class="small"><?php echo e($alert['action_text'] ?? 'View'); ?> →</a>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="text-center text-muted py-2" style="font-size: 0.65rem;">
                        <i class="ti ti-check-circle me-1"></i>
                        No alerts at this time. All metrics are within normal range.
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Monthly Projection -->
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-calendar text-primary me-1"></i>
                        <span style="font-weight: 600; font-size: 0.7rem;">Leave Projections</span>
                    </div>
                </div>
                <div class="card-body p-2">
                    <?php $__currentLoopData = $projection ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $proj): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span style="font-size: 0.7rem;"><?php echo e($proj['month']); ?></span>
                        <div class="d-flex align-items-center gap-2">
                            <span class="fw-semibold" style="font-size: 0.7rem;"><?php echo e(number_format($proj['projected'])); ?></span>
                            <span class="badge bg-<?php echo e($proj['confidence'] == 'high' ? 'success' : 'secondary'); ?>-subtle text-<?php echo e($proj['confidence'] == 'high' ? 'success' : 'secondary'); ?> rounded-pill" style="font-size: 0.55rem;">
                                <?php echo e(ucfirst($proj['confidence'])); ?>

                            </span>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Monthly Trends Chart
    const ctx = document.getElementById('trendsChart')?.getContext('2d');
    if (ctx && <?php echo json_encode($monthlyTrends ?? [], 15, 512) ?>.length > 0) {
        const trends = <?php echo json_encode($monthlyTrends, 15, 512) ?>;
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: trends.map(t => t.month),
                datasets: [
                    {
                        label: 'Total Requests',
                        data: trends.map(t => t.total),
                        borderColor: '#3B82F6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        fill: true,
                        tension: 0.3
                    },
                    {
                        label: 'Approved',
                        data: trends.map(t => t.approved),
                        borderColor: '#10B981',
                        backgroundColor: 'transparent',
                        tension: 0.3
                    },
                    {
                        label: 'Rejected',
                        data: trends.map(t => t.rejected),
                        borderColor: '#EF4444',
                        backgroundColor: 'transparent',
                        tension: 0.3
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { position: 'bottom', labels: { font: { size: 10 } } }
                },
                scales: { y: { beginAtZero: true, grid: { color: '#E8E2D5' } } }
            }
        });
    }
    
    // Monthly Heatmap
    const heatmapCtx = document.getElementById('heatmapChart')?.getContext('2d');
    if (heatmapCtx && <?php echo json_encode($monthlyHeatmap ?? [], 15, 512) ?>.length > 0) {
        const heatmapData = <?php echo json_encode($monthlyHeatmap, 15, 512) ?>;
        const maxCount = Math.max(...heatmapData.map(d => d.count), 1);
        
        new Chart(heatmapCtx, {
            type: 'bar',
            data: {
                labels: heatmapData.map(d => d.month),
                datasets: [{
                    label: 'Leave Requests',
                    data: heatmapData.map(d => d.count),
                    backgroundColor: heatmapData.map(d => {
                        const intensity = d.count / maxCount;
                        if (intensity > 0.7) return '#EF4444';
                        if (intensity > 0.4) return '#F59E0B';
                        return '#10B981';
                    }),
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, title: { display: true, text: 'Requests', font: { size: 10 } } } }
            }
        });
    }
    
    // Category Chart (Pie)
    const categoryCtx = document.getElementById('categoryChart')?.getContext('2d');
    if (categoryCtx) {
        new Chart(categoryCtx, {
            type: 'doughnut',
            data: {
                labels: ['Academic', 'Administrative', 'Supporting'],
                datasets: [{
                    data: [<?php echo e($categoryStats['academic'] ?? 0); ?>, <?php echo e($categoryStats['administrative'] ?? 0); ?>, <?php echo e($categoryStats['supporting'] ?? 0); ?>],
                    backgroundColor: ['#3B82F6', '#10B981', '#94A3B8'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { position: 'bottom', labels: { font: { size: 9 } } }
                }
            }
        });
    }
    
    // Payment Chart
    const paymentCtx = document.getElementById('paymentChart')?.getContext('2d');
    if (paymentCtx) {
        new Chart(paymentCtx, {
            type: 'doughnut',
            data: {
                labels: ['Paid', 'Partial', 'Unpaid'],
                datasets: [{
                    data: [<?php echo e($paymentStats['paid'] ?? 0); ?>, <?php echo e($paymentStats['partial'] ?? 0); ?>, <?php echo e($paymentStats['unpaid'] ?? 0); ?>],
                    backgroundColor: ['#10B981', '#F59E0B', '#EF4444'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { position: 'bottom', labels: { font: { size: 9 } } }
                }
            }
        });
    }
</script>

<style media="print">
    .sidebar-area, .topbar, .page-header .btn, .btn, .breadcrumb, .sticky-banner {
        display: none !important;
    }
    .card-header {
        background: #f8f9fa !important;
        print-color-adjust: exact;
    }
</style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\wamp64\www\unileave\resources\views/vc/dashboard.blade.php ENDPATH**/ ?>