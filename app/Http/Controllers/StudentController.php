<?php

namespace App\Http\Controllers;

use App\Models\Campus;
use App\Models\GraduationCeremony;
use App\Models\Program;
use App\Models\Student;
use App\Services\StudentRosterImportService;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

/**
 * Search/verify students and their graduation status - the first slice of
 * GMSA's graduation-verification feature, built inside sms_demo instead of
 * a separate Filament project (see gmsa's own CLAUDE.md "SUPERSEDED"
 * notice). Deliberately kept separate from the staff/User domain - a
 * Student is never a User, never gets a role, never touches HR tables.
 */
class StudentController extends Controller
{
    private function isCrossInstitute($user): bool
    {
        return $user->hasRole('super-admin');
    }

    private function baseQuery(Request $request, $user, bool $withSearch = true)
    {
        $query = Student::with(['program', 'university'])->orderBy('full_name');

        if (! $this->isCrossInstitute($user)) {
            $query->where('university_id', $user->university_id);
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('is_graduated', $request->status === 'graduated');
        }

        if ($withSearch && $search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('registration_number', 'like', "%{$search}%")
                    ->orWhere('full_name', 'like', "%{$search}%");
            });
        }

        return $query;
    }

    public function index(Request $request)
    {
        $user = auth()->user();

        $students = $this->baseQuery($request, $user)
            ->paginate($request->get('per_page', 25))
            ->appends($request->query());

        if ($request->wantsJson()) {
            $grandTotal = $this->baseQuery($request, $user, false)->count();

            return response()->json([
                'html' => view('students._results', compact('students'))->render(),
                'total' => $students->total(),
                'grand_total' => $grandTotal,
            ]);
        }

        $statsScope = fn () => $this->isCrossInstitute($user)
            ? Student::query()
            : Student::where('university_id', $user->university_id);

        $stats = [
            'total' => $statsScope()->count(),
            'graduated' => $statsScope()->where('is_graduated', true)->count(),
            'not_graduated' => $statsScope()->where('is_graduated', false)->count(),
        ];

        $graduateCounts = Student::where('university_id', $user->university_id)
            ->where('is_graduated', true)
            ->whereNotNull('graduation_year')
            ->selectRaw("COUNT(*) AS total, SUM(gender = 'M') AS males, SUM(gender = 'F') AS females")
            ->first();

        $latestYears = Student::where('university_id', $user->university_id)
            ->where('is_graduated', true)
            ->whereNotNull('graduation_year')
            ->selectRaw("graduation_year AS label, SUM(gender = 'M') AS males, SUM(gender = 'F') AS females, COUNT(*) AS total")
            ->groupBy('graduation_year')
            ->orderByDesc('graduation_year')
            ->get();

        return view('students.index', [
            'students' => $students,
            'stats' => $stats,
            'graduateCounts' => $graduateCounts,
            'latestYears' => $latestYears,
            'isCrossInstitute' => $this->isCrossInstitute($user),
        ]);
    }

    public function filtered(Request $request)
    {
        $user = auth()->user();

        $query = Student::with(['program', 'campus'])
            ->where('university_id', $user->university_id)
            ->where('is_graduated', true)
            ->whereNotNull('graduation_year')
            ->orderBy('last_name')
            ->orderBy('first_name');

        $gender = $request->query('gender');
        if (in_array($gender, ['M', 'F'], true)) {
            $query->where('gender', $gender);
        }

        if ($campusId = $request->integer('campus_id')) {
            $query->where('campus_id', $campusId);
        }

        if ($year = $request->integer('year')) {
            $query->where('graduation_year', $year);
        }

        $byYear = (clone $query)
            ->reorder()
            ->selectRaw('graduation_year AS label, COUNT(*) AS total')
            ->groupBy('graduation_year')
            ->orderByDesc('graduation_year')
            ->get();

        $byProgramme = (clone $query)
            ->reorder()
            ->join('programs', 'programs.id', '=', 'students.program_id')
            ->selectRaw('programs.name AS label, COUNT(*) AS total')
            ->groupBy('programs.id', 'programs.name')
            ->orderByDesc('total')
            ->orderBy('programs.name')
            ->limit(10)
            ->get();

        $students = $query->orderByDesc('graduation_year')->orderBy('last_name')->orderBy('first_name')->orderBy('program_id')->paginate($request->get('per_page', 25))
            ->appends($request->query());

        return view('students.filtered', [
            'students' => $students,
            'gender' => $gender,
            'campusId' => $campusId,
            'year' => $year,
            'byYear' => $byYear,
            'byProgramme' => $byProgramme,
        ]);
    }

    public function show(Student $student)
    {
        abort_unless(
            $this->isCrossInstitute(auth()->user()) || $student->university_id === auth()->user()->university_id,
            403
        );

        $student->load(['program.school', 'university', 'campus', 'graduationLetters' => fn ($q) => $q->latest()]);

        return view('students.show', compact('student'));
    }

    /**
     * The fields a Graduation Roster import never collects (degree
     * wording, classification, GPA, programme duration) but that letter
     * generation reads - see GraduationLetterController::defaultBodyContent().
     * Filled in here, once, when an actual verification request needs
     * them - not required at import time.
     */
    public function edit(Student $student, Request $request)
    {
        abort_unless(
            $this->isCrossInstitute(auth()->user()) || $student->university_id === auth()->user()->university_id,
            403
        );

        $student->load(['program', 'campus']);

        // A person can have more than one graduation (e.g. NTA Level 5 one
        // year, NTA Level 6 another) - degree/classification/GPA/dates below
        // belong to ONE specific graduation, never to "whichever is latest"
        // guessed silently, so the form always knows exactly which one it is
        // editing. `?graduation=` picks it; the latest is the default.
        $graduations = $student->graduations()->with('program')->orderByDesc('graduation_year')->orderByDesc('id')->get();

        $editing = $request->filled('graduation')
            ? $graduations->firstWhere('id', $request->integer('graduation'))
            : null;
        $editing ??= $graduations->first();

        $university = auth()->user()->university;

        $campusOptions = Campus::where('university_id', $university->id)
            ->active()
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn ($campus) => ['value' => $campus->id, 'label' => $campus->name])
            ->all();

        $programmeOptions = Program::whereHas('school.campus', fn ($query) => $query->where('university_id', $university->id))
            ->active()
            ->orderBy('name')
            ->get(['id', 'name', 'code'])
            ->map(fn ($program) => ['value' => $program->id, 'label' => $program->code ? "{$program->name} ({$program->code})" : $program->name])
            ->all();

        $graduationYearOptions = GraduationCeremony::orderBy('year', 'desc')
            ->get()
            ->map(fn ($ceremony) => ['value' => $ceremony->year, 'label' => "{$ceremony->year} — Ceremony No. {$ceremony->sequence_number}"])
            ->all();

        return view('students.edit', compact('student', 'campusOptions', 'programmeOptions', 'graduationYearOptions', 'graduations', 'editing'));
    }

    public function update(Request $request, Student $student, StudentRosterImportService $importService)
    {
        abort_unless(
            $this->isCrossInstitute(auth()->user()) || $student->university_id === auth()->user()->university_id,
            403
        );

        $university = auth()->user()->university;
        $graduations = $student->graduations()->orderByDesc('graduation_year')->orderByDesc('id')->get();

        // This graduate can have more than one graduation. The form always
        // says exactly which one it is submitting (defaulted to the latest
        // when there is only one, or none) - never guessed here, so a
        // correction to one graduation can never land on a different one.
        $validated = $request->validate([
            'graduation_id' => [
                $graduations->count() > 1 ? 'required' : 'nullable',
                'integer',
                Rule::in($graduations->pluck('id')->all()),
            ],
            'campus_id' => ['required', 'integer', Rule::exists('campuses', 'id')->where('university_id', $university->id)],
            'program_id' => ['required', 'integer', Rule::in(
                Program::whereHas('school.campus', fn ($query) => $query->where('university_id', $university->id))->pluck('id')->all()
            )],
            'graduation_year' => 'required|integer|exists:graduation_ceremonies,year',
            'last_name' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'gender' => 'nullable|in:M,F',
            'degree_name' => 'nullable|string|max:255',
            'degree_abbreviation' => 'nullable|string|max:50',
            'classification' => 'nullable|string|max:255',
            'gpa' => 'nullable|numeric|min:0|max:5',
            'programme_duration_years' => 'nullable|integer|min:1|max:10',
            'programme_start_academic_year' => 'nullable|string|max:20',
            'programme_end_academic_year' => 'nullable|string|max:20',
            'graduation_date' => 'nullable|date',
            'certificate_barcode' => 'nullable|string|max:255|unique:students,certificate_barcode,'.$student->id,
        ]);

        $graduation = $validated['graduation_id'] ?? null
            ? $graduations->firstWhere('id', $validated['graduation_id'])
            : $graduations->first();

        $fullName = trim("{$validated['last_name']} {$validated['first_name']} ".($validated['middle_name'] ?? ''));

        try {
            DB::transaction(function () use ($student, $graduation, $validated, $fullName, $importService) {
                if ($graduation) {
                    // Every field here belongs to THIS graduation, not to
                    // the person in general - editing an older graduation
                    // must never touch a different, newer one.
                    $graduation->fill(collect($validated)->except('graduation_id')->all());
                    $graduation->save();

                    // Recomputed fresh from whichever graduation is truly
                    // latest right now - correct whether the one just
                    // edited is that latest graduation or an earlier one.
                    $importService->refreshStudentCache($student);
                } else {
                    // No graduation record exists at all for this person
                    // (e.g. a non-graduated Student row) - nothing to
                    // route this through, so it applies directly as before.
                    $oldProgram = $student->program;
                    $oldGraduationYear = $student->graduation_year ? (int) $student->graduation_year : null;

                    $student->fill(collect($validated)->except('graduation_id')->all());
                    $student->full_name = $fullName;
                    $importService->syncDerivedFields($student, $oldProgram, $oldGraduationYear);
                    $student->save();
                }
            });
        } catch (QueryException $e) {
            if ((int) $e->getCode() === 23000) {
                throw ValidationException::withMessages([
                    'graduation_year' => 'This graduate already has another graduation recorded for that same Campus, Programme and Graduation Year - correct one of those, or edit that other graduation instead.',
                ])->redirectTo(route('students.edit', $student->id).($graduation ? '?graduation='.$graduation->id : ''));
            }

            throw $e;
        }

        return redirect()->route('students.show', $student->id)->with('success', 'Graduate record updated.');
    }
}
