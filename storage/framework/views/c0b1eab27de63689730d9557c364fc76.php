

<?php $__env->startSection('title', 'Approval Details - ' . ($leaveRequest->staff->full_name ?? 'Leave Request')); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-2">
    
    <!-- Breadcrumb -->
    <?php if (isset($component)) { $__componentOriginale19f62b34dfe0bfdf95075badcb45bc2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale19f62b34dfe0bfdf95075badcb45bc2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.breadcrumb','data' => ['items' => [
        ['name' => 'Approvals History', 'url' => route('leave-requests.approvals-history')],
        ['name' => 'Approval Details', 'url' => '#']
    ]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('breadcrumb'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['items' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
        ['name' => 'Approvals History', 'url' => route('leave-requests.approvals-history')],
        ['name' => 'Approval Details', 'url' => '#']
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'APPROVAL DETAILS','description' => 'Complete leave request information']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'APPROVAL DETAILS','description' => 'Complete leave request information']); ?>
        <div>
            <a href="<?php echo e(route('leave-requests.approvals-history')); ?>" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
                <i class="ti ti-arrow-left me-1"></i> Back to List
            </a>
            <?php if($leaveRequest->status === 'approved'): ?>
            <button type="button" onclick="window.print();" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
                <i class="ti ti-printer me-1"></i> Print Letter
            </button>
            <button type="button" id="downloadPdfBtn" class="btn btn-success btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
                <i class="ti ti-download me-1"></i> Download PDF
            </button>
            <?php endif; ?>
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
    
    <div class="row g-2">
        <!-- Main Details Column -->
        <div class="col-lg-7">
            
            <!-- ============================================ -->
            <!-- LEAVE REQUEST DETAILS CARD -->
            <!-- ============================================ -->
            <div class="card rounded-sm mb-2">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-info-circle text-primary me-1" style="font-size: 0.7rem;"></i>
                        <span style="font-weight: 600; font-size: 0.7rem;">Leave Request Details</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <div class="row g-1">
                        <div class="col-md-6 mb-1">
                            <div class="text-muted" style="font-size: 0.6rem;">Staff Name</div>
                            <div class="fw-semibold" style="font-size: 0.75rem;">
                                <?php echo e($leaveRequest->staff->full_name ?? 'N/A'); ?>

                                <span class="text-muted" style="font-size: 0.65rem;">(<?php echo e($leaveRequest->staff->staff_id ?? 'No ID'); ?>)</span>
                            </div>
                        </div>
                        <div class="col-md-6 mb-1">
                            <div class="text-muted" style="font-size: 0.6rem;">Department</div>
                            <div style="font-size: 0.75rem;"><?php echo e($leaveRequest->staff->department->name ?? 'N/A'); ?></div>
                        </div>
                    </div>
                    
                    <div class="row g-1">
                        <div class="col-md-6 mb-1">
                            <div class="text-muted" style="font-size: 0.6rem;">Leave Type</div>
                            <div class="fw-semibold" style="font-size: 0.75rem;"><?php echo e($leaveRequest->leaveType->name ?? 'N/A'); ?></div>
                        </div>
                        <div class="col-md-6 mb-1">
                            <div class="text-muted" style="font-size: 0.6rem;">Leave Code</div>
                            <div style="font-size: 0.75rem;"><?php echo e($leaveRequest->leaveType->code ?? 'N/A'); ?></div>
                        </div>
                    </div>
                    
                    <div class="row g-1">
                        <div class="col-md-6 mb-1">
                            <div class="text-muted" style="font-size: 0.6rem;">Start Date</div>
                            <div style="font-size: 0.75rem;"><?php echo e(\Carbon\Carbon::parse($leaveRequest->start_date)->format('d M Y')); ?></div>
                        </div>
                        <div class="col-md-6 mb-1">
                            <div class="text-muted" style="font-size: 0.6rem;">End Date</div>
                            <div style="font-size: 0.75rem;"><?php echo e(\Carbon\Carbon::parse($leaveRequest->end_date)->format('d M Y')); ?></div>
                        </div>
                    </div>
                    
                    <div class="row g-1">
                        <div class="col-md-6 mb-1">
                            <div class="text-muted" style="font-size: 0.6rem;">Total Days</div>
                            <div class="fw-semibold text-primary" style="font-size: 0.85rem;"><?php echo e($leaveRequest->days); ?> day(s)</div>
                        </div>
                        <div class="col-md-6 mb-1">
                            <div class="text-muted" style="font-size: 0.6rem;">Half Day</div>
                            <div style="font-size: 0.75rem;">
                                <?php if($leaveRequest->half_day && $leaveRequest->half_day != 'full'): ?>
                                    <span class="badge bg-info-subtle text-info rounded-sm"><?php echo e(ucfirst($leaveRequest->half_day)); ?></span>
                                <?php else: ?>
                                    <span class="text-muted">Full Day</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row g-1">
                        <div class="col-md-12 mb-1">
                            <div class="text-muted" style="font-size: 0.6rem;">Reason for Leave</div>
                            <div class="p-1 bg-light rounded-sm" style="font-size: 0.7rem;"><?php echo e($leaveRequest->reason); ?></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- ============================================ -->
            <!-- RETURN TO WORK CARD -->
            <!-- ============================================ -->
            <div class="card rounded-sm mb-2">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-calendar-return text-success me-1" style="font-size: 0.7rem;"></i>
                        <span style="font-weight: 600; font-size: 0.7rem;">Return to Work</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <div class="row g-1">
                        <div class="col-md-6 mb-1">
                            <div class="text-muted" style="font-size: 0.6rem;">Expected Return Date</div>
                            <div class="fw-semibold" style="font-size: 0.75rem;">
                                <?php echo e($leaveRequest->expected_return_date ? \Carbon\Carbon::parse($leaveRequest->expected_return_date)->format('d M Y') : \Carbon\Carbon::parse($leaveRequest->end_date)->addDay()->format('d M Y')); ?>

                            </div>
                        </div>
                        <div class="col-md-6 mb-1">
                            <div class="text-muted" style="font-size: 0.6rem;">Actual Return Date</div>
                            <div class="fw-semibold" style="font-size: 0.75rem;">
                                <?php if($leaveRequest->actual_return_date): ?>
                                    <?php echo e(\Carbon\Carbon::parse($leaveRequest->actual_return_date)->format('d M Y')); ?>

                                    <?php
                                        $expected = $leaveRequest->expected_return_date ? \Carbon\Carbon::parse($leaveRequest->expected_return_date) : \Carbon\Carbon::parse($leaveRequest->end_date)->addDay();
                                        $actual = \Carbon\Carbon::parse($leaveRequest->actual_return_date);
                                        $lateDays = $expected->diffInDays($actual, false);
                                    ?>
                                    <?php if($lateDays > 0): ?>
                                        <span class="badge bg-danger-subtle text-danger ms-1">Late by <?php echo e($lateDays); ?> days</span>
                                    <?php else: ?>
                                        <span class="badge bg-success-subtle text-success ms-1">On Time</span>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="text-muted">Not yet reported</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php if($leaveRequest->late_return_days > 0): ?>
                    <div class="row g-1 mt-1">
                        <div class="col-md-12">
                            <div class="alert alert-warning py-1 px-2 rounded-sm" style="font-size: 0.65rem;">
                                <i class="ti ti-alert-triangle me-1"></i>
                                <strong>Late Return:</strong> <?php echo e($leaveRequest->late_return_days); ?> day(s) late
                                <?php if($leaveRequest->late_return_penalty > 0): ?>
                                    <br>Penalty Applied: $<?php echo e(number_format($leaveRequest->late_return_penalty, 2)); ?>

                                <?php endif; ?>
                                <?php if($leaveRequest->late_return_reason): ?>
                                    <br><strong>Reason:</strong> <?php echo e($leaveRequest->late_return_reason); ?>

                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- ============================================ -->
            <!-- TRAVEL & CONTACT CARD -->
            <!-- ============================================ -->
            <div class="card rounded-sm mb-2">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-plane text-info me-1" style="font-size: 0.7rem;"></i>
                        <span style="font-weight: 600; font-size: 0.7rem;">Travel & Contact Information</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <div class="row g-1">
                        <div class="col-md-6 mb-1">
                            <div class="text-muted" style="font-size: 0.6rem;">Travel Destination</div>
                            <div style="font-size: 0.75rem;"><?php echo e($leaveRequest->travel_destination ?? 'Not specified'); ?></div>
                        </div>
                        <div class="col-md-6 mb-1">
                            <div class="text-muted" style="font-size: 0.6rem;">Leave Address</div>
                            <div style="font-size: 0.75rem;"><?php echo e($leaveRequest->leave_address ?? 'Not specified'); ?></div>
                        </div>
                    </div>
                    <div class="row g-1">
                        <div class="col-md-6 mb-1">
                            <div class="text-muted" style="font-size: 0.6rem;">Contact During Leave</div>
                            <div style="font-size: 0.75rem;"><?php echo e($leaveRequest->contact_number_during_leave ?? 'Not specified'); ?></div>
                        </div>
                        <div class="col-md-6 mb-1">
                            <div class="text-muted" style="font-size: 0.6rem;">Benefit to University</div>
                            <div style="font-size: 0.75rem;"><?php echo e($leaveRequest->benefit_to_university ?? 'N/A'); ?></div>
                        </div>
                    </div>
                    <div class="row g-1">
                        <div class="col-md-6 mb-1">
                            <div class="text-muted" style="font-size: 0.6rem;">Cost Coverage</div>
                            <div>
                                <?php if($leaveRequest->cost_coverage): ?>
                                    <span class="badge bg-primary-subtle text-primary rounded-sm">
                                        <?php echo e(str_replace('_', ' ', ucfirst($leaveRequest->cost_coverage))); ?>

                                    </span>
                                <?php else: ?>
                                    <span class="text-muted">Not specified</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-6 mb-1">
                            <div class="text-muted" style="font-size: 0.6rem;">First Time Applicant</div>
                            <div>
                                <?php if($leaveRequest->is_first_time_applicant): ?>
                                    <span class="badge bg-info-subtle text-info rounded-sm">Yes</span>
                                <?php else: ?>
                                    <span class="text-muted">No</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- ============================================ -->
            <!-- EMERGENCY CONTACT CARD -->
            <!-- ============================================ -->
            <div class="card rounded-sm mb-2">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-phone-call text-danger me-1" style="font-size: 0.7rem;"></i>
                        <span style="font-weight: 600; font-size: 0.7rem;">Emergency Contact</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <div class="row g-1">
                        <div class="col-md-6 mb-1">
                            <div class="text-muted" style="font-size: 0.6rem;">Contact Name</div>
                            <div class="fw-semibold" style="font-size: 0.75rem;"><?php echo e($leaveRequest->emergency_contact_name ?? 'Not specified'); ?></div>
                        </div>
                        <div class="col-md-6 mb-1">
                            <div class="text-muted" style="font-size: 0.6rem;">Contact Phone</div>
                            <div style="font-size: 0.75rem;"><?php echo e($leaveRequest->emergency_contact_phone ?? 'Not specified'); ?></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- ============================================ -->
            <!-- HANDOVER NOTES CARD -->
            <!-- ============================================ -->
            <?php if($leaveRequest->handover_notes): ?>
            <div class="card rounded-sm mb-2">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-file-description text-warning me-1" style="font-size: 0.7rem;"></i>
                        <span style="font-weight: 600; font-size: 0.7rem;">Handover Notes</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <div style="font-size: 0.7rem;"><?php echo e($leaveRequest->handover_notes); ?></div>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- ============================================ -->
            <!-- SUPPORTING DOCUMENTS CARD -->
            <!-- ============================================ -->
            <div class="card rounded-sm mb-2">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-file-text text-primary me-1" style="font-size: 0.7rem;"></i>
                        <span style="font-weight: 600; font-size: 0.7rem;">Supporting Documents</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <?php
                        $documents = $leaveRequest->documents;
                    ?>
                    <?php if($documents && $documents->count() > 0): ?>
                        <div class="list-group list-group-flush">
                            <?php $__currentLoopData = $documents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="d-flex justify-content-between align-items-center py-1 border-bottom">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="ti ti-file-<?php echo e($doc->file_type == 'application/pdf' ? 'pdf' : 'description'); ?> text-primary" style="font-size: 0.8rem;"></i>
                                    <div>
                                        <div style="font-size: 0.7rem;"><?php echo e($doc->file_name); ?></div>
                                        <div class="text-muted" style="font-size: 0.55rem;">
                                            <?php echo e(number_format($doc->file_size / 1024, 1)); ?> KB • Uploaded <?php echo e($doc->created_at->format('d M Y')); ?>

                                        </div>
                                    </div>
                                </div>
                                <a href="<?php echo e(Storage::url($doc->file_path)); ?>" target="_blank" class="btn btn-sm btn-outline-primary rounded-sm" style="font-size: 0.55rem;">
                                    <i class="ti ti-download me-1"></i> View
                                </a>
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-2 text-muted" style="font-size: 0.65rem;">
                            <i class="ti ti-file mb-1 d-block"></i>
                            No supporting documents uploaded
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- ============================================ -->
            <!-- FINANCIAL DETAILS CARD (HR/Admin/DVC/VC only) -->
            <!-- ============================================ -->
            <?php if(auth()->check() && auth()->user()->hasAnyRole('admin|hr-officer|dvca|dvc-pfa|vice-chancellor')): ?>
            <div class="card rounded-sm mb-2">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-currency-dollar text-success me-1" style="font-size: 0.7rem;"></i>
                        <span style="font-weight: 600; font-size: 0.7rem;">Financial Details</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <div class="row g-1">
                        <div class="col-md-6 mb-1">
                            <div class="text-muted" style="font-size: 0.6rem;">Leave Type</div>
                            <div>
                                <?php if($leaveRequest->is_paid_leave ?? true): ?>
                                    <span class="badge bg-success-subtle text-success rounded-sm">Paid Leave</span>
                                <?php else: ?>
                                    <span class="badge bg-warning-subtle text-warning rounded-sm">Unpaid Leave</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-6 mb-1">
                            <div class="text-muted" style="font-size: 0.6rem;">Salary Impact</div>
                            <div class="fw-semibold" style="font-size: 0.75rem;">
                                <?php if($leaveRequest->salary_impact && $leaveRequest->salary_impact > 0): ?>
                                    $<?php echo e(number_format($leaveRequest->salary_impact, 2)); ?>

                                <?php else: ?>
                                    No impact
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php if($leaveRequest->allowances): ?>
                    <div class="row g-1 mt-1">
                        <div class="col-md-12">
                            <div class="text-muted" style="font-size: 0.6rem;">Allowances</div>
                            <div style="font-size: 0.7rem;"><?php echo e($leaveRequest->allowances); ?></div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- ============================================ -->
            <!-- DEPENDENTS CARD -->
            <!-- ============================================ -->
            <?php if($leaveRequest->dependents_count > 0 || $leaveRequest->dependents_info): ?>
            <div class="card rounded-sm mb-2">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-users text-info me-1" style="font-size: 0.7rem;"></i>
                        <span style="font-weight: 600; font-size: 0.7rem;">Dependents Information</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <div class="row g-1">
                        <div class="col-md-6 mb-1">
                            <div class="text-muted" style="font-size: 0.6rem;">Number of Dependents</div>
                            <div class="fw-semibold" style="font-size: 0.75rem;"><?php echo e($leaveRequest->dependents_count ?? 0); ?></div>
                        </div>
                    </div>
                    <?php if($leaveRequest->dependents_info): ?>
                    <div class="row g-1 mt-1">
                        <div class="col-md-12">
                            <div class="text-muted" style="font-size: 0.6rem;">Dependents Details</div>
                            <div style="font-size: 0.7rem;"><?php echo e($leaveRequest->dependents_info); ?></div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- ============================================ -->
            <!-- ACTING STAFF CARD -->
            <!-- ============================================ -->
            <div class="card rounded-sm mb-2">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-user-check text-success me-1" style="font-size: 0.7rem;"></i>
                        <span style="font-weight: 600; font-size: 0.7rem;">Acting Staff (Position Holder)</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <div class="row g-1">
                        <div class="col-md-6 mb-1">
                            <div class="text-muted" style="font-size: 0.6rem;">Name</div>
                            <div class="fw-semibold" style="font-size: 0.75rem;">
                                <?php echo e($leaveRequest->actingStaff->full_name ?? 'Not specified'); ?>

                            </div>
                        </div>
                        <div class="col-md-6 mb-1">
                            <div class="text-muted" style="font-size: 0.6rem;">Status</div>
                            <div>
                                <?php if($leaveRequest->acting_status == 'accepted'): ?>
                                    <span class="badge bg-success-subtle text-success rounded-sm">Accepted</span>
                                <?php elseif($leaveRequest->acting_status == 'pending'): ?>
                                    <span class="badge bg-warning-subtle text-warning rounded-sm">Pending</span>
                                <?php elseif($leaveRequest->acting_status == 'declined'): ?>
                                    <span class="badge bg-danger-subtle text-danger rounded-sm">Declined</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php if($leaveRequest->acting_responded_at): ?>
                    <div class="row g-1 mt-1">
                        <div class="col-md-12">
                            <div class="text-muted" style="font-size: 0.6rem;">Responded At</div>
                            <div style="font-size: 0.7rem;"><?php echo e(\Carbon\Carbon::parse($leaveRequest->acting_responded_at)->format('d M Y H:i')); ?></div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- ============================================ -->
            <!-- APPROVAL TIMELINE CARD -->
            <!-- ============================================ -->
            <div class="card rounded-sm mb-2">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-timeline text-primary me-1" style="font-size: 0.7rem;"></i>
                        <span style="font-weight: 600; font-size: 0.7rem;">Approval Timeline</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <!-- Visual Timeline -->
                    <div class="timeline-horizontal mb-3">
                        <?php
                            $stages = ['Acting', 'HOD', 'Dean', 'HR', 'DVC', 'Final'];
                            $completedLevels = [];
                            foreach ($leaveRequest->approvalLogs->where('action', 'approved') as $log) {
                                $fromRole = strtolower($log->from_role);
                                if ($fromRole == 'hod') $completedLevels[1] = true;
                                if ($fromRole == 'dean') $completedLevels[2] = true;
                                if ($fromRole == 'hr') $completedLevels[3] = true;
                                if (in_array($fromRole, ['dvca', 'dvc-pfa', 'dvc'])) $completedLevels[4] = true;
                                if ($fromRole == 'hr_logistics') $completedLevels[5] = true;
                            }
                            $isFullyApproved = $leaveRequest->status == 'approved';
                        ?>
                        
                        <div class="d-flex justify-content-between">
                            <?php $__currentLoopData = $stages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $stage): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $levelNum = $i;
                                    $isCompleted = ($levelNum == 0 && $leaveRequest->acting_status == 'accepted') ||
                                                   ($levelNum == 1 && (isset($completedLevels[1]) || $isFullyApproved)) ||
                                                   ($levelNum == 2 && (isset($completedLevels[2]) || $isFullyApproved)) ||
                                                   ($levelNum == 3 && (isset($completedLevels[3]) || $isFullyApproved)) ||
                                                   ($levelNum == 4 && (isset($completedLevels[4]) || $isFullyApproved)) ||
                                                   ($levelNum == 5 && $isFullyApproved);
                                ?>
                                <div class="text-center" style="flex: 1;">
                                    <div class="rounded-circle mx-auto d-flex align-items-center justify-content-center <?php echo e($isCompleted ? 'bg-success' : 'bg-secondary'); ?>" style="width: 28px; height: 28px;">
                                        <i class="ti ti-<?php echo e($isCompleted ? 'check' : 'circle'); ?> text-white" style="font-size: 0.7rem;"></i>
                                    </div>
                                    <div class="mt-1" style="font-size: 0.55rem;"><?php echo e($stage); ?></div>
                                </div>
                                <?php if(!$loop->last): ?>
                                    <div class="flex-grow-1" style="flex: 0.5;"></div>
                                <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                    
                    <!-- Detailed Approval Logs -->
                    <div class="approval-timeline">
                        <?php
                            $allLogs = $leaveRequest->approvalLogs->sortBy('created_at');
                        ?>
                        
                        <?php $__currentLoopData = $allLogs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="d-flex align-items-start gap-2 py-1 border-bottom">
                            <div class="rounded-circle p-1 d-flex align-items-center justify-content-center" style="width: 24px; height: 24px; background: <?php echo e($log->action == 'approved' ? '#D1FAE5' : '#FEE2E2'); ?>;">
                                <i class="ti ti-<?php echo e($log->action == 'approved' ? 'check' : 'x'); ?>" style="font-size: 0.6rem; color: <?php echo e($log->action == 'approved' ? '#059669' : '#DC2626'); ?>;"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between flex-wrap">
                                    <span class="fw-semibold" style="font-size: 0.7rem;">
                                        <?php echo e(ucfirst($log->action)); ?> by <?php echo e($log->approver->full_name ?? 'System'); ?>

                                    </span>
                                    <span class="text-muted" style="font-size: 0.55rem;"><?php echo e($log->created_at->format('d M Y H:i')); ?></span>
                                </div>
                                <div class="text-muted" style="font-size: 0.6rem;">From: <?php echo e(ucfirst($log->from_role)); ?> → To: <?php echo e(ucfirst($log->to_role)); ?></div>
                                <?php if($log->comment): ?>
                                    <div class="small text-muted mt-0" style="font-size: 0.55rem;">"<?php echo e($log->comment); ?>"</div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        
                        <?php if($leaveRequest->acting_responded_at): ?>
                        <div class="d-flex align-items-start gap-2 py-1 border-bottom">
                            <div class="rounded-circle p-1 d-flex align-items-center justify-content-center" style="width: 24px; height: 24px; background: <?php echo e($leaveRequest->acting_status == 'accepted' ? '#D1FAE5' : '#FEE2E2'); ?>;">
                                <i class="ti ti-<?php echo e($leaveRequest->acting_status == 'accepted' ? 'check' : 'x'); ?>" style="font-size: 0.6rem; color: <?php echo e($leaveRequest->acting_status == 'accepted' ? '#059669' : '#DC2626'); ?>;"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between flex-wrap">
                                    <span class="fw-semibold" style="font-size: 0.7rem;">
                                        <?php echo e(ucfirst($leaveRequest->acting_status)); ?> by Acting Staff
                                    </span>
                                    <span class="text-muted" style="font-size: 0.55rem;"><?php echo e(\Carbon\Carbon::parse($leaveRequest->acting_responded_at)->format('d M Y H:i')); ?></span>
                                </div>
                                <div class="text-muted" style="font-size: 0.6rem;">From: Acting Staff → To: HOD</div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- ============================================ -->
            <!-- AUDIT TRAIL CARD (VC/Admin only) -->
            <!-- ============================================ -->
            <?php if(auth()->check() && auth()->user()->hasAnyRole('vice-chancellor|admin')): ?>
            <div class="card rounded-sm mb-2">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-shield-lock text-danger me-1" style="font-size: 0.7rem;"></i>
                        <span style="font-weight: 600; font-size: 0.7rem;">VC & Admin - Audit Information</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <div class="row g-1">
                        <div class="col-md-6 mb-1">
                            <div class="text-muted" style="font-size: 0.6rem;">Request ID</div>
                            <div style="font-size: 0.75rem;">#<?php echo e($leaveRequest->id); ?></div>
                        </div>
                        <div class="col-md-6 mb-1">
                            <div class="text-muted" style="font-size: 0.6rem;">Created At</div>
                            <div style="font-size: 0.75rem;"><?php echo e($leaveRequest->created_at->format('d M Y H:i:s')); ?></div>
                        </div>
                    </div>
                    <div class="row g-1">
                        <div class="col-md-6 mb-1">
                            <div class="text-muted" style="font-size: 0.6rem;">Last Updated</div>
                            <div style="font-size: 0.75rem;"><?php echo e($leaveRequest->updated_at->format('d M Y H:i:s')); ?></div>
                        </div>
                        <div class="col-md-6 mb-1">
                            <div class="text-muted" style="font-size: 0.6rem;">Tenant ID</div>
                            <div style="font-size: 0.75rem;"><?php echo e($leaveRequest->tenant_id ?? 'N/A'); ?></div>
                        </div>
                    </div>
                    <?php if($leaveRequest->rejection_reason): ?>
                    <div class="alert alert-danger py-1 px-2 mt-1 rounded-sm" style="font-size: 0.65rem;">
                        <i class="ti ti-alert-circle me-1"></i>
                        <strong>Rejection Reason:</strong> <?php echo e($leaveRequest->rejection_reason); ?>

                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- ============================================ -->
        <!-- RIGHT COLUMN - LETTER & INFO -->
        <!-- ============================================ -->
        <div class="col-lg-5">
            
            <!-- Status Card -->
            <div class="card rounded-sm mb-2">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-flag text-primary me-1" style="font-size: 0.7rem;"></i>
                        <span style="font-weight: 600; font-size: 0.7rem;">Request Status</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2 text-center">
                    <?php if($leaveRequest->status == 'approved'): ?>
                        <div class="badge bg-success-subtle text-success rounded-sm p-2" style="font-size: 0.8rem;">
                            <i class="ti ti-check-circle me-1"></i> FULLY APPROVED
                        </div>
                        <div class="text-muted mt-1" style="font-size: 0.6rem;">
                            Finalized on <?php echo e($leaveRequest->updated_at->format('d M Y')); ?>

                        </div>
                    <?php elseif($leaveRequest->status == 'rejected'): ?>
                        <div class="badge bg-danger-subtle text-danger rounded-sm p-2" style="font-size: 0.8rem;">
                            <i class="ti ti-x-circle me-1"></i> REJECTED
                        </div>
                        <div class="text-muted mt-1" style="font-size: 0.6rem;">
                            Rejected on <?php echo e($leaveRequest->updated_at->format('d M Y')); ?>

                        </div>
                    <?php else: ?>
                        <div class="badge bg-warning-subtle text-warning rounded-sm p-2" style="font-size: 0.8rem;">
                            <i class="ti ti-clock me-1"></i> PENDING
                        </div>
                        <div class="text-muted mt-1" style="font-size: 0.6rem;">
                            Current Level: 
                            <?php
                                $levels = [1 => 'HOD', 2 => 'Dean', 3 => 'HR', 4 => 'DVC', 5 => 'HR Logistics'];
                            ?>
                            <?php echo e($levels[$leaveRequest->current_approval_level] ?? 'Processing'); ?>

                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- ============================================ -->
            <!-- PAYMENT INFORMATION CARD - NEW ADDITION -->
            <!-- ============================================ -->
            <?php if($leaveRequest->payment_status): ?>
            <div class="card rounded-sm mb-2">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-currency-dollar text-primary me-1" style="font-size: 0.7rem;"></i>
                        <span style="font-weight: 600; font-size: 0.7rem;">💰 Payment Information</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <div class="row g-1">
                        <div class="col-md-12 mb-1">
                            <div class="text-muted" style="font-size: 0.6rem;">Payment Status</div>
                            <?php if($leaveRequest->payment_status == 'paid'): ?>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-success-subtle text-success rounded-pill" style="font-size: 0.7rem;">
                                        <i class="ti ti-check-circle me-1"></i> Fully Paid
                                    </span>
                                    <span class="text-muted" style="font-size: 0.6rem;">100% of salary</span>
                                </div>
                            <?php elseif($leaveRequest->payment_status == 'partial'): ?>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-warning-subtle text-warning rounded-pill" style="font-size: 0.7rem;">
                                        <i class="ti ti-alert-triangle me-1"></i> Partially Paid
                                    </span>
                                    <span class="text-muted" style="font-size: 0.6rem;"><?php echo e($leaveRequest->payment_percentage); ?>% of salary</span>
                                </div>
                            <?php else: ?>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-danger-subtle text-danger rounded-pill" style="font-size: 0.7rem;">
                                        <i class="ti ti-x-circle me-1"></i> Unpaid
                                    </span>
                                    <span class="text-muted" style="font-size: 0.6rem;">No salary during leave</span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <?php if($leaveRequest->payment_notes): ?>
                    <div class="mt-2">
                        <div class="text-muted" style="font-size: 0.6rem;">Decision Reason:</div>
                        <div class="p-1 bg-light rounded-sm" style="font-size: 0.65rem;"><?php echo e($leaveRequest->payment_notes); ?></div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if($leaveRequest->payment_decided_by): ?>
                    <div class="text-muted mt-1" style="font-size: 0.55rem;">
                        <i class="ti ti-user-check me-1"></i>
                        Decided by: <?php echo e($leaveRequest->paymentDecidedBy->full_name ?? 'HR Officer'); ?> 
                        on <?php echo e(\Carbon\Carbon::parse($leaveRequest->payment_decision_at)->format('d M Y')); ?>

                    </div>
                    <?php endif; ?>
                    
                    <!-- Financial Impact Calculator -->
                    <?php if($leaveRequest->staff->basic_salary && $leaveRequest->payment_status != 'paid'): ?>
                    <div class="mt-2 p-2 bg-light rounded-sm">
                        <div class="text-muted" style="font-size: 0.55rem;">Financial Impact:</div>
                        <div class="small">
                            Daily rate: TZS <?php echo e(number_format($leaveRequest->staff->basic_salary / 30, 2)); ?><br>
                            <?php if($leaveRequest->payment_status == 'partial'): ?>
                            Total deduction: TZS <?php echo e(number_format(($leaveRequest->staff->basic_salary / 30) * $leaveRequest->days * ((100 - $leaveRequest->payment_percentage) / 100), 2)); ?>

                            <?php else: ?>
                            Total deduction: TZS <?php echo e(number_format(($leaveRequest->staff->basic_salary / 30) * $leaveRequest->days, 2)); ?>

                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Official Letter Card (Only for Approved) -->
            <?php if($leaveRequest->status === 'approved'): ?>
            <div class="card rounded-sm mb-2" id="letterCard">
                <div class="card-header py-1 px-2 bg-light d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-file-text text-primary me-1" style="font-size: 0.7rem;"></i>
                        <span style="font-weight: 600; font-size: 0.7rem;">Official Approval Letter</span>
                    </div>
                    <div>
                        <button type="button" onclick="window.print();" class="btn btn-sm btn-outline-primary rounded-sm" style="font-size: 0.55rem; padding: 0.1rem 0.3rem;">
                            <i class="ti ti-printer"></i> Print
                        </button>
                        <button type="button" onclick="downloadPDF();" class="btn btn-sm btn-outline-success rounded-sm" style="font-size: 0.55rem; padding: 0.1rem 0.3rem;">
                            <i class="ti ti-download"></i> PDF
                        </button>
                    </div>
                </div>
                <div class="card-body p-2" id="letterContent">
                    <?php
                        $university = \App\Models\University::first();
                        $letter = $leaveRequest->letters()->latest()->first();
                    ?>
                    
                    <div class="letter-container" style="font-family: 'Times New Roman', serif; font-size: 0.75rem; line-height: 1.4;">
                        <?php if($letter): ?>
                            <!-- Letter Header -->
                            <div class="text-center mb-2">
                                <?php if($university && $university->logo): ?>
                                    <img src="<?php echo e(Storage::url($university->logo)); ?>" alt="University Logo" style="max-height: 60px; max-width: 100%;">
                                <?php endif; ?>
                                <h5 style="font-size: 0.9rem; margin: 0.2rem 0;"><?php echo e($university->name ?? 'University'); ?></h5>
                                <p style="font-size: 0.6rem; margin: 0;"><?php echo e($university->address ?? ''); ?></p>
                                <hr style="margin: 0.2rem 0;">
                            </div>
                            
                            <!-- Letter Reference -->
                            <div class="mb-2">
                                <strong>Ref:</strong> <?php echo e($letter->letter_number ?? 'N/A'); ?><br>
                                <strong>Date:</strong> <?php echo e($letter->created_at->format('d M Y')); ?>

                            </div>
                            
                            <!-- Letter Subject -->
                            <div class="mb-2">
                                <strong>SUBJECT: APPROVAL OF LEAVE REQUEST</strong>
                            </div>
                            
                            <!-- Letter Body -->
                            <div class="mb-2">
                                <p>Dear <?php echo e($leaveRequest->staff->full_name ?? 'Staff Member'); ?>,</p>
                                <p>We are pleased to inform you that your request for <strong><?php echo e($leaveRequest->leaveType->name ?? 'Leave'); ?></strong> has been <strong>APPROVED</strong> as follows:</p>
                                
                                <ul style="margin: 0.2rem 0; padding-left: 1rem;">
                                    <li><strong>Leave Type:</strong> <?php echo e($leaveRequest->leaveType->name ?? 'N/A'); ?></li>
                                    <li><strong>Period:</strong> <?php echo e(\Carbon\Carbon::parse($leaveRequest->start_date)->format('d M Y')); ?> to <?php echo e(\Carbon\Carbon::parse($leaveRequest->end_date)->format('d M Y')); ?></li>
                                    <li><strong>Total Days:</strong> <?php echo e($leaveRequest->days); ?> day(s)</li>
                                    <li><strong>Acting Staff:</strong> <?php echo e($leaveRequest->actingStaff->full_name ?? 'Not specified'); ?></li>
                                </ul>
                                
                                <?php if($leaveRequest->handover_notes): ?>
                                    <p><strong>Handover Notes:</strong><br><?php echo e($leaveRequest->handover_notes); ?></p>
                                <?php endif; ?>
                                
                                <p>Please ensure proper handover with your designated acting staff before proceeding on leave.</p>
                                <p>We wish you a pleasant break and look forward to your return on <?php echo e(\Carbon\Carbon::parse($leaveRequest->expected_return_date ?? $leaveRequest->end_date)->addDay()->format('d M Y')); ?>.</p>
                            </div>
                            
                            <!-- Signatory -->
                            <div class="text-end mt-3">
                                <p>Yours sincerely,</p>
                                <div class="mt-2" style="margin-top: 2rem !important;">
                                    <p><strong><?php echo e($letter->generated_by ?? 'Human Resources Department'); ?></strong><br>
                                    <?php echo e($letter->generated_by_role ?? 'HR Officer'); ?></p>
                                </div>
                            </div>
                            
                            <!-- Footer -->
                            <hr style="margin: 0.2rem 0;">
                            <div class="text-center" style="font-size: 0.55rem;">
                                <i class="ti ti-lock"></i> Secure Document - Verification Code: <?php echo e($letter->secure_code ?? 'N/A'); ?>

                            </div>
                        <?php else: ?>
                            <div class="alert alert-info py-1 px-2 rounded-sm text-center" style="font-size: 0.7rem;">
                                <i class="ti ti-file-text me-1"></i>
                                Letter will be generated upon HR Logistics completion.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Quick Information Card -->
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-tips text-warning me-1" style="font-size: 0.7rem;"></i>
                        <span style="font-weight: 600; font-size: 0.7rem;">Quick Information</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    
                    <!-- Your Decision / Your Role Section -->
                    <div class="alert alert-light border mb-2 py-1 px-2 rounded-sm">
                        <div class="d-flex align-items-start gap-1">
                            <i class="ti ti-user-check text-success mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                <?php
                                    $user = auth()->user();
                                    $role = $user->getRoleNames()->first();
                                    
                                    // Check if user approved this request
                                    $userApproval = $leaveRequest->approvalLogs
                                        ->where('approver_staff_id', $user->id)
                                        ->where('action', 'approved')
                                        ->first();
                                    
                                    // Check if user rejected this request
                                    $userRejection = $leaveRequest->approvalLogs
                                        ->where('approver_staff_id', $user->id)
                                        ->where('action', 'rejected')
                                        ->first();
                                    
                                    // Check if user is the requester
                                    $isRequester = ($leaveRequest->staff_id == $user->id);
                                    
                                    // Check if user is the acting staff
                                    $isActingStaff = ($leaveRequest->acting_staff_id == $user->id);
                                    
                                    // Determine the message
                                    if ($userApproval) {
                                        $actionText = 'Approved';
                                        $actionIcon = 'check-circle';
                                        $actionColor = 'success';
                                        $actionDate = $userApproval->created_at;
                                        $actionBy = 'You approved';
                                    } elseif ($userRejection) {
                                        $actionText = 'Rejected';
                                        $actionIcon = 'x-circle';
                                        $actionColor = 'danger';
                                        $actionDate = $userRejection->created_at;
                                        $actionBy = 'You rejected';
                                    } elseif ($isRequester) {
                                        $actionText = 'Submitted';
                                        $actionIcon = 'file';
                                        $actionColor = 'primary';
                                        $actionDate = $leaveRequest->created_at;
                                        $actionBy = 'You requested';
                                    } elseif ($isActingStaff && $leaveRequest->acting_responded_at) {
                                        $actionText = ucfirst($leaveRequest->acting_status);
                                        $actionIcon = $leaveRequest->acting_status == 'accepted' ? 'check' : 'x';
                                        $actionColor = $leaveRequest->acting_status == 'accepted' ? 'success' : 'danger';
                                        $actionDate = $leaveRequest->acting_responded_at;
                                        $actionBy = 'You responded';
                                    } else {
                                        $actionText = 'Viewed';
                                        $actionIcon = 'eye';
                                        $actionColor = 'secondary';
                                        $actionDate = now();
                                        $actionBy = 'You viewed';
                                    }
                                ?>
                                
                                <strong class="d-block" style="font-size: 0.65rem;">Your Role: <?php echo e(ucfirst($role)); ?></strong>
                                <div class="mt-1">
                                    <i class="ti ti-<?php echo e($actionIcon); ?> text-<?php echo e($actionColor); ?> me-1" style="font-size: 0.65rem;"></i>
                                    <span class="text-<?php echo e($actionColor); ?> fw-semibold" style="font-size: 0.65rem;">
                                        <?php echo e($actionBy); ?> this request
                                    </span>
                                    <span class="text-muted" style="font-size: 0.6rem;">
                                        on <?php echo e(\Carbon\Carbon::parse($actionDate)->format('d M Y')); ?>

                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Document Access -->
                    <div class="alert alert-light border mb-2 py-1 px-2 rounded-sm">
                        <div class="d-flex align-items-start gap-1">
                            <i class="ti ti-file me-1 text-primary"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">Document Access</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">
                                    <?php if(auth()->check() && auth()->user()->hasAnyRole('admin|hr-officer|dvca|dvc-pfa|vice-chancellor')): ?>
                                        You have full access to all documents and financial details.
                                    <?php elseif($isRequester): ?>
                                        You can view your own documents and the approval letter.
                                    <?php elseif($isActingStaff): ?>
                                        You can view the leave details and acting status.
                                    <?php else: ?>
                                        You can view basic request details and the approval letter.
                                    <?php endif; ?>
                                </small>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Need Help -->
                    <div class="alert alert-light border mb-0 py-1 px-2 rounded-sm">
                        <div class="d-flex align-items-start gap-1">
                            <i class="ti ti-clock text-primary mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">Need Help?</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">
                                    Contact HR department for any questions about this request.
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style media="print">
    .sidebar-area, .topbar, .page-header, .btn, .breadcrumb, .card-header .btn, .sticky-banner {
        display: none !important;
    }
    .content-area {
        margin: 0 !important;
        padding: 0 !important;
    }
    .main-content {
        padding: 0 !important;
    }
    .card {
        border: none !important;
    }
    .card-header {
        display: none !important;
    }
    .letter-container {
        font-size: 12pt !important;
    }
    body {
        background: white !important;
    }
    .container-fluid {
        padding: 0 !important;
    }
</style>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js" integrity="sha512-GsLlZN/3F2ErC5ifS5QtgpiJtWd43JWSuIgh7mbzZ8zBps+dvLusV+eNQATqgA/HdeKFVgA5v3S/cIrLF7QnIg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script>
function downloadPDF() {
    const element = document.getElementById('letterContent');
    const opt = {
        margin: [0.5, 0.5, 0.5, 0.5],
        filename: 'Leave_Approval_Letter_<?php echo e($leaveRequest->id); ?>.pdf',
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2, letterRendering: true },
        jsPDF: { unit: 'in', format: 'a4', orientation: 'portrait' }
    };
    html2pdf().set(opt).from(element).save();
}
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\wamp64\www\unileave\resources\views/shared/leave-requests/approval-details.blade.php ENDPATH**/ ?>