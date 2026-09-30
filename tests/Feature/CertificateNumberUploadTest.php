<?php

namespace Tests\Feature;

use App\Models\Campus;
use App\Models\CertificateNumberUpload;
use App\Models\Program;
use App\Models\School;
use App\Models\Student;
use App\Models\StudentGraduation;
use App\Models\University;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Testing\TestResponse;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class CertificateNumberUploadTest extends TestCase
{
    use RefreshDatabase;

    private function authorizedUser(University $university): User
    {
        Permission::firstOrCreate(['name' => 'view_students', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'import_students', 'guard_name' => 'web']);

        $user = User::factory()->create(['university_id' => $university->id]);
        $user->givePermissionTo(['view_students', 'import_students']);

        return $user;
    }

    private function buildCsv(array $rows, string $originalName = 'certs.csv'): UploadedFile
    {
        $content = "registration number,certificate number\n";
        foreach ($rows as $row) {
            $content .= implode(',', $row)."\n";
        }

        $path = tempnam(sys_get_temp_dir(), 'certs').'.csv';
        file_put_contents($path, $content);

        return new UploadedFile($path, $originalName, 'text/csv', null, true);
    }

    private function referenceFromDownload(TestResponse $response): string
    {
        preg_match('/filename="(.+)\.xlsx"/', $response->headers->get('content-disposition'), $matches);

        return $matches[1];
    }

    public function test_a_certificate_number_already_used_by_another_student_is_flagged_not_a_crash(): void
    {
        $university = University::factory()->create(['enabled_addons' => ['academic_records']]);
        $campus = Campus::factory()->create(['university_id' => $university->id]);
        $program = Program::factory()->create(['school_id' => School::factory()->create(['campus_id' => $campus->id])->id]);
        $user = $this->authorizedUser($university);

        $existingHolder = Student::factory()->create([
            'university_id' => $university->id,
            'registration_number' => 'DGA 01',
            'certificate_barcode' => 'SJUT-2010-000001',
        ]);

        $target = Student::factory()->create([
            'university_id' => $university->id,
            'registration_number' => 'DGA 02',
            'campus_id' => $campus->id,
            'program_id' => $program->id,
        ]);
        $graduation = StudentGraduation::create([
            'student_id' => $target->id,
            'university_id' => $university->id,
            'campus_id' => $campus->id,
            'program_id' => $program->id,
            'graduation_year' => 2010,
            'last_name' => $target->last_name,
            'first_name' => $target->first_name,
            'is_graduated' => true,
        ]);

        $response = $this->actingAs($user)->post(route('students.certificates.store'), [
            'graduation_year' => 2010,
            'program_id' => $program->id,
            'campus_id' => $campus->id,
            'file' => $this->buildCsv([['DGA 02', 'SJUT-2010-000001']]),
        ]);

        $response->assertRedirect();
        $report = session('report');
        $this->assertSame(1, $report['failed']);
        $this->assertSame(0, $report['updated']);
        $this->assertStringContainsString('DGA 01', $report['flagged'][0]['reason']);

        // Neither the graduation nor its cache row were left half-written by the failed save.
        $this->assertNull($graduation->fresh()->certificate_barcode);
        $this->assertNull($target->fresh()->certificate_barcode);
        $this->assertSame('SJUT-2010-000001', $existingHolder->fresh()->certificate_barcode);
    }

    public function test_a_non_conflicting_certificate_number_updates_the_graduation_and_the_student_cache(): void
    {
        $university = University::factory()->create(['enabled_addons' => ['academic_records']]);
        $campus = Campus::factory()->create(['university_id' => $university->id]);
        $program = Program::factory()->create(['school_id' => School::factory()->create(['campus_id' => $campus->id])->id]);
        $user = $this->authorizedUser($university);

        $student = Student::factory()->create([
            'university_id' => $university->id,
            'registration_number' => 'DGA 03',
            'campus_id' => $campus->id,
            'program_id' => $program->id,
        ]);
        $graduation = StudentGraduation::create([
            'student_id' => $student->id,
            'university_id' => $university->id,
            'campus_id' => $campus->id,
            'program_id' => $program->id,
            'graduation_year' => 2010,
            'last_name' => $student->last_name,
            'first_name' => $student->first_name,
            'is_graduated' => true,
        ]);

        $response = $this->actingAs($user)->post(route('students.certificates.store'), [
            'graduation_year' => 2010,
            'program_id' => $program->id,
            'campus_id' => $campus->id,
            'file' => $this->buildCsv([['DGA 03', 'SJUT-2010-000099']]),
        ]);

        $response->assertRedirect();
        $report = session('report');
        $this->assertSame(1, $report['updated']);
        $this->assertSame(0, $report['failed']);

        $this->assertSame('SJUT-2010-000099', $graduation->fresh()->certificate_barcode);
        $this->assertSame('SJUT-2010-000099', $student->fresh()->certificate_barcode);
    }

    public function test_stats_are_scoped_to_the_selected_year_and_programme_not_the_whole_university(): void
    {
        $university = University::factory()->create(['enabled_addons' => ['academic_records']]);
        $campus = Campus::factory()->create(['university_id' => $university->id]);
        $school = School::factory()->create(['campus_id' => $campus->id]);
        $programA = Program::factory()->create(['school_id' => $school->id]);
        $programB = Program::factory()->create(['school_id' => $school->id]);
        $user = $this->authorizedUser($university);

        // Programme A, 2010: one with a certificate, one without.
        StudentGraduation::create([
            'student_id' => Student::factory()->create(['university_id' => $university->id])->id,
            'university_id' => $university->id, 'campus_id' => $campus->id, 'program_id' => $programA->id,
            'graduation_year' => 2010, 'last_name' => 'A', 'first_name' => 'A', 'is_graduated' => true,
            'certificate_barcode' => 'SJUT-2010-0001',
        ]);
        StudentGraduation::create([
            'student_id' => Student::factory()->create(['university_id' => $university->id])->id,
            'university_id' => $university->id, 'campus_id' => $campus->id, 'program_id' => $programA->id,
            'graduation_year' => 2010, 'last_name' => 'B', 'first_name' => 'B', 'is_graduated' => true,
        ]);

        // Programme B, 2011: unrelated batch, should not affect A/2010's numbers.
        StudentGraduation::create([
            'student_id' => Student::factory()->create(['university_id' => $university->id])->id,
            'university_id' => $university->id, 'campus_id' => $campus->id, 'program_id' => $programB->id,
            'graduation_year' => 2011, 'last_name' => 'C', 'first_name' => 'C', 'is_graduated' => true,
        ]);

        $scoped = $this->actingAs($user)->getJson(route('students.certificates.stats', [
            'graduation_year' => 2010,
            'program_id' => $programA->id,
        ]))->json();

        $this->assertSame(2, $scoped['total']);
        $this->assertSame(1, $scoped['with_certificate']);
        $this->assertSame(1, $scoped['pending']);
        $this->assertSame(50, $scoped['coverage']);

        $unscoped = $this->actingAs($user)->getJson(route('students.certificates.stats'))->json();
        $this->assertSame(3, $unscoped['total']);
    }

    public function test_downloading_the_template_requires_a_year_and_a_programme(): void
    {
        $university = University::factory()->create(['enabled_addons' => ['academic_records']]);
        $user = $this->authorizedUser($university);

        $this->actingAs($user)->get(route('students.certificates.template'))
            ->assertSessionHasErrors(['graduation_year', 'program_id']);

        $this->assertSame(0, CertificateNumberUpload::count());
    }

    public function test_downloading_the_template_tracks_the_batch_and_prefills_pending_graduates(): void
    {
        $university = University::factory()->create(['enabled_addons' => ['academic_records']]);
        $campus = Campus::factory()->create(['university_id' => $university->id]);
        $program = Program::factory()->create(['school_id' => School::factory()->create(['campus_id' => $campus->id])->id]);
        $user = $this->authorizedUser($university);

        $pendingStudent = Student::factory()->create(['university_id' => $university->id, 'registration_number' => 'DGA 05', 'last_name' => 'MASSAWE', 'first_name' => 'Grace']);
        StudentGraduation::create([
            'student_id' => $pendingStudent->id, 'university_id' => $university->id, 'campus_id' => $campus->id, 'program_id' => $program->id,
            'graduation_year' => 2010, 'last_name' => 'MASSAWE', 'first_name' => 'Grace', 'is_graduated' => true,
        ]);

        $doneStudent = Student::factory()->create(['university_id' => $university->id, 'registration_number' => 'DGA 06']);
        StudentGraduation::create([
            'student_id' => $doneStudent->id, 'university_id' => $university->id, 'campus_id' => $campus->id, 'program_id' => $program->id,
            'graduation_year' => 2010, 'last_name' => 'X', 'first_name' => 'Y', 'is_graduated' => true, 'certificate_barcode' => 'SJUT-2010-9999',
        ]);

        $response = $this->actingAs($user)->get(route('students.certificates.template', [
            'graduation_year' => 2010,
            'program_id' => $program->id,
            'campus_id' => $campus->id,
        ]));

        $response->assertOk();

        $this->assertSame(1, CertificateNumberUpload::count());
        $batch = CertificateNumberUpload::first();
        $this->assertStringStartsWith('CRT', $batch->reference_number);
        $this->assertSame('open', $batch->status);
        $this->assertSame($university->id, $batch->university_id);
        $this->assertSame($program->id, $batch->program_id);
        $this->assertSame(2010, $batch->graduation_year);
        $this->assertNotNull($batch->downloaded_at);
        $this->assertNull($batch->uploaded_at);
        $this->assertSame($batch->reference_number.'.xlsx', $batch->original_filename);

        $path = tempnam(sys_get_temp_dir(), 'dl').'.xlsx';
        file_put_contents($path, $response->streamedContent());
        $sheet = IOFactory::load($path)->getActiveSheet();

        $this->assertSame('Registration Number', $sheet->getCell('A1')->getValue());
        $this->assertSame('Certificate Number', $sheet->getCell('B1')->getValue());
        $this->assertSame('DGA 05', $sheet->getCell('A2')->getValue());
        $this->assertSame('', (string) $sheet->getCell('B2')->getValue());
        $this->assertStringContainsString('MASSAWE', (string) $sheet->getCell('C2')->getValue());
        // Exactly one worklist row (the pending graduate) - the already-
        // certificated one never appears, and row 3 is blank before the note.
        $this->assertSame('', (string) $sheet->getCell('A3')->getValue());
        $this->assertStringContainsString($batch->reference_number, (string) $sheet->getCell('A4')->getValue());
    }

    public function test_uploading_the_downloaded_template_reuses_the_same_tracked_batch(): void
    {
        $university = University::factory()->create(['enabled_addons' => ['academic_records']]);
        $campus = Campus::factory()->create(['university_id' => $university->id]);
        $program = Program::factory()->create(['school_id' => School::factory()->create(['campus_id' => $campus->id])->id]);
        $user = $this->authorizedUser($university);

        $student = Student::factory()->create(['university_id' => $university->id, 'registration_number' => 'DGA 07']);
        StudentGraduation::create([
            'student_id' => $student->id, 'university_id' => $university->id, 'campus_id' => $campus->id, 'program_id' => $program->id,
            'graduation_year' => 2010, 'last_name' => 'A', 'first_name' => 'B', 'is_graduated' => true,
        ]);

        $download = $this->actingAs($user)->get(route('students.certificates.template', [
            'graduation_year' => 2010,
            'program_id' => $program->id,
            'campus_id' => $campus->id,
        ]));
        $reference = $this->referenceFromDownload($download);

        $this->actingAs($user)->post(route('students.certificates.store'), [
            'graduation_year' => 2010,
            'program_id' => $program->id,
            'campus_id' => $campus->id,
            'file' => $this->buildCsv([['DGA 07', 'SJUT-2010-0001']], $reference.'.xlsx'),
        ])->assertRedirect();

        // Still one row: the download-time row was filled in, not duplicated.
        $this->assertSame(1, CertificateNumberUpload::count());
        $batch = CertificateNumberUpload::first();
        $this->assertSame($reference, $batch->reference_number);
        $this->assertNotNull($batch->downloaded_at);
        $this->assertNotNull($batch->uploaded_at);
        $this->assertSame(1, $batch->updated_count);
        $this->assertSame(['DGA 07'], $batch->updated_reg_numbers);
    }

    public function test_uploading_a_file_whose_name_matches_no_open_batch_starts_a_fresh_tracked_batch(): void
    {
        $university = University::factory()->create(['enabled_addons' => ['academic_records']]);
        $campus = Campus::factory()->create(['university_id' => $university->id]);
        $program = Program::factory()->create(['school_id' => School::factory()->create(['campus_id' => $campus->id])->id]);
        $user = $this->authorizedUser($university);

        $student = Student::factory()->create(['university_id' => $university->id, 'registration_number' => 'DGA 08']);
        StudentGraduation::create([
            'student_id' => $student->id, 'university_id' => $university->id, 'campus_id' => $campus->id, 'program_id' => $program->id,
            'graduation_year' => 2010, 'last_name' => 'A', 'first_name' => 'B', 'is_graduated' => true,
        ]);

        $this->actingAs($user)->post(route('students.certificates.store'), [
            'graduation_year' => 2010,
            'program_id' => $program->id,
            'campus_id' => $campus->id,
            'file' => $this->buildCsv([['DGA 08', 'SJUT-2010-0002']], 'my-own-file.csv'),
        ])->assertRedirect();

        $this->assertSame(1, CertificateNumberUpload::count());
        $batch = CertificateNumberUpload::first();
        $this->assertNull($batch->downloaded_at);
        $this->assertNotNull($batch->uploaded_at);
        $this->assertSame('my-own-file.csv', $batch->original_filename);
    }

    public function test_the_history_page_lists_batches_under_active_and_closed_tabs(): void
    {
        $university = University::factory()->create(['enabled_addons' => ['academic_records']]);
        $user = $this->authorizedUser($university);

        $open = CertificateNumberUpload::create([
            'reference_number' => 'CRT1', 'status' => 'open', 'university_id' => $university->id,
            'imported_by' => $user->id, 'original_filename' => 'CRT1.xlsx', 'downloaded_at' => now(),
        ]);
        $closed = CertificateNumberUpload::create([
            'reference_number' => 'CRT2', 'status' => 'closed', 'university_id' => $university->id,
            'imported_by' => $user->id, 'original_filename' => 'CRT2.xlsx', 'downloaded_at' => now(), 'closed_at' => now(), 'closed_by' => $user->id,
        ]);

        $response = $this->actingAs($user)->get(route('students.certificates.index'));

        $response->assertOk();
        $response->assertViewHas('activeBatches', fn ($batches) => $batches->total() === 1 && $batches->first()->is($open));
        $response->assertViewHas('closedBatches', fn ($batches) => $batches->total() === 1 && $batches->first()->is($closed));
    }

    public function test_a_batch_can_be_viewed_closed_and_reopened(): void
    {
        $university = University::factory()->create(['enabled_addons' => ['academic_records']]);
        $user = $this->authorizedUser($university);

        $batch = CertificateNumberUpload::create([
            'reference_number' => 'CRT3', 'status' => 'open', 'university_id' => $university->id,
            'imported_by' => $user->id, 'original_filename' => 'CRT3.xlsx', 'downloaded_at' => now(),
            'updated_count' => 4, 'flagged' => [['row' => 2, 'registration_number' => 'DGA 99', 'reason' => 'Student not found.']],
        ]);

        $this->actingAs($user)->get(route('students.certificates.show', $batch->id))
            ->assertOk()
            ->assertSee('CRT3')
            ->assertSee('Student not found.');

        $this->actingAs($user)->post(route('students.certificates.close', $batch->id))
            ->assertRedirect(route('students.certificates.index'));
        $this->assertSame('closed', $batch->fresh()->status);
        $this->assertNotNull($batch->fresh()->closed_at);
        $this->assertSame($user->id, $batch->fresh()->closed_by);

        $this->actingAs($user)->post(route('students.certificates.reopen', $batch->id))
            ->assertRedirect(route('students.certificates.show', $batch->id));
        $this->assertSame('open', $batch->fresh()->status);
        $this->assertNull($batch->fresh()->closed_at);
    }

    public function test_viewing_or_closing_another_universitys_batch_is_forbidden(): void
    {
        $university = University::factory()->create(['enabled_addons' => ['academic_records']]);
        $otherUniversity = University::factory()->create(['enabled_addons' => ['academic_records']]);
        $outsider = $this->authorizedUser($otherUniversity);

        $batch = CertificateNumberUpload::create([
            'reference_number' => 'CRT4', 'status' => 'open', 'university_id' => $university->id,
            'imported_by' => User::factory()->create(['university_id' => $university->id])->id,
            'original_filename' => 'CRT4.xlsx', 'downloaded_at' => now(),
        ]);

        $this->actingAs($outsider)->get(route('students.certificates.show', $batch->id))->assertForbidden();
        $this->actingAs($outsider)->post(route('students.certificates.close', $batch->id))->assertForbidden();
    }
}
