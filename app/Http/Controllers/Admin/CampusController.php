<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Campus;
use App\Models\Department;
use App\Models\University;
use App\Traits\PerPageTrait;
use Illuminate\Http\Request;

class CampusController extends Controller
{
    use PerPageTrait;

    /**
     * Block a non-super-admin from acting on a campus outside their own institute.
     */
    private function authorizeOwnUniversity(int $universityId): void
    {
        $user = auth()->user();
        abort_unless($user->hasRole('super-admin') || $user->university_id === $universityId, 403);
    }

    /**
     * Display list of campuses
     */
    public function index(Request $request)
    {
        $perPage = $this->getPerPage($request, 10);
        $user = auth()->user();
        $isSuperAdmin = $user->hasRole('super-admin');

        $campusesQuery = Campus::with('university')->orderBy('name');
        $departmentsQuery = Department::query();
        $universitiesQuery = University::query();

        if (! $isSuperAdmin) {
            $campusesQuery->where('university_id', $user->university_id);
            $departmentsQuery->where('campus_id', $user->campus_id);
            $universitiesQuery->where('id', $user->university_id);
        }

        $campuses = $campusesQuery->paginate($perPage);

        $totalCampuses = (clone $campusesQuery)->count();
        $activeCampuses = (clone $campusesQuery)->where('status', 'active')->count();
        $totalUniversities = $universitiesQuery->count();
        $totalDepartments = $departmentsQuery->count();
        $newThisMonth = (clone $campusesQuery)->whereMonth('created_at', now()->month)->count();

        return view('admin.campuses.index', compact(
            'campuses', 'totalCampuses', 'activeCampuses',
            'totalUniversities', 'totalDepartments', 'newThisMonth', 'isSuperAdmin'
        ));
    }

    /**
     * Show form to create new campus
     */
    public function create(Request $request)
    {
        $user = auth()->user();
        $isSuperAdmin = $user->hasRole('super-admin');

        $universities = $isSuperAdmin
            ? University::where('status', 'active')->get()
            : University::where('id', $user->university_id)->get();
        $selectedUniversity = $isSuperAdmin ? $request->get('university_id') : $user->university_id;

        return view('admin.campuses.create', compact('universities', 'selectedUniversity', 'isSuperAdmin'));
    }

    /**
     * Store new campus
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        $isSuperAdmin = $user->hasRole('super-admin');

        $request->validate([
            'university_id' => $isSuperAdmin ? 'required|exists:universities,id' : 'nullable',
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:campuses,code',
            'gps_lat' => 'nullable|numeric',
            'gps_lng' => 'nullable|numeric',
            'address' => 'nullable|string',
            'status' => 'sometimes|in:active,inactive',
        ]);

        // A non-super-admin can only ever create within their own institute -
        // the submitted university_id is never trusted for them.
        $universityId = $isSuperAdmin ? $request->university_id : $user->university_id;

        Campus::create([
            'university_id' => $universityId,
            'name' => $request->name,
            'code' => $request->code,
            'gps_lat' => $request->gps_lat,
            'gps_lng' => $request->gps_lng,
            'address' => $request->address,
            'status' => $request->status ?? 'active',
        ]);

        return redirect()->route('admin.campuses.index')
            ->with('success', 'Campus created successfully.');
    }

    /**
     * Show form to edit campus
     */
    public function edit($id)
    {
        $campus = Campus::with(['university', 'departments', 'staff'])->findOrFail($id);
        $this->authorizeOwnUniversity($campus->university_id);

        $user = auth()->user();
        $isSuperAdmin = $user->hasRole('super-admin');
        $universities = $isSuperAdmin
            ? University::where('status', 'active')->get()
            : University::where('id', $user->university_id)->get();

        return view('admin.campuses.edit', compact('campus', 'universities', 'isSuperAdmin'));
    }

    /**
     * Update campus
     */
    public function update(Request $request, $id)
    {
        $campus = Campus::findOrFail($id);
        $this->authorizeOwnUniversity($campus->university_id);

        $user = auth()->user();
        $isSuperAdmin = $user->hasRole('super-admin');

        $request->validate([
            'university_id' => $isSuperAdmin ? 'required|exists:universities,id' : 'nullable',
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:campuses,code,'.$id,
            'gps_lat' => 'nullable|numeric',
            'gps_lng' => 'nullable|numeric',
            'address' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        $universityId = $isSuperAdmin ? $request->university_id : $campus->university_id;

        $campus->update([
            'university_id' => $universityId,
            'name' => $request->name,
            'code' => $request->code,
            'gps_lat' => $request->gps_lat,
            'gps_lng' => $request->gps_lng,
            'address' => $request->address,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.campuses.index')
            ->with('success', 'Campus updated successfully.');
    }

    /**
     * Delete campus
     */
    public function destroy($id)
    {
        $campus = Campus::findOrFail($id);
        $this->authorizeOwnUniversity($campus->university_id);

        if ($campus->departments()->count() > 0) {
            return redirect()->route('admin.campuses.index')
                ->with('error', 'Cannot delete campus with existing departments.');
        }

        $campus->delete();

        return redirect()->route('admin.campuses.index')
            ->with('success', 'Campus deleted successfully.');
    }

    /**
     * Get campuses by university (AJAX)
     */
    public function getByUniversity($universityId)
    {
        $this->authorizeOwnUniversity((int) $universityId);

        $campuses = Campus::where('university_id', $universityId)
            ->where('status', 'active')
            ->get(['id', 'name']);

        return response()->json($campuses);
    }

    /**
     * Get all campuses as JSON (for edit page)
     */
    public function getAllJson()
    {
        $user = auth()->user();
        $campusesQuery = Campus::select('id', 'name', 'university_id');

        if (! $user->hasRole('super-admin')) {
            $campusesQuery->where('university_id', $user->university_id);
        }

        return response()->json($campusesQuery->get());
    }
}
