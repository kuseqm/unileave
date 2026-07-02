<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use App\Models\PermissionRequest;
use App\Traits\PerPageTrait;
use Illuminate\Http\Request;

class ApprovalController extends Controller
{
    use PerPageTrait;

    /**
     * Display list of pending approvals
     */
    public function index()
    {
        // Get pending leave requests
        $pendingLeaveRequests = LeaveRequest::with(['staff', 'leaveType'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Get pending permission requests (if you have this module)
        $pendingPermissionRequests = PermissionRequest::with(['staff'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();
        
        $totalPending = $pendingLeaveRequests->count() + $pendingPermissionRequests->count();
        
        return view('approvals.index', compact('pendingLeaveRequests', 'pendingPermissionRequests', 'totalPending'));
    }
    
    /**
     * Display pending leave requests specifically with pagination
     */
    public function leaveRequests(Request $request)
    {
        $perPage = $this->getPerPage($request, 10);
        
        $pendingRequests = LeaveRequest::with(['staff', 'leaveType'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
        
        return view('approvals.leave-requests', compact('pendingRequests'));
    }
    
    /**
     * Display pending permission requests specifically with pagination
     */
    public function permissionRequests(Request $request)
    {
        $perPage = $this->getPerPage($request, 10);
        
        $pendingRequests = PermissionRequest::with(['staff'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
        
        return view('approvals.permission-requests', compact('pendingRequests'));
    }
    
    /**
     * Approve a leave request
     */
    public function approveLeave($id)
    {
        $leaveRequest = LeaveRequest::findOrFail($id);
        $leaveRequest->status = 'approved';
        $leaveRequest->approved_at = now();
        $leaveRequest->approved_by = auth()->id();
        $leaveRequest->save();
        
        return redirect()->back()->with('success', 'Leave request approved successfully.');
    }
    
    /**
     * Reject a leave request
     */
    public function rejectLeave(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500'
        ]);
        
        $leaveRequest = LeaveRequest::findOrFail($id);
        $leaveRequest->status = 'rejected';
        $leaveRequest->rejection_reason = $request->rejection_reason;
        $leaveRequest->approved_at = now();
        $leaveRequest->approved_by = auth()->id();
        $leaveRequest->save();
        
        return redirect()->back()->with('success', 'Leave request rejected.');
    }
    
    /**
     * Approve a permission request
     */
    public function approvePermission($id)
    {
        $permissionRequest = PermissionRequest::findOrFail($id);
        $permissionRequest->status = 'approved';
        $permissionRequest->approved_at = now();
        $permissionRequest->approved_by = auth()->id();
        $permissionRequest->save();
        
        return redirect()->back()->with('success', 'Permission request approved successfully.');
    }
    
    /**
     * Reject a permission request
     */
    public function rejectPermission(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500'
        ]);
        
        $permissionRequest = PermissionRequest::findOrFail($id);
        $permissionRequest->status = 'rejected';
        $permissionRequest->rejection_reason = $request->rejection_reason;
        $permissionRequest->approved_at = now();
        $permissionRequest->approved_by = auth()->id();
        $permissionRequest->save();
        
        return redirect()->back()->with('success', 'Permission request rejected.');
    }
    
    /**
     * Bulk approve multiple requests
     */
    public function bulkApprove(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'type' => 'required|in:leave,permission'
        ]);
        
        if ($request->type === 'leave') {
            LeaveRequest::whereIn('id', $request->ids)->update([
                'status' => 'approved',
                'approved_at' => now(),
                'approved_by' => auth()->id()
            ]);
        } else {
            PermissionRequest::whereIn('id', $request->ids)->update([
                'status' => 'approved',
                'approved_at' => now(),
                'approved_by' => auth()->id()
            ]);
        }
        
        return redirect()->back()->with('success', 'Requests approved successfully.');
    }
    
    /**
     * Get approval statistics
     */
    public function statistics()
    {
        $stats = [
            'pending_leaves' => LeaveRequest::where('status', 'pending')->count(),
            'approved_leaves' => LeaveRequest::where('status', 'approved')->count(),
            'rejected_leaves' => LeaveRequest::where('status', 'rejected')->count(),
            'pending_permissions' => PermissionRequest::where('status', 'pending')->count(),
            'approved_permissions' => PermissionRequest::where('status', 'approved')->count(),
            'rejected_permissions' => PermissionRequest::where('status', 'rejected')->count(),
        ];
        
        return response()->json($stats);
    }
}