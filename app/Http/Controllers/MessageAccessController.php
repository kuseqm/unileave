<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\MessageAccessRequest;
use App\Models\MessageAuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MessageAccessController extends Controller
{
    /**
     * Display message list for Admin/HR/School Admin/HOD
     */
    public function messageList(Request $request)
    {
        $user = auth()->user();
        $role = $user->getRoleNames()->first();
        
        $query = Message::with(['sender', 'receiver'])
            ->withTrashed()
            ->visibleTo($user);
        
        // Apply filters
        if ($request->has('type') && $request->type != 'all') {
            $query->where('message_type', $request->type);
        }
        
        if ($request->has('status') && $request->status != 'all') {
            if ($request->status == 'deleted') {
                $query->onlyTrashed();
            } elseif ($request->status == 'archived') {
                $query->where('is_archived', true);
            } else {
                $query->whereNull('deleted_at');
            }
        }
        
        if ($request->has('priority') && $request->priority != 'all') {
            $query->where('priority', $request->priority);
        }
        
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                  ->orWhereHas('sender', function($sub) use ($search) {
                      $sub->where('first_name', 'like', "%{$search}%")
                          ->orWhere('last_name', 'like', "%{$search}%");
                  });
            });
        }
        
        $messages = $query->orderBy('created_at', 'desc')->paginate(20);
        
        return view('shared.messages.admin-list', compact('messages', 'role'));
    }
    
    /**
     * Show message metadata and audit trail (no content - for non-repudiation)
     */
    public function metadata($id)
    {
        $message = Message::withTrashed()
            ->with([
                'auditLogs.user', 
                'accessRequests.requestedBy', 
                'accessRequests.approvedBy', 
                'sender', 
                'receiver'
            ])
            ->findOrFail($id);
        
        return view('shared.messages.metadata', compact('message'));
    }
    
    /**
     * Display secure message content for VC-approved access
     */
    public function secureContent($id)
    {
        $user = auth()->user();
        $message = Message::findOrFail($id);
        
        // Check if user has permission to view
        if (!$message->canViewContent($user)) {
            abort(403, 'You do not have permission to view this message content.');
        }
        
        // Get the access request if it exists (for HR)
        $accessRequest = MessageAccessRequest::where('message_id', $id)
            ->where('requested_by', $user->id)
            ->where('status', 'approved')
            ->first();
        
        return view('shared.messages.secure-message-content', compact('message', 'accessRequest'));
    }
    
    /**
     * Restore a soft-deleted message
     */
    public function restoreMessage($id)
    {
        $message = Message::withTrashed()->findOrFail($id);
        $message->restore();
        
        MessageAuditLog::create([
            'message_id' => $message->id,
            'user_id' => auth()->id(),
            'action' => 'restored',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
        
        return response()->json(['success' => true, 'message' => 'Message restored successfully.']);
    }
    
    /**
     * Display message audit trail
     */
    public function auditTrail($id)
    {
        $message = Message::withTrashed()
            ->with(['auditLogs.user', 'accessRequests.requestedBy', 'accessRequests.approvedBy'])
            ->findOrFail($id);
        
        return view('shared.messages.audit-trail', compact('message'));
    }
    
    /**
     * Request access to view a message (HR to VC)
     */
    public function requestAccess(Request $request, $messageId)
    {
        $message = Message::findOrFail($messageId);
        $user = auth()->user();
        
        if (!$user->hasRole('hr_manager')) {
            return response()->json(['success' => false, 'message' => 'Only HR can request message access.'], 403);
        }
        
        $existingRequest = MessageAccessRequest::where('message_id', $messageId)
            ->where('status', 'pending')
            ->first();
            
        if ($existingRequest) {
            return response()->json(['success' => false, 'message' => 'Access request already pending for this message.']);
        }
        
        $request->validate([
            'reason' => 'required|string|min:10|max:500',
        ]);
        
        $accessRequest = MessageAccessRequest::create([
            'message_id' => $messageId,
            'requested_by' => $user->id,
            'reason' => $request->reason,
            'status' => 'pending',
        ]);
        
        return response()->json(['success' => true, 'message' => 'Access request sent to VC for approval.']);
    }
    
    /**
     * VC views pending access requests
     */
    public function pendingRequests()
    {
        $user = auth()->user();
        
        if (!$user->hasRole('vice-chancellor') && !$user->hasRole('admin')) {
            abort(403);
        }
        
        $requests = MessageAccessRequest::with(['message', 'requestedBy', 'message.sender'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        return view('shared.messages.access-requests', compact('requests'));
    }
    
    /**
     * Get pending access requests count for sidebar badge
     */
    public function pendingCount()
    {
        $count = MessageAccessRequest::where('status', 'pending')->count();
        return response()->json(['count' => $count]);
    }
    
    /**
     * VC approves access request and generates one-time code
     */
    public function approveRequest($requestId)
    {
        $user = auth()->user();
        
        if (!$user->hasRole('vice-chancellor') && !$user->hasRole('admin')) {
            abort(403);
        }
        
        $accessRequest = MessageAccessRequest::findOrFail($requestId);
        
        if ($accessRequest->status !== 'pending') {
            return back()->with('error', 'This request has already been processed.');
        }
        
        $oneTimeCode = strtoupper(Str::random(12));
        
        $accessRequest->update([
            'approved_by' => $user->id,
            'status' => 'approved',
            'one_time_code' => bcrypt($oneTimeCode),
            'code_expires_at' => now()->addHours(24),
        ]);
        
        MessageAuditLog::create([
            'message_id' => $accessRequest->message_id,
            'user_id' => $user->id,
            'action' => 'access_approved',
            'access_method' => 'vc_approval',
        ]);
        
        return back()->with('success', "Access approved. One-time code: {$oneTimeCode} (valid for 24 hours)");
    }
    
    /**
     * VC rejects access request
     */
    public function rejectRequest(Request $request, $requestId)
    {
        $user = auth()->user();
        
        if (!$user->hasRole('vice-chancellor') && !$user->hasRole('admin')) {
            abort(403);
        }
        
        $request->validate([
            'rejection_reason' => 'required|string|min:10',
        ]);
        
        $accessRequest = MessageAccessRequest::findOrFail($requestId);
        
        $accessRequest->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
        ]);
        
        return back()->with('error', 'Access request rejected.');
    }
    
    /**
     * HR views message using one-time code
     */
    public function viewWithCode(Request $request, $messageId)
    {
        $user = auth()->user();
        
        if (!$user->hasRole('hr_manager')) {
            abort(403);
        }
        
        $request->validate([
            'one_time_code' => 'required|string',
        ]);
        
        $message = Message::findOrFail($messageId);
        
        // Find the access request
        $accessRequest = MessageAccessRequest::where('message_id', $messageId)
            ->where('status', 'approved')
            ->where('code_expires_at', '>', now())
            ->first();
            
        if (!$accessRequest) {
            return back()->with('error', 'No valid access request found or code expired.');
        }
        
        // Verify the one-time code
        if (!\Hash::check($request->one_time_code, $accessRequest->one_time_code)) {
            // Log failed attempt
            MessageAuditLog::create([
                'message_id' => $messageId,
                'user_id' => $user->id,
                'action' => 'failed_access_attempt',
                'access_method' => 'invalid_code',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
            return back()->with('error', 'Invalid one-time code.');
        }
        
        // Log successful access
        MessageAuditLog::create([
            'message_id' => $messageId,
            'user_id' => $user->id,
            'action' => 'accessed_via_code',
            'access_method' => 'one_time_code',
            'one_time_code_used' => $request->one_time_code,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
        
        // Mark request as viewed and invalidate the code
        $accessRequest->update([
            'viewed_at' => now(),
            'one_time_code' => null,
        ]);
        
        // Return the secure message content view
        return view('shared.messages.secure-message-content', compact('message', 'accessRequest'));
    }
}