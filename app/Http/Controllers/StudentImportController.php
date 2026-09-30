<?php

namespace App\Http\Controllers;

use App\Models\Campus;
use App\Models\GraduationCeremony;
use App\Models\GraduationLetter;
use App\Models\Program;
use App\Models\School;
use App\Models\Student;
use App\Models\StudentGraduation;
use App\Models\StudentRosterImport;
use App\Services\StudentRosterImportService;
use App\Traits\GeneratesImportReference;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * Bulk-registers already-graduated students from the registry's physical
 * graduation books - deliberately the minimum facts the registry is
 * actually certain of (registration number, name, sex, graduation year),
 * not a full academic record. Campus, Programme and Graduation Year are
 * picked once for the whole batch (a graduation book is naturally one
 * campus, one programme and one graduation year), not per row - see
 * StudentRosterImportService for the derivation rules (start academic
 * year from the registration number, ceremony date from
 * `graduation_ceremonies`, legacy certificate number equals registration
 * number before 2018).
 *
 * Campus is independent of Programme/Faculty here - a Programme's own
 * `school_id` is its catalog "home", not where a given student actually
 * attended, since the same Programme is offered across every campus.
 *
 * Every download and every upload gets a reference number (the same
 * {role_code}{ymdH}{serial} pattern as staff import's ImportSequence, role
 * code "ROS") and lands in `student_roster_imports`.
 *
 * SUPERSEDED 2026-09-29: this originally created two independent rows
 * (download, then upload) deliberately, to avoid matching a later upload
 * back to its download by filename. The user has since asked for the
 * opposite - the same batch, one reference, all the way through - so
 * resolveUploadBatch() now reuses the open, not-yet-uploaded row Download
 * Template created (matched by its reference-as-filename) whenever the
 * Campus/Programme/Year still agree; a renamed file, a hand-built one, or
 * a mismatched selection still falls back to a fresh row, same as before.
 * Certificate Number uploads (CertificateNumberUploadController,
 * `certificate_number_uploads`) follow the identical pattern under role
 * code "CRT", kept as its own table since a certificate upload never
 * creates a graduate, only updates one.
 */
class StudentImportController extends Controller
{
    use GeneratesImportReference;

