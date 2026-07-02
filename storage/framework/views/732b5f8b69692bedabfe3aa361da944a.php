

<?php $__env->startSection('title', 'Edit Department'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-2">
    
    <!-- Breadcrumb -->
    <?php if (isset($component)) { $__componentOriginale19f62b34dfe0bfdf95075badcb45bc2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale19f62b34dfe0bfdf95075badcb45bc2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.breadcrumb','data' => ['items' => [
        ['name' => 'Departments', 'url' => route('admin.departments.index')],
        ['name' => 'Edit Department', 'url' => '#']
    ]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('breadcrumb'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['items' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
        ['name' => 'Departments', 'url' => route('admin.departments.index')],
        ['name' => 'Edit Department', 'url' => '#']
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'EDIT DEPARTMENT','description' => 'Update department information']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'EDIT DEPARTMENT','description' => 'Update department information']); ?>
        <a href="<?php echo e(route('admin.departments.index')); ?>" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
            <i class="ti ti-arrow-left me-1"></i> Back
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
    
    <div class="row g-2">
        <!-- Main Form -->
        <div class="col-lg-7">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-folder text-primary me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Edit Department: <?php echo e($department->name); ?></span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <form action="<?php echo e(route('admin.departments.update', $department->id)); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PUT'); ?>
                        
                        <div class="mb-1">
                            <input type="hidden" name="campus_id" value="<?php echo e($department->campus_id); ?>">
                            <label class="form-label" style="font-size: 0.65rem;">School/Faculty <span class="text-danger">*</span></label>
                            <select name="school_id" class="form-select form-select-sm rounded-sm <?php $__errorArgs = ['school_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;" required>
                                <option value="">Select School/Faculty</option>
                                <?php $__currentLoopData = $schools; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $school): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($school->id); ?>" <?php echo e(old('school_id', $department->school_id) == $school->id ? 'selected' : ''); ?>>
                                    <?php echo e($school->name); ?> (<?php echo e($school->campus->name ?? 'N/A'); ?>)
                                </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <?php $__errorArgs = ['school_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback" style="font-size: 0.65rem;"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        
                        <div class="row g-1">
                            <div class="col-md-6 mb-1">
                                <label class="form-label" style="font-size: 0.65rem;">Department Name <span class="text-danger">*</span></label>
                                <input type="text" 
                                       name="name" 
                                       class="form-control form-control-sm rounded-sm <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                       value="<?php echo e(old('name', $department->name)); ?>" 
                                       style="font-size: 0.7rem; padding: 0.2rem 0.3rem;"
                                       required>
                                <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback" style="font-size: 0.65rem;"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="col-md-6 mb-1">
                                <label class="form-label" style="font-size: 0.65rem;">Code <span class="text-danger">*</span></label>
                                <input type="text" 
                                       name="code" 
                                       class="form-control form-control-sm rounded-sm <?php $__errorArgs = ['code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                       value="<?php echo e(old('code', $department->code)); ?>" 
                                       style="font-size: 0.7rem; padding: 0.2rem 0.3rem;"
                                       required>
                                <?php $__errorArgs = ['code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback" style="font-size: 0.65rem;"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>
                        
                        <div class="mb-1">
                            <label class="form-label" style="font-size: 0.65rem;">Head of Department (HOD)</label>
                            <select name="hod_staff_id" class="form-select form-select-sm rounded-sm <?php $__errorArgs = ['hod_staff_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;">
                                <option value="">Select HOD</option>
                                <?php $__currentLoopData = $hodCandidates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $hod): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($hod->id); ?>" <?php echo e(old('hod_staff_id', $department->hod_staff_id) == $hod->id ? 'selected' : ''); ?>>
                                    <?php echo e($hod->title ? $hod->title . ' ' : ''); ?><?php echo e($hod->first_name); ?> <?php echo e($hod->last_name); ?> (<?php echo e($hod->staff_id); ?>)
                                </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <?php $__errorArgs = ['hod_staff_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback" style="font-size: 0.65rem;"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            <small class="text-muted" style="font-size: 0.6rem;">Academic staff member who heads this department</small>
                        </div>
                        
                        <!-- ✅ ADDED: Unit Head Selection (for non-academic staff) -->
                        <div class="mb-1">
                            <label class="form-label" style="font-size: 0.65rem;">Unit Head</label>
                            <select name="unit_head_staff_id" class="form-select form-select-sm rounded-sm <?php $__errorArgs = ['unit_head_staff_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;">
                                <option value="">Select Unit Head</option>
                                <?php $__currentLoopData = $unitHeadCandidates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $unitHead): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($unitHead->id); ?>" <?php echo e(old('unit_head_staff_id', $department->unit_head_staff_id) == $unitHead->id ? 'selected' : ''); ?>>
                                    <?php echo e($unitHead->title ? $unitHead->title . ' ' : ''); ?><?php echo e($unitHead->first_name); ?> <?php echo e($unitHead->last_name); ?> (<?php echo e($unitHead->staff_id); ?>) - <?php echo e(ucfirst($unitHead->staff_category)); ?>

                                </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <?php $__errorArgs = ['unit_head_staff_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback" style="font-size: 0.65rem;"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            <small class="text-muted" style="font-size: 0.6rem;">Staff member who heads this unit (for non-academic departments)</small>
                        </div>
                        
                        <div class="mb-1">
                            <label class="form-label" style="font-size: 0.65rem;">Description</label>
                            <textarea name="description" 
                                      class="form-control form-control-sm rounded-sm <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                      rows="2"
                                      style="font-size: 0.7rem; padding: 0.2rem 0.3rem;"><?php echo e(old('description', $department->description)); ?></textarea>
                            <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback" style="font-size: 0.65rem;"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        
                        <div class="mb-1">
                            <label class="form-label" style="font-size: 0.65rem;">Status</label>
                            <select name="status" class="form-select form-select-sm rounded-sm <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;">
                                <option value="active" <?php echo e(old('status', $department->status) == 'active' ? 'selected' : ''); ?>>Active</option>
                                <option value="inactive" <?php echo e(old('status', $department->status) == 'inactive' ? 'selected' : ''); ?>>Inactive</option>
                            </select>
                            <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback" style="font-size: 0.65rem;"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        
                        <div class="d-flex justify-content-end gap-1 mt-2">
                            <a href="<?php echo e(route('admin.departments.index')); ?>" class="btn btn-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.25rem 0.5rem;">Cancel</a>
                            <button type="submit" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.25rem 0.5rem;">
                                <i class="ti ti-device-floppy me-1"></i> Update Department
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Right Column: Statistics & Info -->
        <div class="col-lg-5">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-chart-bar text-success me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Department Statistics</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="fw-bold text-info" style="font-size: 1rem;"><?php echo e($department->staff->count()); ?></div>
                            <div class="text-muted" style="font-size: 0.6rem;">Staff Member(s)</div>
                        </div>
                        <div class="col-6">
                            <div class="fw-bold text-primary" style="font-size: 0.9rem;"><?php echo e($department->school->name ?? 'N/A'); ?></div>
                            <div class="text-muted" style="font-size: 0.6rem;">Parent School</div>
                        </div>
                    </div>
                    <hr class="my-1">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="fw-bold text-success" style="font-size: 0.85rem;">
                                <?php echo e($department->hod ? $department->hod->full_name : 'Not Assigned'); ?>

                            </div>
                            <div class="text-muted" style="font-size: 0.55rem;">Current HOD</div>
                        </div>
                        <div class="col-6">
                            <div class="fw-bold text-info" style="font-size: 0.85rem;">
                                <?php echo e($department->unitHead ? $department->unitHead->full_name : 'Not Assigned'); ?>

                            </div>
                            <div class="text-muted" style="font-size: 0.55rem;">Current Unit Head</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card rounded-sm mt-2">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-info-circle text-info me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Department Information</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <div class="alert alert-light border mb-0 py-1 px-2 rounded-sm">
                        <div class="d-flex align-items-start gap-1">
                            <i class="ti ti-tips text-warning mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">About this Department</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">• Created: <?php echo e($department->created_at->format('d M Y')); ?></small><br>
                                <small class="text-muted" style="font-size: 0.6rem;">• Last updated: <?php echo e($department->updated_at->format('d M Y')); ?></small><br>
                                <small class="text-muted" style="font-size: 0.6rem;">• HOD reviews academic staff leave requests</small><br>
                                <small class="text-muted" style="font-size: 0.6rem;">• Unit Head reviews non-academic staff leave requests</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\wamp64\www\unileave\resources\views/admin/departments/edit.blade.php ENDPATH**/ ?>