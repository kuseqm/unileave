

<?php $__env->startSection('title', 'Dashboard'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-2">
    
    <!-- Breadcrumb + Welcome Row -->
    <div class="row align-items-center mb-3">
        <div class="col">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0" style="font-size: 0.75rem;">
                    <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>" class="text-muted">Home</a></li>
                    <li class="breadcrumb-item active">Dashboard</li>
                </ol>
            </nav>
        </div>
    </div>
    
    <!-- Stat Cards - Using x-stats-card component with clickable links -->
    <div class="row g-2 mb-3">
        <?php if (isset($component)) { $__componentOriginal8f216e051c231b98198765acd723fb77 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8f216e051c231b98198765acd723fb77 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stats-card','data' => ['id' => 'stat-total-staff','value' => ''.e(number_format($totalStaff ?? 0)).'','label' => 'Total Staff','subtext' => '↑ +'.e(number_format($newStaffThisMonth ?? 0)).' this month','icon' => 'users','link' => ''.e($statsLinks['total_staff'] ?? null).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('stats-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'stat-total-staff','value' => ''.e(number_format($totalStaff ?? 0)).'','label' => 'Total Staff','subtext' => '↑ +'.e(number_format($newStaffThisMonth ?? 0)).' this month','icon' => 'users','link' => ''.e($statsLinks['total_staff'] ?? null).'']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stats-card','data' => ['id' => 'stat-pending','value' => ''.e(number_format($pendingApprovalsCount ?? 0)).'','label' => 'Pending','subtext' => 'awaiting approval','color' => 'warning','icon' => 'clock','link' => ''.e($statsLinks['pending_approvals'] ?? route('approvals')).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('stats-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'stat-pending','value' => ''.e(number_format($pendingApprovalsCount ?? 0)).'','label' => 'Pending','subtext' => 'awaiting approval','color' => 'warning','icon' => 'clock','link' => ''.e($statsLinks['pending_approvals'] ?? route('approvals')).'']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stats-card','data' => ['id' => 'stat-on-leave','value' => ''.e(number_format($onLeaveToday ?? 0)).'','label' => 'On Leave','subtext' => 'currently away','color' => 'success','icon' => 'calendar','link' => ''.e($statsLinks['on_leave'] ?? null).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('stats-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'stat-on-leave','value' => ''.e(number_format($onLeaveToday ?? 0)).'','label' => 'On Leave','subtext' => 'currently away','color' => 'success','icon' => 'calendar','link' => ''.e($statsLinks['on_leave'] ?? null).'']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stats-card','data' => ['id' => 'stat-leave-used','value' => ''.e(number_format($leaveUsed ?? 0)).'','label' => 'Days Used','subtext' => 'this year','icon' => 'chart-pie','link' => ''.e($statsLinks['leave_used'] ?? null).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('stats-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'stat-leave-used','value' => ''.e(number_format($leaveUsed ?? 0)).'','label' => 'Days Used','subtext' => 'this year','icon' => 'chart-pie','link' => ''.e($statsLinks['leave_used'] ?? null).'']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stats-card','data' => ['id' => 'stat-low-balance','value' => ''.e(number_format($lowBalanceCount ?? 0)).'','label' => 'Low Balance','subtext' => 'below 5 days','color' => 'danger','icon' => 'alert-triangle','link' => ''.e($statsLinks['low_balance'] ?? null).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('stats-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'stat-low-balance','value' => ''.e(number_format($lowBalanceCount ?? 0)).'','label' => 'Low Balance','subtext' => 'below 5 days','color' => 'danger','icon' => 'alert-triangle','link' => ''.e($statsLinks['low_balance'] ?? null).'']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stats-card','data' => ['id' => 'stat-upcoming','value' => ''.e(number_format($upcomingLeave ?? 0)).'','label' => 'Upcoming','subtext' => 'next 7 days','color' => 'info','icon' => 'calendar-event','link' => ''.e($statsLinks['upcoming_leave'] ?? null).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('stats-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'stat-upcoming','value' => ''.e(number_format($upcomingLeave ?? 0)).'','label' => 'Upcoming','subtext' => 'next 7 days','color' => 'info','icon' => 'calendar-event','link' => ''.e($statsLinks['upcoming_leave'] ?? null).'']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stats-card','data' => ['value' => ''.e(number_format($totalDepartments ?? 0)).'','label' => 'Depts','subtext' => 'active','icon' => 'building','link' => ''.e($statsLinks['total_departments'] ?? null).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('stats-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => ''.e(number_format($totalDepartments ?? 0)).'','label' => 'Depts','subtext' => 'active','icon' => 'building','link' => ''.e($statsLinks['total_departments'] ?? null).'']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stats-card','data' => ['value' => ''.e(number_format($leaveTypesCount ?? 0)).'','label' => 'Leave Types','subtext' => 'policies','icon' => 'tags','link' => ''.e($statsLinks['leave_types'] ?? null).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('stats-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => ''.e(number_format($leaveTypesCount ?? 0)).'','label' => 'Leave Types','subtext' => 'policies','icon' => 'tags','link' => ''.e($statsLinks['leave_types'] ?? null).'']); ?>
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
    
    <!-- Main Content -->
    <div class="row g-2">
        
        <!-- Left Column -->
        <div class="col-lg-7">
            <!-- Recent Leave Requests Table - Clickable Rows -->
            <div class="card">
                <div class="card-header py-2 px-3 d-flex justify-content-between align-items-center">
                    <span style="font-size: 0.8rem; font-weight: 600;">Recent Leave Requests</span>
                    <div>
                        <a href="<?php echo e(route('leave-requests.status')); ?>" style="font-size: 0.7rem; margin-right: 0.5rem;">
                            <i class="ti ti-list me-1"></i>View All Status
                        </a>
                        <a href="<?php echo e(route('approvals')); ?>" style="font-size: 0.7rem;">View all →</a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0" style="font-size: 0.75rem;">
                            <thead>
                                <tr class="small">
                                    <th class="ps-3 fw-semibold">Staff</th>
                                    <th class="fw-semibold">Leave Type</th>
                                    <th class="fw-semibold">Duration</th>
                                    <th class="fw-semibold">Days</th>
                                    <th class="pe-3 fw-semibold">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $user = auth()->user();
                                    $role = $user->getRoleNames()->first();
                                    
                                    $recentQuery = App\Models\LeaveRequest::with(['staff', 'leaveType']);
                                    
                                    switch ($role) {
                                        case 'staff':
                                            $recentQuery->where('staff_id', $user->id);
                                            break;
                                        case 'unit_head':
                                            $unitHeadDept = $user->unitHeadDepartment;
                                            if ($unitHeadDept) {
                                                $recentQuery->whereHas('staff', function($q) use ($unitHeadDept) {
                                                    $q->where('department_id', $unitHeadDept->id);
                                                });
                                            } else {
                                                $recentQuery->whereRaw('1 = 0');
                                            }
                                            break;
                                        case 'hod':
                                            $recentQuery->whereHas('staff.department', function($q) use ($user) {
                                                $q->where('hod_staff_id', $user->id);
                                            });
                                            break;
                                        case 'dean':
                                            $recentQuery->whereHas('staff.department.school', function($q) use ($user) {
                                                $q->where('dean_staff_id', $user->id);
                                            });
                                            break;
                                        case 'dvca':
                                            $recentQuery->whereHas('staff', function($q) {
                                                $q->where('staff_category', 'academic');
                                            });
                                            break;
                                        case 'dvc-pfa':
                                            $recentQuery->whereHas('staff', function($q) {
                                                $q->where('staff_category', 'administrative');
                                            });
                                            break;
                                        case 'hr-officer':
                                        case 'hr-manager':
                                        case 'admin':
                                            break;
                                        default:
                                            $recentQuery->where('staff_id', $user->id);
                                            break;
                                    }
                                    
                                    $recentRequests = $recentQuery->latest()->limit(5)->get();
                                ?>
                                
                                <?php $__empty_1 = true; $__currentLoopData = $recentRequests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $request): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr style="font-size: 0.75rem; cursor: pointer;" onclick="window.location='<?php echo e(route('leave-requests.show', $request->id)); ?>'" class="clickable-row">
                                    <td class="ps-3">
                                        <strong><?php echo e($request->staff->first_name); ?> <?php echo e($request->staff->last_name); ?></strong>
                                        <div class="text-muted" style="font-size: 0.65rem;"><?php echo e($request->staff->staff_id ?? ''); ?></div>
                                    </td>
                                    <td><?php echo e($request->leaveType->name ?? 'N/A'); ?></td>
                                    <td><?php echo e(\Carbon\Carbon::parse($request->start_date)->format('M d')); ?> - <?php echo e(\Carbon\Carbon::parse($request->end_date)->format('M d')); ?></td>
                                    <td><?php echo e($request->days); ?></td>
                                    <td class="pe-3">
                                        <?php if($request->status == 'pending'): ?>
                                            <span class="badge bg-warning-subtle text-warning px-2 py-1 rounded-pill" style="font-size: 0.65rem;">Pending</span>
                                        <?php elseif($request->status == 'approved'): ?>
                                            <span class="badge bg-success-subtle text-success px-2 py-1 rounded-pill" style="font-size: 0.65rem;">Approved</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger-subtle text-danger px-2 py-1 rounded-pill" style="font-size: 0.65rem;">Rejected</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-3 text-muted">No leave requests found</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Department Leave Requests Card - Clickable Rows -->
            <?php if(auth()->check() && auth()->user()->hasAnyRole('admin|hr-officer')): ?>
            <div class="card mt-2">
                <div class="card-header py-2 px-3 d-flex justify-content-between align-items-center">
                    <span style="font-size: 0.8rem; font-weight: 600;">Department Leave Requests</span>
                    <a href="<?php echo e(route('admin.departments.index')); ?>" style="font-size: 0.7rem;">Manage Departments →</a>
                </div>
                <div class="card-body p-0">
                    <?php $__empty_1 = true; $__currentLoopData = $departmentLeaveStats ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dept): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="d-flex justify-content-between align-items-center px-3 py-2" style="font-size: 0.75rem; border-bottom: 1px solid #E8E2D5; cursor: pointer;" onclick="window.location='<?php echo e(route('staff.index', ['department_id' => $dept->id])); ?>'">
                        <span class="hover-link"><?php echo e($dept->name); ?></span>
                        <div class="d-flex align-items-center gap-3">
                            <span class="text-muted" style="font-size: 0.7rem;"><?php echo e($dept->request_count ?? 0); ?> requests</span>
                            <div class="progress" style="width: 100px; height: 4px;">
                                <div class="progress-bar bg-primary" style="width: <?php echo e($dept->percentage ?? 0); ?>%"></div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="text-center py-3 text-muted" style="font-size: 0.75rem;">No department data available</div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Right Column -->
        <div class="col-lg-5">
            <!-- Quick Actions Card -->
            <div class="card">
                <div class="card-header py-2 px-3">
                    <span style="font-size: 0.8rem; font-weight: 600;">Quick Actions</span>
                </div>
                <div class="card-body p-2">
                    <div class="row g-1">
                        <div class="col-6">
                            <a href="<?php echo e(route('staff.create')); ?>" class="btn btn-primary w-100 py-1" style="font-size: 0.7rem;">
                                <i class="ti ti-user-plus me-1"></i> Add Staff
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="<?php echo e(route('admin.import.staff')); ?>" class="btn btn-outline-secondary w-100 py-1" style="font-size: 0.7rem;">
                                <i class="ti ti-file-import me-1"></i> Import
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="<?php echo e(route('blackout.index')); ?>" class="btn btn-outline-secondary w-100 py-1" style="font-size: 0.7rem;">
                                <i class="ti ti-ban me-1"></i> Blackout
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="<?php echo e(route('reports.index')); ?>" class="btn btn-outline-secondary w-100 py-1" style="font-size: 0.7rem;">
                                <i class="ti ti-chart-bar me-1"></i> Reports
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Low Balance Alerts - Clickable Staff Names -->
            <?php if(auth()->check() && auth()->user()->hasAnyRole('admin|hr-officer')): ?>
            <div class="card mt-2">
                <div class="card-header py-2 px-3 d-flex justify-content-between align-items-center">
                    <span style="font-size: 0.8rem; font-weight: 600;">⚠️ Low Balance Alerts</span>
                    <a href="<?php echo e(route('staff.index', ['filter' => 'low_balance'])); ?>" style="font-size: 0.7rem;">View All →</a>
                </div>
                <div class="card-body p-0">
                    <?php $__empty_1 = true; $__currentLoopData = $lowBalanceStaff ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $staff): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="d-flex justify-content-between align-items-center px-3 py-2" style="font-size: 0.75rem; border-bottom: 1px solid #E8E2D5; cursor: pointer;" onclick="window.location='<?php echo e(route('staff.show', $staff->id)); ?>'">
                        <div>
                            <span class="fw-semibold hover-link"><?php echo e($staff->first_name); ?> <?php echo e($staff->last_name); ?></span>
                            <div class="text-muted" style="font-size: 0.65rem;"><?php echo e($staff->staff_id ?? ''); ?></div>
                        </div>
                        <span class="text-danger fw-semibold" style="font-size: 0.75rem;">
                            <?php
                                $balance = $staff->leaveBalances->where('leave_type_id', 1)->first();
                                echo ($balance ? $balance->balance : 0) . ' days left';
                            ?>
                        </span>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="text-center py-3 text-muted" style="font-size: 0.75rem;">No low balance alerts</div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Unresponsive Acting Staff Widget -->
            <?php if(auth()->check() && auth()->user()->hasAnyRole('hod|hr-officer|admin')): ?>
            <?php
                $unresponsiveCount = App\Models\LeaveRequest::where('acting_status', 'pending')
                    ->where('created_at', '<', now()->subHours(48))
                    ->count();
            ?>
            <?php if($unresponsiveCount > 0): ?>
            <div class="card mt-2 border-danger">
                <div class="card-header py-2 px-3 bg-danger-subtle border-danger">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-alert-circle text-danger me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.75rem; font-weight: 600;">⚠️ Unresponsive Acting Staff</span>
                        <span class="badge bg-danger ms-2" style="font-size: 0.6rem;"><?php echo e($unresponsiveCount); ?></span>
                    </div>
                </div>
                <div class="card-body p-2">
                    <div class="alert alert-danger py-1 px-2 mb-0 rounded-sm" style="font-size: 0.65rem; cursor: pointer;" onclick="window.location='<?php echo e(route('leave-requests.pending')); ?>'">
                        <i class="ti ti-clock me-1"></i>
                        <strong><?php echo e($unresponsiveCount); ?></strong> leave request(s) have been waiting for acting staff response for over 48 hours.
                        <div class="mt-1">
                            <span class="text-danger fw-semibold" style="font-size: 0.65rem;">
                                Click to review pending requests →
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            <?php endif; ?>
            
            <!-- Active Blackouts - Clickable Rows -->
            <div class="card mt-2">
                <div class="card-header py-2 px-3 d-flex justify-content-between align-items-center">
                    <span style="font-size: 0.8rem; font-weight: 600;">🚫 Active Blackouts</span>
                    <a href="<?php echo e(route('blackout.index')); ?>" style="font-size: 0.7rem;">Manage →</a>
                </div>
                <div class="card-body p-0">
                    <?php $__empty_1 = true; $__currentLoopData = $blackoutPeriods ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $blackout): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="d-flex justify-content-between align-items-center px-3 py-2" style="font-size: 0.75rem; border-bottom: 1px solid #E8E2D5; cursor: pointer;" onclick="window.location='<?php echo e(route('blackout.show', $blackout->id)); ?>'">
                        <div>
                            <span class="fw-semibold hover-link"><?php echo e($blackout->name); ?></span>
                            <div class="text-muted" style="font-size: 0.65rem;">
                                <?php echo e(\Carbon\Carbon::parse($blackout->start_date)->format('M d')); ?> - <?php echo e(\Carbon\Carbon::parse($blackout->end_date)->format('M d, Y')); ?>

                            </div>
                        </div>
                        <span class="badge <?php echo e($blackout->restriction_type == 'hard' ? 'bg-danger-subtle text-danger' : 'bg-warning-subtle text-warning'); ?> px-2 py-1 rounded-pill" style="font-size: 0.65rem;"><?php echo e($blackout->restriction_type == 'hard' ? 'Hard Block' : 'Soft Warning'); ?></span>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="text-center py-3 text-muted" style="font-size: 0.75rem;">No active blackout periods</div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Recent Approvals - Clickable Rows -->
            <?php if(auth()->check() && auth()->user()->hasRole('admin')): ?>
            <div class="card mt-2">
                <div class="card-header py-2 px-3 d-flex justify-content-between align-items-center">
                    <span style="font-size: 0.8rem; font-weight: 600;">✅ Recent Approvals</span>
                    <a href="<?php echo e(route('leave-requests.status', ['filter' => 'approved'])); ?>" style="font-size: 0.7rem;">View All →</a>
                </div>
                <div class="card-body p-0">
                    <?php $__empty_1 = true; $__currentLoopData = $recentApprovals ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $approval): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="d-flex justify-content-between align-items-center px-3 py-2" style="font-size: 0.75rem; border-bottom: 1px solid #E8E2D5; cursor: pointer;" onclick="window.location='<?php echo e(route('leave-requests.show', $approval->id)); ?>'">
                        <div>
                            <span class="hover-link"><?php echo e($approval->staff->first_name ?? 'Unknown'); ?> <?php echo e($approval->staff->last_name ?? ''); ?></span>
                            <div class="text-muted" style="font-size: 0.65rem;"><?php echo e($approval->leaveType->name ?? 'Leave Request'); ?></div>
                        </div>
                        <span class="badge <?php echo e($approval->status == 'approved' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger'); ?> px-2 py-1 rounded-pill" style="font-size: 0.65rem;"><?php echo e(ucfirst($approval->status ?? 'Pending')); ?></span>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="text-center py-3 text-muted" style="font-size: 0.75rem;">No recent activity</div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Leave Type Distribution - Clickable Rows -->
    <?php if(auth()->check() && auth()->user()->hasAnyRole('admin|hr-officer')): ?>
    <div class="row g-2 mt-2">
        <div class="col-12">
            <div class="card">
                <div class="card-header py-2 px-3 d-flex justify-content-between align-items-center">
                    <span style="font-size: 0.8rem; font-weight: 600;">Leave Type Distribution</span>
                    <a href="<?php echo e(route('reports.index')); ?>" style="font-size: 0.7rem;">View Reports →</a>
                </div>
                <div class="card-body p-2">
                    <div class="row g-2">
                        <?php $__empty_1 = true; $__currentLoopData = $leaveTypeStats ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="col-md-3 text-center py-2" style="cursor: pointer;" onclick="window.location='<?php echo e(route('leave-requests.status', ['leave_type' => $type->id])); ?>'">
                            <div class="text-muted" style="font-size: 0.7rem;" class="hover-link"><?php echo e($type->name); ?></div>
                            <div class="fw-bold" style="font-size: 1rem;"><?php echo e($type->request_count ?? 0); ?></div>
                            <div class="text-muted" style="font-size: 0.65rem;">requests</div>
                            <div class="progress mx-auto mt-1" style="width: 80%; height: 3px;">
                                <div class="progress-bar bg-primary" style="width: <?php echo e($type->percentage ?? 0); ?>%"></div>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="col-12 text-center py-3 text-muted" style="font-size: 0.75rem;">No leave type data available</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<style>