    public function index()
    {
        $university = auth()->user()->university;

        $campuses = Campus::where('university_id', $university->id)
            ->active()
            ->orderBy('name')
            ->get(['id', 'name']);

        $schools = School::whereHas('campus', fn ($query) => $query->where('university_id', $university->id))
            ->active()
            ->orderBy('name')
            ->get(['id', 'name', 'code']);

        // The graduates written by the most recent upload - not "recently
        // touched" records, which an Edit or a Change Programme would reshuffle.
        $latestBatch = StudentRosterImport::where('university_id', $university->id)
            ->whereNotNull('uploaded_at')
            ->whereHas('students')
            ->withCount('students')
            ->latest('id')
            ->first();

        $recentImports = $latestBatch
            ? $latestBatch->students()->with(['program', 'campus'])->latest('id')->limit(5)->get()
            : collect();

        $yearStats = Student::where('university_id', $university->id)
            ->where('is_graduated', true)
            ->whereNotNull('graduation_year')
            ->selectRaw("graduation_year, SUM(gender = 'M') AS males, SUM(gender = 'F') AS females, COUNT(*) AS total")
            ->groupBy('graduation_year')
            ->orderByDesc('graduation_year')
            ->get();

        $missingSexQuery = Student::where('university_id', $university->id)
            ->where('is_graduated', true)
            ->whereNotNull('graduation_year')
            ->where(fn ($query) => $query->whereNull('gender')->orWhereNotIn('gender', ['M', 'F']));

        $unknownSexCount = (clone $missingSexQuery)->count();
        $unknownSexGraduates = $missingSexQuery->orderBy('graduation_year')->orderBy('registration_number')->limit(15)->get();

        $perPageOptions = [10, 25, 50];
        $tab = request('tab') === 'closed' ? 'closed' : 'active';

        $historyQuery = fn () => StudentRosterImport::where('university_id', $university->id)
            ->with(['importedBy', 'campus', 'program', 'originalProgram'])
            ->withCount('students')
            ->latest('id');

        $activeBatches = $historyQuery()
            ->where('status', '!=', 'closed')
            ->paginate(in_array(request()->integer('history_per_page'), $perPageOptions) ? request()->integer('history_per_page') : 10, ['*'], 'history_page')
            ->withQueryString()
            ->appends('tab', 'active');

        $closedBatches = $historyQuery()
            ->where('status', 'closed')
            ->paginate(in_array(request()->integer('closed_per_page'), $perPageOptions) ? request()->integer('closed_per_page') : 10, ['*'], 'closed_page')
            ->withQueryString()
            ->appends('tab', 'closed');

        $reviewItems = $this->reviewItemsFor($activeBatches->getCollection()->merge($closedBatches->getCollection()), $university->id);

        $graduationCeremonies = GraduationCeremony::orderBy('year')->get();

        $programmeOptions = Program::whereHas('school.campus', fn ($query) => $query->where('university_id', $university->id))
            ->active()
            ->orderBy('name')
            ->get(['id', 'name', 'code'])
            ->map(fn ($program) => ['value' => $program->id, 'label' => $program->code ? "{$program->name} ({$program->code})" : $program->name])
            ->all();

        return view('students.import', compact('campuses', 'schools', 'yearStats', 'unknownSexCount', 'unknownSexGraduates', 'latestBatch', 'recentImports', 'activeBatches', 'closedBatches', 'tab', 'reviewItems', 'graduationCeremonies', 'programmeOptions'));
    }

    /**
     * The still-outstanding flagged rows of each listed batch, each with a
     * link to that graduate's Edit page when the registration number matches
     * a record. A flag is recorded at upload time; once the graduate has
     * since been fixed (start year filled in, sex set, or the year's
     * ceremony added) it no longer counts as needing review.
     *
     * @param  Collection<int, StudentRosterImport>  $batches
     * @return array<int, array<int, array{row: int, registration_number: string, reason: string, edit_url: string|null}>>
     */
    private function reviewItemsFor($batches, int $universityId): array
    {
        $registrationNumbers = $batches
            ->flatMap(fn ($batch) => collect($batch->flagged ?? [])->pluck('registration_number'))
            ->filter()
            ->unique()
            ->values();

        $students = $registrationNumbers->isEmpty()
            ? collect()
            : Student::where('university_id', $universityId)
                ->whereIn('registration_number', $registrationNumbers)
                ->get(['id', 'registration_number', 'gender', 'programme_start_academic_year'])
                ->keyBy('registration_number');

        $ceremonyYears = GraduationCeremony::pluck('year')->all();

        return $batches->mapWithKeys(function ($batch) use ($students, $ceremonyYears) {
            $items = collect($batch->flagged ?? [])
                ->reject(function ($item) use ($students, $ceremonyYears, $batch) {
                    $reason = $item['reason'] ?? '';
                    $student = $students[$item['registration_number'] ?? ''] ?? null;

                    return match (true) {
                        str_starts_with($reason, 'Could not derive the start') => $student && $student->programme_start_academic_year,
                        str_starts_with($reason, 'Sex value') => $student && $student->gender,
                        str_starts_with($reason, 'No Graduation Ceremony') => in_array($batch->graduation_year, $ceremonyYears),
                        default => false,
                    };
                })
                ->map(fn ($item) => [
                    'row' => $item['row'] ?? 0,
                    'registration_number' => $item['registration_number'] ?? '',
                    'reason' => $item['reason'] ?? '',
                    'edit_url' => isset($students[$item['registration_number'] ?? '']) ? route('students.edit', $students[$item['registration_number']]->id) : null,
                ])
                ->values()
                ->all();

            return [$batch->id => $items];
        })->all();
    }

