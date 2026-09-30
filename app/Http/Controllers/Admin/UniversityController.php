<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Campus;
use App\Models\Department;
use App\Models\University;
use App\Traits\PerPageTrait;
use Illuminate\Http\Request;

class UniversityController extends Controller
{
    use PerPageTrait;

    /**
     * Block a non-super-admin from acting on a university that isn't their own.
     */
    private function authorizeOwnUniversity(University $university): void
    {
        $user = auth()->user();
        abort_unless($user->hasRole('super-admin') || $user->university_id === $university->id, 403);
    }

    /**
     * Display list of universities
     */
    public function index(Request $request)
    {
        $perPage = $this->getPerPage($request, 10);
        $isSuperAdmin = auth()->user()->hasRole('super-admin');

        $universitiesQuery = University::withCount('campuses')->orderBy('name');
        $campusesQuery = Campus::query();
        $departmentsQuery = Department::query();

        if (! $isSuperAdmin) {
            $universitiesQuery->where('id', auth()->user()->university_id);
            $campusesQuery->where('university_id', auth()->user()->university_id);
            $departmentsQuery->where('campus_id', auth()->user()->campus_id);
        }

        $universities = $universitiesQuery->paginate($perPage);

        // Statistics from database, scoped the same way as the listing above
        $totalUniversities = (clone $universitiesQuery)->count();
        $activeUniversities = (clone $universitiesQuery)->where('status', 'active')->count();
        $totalCampuses = $campusesQuery->count();
        $totalDepartments = $departmentsQuery->count();
        $newThisMonth = (clone $universitiesQuery)->whereMonth('created_at', now()->month)->count();

        return view('admin.universities.index', compact(
            'universities', 'totalUniversities', 'activeUniversities',
            'totalCampuses', 'totalDepartments', 'newThisMonth', 'isSuperAdmin'
        ));
    }

    /**
     * Show form to create new university
     */
    public function create()
    {
        // Creating a brand new institute is cross-tenant bootstrapping,
        // not something a single-institute admin should ever need to do.
        abort_unless(auth()->user()->hasRole('super-admin'), 403);

        return view('admin.universities.create');
    }

    /**
     * Store new university
     */
    public function store(Request $request)
    {
        abort_unless(auth()->user()->hasRole('super-admin'), 403);

        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:universities,code',
            'gps_lat' => 'nullable|numeric',
            'gps_lng' => 'nullable|numeric',
            'address' => 'nullable|string',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'status' => 'sometimes|in:active,inactive',
        ]);

        University::create([
            'name' => $request->name,
            'code' => $request->code,
            'gps_lat' => $request->gps_lat,
            'gps_lng' => $request->gps_lng,
            'address' => $request->address,
            'phone' => $request->phone,
            'email' => $request->email,
            'status' => $request->status ?? 'active',
        ]);

        return redirect()->route('admin.universities.index')
            ->with('success', 'University created successfully.');
    }

    /**
     * Show form to edit university
     */
    public function edit($id)
    {
        $university = University::findOrFail($id);
        $this->authorizeOwnUniversity($university);

        return view('admin.universities.edit', compact('university'));
    }

    /**
     * Update university
     */
    public function update(Request $request, $id)
    {
        $university = University::findOrFail($id);
        $this->authorizeOwnUniversity($university);

        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:universities,code,'.$id,
            'gps_lat' => 'nullable|numeric',
            'gps_lng' => 'nullable|numeric',
            'address' => 'nullable|string',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'status' => 'sometimes|in:active,inactive',
            'enabled_addons' => 'sometimes|array',
            'enabled_addons.*' => 'string|in:academic_records',
        ]);

        $attributes = [
            'name' => $request->name,
            'code' => $request->code,
            'gps_lat' => $request->gps_lat,
            'gps_lng' => $request->gps_lng,
            'address' => $request->address,
            'phone' => $request->phone,
            'email' => $request->email,
            'status' => $request->status ?? $university->status,
        ];

        // Opt-in add-ons are granted per institution, not self-service - a
        // single-institution admin can edit their own university's other
        // fields through this same form, but only super-admin (managing
        // every institution) may change which add-ons it has.
        if (auth()->user()->hasRole('super-admin')) {
            $attributes['enabled_addons'] = $request->input('enabled_addons', []);
        }

        $university->update($attributes);

        return redirect()->route('admin.universities.index')
            ->with('success', 'University updated successfully.');
    }

    /**
     * Delete university
     */
    public function destroy($id)
    {
        // Deleting a whole institute is cross-tenant and irreversible -
        // only super-admin may do it, regardless of ownership.
        abort_unless(auth()->user()->hasRole('super-admin'), 403);

        $university = University::findOrFail($id);

        // Check if university has campuses
        if ($university->campuses()->count() > 0) {
            return redirect()->route('admin.universities.index')
                ->with('error', 'Cannot delete university with existing campuses. Please delete all campuses first.');
        }

        $university->delete();

        return redirect()->route('admin.universities.index')
            ->with('success', 'University deleted successfully.');
    }
}
