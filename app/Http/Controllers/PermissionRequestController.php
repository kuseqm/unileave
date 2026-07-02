<?php

namespace App\Http\Controllers;

use App\Models\PermissionRequest;
use App\Models\TravelPermission;
use App\Models\User;
use App\Traits\PerPageTrait;
use App\Helpers\AuditLogHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PermissionRequestController extends Controller
{
    use PerPageTrait;

    /**
     * Staff - My Permission Requests
     */
    public function myRequests(Request $request)
    {
        $perPage = $this->getPerPage($request, 10);
        
        $permissionRequests = PermissionRequest::with(['staff'])
            ->where('staff_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
        
        // Also get travel requests for combined view
        $travelRequests = TravelPermission::with(['staff'])
            ->where('staff_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();
        
        $stats = [
            'total' => PermissionRequest::where('staff_id', auth()->id())->count(),
            'pending' => PermissionRequest::where('staff_id', auth()->id())->where('status', 'pending')->count(),
            'approved' => PermissionRequest::where('staff_id', auth()->id())->where('status', 'approved')->count(),
            'rejected' => PermissionRequest::where('staff_id', auth()->id())->where('status', 'rejected')->count(),
            'cancelled' => PermissionRequest::where('staff_id', auth()->id())->where('status', 'cancelled')->count(),
        ];
        
        return view('shared.permissions.my-requests', compact('permissionRequests', 'travelRequests', 'stats'));
    }

    /**
     * Staff - Create Permission Request Form
     */
    public function create()
    {
        $colleagues = User::where('department_id', auth()->user()->department_id)
            ->where('id', '!=', auth()->id())
            ->where('status', 'active')
            ->get();
        
        return view('shared.permissions.create', compact('colleagues'));
    }

    /**
     * Staff - Store Permission Request
     */
    public function store(Request $request)
    {
        $request->validate([
            'permission_type' => 'required|in:late_arrival,early_departure,short_leave,official_duty,other',
            'start_datetime' => 'required|date|after_or_equal:today',
            'end_datetime' => 'required|date|after:start_datetime',
            'reason' => 'required|string|min:5',
            'official_duty_description' => 'nullable|string|max:500',
        ]);

        // Calculate hours from datetime fields
        $start = \Carbon\Carbon::parse($request->start_datetime);
        $end = \Carbon\Carbon::parse($request->end_datetime);
        $hours = round($end->diffInMinutes($start) / 60, 1);
        
        // Use the date part only for the date field
        $date = $start->toDateString();
        
        // Combine reason with official duty description if present
        $reason = $request->reason;
        if ($request->permission_type === 'official_duty' && $request->official_duty_description) {
            $reason = "Official Duty: " . $request->official_duty_description . "\n\n" . $reason;
        }

        DB::beginTransaction();

        try {
            $permission = PermissionRequest::create([
                'staff_id' => auth()->id(),
                'permission_type' => $request->permission_type,
                'date' => $date,
                'start_time' => $start->format('H:i:s'),
                'end_time' => $end->format('H:i:s'),
                'hours' => $hours,
                'duration_hours' => $hours,  // ✅ ADDED - fixes the database error
                'reason' => $reason,
                'status' => 'pending',
                'hod_status' => 'pending',
                'hr_status' => 'pending',
            ]);

            AuditLogHelper::log('create', 'permission', $permission->id, null, $permission->toArray());

            // Notify HOD
            $hod = $permission->staff->department->hod ?? null;
            if ($hod) {
                \App\Models\Notification::create([
                    'user_id' => $hod->id,
                    'type' => 'permission_pending',
                    'title' => 'Permission Request Pending',
                    'message' => "{$permission->staff->first_name} {$permission->staff->last_name} has requested {$permission->permission_type} on {$permission->formatted_date} for {$hours} hour(s)",
                    'action_url' => route('permissions.pending'),
                    'action_text' => 'Review Request',
                    'is_read' => false,
                ]);
            }

            DB::commit();

            return redirect()->route('permissions.my-requests')
                ->with('success', 'Permission request submitted successfully. Waiting for HOD approval.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to submit request: ' . $e->getMessage());
        }
    }

    /**
     * Staff - Show Permission Request Details (Comprehensive view with timeline)
     */
    public function show($id)
    {
        $user = auth()->user();
        $role = $user->getRoleNames()->first();
        
        // Authorized roles that can view any permission request
        $authorizedRoles = ['admin', 'hod', 'hr-officer', 'hr-manager'];
        
        $query = PermissionRequest::with(['staff']);
        
        if (in_array($role, $authorizedRoles)) {
            $permissionRequest = $query->findOrFail($id);
        } else {
            $permissionRequest = $query->where('staff_id', $user->id)->findOrFail($id);
        }
        
        // Get approval timeline
        $approvalTimeline = [];
        
        // Submission
        $approvalTimeline[] = [
            'action' => 'submitted',
            'by' => $permissionRequest->staff->full_name ?? 'Staff',
            'date' => $permissionRequest->created_at,
            'comment' => 'Permission request submitted',
        ];
        
        // HOD Action
        if ($permissionRequest->hod_status !== 'pending') {
            $hod = \App\Models\User::find($permissionRequest->hod_id);
            $approvalTimeline[] = [
                'action' => $permissionRequest->hod_status === 'approved' ? 'approved' : 'rejected',
                'by' => $hod ? $hod->full_name : 'HOD',
                'role' => 'HOD',
                'date' => $permissionRequest->hod_acted_at,
                'comment' => $permissionRequest->hod_comment,
            ];
        }
        
        // HR Action
        if ($permissionRequest->hr_status !== 'pending') {
            $hr = \App\Models\User::find($permissionRequest->hr_id);
            $approvalTimeline[] = [
                'action' => $permissionRequest->hr_status === 'approved' ? 'approved' : 'rejected',
                'by' => $hr ? $hr->full_name : 'HR',
                'role' => 'HR',
                'date' => $permissionRequest->hr_acted_at,
                'comment' => $permissionRequest->hr_comment,
            ];
        }
        
        // Calculate total approval time
        $totalApprovalTime = null;
        $firstDate = $permissionRequest->created_at;
        $lastDate = $permissionRequest->hr_acted_at ?? $permissionRequest->hod_acted_at ?? null;
        
        if ($firstDate && $lastDate && $permissionRequest->status === 'approved') {
            $totalMinutes = $firstDate->diffInMinutes($lastDate);
            if ($totalMinutes < 60) {
                $totalApprovalTime = $totalMinutes . ' minutes';
            } elseif ($totalMinutes < 1440) {
                $totalApprovalTime = round($totalMinutes / 60, 1) . ' hours';
            } else {
                $totalApprovalTime = round($totalMinutes / 1440, 1) . ' days';
            }
        }
        
        return view('shared.permissions.details', compact('permissionRequest', 'approvalTimeline', 'totalApprovalTime'));
    }

    /**
     * HOD/HR/Admin - Pending Approvals
     */
    public function pendingApprovals(Request $request)
    {
        $perPage = $this->getPerPage($request, 10);
        $user = auth()->user();
        $role = $user->getRoleNames()->first();
        
        // Short permissions query
        $query = PermissionRequest::with(['staff']);
        
        if ($role == 'hod') {
            $query->whereHas('staff.department', function($q) use ($user) {
                $q->where('hod_staff_id', $user->id);
            })->where('hod_status', 'pending');
        } elseif ($role == 'hr-officer' || $role == 'hr-manager') {
            $query->where('hod_status', 'approved')->where('hr_status', 'pending');
        } elseif ($role == 'admin') {
            // Admin sees all pending permissions
            $query->where('status', 'pending');
        } else {
            return redirect()->route('dashboard');
        }
        
        $pendingRequests = $query->orderBy('created_at', 'asc')->paginate($perPage);
        
        // Travel permissions pending (for combined view)
        $travelQuery = TravelPermission::with(['staff'])
            ->where('status', 'pending')
            ->where('acting_status', 'accepted');
        
        if ($role == 'hod') {
            $travelQuery->whereHas('staff.department', function($q) use ($user) {
                $q->where('hod_staff_id', $user->id);
            });
        } elseif ($role == 'hr-officer' || $role == 'hr-manager') {
            $travelQuery->where('current_approval_level', 'hr_final');
        }
        
        $travelPending = $travelQuery->get();
        
        return view('shared.permissions.pending-approvals', compact('pendingRequests', 'travelPending'));
    }

    /**
     * HOD - Approve Permission
     */
    public function hodApprove($id)
    {
        $permission = PermissionRequest::findOrFail($id);
        
        // Verify HOD has authority
        if ($permission->staff->department->hod_staff_id != auth()->id()) {
            return back()->with('error', 'You are not authorized to approve this request.');
        }
        
        DB::beginTransaction();
        
        try {
            $permission->update([
                'hod_status' => 'approved',
                'hod_acted_at' => now(),
                'hod_id' => auth()->id(),
                'status' => 'hod_approved',
            ]);
            
            AuditLogHelper::log('approve', 'permission', $permission->id,
                ['hod_status' => 'pending', 'status' => 'pending'],
                ['hod_status' => 'approved', 'status' => 'hod_approved']
            );
            
            // Notify staff
            \App\Models\Notification::create([
                'user_id' => $permission->staff_id,
                'type' => 'permission_hod_approved',
                'title' => 'Permission Approved by HOD',
                'message' => "Your permission request has been approved by HOD. Awaiting HR final approval.",
                'action_url' => route('permissions.show', $permission->id),
                'action_text' => 'View Request',
                'is_read' => false,
            ]);
            
            // Notify HR
            $hr = User::role('hr-officer')->first();
            if ($hr) {
                \App\Models\Notification::create([
                    'user_id' => $hr->id,
                    'type' => 'permission_pending_hr',
                    'title' => 'Permission Request Pending HR Approval',
                    'message' => "Permission request from {$permission->staff->first_name} {$permission->staff->last_name} requires your final approval.",
                    'action_url' => route('permissions.pending'),
                    'action_text' => 'Review Request',
                    'is_read' => false,
                ]);
            }
            
            DB::commit();
            
            return redirect()->back()->with('success', 'Permission request approved. Sent to HR for final approval.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to approve: ' . $e->getMessage());
        }
    }

    /**
     * HOD - Reject Permission
     */
    public function hodReject(Request $request, $id)
    {
        $request->validate([
            'hod_comment' => 'required|string|min:5|max:500',
        ]);
        
        $permission = PermissionRequest::findOrFail($id);
        
        if ($permission->staff->department->hod_staff_id != auth()->id()) {
            return back()->with('error', 'You are not authorized to reject this request.');
        }
        
        DB::beginTransaction();
        
        try {
            $permission->update([
                'hod_status' => 'rejected',
                'hod_acted_at' => now(),
                'hod_id' => auth()->id(),
                'hod_comment' => $request->hod_comment,
                'status' => 'rejected',
            ]);
            
            AuditLogHelper::log('reject', 'permission', $permission->id,
                ['hod_status' => 'pending'],
                ['hod_status' => 'rejected', 'reason' => $request->hod_comment]
            );
            
            // Notify staff
            \App\Models\Notification::create([
                'user_id' => $permission->staff_id,
                'type' => 'permission_rejected',
                'title' => 'Permission Request Rejected',
                'message' => "Your permission request has been rejected by HOD. Reason: " . $request->hod_comment,
                'action_url' => route('permissions.show', $permission->id),
                'action_text' => 'View Request',
                'is_read' => false,
            ]);
            
            DB::commit();
            
            return redirect()->back()->with('success', 'Permission request rejected.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to reject: ' . $e->getMessage());
        }
    }

    /**
     * HR - Final Approve
     */
    public function hrApprove($id)
    {
        $permission = PermissionRequest::where('hod_status', 'approved')
            ->where('hr_status', 'pending')
            ->findOrFail($id);
        
        DB::beginTransaction();
        
        try {
            $permission->update([
                'hr_status' => 'approved',
                'hr_acted_at' => now(),
                'hr_id' => auth()->id(),
                'status' => 'approved',
            ]);
            
            AuditLogHelper::log('approve', 'permission', $permission->id,
                ['hr_status' => 'pending', 'status' => 'hod_approved'],
                ['hr_status' => 'approved', 'status' => 'approved']
            );
            
            // Notify staff
            \App\Models\Notification::create([
                'user_id' => $permission->staff_id,
                'type' => 'permission_approved',
                'title' => 'Permission Request Approved',
                'message' => "Your permission request has been fully approved.",
                'action_url' => route('permissions.show', $permission->id),
                'action_text' => 'View Request',
                'is_read' => false,
            ]);
            
            DB::commit();
            
            return redirect()->back()->with('success', 'Permission request fully approved.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to approve: ' . $e->getMessage());
        }
    }

    /**
     * HR - Final Reject
     */
    public function hrReject(Request $request, $id)
    {
        $request->validate([
            'hr_comment' => 'required|string|min:5|max:500',
        ]);
        
        $permission = PermissionRequest::where('hod_status', 'approved')
            ->findOrFail($id);
        
        DB::beginTransaction();
        
        try {
            $permission->update([
                'hr_status' => 'rejected',
                'hr_acted_at' => now(),
                'hr_id' => auth()->id(),
                'hr_comment' => $request->hr_comment,
                'status' => 'rejected',
            ]);
            
            AuditLogHelper::log('reject', 'permission', $permission->id,
                ['hr_status' => 'pending', 'status' => 'hod_approved'],
                ['hr_status' => 'rejected', 'status' => 'rejected', 'reason' => $request->hr_comment]
            );
            
            // Notify staff
            \App\Models\Notification::create([
                'user_id' => $permission->staff_id,
                'type' => 'permission_rejected',
                'title' => 'Permission Request Rejected',
                'message' => "Your permission request has been rejected by HR. Reason: " . $request->hr_comment,
                'action_url' => route('permissions.show', $permission->id),
                'action_text' => 'View Request',
                'is_read' => false,
            ]);
            
            DB::commit();
            
            return redirect()->back()->with('success', 'Permission request rejected.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to reject: ' . $e->getMessage());
        }
    }
    
    /**
     * Staff - Cancel Permission Request (Only pending ones)
     */
    public function cancel($id)
    {
        $permission = PermissionRequest::where('staff_id', auth()->id())
            ->where('status', 'pending')
            ->findOrFail($id);
        
        DB::beginTransaction();
        
        try {
            $permission->update([
                'status' => 'cancelled',
            ]);
            
            AuditLogHelper::log('update', 'permission', $permission->id,
                ['status' => 'pending'],
                ['status' => 'cancelled']
            );
            
            DB::commit();
            
            if (request()->expectsJson()) {
                return response()->json(['success' => true]);
            }
            
            return redirect()->back()->with('success', 'Permission request cancelled successfully.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to cancel: ' . $e->getMessage());
        }
    }
}