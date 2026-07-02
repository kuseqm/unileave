<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\School;
use App\Models\Campus;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DepartmentController extends Controller
{
    /**
     * Display list of departments
     */
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        
        $departments = Department::with(['school', 'campus', 'hod', 'unitHead'])
            ->orderBy('name')
            ->paginate($perPage);
        
        // Calculate statistics for the dashboard cards
        $totalDepartments = Department::count();
        $activeDepartments = Department::where('status', 'active')->count();
        $totalSchools = School::count();
        $totalStaff = User::count();
        
        // Calculate new departments added this month
        $newThisMonth = Department::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        
        return view('admin.departments.index', compact(
            'departments', 
            'totalDepartments', 
            'activeDepartments', 
            'totalSchools', 
            'totalStaff',
            'newThisMonth'
        ));
    }

    /**
     * Show form to create new department
     */
    public function create()
    {
        $schools = School::with('campus')->where('status', 'active')->get();
        $campuses = Campus::where('status', 'active')->get();
        
        // Get potential HODs (academic staff only)
        $hodCandidates = User::whereIn('staff_category', ['academic'])
            ->where('status', 'active')
            ->orderBy('last_name')
            ->get();
        
        // Get potential Unit Heads (all active staff - can be academic or administrative)
        $unitHeadCandidates = User::where('status', 'active')
            ->orderBy('last_name')
            ->get();
        
        return view('admin.departments.create', compact('schools', 'campuses', 'hodCandidates', 'unitHeadCandidates'));
    }

    /**
     * Store new department
     */
    public function store(Request $request)
    {
        $request->validate([
            'campus_id' => 'required|exists:campuses,id',
            'school_id' => 'required|exists:schools,id',
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:departments,code',
            'hod_staff_id' => 'nullable|exists:users,id',
            'unit_head_staff_id' => 'nullable|exists:users,id',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        Department::create([
            'campus_id' => $request->campus_id,
            'school_id' => $request->school_id,
            'name' => $request->name,
            'code' => $request->code,
            'hod_staff_id' => $request->hod_staff_id,
            'unit_head_staff_id' => $request->unit_head_staff_id,
            'description' => $request->description,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.departments.index')
            ->with('success', 'Department created successfully.');
    }

    /**
     * Display the specified department - SHOW METHOD ADDED
     */
    public function show($id)
    {
        $department = Department::with(['school.campus.university', 'hod', 'unitHead', 'staff'])
            ->findOrFail($id);
        
        return view('admin.departments.show', compact('department'));
    }

    /**
     * Show form to edit department
     */
    public function edit($id)
    {
        $department = Department::with(['school', 'campus'])->findOrFail($id);
        $schools = School::with('campus')->where('status', 'active')->get();
        $campuses = Campus::where('status', 'active')->get();
        
        // Get potential HODs (academic staff only)
        $hodCandidates = User::whereIn('staff_category', ['academic'])
            ->where('status', 'active')
            ->orderBy('last_name')
            ->get();
        
        // Get potential Unit Heads (all active staff)
        $unitHeadCandidates = User::where('status', 'active')
            ->orderBy('last_name')
            ->get();
        
        return view('admin.departments.edit', compact('department', 'schools', 'campuses', 'hodCandidates', 'unitHeadCandidates'));
    }

    /**
     * Update department
     */
    public function update(Request $request, $id)
    {
        $department = Department::findOrFail($id);

        $request->validate([
            'campus_id' => 'required|exists:campuses,id',
            'school_id' => 'required|exists:schools,id',
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:departments,code,' . $id,
            'hod_staff_id' => 'nullable|exists:users,id',
            'unit_head_staff_id' => 'nullable|exists:users,id',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        $department->update([
            'campus_id' => $request->campus_id,
            'school_id' => $request->school_id,
            'name' => $request->name,
            'code' => $request->code,
            'hod_staff_id' => $request->hod_staff_id,
            'unit_head_staff_id' => $request->unit_head_staff_id,
            'description' => $request->description,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.departments.index')
            ->with('success', 'Department updated successfully.');
    }

    /**
     * Delete department
     */
    public function destroy($id)
    {
        $department = Department::findOrFail($id);
        
        // Check if department has staff (users)
        if ($department->staff()->count() > 0) {
            return redirect()->route('admin.departments.index')
                ->with('error', 'Cannot delete department. It has staff members assigned.');
        }

        $department->delete();

        return redirect()->route('admin.departments.index')
            ->with('success', 'Department deleted successfully.');
    }

    /**
     * AJAX: Get departments by school
     */
    public function getBySchool($schoolId)
    {
        $departments = Department::where('school_id', $schoolId)
            ->where('status', 'active')
            ->get(['id', 'name']);
        
        return response()->json($departments);
    }

    /**
     * AJAX: Get all departments as JSON
     */
    public function getAllJson()
    {
        $departments = Department::where('status', 'active')
            ->with('school')
            ->get(['id', 'name', 'school_id']);
        
        return response()->json($departments);
    }

    /**
     * AJAX: Get schools by campus for cascading select
     */
    public function getSchoolsByCampus($campusId)
    {
        $schools = School::where('campus_id', $campusId)
            ->where('status', 'active')
            ->get(['id', 'name']);
        
        return response()->json($schools);
    }

    /**
     * Bulk update departments
     */
    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'department_ids' => 'required|array',
            'department_ids.*' => 'exists:departments,id',
            'status' => 'nullable|in:active,inactive',
            'hod_staff_id' => 'nullable|exists:users,id',
        ]);

        Department::whereIn('id', $request->department_ids)->update([
            'status' => $request->status,
            'hod_staff_id' => $request->hod_staff_id,
        ]);

        return redirect()->route('admin.departments.index')
            ->with('success', 'Departments updated successfully.');
    }

    /**
     * Toggle department status
     */
    public function toggleStatus($id)
    {
        $department = Department::findOrFail($id);
        $department->status = $department->status === 'active' ? 'inactive' : 'active';
        $department->save();

        return redirect()->back()
            ->with('success', 'Department status updated successfully.');
    }

    /**
     * Export departments to Excel
     */
    public function export()
    {
        // This will be implemented later
        return redirect()->back()->with('info', 'Export functionality coming soon.');
    }

    /**
     * Export departments to PDF
     */
    public function exportPdf()
    {
        // This will be implemented later
        return redirect()->back()->with('info', 'PDF export functionality coming soon.');
    }
}