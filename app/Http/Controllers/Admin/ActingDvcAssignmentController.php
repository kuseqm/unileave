<?php
// app/Http/Controllers/Admin/ActingDvcAssignmentController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActingDvcAssignment;
use App\Models\User;
use Illuminate\Http\Request;

class ActingDvcAssignmentController extends Controller
{
    /**
     * Display a listing of acting DVC assignments.
     */
    public function index()
    {
        $activeAssignments = ActingDvcAssignment::with(['staff', 'approvedBy'])
            ->where('status', 'active')
            ->where('end_date', '>=', now())
            ->get();
            
        $pendingAssignments = ActingDvcAssignment::with(['staff', 'approvedBy'])
            ->where('status', 'pending')
            ->get();
            
        $history = ActingDvcAssignment::with(['staff', 'approvedBy'])
            ->whereIn('status', ['expired', 'rejected'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        $dvcCandidates = User::role(['dvca', 'dvc-pfa'])->get();
        $allStaff = User::where('status', 'active')->get();
        
        return view('admin.acting-dvc.index', compact(
            'activeAssignments', 'pendingAssignments', 'history',
            'dvcCandidates', 'allStaff'
        ));
    }
    
    /**
     * Show the form for creating a new acting DVC assignment.
     */
    public function create()
    {
        $staff = User::where('status', 'active')
            ->orderBy('first_name')
            ->get();
            
        return view('admin.acting-dvc.create', compact('staff'));
    }
    
    /**
     * Store a newly created acting DVC assignment.
     */
    public function store(Request $request)
    {
        $request->validate([
            'staff_id' => 'required|exists:staff,id',
            'role' => 'required|in:dvca,dvc-pfa',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
            'reason' => 'nullable|string',
        ]);
        
        // Check for overlapping assignments
        $overlap = ActingDvcAssignment::where('role', $request->role)
            ->where('status', 'active')
            ->where(function($q) use ($request) {
                $q->whereBetween('start_date', [$request->start_date, $request->end_date])
                  ->orWhereBetween('end_date', [$request->start_date, $request->end_date]);
            })->exists();
            
        if ($overlap) {
            return back()->with('error', 'An acting assignment already exists for this period.');
        }
        
        $assignment = ActingDvcAssignment::create([
            'staff_id' => $request->staff_id,
            'role' => $request->role,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'reason' => $request->reason,
            'status' => 'pending',
        ]);
        
        return redirect()->route('admin.acting-dvc.index')
            ->with('success', 'Acting DVC assignment created and pending approval.');
    }
    
    /**
     * Display the specified acting DVC assignment.
     */
    public function show($id)
    {
        $assignment = ActingDvcAssignment::with(['staff', 'approvedBy'])
            ->findOrFail($id);
            
        return view('admin.acting-dvc.show', compact('assignment'));
    }
    
    /**
     * Show the form for editing the specified acting DVC assignment.
     */
    public function edit($id)
    {
        $assignment = ActingDvcAssignment::findOrFail($id);
        $staff = User::where('status', 'active')->get();
        
        return view('admin.acting-dvc.edit', compact('assignment', 'staff'));
    }
    
    /**
     * Update the specified acting DVC assignment.
     */
    public function update(Request $request, $id)
    {
        $assignment = ActingDvcAssignment::findOrFail($id);
        
        $request->validate([
            'staff_id' => 'required|exists:staff,id',
            'role' => 'required|in:dvca,dvc-pfa',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'reason' => 'nullable|string',
            'status' => 'nullable|in:pending,active,expired,rejected',
        ]);
        
        // If changing from active to something else, remove role
        if ($assignment->status === 'active' && $request->status !== 'active') {
            $assignment->staff->removeRole($assignment->role);
        }
        
        // If changing to active, add role
        if ($request->status === 'active' && $assignment->status !== 'active') {
            $assignment->staff->assignRole($request->role);
        }
        
        $assignment->update([
            'staff_id' => $request->staff_id,
            'role' => $request->role,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'reason' => $request->reason,
            'status' => $request->status ?? $assignment->status,
        ]);
        
        return redirect()->route('admin.acting-dvc.index')
            ->with('success', 'Acting DVC assignment updated successfully.');
    }
    
    /**
     * Approve the specified acting DVC assignment.
     */
    public function approve($id)
    {
        $assignment = ActingDvcAssignment::findOrFail($id);
        
        $assignment->update([
            'status' => 'active',
            'approved_by' => auth()->id(),
        ]);
        
        // Assign temporary role to acting staff
        $assignment->staff->assignRole($assignment->role);
        
        return redirect()->back()->with('success', 'Acting DVC assignment approved.');
    }
    
    /**
     * Remove the specified acting DVC assignment.
     */
    public function destroy($id)
    {
        $assignment = ActingDvcAssignment::findOrFail($id);
        
        // Remove temporary role
        if ($assignment->status === 'active') {
            $assignment->staff->removeRole($assignment->role);
        }
        
        $assignment->update(['status' => 'expired']);
        
        return redirect()->back()->with('success', 'Acting DVC assignment ended.');
    }
}