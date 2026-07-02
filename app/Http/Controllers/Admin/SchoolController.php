<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Campus;
use App\Models\School;
use App\Models\Department;
use App\Models\User;
use App\Traits\PerPageTrait;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SchoolController extends Controller
{
    use PerPageTrait;

    /**
     * Display list of schools/faculties
     */
    public function index(Request $request)
    {
        $perPage = $this->getPerPage($request, 10);
        
        $schools = School::with('campus.university', 'dean', 'departments')
            ->orderBy('name')
            ->paginate($perPage);
        
        $totalSchools = School::count();
        $activeSchools = School::where('status', 'active')->count();
        $totalCampuses = Campus::count();
        $totalDepartments = Department::count();
        $newThisMonth = School::whereMonth('created_at', now()->month)->count();
        
        return view('admin.schools.index', compact(
            'schools', 'totalSchools', 'activeSchools',
            'totalCampuses', 'totalDepartments', 'newThisMonth'
        ));
    }

    /**
     * Show form to create new school/faculty
     */
    public function create(Request $request)
    {
        $campuses = Campus::with('university')->where('status', 'active')->get();
        $staff = User::where('status', 'active')->orderBy('first_name')->get();
        $selectedCampus = $request->get('campus_id');
        
        return view('admin.schools.create', compact('campuses', 'staff', 'selectedCampus'));
    }

    /**
     * Store new school/faculty
     */
    public function store(Request $request)
    {
        $request->validate([
            'campus_id' => 'required|exists:campuses,id',
            'name' => 'required|string|max:255|unique:schools,name',
            'code' => 'required|string|max:50|unique:schools,code',
            'dean_staff_id' => 'nullable|exists:staff,id',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        School::create([
            'campus_id' => $request->campus_id,
            'name' => $request->name,
            'code' => strtoupper($request->code),
            'dean_staff_id' => $request->dean_staff_id,
            'description' => $request->description,
            'status' => $request->status ?? 'active',
        ]);

        return redirect()->route('admin.schools.index')
            ->with('success', 'School/Faculty created successfully.');
    }

    /**
     * Show form to edit school/faculty
     */
    public function edit($id)
    {
        $school = School::with('campus.university', 'departments', 'staff')->findOrFail($id);
        $campuses = Campus::with('university')->where('status', 'active')->get();
        $staff = User::where('status', 'active')->orderBy('first_name')->get();
        
        return view('admin.schools.edit', compact('school', 'campuses', 'staff'));
    }

    /**
     * Update school/faculty
     */
    public function update(Request $request, $id)
    {
        $school = School::findOrFail($id);

        $request->validate([
            'campus_id' => 'required|exists:campuses,id',
            'name' => ['required', 'string', 'max:255', Rule::unique('schools')->ignore($id)],
            'code' => ['required', 'string', 'max:50', Rule::unique('schools')->ignore($id)],
            'dean_staff_id' => 'nullable|exists:staff,id',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        $school->update([
            'campus_id' => $request->campus_id,
            'name' => $request->name,
            'code' => strtoupper($request->code),
            'dean_staff_id' => $request->dean_staff_id,
            'description' => $request->description,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.schools.index')
            ->with('success', 'School/Faculty updated successfully.');
    }

    /**
     * Delete school/faculty
     */
    public function destroy($id)
    {
        $school = School::findOrFail($id);

        if ($school->departments()->count() > 0) {
            return redirect()->route('admin.schools.index')
                ->with('error', 'Cannot delete school/faculty with existing departments. Please delete all departments first.');
        }

        $school->delete();

        return redirect()->route('admin.schools.index')
            ->with('success', 'School/Faculty deleted successfully.');
    }

    /**
     * Get schools by campus (AJAX)
     */
    public function getByCampus($campusId)
    {
        $schools = School::where('campus_id', $campusId)
            ->where('status', 'active')
            ->get(['id', 'name', 'code']);
        
        return response()->json($schools);
    }

    /**
     * Get all schools as JSON
     */
    public function getAllJson()
    {
        $schools = School::with('campus')->select('id', 'name', 'campus_id', 'code')->get();
        return response()->json($schools);
    }
}