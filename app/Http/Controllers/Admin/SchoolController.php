<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Campus;
use App\Models\Department;
use App\Models\School;
use App\Models\User;
use App\Traits\PerPageTrait;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SchoolController extends Controller
{
    use PerPageTrait;

    /**
     * Block a non-super-admin from acting on a school outside their own institute.
     */
    private function authorizeOwnUniversity(int $universityId): void
    {
        $user = auth()->user();
        abort_unless($user->hasRole('super-admin') || $user->university_id === $universityId, 403);
    }

    /**
     * Display list of schools/faculties
     */
    public function index(Request $request)
    {
        $perPage = $this->getPerPage($request, 10);
        $user = auth()->user();
        $isSuperAdmin = $user->hasRole('super-admin');

        $schoolsQuery = School::with('campus.university', 'dean', 'departments')->orderBy('name');
        $campusesQuery = Campus::query();
        $departmentsQuery = Department::query();

        if (! $isSuperAdmin) {
            $schoolsQuery->whereHas('campus', function ($q) use ($user) {
                $q->where('university_id', $user->university_id);
            });
            $campusesQuery->where('university_id', $user->university_id);
            $departmentsQuery->where('campus_id', $user->campus_id);
        }

        $schools = $schoolsQuery->paginate($perPage);

        $totalSchools = (clone $schoolsQuery)->count();
        $activeSchools = (clone $schoolsQuery)->where('status', 'active')->count();
        $totalCampuses = $campusesQuery->count();
        $totalDepartments = $departmentsQuery->count();
        $newThisMonth = (clone $schoolsQuery)->whereMonth('created_at', now()->month)->count();

        return view('admin.schools.index', compact(
            'schools', 'totalSchools', 'activeSchools',
            'totalCampuses', 'totalDepartments', 'newThisMonth', 'isSuperAdmin'
        ));
    }

    /**
     * Show form to create new school/faculty
     */
    public function create(Request $request)
    {
        $user = auth()->user();
        $isSuperAdmin = $user->hasRole('super-admin');

        $campusesQuery = Campus::with('university')->where('status', 'active');
        $staffQuery = User::where('status', 'active')->orderBy('first_name');

        if (! $isSuperAdmin) {
            $campusesQuery->where('university_id', $user->university_id);
            $staffQuery->where('university_id', $user->university_id);
        }

        $campuses = $campusesQuery->get();
        $staff = $staffQuery->get();
        $selectedCampus = $request->get('campus_id');

        return view('admin.schools.create', compact('campuses', 'staff', 'selectedCampus', 'isSuperAdmin'));
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

        $campus = Campus::findOrFail($request->campus_id);
        $this->authorizeOwnUniversity($campus->university_id);

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
        $this->authorizeOwnUniversity($school->campus->university_id);

        $user = auth()->user();
        $isSuperAdmin = $user->hasRole('super-admin');
        $campusesQuery = Campus::with('university')->where('status', 'active');
        $staffQuery = User::where('status', 'active')->orderBy('first_name');

        if (! $isSuperAdmin) {
            $campusesQuery->where('university_id', $user->university_id);
            $staffQuery->where('university_id', $user->university_id);
        }

        $campuses = $campusesQuery->get();
        $staff = $staffQuery->get();

        return view('admin.schools.edit', compact('school', 'campuses', 'staff', 'isSuperAdmin'));
    }

    /**
     * Update school/faculty
     */
    public function update(Request $request, $id)
    {
        $school = School::findOrFail($id);
        $this->authorizeOwnUniversity($school->campus->university_id);

        $request->validate([
            'campus_id' => 'required|exists:campuses,id',
            'name' => ['required', 'string', 'max:255', Rule::unique('schools')->ignore($id)],
            'code' => ['required', 'string', 'max:50', Rule::unique('schools')->ignore($id)],
            'dean_staff_id' => 'nullable|exists:staff,id',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        $campus = Campus::findOrFail($request->campus_id);
        $this->authorizeOwnUniversity($campus->university_id);

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
        $this->authorizeOwnUniversity($school->campus->university_id);

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
        $campus = Campus::findOrFail($campusId);
        $this->authorizeOwnUniversity($campus->university_id);

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
        $user = auth()->user();
        $schoolsQuery = School::with('campus')->select('id', 'name', 'campus_id', 'code');

        if (! $user->hasRole('super-admin')) {
            $schoolsQuery->whereHas('campus', function ($q) use ($user) {
                $q->where('university_id', $user->university_id);
            });
        }

        return response()->json($schoolsQuery->get());
    }
}
