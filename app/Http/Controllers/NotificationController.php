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
     * Get all notifications for current user, with an optional read/unread
     * filter and an optional selected notification shown in the detail pane.
     * Opening a notification here marks it read as a side effect, same as
     * clicking it in the topbar dropdown.
     */
    public function index(Request $request)
    {
        $userId = auth()->id();
        $filter = $request->get('filter', 'all');

        $query = Notification::forUser($userId);
        if ($filter === 'unread') {
            $query->unread();
        } elseif ($filter === 'read') {
            $query->read();
        }

        $notifications = $query->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        $selected = null;
        if ($request->filled('notification')) {
            $selected = Notification::forUser($userId)->find($request->integer('notification'));
            if ($selected && ! $selected->is_read) {
                $selected->markAsRead();
            }
        }

        $counts = [
            'all' => Notification::forUser($userId)->count(),
            'unread' => Notification::forUser($userId)->unread()->count(),
            'read' => Notification::forUser($userId)->read()->count(),
            'urgent' => Notification::forUser($userId)->unread()->whereIn('priority', ['high', 'urgent'])->count(),
        ];

        return view('notifications.index', compact('notifications', 'selected', 'filter', 'counts'));
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
            'read_at' => now(),
        ]);

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'All notifications marked as read');
    }

    /**
     * Soft-delete a notification (SoftDeletes on the model) so it drops out
     * of every list/count without losing the record.
     */
    public function destroy(Request $request, $id)
    {
        $notification = Notification::forUser(auth()->id())->findOrFail($id);
        $notification->delete();

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('notifications.index', ['filter' => $request->get('filter', 'all')])
            ->with('success', 'Notification removed');
    }

    /**
     * Soft-delete several notifications at once (SoftDeletes on the model),
     * scoped to the current user so nobody can pass in someone else's IDs.
     */
    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer',
        ]);

        $count = Notification::forUser(auth()->id())
            ->whereIn('id', $request->input('ids'))
            ->delete();

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'count' => $count]);
        }

        return redirect()->route('notifications.index', ['filter' => $request->get('filter', 'all')])
            ->with('success', $count === 1 ? '1 notification removed' : "{$count} notifications removed");
    }

    public function list()
    {
        $notifications = Notification::forUser(auth()->id())
            ->orderBy('created_at', 'desc')
            ->limit(15)
            ->get()
            ->map(function ($n) {
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
        return match ($type) {
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
        return match ($type) {
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
