<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\University;
use App\Models\Campus;
use App\Models\Department;
use App\Traits\PerPageTrait;
use Illuminate\Http\Request;

class CampusController extends Controller
{
    use PerPageTrait;

    /**
     * Display list of campuses
     */
    public function index(Request $request)
    {
        $perPage = $this->getPerPage($request, 10);
        
        $campuses = Campus::with('university')
            ->orderBy('name')
            ->paginate($perPage);
        
        $totalCampuses = Campus::count();
        $activeCampuses = Campus::where('status', 'active')->count();
        $totalUniversities = University::count();
        $totalDepartments = Department::count();
        $newThisMonth = Campus::whereMonth('created_at', now()->month)->count();
        
        return view('admin.campuses.index', compact(
            'campuses', 'totalCampuses', 'activeCampuses',
            'totalUniversities', 'totalDepartments', 'newThisMonth'
        ));
    }

    /**
     * Show form to create new campus
     */
    public function create(Request $request)
    {
        $universities = University::where('status', 'active')->get();
        $selectedUniversity = $request->get('university_id');
        return view('admin.campuses.create', compact('universities', 'selectedUniversity'));
    }

    /**
     * Store new campus
     */
    public function store(Request $request)
    {
        $request->validate([
            'university_id' => 'required|exists:universities,id',
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:campuses,code',
            'gps_lat' => 'nullable|numeric',
            'gps_lng' => 'nullable|numeric',
            'address' => 'nullable|string',
            'status' => 'sometimes|in:active,inactive',
        ]);

        Campus::create([
            'university_id' => $request->university_id,
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
        $universities = University::where('status', 'active')->get();
        return view('admin.campuses.edit', compact('campus', 'universities'));
    }

    /**
     * Update campus
     */
    public function update(Request $request, $id)
    {
        $campus = Campus::findOrFail($id);

        $request->validate([
            'university_id' => 'required|exists:universities,id',
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:campuses,code,' . $id,
            'gps_lat' => 'nullable|numeric',
            'gps_lng' => 'nullable|numeric',
            'address' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        $campus->update([
            'university_id' => $request->university_id,
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
        $campuses = Campus::select('id', 'name', 'university_id')->get();
        return response()->json($campuses);
    }
}