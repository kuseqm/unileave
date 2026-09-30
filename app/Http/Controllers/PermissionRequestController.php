<?php

namespace App\Http\Controllers;

use App\Helpers\AuditLogHelper;
use App\Models\AuditLog;
use App\Models\Notification;
use App\Models\PermissionRequest;
use App\Models\TravelPermission;
use App\Models\User;
use App\Traits\PerPageTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PermissionRequestController extends Controller
{
    use PerPageTrait;

    /**
     * Lightweight signature for the live-refresh poll on "my requests".
     */
    public function myRequestsHeartbeat()
    {
        $permQuery = PermissionRequest::where('staff_id', auth()->id());
        $travelQuery = TravelPermission::where('staff_id', auth()->id());

        $signature = $permQuery->count().':'.$permQuery->max('updated_at').'|'.$travelQuery->count().':'.$travelQuery->max('updated_at');

        return response()->json(['signature' => $signature]);
    }

    /**
     * Staff - My Permission Requests
     */
    public function myRequests(Request $request)
    {
        $perPage = $this->getPerPage($request, 10);

        $query = PermissionRequest::with(['staff'])
            ->where('staff_id', auth()->id())
            ->orderBy('created_at', 'desc');

        $grandTotal = (clone $query)->count();

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('permission_type', 'like', "%{$search}%")
                    ->orWhere('reason', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $permissionRequests = $query->paginate($perPage)->appends($request->query());

        if ($request->wantsJson()) {
            return response()->json([
                'html' => view('shared.permissions._my-requests-results', compact('permissionRequests'))->render(),
                'total' => $permissionRequests->total(),
                'grand_total' => $grandTotal,
            ]);
        }

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
        $thisMonthCount = PermissionRequest::where('staff_id', auth()->id())
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        // The HOD of a department would otherwise have to approve their
        // own permission request at the HOD stage - if that's who's
        // applying, they pick a colleague in their own department to
        // stand in as acting HOD for that one stage only.
        $actingHodOptions = $this->isOwnDepartmentHod(auth()->user())
            ? $this->eligibleActingHods(auth()->user())
            : collect();

        return view('shared.permissions.create', compact('thisMonthCount', 'actingHodOptions'));
    }

    /**
     * Whether this staff member is the HOD of their own department -
     * meaning their own permission request would otherwise reach the HOD
     * stage with nobody but themselves able to approve it.
     */
    private function isOwnDepartmentHod(User $staff): bool
    {
        $department = $staff->department;

        return $department && $department->hod_staff_id == $staff->id;
    }

    /**
     * Active department colleagues eligible to stand in as acting HOD for
     * an HOD's own permission request.
     */
    private function eligibleActingHods(User $hod): Collection
    {
        return User::where('department_id', $hod->department_id)
            ->where('id', '!=', $hod->id)
            ->where('status', 'active')
            ->get();
    }

    /**
     * Who can act on a request at the HOD stage: the real HOD normally, or
     * - if the applicant IS their own department's HOD - specifically the
     * acting HOD they picked, regardless of that person's own role.
     */
    private function canReviewAsHod(User $user, PermissionRequest $permission): bool
    {
        $realHodId = $permission->staff->department->hod_staff_id ?? null;

        if ($realHodId == $permission->staff_id) {
            return $user->id == $permission->acting_hod_id;
        }

        return $realHodId == $user->id;
    }

    /**
     * IDs of pending-at-HOD permission requests where $user is the acting
     * HOD stand-in for an applicant who is their own department's HOD.
     * Re-verifies the collision in PHP rather than trusting acting_hod_id
     * alone, in case the department's real HOD changed after submission.
     */
    private function standinHodApprovalIds(User $user): Collection
    {
        return PermissionRequest::where('hod_status', 'pending')
            ->where('acting_hod_id', $user->id)
            ->with('staff.department')
            ->get()
            ->filter(function ($permission) {
                return ($permission->staff->department->hod_staff_id ?? null) == $permission->staff_id;
            })
            ->pluck('id');
    }

    /**
     * Staff - Store Permission Request
     */
    public function store(Request $request)
    {
        $isOwnDepartmentHod = $this->isOwnDepartmentHod(auth()->user());
        $eligibleActingHodIds = $isOwnDepartmentHod
            ? $this->eligibleActingHods(auth()->user())->pluck('id')
            : collect();

        $request->validate([
            'permission_type' => [
                'required',
                'in:late_arrival,early_departure,short_leave,official_duty,other',
                // "Stop and wait": only one unresolved permission request at
                // a time, regardless of type. Approved ones don't block -
                // permissions are short/one-off, not an ongoing period.
                function ($attribute, $value, $fail) {
                    $unresolved = PermissionRequest::where('staff_id', auth()->id())
                        ->whereNotIn('status', ['approved', 'rejected', 'hod_rejected', 'hr_rejected', 'cancelled'])
                        ->exists();
                    if ($unresolved) {
                        $fail('You already have a permission request awaiting approval. Please wait until it is resolved before applying again.');
                    }
                },
            ],
            'acting_hod_id' => $isOwnDepartmentHod
                ? ['required', Rule::in($eligibleActingHodIds)]
                : ['nullable'],
            'start_datetime' => 'required|date|after_or_equal:today',
            'end_datetime' => 'required|date|after:start_datetime',
            'reason' => 'required|string|min:5',
            'official_duty_description' => 'nullable|string|max:500',
        ]);

        // Calculate hours from datetime fields
        $start = Carbon::parse($request->start_datetime);
        $end = Carbon::parse($request->end_datetime);
        $hours = round($start->diffInMinutes($end, true) / 60, 1);

        // Use the date part only for the date field
        $date = $start->toDateString();

        // Combine reason with official duty description if present
        $reason = $request->reason;
        if ($request->permission_type === 'official_duty' && $request->official_duty_description) {
            $reason = 'Official Duty: '.$request->official_duty_description."\n\n".$reason;
        }

        DB::beginTransaction();

        try {
            $permission = PermissionRequest::create([
                'staff_id' => auth()->id(),
                'acting_hod_id' => $isOwnDepartmentHod ? $request->acting_hod_id : null,
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

            // Notify HOD - or the acting HOD they picked, if they're their
            // own department's HOD and would otherwise review themselves.
            $hod = $isOwnDepartmentHod ? $permission->actingHod : ($permission->staff->department->hod ?? null);
            if ($hod) {
                Notification::create([
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
            \Log::error('Permission request submission failed: '.$e->getMessage());

            return back()->with('error', 'Failed to submit request: '.$e->getMessage());
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
        $authorizedRoles = ['admin', 'hod', 'hr-officer', 'hr-manager', 'internal-auditor', 'registrar'];

        $query = PermissionRequest::with(['staff']);

        if (in_array($role, $authorizedRoles)) {
            // Institute-level oversight/approval seats only reach requests
            // from their own university (true super-admin excepted).
            // Admin sees all.
            if (! $user->hasRole('super-admin') && ! $user->hasRole('admin')) {
                $query->whereHas('staff', function ($q) use ($user) {
                    $q->where('university_id', $user->university_id);
                });
            }
            $permissionRequest = $query->findOrFail($id);
        } else {
            // Self-collision stand-in: not necessarily a privileged role,
            // but the acting HOD picked for an applicant who is their own
            // department's HOD needs to see it too.
            $permissionRequest = $query->where(function ($q) use ($user) {
                $q->where('staff_id', $user->id)
                    ->orWhere('acting_hod_id', $user->id);
            })->findOrFail($id);
        }

        if ($user->id !== $permissionRequest->staff_id) {
            AuditLogHelper::log('view', 'permission', $permissionRequest->id);
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
            $hod = User::find($permissionRequest->hod_id);
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
            $hr = User::find($permissionRequest->hr_id);
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
                $totalApprovalTime = $totalMinutes.' minutes';
            } elseif ($totalMinutes < 1440) {
                $totalApprovalTime = round($totalMinutes / 60, 1).' hours';
            } else {
                $totalApprovalTime = round($totalMinutes / 1440, 1).' days';
            }
        }

        return view('shared.permissions.details', compact('permissionRequest', 'approvalTimeline', 'totalApprovalTime'));
    }

    /**
     * Lightweight signature for the live-refresh poll on a single request's
     * detail page.
     */
    public function showHeartbeat($id)
    {
        $permissionRequest = PermissionRequest::with('staff')->findOrFail($id);
        $user = auth()->user();

        $isOwn = $permissionRequest->staff_id === $user->id;
        if (! $isOwn) {
            $authorizedRoles = ['admin', 'hod', 'hr-officer', 'hr-manager', 'internal-auditor', 'registrar'];
            $inAuthorizedRole = in_array($user->getRoleNames()->first(), $authorizedRoles);
            $sameUniversity = $user->hasRole('super-admin') || $permissionRequest->staff->university_id === $user->university_id;
            abort_unless($inAuthorizedRole && $sameUniversity, 403);
        }

        return response()->json(['signature' => (string) $permissionRequest->updated_at]);
    }

    /**
     * Lightweight signature for the live-refresh poll on Pending Approvals -
     * mirrors pendingApprovals()'s scoping so it changes exactly when that
     * page's contents would.
     */
    public function pendingApprovalsHeartbeat()
    {
        $user = auth()->user();
        $role = $user->getRoleNames()->first();

        $query = PermissionRequest::query();
        $travelQuery = TravelPermission::where('status', 'pending')->where('acting_status', 'accepted');

        if ($role == 'hod') {
            $query->whereHas('staff.department', fn ($q) => $q->where('hod_staff_id', $user->id))
                ->where('hod_status', 'pending')
                ->where('staff_id', '!=', $user->id);
            $travelQuery->whereHas('staff.department', fn ($q) => $q->where('hod_staff_id', $user->id));
        } elseif ($role == 'hr-officer' || $role == 'hr-manager') {
            $query->where('hod_status', 'approved')->where('hr_status', 'pending')
                ->where('staff_id', '!=', $user->id)
                ->whereHas('staff', fn ($q) => $q->where('university_id', $user->university_id));
            $travelQuery->where('current_approval_level', 'hr_final')
                ->whereHas('staff', fn ($q) => $q->where('university_id', $user->university_id));
        } elseif ($role == 'admin') {
            $query->where('status', 'pending');
            if (! $user->hasRole('super-admin')) {
                $query->whereHas('staff', fn ($q) => $q->where('university_id', $user->university_id));
                $travelQuery->whereHas('staff', fn ($q) => $q->where('university_id', $user->university_id));
            }
        } else {
            return response()->json(['signature' => null]);
        }

        $signature = $query->count().':'.$query->max('updated_at').'|'.$travelQuery->count().':'.$travelQuery->max('updated_at');

        return response()->json(['signature' => $signature]);
    }

    /**
     * HOD/HR/Admin - Pending Approvals
     */
    public function pendingApprovals(Request $request)
    {
        $perPage = $this->getPerPage($request, 10);
        $user = auth()->user();
        $role = $user->getRoleNames()->first();

        // Self-collision stand-in: this person isn't necessarily an HOD by
        // role, but they're who an applicant (who IS their own department's
        // HOD) picked to stand in for this one review.
        $standinIds = $this->standinHodApprovalIds($user);

        // Short permissions query
        $query = PermissionRequest::with(['staff']);

        if ($role == 'hod') {
            $query->whereHas('staff.department', function ($q) use ($user) {
                $q->where('hod_staff_id', $user->id);
            })
                // Their own request needs the acting HOD they picked, not
                // them - excluded here, surfaced below via $standinIds.
                ->where('staff_id', '!=', $user->id)
                ->where('hod_status', 'pending');

            if ($standinIds->isNotEmpty()) {
                $query->orWhere(function ($q) use ($standinIds) {
                    $q->whereIn('id', $standinIds)->where('hod_status', 'pending');
                });
            }
        } elseif ($role == 'hr-officer' || $role == 'hr-manager') {
            $query->where('hod_status', 'approved')->where('hr_status', 'pending')
                // Same principle as the HOD branch above - an HR officer's
                // own request still reaches this stage (there's no "acting
                // HR" stand-in for it), but they can't act on it themselves,
                // so it has no business appearing in their own queue.
                ->where('staff_id', '!=', $user->id)
                ->whereHas('staff', function ($q) use ($user) {
                    $q->where('university_id', $user->university_id);
                });
        } elseif ($role == 'admin') {
            $query->where('status', 'pending');
            if (! $user->hasRole('super-admin')) {
                $query->whereHas('staff', function ($q) use ($user) {
                    $q->where('university_id', $user->university_id);
                });
            }
        } elseif ($standinIds->isNotEmpty()) {
            $query->whereIn('id', $standinIds)->where('hod_status', 'pending');
        } else {
            return redirect()->route('dashboard');
        }

        $overduePermissionCount = (clone $query)->where('created_at', '<', now()->subHours(48))->count();
        $grandTotal = (clone $query)->count();

        if ($search = $request->get('search')) {
            $query->whereHas('staff', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('staff_id', 'like', "%{$search}%");
            });
        }

        $pendingRequests = $query->orderBy('created_at', 'asc')->paginate($perPage)->appends($request->query());

        if ($request->wantsJson()) {
            return response()->json([
                'html' => view('shared.permissions._pending-approvals-results', compact('pendingRequests'))->render(),
                'total' => $pendingRequests->total(),
                'grand_total' => $grandTotal,
            ]);
        }

        // Travel permissions pending (for combined view)
        $travelQuery = TravelPermission::with(['staff'])
            ->where('status', 'pending')
            ->where('acting_status', 'accepted');

        if ($role == 'hod') {
            $travelQuery->whereHas('staff.department', function ($q) use ($user) {
                $q->where('hod_staff_id', $user->id);
            });
        } elseif ($role == 'hr-officer' || $role == 'hr-manager') {
            $travelQuery->where('current_approval_level', 'hr_final')
                ->whereHas('staff', function ($q) use ($user) {
                    $q->where('university_id', $user->university_id);
                });
        } elseif ($role == 'admin' && ! $user->hasRole('super-admin')) {
            $travelQuery->whereHas('staff', function ($q) use ($user) {
                $q->where('university_id', $user->university_id);
            });
        }

        $overdueTravelCount = (clone $travelQuery)->where('created_at', '<', now()->subHours(48))->count();

        $travelPending = $travelQuery->get();

        $overdueCount = $overduePermissionCount + $overdueTravelCount;

        return view('shared.permissions.pending-approvals', compact('pendingRequests', 'travelPending', 'overdueCount'));
    }

    /**
     * HOD/HR - History of permission requests this user has approved or rejected
     */
    public function approvalsHistory(Request $request)
    {
        $perPage = $this->getPerPage($request, 10);
        $userId = auth()->id();

        $query = AuditLog::where('user_id', $userId)
            ->where('module', 'permission')
            ->whereIn('action', ['approve', 'reject'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('action')) {
            $query->where('action', $request->action === 'approved' ? 'approve' : 'reject');
        }

        if ($search = $request->get('search')) {
            $matchingIds = PermissionRequest::whereHas('staff', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('staff_id', 'like', "%{$search}%");
            })->pluck('id');
            $query->whereIn('record_id', $matchingIds);
        }

        $logs = $query->paginate($perPage)->appends($request->query());

        $permissions = PermissionRequest::with('staff')
            ->whereIn('id', $logs->getCollection()->pluck('record_id'))
            ->get()
            ->keyBy('id');

        $logs->getCollection()->transform(function (AuditLog $log) use ($permissions) {
            $log->permissionRequest = $permissions->get($log->record_id);

            return $log;
        });

        $stats = [
            'total' => AuditLog::where('user_id', $userId)->where('module', 'permission')->whereIn('action', ['approve', 'reject'])->count(),
            'approved' => AuditLog::where('user_id', $userId)->where('module', 'permission')->where('action', 'approve')->count(),
            'rejected' => AuditLog::where('user_id', $userId)->where('module', 'permission')->where('action', 'reject')->count(),
            'this_month' => AuditLog::where('user_id', $userId)->where('module', 'permission')->whereIn('action', ['approve', 'reject'])->whereMonth('created_at', now()->month)->count(),
        ];

        if ($request->wantsJson()) {
            return response()->json([
                'html' => view('shared.permissions._approvals-history-results', ['approvals' => $logs])->render(),
                'total' => $logs->total(),
                'grand_total' => (clone $query)->count(),
            ]);
        }

        return view('shared.permissions.approvals-history', ['approvals' => $logs, 'stats' => $stats]);
    }

    /**
     * HOD - Approve Permission
     */
    public function hodApprove($id)
    {
        $permission = PermissionRequest::findOrFail($id);

        if (! $this->canReviewAsHod(auth()->user(), $permission)) {
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
            Notification::create([
                'user_id' => $permission->staff_id,
                'type' => 'permission_hod_approved',
                'title' => 'Permission Approved by HOD',
                'message' => 'Your permission request has been approved by HOD. Awaiting HR final approval.',
                'action_url' => route('permissions.show', $permission->id),
                'action_text' => 'View Request',
                'is_read' => false,
            ]);

            // Notify every HR officer at this staff member's own institute,
            // not just one - otherwise whichever one isn't picked never
            // learns the request is waiting on them. Institution-wide,
            // never cross-institute.
            foreach (User::role(['hr-officer', 'hr-manager'])->where('university_id', $permission->staff->university_id)->get() as $hr) {
                Notification::create([
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

            return back()->with('error', 'Failed to approve: '.$e->getMessage());
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

        if (! $this->canReviewAsHod(auth()->user(), $permission)) {
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
            Notification::create([
                'user_id' => $permission->staff_id,
                'type' => 'permission_rejected',
                'title' => 'Permission Request Rejected',
                'message' => 'Your permission request has been rejected by HOD. Reason: '.$request->hod_comment,
                'action_url' => route('permissions.show', $permission->id),
                'action_text' => 'View Request',
                'is_read' => false,
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'Permission request rejected.');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Failed to reject: '.$e->getMessage());
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

        if (! auth()->user()->hasRole(['hr-officer', 'hr-manager', 'admin'])) {
            return back()->with('error', 'You are not authorized to approve this request.');
        }

        if (! auth()->user()->hasRole('super-admin') && $permission->staff->university_id !== auth()->user()->university_id) {
            return back()->with('error', 'You are not authorized to approve this request.');
        }

        // An HR officer/manager applying for their own permission still
        // reaches this exact stage - unlike the HOD stage, there's no
        // "acting HR" stand-in, so the guard here is simply: not yourself.
        // Every other HR officer at the institution was already notified
        // when the HOD approved (see hodApprove()), so this never leaves
        // the request stuck.
        if ($permission->staff_id === auth()->id()) {
            return back()->with('error', 'You cannot approve your own permission request - another HR officer or manager must complete this step.');
        }

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
            Notification::create([
                'user_id' => $permission->staff_id,
                'type' => 'permission_approved',
                'title' => 'Permission Request Approved',
                'message' => 'Your permission request has been fully approved.',
                'action_url' => route('permissions.show', $permission->id),
                'action_text' => 'View Request',
                'is_read' => false,
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'Permission request fully approved.');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Failed to approve: '.$e->getMessage());
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

        if (! auth()->user()->hasRole(['hr-officer', 'hr-manager', 'admin'])) {
            return back()->with('error', 'You are not authorized to reject this request.');
        }

        if (! auth()->user()->hasRole('super-admin') && $permission->staff->university_id !== auth()->user()->university_id) {
            return back()->with('error', 'You are not authorized to reject this request.');
        }

        // Same self-collision guard as hrApprove() - see the comment there.
        if ($permission->staff_id === auth()->id()) {
            return back()->with('error', 'You cannot reject your own permission request - another HR officer or manager must complete this step.');
        }

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
            Notification::create([
                'user_id' => $permission->staff_id,
                'type' => 'permission_rejected',
                'title' => 'Permission Request Rejected',
                'message' => 'Your permission request has been rejected by HR. Reason: '.$request->hr_comment,
                'action_url' => route('permissions.show', $permission->id),
                'action_text' => 'View Request',
                'is_read' => false,
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'Permission request rejected.');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Failed to reject: '.$e->getMessage());
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

            return back()->with('error', 'Failed to cancel: '.$e->getMessage());
        }
    }
}
