<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\University;
use App\Models\Campus;
use App\Models\Department;
use App\Traits\PerPageTrait;
use Illuminate\Http\Request;

class UniversityController extends Controller
{
    use PerPageTrait;

    /**
     * Display list of universities
     */
    public function index(Request $request)
    {
        $perPage = $this->getPerPage($request, 10);
        
        $universities = University::withCount('campuses')
            ->orderBy('name')
            ->paginate($perPage);
        
        // Statistics from database
        $totalUniversities = University::count();
        $activeUniversities = University::where('status', 'active')->count();
        $totalCampuses = Campus::count();
        $totalDepartments = Department::count();
        $newThisMonth = University::whereMonth('created_at', now()->month)->count();
        
        return view('admin.universities.index', compact(
            'universities', 'totalUniversities', 'activeUniversities',
            'totalCampuses', 'totalDepartments', 'newThisMonth'
        ));
    }

    /**
     * Show form to create new university
     */
    public function create()
    {
        return view('admin.universities.create');
    }

    /**
     * Store new university
     */
    public function store(Request $request)
    {
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
        return view('admin.universities.edit', compact('university'));
    }

    /**
     * Update university
     */
    public function update(Request $request, $id)
    {
        $university = University::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:universities,code,' . $id,
            'gps_lat' => 'nullable|numeric',
            'gps_lng' => 'nullable|numeric',
            'address' => 'nullable|string',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'status' => 'sometimes|in:active,inactive',
        ]);

        $university->update([
            'name' => $request->name,
            'code' => $request->code,
            'gps_lat' => $request->gps_lat,
            'gps_lng' => $request->gps_lng,
            'address' => $request->address,
            'phone' => $request->phone,
            'email' => $request->email,
            'status' => $request->status ?? $university->status,
        ]);

        return redirect()->route('admin.universities.index')
            ->with('success', 'University updated successfully.');
    }

    /**
     * Delete university
     */
    public function destroy($id)
    {
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