<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Get unread count for current user (for AJAX)
     */
    public function unreadCount()
    {
        $count = Notification::forUser(auth()->id())->unread()->count();
        return response()->json(['count' => $count]);
    }

    /**
     * Get all notifications for current user
     */
    public function index()
    {
        $notifications = Notification::forUser(auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('notifications.index', compact('notifications'));
    }

    /**
     * Mark a single notification as read
     */
    public function markAsRead($id)
    {
        $notification = Notification::forUser(auth()->id())->findOrFail($id);
        $notification->markAsRead();
        
        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }
        
        return redirect()->back()->with('success', 'Notification marked as read');
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        Notification::forUser(auth()->id())->unread()->update([
            'is_read' => true,
            'read_at' => now()
        ]);
        
        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }
        
        return redirect()->back()->with('success', 'All notifications marked as read');
    }

    /**
     * Delete a notification
     */
    public function destroy($id)
    {
        $notification = Notification::forUser(auth()->id())->findOrFail($id);
        $notification->delete();
        
        return redirect()->back()->with('success', 'Notification deleted');
    }

    public function list()
{
    $notifications = Notification::forUser(auth()->id())
        ->orderBy('created_at', 'desc')
        ->limit(15)
        ->get()
        ->map(function($n) {
            return [
                'id' => $n->id,
                'title' => $n->title,
                'message' => $n->message,
                'action_url' => $n->action_url,
                'is_read' => $n->is_read,
                'time_ago' => $n->created_at->diffForHumans(),
                'icon' => $this->getIconForType($n->type),
                'icon_color' => $this->getIconColorForType($n->type),
            ];
        });
    
    return response()->json([
        'notifications' => $notifications,
        'unread_count' => Notification::forUser(auth()->id())->unread()->count(),
    ]);
}

private function getIconForType($type)
{
    return match($type) {
        'acting_request' => 'user-check',
        'approval_pending' => 'clock',
        'approval_granted' => 'check-circle',
        'approval_rejected' => 'x-circle',
        'hold_accepted' => 'user-check',
        'hold_declined' => 'user-x',
        'leave_approved_final' => 'calendar-check',
        default => 'bell',
    };
}

private function getIconColorForType($type)
{
    return match($type) {
        'acting_request' => 'text-warning',
        'approval_pending' => 'text-warning',
        'approval_granted' => 'text-success',
        'leave_approved_final' => 'text-success',
        'approval_rejected' => 'text-danger',
        'hold_declined' => 'text-danger',
        default => 'text-primary',
    };
}
}