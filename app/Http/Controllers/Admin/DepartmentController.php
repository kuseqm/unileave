<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Campus;
use App\Models\Department;
use App\Models\School;
use App\Models\User;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    /**
     * Block a non-super-admin from acting on a department outside their own institute.
     */
    private function authorizeOwnUniversity(int $universityId): void
    {
        $user = auth()->user();
        abort_unless($user->hasRole('super-admin') || $user->university_id === $universityId, 403);
    }

    /**
     * Display list of departments
     */
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $user = auth()->user();
        $isSuperAdmin = $user->hasRole('super-admin');

        $departmentsQuery = Department::with(['school', 'campus', 'hod', 'unitHead'])->orderBy('name');
        $schoolsQuery = School::query();
        $staffQuery = User::query();

        if (! $isSuperAdmin) {
            $departmentsQuery->where('campus_id', $user->campus_id);
            $schoolsQuery->whereHas('campus', function ($q) use ($user) {
                $q->where('university_id', $user->university_id);
            });
            $staffQuery->where('university_id', $user->university_id);
        }

        $departments = $departmentsQuery->paginate($perPage);

        // Calculate statistics for the dashboard cards
        $totalDepartments = (clone $departmentsQuery)->count();
        $activeDepartments = (clone $departmentsQuery)->where('status', 'active')->count();
        $totalSchools = $schoolsQuery->count();
        $totalStaff = $staffQuery->count();

        // Calculate new departments added this month
        $newThisMonth = (clone $departmentsQuery)->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        return view('admin.departments.index', compact(
            'departments',
            'totalDepartments',
            'activeDepartments',
            'totalSchools',
            'totalStaff',
            'newThisMonth',
            'isSuperAdmin'
        ));
    }

    /**
     * Show form to create new department
     */
    public function create()
    {
        $user = auth()->user();
        $isSuperAdmin = $user->hasRole('super-admin');

        $schoolsQuery = School::with('campus')->where('status', 'active');
        $campusesQuery = Campus::where('status', 'active');
        $hodCandidatesQuery = User::whereIn('staff_category', ['academic'])->where('status', 'active')->orderBy('last_name');
        $unitHeadCandidatesQuery = User::where('status', 'active')->orderBy('last_name');

        if (! $isSuperAdmin) {
            $schoolsQuery->whereHas('campus', function ($q) use ($user) {
                $q->where('university_id', $user->university_id);
            });
            $campusesQuery->where('university_id', $user->university_id);
            $hodCandidatesQuery->where('university_id', $user->university_id);
            $unitHeadCandidatesQuery->where('university_id', $user->university_id);
        }

        $schools = $schoolsQuery->get();
        $campuses = $campusesQuery->get();
        $hodCandidates = $hodCandidatesQuery->get();
        $unitHeadCandidates = $unitHeadCandidatesQuery->get();

        return view('admin.departments.create', compact('schools', 'campuses', 'hodCandidates', 'unitHeadCandidates', 'isSuperAdmin'));
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

        $campus = Campus::findOrFail($request->campus_id);
        $this->authorizeOwnUniversity($campus->university_id);

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
        $this->authorizeOwnUniversity($department->campus->university_id);

        return view('admin.departments.show', compact('department'));
    }

    /**
     * Show form to edit department
     */
    public function edit($id)
    {
        $department = Department::with(['school', 'campus'])->findOrFail($id);
        $this->authorizeOwnUniversity($department->campus->university_id);

        $user = auth()->user();
        $isSuperAdmin = $user->hasRole('super-admin');

        $schoolsQuery = School::with('campus')->where('status', 'active');
        $campusesQuery = Campus::where('status', 'active');
        $hodCandidatesQuery = User::whereIn('staff_category', ['academic'])->where('status', 'active')->orderBy('last_name');
        $unitHeadCandidatesQuery = User::where('status', 'active')->orderBy('last_name');

        if (! $isSuperAdmin) {
            $schoolsQuery->whereHas('campus', function ($q) use ($user) {
                $q->where('university_id', $user->university_id);
            });
            $campusesQuery->where('university_id', $user->university_id);
            $hodCandidatesQuery->where('university_id', $user->university_id);
            $unitHeadCandidatesQuery->where('university_id', $user->university_id);
        }

        $schools = $schoolsQuery->get();
        $campuses = $campusesQuery->get();
        $hodCandidates = $hodCandidatesQuery->get();
        $unitHeadCandidates = $unitHeadCandidatesQuery->get();

        return view('admin.departments.edit', compact('department', 'schools', 'campuses', 'hodCandidates', 'unitHeadCandidates', 'isSuperAdmin'));
    }

    /**
     * Update department
     */
    public function update(Request $request, $id)
    {
        $department = Department::findOrFail($id);
        $this->authorizeOwnUniversity($department->campus->university_id);

        $request->validate([
            'campus_id' => 'required|exists:campuses,id',
            'school_id' => 'required|exists:schools,id',
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:departments,code,'.$id,
            'hod_staff_id' => 'nullable|exists:users,id',
            'unit_head_staff_id' => 'nullable|exists:users,id',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        $campus = Campus::findOrFail($request->campus_id);
        $this->authorizeOwnUniversity($campus->university_id);

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
        $this->authorizeOwnUniversity($department->campus->university_id);

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
        $school = School::with('campus')->findOrFail($schoolId);
        $this->authorizeOwnUniversity($school->campus->university_id);

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
        $user = auth()->user();
        $departmentsQuery = Department::where('status', 'active')->with('school');

        if (! $user->hasRole('super-admin')) {
            $departmentsQuery->where('campus_id', $user->campus_id);
        }

        return response()->json($departmentsQuery->get(['id', 'name', 'school_id']));
    }

    /**
     * AJAX: Get schools by campus for cascading select
     */
    public function getSchoolsByCampus($campusId)
    {
        $campus = Campus::findOrFail($campusId);
        $this->authorizeOwnUniversity($campus->university_id);

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

        $user = auth()->user();
        $departmentsQuery = Department::whereIn('id', $request->department_ids);

        if (! $user->hasRole('super-admin')) {
            // Silently drop any ids outside the admin's own campus rather than
            // trusting the submitted list - the checkboxes only ever render
            // this admin's own departments, so a mismatch means tampering.
            $departmentsQuery->where('campus_id', $user->campus_id);
        }

        $departmentsQuery->update([
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
        $this->authorizeOwnUniversity($department->campus->university_id);

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
