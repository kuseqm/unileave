<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Traits\PerPageTrait;
use Illuminate\Http\Request;

class AcademicYearController extends Controller
{
    use PerPageTrait;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $academicYears = AcademicYear::orderByDesc('start_date')->paginate($this->getPerPage($request, 20))->appends($request->query());

        return view('admin.academic-years.index', compact('academicYears'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $latest = AcademicYear::orderByDesc('end_date')->first();

        // Suggest the next academic year's dates so the admin isn't typing
        // from scratch every year - still has to review and confirm them.
        if ($latest) {
            $suggestedStart = $latest->end_date->copy()->addDay();
        } elseif (now()->month >= 10) {
            $suggestedStart = now()->copy()->startOfMonth();
        } else {
            $suggestedStart = now()->copy()->subYear()->month(10)->startOfMonth();
        }
        $suggestedEnd = $suggestedStart->copy()->addYear()->subDay();
        $suggestedLabel = $suggestedStart->format('Y').'/'.$suggestedEnd->format('Y');

        return view('admin.academic-years.create', compact('suggestedStart', 'suggestedEnd', 'suggestedLabel'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $this->validated($request);

        AcademicYear::create([
            ...$validated,
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('admin.academic-years.index')
            ->with('success', 'Academic year created. Activate it from the list when you are ready for it to take effect.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $academicYear = AcademicYear::findOrFail($id);

        return view('admin.academic-years.edit', compact('academicYear'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $academicYear = AcademicYear::findOrFail($id);
        $academicYear->update($this->validated($request));

        return redirect()->route('admin.academic-years.index')
            ->with('success', 'Academic year updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $academicYear = AcademicYear::findOrFail($id);

        if ($academicYear->is_current) {
            return redirect()->route('admin.academic-years.index')
                ->with('error', 'You cannot delete the currently active academic year.');
        }

        $academicYear->delete();

        return redirect()->route('admin.academic-years.index')
            ->with('success', 'Academic year deleted.');
    }

    /**
     * Make this the one active academic year - a deliberate, explicit step
     * rather than something the system decides on its own, since it
     * controls when leave balances/leave-year windows roll over.
     */
    public function activate(string $id)
    {
        $academicYear = AcademicYear::findOrFail($id);
        $academicYear->activate();

        return redirect()->route('admin.academic-years.index')
            ->with('success', "{$academicYear->label} is now the active academic year.");
    }

    /**
     * Switch which semester is current within the active academic year.
     */
    public function setSemester(Request $request, string $id)
    {
        $validated = $request->validate([
            'current_semester' => 'required|in:1,2',
        ]);

        $academicYear = AcademicYear::findOrFail($id);
        $academicYear->update(['current_semester' => $validated['current_semester']]);

        return redirect()->route('admin.academic-years.index')
            ->with('success', 'Current semester updated.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'label' => 'required|string|max:20',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'semester_1_name' => 'required|string|max:50',
            'semester_1_start' => 'required|date|after_or_equal:start_date',
            'semester_1_end' => 'required|date|after:semester_1_start',
            'semester_2_name' => 'required|string|max:50',
            'semester_2_start' => 'required|date|after:semester_1_end',
            'semester_2_end' => 'required|date|after:semester_2_start|before_or_equal:end_date',
        ]);
    }
}
