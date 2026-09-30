<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use App\Models\Message;
use App\Models\MessageAction;
use App\Models\MessageAttachment;
use App\Models\MessageConversation;
use App\Models\MessageTemplate;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class MessageController extends Controller
{
    /**
     * Display inbox messages
     */
    public function inbox(Request $request)
    {
        $userId = auth()->id();

        $query = Message::where('receiver_id', $userId)
            ->where('status', '!=', 'draft')
            ->where('is_archived', false)
            ->orderByRaw("FIELD(priority, 'urgent', 'high', 'normal', 'low')")
            ->orderBy('created_at', 'desc');

        $grandTotal = (clone $query)->count();

        // Apply filters
        if ($request->filled('type') && $request->type != 'all') {
            $query->where('message_type', $request->type);
        }

        if ($request->filled('priority') && $request->priority != 'all') {
            $query->where('priority', $request->priority);
        }

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                    ->orWhere('message', 'like', "%{$search}%")
                    ->orWhereHas('sender', function ($sq) use ($search) {
                        $sq->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%");
                    });
            });
        }

        $messages = $query->paginate($request->get('per_page', 20))->appends($request->query());
        $unreadCount = Message::where('receiver_id', $userId)
            ->where('is_read', false)
            ->where('is_archived', false)
            ->count();
        $thisWeekCount = Message::where('receiver_id', $userId)
            ->where('status', '!=', 'draft')
            ->where('is_archived', false)
            ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->count();

        if ($request->wantsJson()) {
            return response()->json([
                'html' => view('shared.messages._inbox-results', compact('messages', 'unreadCount'))->render(),
                'total' => $messages->total(),
                'grand_total' => $grandTotal,
            ]);
        }

        return view('shared.messages.inbox', compact('messages', 'unreadCount', 'thisWeekCount'));
    }

    /**
     * Display sent messages
     */
    public function sent(Request $request)
    {
        $userId = auth()->id();
        $query = Message::where('sender_id', $userId)
            ->where('status', '!=', 'draft')
            ->orderBy('created_at', 'desc');

        $grandTotal = (clone $query)->count();

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                    ->orWhere('message', 'like', "%{$search}%")
                    ->orWhereHas('receiver', function ($sq) use ($search) {
                        $sq->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%");
                    });
            });
        }

        $messages = $query->paginate($request->get('per_page', 20))->appends($request->query());

        if ($request->wantsJson()) {
            return response()->json([
                'html' => view('shared.messages._sent-results', compact('messages'))->render(),
                'total' => $messages->total(),
                'grand_total' => $grandTotal,
            ]);
        }

        return view('shared.messages.sent', compact('messages'));
    }

    /**
     * Display drafts
     */
    public function drafts(Request $request)
    {
        $userId = auth()->id();
        $query = Message::where('sender_id', $userId)
            ->where('status', 'draft')
            ->orderBy('updated_at', 'desc');

        $grandTotal = (clone $query)->count();

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                    ->orWhere('message', 'like', "%{$search}%");
            });
        }

        $messages = $query->paginate($request->get('per_page', 20))->appends($request->query());

        if ($request->wantsJson()) {
            return response()->json([
                'html' => view('shared.messages._drafts-results', compact('messages'))->render(),
                'total' => $messages->total(),
                'grand_total' => $grandTotal,
            ]);
        }

        return view('shared.messages.drafts', compact('messages'));
    }

    /**
     * Show compose form
     */
    public function create(Request $request)
    {
        $users = User::where('id', '!=', auth()->id())->get();
        $selectedUser = $request->get('to', null);
        $templates = MessageTemplate::where('is_active', true)->get();
        $replyTo = null;
        $forwardMessage = null;
        $subject = '';
        $messageContent = '';
        $replyAllRecipients = [];

        // Handle Reply
        if ($request->has('reply_to')) {
            $replyTo = Message::with('sender')->find($request->reply_to);
            if ($replyTo && ! $replyTo->canViewContent(auth()->user())) {
                $replyTo = null;
            }
            if ($replyTo) {
                // Pre-select the original sender
                $selectedUser = $replyTo->sender_id;
                // Set subject with Re:
                $subject = 'Re: '.$replyTo->subject;
                // Optional: Quote original message
                $messageContent = "\n\n--- Original Message ---\nFrom: ".($replyTo->sender->full_name ?? $replyTo->sender->first_name).
                                "\nSent: ".$replyTo->created_at->format('d M Y, H:i').
                                "\nSubject: ".$replyTo->subject.
                                "\n\n".$replyTo->message;
            }
        }

        // Handle Reply All
        if ($request->has('reply_all')) {
            $replyTo = Message::with('sender')->find($request->reply_all);
            if ($replyTo && ! $replyTo->canViewContent(auth()->user())) {
                $replyTo = null;
            }
            if ($replyTo) {
                // Get all recipients except current user
                $replyAllRecipients = $replyTo->recipient_ids ?? [];
                if ($replyTo->sender_id != auth()->id()) {
                    $replyAllRecipients[] = $replyTo->sender_id;
                }
                // Remove current user from recipients
                $replyAllRecipients = array_unique(array_diff($replyAllRecipients, [auth()->id()]));
                $selectedUser = $replyAllRecipients[0] ?? null;
                $subject = 'Re: '.$replyTo->subject;
                $messageContent = "\n\n--- Original Message ---\nFrom: ".($replyTo->sender->full_name ?? $replyTo->sender->first_name).
                                "\nTo: ".($replyTo->recipient_names ?? 'Multiple recipients').
                                "\nSent: ".$replyTo->created_at->format('d M Y, H:i').
                                "\nSubject: ".$replyTo->subject.
                                "\n\n".$replyTo->message;
            }
        }

        // Handle Forward
        if ($request->has('forward')) {
            $forwardMessage = Message::with('sender')->find($request->forward);
            if ($forwardMessage && ! $forwardMessage->canViewContent(auth()->user())) {
                $forwardMessage = null;
            }
            if ($forwardMessage) {
                // No pre-selected recipients for forward
                $selectedUser = null;
                // Set subject with Fwd:
                $subject = 'Fwd: '.$forwardMessage->subject;
                // Quote the original message
                $messageContent = "\n\n--- Forwarded Message ---\nFrom: ".($forwardMessage->sender->full_name ?? $forwardMessage->sender->first_name).
                                "\nTo: ".($forwardMessage->recipient_names ?? $forwardMessage->receiver->first_name ?? 'Unknown').
                                "\nSent: ".$forwardMessage->created_at->format('d M Y, H:i').
                                "\nSubject: ".$forwardMessage->subject.
                                "\n\n".$forwardMessage->message;
            }
        }

        return view('shared.messages.compose', compact('users', 'selectedUser', 'templates', 'replyTo', 'forwardMessage', 'subject', 'messageContent', 'replyAllRecipients'));
    }

    /**
     * Store and send message (supports multiple recipients)
     */
    public function store(Request $request)
    {
        // FIX: Cast recipient_ids to integers
        if ($request->has('recipient_ids')) {
            $recipientIds = $request->recipient_ids;
            if (is_array($recipientIds)) {
                $recipientIds = array_map('intval', $recipientIds);
                $request->merge(['recipient_ids' => $recipientIds]);
            }
        }

        $request->validate([
            'recipient_ids' => 'required|array|min:1|max:20',
            'recipient_ids.*' => 'exists:staff,id',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'priority' => 'nullable|in:low,normal,high,urgent',
            'message_type' => 'nullable|in:general,minute,approval,task,escalation,notification',
            'deadline' => 'nullable|date',
            'attachments.*' => 'nullable|file|max:10240',
        ]);

        DB::beginTransaction();

        try {
            $recipientIds = $request->recipient_ids;
            $senderId = auth()->id();
            $createdMessages = [];

            // Create a separate message for each recipient
            foreach ($recipientIds as $recipientId) {
                // Get or create conversation FIRST
                $conversation = $this->getOrCreateConversation(
                    $senderId,
                    $recipientId,
                    $request->leave_request_id ?? null
                );

                // Create message with ALL required fields
                $message = Message::create([
                    'conversation_id' => $conversation->id,
                    'sender_id' => $senderId,
                    'receiver_id' => $recipientId,
                    'recipient_ids' => json_encode($recipientIds),
                    'leave_request_id' => $request->leave_request_id ?? null,
                    'message_number' => $this->generateMessageNumber(),
                    'subject' => $request->subject,
                    'message' => $request->message,
                    'message_type' => $request->message_type ?? 'general',
                    'priority' => $request->priority ?? 'normal',
                    'status' => 'sent',
                    'sent_at' => now(),
                    'delivered_at' => now(),
                    'is_read' => false,
                ]);

                // Handle attachments (only once)
                if ($request->hasFile('attachments') && empty($createdMessages)) {
                    foreach ($request->file('attachments') as $file) {
                        $path = $file->store('message_attachments/'.date('Y/m'), 'local');
                        MessageAttachment::create([
                            'message_id' => $message->id,
                            'file_name' => $file->getClientOriginalName(),
                            'file_path' => $path,
                            'file_size' => $file->getSize(),
                            'mime_type' => $file->getMimeType(),
                            'disk' => 'local',
                        ]);
                    }
                } elseif (! empty($createdMessages)) {
                    // Copy attachments from first message to others
                    $firstMessage = $createdMessages[0];
                    $attachments = MessageAttachment::where('message_id', $firstMessage->id)->get();
                    foreach ($attachments as $attachment) {
                        MessageAttachment::create([
                            'message_id' => $message->id,
                            'file_name' => $attachment->file_name,
                            'file_path' => $attachment->file_path,
                            'file_size' => $attachment->file_size,
                            'mime_type' => $attachment->mime_type,
                            'disk' => $attachment->disk,
                        ]);
                    }
                }

                // Log action
                MessageAction::create([
                    'message_id' => $message->id,
                    'user_id' => $senderId,
                    'action' => 'sent',
                ]);

                // Update conversation
                $conversation->update([
                    'last_message_at' => now(),
                    'is_active' => true,
                ]);

                $createdMessages[] = $message;
            }

            DB::commit();

            $recipientCount = count($recipientIds);

            return redirect()->route('messages.inbox')
                ->with('success', "Message sent successfully to {$recipientCount} recipient(s)!");

        } catch (\Exception $e) {
            DB::rollback();

            return back()->with('error', 'Failed to send message: '.$e->getMessage())->withInput();
        }
    }

    /**
     * Show a single message
     */
    public function show($id)
    {
        // Eager load ALL relationships including attachments
        $message = Message::with([
            'sender',
            'receiver',
            'attachments',  // ← THIS IS CRITICAL
            'actions.user',
            'conversation',
        ])->findOrFail($id);

        if (! $message->canViewContent(auth()->user())) {
            abort(403, 'You do not have permission to view this message.');
        }

        // Mark as read if user is receiver
        if ($message->receiver_id == auth()->id() && ! $message->is_read) {
            $message->markAsRead();
        }

        // Get conversation thread
        $thread = Message::where('conversation_id', $message->conversation_id)
            ->with(['sender', 'receiver'])
            ->orderBy('created_at', 'asc')
            ->get();

        return view('shared.messages.show', compact('message', 'thread'));
    }

    /**
     * Show conversation with a specific user
     */
    public function showForUser($userId)
    {
        $otherUser = User::findOrFail($userId);
        $currentUserId = auth()->id();

        // Find conversation between current user and this user
        $conversation = MessageConversation::where(function ($q) use ($currentUserId, $userId) {
            $q->whereJsonContains('participant_ids', (string) $currentUserId)
                ->whereJsonContains('participant_ids', (string) $userId);
        })->first();

        if (! $conversation) {
            // No messages yet, show empty conversation
            return view('shared.messages.conversation', compact('otherUser'));
        }

        $messages = Message::where('conversation_id', $conversation->id)
            ->with(['sender', 'receiver'])
            ->orderBy('created_at', 'asc')
            ->get();

        // Mark messages from other user as read
        Message::where('conversation_id', $conversation->id)
            ->where('receiver_id', $currentUserId)
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        return view('shared.messages.conversation', compact('messages', 'otherUser', 'conversation'));
    }

    /**
     * Show conversation for a leave request
     */
    public function showForLeaveRequest($leaveRequestId)
    {
        $leaveRequest = LeaveRequest::findOrFail($leaveRequestId);

        // Find conversation linked to this leave request
        $conversation = MessageConversation::where('leave_request_id', $leaveRequestId)->first();

        if (! $conversation) {
            return redirect()->route('messages.create', ['leave_request_id' => $leaveRequestId])
                ->with('info', 'Start a conversation about this leave request.');
        }

        if (! in_array((string) auth()->id(), $conversation->participant_ids ?? [])) {
            abort(403, 'You are not a participant in this conversation.');
        }

        $messages = Message::where('conversation_id', $conversation->id)
            ->with(['sender', 'receiver'])
            ->orderBy('created_at', 'asc')
            ->get();

        return view('shared.messages.conversation', compact('messages', 'conversation', 'leaveRequest'));
    }

    /**
     * Reply to a message
     */
    public function reply(Request $request, $id)
    {
        $request->validate([
            'message' => 'required|string',
            'attachments.*' => 'nullable|file|max:10240',
        ]);

        $parentMessage = Message::findOrFail($id);

        if (! $parentMessage->canViewContent(auth()->user())) {
            abort(403, 'You do not have permission to reply to this message.');
        }

        DB::beginTransaction();

        try {
            // Create reply message
            $reply = Message::create([
                'conversation_id' => $parentMessage->conversation_id,
                'sender_id' => auth()->id(),
                'receiver_id' => $parentMessage->sender_id,
                'parent_message_id' => $parentMessage->id,
                'message_number' => $this->generateMessageNumber(),
                'subject' => 'Re: '.$parentMessage->subject,
                'message' => $request->message,
                'message_type' => $parentMessage->message_type,
                'priority' => $parentMessage->priority,
                'status' => 'sent',
                'sent_at' => now(),
                'delivered_at' => now(),
                'is_read' => false,
            ]);

            // Mark parent as replied
            $parentMessage->update(['status' => 'replied', 'replied_at' => now()]);

            // Handle attachments
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('message_attachments/'.date('Y/m'), 'local');

                    MessageAttachment::create([
                        'message_id' => $reply->id,
                        'file_name' => $file->getClientOriginalName(),
                        'file_path' => $path,
                        'file_size' => $file->getSize(),
                        'mime_type' => $file->getMimeType(),
                        'disk' => 'local',
                    ]);
                }
            }

            // Update conversation
            $parentMessage->conversation->update([
                'last_message_at' => now(),
            ]);

            DB::commit();

            return redirect()->route('messages.show', $reply->id)
                ->with('success', 'Reply sent successfully!');

        } catch (\Exception $e) {
            DB::rollback();

            return back()->with('error', 'Failed to send reply: '.$e->getMessage());
        }
    }

    /**
     * Mark a message as read (legacy endpoint)
     */
    public function markAsRead($id)
    {
        $message = Message::where('receiver_id', auth()->id())->findOrFail($id);
        $message->markAsRead();

        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Message marked as read.');
    }

    /**
     * Delete (soft delete) a message
     */
    public function destroy($id)
    {
        $message = Message::where(function ($q) {
            $q->where('sender_id', auth()->id())
                ->orWhere('receiver_id', auth()->id());
        })->findOrFail($id);

        // Soft delete - just mark as deleted
        $message->delete();

        // Log the soft delete
        MessageAuditLog::create([
            'message_id' => $message->id,
            'user_id' => auth()->id(),
            'action' => 'soft_deleted',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return back()->with('success', 'Message moved to archive (will be kept for 7 years).');
    }

    /**
     * Archive a message
     */
    public function archive($id)
    {
        $message = Message::where('receiver_id', auth()->id())->findOrFail($id);
        $message->update(['is_archived' => true]);

        return back()->with('success', 'Message archived.');
    }

    /**
     * Star/unstar a message
     */
    public function toggleStar($id)
    {
        $message = Message::where('receiver_id', auth()->id())->findOrFail($id);
        $message->update(['is_starred' => ! $message->is_starred]);

        $status = $message->is_starred ? 'starred' : 'unstarred';

        return back()->with('success', "Message {$status}.");
    }

    /**
     * Mark message as unread
     */
    public function markUnread($id)
    {
        $message = Message::where('receiver_id', auth()->id())->findOrFail($id);
        $message->update([
            'is_read' => false,
            'read_at' => null,
            'status' => 'delivered',
        ]);

        return back()->with('success', 'Message marked as unread.');
    }

    /**
     * Get unread count for sidebar badge
     */
    public function getUnreadCount()
    {
        $count = Message::where('receiver_id', auth()->id())
            ->where('is_read', false)
            ->where('is_archived', false)
            ->count();

        // request()->ajax() checks for X-Requested-With: XMLHttpRequest,
        // which jQuery's $.ajax() sets automatically but the plain fetch()
        // calls that poll this endpoint (topbar.blade.php, sidebar.blade.php)
        // never do - so this always fell through to the plain-integer branch
        // below. That response comes back as a bare number, not
        // {unread_count: N}, so the polling badges have never displayed a
        // real count. This route only ever serves that polling fetch, so
        // it's safe to always return JSON.
        return response()->json(['unread_count' => $count]);
    }

    /**
     * Bulk delete messages
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'message_ids' => 'required|array',
            'message_ids.*' => 'exists:messages,id',
        ]);

        Message::whereIn('id', $request->message_ids)
            ->where(function ($q) {
                $q->where('sender_id', auth()->id())
                    ->orWhere('receiver_id', auth()->id());
            })
            ->delete();

        return back()->with('success', 'Messages deleted successfully.');
    }

    /**
     * Search messages
     */
    public function search(Request $request)
    {
        $request->validate([
            'query' => 'required|string|min:2',
        ]);

        $userId = auth()->id();
        $searchQuery = $request->query('query');

        $messages = Message::where(function ($q) use ($userId) {
            $q->where('sender_id', $userId)
                ->orWhere('receiver_id', $userId);
        })
            ->where(function ($q) use ($searchQuery) {
                $q->where('subject', 'like', "%{$searchQuery}%")
                    ->orWhere('message', 'like', "%{$searchQuery}%")
                    ->orWhere('message_number', 'like', "%{$searchQuery}%");
            })
            ->with(['sender', 'receiver'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('shared.messages.search', compact('messages', 'searchQuery'));
    }

    /**
     * Get or create a conversation
     */
    private function getOrCreateConversation($senderId, $receiverId, $leaveRequestId = null)
    {
        // First, try to find existing conversation
        $existing = MessageConversation::where(function ($q) use ($senderId, $receiverId) {
            $q->whereJsonContains('participant_ids', (string) $senderId)
                ->whereJsonContains('participant_ids', (string) $receiverId);
        })->first();

        if ($existing) {
            return $existing;
        }

        // Create new conversation
        return MessageConversation::create([
            'leave_request_id' => $leaveRequestId,
            'initiator_id' => $senderId,
            'participant_ids' => json_encode([$senderId, $receiverId]),
            'conversation_number' => $this->generateConversationNumber(),
            'is_active' => true,
            'status' => 'active',
        ]);
    }

    /**
     * Generate unique message number
     */
    private function generateMessageNumber()
    {
        $year = date('Y');
        $last = Message::whereYear('created_at', $year)->max('id') ?? 0;
        $number = str_pad($last + 1, 4, '0', STR_PAD_LEFT);

        return "MSG/{$year}/{$number}";
    }

    /**
     * Generate unique conversation number
     */
    private function generateConversationNumber()
    {
        $year = date('Y');
        $last = MessageConversation::whereYear('created_at', $year)->max('id') ?? 0;
        $number = str_pad($last + 1, 4, '0', STR_PAD_LEFT);

        return "CONV/{$year}/{$number}";
    }

    /**
     * Search users via AJAX for compose form
     * Handles 5000+ users efficiently
     */
    public function searchUsers(Request $request)
    {
        $search = $request->get('q', '');

        if (strlen($search) < 2) {
            return response()->json(['results' => []]);
        }

        $users = User::where('id', '!=', auth()->id())
            ->where(function ($query) use ($search) {
                $query->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('staff_id', 'like', "%{$search}%")
                    ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"]);
            })
            ->limit(20)
            ->get(['id', 'first_name', 'last_name', 'email', 'staff_id']);

        $results = [];
        foreach ($users as $user) {
            $results[] = [
                'id' => $user->id,
                'text' => "{$user->first_name} {$user->last_name} - {$user->staff_id} ({$user->email})",
            ];
        }

        return response()->json(['results' => $results]);
    }

    /**
     * Alias for getUnreadCount (for legacy routes)
     */
    public function unreadCount()
    {
        return $this->getUnreadCount();
    }

    /**
     * Download/stream a message attachment.
     * Authorization mirrors Message::canViewContent(): sender, receiver,
     * a user with an approved (non-expired) one-time access code, or admin/vice-chancellor.
     */
    public function downloadAttachment($attachmentId)
    {
        $attachment = MessageAttachment::with('message')->findOrFail($attachmentId);
        $message = $attachment->message;

        if (! $message || ! $message->canViewContent(auth()->user())) {
            abort(403, 'You do not have permission to view this attachment.');
        }

        $disk = $attachment->disk ?? 'local';

        if (! Storage::disk($disk)->exists($attachment->file_path)) {
            abort(404, 'File not found.');
        }

        return Storage::disk($disk)->response($attachment->file_path, $attachment->file_name);
    }
}
