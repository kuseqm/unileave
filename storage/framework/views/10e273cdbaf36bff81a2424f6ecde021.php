

<?php $__env->startSection('title', 'Staff Details'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-2">
    
    <!-- Breadcrumb -->
    <?php if (isset($component)) { $__componentOriginale19f62b34dfe0bfdf95075badcb45bc2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale19f62b34dfe0bfdf95075badcb45bc2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.breadcrumb','data' => ['items' => [
        ['name' => 'Staff', 'url' => route('staff.index')],
        ['name' => 'Staff Details', 'url' => '#']
    ]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('breadcrumb'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['items' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
        ['name' => 'Staff', 'url' => route('staff.index')],
        ['name' => 'Staff Details', 'url' => '#']
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
    <div class="card rounded-sm mb-3">
        <div class="card-body py-2 px-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h1 class="h5 mb-0" style="color: #3B82F6; font-weight: 700;">STAFF DETAILS</h1>
                    <p class="text-muted mb-0" style="font-size: 0.7rem;">View complete staff information</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="<?php echo e(route('staff.index')); ?>" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
                        <i class="ti ti-arrow-left me-1"></i> Back
                    </a>
                    <a href="<?php echo e(route('staff.edit', $staff->id)); ?>" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
                        <i class="ti ti-edit me-1"></i> Edit Staff
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <?php if(session('success')): ?>
    <div class="alert alert-success alert-dismissible fade show py-1 px-2 mb-2 rounded-sm" style="font-size: 0.7rem;" role="alert">
        <i class="ti ti-check-circle me-1"></i> <?php echo e(session('success')); ?>

        <button type="button" class="btn-close py-0" style="font-size: 0.6rem;" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>
    
    <div class="row">
        <!-- Left Column - Profile Picture & Quick Info -->
        <div class="col-md-3">
            <!-- Profile Picture Card -->
            <div class="card rounded-sm mb-2">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-photo text-primary me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Profile Picture</span>
                    </div>
                </div>
                <div class="card-body text-center py-3">
                    <div class="user-avatar-large mx-auto mb-2" style="width: 120px; height: 120px; background: #EFF6FF; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto; overflow: hidden;">
                        <?php if($staff->avatar && Storage::disk('public')->exists($staff->avatar)): ?>
                            <img src="<?php echo e(Storage::url($staff->avatar)); ?>" style="width: 120px; height: 120px; border-radius: 50%; object-fit: cover;">
                        <?php else: ?>
                            <span style="font-size: 2.5rem; font-weight: 600; color: #3B82F6;">
                                <?php echo e(strtoupper(substr($staff->first_name, 0, 1))); ?>

                                <?php if($staff->middle_name): ?>
                                    <?php echo e(strtoupper(substr($staff->middle_name, 0, 1))); ?>

                                <?php endif; ?>
                                <?php echo e(strtoupper(substr($staff->last_name, 0, 1))); ?>

                            </span>
                        <?php endif; ?>
                    </div>
                    <h6 class="mb-0" style="font-size: 0.85rem;"><?php echo e($staff->title ? $staff->title . ' ' : ''); ?><?php echo e($staff->first_name); ?> <?php echo e($staff->last_name); ?></h6>
                    <p class="text-muted mb-2" style="font-size: 0.65rem;"><?php echo e($staff->job_title ?? 'Staff Member'); ?></p>
                    <hr class="my-2">
                    <div class="text-start">
                        <div class="mb-1" style="font-size: 0.65rem;">
                            <i class="ti ti-id me-1"></i> <strong>Staff ID:</strong> <?php echo e($staff->staff_id); ?>

                        </div>
                        <div class="mb-1" style="font-size: 0.65rem;">
                            <i class="ti ti-building me-1"></i> <strong>Department:</strong> <?php echo e($staff->department->name ?? 'N/A'); ?>

                        </div>
                        <div class="mb-1" style="font-size: 0.65rem;">
                            <i class="ti ti-mail me-1"></i> <strong>Email:</strong> <?php echo e($staff->email); ?>

                        </div>
                        <div style="font-size: 0.65rem;">
                            <i class="ti ti-phone me-1"></i> <strong>Phone:</strong> <?php echo e($staff->phone ?? 'N/A'); ?>

                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Quick Stats Card -->
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-chart-bar text-primary me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Quick Statistics</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <div class="row text-center">
                        <div class="col-6 mb-2">
                            <div class="fw-bold text-primary" style="font-size: 1.2rem;"><?php echo e($totalLeaveRequests ?? 0); ?></div>
                            <div class="text-muted" style="font-size: 0.6rem;">Total Leave</div>
                        </div>
                        <div class="col-6 mb-2">
                            <div class="fw-bold text-success" style="font-size: 1.2rem;"><?php echo e($approvedLeave ?? 0); ?></div>
                            <div class="text-muted" style="font-size: 0.6rem;">Approved</div>
                        </div>
                        <div class="col-6">
                            <div class="fw-bold text-warning" style="font-size: 1.2rem;"><?php echo e($pendingLeave ?? 0); ?></div>
                            <div class="text-muted" style="font-size: 0.6rem;">Pending</div>
                        </div>
                        <div class="col-6">
                            <div class="fw-bold text-secondary" style="font-size: 1.2rem;"><?php echo e($staff->qualifications->count() ?? 0); ?></div>
                            <div class="text-muted" style="font-size: 0.6rem;">Qualifications</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Right Column - Tabs -->
        <div class="col-md-9">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <ul class="nav nav-tabs card-header-tabs" id="staffDetailsTabs" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab">
                                <i class="ti ti-user me-1"></i> Profile
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" id="employment-tab" data-bs-toggle="tab" data-bs-target="#employment" type="button" role="tab">
                                <i class="ti ti-briefcase me-1"></i> Employment
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" id="qualifications-tab" data-bs-toggle="tab" data-bs-target="#qualifications" type="button" role="tab">
                                <i class="ti ti-certificate me-1"></i> Qualifications
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" id="skills-tab" data-bs-toggle="tab" data-bs-target="#skills" type="button" role="tab">
                                <i class="ti ti-code me-1"></i> Skills & Languages
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" id="contact-tab" data-bs-toggle="tab" data-bs-target="#contact" type="button" role="tab">
                                <i class="ti ti-phone me-1"></i> Contact
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" id="documents-tab" data-bs-toggle="tab" data-bs-target="#documents" type="button" role="tab">
                                <i class="ti ti-files me-1"></i> Documents
                            </button>
                        </li>
                    </ul>
                </div>
                <div class="card-body p-2">
                    <div class="tab-content">
                        
                        <!-- ============================================ -->
                        <!-- TAB 1: PROFILE (Personal Information) -->
                        <!-- ============================================ -->
                        <div class="tab-pane fade show active" id="profile" role="tabpanel">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card rounded-sm mb-2">
                                        <div class="card-header py-1 px-2 bg-light">
                                            <i class="ti ti-info-circle text-primary me-1" style="font-size: 0.7rem;"></i>
                                            <span style="font-size: 0.7rem; font-weight: 600;">Personal Information</span>
                                        </div>
                                        <div class="card-body py-2 px-2">
                                            <table class="table table-sm mb-0" style="font-size: 0.7rem;">
                                                <tr><td width="35%"><strong>Full Name</strong></td><td><?php echo e($staff->title ? $staff->title . ' ' : ''); ?><?php echo e($staff->first_name); ?> <?php echo e($staff->middle_name ? $staff->middle_name . ' ' : ''); ?><?php echo e($staff->last_name); ?></td></tr>
                                                <tr><td><strong>Staff ID</strong></td><td><?php echo e($staff->staff_id); ?></td></tr>
                                                <tr><td><strong>Gender</strong></td><td><?php echo e(ucfirst($staff->gender ?? 'N/A')); ?></td></tr>
                                                <tr><td><strong>Date of Birth</strong></td><td><?php echo e($staff->date_of_birth ? $staff->date_of_birth->format('d M Y') : 'N/A'); ?> <?php if($staff->date_of_birth): ?> <span class="text-muted">(<?php echo e($staff->age ?? '?'); ?> years)</span> <?php endif; ?></td></tr>
                                                <tr><td><strong>National ID</strong></td><td><?php echo e($staff->national_id ?? 'N/A'); ?></td></tr>
                                                <tr><td><strong>Marital Status</strong></td><td><?php echo e(ucfirst($staff->marital_status ?? 'N/A')); ?></td></tr>
                                                <tr><td><strong>Spouse Name</strong></td><td><?php echo e($staff->spouse_name ?? 'N/A'); ?></td></tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card rounded-sm mb-2">
                                        <div class="card-header py-1 px-2 bg-light">
                                            <i class="ti ti-location-pin text-primary me-1" style="font-size: 0.7rem;"></i>
                                            <span style="font-size: 0.7rem; font-weight: 600;">Location Assignment</span>
                                        </div>
                                        <div class="card-body py-2 px-2">
                                            <table class="table table-sm mb-0" style="font-size: 0.7rem;">
                                                <tr><td width="35%"><strong>University</strong></td><td><?php echo e($staff->university->name ?? 'N/A'); ?></td></tr>
                                                <tr><td><strong>Campus</strong></td><td><?php echo e($staff->campus->name ?? 'N/A'); ?></td></tr>
                                                <tr><td><strong>School/Faculty</strong></td><td><?php echo e($staff->school->name ?? 'N/A'); ?></td></tr>
                                                <tr><td><strong>Department</strong></td><td><?php echo e($staff->department->name ?? 'N/A'); ?></td></tr>
                                                <tr><td><strong>Role</strong></td><td><span class="badge bg-primary-subtle text-primary rounded-sm"><?php echo e(ucfirst($staffRole ?? 'N/A')); ?></span></td></tr>
                                                <tr><td><strong>Status</strong></td><td><?php if($staff->status == 'active'): ?> <span class="badge bg-success-subtle text-success rounded-sm">Active</span> <?php elseif($staff->status == 'inactive'): ?> <span class="badge bg-warning-subtle text-warning rounded-sm">Inactive</span> <?php else: ?> <span class="badge bg-danger-subtle text-danger rounded-sm">Suspended</span> <?php endif; ?></td></tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Dependents Section -->
                            <div class="card rounded-sm">
                                <div class="card-header py-1 px-2 bg-light">
                                    <i class="ti ti-users text-primary me-1" style="font-size: 0.7rem;"></i>
                                    <span style="font-size: 0.7rem; font-weight: 600;">Dependents</span>
                                </div>
                                <div class="card-body p-0">
                                    <?php if($staff->dependents && $staff->dependents->count() > 0): ?>
                                    <div class="table-responsive">
                                        <table class="table table-sm mb-0" style="font-size: 0.7rem;">
                                            <thead class="bg-light">
                                                <tr><th class="ps-2 py-1">Name</th><th>Relationship</th><th>Date of Birth</th><th>Phone</th><th>Emergency</th><th>Beneficiary</th></tr>
                                            </thead>
                                            <tbody>
                                                <?php $__currentLoopData = $staff->dependents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dependent): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <tr><td class="ps-2 py-1"><?php echo e($dependent->name); ?></td><td><?php echo e(ucfirst($dependent->relationship)); ?></td><td><?php echo e($dependent->date_of_birth ? \Carbon\Carbon::parse($dependent->date_of_birth)->format('d M Y') : '-'); ?></td><td><?php echo e($dependent->phone ?? '-'); ?></td><td><?php if($dependent->is_emergency_contact): ?><span class="badge bg-success-subtle text-success">Yes</span><?php else: ?> No <?php endif; ?></td><td><?php if($dependent->is_beneficiary): ?><span class="badge bg-info-subtle text-info">Yes</span><?php else: ?> No <?php endif; ?></td></tr>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <?php else: ?>
                                    <p class="text-muted text-center py-2 mb-0" style="font-size: 0.7rem;">No dependents recorded.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        
                        <!-- ============================================ -->
                        <!-- TAB 2: EMPLOYMENT -->
                        <!-- ============================================ -->
                        <div class="tab-pane fade" id="employment" role="tabpanel">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card rounded-sm mb-2">
                                        <div class="card-header py-1 px-2 bg-light">
                                            <i class="ti ti-briefcase text-primary me-1" style="font-size: 0.7rem;"></i>
                                            <span style="font-size: 0.7rem; font-weight: 600;">Employment Details</span>
                                        </div>
                                        <div class="card-body py-2 px-2">
                                            <table class="table table-sm mb-0" style="font-size: 0.7rem;">
                                                <tr><td width="35%"><strong>Job Title</strong></td><td><?php echo e($staff->job_title ?? 'N/A'); ?></td></tr>
                                                <tr><td><strong>Job Grade</strong></td><td><?php echo e($staff->job_grade ?? 'N/A'); ?></td></tr>
                                                <tr><td><strong>Staff Category</strong></td><td><?php echo e(ucfirst($staff->staff_category ?? 'N/A')); ?></td></tr>
                                                <tr><td><strong>Joining Date</strong></td><td><?php echo e($staff->joining_date ? \Carbon\Carbon::parse($staff->joining_date)->format('d M Y') : 'N/A'); ?></td></tr>
                                                <tr><td><strong>Reporting To</strong></td><td><?php echo e($staff->reportsTo->full_name ?? 'N/A'); ?></td></tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card rounded-sm mb-2">
                                        <div class="card-header py-1 px-2 bg-light">
                                            <i class="ti ti-file-text text-primary me-1" style="font-size: 0.7rem;"></i>
                                            <span style="font-size: 0.7rem; font-weight: 600;">Contract Information</span>
                                        </div>
                                        <div class="card-body py-2 px-2">
                                            <table class="table table-sm mb-0" style="font-size: 0.7rem;">
                                                <tr><td width="35%"><strong>Contract Type</strong></td><td><?php echo e(ucfirst($staff->contract_type ?? 'N/A')); ?></td></tr>
                                                <tr><td><strong>Contract End Date</strong></td><td><?php echo e($staff->contract_end_date ? \Carbon\Carbon::parse($staff->contract_end_date)->format('d M Y') : 'N/A'); ?></td></tr>
                                                <tr><td><strong>TIN Number</strong></td><td><?php echo e($staff->tin_number ?? 'N/A'); ?></td></tr>
                                                <tr><td><strong>NSSF Number</strong></td><td><?php echo e($staff->nssf_number ?? 'N/A'); ?></td></tr>
                                                <tr><td><strong>Highest Qualification</strong></td><td><?php echo e($staff->qualification ?? 'N/A'); ?></td></tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Work Experience -->
                            <div class="card rounded-sm">
                                <div class="card-header py-1 px-2 bg-light">
                                    <i class="ti ti-history text-primary me-1" style="font-size: 0.7rem;"></i>
                                    <span style="font-size: 0.7rem; font-weight: 600;">Work Experience</span>
                                </div>
                                <div class="card-body p-0">
                                    <?php if($staff->workExperience && $staff->workExperience->count() > 0): ?>
                                    <div class="table-responsive">
                                        <table class="table table-sm mb-0" style="font-size: 0.7rem;">
                                            <thead class="bg-light">
                                                <tr><th class="ps-2 py-1">Employer</th><th>Job Title</th><th>Start Date</th><th>End Date</th><th>Duration</th></tr>
                                            </thead>
                                            <tbody>
                                                <?php $__currentLoopData = $staff->workExperience; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $exp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <tr><td class="ps-2 py-1"><?php echo e($exp->employer_name); ?></td><td><?php echo e($exp->job_title ?? '-'); ?></td><td><?php echo e(\Carbon\Carbon::parse($exp->start_date)->format('M Y')); ?></td><td><?php echo e($exp->is_current ? 'Present' : ($exp->end_date ? \Carbon\Carbon::parse($exp->end_date)->format('M Y') : '-')); ?></td><td><?php echo e(\Carbon\Carbon::parse($exp->start_date)->diffInYears($exp->end_date ?? now())); ?> years</td></tr>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <?php else: ?>
                                    <p class="text-muted text-center py-2 mb-0" style="font-size: 0.7rem;">No work experience recorded.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        
                        <!-- ============================================ -->
                        <!-- TAB 3: QUALIFICATIONS & CERTIFICATIONS -->
                        <!-- ============================================ -->
                        <div class="tab-pane fade" id="qualifications" role="tabpanel">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card rounded-sm mb-2">
                                        <div class="card-header py-1 px-2 bg-light">
                                            <i class="ti ti-certificate text-primary me-1" style="font-size: 0.7rem;"></i>
                                            <span style="font-size: 0.7rem; font-weight: 600;">Academic Qualifications</span>
                                        </div>
                                        <div class="card-body p-0">
                                            <?php if($staff->qualifications && $staff->qualifications->count() > 0): ?>
                                            <div class="table-responsive">
                                                <table class="table table-sm mb-0" style="font-size: 0.7rem;">
                                                    <thead class="bg-light">
                                                        <tr><th class="ps-2 py-1">Qualification</th><th>Institution</th><th>Year</th><th>Type</th></tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php $__currentLoopData = $staff->qualifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $qual): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <td><td class="ps-2 py-1"><?php echo e($qual->qualification_name); ?></td><td><?php echo e($qual->institution ?? '-'); ?></td><td><?php echo e($qual->year_obtained ?? '-'); ?></td><td><span class="badge bg-secondary-subtle text-secondary"><?php echo e(strtoupper($qual->qualification_type ?? 'N/A')); ?></span></td></table>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <?php else: ?>
                                            <p class="text-muted text-center py-2 mb-0" style="font-size: 0.7rem;">No qualifications recorded.</p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card rounded-sm mb-2">
                                        <div class="card-header py-1 px-2 bg-light">
                                            <i class="ti ti-award text-primary me-1" style="font-size: 0.7rem;"></i>
                                            <span style="font-size: 0.7rem; font-weight: 600;">Professional Certifications</span>
                                        </div>
                                        <div class="card-body p-0">
                                            <?php if($staff->certifications && $staff->certifications->count() > 0): ?>
                                            <div class="table-responsive">
                                                <table class="table table-sm mb-0" style="font-size: 0.7rem;">
                                                    <thead class="bg-light">
                                                        <tr><th class="ps-2 py-1">Certification</th><th>Issuing Body</th><th>Expiry Date</th></tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php $__currentLoopData = $staff->certifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cert): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <tr><td class="ps-2 py-1"><?php echo e($cert->certification_name); ?></td><td><?php echo e($cert->issuing_body ?? '-'); ?></td><td><?php if($cert->expiry_date): ?><span class="<?php echo e($cert->expiry_date->isPast() ? 'text-danger' : 'text-success'); ?>"><?php echo e($cert->expiry_date->format('d M Y')); ?></span><?php else: ?> - <?php endif; ?></td></tr>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <?php else: ?>
                                            <p class="text-muted text-center py-2 mb-0" style="font-size: 0.7rem;">No certifications recorded.</p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Awards -->
                            <div class="card rounded-sm">
                                <div class="card-header py-1 px-2 bg-light">
                                    <i class="ti ti-star text-primary me-1" style="font-size: 0.7rem;"></i>
                                    <span style="font-size: 0.7rem; font-weight: 600;">Awards & Recognitions</span>
                                </div>
                                <div class="card-body p-0">
                                    <?php if($staff->awards && $staff->awards->count() > 0): ?>
                                    <div class="table-responsive">
                                        <table class="table table-sm mb-0" style="font-size: 0.7rem;">
                                            <thead class="bg-light">
                                                <tr><th class="ps-2 py-1">Award Name</th><th>Awarding Body</th><th>Date</th><th>Level</th></tr>
                                            </thead>
                                            <tbody>
                                                <?php $__currentLoopData = $staff->awards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $award): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <tr><td class="ps-2 py-1"><?php echo e($award->award_name); ?></td><td><?php echo e($award->awarding_body ?? '-'); ?></td><td><?php echo e($award->award_date ? \Carbon\Carbon::parse($award->award_date)->format('d M Y') : '-'); ?></td><td><span class="badge bg-warning-subtle text-warning"><?php echo e(ucfirst($award->award_level ?? 'N/A')); ?></span></td></tr>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <?php else: ?>
                                    <p class="text-muted text-center py-2 mb-0" style="font-size: 0.7rem;">No awards recorded.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        
                        <!-- ============================================ -->
                        <!-- TAB 4: SKILLS & LANGUAGES -->
                        <!-- ============================================ -->
                        <div class="tab-pane fade" id="skills" role="tabpanel">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card rounded-sm mb-2">
                                        <div class="card-header py-1 px-2 bg-light">
                                            <i class="ti ti-code text-primary me-1" style="font-size: 0.7rem;"></i>
                                            <span style="font-size: 0.7rem; font-weight: 600;">Skills</span>
                                        </div>
                                        <div class="card-body p-0">
                                            <?php if($staff->skills && $staff->skills->count() > 0): ?>
                                            <div class="table-responsive">
                                                <table class="table table-sm mb-0" style="font-size: 0.7rem;">
                                                    <thead class="bg-light">
                                                        <tr><th class="ps-2 py-1">Skill</th><th>Category</th><th>Proficiency</th></tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php $__currentLoopData = $staff->skills; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $skill): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <tr><td class="ps-2 py-1"><?php echo e($skill->skill_name); ?></td><td><?php echo e(ucfirst($skill->skill_category ?? 'Other')); ?></td><td><span class="badge bg-info-subtle text-info"><?php echo e(ucfirst($skill->proficiency_level)); ?></span></td></tr>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <?php else: ?>
                                            <p class="text-muted text-center py-2 mb-0" style="font-size: 0.7rem;">No skills recorded.</p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card rounded-sm mb-2">
                                        <div class="card-header py-1 px-2 bg-light">
                                            <i class="ti ti-language text-primary me-1" style="font-size: 0.7rem;"></i>
                                            <span style="font-size: 0.7rem; font-weight: 600;">Languages</span>
                                        </div>
                                        <div class="card-body p-0">
                                            <?php if($staff->languages && $staff->languages->count() > 0): ?>
                                            <div class="table-responsive">
                                                <table class="table table-sm mb-0" style="font-size: 0.7rem;">
                                                    <thead class="bg-light">
                                                        <tr><th class="ps-2 py-1">Language</th><th>Speak</th><th>Read</th><th>Write</th></tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php $__currentLoopData = $staff->languages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lang): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <tr><td class="ps-2 py-1"><?php echo e($lang->language_name); ?></td><td><?php echo e(ucfirst($lang->proficiency_speak)); ?></td><td><?php echo e(ucfirst($lang->proficiency_read)); ?></td><td><?php echo e(ucfirst($lang->proficiency_write)); ?></td></tr>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <?php else: ?>
                                            <p class="text-muted text-center py-2 mb-0" style="font-size: 0.7rem;">No languages recorded.</p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Professional Memberships -->
                            <div class="card rounded-sm">
                                <div class="card-header py-1 px-2 bg-light">
                                    <i class="ti ti-users text-primary me-1" style="font-size: 0.7rem;"></i>
                                    <span style="font-size: 0.7rem; font-weight: 600;">Professional Memberships</span>
                                </div>
                                <div class="card-body p-0">
                                    <?php if($staff->memberships && $staff->memberships->count() > 0): ?>
                                    <div class="table-responsive">
                                        <table class="table table-sm mb-0" style="font-size: 0.7rem;">
                                            <thead class="bg-light">
                                                <tr><th class="ps-2 py-1">Organization</th><th>Membership No.</th><th>Valid To</th></tr>
                                            </thead>
                                            <tbody>
                                                <?php $__currentLoopData = $staff->memberships; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $membership): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <tr><td class="ps-2 py-1"><?php echo e($membership->organization_name); ?></td><td><?php echo e($membership->membership_number ?? '-'); ?></td><td><?php if($membership->valid_to): ?><span class="<?php echo e($membership->valid_to->isPast() ? 'text-danger' : 'text-success'); ?>"><?php echo e($membership->valid_to->format('d M Y')); ?></span><?php else: ?> - <?php endif; ?></td></tr>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <?php else: ?>
                                    <p class="text-muted text-center py-2 mb-0" style="font-size: 0.7rem;">No professional memberships recorded.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        
                        <!-- ============================================ -->
                        <!-- TAB 5: CONTACT (Phones, Emails, Addresses) -->
                        <!-- ============================================ -->
                        <div class="tab-pane fade" id="contact" role="tabpanel">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card rounded-sm mb-2">
                                        <div class="card-header py-1 px-2 bg-light">
                                            <i class="ti ti-phone text-primary me-1" style="font-size: 0.7rem;"></i>
                                            <span style="font-size: 0.7rem; font-weight: 600;">Phone Numbers</span>
                                        </div>
                                        <div class="card-body p-0">
                                            <?php if($staff->phones && $staff->phones->count() > 0): ?>
                                            <div class="table-responsive">
                                                <table class="table table-sm mb-0" style="font-size: 0.7rem;">
                                                    <thead class="bg-light">
                                                        <tr><th class="ps-2 py-1">Number</th><th>Type</th><th>Primary</th></tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php $__currentLoopData = $staff->phones; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $phone): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <tr><td class="ps-2 py-1"><?php echo e($phone->phone_number); ?></td><td><?php echo e(ucfirst($phone->phone_type)); ?></td><td><?php if($phone->is_primary): ?><span class="badge bg-success-subtle text-success">Primary</span><?php endif; ?></td></tr>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <?php else: ?>
                                            <p class="text-muted text-center py-2 mb-0" style="font-size: 0.7rem;">No phone numbers recorded.</p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="card rounded-sm">
                                        <div class="card-header py-1 px-2 bg-light">
                                            <i class="ti ti-mail text-primary me-1" style="font-size: 0.7rem;"></i>
                                            <span style="font-size: 0.7rem; font-weight: 600;">Email Addresses</span>
                                        </div>
                                        <div class="card-body p-0">
                                            <?php if($staff->emails && $staff->emails->count() > 0): ?>
                                            <div class="table-responsive">
                                                <table class="table table-sm mb-0" style="font-size: 0.7rem;">
                                                    <thead class="bg-light">
                                                        <tr><th class="ps-2 py-1">Email</th><th>Type</th><th>Primary</th></tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php $__currentLoopData = $staff->emails; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $email): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <tr><td class="ps-2 py-1"><?php echo e($email->email); ?></td><td><?php echo e(ucfirst($email->email_type)); ?></td><td><?php if($email->is_primary): ?><span class="badge bg-success-subtle text-success">Primary</span><?php endif; ?></td></tr>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <?php else: ?>
                                            <p class="text-muted text-center py-2 mb-0" style="font-size: 0.7rem;">No email addresses recorded.</p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card rounded-sm mb-2">
                                        <div class="card-header py-1 px-2 bg-light">
                                            <i class="ti ti-home text-primary me-1" style="font-size: 0.7rem;"></i>
                                            <span style="font-size: 0.7rem; font-weight: 600;">Addresses</span>
                                        </div>
                                        <div class="card-body p-0">
                                            <?php if($staff->addresses && $staff->addresses->count() > 0): ?>
                                            <div class="table-responsive">
                                                <table class="table table-sm mb-0" style="font-size: 0.7rem;">
                                                    <thead class="bg-light">
                                                        <tr><th class="ps-2 py-1">Type</th><th>Address</th><th>City</th><th>Primary</th></tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php $__currentLoopData = $staff->addresses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $address): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <tr><td class="ps-2 py-1"><?php echo e(ucfirst($address->address_type)); ?></td><td><?php echo e(Str::limit($address->address ?? '-', 30)); ?></td><td><?php echo e($address->city ?? '-'); ?></td><td><?php if($address->is_primary): ?><span class="badge bg-success-subtle text-success">Primary</span><?php endif; ?></td></tr>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <?php else: ?>
                                            <p class="text-muted text-center py-2 mb-0" style="font-size: 0.7rem;">No addresses recorded.</p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="card rounded-sm">
                                        <div class="card-header py-1 px-2 bg-light">
                                            <i class="ti ti-phone-call text-primary me-1" style="font-size: 0.7rem;"></i>
                                            <span style="font-size: 0.7rem; font-weight: 600;">Emergency Contacts</span>
                                        </div>
                                        <div class="card-body p-0">
                                            <?php if($staff->emergencyContacts && $staff->emergencyContacts->count() > 0): ?>
                                            <div class="table-responsive">
                                                <table class="table table-sm mb-0" style="font-size: 0.7rem;">
                                                    <thead class="bg-light">
                                                        <tr><th class="ps-2 py-1">Name</th><th>Relationship</th><th>Phone</th><th>Primary</th></tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php $__currentLoopData = $staff->emergencyContacts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $contact): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <tr><td class="ps-2 py-1"><?php echo e($contact->name); ?></td><td><?php echo e($contact->relationship); ?></td><td><?php echo e($contact->phone); ?></td><td><?php if($contact->is_primary): ?><span class="badge bg-success-subtle text-success">Primary</span><?php endif; ?></td></td>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <?php else: ?>
                                            <p class="text-muted text-center py-2 mb-0" style="font-size: 0.7rem;">No emergency contacts recorded.</p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- ============================================ -->
                        <!-- TAB 6: DOCUMENTS & BANK DETAILS -->
                        <!-- ============================================ -->
                        <div class="tab-pane fade" id="documents" role="tabpanel">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card rounded-sm mb-2">
                                        <div class="card-header py-1 px-2 bg-light">
                                            <i class="ti ti-bank text-primary me-1" style="font-size: 0.7rem;"></i>
                                            <span style="font-size: 0.7rem; font-weight: 600;">Bank Details</span>
                                        </div>
                                        <div class="card-body py-2 px-2">
                                            <?php if($staff->bankDetails && $staff->bankDetails->count() > 0): ?>
                                                <?php $__currentLoopData = $staff->bankDetails; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bank): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <div class="<?php echo e(!$loop->last ? 'border-bottom mb-2 pb-2' : ''); ?>">
                                                        <?php if($staff->bankDetails->count() > 1): ?>
                                                            <div class="fw-semibold mb-1" style="font-size: 0.65rem;">Account <?php echo e($loop->iteration); ?></div>
                                                        <?php endif; ?>
                                                        <table class="table table-sm mb-0" style="font-size: 0.7rem;">
                                                            <tr>
                                                                <td width="35%"><strong>Bank Name</strong></td>
                                                                <td><?php echo e($bank->bank_name ?? 'N/A'); ?></td>
                                                            </tr>
                                                            <tr>
                                                                <td><strong>Account Name</strong></td>
                                                                <td><?php echo e($bank->account_name ?? 'N/A'); ?></td>
                                                            </tr>
                                                            <tr>
                                                                <td><strong>Account Number</strong></td>
                                                                <td><?php echo e($bank->account_number ?? 'N/A'); ?></td>
                                                            </tr>
                                                            <tr>
                                                                <td><strong>TIN Number</strong></td>
                                                                <td><?php echo e($bank->tin_number ?? 'N/A'); ?></td>
                                                            </tr>
                                                        </table>
                                                        <?php if($bank->is_primary): ?>
                                                            <div class="mt-1">
                                                                <span class="badge bg-primary" style="font-size: 0.55rem;">Primary Account</span>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            <?php else: ?>
                                                <p class="text-muted text-center py-2 mb-0" style="font-size: 0.7rem;">No bank details recorded.</p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card rounded-sm mb-2">
                                        <div class="card-header py-1 px-2 bg-light">
                                            <i class="ti ti-key text-primary me-1" style="font-size: 0.7rem;"></i>
                                            <span style="font-size: 0.7rem; font-weight: 600;">Account Information</span>
                                        </div>
                                        <div class="card-body py-2 px-2">
                                            <table class="table table-sm mb-0" style="font-size: 0.7rem;">
                                                <tr><td width="35%"><strong>Account Created</strong></td><td><?php echo e($staff->created_at ? $staff->created_at->format('d M Y, h:i A') : 'N/A'); ?></td></tr>
                                                <tr><td><strong>Last Updated</strong></td><td><?php echo e($staff->updated_at ? $staff->updated_at->format('d M Y, h:i A') : 'N/A'); ?></td></tr>
                                                <tr><td><strong>Last Login</strong></td><td><?php echo e($staff->last_login_at ? \Carbon\Carbon::parse($staff->last_login_at)->format('d M Y, h:i A') : 'Never'); ?><\/td></tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Publications (if academic staff) -->
                            <?php if($staff->publications && $staff->publications->count() > 0): ?>
                            <div class="card rounded-sm">
                                <div class="card-header py-1 px-2 bg-light">
                                    <i class="ti ti-file-text text-primary me-1" style="font-size: 0.7rem;"></i>
                                    <span style="font-size: 0.7rem; font-weight: 600;">Publications</span>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-sm mb-0" style="font-size: 0.7rem;">
                                            <thead class="bg-light">
                                                <tr><th class="ps-2 py-1">Title</th><th>Type</th><th>Journal/Publisher</th><th>Year</th></tr>
                                            </thead>
                                            <tbody>
                                                <?php $__currentLoopData = $staff->publications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pub): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <tr><td class="ps-2 py-1"><?php echo e(Str::limit($pub->publication_title, 40)); ?></td>
                                                <td><?php echo e(ucfirst($pub->publication_type)); ?></td>
                                                <td><?php echo e($pub->journal_name ?? $pub->publisher ?? '-'); ?></td>
                                                <td><?php echo e($pub->publication_year ?? '-'); ?></td>
                                                </tr>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Password Reset Modal -->
