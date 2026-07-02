<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['leaveRequest', 'showComments' => false]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['leaveRequest', 'showComments' => false]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $user = auth()->user();
    $role = $user->getRoleNames()->first();
    $authorizedRoles = ['admin', 'vice-chancellor', 'hod', 'dean', 'hr-officer', 'dvca', 'dvc-pfa'];
    $canSeeComments = in_array($role, $authorizedRoles);
    
    // Track completed stages from approval logs
    $completedLevels = [];
    foreach ($leaveRequest->approvalLogs->where('action', 'approved') as $log) {
        $fromRole = strtolower($log->from_role);
        if ($fromRole == 'hod') $completedLevels[1] = true;
        if ($fromRole == 'dean') $completedLevels[2] = true;
        if ($fromRole == 'hr') $completedLevels[3] = true;
        if (in_array($fromRole, ['dvca', 'dvc-pfa', 'dvc'])) $completedLevels[4] = true;
        if ($fromRole == 'hr_logistics') $completedLevels[5] = true;
    }
    
    $currentLevelInt = (int)$leaveRequest->current_approval_level;
    $actingStatus = $leaveRequest->acting_status;
    $isFullyApproved = $leaveRequest->status == 'approved';
    
    $stages = [
        ['key' => 'acting', 'label' => 'Acting Staff', 'description' => 'Staff member to hold position', 'level' => null],
        ['key' => 'hod', 'label' => 'HOD', 'description' => 'Head of Department approval', 'level' => 1],
        ['key' => 'dean', 'label' => 'Dean', 'description' => 'Dean of School approval', 'level' => 2],
        ['key' => 'hr', 'label' => 'HR', 'description' => 'Human Resources review', 'level' => 3],
        ['key' => 'dvc', 'label' => 'DVC', 'description' => 'Deputy Vice Chancellor approval', 'level' => 4],
        ['key' => 'hr_final', 'label' => 'HR Logistics', 'description' => 'Final processing', 'level' => 5],
    ];
    
    // Determine status for each stage
    foreach ($stages as $i => $stage) {
        if ($stage['key'] == 'acting') {
            if ($actingStatus == 'accepted') {
                $stages[$i]['status'] = 'completed';
                $stages[$i]['status_text'] = 'Accepted';
                $stages[$i]['date'] = $leaveRequest->acting_responded_at;
                $stages[$i]['by'] = $leaveRequest->actingStaff?->full_name ?? 'Unknown';
            } elseif ($actingStatus == 'pending') {
                $stages[$i]['status'] = 'current';
                $stages[$i]['status_text'] = 'Awaiting Response';
            } else {
                $stages[$i]['status'] = 'pending';
                $stages[$i]['status_text'] = 'Not Started';
            }
        } else {
            $level = $stage['level'];
            if (isset($completedLevels[$level])) {
                $stages[$i]['status'] = 'completed';
                $stages[$i]['status_text'] = 'Approved';
                $log = $leaveRequest->approvalLogs->where('action', 'approved')->first(function($log) use ($level) {
                    $fromRole = strtolower($log->from_role);
                    if ($level == 1) return $fromRole == 'hod';
                    if ($level == 2) return $fromRole == 'dean';
                    if ($level == 3) return $fromRole == 'hr';
                    if ($level == 4) return in_array($fromRole, ['dvca', 'dvc-pfa', 'dvc']);
                    if ($level == 5) return $fromRole == 'hr_logistics';
                    return false;
                });
                if ($log) {
                    $stages[$i]['date'] = $log->created_at;
                    $stages[$i]['by'] = $log->approver?->full_name ?? 'System';
                    // Only store comment if user is authorized to see it
                    if ($canSeeComments && $log->comment) {
                        $stages[$i]['comment'] = $log->comment;
                    }
                }
            } elseif ($currentLevelInt == $level && !$isFullyApproved) {
                $stages[$i]['status'] = 'current';
                $stages[$i]['status_text'] = 'In Progress';
            } elseif ($isFullyApproved) {
                $stages[$i]['status'] = 'completed';
                $stages[$i]['status_text'] = 'Approved';
            } else {
                $stages[$i]['status'] = 'pending';
                $stages[$i]['status_text'] = 'Pending';
            }
        }
    }
?>