    /**
     * AJAX: programmes offered by a given school, for the cascading
     * Faculty -> Programme selector (mirrors the staff-import pattern in
     * Admin\ImportController::getDepartmentsBySchool).
     */
    public function trace(Request $request)
    {
        $university = auth()->user()->university;
        $reg = $request->input('reg');
        $student = null;

        if ($reg) {
            $student = Student::with([
                'program',
                'campus',
                'rosterImport.importedBy',
                'graduations' => fn ($q) => $q->orderByDesc('graduation_year')->orderByDesc('id'),
                'graduations.program',
                'graduations.rosterImport',
            ])
                ->where('university_id', $university->id)
                ->where('registration_number', $reg)
                ->first();
        }

        return view('students.import.trace', [
            'student' => $student,
            'reg' => $reg,
        ]);
    }

    public function programmesBySchool(School $school)
    {
        $university = auth()->user()->university;

        abort_unless($school->campus->university_id === $university->id, 403);

        $programmes = Program::where('school_id', $school->id)
            ->active()
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json($programmes);
    }

    public function downloadTemplate(Request $request)
    {
        $university = auth()->user()->university;
        $campus = $this->resolveCampus($request, $university);
        $program = $this->resolveProgram($request, $university);
        $graduationYear = $request->integer('graduation_year');
        $reference = $this->generateRosterReference();

        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Graduation Roster');

        $headers = [
            'A1' => 'Registration Number',
            'B1' => 'Surname',
            'C1' => 'First Name',
            'D1' => 'Other Names',
            'E1' => 'Sex (M/F)',
            'F1' => 'Certificate Number (Optional)',
        ];

        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }

