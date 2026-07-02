<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LeaveType;
use App\Traits\PerPageTrait;  // ADD THIS
use Illuminate\Http\Request;

class LeaveTypeController extends Controller
{
    use PerPageTrait;  // ADD THIS

    /**
     * Display list of leave types
     */
    public function index(Request $request)  // ADD $request parameter
    {
        $perPage = $this->getPerPage($request, 15);  // ADD THIS
        
        $leaveTypes = LeaveType::orderBy('sort_order')
            ->orderBy('name')
            ->paginate($perPage);  // CHANGE: paginate(15) to paginate($perPage)
        
        $totalLeaveTypes = LeaveType::count();
        $activeLeaveTypes = LeaveType::where('status', 'active')->count();
        $certificateRequired = LeaveType::where('requires_certificate', true)->count();
        $deductBalance = LeaveType::where('deduct_balance', true)->count();
        $inactiveLeaveTypes = LeaveType::where('status', 'inactive')->count();  // ADD THIS for stats card
        $newThisMonth = LeaveType::whereMonth('created_at', now()->month)->count();  // ADD THIS for stats card
        
        return view('admin.leave-types.index', compact(
            'leaveTypes', 'totalLeaveTypes', 'activeLeaveTypes', 
            'certificateRequired', 'deductBalance', 'inactiveLeaveTypes', 'newThisMonth'  // ADD new variables
        ));
    }

    /**
     * Show form to create new leave type
     */
    public function create()
    {
        return view('admin.leave-types.create');
    }

    /**
     * Store new leave type
     */
    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:10|unique:leave_types,code',
            'name' => 'required|string|max:100',
            'requires_certificate' => 'boolean',
            'requires_letter' => 'boolean',
            'deduct_balance' => 'boolean',
            'max_consecutive_days' => 'nullable|integer|min:1',
            'status' => 'required|in:active,inactive',
        ]);

        LeaveType::create([
            'code' => strtoupper($request->code),  // ADDED: convert to uppercase
            'name' => $request->name,
            'description' => $request->description,
            'requires_certificate' => $request->has('requires_certificate'),
            'requires_letter' => $request->has('requires_letter'),
            'deduct_balance' => $request->has('deduct_balance'),
            'vc_discretion' => $request->has('vc_discretion'),
            'max_consecutive_days' => $request->max_consecutive_days,
            'is_configurable' => true,
            'status' => $request->status,
            'sort_order' => LeaveType::count() + 1,
        ]);

        return redirect()->route('admin.leave-types.index')
            ->with('success', "Leave type '{$request->name}' created successfully.");
    }

    /**
     * Display a specific leave type
     */
    public function show($id)
    {
        $leaveType = LeaveType::with(['leaveRequests.staff', 'leaveRequests.leaveType'])
            ->findOrFail($id);
        
        return view('admin.leave-types.show', compact('leaveType'));
    }

    /**
     * Show form to edit leave type
     */
    public function edit($id)
    {
        $leaveType = LeaveType::findOrFail($id);
        return view('admin.leave-types.edit', compact('leaveType'));
    }

    /**
     * Update leave type
     */
    public function update(Request $request, $id)
    {
        $leaveType = LeaveType::findOrFail($id);

        $request->validate([
            'code' => 'required|string|max:10|unique:leave_types,code,' . $id,
            'name' => 'required|string|max:100',
            'requires_certificate' => 'boolean',
            'requires_letter' => 'boolean',
            'deduct_balance' => 'boolean',
            'max_consecutive_days' => 'nullable|integer|min:1',
            'status' => 'required|in:active,inactive',
        ]);

        $leaveType->update([
            'code' => strtoupper($request->code),  // ADDED: convert to uppercase
            'name' => $request->name,
            'description' => $request->description,
            'requires_certificate' => $request->has('requires_certificate'),
            'requires_letter' => $request->has('requires_letter'),
            'deduct_balance' => $request->has('deduct_balance'),
            'vc_discretion' => $request->has('vc_discretion'),
            'max_consecutive_days' => $request->max_consecutive_days,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.leave-types.index')
            ->with('success', "Leave type '{$leaveType->name}' updated successfully.");
    }

    /**
     * Delete leave type
     */
    public function destroy($id)
    {
        $leaveType = LeaveType::findOrFail($id);
        
        // Check if leave type is used in any requests
        if ($leaveType->leaveRequests()->count() > 0) {
            return redirect()->route('admin.leave-types.index')
                ->with('error', 'Cannot delete leave type that has existing requests.');
        }
        
        $leaveType->delete();
        
        return redirect()->route('admin.leave-types.index')
            ->with('success', "Leave type '{$leaveType->name}' deleted successfully.");
    }
}