/* Hover effect for clickable rows */
.clickable-row:hover {
    background-color: #F8FAFC !important;
    transition: background 0.2s ease;
}

.hover-link:hover {
    text-decoration: underline;
    color: #3B82F6 !important;
}

.d-flex.justify-content-between.align-items-center.px-3.py-2:hover {
    background-color: #F8FAFC;
}

.col-md-3.text-center.py-2:hover {
    background-color: #F8FAFC;
    border-radius: 8px;
    transform: translateY(-2px);
    transition: all 0.2s ease;
}

.stat-highlight {
    animation: statFlash 0.5s ease-in-out;
}

@keyframes statFlash {
    0% { background-color: transparent; transform: scale(1); }
    50% { background-color: rgba(59, 130, 246, 0.2); transform: scale(1.05); }
    100% { background-color: transparent; transform: scale(1); }
}
</style>

<!-- Real-time Stats Auto-Refresh -->
<script>
// Store current values to detect changes
let currentStats = {
    pendingApprovalsCount: <?php echo e($pendingApprovalsCount ?? 0); ?>,
    totalStaff: <?php echo e($totalStaff ?? 0); ?>,
    onLeaveToday: <?php echo e($onLeaveToday ?? 0); ?>,
    upcomingLeave: <?php echo e($upcomingLeave ?? 0); ?>,
    leaveUsed: <?php echo e($leaveUsed ?? 0); ?>,
    lowBalanceCount: <?php echo e($lowBalanceCount ?? 0); ?>

};

