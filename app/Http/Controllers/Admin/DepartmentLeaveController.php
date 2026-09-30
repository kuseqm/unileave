<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\LeaveRequestController;
use App\Models\Department;
use App\Models\LeaveRequest;
use App\Traits\PerPageTrait;
use Illuminate\Http\Request;

class DepartmentLeaveController extends Controller
{
    use PerPageTrait;

    /**
     * Track leave requests within the user's department.
     */
    public function index(Request $request)
    {
        $auth = auth()->user();

        $query = LeaveRequest::with(['staff', 'leaveType', 'actingStaff'])
            ->orderBy('created_at', 'desc');

        if (! $auth->hasRole('super-admin')) {
            $departmentIds = Department::where('hod_staff_id', $auth->id)->pluck('id');

            if ($departmentIds->isEmpty() && $auth->department_id) {
                $departmentIds = collect([$auth->department_id]);
            }

            if ($departmentIds->isNotEmpty()) {
                $query->whereHas('staff', function ($q) use ($departmentIds) {
                    $q->whereIn('department_id', $departmentIds);
                });
            } else {
                $query->whereRaw('0 = 1');
            }
        }

        $grandTotal = (clone $query)->count();

        if ($search = $request->get('search')) {
            $query->whereHas('staff', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('middle_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('staff_id', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status') && in_array($request->status, ['pending', 'approved', 'rejected', 'cancelled'])) {
            $query->where('status', $request->status);
        }

        $perPage = $this->getPerPage($request, 10);

        $requests = $query->paginate($perPage)->appends($request->query());

        if ($request->wantsJson()) {
            return response()->json([
                'html' => view('admin.department-leave._results', compact('requests'))->render(),
                'total' => $requests->total(),
                'grand_total' => $grandTotal,
            ]);
        }

        return view('admin.department-leave.index', compact('requests', 'grandTotal'));
    }

    /**
     * Approve a department-level leave request by reusing the core logic.
     */
    public function approve(Request $request, $id)
    {
        $controller = app(LeaveRequestController::class);

        return $controller->approve($request, $id);
    }

    /**
     * Reject a department-level leave request by reusing the core logic.
     */
    public function reject(Request $request, $id)
    {
        $controller = app(LeaveRequestController::class);

        return $controller->reject($request, $id);
    }
}
