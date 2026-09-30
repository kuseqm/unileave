<?php

namespace App\Http\Controllers;

use App\Models\Campus;
use App\Models\GraduationCeremony;
use App\Models\Program;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * Lets the Examinations Officer (or anyone else with `import_students`)
 * add the reference data the Graduation Roster import depends on - a new
 * Faculty/School, a new Programme, or a new Graduation Ceremony - without
 * needing the heavier institution-wide `manage_schools`/`manage_departments`
 * permissions meant for general admin. Deliberately a separate, lean
 * controller rather than reusing Admin\SchoolController, since that one is
 * gated by a different permission and carries fields (dean, description)
 * this workflow doesn't need.
 */
class AcademicCatalogController extends Controller
{
    /**
     * SJUT's real organisational unit types - the 8 real "schools" are a
     * mix of these (CICT is a Centre, IDS an Institute, the rest are
     * School/Faculty), and Directorate/Unit are real possibilities per the
     * registry even though none exist yet.
     */
    public const SCHOOL_TYPES = ['Directorate', 'Faculty', 'School', 'Centre', 'Institute', 'Unit'];

    /** A photo of the physical book: a common image type, small enough to open quickly. */
    private const BOOK_COVER_RULES = 'nullable|file|image|mimes:jpg,jpeg,png,webp|max:4096';

    public function index()
    {
        $university = auth()->user()->university;

        $campuses = Campus::where('university_id', $university->id)
            ->active()
            ->orderBy('name')
            ->get();

        $schools = School::whereHas('campus', fn ($query) => $query->where('university_id', $university->id))
            ->with('campus')
            ->orderBy('name')
            ->get();

        $programs = Program::whereHas(
            'school.campus',
            fn ($query) => $query->where('university_id', $university->id)
        )
            ->with('school')
            ->orderBy('name')
            ->get();

        $graduationCeremonies = GraduationCeremony::orderBy('year', 'desc')->get();

        $schoolTypes = self::SCHOOL_TYPES;

        return view('students.catalog', compact('campuses', 'schools', 'programs', 'graduationCeremonies', 'schoolTypes'));
    }