function highlightElement(elementId) {
    const element = document.getElementById(elementId);
    if (element) {
        element.classList.add('stat-highlight');
        setTimeout(() => {
            element.classList.remove('stat-highlight');
        }, 1000);
    }
}

function refreshDashboardStats() {
    fetch('<?php echo e(route("dashboard.stats")); ?>')
        .then(response => response.json())
        .then(data => {
            if (data.pendingApprovalsCount !== undefined && currentStats.pendingApprovalsCount !== data.pendingApprovalsCount) {
                const element = document.getElementById('stat-pending');
                if (element) {
                    const valueSpan = element.querySelector('.fw-bold');
                    if (valueSpan) valueSpan.innerText = data.pendingApprovalsCount;
                    highlightElement('stat-pending');
                }
                currentStats.pendingApprovalsCount = data.pendingApprovalsCount;
            }
            
            if (data.totalStaff !== undefined && currentStats.totalStaff !== data.totalStaff) {
                const element = document.getElementById('stat-total-staff');
                if (element) {
                    const valueSpan = element.querySelector('.fw-bold');
                    if (valueSpan) valueSpan.innerText = data.totalStaff.toLocaleString();
                    highlightElement('stat-total-staff');
                }
                currentStats.totalStaff = data.totalStaff;
            }
            
            if (data.onLeaveToday !== undefined && currentStats.onLeaveToday !== data.onLeaveToday) {
                const element = document.getElementById('stat-on-leave');
                if (element) {
                    const valueSpan = element.querySelector('.fw-bold');
                    if (valueSpan) valueSpan.innerText = data.onLeaveToday;
                    highlightElement('stat-on-leave');
                }
                currentStats.onLeaveToday = data.onLeaveToday;
            }
            
            if (data.upcomingLeave !== undefined && currentStats.upcomingLeave !== data.upcomingLeave) {
                const element = document.getElementById('stat-upcoming');
                if (element) {
                    const valueSpan = element.querySelector('.fw-bold');
                    if (valueSpan) valueSpan.innerText = data.upcomingLeave;
                    highlightElement('stat-upcoming');
                }
                currentStats.upcomingLeave = data.upcomingLeave;
            }
            
            if (data.leaveUsed !== undefined && currentStats.leaveUsed !== data.leaveUsed) {
                const element = document.getElementById('stat-leave-used');
                if (element) {
                    const valueSpan = element.querySelector('.fw-bold');
                    if (valueSpan) valueSpan.innerText = data.leaveUsed.toLocaleString();
                    highlightElement('stat-leave-used');
                }
                currentStats.leaveUsed = data.leaveUsed;
            }
            
            if (data.lowBalanceCount !== undefined && currentStats.lowBalanceCount !== data.lowBalanceCount) {
                const element = document.getElementById('stat-low-balance');
                if (element) {
                    const valueSpan = element.querySelector('.fw-bold');
                    if (valueSpan) valueSpan.innerText = data.lowBalanceCount;
                    highlightElement('stat-low-balance');
                }
                currentStats.lowBalanceCount = data.lowBalanceCount;
            }
        })
        .catch(error => console.error('Error refreshing stats:', error));
}

let refreshInterval = setInterval(refreshDashboardStats, 30000);

document.addEventListener('visibilitychange', function() {
    if (document.hidden) {
        clearInterval(refreshInterval);
    } else {
        refreshInterval = setInterval(refreshDashboardStats, 30000);
        refreshDashboardStats();
    }
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\wamp64\www\unileave\resources\views/shared/dashboard.blade.php ENDPATH**/ ?>