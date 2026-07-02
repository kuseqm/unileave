

<?php $__env->startSection('title', 'Add Staff'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-2">
    
    <!-- Breadcrumb -->
    <?php if (isset($component)) { $__componentOriginale19f62b34dfe0bfdf95075badcb45bc2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale19f62b34dfe0bfdf95075badcb45bc2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.breadcrumb','data' => ['items' => [
        ['name' => 'Staff', 'url' => route('staff.index')],
        ['name' => 'Add Staff', 'url' => '#']
    ]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('breadcrumb'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['items' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
        ['name' => 'Staff', 'url' => route('staff.index')],
        ['name' => 'Add Staff', 'url' => '#']
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
    
    <!-- Page Header Card -->
    <div class="card rounded-sm mb-3">
        <div class="card-body py-2 px-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h1 class="h5 mb-0" style="color: #3B82F6; font-weight: 700;">ADD STAFF MEMBER</h1>
                    <p class="text-muted mb-0" style="font-size: 0.7rem;">Create a new staff account in the system</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="<?php echo e(route('staff.index')); ?>" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
                        <i class="ti ti-arrow-left me-1"></i> Back
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <?php if(session('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show py-1 px-2 mb-2 rounded-sm" style="font-size: 0.7rem;" role="alert">
        <i class="ti ti-alert-circle me-1"></i> <?php echo e(session('error')); ?>

        <button type="button" class="btn-close py-0" style="font-size: 0.6rem;" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>
    
    <div class="row g-2">
        <!-- Main Form -->
        <div class="col-lg-7">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-user-plus text-primary me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Staff Information</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <form action="<?php echo e(route('staff.store')); ?>" method="POST" id="staffForm">
                        <?php echo csrf_field(); ?>
                        
                        <!-- Name Fields with Title and Gender -->
                        <div class="row g-1">
                            <!-- Title (NEW) -->
                            <div class="col-md-2 mb-1">
                                <label class="form-label" style="font-size: 0.65rem;">Title</label>
                                <select name="title" class="form-select form-select-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;">
                                    <option value="">Select Title</option>
                                    <option value="Mr" <?php echo e(old('title') == 'Mr' ? 'selected' : ''); ?>>Mr</option>
                                    <option value="Mrs" <?php echo e(old('title') == 'Mrs' ? 'selected' : ''); ?>>Mrs</option>
                                    <option value="Ms" <?php echo e(old('title') == 'Ms' ? 'selected' : ''); ?>>Ms</option>
                                    <option value="Dr" <?php echo e(old('title') == 'Dr' ? 'selected' : ''); ?>>Dr</option>
                                    <option value="Prof" <?php echo e(old('title') == 'Prof' ? 'selected' : ''); ?>>Prof</option>
                                    <option value="Eng" <?php echo e(old('title') == 'Eng' ? 'selected' : ''); ?>>Eng</option>
                                    <option value="Rev" <?php echo e(old('title') == 'Rev' ? 'selected' : ''); ?>>Rev</option>
                                </select>
                            </div>
                            
                            <!-- Gender (NEW) -->
                            <div class="col-md-2 mb-1">
                                <label class="form-label" style="font-size: 0.65rem;">Gender</label>
                                <select name="gender" class="form-select form-select-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;">
                                    <option value="">Select Gender</option>
                                    <option value="male" <?php echo e(old('gender') == 'male' ? 'selected' : ''); ?>>Male</option>
                                    <option value="female" <?php echo e(old('gender') == 'female' ? 'selected' : ''); ?>>Female</option>
                                    <option value="other" <?php echo e(old('gender') == 'other' ? 'selected' : ''); ?>>Other</option>
                                </select>
                            </div>
                            
                            <div class="col-md-4 mb-1">
                                <label class="form-label" style="font-size: 0.65rem;">First Name <span class="text-danger">*</span></label>
                                <input type="text" name="first_name" class="form-control form-control-sm rounded-sm <?php $__errorArgs = ['first_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('first_name')); ?>" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;" required>
                                <?php $__errorArgs = ['first_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback" style="font-size: 0.65rem;"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="col-md-4 mb-1">
                                <label class="form-label" style="font-size: 0.65rem;">Middle Name</label>
                                <input type="text" name="middle_name" class="form-control form-control-sm rounded-sm <?php $__errorArgs = ['middle_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('middle_name')); ?>" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;">
                                <?php $__errorArgs = ['middle_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback" style="font-size: 0.65rem;"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="col-md-4 mb-1">
                                <label class="form-label" style="font-size: 0.65rem;">Last Name <span class="text-danger">*</span></label>
                                <input type="text" name="last_name" class="form-control form-control-sm rounded-sm <?php $__errorArgs = ['last_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('last_name')); ?>" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;" required>
                                <?php $__errorArgs = ['last_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback" style="font-size: 0.65rem;"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>
                        
                        <div class="mb-1">
                            <label class="form-label" style="font-size: 0.65rem;">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control form-control-sm rounded-sm <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('email')); ?>" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;" required>
                            <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback" style="font-size: 0.65rem;"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        
                        <div class="row g-1">
                            <div class="col-md-6 mb-1">
                                <label class="form-label" style="font-size: 0.65rem;">Role <span class="text-danger">*</span></label>
                                <select name="role" class="form-select form-select-sm rounded-sm <?php $__errorArgs = ['role'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;" required>
                                    <option value="">Select Role</option>
                                    <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($role); ?>"><?php echo e(ucfirst($role)); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <?php $__errorArgs = ['role'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback" style="font-size: 0.65rem;"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            
                            <div class="col-md-6 mb-1">
                                <label class="form-label" style="font-size: 0.65rem;">Staff Category</label>
                                <input type="text" id="category_display" class="form-control form-control-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.3rem; background: #f5f5f5;" readonly>
                                <small class="text-muted" style="font-size: 0.6rem;">Auto-selected based on role</small>
                            </div>
                        </div>
                        
                        <!-- Location Section -->
                        <div class="border rounded-sm p-2 mt-2 mb-2" style="background: #f8fafc;">
                            <strong class="d-block mb-1" style="font-size: 0.7rem;"><i class="ti ti-map-pin me-1"></i> Location Assignment</strong>
                            
                            <div class="mb-1">
                                <label class="form-label" style="font-size: 0.65rem;">University <span class="text-danger">*</span></label>
                                <select id="university_id" name="university_id" class="form-select form-select-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;" required>
                                    <option value="">Select University</option>
                                    <?php $__currentLoopData = $universities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $uni): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($uni->id); ?>"><?php echo e($uni->name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            
                            <div class="mb-1">
                                <label class="form-label" style="font-size: 0.65rem;">Campus <span class="text-danger">*</span></label>
                                <select id="campus_id" name="campus_id" class="form-select form-select-sm rounded-sm" disabled style="font-size: 0.7rem; padding: 0.2rem 0.3rem;" required>
                                    <option value="">Select University First</option>
                                </select>
                            </div>
                            
                            <div class="mb-1">
                                <label class="form-label" style="font-size: 0.65rem;">School/Faculty</label>
                                <select id="school_id" name="school_id" class="form-select form-select-sm rounded-sm" disabled style="font-size: 0.7rem; padding: 0.2rem 0.3rem;">
                                    <option value="">Select Campus First</option>
                                </select>
                            </div>
                            
                            <div class="mb-1">
                                <label class="form-label" style="font-size: 0.65rem;">Department <span class="text-danger">*</span></label>
                                <select id="department_id" name="department_id" class="form-select form-select-sm rounded-sm" disabled style="font-size: 0.7rem; padding: 0.2rem 0.3rem;" required>
                                    <option value="">Select School First</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="alert alert-info py-1 px-2 mb-2 rounded-sm" style="font-size: 0.65rem;">
                            <i class="ti ti-info-circle me-1"></i>
                            <strong>Temporary Password:</strong> <code>Temp@123</code> (user will be required to change on first login)
                        </div>
                        
                        <div class="d-flex justify-content-end gap-1 mt-2">
                            <a href="<?php echo e(route('staff.index')); ?>" class="btn btn-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.25rem 0.5rem;">Cancel</a>
                            <button type="submit" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.25rem 0.5rem;">
                                <i class="ti ti-device-floppy me-1"></i> Create Staff
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Info Card -->
        <div class="col-lg-5">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-info-circle text-info me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Staff Creation Guide</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <div class="mb-2">
                        <div class="fw-semibold" style="font-size: 0.7rem;"><i class="ti ti-user me-1 text-primary"></i> Staff Categories</div>
                        <ul class="mb-0 ps-3" style="font-size: 0.65rem; color: #555;">
                            <li><strong>Academic</strong> - Teaching staff, professors, lecturers</li>
                            <li><strong>Administrative</strong> - Office staff, HR, finance</li>
                            <li><strong>Supporting</strong> - Technical, IT, library, maintenance</li>
                        </ul>
                    </div>
                    
                    <div class="mb-2">
                        <div class="fw-semibold" style="font-size: 0.7rem;"><i class="ti ti-shield me-1 text-primary"></i> Roles & Permissions</div>
                        <ul class="mb-0 ps-3" style="font-size: 0.65rem; color: #555;">
                            <li><strong>Staff</strong> - Basic leave requests and tracking</li>
                            <li><strong>HOD</strong> - Approve departmental leave requests</li>
                            <li><strong>HR Officer</strong> - Final approvals and reports</li>
                            <li><strong>Admin</strong> - Full system control</li>
                        </ul>
                    </div>
                    
                    <div class="alert alert-light border mb-0 py-1 px-2 rounded-sm" style="font-size: 0.65rem;">
                        <i class="ti ti-mail text-primary me-1"></i>
                        <span>New staff will receive a welcome email with their temporary password.</span>
                    </div>
                </div>
            </div>
            
            <!-- Quick Stats -->
            <div class="card rounded-sm mt-2">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-chart-bar text-success me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">System Statistics</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="fw-bold text-primary" style="font-size: 1.1rem;"><?php echo e(\App\Models\User::count()); ?></div>
                            <div class="text-muted" style="font-size: 0.6rem;">Total Staff</div>
                        </div>
                        <div class="col-6">
                            <div class="fw-bold text-success" style="font-size: 1.1rem;"><?php echo e(\App\Models\Department::count()); ?></div>
                            <div class="text-muted" style="font-size: 0.6rem;">Departments</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Category auto-select script -->
<script>
document.querySelector('select[name="role"]').addEventListener('change', function() {
    const role = this.value;
    const categoryDisplay = document.getElementById('category_display');
    let category = '';
    
    if (role === 'hod' || role === 'staff') {
        category = 'Academic';
    } else if (role === 'hr-officer') {
        category = 'Administrative';
    } else if (role === 'admin') {
        category = 'Administrative';
    } else {
        category = '';
    }
    
    categoryDisplay.value = category;
});

// Cascading selects - UPDATED URLs
document.getElementById('university_id').addEventListener('change', function() {
    const universityId = this.value;
    const campusSelect = document.getElementById('campus_id');
    const schoolSelect = document.getElementById('school_id');
    const deptSelect = document.getElementById('department_id');
    
    campusSelect.innerHTML = '<option value="">Select Campus</option>';
    schoolSelect.innerHTML = '<option value="">Select Campus First</option>';
    deptSelect.innerHTML = '<option value="">Select School First</option>';
    campusSelect.disabled = true;
    schoolSelect.disabled = true;
    deptSelect.disabled = true;
    
    if (universityId) {
        fetch(`/staff/campuses/${universityId}`)
            .then(res => res.json())
            .then(data => {
                data.forEach(campus => {
                    campusSelect.innerHTML += `<option value="${campus.id}">${campus.name}</option>`;
                });
                campusSelect.disabled = false;
            });
    }
});

document.getElementById('campus_id').addEventListener('change', function() {
    const campusId = this.value;
    const schoolSelect = document.getElementById('school_id');
    const deptSelect = document.getElementById('department_id');
    
    schoolSelect.innerHTML = '<option value="">Select School</option>';
    deptSelect.innerHTML = '<option value="">Select School First</option>';
    schoolSelect.disabled = true;
    deptSelect.disabled = true;
    
    if (campusId) {
        fetch(`/staff/schools/${campusId}`)
            .then(res => res.json())
            .then(data => {
                data.forEach(school => {
                    schoolSelect.innerHTML += `<option value="${school.id}">${school.name}</option>`;
                });
                schoolSelect.disabled = false;
            });
    }
});

document.getElementById('school_id').addEventListener('change', function() {
    const schoolId = this.value;
    const deptSelect = document.getElementById('department_id');
    
    deptSelect.innerHTML = '<option value="">Select Department</option>';
    deptSelect.disabled = true;
    
    if (schoolId) {
        fetch(`/staff/departments/${schoolId}`)
            .then(res => res.json())
            .then(data => {
                data.forEach(dept => {
                    deptSelect.innerHTML += `<option value="${dept.id}">${dept.name}</option>`;
                });
                deptSelect.disabled = false;
            });
    }
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\wamp64\www\unileave\resources\views/admin/staff/create.blade.php ENDPATH**/ ?>