<div class="approval-tracker-full">
    <div class="timeline-horizontal">
        <?php $__currentLoopData = $stages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $stage): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="timeline-stage <?php echo e($stage['status']); ?>">
                <div class="timeline-marker">
                    <?php if($stage['status'] == 'completed'): ?>
                        <i class="ti ti-check"></i>
                    <?php elseif($stage['status'] == 'current'): ?>
                        <i class="ti ti-loader"></i>
                    <?php else: ?>
                        <i class="ti ti-circle"></i>
                    <?php endif; ?>
                </div>
                <div class="timeline-content">
                    <div class="timeline-title"><?php echo e($stage['label']); ?></div>
                    <div class="timeline-desc"><?php echo e($stage['description']); ?></div>
                    <?php if($stage['status'] == 'completed' && isset($stage['date'])): ?>
                        <div class="timeline-meta">
                            <span class="timeline-date"><?php echo e(\Carbon\Carbon::parse($stage['date'])->format('d M Y H:i')); ?></span>
                            <?php if(isset($stage['by'])): ?>
                                <span class="timeline-by">by <?php echo e($stage['by']); ?></span>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    <?php if($stage['status'] == 'current'): ?>
                        <div class="timeline-current-badge">Waiting</div>
                    <?php endif; ?>
                    
                    <?php if($stage['status'] == 'completed' && isset($stage['comment']) && $stage['comment']): ?>
                        <div class="timeline-comment mt-1">"<?php echo e($stage['comment']); ?>"</div>
                    <?php endif; ?>
                </div>
            </div>
            <?php if(!$loop->last): ?>
                <div class="timeline-connector <?php echo e($stage['status'] == 'completed' ? 'completed' : ''); ?>"></div>
            <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</div>

<style>
.approval-tracker-full {
    padding: 0.5rem 0;
}

.timeline-horizontal {
    display: flex;
    flex-wrap: wrap;
    align-items: flex-start;
    gap: 0.5rem;
}

.timeline-stage {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    flex: 1;
    min-width: 80px;
    position: relative;
}

.timeline-marker {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.85rem;
    margin-bottom: 0.5rem;
    transition: all 0.2s ease;
}

.timeline-stage.completed .timeline-marker {
    background: #10B981;
    color: white;
}

.timeline-stage.current .timeline-marker {
    background: #3B82F6;
    color: white;
    animation: fullPulse 1.5s infinite;
}

.timeline-stage.pending .timeline-marker {
    background: #E2E8F0;
    color: #94A3B8;
    border: 1px solid #CBD5E1;
}

.timeline-content {
    width: 100%;
}

.timeline-title {
    font-weight: 600;
    font-size: 0.7rem;
    color: #1E293B;
}

.timeline-desc {
    font-size: 0.55rem;
    color: #64748B;
    margin-top: 0.1rem;
}

.timeline-meta {
    margin-top: 0.25rem;
    font-size: 0.5rem;
    color: #10B981;
}

.timeline-date {
    display: inline-block;
}

.timeline-by {
    display: inline-block;
    margin-left: 0.25rem;
}

.timeline-current-badge {
    margin-top: 0.25rem;
    font-size: 0.5rem;
    background: #DBEAFE;
    color: #1E40AF;
    display: inline-block;
    padding: 0.1rem 0.4rem;
    border-radius: 4px;
}

.timeline-comment {
    font-size: 0.55rem;
    color: #64748B;
    font-style: italic;
    background: #F8FAFC;
    padding: 0.2rem 0.4rem;
    border-radius: 4px;
    margin-top: 0.25rem;
    max-width: 150px;
    word-wrap: break-word;
}

.timeline-connector {
    width: 20px;
    height: 2px;
    background: #E2E8F0;
    margin-top: 15px;
}

.timeline-connector.completed {
    background: #10B981;
}

@keyframes fullPulse {
    0% {
        transform: scale(1);
        box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.4);
    }
    70% {
        transform: scale(1.1);
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0);
    }
    100% {
        transform: scale(1);
        box-shadow: 0 0 0 0 rgba(59, 130, 246, 0);
    }
}

@media (max-width: 768px) {
    .timeline-horizontal {
        flex-direction: column;
        gap: 0.75rem;
    }
    .timeline-stage {
        flex-direction: row;
        text-align: left;
        gap: 0.75rem;
        min-width: 100%;
    }
    .timeline-marker {
        margin-bottom: 0;
    }
    .timeline-connector {
        display: none;
    }
    .timeline-content {
        flex: 1;
    }
}
</style><?php /**PATH C:\wamp64\www\unileave\resources\views/components/approval-tracker-full.blade.php ENDPATH**/ ?>