<div class="modal fade" id="resetPasswordModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-sm">
            <div class="modal-header py-1 px-2">
                <h5 class="modal-title" style="font-size: 0.8rem;">Confirm Password Reset</h5>
                <button type="button" class="btn-close py-0" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body py-2 px-2">
                <p style="font-size: 0.75rem;">Are you sure you want to reset the password for <strong id="resetStaffName"></strong>?</p>
                <div class="alert alert-info py-1 px-2 rounded-sm" style="font-size: 0.7rem;">
                    <i class="ti ti-info-circle me-1"></i> Temporary password will be: <code>Temp@123</code>
                </div>
            </div>
            <div class="modal-footer py-1 px-2">
                <button type="button" class="btn btn-secondary btn-sm rounded-sm" data-bs-dismiss="modal" style="font-size: 0.7rem;">Cancel</button>
                <a href="#" id="resetConfirmBtn" class="btn btn-warning btn-sm rounded-sm" style="font-size: 0.7rem;">Reset Password</a>
            </div>
        </div>
    </div>
</div>

<script>
function showResetModal(id, name) {
    document.getElementById('resetStaffName').innerText = name;
    document.getElementById('resetConfirmBtn').href = `/staff/${id}/reset-password`;
    new bootstrap.Modal(document.getElementById('resetPasswordModal')).show();
}
</script>

<style>
.rounded-sm { border-radius: 4px; }
.nav-tabs .nav-link { font-size: 0.7rem; padding: 0.4rem 0.8rem; }
.table-sm td, .table-sm th { padding: 0.3rem 0.5rem; }
</style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\wamp64\www\unileave\resources\views/admin/staff/show.blade.php ENDPATH**/ ?>