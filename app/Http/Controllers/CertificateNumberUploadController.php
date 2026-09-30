<?php

namespace App\Http\Controllers;

use App\Models\Campus;
use App\Models\CertificateNumberUpload;
use App\Models\GraduationCeremony;
use App\Models\Program;
use App\Models\Student;
use App\Models\StudentGraduation;
use App\Services\StudentRosterImportService;
use App\Traits\GeneratesImportReference;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class CertificateNumberUploadController extends Controller
{
    use GeneratesImportReference;

    public function index()
    {
        $university = auth()->user()->university;

        $programs = Program::whereHas('school.campus', fn ($query) => $query->where('university_id', $university->id))
            ->orderBy('name')
            ->get();

        $campuses = Campus::where('university_id', $university->id)
            ->orderBy('name')
            ->get();

        $graduationCeremonies = GraduationCeremony::orderByDesc('year')->get();

        // Whole-university totals for the first paint - narrowed live by
        // stats() below once a Year/Programme/Campus is actually picked.
        $stats = $this->computeStats($university->id, null, null, null);

        // Same Active/Closed history pattern as the Graduation Roster page.
        $perPageOptions = [10, 25, 50];
        $tab = request('tab') === 'closed' ? 'closed' : 'active';

        $historyQuery = fn () => CertificateNumberUpload::where('university_id', $university->id)
            ->with(['importedBy', 'campus', 'program'])
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

        return view('students.certificates.upload', compact('programs', 'campuses', 'graduationCeremonies', 'stats', 'activeBatches', 'closedBatches', 'tab'));
    }

    public function show(CertificateNumberUpload $certificateNumberUpload)
    {
        abort_unless($certificateNumberUpload->university_id === auth()->user()->university_id, 403);

        $certificateNumberUpload->load(['importedBy', 'closedBy', 'campus', 'program.school']);

        return view('students.certificates.show', ['batch' => $certificateNumberUpload]);
    }

    public function close(CertificateNumberUpload $certificateNumberUpload)
    {
        abort_unless($certificateNumberUpload->university_id === auth()->user()->university_id, 403);

        $certificateNumberUpload->update([
            'status' => 'closed',
            'closed_at' => now(),
            'closed_by' => auth()->id(),
        ]);

        return redirect()->route('students.certificates.index')->with('success', "Reference {$certificateNumberUpload->reference_number} closed.");
    }

    public function reopen(CertificateNumberUpload $certificateNumberUpload)
    {
        abort_unless($certificateNumberUpload->university_id === auth()->user()->university_id, 403);

        $certificateNumberUpload->update([
            'status' => 'open',
            'closed_at' => null,
            'closed_by' => null,
        ]);

        return redirect()->route('students.certificates.show', $certificateNumberUpload)->with('success', "Reference {$certificateNumberUpload->reference_number} re-opened.");
    }

    /**
     * Same counts as index(), scoped to whichever of Year/Programme/Campus
     * the form currently has selected - so "confirm your file is complete
     * before uploading" means the file actually about to be uploaded, not
     * the whole university's history across every year and programme.
     */
    public function stats(Request $request)
    {
        $university = auth()->user()->university;

        $stats = $this->computeStats(
            $university->id,
            $request->integer('graduation_year') ?: null,
            $request->integer('program_id') ?: null,
            $request->integer('campus_id') ?: null,
        );

        return response()->json([
            'total' => $stats['total'],
            'with_certificate' => $stats['with_certificate'],
            'pending' => $stats['pending'],
            'coverage' => $stats['total'] > 0 ? round(($stats['with_certificate'] / $stats['total']) * 100) : 0,
        ]);
    }

    /**
     * @return array{total: int, with_certificate: int, pending: int}
     */
    private function computeStats(int $universityId, ?int $graduationYear, ?int $programId, ?int $campusId): array
    {
        $base = fn () => StudentGraduation::where('university_id', $universityId)
            ->where('is_graduated', true)
            ->when($graduationYear, fn ($query) => $query->where('graduation_year', $graduationYear))
            ->when($programId, fn ($query) => $query->where('program_id', $programId))
            ->when($campusId, fn ($query) => $query->where('campus_id', $campusId));

        $total = $base()->count();
        $withCertificate = $base()->whereNotNull('certificate_barcode')->where('certificate_barcode', '!=', '')->count();

        return [
            'total' => $total,
            'with_certificate' => $withCertificate,
            'pending' => $total - $withCertificate,
        ];
    }

    public function store(Request $request, StudentRosterImportService $rosterImportService)
    {
        $validated = $request->validate([
            'graduation_year' => ['required', 'integer', 'min:1900', 'max:2100'],
            'program_id' => ['required', 'exists:programs,id'],
            'campus_id' => ['nullable', 'exists:campuses,id'],
            'file' => ['required', 'file', 'mimes:csv,xlsx', 'max:10240'],
        ]);

        $graduationYear = (int) $validated['graduation_year'];
        $programId = (int) $validated['program_id'];
        $campusId = $validated['campus_id'] ? (int) $validated['campus_id'] : null;
        $universityId = auth()->user()->university_id;

        $report = [
            'updated' => 0,
            'skipped' => 0,
            'not_found' => 0,
            'failed' => 0,
            'updated_reg_numbers' => [],
            'flagged' => [],
        ];

        $file = $request->file('file');
        $batch = $this->resolveCertificateBatch($file->getClientOriginalName(), $universityId, $programId, $campusId, $graduationYear);
        $reader = IOFactory::createReaderForFile($file->getRealPath());
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($file->getRealPath());
        $rows = $spreadsheet->getActiveSheet()->toArray();

        $start = 0;
        if (! empty($rows[0]) && is_string($rows[0][0] ?? null)) {
            $first = strtolower(trim((string) $rows[0][0]));
            if (str_contains($first, 'registration') || str_contains($first, 'reg')) {
                $start = 1;
            }
        }

        $importerId = auth()->id();

        for ($i = $start; $i < count($rows); $i++) {
            $row = $rows[$i];
            $registrationNumber = isset($row[0]) ? trim((string) $row[0]) : '';
            $certificateNumber = isset($row[1]) ? trim((string) $row[1]) : '';

            if ($registrationNumber === '') {
                continue;
            }

            $student = Student::where('university_id', $universityId)
                ->where('registration_number', $registrationNumber)
                ->first();

            if (! $student) {
                $report['not_found']++;
                $report['flagged'][] = [
                    'row' => $i + 1,
                    'registration_number' => $registrationNumber,
                    'reason' => 'Student not found.',
                ];

                continue;
            }

            $query = StudentGraduation::where('student_id', $student->id)
                ->where('graduation_year', $graduationYear)
                ->where('program_id', $programId);

            if ($campusId) {
                $graduation = $query->where('campus_id', $campusId)->first();
            } else {
                $matches = $query->get();
                if ($matches->count() > 1) {
                    $report['not_found']++;
                    $report['flagged'][] = [
                        'row' => $i + 1,
                        'registration_number' => $registrationNumber,
                        'reason' => 'More than one graduation matches this year and programme; please specify campus.',
                    ];

                    continue;
                }
                $graduation = $matches->first();
            }

            if (! $graduation) {
                $report['not_found']++;
                $report['flagged'][] = [
                    'row' => $i + 1,
                    'registration_number' => $registrationNumber,
                    'reason' => "No graduation found for year {$graduationYear}, selected programme and campus.",
                ];

                continue;
            }

            if ($certificateNumber === '') {
                $report['skipped']++;

                continue;
            }

            if ($graduation->certificate_barcode === $certificateNumber) {
                $report['skipped']++;

                continue;
            }

            try {
                DB::transaction(function () use ($graduation, $certificateNumber, $importerId, $student, $rosterImportService) {
                    $graduation->certificate_barcode = $certificateNumber;
                    $graduation->imported_by = $importerId;
                    $graduation->save();

                    $rosterImportService->refreshStudentCache($student);
                });
            } catch (QueryException $e) {
                if ($e->getCode() !== '23000') {
                    throw $e;
                }

                $holder = Student::where('certificate_barcode', $certificateNumber)
                    ->where('id', '!=', $student->id)
                    ->first();

                $report['failed']++;
                $report['flagged'][] = [
                    'row' => $i + 1,
                    'registration_number' => $registrationNumber,
                    'reason' => $holder
                        ? "Certificate number {$certificateNumber} is already assigned to {$holder->registration_number}."
                        : "Certificate number {$certificateNumber} could not be saved because it is already in use.",
                ];

                continue;
            }

            $report['updated']++;
            $report['updated_reg_numbers'][] = $registrationNumber;
        }

        $batch->update([
            'updated_count' => $report['updated'],
            'skipped_count' => $report['skipped'],
            'not_found_count' => $report['not_found'],
            'failed_count' => $report['failed'],
            'updated_reg_numbers' => $report['updated_reg_numbers'],
            'flagged' => $report['flagged'],
        ]);

        return back()->with('report', $report);
    }

    /**
     * Same "one reference per batch, named after that reference" scheme as
     * the Graduation Roster template (see StudentImportController::
     * downloadTemplate) - pre-filled with every graduate in this Year /
     * Programme / Campus who is still missing a certificate number, so
     * staff are filling in a real worklist instead of copying registration
     * numbers in by hand. Certificate Number stays column B (index 1, what
     * store() actually reads) even with Name added for context, so a
     * template built here parses identically to a hand-built 2-column file.
     */
    public function downloadTemplate(Request $request)
    {
        $validated = $request->validate([
            'graduation_year' => ['required', 'integer', 'min:1900', 'max:2100'],
            'program_id' => ['required', 'exists:programs,id'],
            'campus_id' => ['nullable', 'exists:campuses,id'],
        ]);

        $university = auth()->user()->university;
        $graduationYear = (int) $validated['graduation_year'];

        $program = Program::whereHas('school.campus', fn ($query) => $query->where('university_id', $university->id))
            ->with('school')
            ->findOrFail($validated['program_id']);

        $campus = $validated['campus_id']
            ? Campus::where('university_id', $university->id)->findOrFail($validated['campus_id'])
            : null;

        $pending = StudentGraduation::where('university_id', $university->id)
            ->where('is_graduated', true)
            ->where('graduation_year', $graduationYear)
            ->where('program_id', $program->id)
            ->when($campus, fn ($query) => $query->where('campus_id', $campus->id))
            ->where(fn ($query) => $query->whereNull('certificate_barcode')->orWhere('certificate_barcode', ''))
            ->with('student')
            ->orderBy('last_name')
            ->get();

        $reference = $this->generateImportReference('CRT');

        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Certificate Numbers');

        $headers = [
            'A1' => 'Registration Number',
            'B1' => 'Certificate Number',
            'C1' => 'Name (for reference only)',
        ];
        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }
        $sheet->getStyle('A1:C1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E3A5F']],
        ]);

        $row = 2;
        foreach ($pending as $graduation) {
            $sheet->setCellValue("A{$row}", $graduation->student->registration_number ?? '');
            $sheet->setCellValue("B{$row}", '');
            $sheet->setCellValue("C{$row}", $graduation->formatted_name);
            $row++;
        }

        if ($pending->isEmpty()) {
            $sheet->setCellValue('A2', '2018/0001');
            $sheet->setCellValue('B2', 'SJUT-2018-000001');
            $sheet->setCellValue('C2', 'Example Name');
            $sheet->getStyle('A2:C2')->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F5F5F5']],
                'font' => ['color' => ['rgb' => '999999']],
            ]);
            $sheet->setCellValue('A3', '⚠ No pending graduate matched this Year/Programme/Campus - DELETE THIS EXAMPLE ROW before uploading.');
            $sheet->getStyle('A3')->getFont()->setBold(true)->setSize(9)->getColor()->setARGB('FFFF0000');
            $row = 4;
        }

        $noteRow = $row + 1;
        $sheet->setCellValue("A{$noteRow}", 'Pre-selected: '.($campus->name ?? 'All / auto-detect campus')." | {$program->school->name} | {$program->name} | Graduation Year {$graduationYear} | Reference: {$reference}");
        $sheet->getStyle("A{$noteRow}")->getFont()->setItalic(true)->setSize(9);

        foreach (['A', 'B', 'C'] as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        $sheet->freezePane('A2');

        // Same convention as the roster template: the filename IS the
        // reference number, so Upload can find this exact batch again
        // (see resolveCertificateBatch) without parsing spreadsheet cells.
        $filename = $reference.'.xlsx';

        CertificateNumberUpload::create([
            'reference_number' => $reference,
            'status' => 'open',
            'university_id' => $university->id,
            'campus_id' => $campus?->id,
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

    /**
     * Mirrors StudentImportController::resolveUploadBatch() - reuses the
     * open, not-yet-uploaded row Download Template created (matched by its
     * reference-as-filename) when the Campus/Programme/Year still agree,
     * otherwise starts a fresh tracked batch.
     */
    private function resolveCertificateBatch(string $originalFilename, int $universityId, int $programId, ?int $campusId, int $graduationYear): CertificateNumberUpload
    {
        $candidateReference = pathinfo($originalFilename, PATHINFO_FILENAME);

        $existing = CertificateNumberUpload::where('reference_number', $candidateReference)
            ->where('university_id', $universityId)
            ->whereNull('uploaded_at')
            ->first();

        if ($existing
            && (int) $existing->program_id === $programId
            && (int) $existing->graduation_year === $graduationYear
            && (int) $existing->campus_id === (int) $campusId) {
            $existing->fill([
                'imported_by' => auth()->id(),
                'original_filename' => $originalFilename,
                'uploaded_at' => now(),
            ]);
            $existing->save();

            return $existing;
        }

        return CertificateNumberUpload::create([
            'reference_number' => $this->generateImportReference('CRT'),
            'status' => 'open',
            'university_id' => $universityId,
            'campus_id' => $campusId,
            'program_id' => $programId,
            'graduation_year' => $graduationYear,
            'imported_by' => auth()->id(),
            'original_filename' => $originalFilename,
            'uploaded_at' => now(),
        ]);
    }
}
