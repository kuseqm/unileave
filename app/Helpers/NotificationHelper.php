<?php

use App\Models\Notification;
use Carbon\Carbon;

// ============================================
// CORE NOTIFICATION FUNCTION
// ============================================

if (! function_exists('sendNotification')) {
    function sendNotification($userId, $type, $title, $message, $actionUrl = null, $actionText = null, $data = [])
    {
        return Notification::create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'action_url' => $actionUrl,
            'action_text' => $actionText,
            'data' => $data,
            'is_read' => false,
        ]);
    }
}

// ============================================
// LEAVE REQUEST NOTIFICATIONS
// ============================================

if (! function_exists('notifyActingStaff')) {
    function notifyActingStaff($leaveRequest)
    {
        $actingStaff = $leaveRequest->actingStaff;
        $requester = $leaveRequest->staff;

        $actionUrl = route('leave-requests.acting.response', $leaveRequest->acting_token);

        sendNotification(
            $actingStaff->id,
            'acting_request',
            'Hold Position Request',
            "{$requester->first_name} {$requester->last_name} has requested you to hold their position during leave from ".
            Carbon::parse($leaveRequest->start_date)->format('d M Y').' to '.
            Carbon::parse($leaveRequest->end_date)->format('d M Y'),
            $actionUrl,
            'Review Request'
        );
    }
}

if (! function_exists('notifyApprover')) {
    function notifyApprover($leaveRequest, $approverRole, $approverId)
    {
        $roleNames = [
            'hod' => 'Head of Department',
            'dean' => 'Dean',
            'dvca' => 'DVC Academic',
            'dvc_pfa' => 'DVC PFA',
            'hr_logistics' => 'HR Officer',
        ];

        $roleName = $roleNames[$approverRole] ?? ucfirst($approverRole);

        sendNotification(
            $approverId,
            'approval_pending',
            'Leave Request Pending Approval',
            "Leave request from {$leaveRequest->staff->first_name} {$leaveRequest->staff->last_name} requires your approval as {$roleName}",
            route('leave-requests.pending'),
            'Review Request'
        );
    }
}

if (! function_exists('notifyRequestStatus')) {
    function notifyRequestStatus($leaveRequest, $status)
    {
        $messages = [
            'approved' => 'Your leave request has been approved',
            'rejected' => 'Your leave request has been rejected',
            'cancelled' => 'Your leave request has been cancelled',
            'fully_approved' => 'Your leave request has been fully approved',
        ];

        sendNotification(
            $leaveRequest->staff_id,
            $status == 'approved' ? 'approval_granted' : 'approval_rejected',
            $messages[$status] ?? 'Leave Request Update',
            $messages[$status] ?? 'Your leave request has been updated',
            route('leave-requests.show', $leaveRequest->id),
            'View Details'
        );
    }
}

// ============================================
// LETTER NOTIFICATIONS
// ============================================

if (! function_exists('notifyLetterApprover')) {
    function notifyLetterApprover($letter, $approverId, $level)
    {
        $levelNames = [
            'hod' => 'Head of Department',
            'dean' => 'Dean of Faculty',
            'dvca' => 'DVC Academic',
            'dvc-pfa' => 'DVC PFA',
            'vice-chancellor' => 'Vice Chancellor',
        ];

        $levelName = $levelNames[$level] ?? ucfirst($level);
        $isSignature = ($level === 'vice-chancellor');

        sendNotification(
            $approverId,
            $isSignature ? 'letter_pending_signature' : 'letter_pending_approval',
            $isSignature ? '✍️ Letter Pending Your Signature' : '📨 Letter Pending Your Approval',
            "Letter '{$letter->title}' from {$letter->sender->full_name} requires your ".($isSignature ? 'digital signature' : 'approval')." as {$levelName}.",
            $isSignature ? route('letters.pending.signature') : route('letters.pending.approval'),
            $isSignature ? 'Sign Letter' : 'Review Letter',
            ['letter_id' => $letter->id, 'level' => $level]
        );
    }
}

if (! function_exists('notifyLetterSender')) {
    function notifyLetterSender($letter, $status, $level = null)
    {
        $messages = [
            'submitted' => "📨 Your letter '{$letter->title}' has been submitted for approval.",
            'hod_approved' => "✅ Your letter '{$letter->title}' has been approved by HOD. Forwarded to Dean.",
            'dean_approved' => "✅ Your letter '{$letter->title}' has been approved by Dean. Forwarded to DVC.",
            'dvc_approved' => "✅ Your letter '{$letter->title}' has been approved by DVC. Forwarded to VC for signature.",
            'signed' => "🎉 Your letter '{$letter->title}' has been digitally signed by the Vice Chancellor. It is now official.",
            'rejected' => "❌ Your letter '{$letter->title}' has been rejected.",
        ];

        $notificationTypes = [
            'submitted' => 'letter_progress_update',
            'hod_approved' => 'letter_progress_update',
            'dean_approved' => 'letter_progress_update',
            'dvc_approved' => 'letter_progress_update',
            'signed' => 'letter_signed',
            'rejected' => 'letter_rejected',
        ];

        sendNotification(
            $letter->sender_id,
            $notificationTypes[$status] ?? 'letter_progress_update',
            $messages[$status] ?? 'Letter Update',
            $messages[$status] ?? 'Your letter has been updated',
            route('letters.show', $letter->id),
            'View Letter',
            ['letter_id' => $letter->id, 'status' => $status]
        );
    }
}

if (! function_exists('notifyLetterCompleted')) {
    function notifyLetterCompleted($letter, $approverIds)
    {
        foreach ($approverIds as $approverId) {
            if ($approverId) {
                sendNotification(
                    $approverId,
                    'letter_completed',
                    '✅ Letter Process Completed',
                    "Letter '{$letter->title}' has been fully signed and is now official.",
                    route('letters.show', $letter->id),
                    'View Letter',
                    ['letter_id' => $letter->id]
                );
            }
        }
    }
}

// ============================================
// GENERAL NOTIFICATION HELPERS
// ============================================

if (! function_exists('getUnreadNotificationsCount')) {
    function getUnreadNotificationsCount($userId)
    {
        return Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->count();
    }
}

if (! function_exists('markNotificationAsRead')) {
    function markNotificationAsRead($notificationId, $userId)
    {
        $notification = Notification::where('id', $notificationId)
            ->where('user_id', $userId)
            ->first();

        if ($notification) {
            $notification->update(['is_read' => true, 'read_at' => now()]);

            return true;
        }

        return false;
    }
}

if (! function_exists('markAllNotificationsAsRead')) {
    function markAllNotificationsAsRead($userId)
    {
        return Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);
    }
}