        $sheet->getStyle('A1:F1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E3A5F']],
        ]);

        $sheet->setCellValue('A2', '2022/0001');
        $sheet->setCellValue('B2', 'MALECELA');
        $sheet->setCellValue('C2', 'Nyagalu');
        $sheet->setCellValue('D2', 'Michael');
        $sheet->setCellValue('E2', 'M');
        $sheet->setCellValue('F2', '');

        $sheet->getStyle('A2:F2')->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F5F5F5']],
            'font' => ['color' => ['rgb' => '999999']],
        ]);

        $sheet->setCellValue('A4', "Pre-selected: {$campus->name} | {$program->school->name} | {$program->name} | Graduation Year {$graduationYear} | Reference: {$reference}");
        $sheet->getStyle('A4')->getFont()->setItalic(true)->setSize(9);

        $sheet->setCellValue('A5', '⚠ DELETE THIS EXAMPLE ROW (ROW 2) BEFORE UPLOADING');
        $sheet->getStyle('A5')->getFont()->setBold(true)->setSize(9);
        $sheet->getStyle('A5')->getFont()->getColor()->setARGB('FFFF0000');

        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        $sheet->freezePane('A2');

        // The filename is just the reference number itself, same convention
        // as staff import's own templates - Campus/Programme/Year live on
        // the reference record (student_roster_imports), not the filename,
        // so two templates for the same Programme/Year but different
        // Campus never collide on disk despite sharing no descriptive text
        // in the name.
        $filename = $reference.'.xlsx';

        StudentRosterImport::create([
            'reference_number' => $reference,
            'status' => 'open',
            'university_id' => $university->id,
            'campus_id' => $campus->id,
            'program_id' => $program->id,
            'graduation_year' => $graduationYear,
            'imported_by' => auth()->id(),
            'original_filename' => $filename,
            'downloaded_at' => now(),
        ]);

        $writer = new Xlsx($spreadsheet);

        return response()->stream(function () use ($writer) {
            $writer->save('php://output');
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    public function upload(Request $request, StudentRosterImportService $importService)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,csv|max:10240',
        ]);

        $university = auth()->user()->university;
        $campus = $this->resolveCampus($request, $university);
        $program = $this->resolveProgram($request, $university);
        $graduationYear = $request->integer('graduation_year');
        $file = $request->file('file');
        $originalFilename = $file->getClientOriginalName();

        $batch = $this->resolveUploadBatch($originalFilename, $university->id, $campus, $program, $graduationYear);

        $report = $importService->import($file, $campus, $program, $graduationYear, $university, auth()->user(), $batch->id);

        $batch->update([
            'created_count' => $report['created'],
            'updated_count' => $report['updated'],
            'created_reg_numbers' => $report['created_reg_numbers'] ?? [],
            'updated_reg_numbers' => $report['updated_reg_numbers'] ?? [],
            'flagged' => isset($report['error']) ? [['row' => 0, 'registration_number' => '', 'reason' => $report['error']]] : $report['flagged'],
        ]);

        if (isset($report['error'])) {
            return redirect()->route('students.import.index')
                ->with('error', $report['error']);
        }

        return redirect()->route('students.import.index')
            ->with('success', "Roster uploaded: {$report['created']} new, {$report['updated']} updated.")
            ->with('importReport', $report);
    }

    /**
     * Step 1 of the browser upload: validate the file and park its rows.
     * Returns JSON (not a redirect) - an XHR that follows a redirect would
     * load the destination page itself and use up the session flash message
     * meant for the reload that follows.
     */
    public function stage(Request $request, StudentRosterImportService $importService)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,csv|max:10240',
        ]);

        $university = auth()->user()->university;
        $campus = $this->resolveCampus($request, $university);
        $program = $this->resolveProgram($request, $university);
        $graduationYear = $request->integer('graduation_year');

        $batch = $this->resolveUploadBatch($request->file('file')->getClientOriginalName(), $university->id, $campus, $program, $graduationYear);

        $staged = $importService->stageRoster($request->file('file'), $batch);

        if (isset($staged['error'])) {
            $batch->update([
                'flagged' => [['row' => 0, 'registration_number' => '', 'reason' => $staged['error']]],
                'total_rows' => 0,
            ]);
            session()->flash('error', $staged['error']);

            return response()->json(['error' => $staged['error']], 422);
        }

        return response()->json([
            'total' => $staged['total'],
            'process_url' => route('students.import.process', $batch->id),
        ]);
    }

    /**
     * Step 2: imports the next chunk of staged rows; the page calls this in
     * a loop and draws the progress bar from the returned counts.
     */
    public function processChunk(StudentRosterImport $studentRosterImport, StudentRosterImportService $importService)
    {
        abort_unless($studentRosterImport->university_id === auth()->user()->university_id, 403);

        $result = $importService->processStagedChunk($studentRosterImport, auth()->user());

        if ($result['done']) {
            session()->flash('success', "Roster uploaded: {$result['report']['created']} new, {$result['report']['updated']} updated.");
            session()->flash('importReport', $result['report']);
        }

        return response()->json([
            'processed' => $result['processed'],
            'total' => $result['total'],
            'done' => $result['done'],
        ]);
    }

    public function close(StudentRosterImport $studentRosterImport)
    {
        abort_unless($studentRosterImport->university_id === auth()->user()->university_id, 403);

        $studentRosterImport->update([
            'status' => 'closed',
            'closed_at' => now(),
            'closed_by' => auth()->id(),
        ]);

        return redirect()->route('students.import.index')->with('success', "Reference {$studentRosterImport->reference_number} closed.");
    }

    public function reopen(StudentRosterImport $studentRosterImport)
    {
        abort_unless($studentRosterImport->university_id === auth()->user()->university_id, 403);

        $studentRosterImport->update([
            'status' => 'open',
            'closed_at' => null,
            'closed_by' => null,
        ]);

        return redirect()->route('students.import.show', $studentRosterImport)->with('success', "Reference {$studentRosterImport->reference_number} re-opened.");
    }

    /**
     * How many issued alumni/verification letters belong to graduates this
     * batch touched - shown before Reset so a closed, letters-already-issued
     * batch isn't deleted without the person seeing the real consequence.
     */
    private function lettersAffectedByBatch(StudentRosterImport $studentRosterImport): int
    {
        $regNumbers = array_values(array_unique(array_merge(
            $studentRosterImport->created_reg_numbers ?? [],
            $studentRosterImport->updated_reg_numbers ?? []
        )));

        if (empty($regNumbers)) {
            return 0;
        }

        $studentIds = Student::where('university_id', $studentRosterImport->university_id)
            ->whereIn('registration_number', $regNumbers)
            ->pluck('id');

        return GraduationLetter::whereIn('student_id', $studentIds)->count();
    }

    public function reset(StudentRosterImport $studentRosterImport, StudentRosterImportService $importService)
    {
        $university = auth()->user()->university;

        abort_unless($studentRosterImport->university_id === $university->id, 403);
        abort_if($studentRosterImport->downloaded_at && ! $studentRosterImport->uploaded_at, 403, 'Only uploaded batches can be reset.');

        // Same rule as Change Programme / Change Year: a Closed reference is
        // "finished, no longer editable" - Reset is the most destructive
        // action of all, so it follows that rule at least as strictly.
        if ($studentRosterImport->status !== 'open') {
            return redirect()->route('students.import.index')
                ->with('error', 'Only an open reference can be reset - re-open it first if you really need to undo it.');
        }

        DB::transaction(function () use ($studentRosterImport, $importService) {
            // 1. Delete every graduation created or updated by this batch.
            StudentGraduation::where('roster_import_id', $studentRosterImport->id)->delete();

            // 2. Every registration number this batch touched, whether the
            //    row counted as "created" or "updated": a "created" row can
            //    still belong to a person who already existed with OTHER
            //    graduations (this batch simply added a new one for them),
            //    so it is never safe to treat that split as "delete vs
            //    refresh" - both groups need the same check.
            $regNumbers = array_values(array_unique(array_merge(
                $studentRosterImport->created_reg_numbers ?? [],
                $studentRosterImport->updated_reg_numbers ?? []
            )));

            // 3. For each: if any graduation remains (this batch's own was
            //    just removed above), refresh the cache from it - never
            //    left stale. If none remains, this person existed only
            //    because of this batch, so they are removed entirely.
            Student::where('university_id', $studentRosterImport->university_id)
                ->whereIn('registration_number', $regNumbers)
                ->chunkById(100, function ($students) use ($importService) {
                    foreach ($students as $student) {
                        if ($student->graduations()->exists()) {
                            $importService->refreshStudentCache($student);
                        } else {
                            $student->delete();
                        }
                    }
                });

            $studentRosterImport->delete();
        });

        return redirect()->route('students.import.index')->with('success', "Reference {$studentRosterImport->reference_number} has been reset - you can now upload a corrected file.");
    }

    public function show(StudentRosterImport $studentRosterImport)
    {
        $university = auth()->user()->university;

        abort_unless($studentRosterImport->university_id === $university->id, 403);

        $isDownload = $studentRosterImport->downloaded_at && ! $studentRosterImport->uploaded_at;

        $graduationYears = GraduationCeremony::orderByDesc('year')
            ->pluck('year')
            ->unique()
            ->map(fn ($year) => ['value' => $year, 'label' => (string) $year])
            ->values()
            ->toArray();

        $programmeOptions = Program::whereHas('school.campus', fn ($query) => $query->where('university_id', $university->id))
            ->active()
            ->orderBy('name')
            ->get()
            ->map(fn ($program) => [
                'value' => $program->id,
                'label' => $program->degree_abbreviation ?? $program->code ?? $program->name,
            ])
            ->toArray();

        $reviewItems = $this->reviewItemsFor(collect([$studentRosterImport]), $university->id);

        $updateCount = StudentGraduation::where('roster_import_id', $studentRosterImport->id)
            ->whereNotNull('import_diff')
            ->count();

        return view('students.import.show', [
            'batch' => $studentRosterImport,
            'isDownload' => $isDownload,
            'graduationYears' => $graduationYears,
            'programmeOptions' => $programmeOptions,
            'reviewItems' => $reviewItems[$studentRosterImport->id] ?? [],
            'updateCount' => $updateCount,
            'lettersAffected' => $isDownload ? 0 : $this->lettersAffectedByBatch($studentRosterImport),
        ]);
    }

    public function reassignProgramme(Request $request, StudentRosterImport $studentRosterImport, StudentRosterImportService $importService)
    {
        $university = auth()->user()->university;

        abort_unless($studentRosterImport->university_id === $university->id, 403);

        if ($studentRosterImport->status !== 'open' || ! $studentRosterImport->uploaded_at) {
            return redirect()->route('students.import.index')
                ->with('error', 'Only an open, uploaded reference can have its Programme changed.');
        }

        $validated = $request->validate([
            'program_id' => 'required|integer',
        ]);

        $program = Program::whereHas('school.campus', fn ($query) => $query->where('university_id', $university->id))
            ->find($validated['program_id']);

        abort_unless($program, 422, 'Selected Programme was not found for this institution.');

        if ($program->id === $studentRosterImport->program_id) {
            return redirect()->route('students.import.index')
                ->with('error', 'That is already the Programme of this reference.');
        }

        $moved = $importService->reassignProgramme($studentRosterImport, $program);

        return redirect()->route('students.import.index')
            ->with('success', "{$moved} graduate(s) of {$studentRosterImport->reference_number} moved to {$program->name}.");
    }

    public function reassignYear(Request $request, StudentRosterImport $studentRosterImport, StudentRosterImportService $importService)
    {
        $university = auth()->user()->university;

        abort_unless($studentRosterImport->university_id === $university->id, 403);

        if ($studentRosterImport->status !== 'open' || ! $studentRosterImport->uploaded_at) {
            return redirect()->route('students.import.index')
                ->with('error', 'Only an open, uploaded reference can have its Graduation Year changed.');
        }

        $validated = $request->validate([
            'graduation_year' => 'required|integer|min:1900|max:2100',
        ]);

        $newYear = (int) $validated['graduation_year'];

        if ($newYear === $studentRosterImport->graduation_year) {
            return redirect()->route('students.import.index')
                ->with('error', 'That is already the Graduation Year of this reference.');
        }

        $moved = $importService->reassignYear($studentRosterImport, $newYear);

        return redirect()->route('students.import.index')
            ->with('success', "{$moved} graduate(s) of {$studentRosterImport->reference_number} moved to {$newYear}.");
    }

    public function batchStudents(Request $request, StudentRosterImport $studentRosterImport)
    {
        $university = auth()->user()->university;

        abort_unless($studentRosterImport->university_id === $university->id, 403);

        $filter = $request->input('filter');

        $query = Student::with(['program', 'campus'])
            ->orderBy('registration_number');

        if ($filter === 'updated') {
            $query->where('university_id', $university->id)
                ->whereIn('registration_number', $studentRosterImport->updated_reg_numbers ?? []);
        } elseif ($filter === 'created') {
            $query->where('university_id', $university->id)
                ->whereIn('registration_number', $studentRosterImport->created_reg_numbers ?? []);
        } else {
            $query->where('roster_import_id', $studentRosterImport->id);
        }

        $perPage = $request->integer('per_page', 25);
        $students = $query->paginate($perPage)->withQueryString();

        return view('students.import-students', [
            'batch' => $studentRosterImport,
            'students' => $students,
            'filter' => $filter,
        ]);
    }

    private function resolveCampus(Request $request, $university): Campus
    {
        $request->validate([
            'campus_id' => 'required|integer',
        ]);

        $campus = Campus::where('university_id', $university->id)->find($request->integer('campus_id'));

        abort_unless($campus, 422, 'Selected Campus was not found for this institution.');

        return $campus;
    }

    private function resolveProgram(Request $request, $university): Program
    {
        $request->validate([
            'program_id' => 'required|integer',
            'graduation_year' => 'required|integer|min:1900|max:2100',
        ]);

        $program = Program::whereHas(
            'school.campus',
            fn ($query) => $query->where('university_id', $university->id)
        )
            ->with('school')
            ->find($request->integer('program_id'));

        abort_unless($program, 422, 'Selected Programme was not found for this institution.');

        return $program;
    }

    /**
     * Show every graduate whose latest import was an update rather than a create,
     * including the old vs new values stored in `import_diff`.
     */
    public function recentUpdates(Request $request)
    {
        $university = auth()->user()->university;

        $base = fn () => StudentGraduation::query()
            ->where('student_graduations.university_id', $university->id)
            ->whereNotNull('student_graduations.import_diff');

        $status = in_array($request->query('status'), ['pending', 'accepted', 'rejected', 'all'], true)
            ? $request->query('status')
            : 'pending';

        $statusCounts = [
            'pending' => (clone $base())->where(fn ($q) => $q->whereNull('import_review_status')->orWhere('import_review_status', 'pending'))->count(),
            'accepted' => (clone $base())->where('import_review_status', 'accepted')->count(),
            'rejected' => (clone $base())->where('import_review_status', 'rejected')->count(),
        ];

        $query = $base()->with(['student', 'program', 'campus', 'rosterImport', 'importedBy', 'importReviewedBy']);

        if ($status === 'pending') {
            $query->where(fn ($q) => $q->whereNull('import_review_status')->orWhere('import_review_status', 'pending'));
        } elseif ($status !== 'all') {
            $query->where('import_review_status', $status);
        }

        if ($q = $request->query('q')) {
            $like = '%'.str_replace(' ', '%', $q).'%';
            $query->where(function ($sub) use ($like) {
                $sub->whereHas('student', fn ($s) => $s->where('registration_number', 'like', $like)->orWhere('full_name', 'like', $like))
                    ->orWhereHas('rosterImport', fn ($r) => $r->where('reference_number', 'like', $like));
            });
        }

        $updates = $query
            ->orderByDesc('student_graduations.updated_at')
            ->paginate(50)
            ->withQueryString();

        return view('students.import.updates', [
            'updates' => $updates,
            'q' => $request->query('q'),
            'status' => $status,
            'statusCounts' => $statusCounts,
        ]);
    }

    /**
     * Show the full details of a single import update, including the file/batch
     * it came from, its status and all old vs new values.
     */
    public function showUpdate(StudentGraduation $studentGraduation)
    {
        abort_unless($studentGraduation->university_id === auth()->user()->university_id, 403);
        abort_if($studentGraduation->import_diff === null, 404, 'No import diff recorded for this graduation.');

        $studentGraduation->load(['student', 'program', 'campus', 'rosterImport', 'importedBy']);

        $updateCount = 0;
        if ($studentGraduation->roster_import_id) {
            $updateCount = StudentGraduation::where('roster_import_id', $studentGraduation->roster_import_id)
                ->whereNotNull('import_diff')
                ->count();
        }

        return view('students.import.update-show', compact('studentGraduation', 'updateCount'));
    }

    /**
     * Confirms this update is correct as imported - a record-keeping
     * decision only, no data changes.
     */
    public function acceptUpdate(StudentGraduation $studentGraduation)
    {
        abort_unless($studentGraduation->university_id === auth()->user()->university_id, 403);
        abort_if($studentGraduation->import_diff === null, 404, 'No import diff recorded for this graduation.');

        $studentGraduation->update([
            'import_review_status' => 'accepted',
            'import_reviewed_by' => auth()->id(),
            'import_reviewed_at' => now(),
        ]);

        return back()->with('success', 'Update accepted.');
    }

    /**
     * Restores every field this update actually changed back to what it
     * was before, using the raw per-column snapshot taken at import time
     * (`import_diff_raw`) rather than the grouped display diff - so a name
     * change and a name change alone gets undone, never a whole record.
     * Updates made before this review feature existed have no such
     * snapshot; those are marked rejected but left as-is, since there is
     * nothing safe to replay automatically.
     */
    public function rejectUpdate(StudentGraduation $studentGraduation, StudentRosterImportService $importService)
    {
        abort_unless($studentGraduation->university_id === auth()->user()->university_id, 403);
        abort_if($studentGraduation->import_diff === null, 404, 'No import diff recorded for this graduation.');

        $revertDiff = $studentGraduation->import_diff_raw ?? [];

        if (empty($revertDiff)) {
            $studentGraduation->update([
                'import_review_status' => 'rejected',
                'import_reviewed_by' => auth()->id(),
                'import_reviewed_at' => now(),
            ]);

            return back()->with('error', 'Marked as rejected, but this update predates automatic revert - please correct the record manually via Edit.');
        }

        try {
            DB::transaction(function () use ($studentGraduation, $revertDiff, $importService) {
                foreach ($revertDiff as $field => $change) {
                    $studentGraduation->{$field} = $change['old'] ?? null;
                }

                $studentGraduation->import_review_status = 'rejected';
                $studentGraduation->import_reviewed_by = auth()->id();
                $studentGraduation->import_reviewed_at = now();
                $studentGraduation->save();

                $importService->refreshStudentCache($studentGraduation->student);
            });
        } catch (QueryException $e) {
            if ($e->getCode() !== '23000') {
                throw $e;
            }

            return back()->with('error', 'Could not revert this update - the previous value now conflicts with another record.');
        }

        return back()->with('success', 'Update rejected - the previous values have been restored.');
    }

    /**
     * "ROS" + yymmddHH + a 4-digit serial - see GeneratesImportReference.
     */
    private function generateRosterReference(): string
    {
        return $this->generateImportReference('ROS');
    }

    /**
     * Download Template creates an open, not-yet-uploaded batch row and
     * names the file after its own reference number. If the file coming
     * back through Upload/browser-staging still has that name and its
     * Campus/Programme/Year still match, this is that same batch reaching
     * its next lifecycle step - fill the existing row in rather than
     * minting a second reference for it. Anything else (a renamed file, a
     * hand-built one, or one whose selection no longer matches) gets a
     * fresh row, exactly as before.
     */
    private function resolveUploadBatch(string $originalFilename, int $universityId, Campus $campus, Program $program, int $graduationYear): StudentRosterImport
    {
        $candidateReference = pathinfo($originalFilename, PATHINFO_FILENAME);

        $existing = StudentRosterImport::where('reference_number', $candidateReference)
            ->where('university_id', $universityId)
            ->whereNull('uploaded_at')
            ->first();

        if ($existing
            && (int) $existing->campus_id === $campus->id
            && (int) $existing->program_id === $program->id
            && (int) $existing->graduation_year === $graduationYear) {
            $existing->fill([
                'imported_by' => auth()->id(),
                'original_filename' => $originalFilename,
                'uploaded_at' => now(),
            ]);
            $existing->save();

            return $existing;
        }

        return StudentRosterImport::create([
            'reference_number' => $this->generateRosterReference(),
            'status' => 'open',
            'university_id' => $universityId,
            'campus_id' => $campus->id,
            'program_id' => $program->id,
            'graduation_year' => $graduationYear,
            'imported_by' => auth()->id(),
            'original_filename' => $originalFilename,
            'created_count' => 0,
            'updated_count' => 0,
            'uploaded_at' => now(),
        ]);
    }
}