    public function storeSchool(Request $request)
    {
        $university = auth()->user()->university;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:'.implode(',', self::SCHOOL_TYPES),
            'code' => 'required|string|max:50',
            'campus_id' => 'required|integer|exists:campuses,id',
        ]);

        $campus = Campus::where('university_id', $university->id)->findOrFail($validated['campus_id']);

        School::create([
            'campus_id' => $campus->id,
            'name' => $validated['name'],
            'type' => $validated['type'],
            'code' => $validated['code'],
            'status' => 'active',
        ]);

        return redirect()->route('students.catalog.index')->with('success', "Faculty/School '{$validated['name']}' added.");
    }

    public function storeProgram(Request $request)
    {
        $university = auth()->user()->university;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'study_level' => 'required|string|max:50',
            'duration_years' => 'required|integer|min:1|max:6',
            'school_id' => 'required|integer|exists:schools,id',
        ]);

        $school = School::whereHas('campus', fn ($query) => $query->where('university_id', $university->id))
            ->findOrFail($validated['school_id']);

        $existing = Program::whereHas('school.campus', fn ($query) => $query->where('university_id', $university->id))
            ->whereRaw('LOWER(TRIM(name)) = ?', [mb_strtolower(trim($validated['name']))])
            ->first();

        if ($existing) {
            return back()->withInput()->withErrors([
                'name' => "A programme with this name already exists ({$existing->code}) - pick it from the list instead of adding it again.",
            ]);
        }

        Program::create([
            'school_id' => $school->id,
            'name' => $validated['name'],
            'code' => $validated['code'] ?? null,
            'study_level' => $validated['study_level'],
            'duration_years' => $validated['duration_years'],
            'status' => 'active',
        ]);

        return redirect()->route('students.catalog.index')->with('success', "Programme '{$validated['name']}' added.");
    }

    /** Corrects a programme - most often the Faculty/School it was put under by mistake. */
    public function updateProgram(Request $request, Program $program)
    {
        $university = auth()->user()->university;

        abort_unless(
            Program::whereKey($program->id)
                ->whereHas('school.campus', fn ($query) => $query->where('university_id', $university->id))
                ->exists(),
            403
        );

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'study_level' => 'required|string|max:50',
            'duration_years' => 'required|integer|min:1|max:6',
            'school_id' => 'required|integer|exists:schools,id',
        ]);

        $school = School::whereHas('campus', fn ($query) => $query->where('university_id', $university->id))
            ->findOrFail($validated['school_id']);

        $duplicate = Program::whereHas('school.campus', fn ($query) => $query->where('university_id', $university->id))
            ->whereKeyNot($program->id)
            ->whereRaw('LOWER(TRIM(name)) = ?', [mb_strtolower(trim($validated['name']))])
            ->first();

        if ($duplicate) {
            return back()->withInput()->withErrors([
                'name' => "Another programme already has this name ({$duplicate->code}).",
            ]);
        }

        $movedFaculty = $program->school_id !== $school->id;

        $program->update([
            'school_id' => $school->id,
            'name' => $validated['name'],
            'code' => $validated['code'] ?? null,
            'study_level' => $validated['study_level'],
            'duration_years' => $validated['duration_years'],
        ]);

        return redirect()->route('students.catalog.index')->with(
            'success',
            "Programme '{$program->name}' updated".($movedFaculty ? " - now under {$school->code}. Its graduates follow it there." : '.')
        );
    }

    public function updateProgramDuration(Request $request, Program $program)
    {
        $university = auth()->user()->university;

        abort_unless(
            Program::whereKey($program->id)
                ->whereHas('school.campus', fn ($query) => $query->where('university_id', $university->id))
                ->exists(),
            403
        );

        $validated = $request->validate([
            'duration_years' => 'required|integer|min:1|max:6',
        ]);

        $program->update($validated);

        return redirect()->route('students.catalog.index')->with('success', "Duration of '{$program->name}' set to {$validated['duration_years']} year(s).");
    }

    public function storeGraduationCeremony(Request $request)
    {
        $validated = $request->validate([
            'year' => 'required|integer|min:1900|max:2100|unique:graduation_ceremonies,year',
            'sequence_number' => 'required|integer|min:1',
            'ceremony_date' => 'required|date',
            'book_cover' => self::BOOK_COVER_RULES,
        ]);

        $ceremony = GraduationCeremony::create(collect($validated)->except('book_cover')->all());

        if ($request->hasFile('book_cover')) {
            $this->storeBookCover($ceremony, $request->file('book_cover'));
        }

        return redirect()->route('students.catalog.index')->with('success', "Graduation Ceremony for {$validated['year']} added.");
    }

    public function updateBookCover(Request $request, GraduationCeremony $graduationCeremony)
    {
        $request->validate(['book_cover' => str_replace('nullable', 'required', self::BOOK_COVER_RULES)]);

        $this->storeBookCover($graduationCeremony, $request->file('book_cover'));

        return redirect()->route('students.catalog.index')->with('success', "Book cover for the {$graduationCeremony->year} graduation saved.");
    }

    public function showBookCover(GraduationCeremony $graduationCeremony)
    {
        abort_unless(
            $graduationCeremony->book_cover_path && Storage::disk('local')->exists($graduationCeremony->book_cover_path),
            404
        );

        return response()->file(Storage::disk('local')->path($graduationCeremony->book_cover_path));
    }

    private function storeBookCover(GraduationCeremony $ceremony, $file): void
    {
        $previous = $ceremony->book_cover_path;

        $path = $file->storeAs('graduation-book-covers', "{$ceremony->year}-".now()->timestamp.'.'.$file->extension(), 'local');
        $ceremony->update(['book_cover_path' => $path]);

        if ($previous) {
            Storage::disk('local')->delete($previous);
        }
    }
}
