

<?php $__env->startSection('title', 'Apply for Leave'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-2">
    
    <!-- Breadcrumb -->
    <?php if (isset($component)) { $__componentOriginale19f62b34dfe0bfdf95075badcb45bc2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale19f62b34dfe0bfdf95075badcb45bc2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.breadcrumb','data' => ['items' => [
        ['name' => 'Leave Requests', 'url' => route('leave-requests.my-requests')],
        ['name' => 'Apply for Leave', 'url' => '#']
    ]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('breadcrumb'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['items' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
        ['name' => 'Leave Requests', 'url' => route('leave-requests.my-requests')],
        ['name' => 'Apply for Leave', 'url' => '#']
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'APPLY FOR LEAVE','description' => 'Submit a new leave request']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'APPLY FOR LEAVE','description' => 'Submit a new leave request']); ?>
        <a href="<?php echo e(route('leave-requests.my-requests')); ?>" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
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
    
    <!-- Validation Errors -->
    <?php if($errors->any()): ?>
    <div class="alert alert-danger alert-dismissible fade show py-1 px-2 mb-2 rounded-sm" style="font-size: 0.7rem;" role="alert">
        <i class="ti ti-alert-circle me-1"></i>
        <strong>Please fix the following errors:</strong>
        <ul class="mb-0 mt-1 ps-3">
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php echo e($error); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
        <button type="button" class="btn-close py-0" style="font-size: 0.6rem;" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>
    
    <!-- Success Message -->
    <?php if(session('success')): ?>
    <div class="alert alert-success alert-dismissible fade show py-1 px-2 mb-2 rounded-sm" style="font-size: 0.7rem;" role="alert">
        <i class="ti ti-check-circle me-1"></i> <?php echo e(session('success')); ?>

        <button type="button" class="btn-close py-0" style="font-size: 0.6rem;" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>
    
    <!-- Error Message -->
    <?php if(session('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show py-1 px-2 mb-2 rounded-sm" style="font-size: 0.7rem;" role="alert">
        <i class="ti ti-alert-circle me-1"></i> <?php echo e(session('error')); ?>

        <button type="button" class="btn-close py-0" style="font-size: 0.6rem;" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>
    
    <div class="row g-2">
        <!-- Main Form -->
        <div class="col-lg-8">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-calendar text-primary me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Leave Application</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <form action="<?php echo e(route('leave-requests.store')); ?>" method="POST" id="leaveForm" enctype="multipart/form-data">
                        <?php echo csrf_field(); ?>
                        
                        <!-- Leave Type -->
                        <div class="mb-1">
                            <label class="form-label" style="font-size: 0.65rem;">Leave Type <span class="text-danger">*</span></label>
                            <select name="leave_type_id" id="leave_type_id" class="form-select form-select-sm rounded-sm <?php $__errorArgs = ['leave_type_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;" required>
                                <option value="">Select Leave Type</option>
                                <?php $__currentLoopData = $leaveTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($type->id); ?>" <?php echo e(old('leave_type_id') == $type->id ? 'selected' : ''); ?> data-requires-cert="<?php echo e($type->requires_certificate); ?>">
                                    <?php echo e($type->name); ?> (<?php echo e($type->code); ?>) <?php if($type->requires_certificate): ?> - Requires Certificate <?php endif; ?>
                                </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <?php $__errorArgs = ['leave_type_id'];
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
                        
                        <!-- Half Day Option -->
                        <div class="mb-1">
                            <label class="form-label" style="font-size: 0.65rem;">Leave Period</label>
                            <div class="d-flex gap-3">
                                <div class="form-check">
                                    <input type="radio" name="half_day" id="full_day" value="full" class="form-check-input" checked>
                                    <label class="form-check-label" for="full_day" style="font-size: 0.65rem;">Full Day</label>
                                </div>
                                <div class="form-check">
                                    <input type="radio" name="half_day" id="morning_half" value="morning" class="form-check-input">
                                    <label class="form-check-label" for="morning_half" style="font-size: 0.65rem;">Half Day - Morning</label>
                                </div>
                                <div class="form-check">
                                    <input type="radio" name="half_day" id="afternoon_half" value="afternoon" class="form-check-input">
                                    <label class="form-check-label" for="afternoon_half" style="font-size: 0.65rem;">Half Day - Afternoon</label>
                                </div>
                            </div>
                            <small class="text-muted" style="font-size: 0.55rem;">Half day leave counts as 0.5 day</small>
                        </div>
                        
                        <!-- Date Range -->
                        <div class="row g-1">
                            <div class="col-md-6 mb-1">
                                <label class="form-label" style="font-size: 0.65rem;">Start Date <span class="text-danger">*</span></label>
                                <input type="date" name="start_date" id="start_date" class="form-control form-control-sm rounded-sm <?php $__errorArgs = ['start_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('start_date')); ?>" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;" required>
                                <?php $__errorArgs = ['start_date'];
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
                                <label class="form-label" style="font-size: 0.65rem;">End Date <span class="text-danger">*</span></label>
                                <input type="date" name="end_date" id="end_date" class="form-control form-control-sm rounded-sm <?php $__errorArgs = ['end_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('end_date')); ?>" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;" required>
                                <?php $__errorArgs = ['end_date'];
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
                        
                        <!-- Expected Return Date -->
                        <div class="mb-1">
                            <label class="form-label" style="font-size: 0.65rem;">Expected Return Date</label>
                            <input type="date" name="expected_return_date" id="expected_return_date" class="form-control form-control-sm rounded-sm" value="<?php echo e(old('expected_return_date')); ?>" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;">
                            <small class="text-muted" style="font-size: 0.55rem;">When do you plan to resume work?</small>
                        </div>
                        
                        <!-- Days Display -->
                        <div class="mb-1">
                            <div class="alert alert-info py-1 px-2 rounded-sm mb-1" style="font-size: 0.65rem;">
                                <i class="ti ti-info-circle me-1"></i>
                                <strong>Number of days:</strong> <span id="days_count">0</span> day(s)
                                <span id="half_day_note" class="text-warning d-none"> (Half day selected)</span>
                            </div>
                        </div>
                        
                        <!-- Acting Staff (Hold Position) -->
                        <div class="mb-1">
                            <label class="form-label" style="font-size: 0.65rem;">Who will hold your position? <span class="text-danger">*</span></label>
                            <select name="acting_staff_id" class="form-select form-select-sm rounded-sm <?php $__errorArgs = ['acting_staff_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;" required>
                                <option value="">Select Colleague</option>
                                <?php $__currentLoopData = $colleagues; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $colleague): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($colleague->id); ?>" <?php echo e(old('acting_staff_id') == $colleague->id ? 'selected' : ''); ?>>
                                    <?php echo e($colleague->title ? $colleague->title . ' ' : ''); ?><?php echo e($colleague->first_name); ?> <?php if($colleague->middle_name): ?><?php echo e($colleague->middle_name); ?> <?php endif; ?><?php echo e($colleague->last_name); ?> (<?php echo e($colleague->staff_id); ?>)
                                </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <?php $__errorArgs = ['acting_staff_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback" style="font-size: 0.65rem;"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            <small class="text-muted" style="font-size: 0.6rem;">This person will be notified to accept or decline holding your position during your absence.</small>
                        </div>
                        
                        <!-- ============================================ -->
                        <!-- EMERGENCY CONTACT SECTION -->
                        <!-- ============================================ -->
                        <div class="border-top pt-2 mt-2">
                            <div class="fw-semibold mb-1" style="font-size: 0.7rem;">
                                <i class="ti ti-phone text-warning me-1"></i> Emergency Contact
                            </div>
                            
                            <!-- Select from saved contacts -->
                            <div class="mb-1">
                                <label class="form-label" style="font-size: 0.65rem;">Select Saved Emergency Contact</label>
                                <select name="emergency_contact_id" id="emergency_contact_id" class="form-select form-select-sm rounded-sm" style="font-size: 0.7rem;">
                                    <option value="">-- Select from saved contacts --</option>
                                    <?php $__currentLoopData = auth()->user()->emergencyContacts ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $contact): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($contact->id); ?>" data-name="<?php echo e($contact->full_name); ?>" data-phone="<?php echo e($contact->phone_primary); ?>">
                                        <?php echo e($contact->full_name); ?> (<?php echo e($contact->relationship); ?>) - <?php echo e($contact->phone_primary); ?>

                                    </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <small class="text-muted" style="font-size: 0.55rem;">Or manually enter below</small>
                            </div>
                            
                            <!-- Manual Emergency Contact -->
                            <div class="row g-1">
                                <div class="col-md-6 mb-1">
                                    <label class="form-label" style="font-size: 0.65rem;">Emergency Contact Name</label>
                                    <input type="text" name="emergency_contact_name" id="emergency_contact_name" class="form-control form-control-sm rounded-sm" value="<?php echo e(old('emergency_contact_name')); ?>" style="font-size: 0.7rem;" placeholder="Full name">
                                </div>
                                <div class="col-md-6 mb-1">
                                    <label class="form-label" style="font-size: 0.65rem;">Emergency Contact Phone</label>
                                    <input type="text" name="emergency_contact_phone" id="emergency_contact_phone" class="form-control form-control-sm rounded-sm" value="<?php echo e(old('emergency_contact_phone')); ?>" style="font-size: 0.7rem;" placeholder="Phone number">
                                </div>
                            </div>
                        </div>
                        
                        <!-- ============================================ -->
                        <!-- CONTACT DURING LEAVE & ADDRESS -->
                        <!-- ============================================ -->
                        <div class="border-top pt-2 mt-2">
                            <div class="fw-semibold mb-1" style="font-size: 0.7rem;">
                                <i class="ti ti-map-pin text-info me-1"></i> Contact Information During Leave
                            </div>
                            
                            <div class="row g-1">
                                <div class="col-md-6 mb-1">
                                    <label class="form-label" style="font-size: 0.65rem;">Contact Number (During Leave)</label>
                                    <input type="text" name="contact_number_during_leave" class="form-control form-control-sm rounded-sm" value="<?php echo e(old('contact_number_during_leave')); ?>" style="font-size: 0.7rem;" placeholder="Phone number where you can be reached">
                                    <small class="text-muted" style="font-size: 0.55rem;">Optional but recommended</small>
                                </div>
                                <div class="col-md-6 mb-1">
                                    <label class="form-label" style="font-size: 0.65rem;">Leave Address</label>
                                    <input type="text" name="leave_address" class="form-control form-control-sm rounded-sm" value="<?php echo e(old('leave_address')); ?>" style="font-size: 0.7rem;" placeholder="Where you will be staying">
                                </div>
                            </div>
                            
                            <div class="row g-1">
                                <div class="col-md-12 mb-1">
                                    <label class="form-label" style="font-size: 0.65rem;">Travel Destination</label>
                                    <input type="text" name="travel_destination" class="form-control form-control-sm rounded-sm" value="<?php echo e(old('travel_destination')); ?>" style="font-size: 0.7rem;" placeholder="Where are you traveling to (if applicable)">
                                </div>
                            </div>
                        </div>
                        
                        <!-- ============================================ -->
                        <!-- OFFICIAL TRAVEL DETAILS (NEW) -->
                        <!-- ============================================ -->
                        <div class="border-top pt-2 mt-2">
                            <div class="fw-semibold mb-1" style="font-size: 0.7rem;">
                                <i class="ti ti-briefcase text-primary me-1"></i> Official Travel Details
                            </div>
                            
                            <div class="mb-1">
                                <label class="form-label" style="font-size: 0.65rem;">Benefit to University</label>
                                <textarea name="benefit_to_university" rows="2" class="form-control form-control-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;" placeholder="What benefit will the university derive from this travel? (Required for official travel)"><?php echo e(old('benefit_to_university')); ?></textarea>
                                <small class="text-muted" style="font-size: 0.55rem;">Explain how this travel benefits SJUT</small>
                            </div>
                            
                            <div class="mb-1">
                                <label class="form-label" style="font-size: 0.65rem;">Cost Coverage</label>
                                <select name="cost_coverage" class="form-select form-select-sm rounded-sm" style="font-size: 0.7rem;">
                                    <option value="">Select who covers the cost</option>
                                    <option value="organizers_full" <?php echo e(old('cost_coverage') == 'organizers_full' ? 'selected' : ''); ?>>Organizers (Fully Paid)</option>
                                    <option value="organizers_partial" <?php echo e(old('cost_coverage') == 'organizers_partial' ? 'selected' : ''); ?>>Organizers (Partially Paid)</option>
                                    <option value="sjut" <?php echo e(old('cost_coverage') == 'sjut' ? 'selected' : ''); ?>>SJUT</option>
                                    <option value="private" <?php echo e(old('cost_coverage') == 'private' ? 'selected' : ''); ?>>Private Travel</option>
                                </select>
                            </div>
                            
                            <div class="form-check">
                                <input type="checkbox" name="is_first_time_applicant" class="form-check-input" id="firstTimeApplicant" value="1" <?php echo e(old('is_first_time_applicant') ? 'checked' : ''); ?>>
                                <label class="form-check-label" for="firstTimeApplicant" style="font-size: 0.65rem;">
                                    This is my first time applying for this type of leave
                                </label>
                            </div>
                        </div>
                        
                        <!-- ============================================ -->
                        <!-- HANDOVER NOTES -->
                        <!-- ============================================ -->
                        <div class="border-top pt-2 mt-2">
                            <div class="fw-semibold mb-1" style="font-size: 0.7rem;">
                                <i class="ti ti-file-description text-success me-1"></i> Handover Notes
                            </div>
                            
                            <div class="mb-1">
                                <textarea name="handover_notes" rows="3" class="form-control form-control-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;" placeholder="Describe your pending tasks, ongoing projects, and who will handle them during your absence..."><?php echo e(old('handover_notes')); ?></textarea>
                                <small class="text-muted" style="font-size: 0.55rem;">Provide details about work responsibilities and coverage</small>
                            </div>
                        </div>
                        
                        <!-- ============================================ -->
                        <!-- SUPPORTING DOCUMENTS -->
                        <!-- ============================================ -->
                        <div class="border-top pt-2 mt-2">
                            <div class="fw-semibold mb-1" style="font-size: 0.7rem;">
                                <i class="ti ti-file-text text-danger me-1"></i> Supporting Documents
                            </div>
                            
                            <div class="mb-1">
                                <input type="file" name="supporting_documents[]" id="supporting_documents" class="form-control form-control-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;" multiple accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                                <small class="text-muted" style="font-size: 0.55rem;">Upload medical certificates, invitation letters, or other supporting documents (Max 5 files, 5MB each)</small>
                                <div id="certificate_requirement" class="text-danger d-none" style="font-size: 0.55rem;">This leave type requires a medical certificate</div>
                            </div>
                            <div id="fileList" class="mt-1"></div>
                        </div>
                        
                        <!-- Reason -->
                        <div class="border-top pt-2 mt-2">
                            <div class="fw-semibold mb-1" style="font-size: 0.7rem;">
                                <i class="ti ti-message-2 me-1"></i> Reason <span class="text-danger">*</span>
                            </div>
                            <div class="mb-1">
                                <textarea name="reason" rows="3" class="form-control form-control-sm rounded-sm <?php $__errorArgs = ['reason'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;" required><?php echo e(old('reason')); ?></textarea>
                                <?php $__errorArgs = ['reason'];
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
                        
                        <!-- Letter Content (Optional) -->
                        <div class="border-top pt-2 mt-2">
                            <div class="fw-semibold mb-1" style="font-size: 0.7rem;">
                                <i class="ti ti-file-text me-1"></i> Letter Content (Optional)
                            </div>
                            <div class="mb-1">
                                <textarea name="letter_content" rows="5" class="form-control form-control-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;" placeholder="Write your leave letter here..."><?php echo e(old('letter_content')); ?></textarea>
                                <small class="text-muted" style="font-size: 0.55rem;">This will be included in the formal leave letter.</small>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-end gap-1 mt-2">
                            <a href="<?php echo e(route('leave-requests.my-requests')); ?>" class="btn btn-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.25rem 0.5rem;">Cancel</a>
                            <button type="submit" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.25rem 0.5rem;">
                                <i class="ti ti-device-floppy me-1"></i> Submit Request
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Info Card -->
        <div class="col-lg-4">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-info-circle text-info me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Information</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <div class="mb-2">
                        <div class="fw-semibold" style="font-size: 0.7rem;"><i class="ti ti-clock me-1 text-primary"></i> Process Steps</div>
                        <ul class="mb-0 ps-3" style="font-size: 0.65rem; color: #555;">
                            <li>1. Select colleague to hold your position</li>
                            <li>2. Colleague accepts/declines (notification sent)</li>
                            <li>3. Head of Unit reviews (if applicable)</li>
                            <li>4. Head of Department reviews</li>
                            <li>5. Dean reviews (academic staff only)</li>
                            <li>6. DVC (Academic/PFA) reviews</li>
                            <li>7. HR Officer processes final balance</li>
                        </ul>
                    </div>
                    
                    <div class="alert alert-light border mb-0 py-1 px-2 rounded-sm">
                        <div class="d-flex align-items-start gap-1">
                            <i class="ti ti-tips text-warning mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">Important Notes</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">• The colleague you select will receive a notification to accept or decline</small><br>
                                <small class="text-muted" style="font-size: 0.6rem;">• Medical certificates required for Sick Leave</small><br>
                                <small class="text-muted" style="font-size: 0.6rem;">• Official travel requires benefit to university statement</small><br>
                                <small class="text-muted" style="font-size: 0.6rem;">• All documents become part of your leave record</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Calculate days between dates (excluding weekends, accounting for half day)
function calculateDays() {
    const startDate = document.getElementById('start_date').value;
    const endDate = document.getElementById('end_date').value;
    const halfDay = document.querySelector('input[name="half_day"]:checked').value;
    
    if (startDate && endDate) {
        const start = new Date(startDate);
        const end = new Date(endDate);
        
        if (end >= start) {
            let days = 0;
            let current = new Date(start);
            
            while (current <= end) {
                const dayOfWeek = current.getDay();
                if (dayOfWeek !== 0 && dayOfWeek !== 6) {
                    days++;
                }
                current.setDate(current.getDate() + 1);
            }
            
            // If half day, subtract 0.5
            if (halfDay !== 'full') {
                days = days - 0.5;
                document.getElementById('half_day_note').classList.remove('d-none');
                document.getElementById('days_count').innerText = days;
            } else {
                document.getElementById('half_day_note').classList.add('d-none');
                document.getElementById('days_count').innerText = days;
            }
        } else {
            document.getElementById('days_count').innerText = '0';
        }
    }
}

// Auto-fill emergency contact details when selected from dropdown
document.getElementById('emergency_contact_id')?.addEventListener('change', function() {
    const selected = this.options[this.selectedIndex];
    const name = selected.dataset.name;
    const phone = selected.dataset.phone;
    
    if (name && phone) {
        document.getElementById('emergency_contact_name').value = name;
        document.getElementById('emergency_contact_phone').value = phone;
    }
});

// Show certificate requirement for sick leave
document.getElementById('leave_type_id')?.addEventListener('change', function() {
    const selected = this.options[this.selectedIndex];
    const requiresCert = selected.dataset.requiresCert === '1';
    const certDiv = document.getElementById('certificate_requirement');
    
    if (requiresCert) {
        certDiv.classList.remove('d-none');
    } else {
        certDiv.classList.add('d-none');
    }
});

// File upload preview
document.getElementById('supporting_documents')?.addEventListener('change', function(e) {
    const fileList = document.getElementById('fileList');
    fileList.innerHTML = '';
    
    const maxFiles = 5;
    const maxSize = 5 * 1024 * 1024; // 5MB
    
    if (this.files.length > maxFiles) {
        alert(`You can only upload up to ${maxFiles} files`);
        this.value = '';
        return;
    }
    
    for (let i = 0; i < this.files.length; i++) {
        const file = this.files[i];
        if (file.size > maxSize) {
            alert(`File "${file.name}" exceeds 5MB limit`);
            this.value = '';
            return;
        }
        
        const div = document.createElement('div');
        div.className = 'd-flex align-items-center gap-1 mb-1';
        div.style.fontSize = '0.6rem';
        div.innerHTML = `
            <i class="ti ti-file text-primary"></i>
            <span>${file.name}</span>
            <span class="text-muted">(${(file.size / 1024).toFixed(1)} KB)</span>
        `;
        fileList.appendChild(div);
    }
});

// Recalculate days when half day option changes
document.querySelectorAll('input[name="half_day"]').forEach(radio => {
    radio.addEventListener('change', calculateDays);
});

document.getElementById('start_date').addEventListener('change', calculateDays);
document.getElementById('end_date').addEventListener('change', calculateDays);

// Initial calculation if values exist
if (document.getElementById('start_date').value && document.getElementById('end_date').value) {
    calculateDays();
}
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\wamp64\www\unileave\resources\views/shared/leave-requests/create.blade.php ENDPATH**/ ?>