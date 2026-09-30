<?php

namespace Tests\Feature;

use App\Models\Campus;
use App\Models\GraduationCeremony;
use App\Models\GraduationLetter;
use App\Models\Program;
use App\Models\School;
use App\Models\Student;
use App\Models\StudentGraduation;
use App\Models\StudentRosterImport;
use App\Models\University;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class StudentRosterImportTest extends TestCase
{
    use RefreshDatabase;

    private function buildRosterFile(array $dataRows): UploadedFile
    {
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray(array_merge([
            ['Registration Number', 'Surname', 'First Name', 'Other Names', 'Sex (M/F)', 'Certificate Number (Optional)'],
        ], $dataRows));

        $path = tempnam(sys_get_temp_dir(), 'roster').'.xlsx';
        (new Xlsx($spreadsheet))->save($path);

        return new UploadedFile($path, 'roster.xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', null, true);
    }

    private function authorizedUser(University $university): User
    {
        Permission::firstOrCreate(['name' => 'view_students', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'import_students', 'guard_name' => 'web']);

        $user = User::factory()->create(['university_id' => $university->id]);
        $user->givePermissionTo(['view_students', 'import_students']);

        return $user;
    }

    public function test_start_year_is_estimated_from_programme_duration_when_reg_number_has_no_year(): void
    {
        $university = University::factory()->create(['enabled_addons' => ['academic_records']]);
        $campus = Campus::factory()->create(['university_id' => $university->id]);
        $school = School::factory()->create(['campus_id' => $campus->id]);
        $program = Program::factory()->create(['school_id' => $school->id, 'duration_years' => 3]);
        $user = $this->authorizedUser($university);

        $file = $this->buildRosterFile([
            ['DGA 01', 'MADINDA', 'Emmanuel', 'Godfrey', 'M', ''],
            ['2012/0001', 'WITHYEAR', 'Anna', '', 'F', ''],
        ]);

        $this->actingAs($user)->post(route('students.import.upload'), [
            'campus_id' => $campus->id,
            'program_id' => $program->id,
            'graduation_year' => 2015,
            'file' => $file,
        ]);

        $noYear = Student::where('registration_number', 'DGA 01')->firstOrFail();
        $this->assertSame('2012/2013', $noYear->programme_start_academic_year);
        $this->assertSame('2014/2015', $noYear->programme_end_academic_year);
        $this->assertSame(3, $noYear->programme_duration_years);

        $withYear = Student::where('registration_number', '2012/0001')->firstOrFail();
        $this->assertSame('2012/2013', $withYear->programme_start_academic_year);
        $this->assertSame(3, $withYear->programme_duration_years);
    }

    private function uploadedBatch(University $university, Campus $campus, Program $program, User $user, array $rows): StudentRosterImport
    {
        $this->actingAs($user)->post(route('students.import.upload'), [
            'campus_id' => $campus->id,
            'program_id' => $program->id,
            'graduation_year' => 2015,
            'file' => $this->buildRosterFile($rows),
        ])->assertRedirect(route('students.import.index'));

        return StudentRosterImport::where('university_id', $university->id)->latest('id')->firstOrFail();
    }

    public function test_programme_of_a_whole_batch_can_be_corrected_without_reupload(): void
    {
        $university = University::factory()->create(['enabled_addons' => ['academic_records']]);
        $campus = Campus::factory()->create(['university_id' => $university->id]);
        $school = School::factory()->create(['campus_id' => $campus->id]);
        $wrongProgram = Program::factory()->create(['school_id' => $school->id, 'duration_years' => 3]);
        $rightProgram = Program::factory()->create(['school_id' => $school->id, 'duration_years' => 4]);
        $user = $this->authorizedUser($university);

        $batch = $this->uploadedBatch($university, $campus, $wrongProgram, $user, [
            ['DGA 01', 'ONE', 'Anna', '', 'F', ''],
            ['DGA 02', 'TWO', 'Ben', '', 'M', ''],
            ['2012/0003', 'THREE', 'Cara', '', 'F', ''],
        ]);

        // A person has since corrected this one start year by hand.
        Student::where('registration_number', 'DGA 02')->update(['programme_start_academic_year' => '2010/2011']);
        $otherProgramStudent = Student::factory()->create(['university_id' => $university->id, 'program_id' => $wrongProgram->id]);

        $this->actingAs($user)
            ->post(route('students.import.reassign-programme', $batch->id), ['program_id' => $rightProgram->id])
            ->assertRedirect(route('students.import.index'));

        $moved = Student::where('roster_import_id', $batch->id)->get();
        $this->assertCount(3, $moved);
        $this->assertTrue($moved->every(fn ($student) => $student->program_id === $rightProgram->id));
        $this->assertTrue($moved->every(fn ($student) => (int) $student->programme_duration_years === 4));

        $this->assertSame('2011/2012', Student::where('registration_number', 'DGA 01')->value('programme_start_academic_year'));
        $this->assertSame('2010/2011', Student::where('registration_number', 'DGA 02')->value('programme_start_academic_year'));
        $this->assertSame('2012/2013', Student::where('registration_number', '2012/0003')->value('programme_start_academic_year'));

        $this->assertSame($wrongProgram->id, $otherProgramStudent->fresh()->program_id);

        $batch->refresh();
        $this->assertSame($rightProgram->id, $batch->program_id);
        $this->assertSame($wrongProgram->id, $batch->original_program_id);
    }

    public function test_closed_batches_and_foreign_programmes_cannot_be_reassigned(): void
    {
        $university = University::factory()->create(['enabled_addons' => ['academic_records']]);
        $campus = Campus::factory()->create(['university_id' => $university->id]);
        $school = School::factory()->create(['campus_id' => $campus->id]);
        $program = Program::factory()->create(['school_id' => $school->id]);
        $otherProgram = Program::factory()->create(['school_id' => $school->id]);
        $user = $this->authorizedUser($university);

        $foreignCampus = Campus::factory()->create(['university_id' => University::factory()->create()->id]);
        $foreignProgram = Program::factory()->create(['school_id' => School::factory()->create(['campus_id' => $foreignCampus->id])->id]);

        $batch = $this->uploadedBatch($university, $campus, $program, $user, [['DGA 01', 'ONE', 'Anna', '', 'F', '']]);

        $this->actingAs($user)
            ->post(route('students.import.reassign-programme', $batch->id), ['program_id' => $foreignProgram->id])
            ->assertStatus(422);

        $batch->update(['status' => 'closed']);
        $this->actingAs($user)
            ->post(route('students.import.reassign-programme', $batch->id), ['program_id' => $otherProgram->id])
            ->assertSessionHas('error');

        $this->assertSame($program->id, Student::where('roster_import_id', $batch->id)->value('program_id'));
    }

    public function test_chunked_import_reports_real_progress_and_flashes_the_result_for_the_reload(): void
    {
        $university = University::factory()->create(['enabled_addons' => ['academic_records']]);
        $campus = Campus::factory()->create(['university_id' => $university->id]);
        $school = School::factory()->create(['campus_id' => $campus->id]);
        $program = Program::factory()->create(['school_id' => $school->id, 'duration_years' => 3]);
        $user = $this->authorizedUser($university);

        $rows = [];
        for ($i = 1; $i <= 25; $i++) {
            $rows[] = [sprintf('2012/%04d', $i), "SURNAME{$i}", "First{$i}", '', 'M', ''];
        }

        $stage = $this->actingAs($user)->postJson(route('students.import.stage'), [
            'campus_id' => $campus->id,
            'program_id' => $program->id,
            'graduation_year' => 2015,
            'file' => $this->buildRosterFile($rows),
        ]);

        $stage->assertOk()->assertJsonPath('total', 25);
        $this->assertSame(0, Student::count());

        $processUrl = $stage->json('process_url');
        $seen = [];

        do {
            $response = $this->actingAs($user)->postJson($processUrl)->assertOk();
            $seen[] = $response->json('processed');
        } while (! $response->json('done'));

        $this->assertSame([10, 20, 25], $seen);
        $this->assertSame(25, Student::count());
        $response->assertSessionHas('success', 'Roster uploaded: 25 new, 0 updated.');
        $response->assertSessionHas('importReport');

        $batch = StudentRosterImport::firstOrFail();
        $this->assertSame(25, $batch->total_rows);
        $this->assertSame(25, $batch->processed_rows);
        $this->assertSame(25, $batch->created_count);
        $this->assertFalse($batch->is_incomplete);
        $this->assertSame(25, Student::where('roster_import_id', $batch->id)->count());
    }

    public function test_chunked_import_rejects_a_wrong_template_before_staging_and_flashes_the_error(): void
    {
        $university = University::factory()->create(['enabled_addons' => ['academic_records']]);
        $campus = Campus::factory()->create(['university_id' => $university->id]);
        $program = Program::factory()->create(['school_id' => School::factory()->create(['campus_id' => $campus->id])->id]);
        $user = $this->authorizedUser($university);

        $spreadsheet = new Spreadsheet;
        $spreadsheet->getActiveSheet()->fromArray([['Not', 'The', 'Template'], ['a', 'b', 'c']]);
        $path = tempnam(sys_get_temp_dir(), 'bad').'.xlsx';
        (new Xlsx($spreadsheet))->save($path);
        $file = new UploadedFile($path, 'bad.xlsx', null, null, true);

        $response = $this->actingAs($user)->postJson(route('students.import.stage'), [
            'campus_id' => $campus->id,
            'program_id' => $program->id,
            'graduation_year' => 2015,
            'file' => $file,
        ]);

        $response->assertStatus(422)->assertJsonStructure(['error']);
        $response->assertSessionHas('error');
        $this->assertSame(0, Student::count());
    }

    public function test_processing_a_batch_from_another_university_is_forbidden(): void
    {
        $university = University::factory()->create(['enabled_addons' => ['academic_records']]);
        $otherUniversity = University::factory()->create();
        $foreignBatch = StudentRosterImport::create([
            'reference_number' => 'ROSFOREIGN0001',
            'status' => 'open',
            'university_id' => $otherUniversity->id,
            'uploaded_at' => now(),
            'original_filename' => 'foreign.xlsx',
            'imported_by' => User::factory()->create(['university_id' => $otherUniversity->id])->id,
        ]);
        $user = $this->authorizedUser($university);

        $this->actingAs($user)->postJson(route('students.import.process', $foreignBatch->id))->assertForbidden();
    }

    private function makeBatch(University $university, User $user, string $status = 'open', array $overrides = []): StudentRosterImport
    {
        static $serial = 0;
        $serial++;

        return StudentRosterImport::create(array_merge([
            'reference_number' => sprintf('ROSTEST%04d', $serial),
            'status' => $status,
            'university_id' => $university->id,
            'imported_by' => $user->id,
            'original_filename' => 'x.xlsx',
            'created_count' => 0,
            'updated_count' => 0,
            'uploaded_at' => now(),
        ], $overrides));
    }

    public function test_import_history_is_paginated_and_closed_references_have_their_own_list(): void
    {
        $university = University::factory()->create(['enabled_addons' => ['academic_records']]);
        $user = $this->authorizedUser($university);

        foreach (range(1, 12) as $ignored) {
            $this->makeBatch($university, $user, 'open');
        }
        foreach (range(1, 3) as $ignored) {
            $this->makeBatch($university, $user, 'closed');
        }

        $response = $this->actingAs($user)->get(route('students.import.index'))->assertOk();

        $active = $response->viewData('activeBatches');
        $closed = $response->viewData('closedBatches');
        $this->assertSame(12, $active->total());
        $this->assertCount(10, $active->items());
        $this->assertSame(3, $closed->total());
        $this->assertTrue(collect($active->items())->every(fn ($batch) => $batch->status === 'open'));
        $this->assertTrue(collect($closed->items())->every(fn ($batch) => $batch->status === 'closed'));

        $second = $this->actingAs($user)->get(route('students.import.index', ['history_page' => 2]))->viewData('activeBatches');
        $this->assertCount(2, $second->items());
    }

    public function test_latest_upload_card_lists_that_batchs_graduates_not_recently_edited_ones(): void
    {
        $university = University::factory()->create(['enabled_addons' => ['academic_records']]);
        $user = $this->authorizedUser($university);

        $older = $this->makeBatch($university, $user);
        $latest = $this->makeBatch($university, $user);
        $inOlder = Student::factory()->create(['university_id' => $university->id, 'roster_import_id' => $older->id]);
        $inLatest = Student::factory()->count(2)->create(['university_id' => $university->id, 'roster_import_id' => $latest->id]);

        // Editing a graduate from the older batch just now must not push it into the "latest upload" card.
        $inOlder->touch();

        $response = $this->actingAs($user)->get(route('students.import.index'))->assertOk();

        $this->assertSame($latest->id, $response->viewData('latestBatch')->id);
        $shownIds = $response->viewData('recentImports')->pluck('id')->all();
        $this->assertEqualsCanonicalizing($inLatest->pluck('id')->all(), $shownIds);
        $this->assertNotContains($inOlder->id, $shownIds);
    }

    public function test_flagged_rows_can_be_reviewed_with_a_link_to_the_graduate(): void
    {
        $university = University::factory()->create(['enabled_addons' => ['academic_records']]);
        $user = $this->authorizedUser($university);
        $student = Student::factory()->create(['university_id' => $university->id, 'registration_number' => 'DGA 01', 'programme_start_academic_year' => null]);
        $fixed = Student::factory()->create(['university_id' => $university->id, 'registration_number' => 'DGA 02', 'programme_start_academic_year' => '2007/2008']);

        $batch = $this->makeBatch($university, $user, 'open', [
            'flagged' => [
                ['row' => 3, 'registration_number' => 'DGA 01', 'reason' => 'Could not derive the start Academic Year.'],
                ['row' => 4, 'registration_number' => 'DGA 02', 'reason' => 'Could not derive the start Academic Year.'],
                ['row' => 0, 'registration_number' => '', 'reason' => 'No Graduation Ceremony is on file for 2010.'],
            ],
            'graduation_year' => 2010,
        ]);

        $items = $this->actingAs($user)->get(route('students.import.index'))->viewData('reviewItems')[$batch->id];

        // DGA 02 was fixed since the upload, so only DGA 01 and the ceremony flag remain.
        $this->assertCount(2, $items);
        $this->assertSame(route('students.edit', $student->id), $items[0]['edit_url']);
        $this->assertNull($items[1]['edit_url']);
        $this->assertSame('No Graduation Ceremony is on file for 2010.', $items[1]['reason']);
    }

    public function test_graduates_by_year_counts_males_females_and_totals_for_this_university_only(): void
    {
        $university = University::factory()->create(['enabled_addons' => ['academic_records']]);
        $other = University::factory()->create();
        $user = $this->authorizedUser($university);

        Student::factory()->count(3)->create(['university_id' => $university->id, 'is_graduated' => true, 'graduation_year' => 2010, 'gender' => 'M']);
        Student::factory()->count(2)->create(['university_id' => $university->id, 'is_graduated' => true, 'graduation_year' => 2010, 'gender' => 'F']);
        Student::factory()->create(['university_id' => $university->id, 'is_graduated' => true, 'graduation_year' => 2010, 'gender' => null]);
        Student::factory()->count(4)->create(['university_id' => $university->id, 'is_graduated' => true, 'graduation_year' => 2011, 'gender' => 'F']);
        Student::factory()->create(['university_id' => $university->id, 'is_graduated' => false, 'graduation_year' => 2011, 'gender' => 'M']);
        Student::factory()->count(5)->create(['university_id' => $other->id, 'is_graduated' => true, 'graduation_year' => 2010, 'gender' => 'M']);

        $stats = $this->actingAs($user)->get(route('students.import.index'))->assertOk()->viewData('yearStats');

        $this->assertSame([2011, 2010], $stats->pluck('graduation_year')->map(fn ($year) => (int) $year)->all());

        $y2010 = $stats->firstWhere('graduation_year', 2010);
        $this->assertSame([3, 2, 6], [(int) $y2010->males, (int) $y2010->females, (int) $y2010->total]);

        $response = $this->actingAs($user)->get(route('students.import.index'));
        $this->assertSame(1, $response->viewData('unknownSexCount'));
        $this->assertCount(1, $response->viewData('unknownSexGraduates'));

        $y2011 = $stats->firstWhere('graduation_year', 2011);
        $this->assertSame([0, 4, 4], [(int) $y2011->males, (int) $y2011->females, (int) $y2011->total]);
    }

    public function test_upload_creates_updates_and_derives_fields_correctly(): void
    {
        $university = University::factory()->create(['enabled_addons' => ['academic_records']]);
        $campus = Campus::factory()->create(['university_id' => $university->id]);
        $school = School::factory()->create(['campus_id' => $campus->id]);
        $program = Program::factory()->create([
            'school_id' => $school->id,
            'name' => 'Bachelor of Science in Information Technology',
        ]);

        GraduationCeremony::factory()->create([
            'year' => 2024,
            'sequence_number' => 15,
            'ceremony_date' => '2024-11-21',
        ]);

        $existingStudent = Student::factory()->create([
            'university_id' => $university->id,
            'registration_number' => '2020/0099',
            'program_id' => $program->id,
            'certificate_barcode' => 'PRE-EXISTING-BARCODE',
        ]);

        // Realistic pre-existing data: this person already has a recorded
        // graduation for the exact Campus/Programme/Year the batch below
        // re-uploads - only then is the row genuinely an "update" to an
        // existing graduation, not the first-ever graduation for them.
        StudentGraduation::create([
            'student_id' => $existingStudent->id,
            'university_id' => $university->id,
            'campus_id' => $campus->id,
            'program_id' => $program->id,
            'graduation_year' => 2024,
            'last_name' => $existingStudent->last_name,
            'first_name' => $existingStudent->first_name,
            'certificate_barcode' => 'PRE-EXISTING-BARCODE',
            'is_graduated' => true,
        ]);

        $user = $this->authorizedUser($university);

        $file = $this->buildRosterFile([
            ['2022/0001', 'MALECELA', 'Nyagalu', 'Michael', 'M', ''],
            [$existingStudent->registration_number, 'UPDATEDNAME', 'Jane', '', 'F', ''],
            ['D/2016/0005', 'OLDSTUDENT', 'Jane', '', 'F', ''],
        ]);

        $response = $this->actingAs($user)->post(route('students.import.upload'), [
            'campus_id' => $campus->id,
            'program_id' => $program->id,
            'graduation_year' => 2024,
            'file' => $file,
        ]);

        $response->assertRedirect(route('students.import.index'));

        // New student: standard reg-number format, year derivation and
        // ceremony lookup should both resolve, sex should map to gender.
        $new = Student::where('registration_number', '2022/0001')->first();
        $this->assertNotNull($new);
        $this->assertSame('2022/2023', $new->programme_start_academic_year);
        $this->assertSame('2023/2024', $new->programme_end_academic_year);
        $this->assertSame(15, $new->graduation_ceremony_number);
        $this->assertSame('2024-11-21', $new->graduation_date->format('Y-m-d'));
        $this->assertTrue((bool) $new->is_graduated);
        $this->assertSame($program->id, $new->program_id);
        $this->assertSame($campus->id, $new->campus_id);
        $this->assertSame('M', $new->gender);

        // Existing student: updated in place, and its pre-existing
        // certificate barcode must survive the re-import untouched.
        $existingStudent->refresh();
        $this->assertSame('UPDATEDNAME', $existingStudent->last_name);
        $this->assertSame('PRE-EXISTING-BARCODE', $existingStudent->certificate_barcode);
        $this->assertSame('F', $existingStudent->gender);

        // Legacy-format reg number (before 2018 graduation year cutoff
        // applied at upload time via the batch's own graduation_year):
        // certificate barcode auto-fills to the registration number, but
        // the non-standard format can't be used to derive a start
        // academic year.
        $legacy = Student::where('registration_number', 'D/2016/0005')->first();
        $this->assertNotNull($legacy);
        $this->assertNull($legacy->programme_start_academic_year);

        $this->assertSame(3, Student::where('university_id', $university->id)->count());

        // Audit trail persisted, with its own reference number and an
        // uploaded_at timestamp (this row represents the upload event).
        $batch = StudentRosterImport::first();
        $this->assertNotNull($batch);
        $this->assertSame(2, $batch->created_count);
        $this->assertSame(1, $batch->updated_count);
        $this->assertSame(1, $batch->flagged_count);
        $this->assertSame($user->id, $batch->imported_by);
        $this->assertNotNull($batch->reference_number);
        $this->assertStringStartsWith('ROS', $batch->reference_number);
        $this->assertSame('open', $batch->status);
        $this->assertNotNull($batch->uploaded_at);
        $this->assertNull($batch->downloaded_at);
    }

    public function test_download_template_creates_its_own_open_reference(): void
    {
        $university = University::factory()->create(['enabled_addons' => ['academic_records']]);
        $campus = Campus::factory()->create(['university_id' => $university->id]);
        $school = School::factory()->create(['campus_id' => $campus->id]);
        $program = Program::factory()->create(['school_id' => $school->id]);
        $user = $this->authorizedUser($university);

        $response = $this->actingAs($user)->get(route('students.import.template', [
            'campus_id' => $campus->id,
            'program_id' => $program->id,
            'graduation_year' => 2024,
        ]));

        $response->assertOk();

        $batch = StudentRosterImport::first();
        $this->assertNotNull($batch);
        $this->assertNotNull($batch->reference_number);
        $this->assertStringStartsWith('ROS', $batch->reference_number);
        $this->assertSame('open', $batch->status);
        $this->assertNotNull($batch->downloaded_at);
        $this->assertNull($batch->uploaded_at);
        $this->assertSame(0, $batch->created_count);
    }

    public function test_open_reference_can_be_closed_and_a_foreign_one_cannot(): void
    {
        $university = University::factory()->create(['enabled_addons' => ['academic_records']]);
        $otherUniversity = University::factory()->create();
        $user = $this->authorizedUser($university);

        $ownBatch = StudentRosterImport::create([
            'reference_number' => 'ROS0000000001',
            'status' => 'open',
            'university_id' => $university->id,
            'imported_by' => $user->id,
            'original_filename' => 'roster.xlsx',
            'downloaded_at' => now(),
        ]);

        $foreignBatch = StudentRosterImport::create([
            'reference_number' => 'ROS0000000002',
            'status' => 'open',
            'university_id' => $otherUniversity->id,
            'imported_by' => $user->id,
            'original_filename' => 'roster.xlsx',
            'downloaded_at' => now(),
        ]);

        $response = $this->actingAs($user)->post(route('students.import.close', $ownBatch->id));
        $response->assertRedirect(route('students.import.index'));
        $this->assertSame('closed', $ownBatch->fresh()->status);
        $this->assertSame($user->id, $ownBatch->fresh()->closed_by);

        $this->actingAs($user)->post(route('students.import.close', $foreignBatch->id))->assertForbidden();
        $this->assertSame('open', $foreignBatch->fresh()->status);
    }

    public function test_swahili_sex_values_me_and_ke_are_understood(): void
    {
        $university = University::factory()->create(['enabled_addons' => ['academic_records']]);
        $campus = Campus::factory()->create(['university_id' => $university->id]);
        $program = Program::factory()->create(['school_id' => School::factory()->create(['campus_id' => $campus->id])->id]);
        GraduationCeremony::factory()->create(['year' => 2024, 'sequence_number' => 15, 'ceremony_date' => '2024-11-21']);
        $user = $this->authorizedUser($university);

        $file = $this->buildRosterFile([
            ['2022/0101', 'ONE', 'Juma', '', 'Me', ''],
            ['2022/0102', 'TWO', 'Amina', '', 'ke', ''],
            ['2022/0103', 'THREE', 'Baraka', '', ' ME ', ''],
            ['2022/0104', 'FOUR', 'Neema', '', 'Kike', ''],
        ]);

        $this->actingAs($user)->post(route('students.import.upload'), [
            'campus_id' => $campus->id,
            'program_id' => $program->id,
            'graduation_year' => 2024,
            'file' => $file,
        ]);

        $this->assertSame('M', Student::where('registration_number', '2022/0101')->value('gender'));
        $this->assertSame('F', Student::where('registration_number', '2022/0102')->value('gender'));
        $this->assertSame('M', Student::where('registration_number', '2022/0103')->value('gender'));
        $this->assertSame('F', Student::where('registration_number', '2022/0104')->value('gender'));
        $this->assertSame(0, StudentRosterImport::first()->flagged_count);
    }

    public function test_unrecognised_sex_value_is_flagged_but_row_still_imports(): void
    {
        $university = University::factory()->create(['enabled_addons' => ['academic_records']]);
        $campus = Campus::factory()->create(['university_id' => $university->id]);
        $school = School::factory()->create(['campus_id' => $campus->id]);
        $program = Program::factory()->create(['school_id' => $school->id]);
        GraduationCeremony::factory()->create(['year' => 2024, 'sequence_number' => 15, 'ceremony_date' => '2024-11-21']);
        $user = $this->authorizedUser($university);

        $file = $this->buildRosterFile([
            ['2022/0003', 'MWAKASEGE', 'John', '', 'X', ''],
        ]);

        $this->actingAs($user)->post(route('students.import.upload'), [
            'campus_id' => $campus->id,
            'program_id' => $program->id,
            'graduation_year' => 2024,
            'file' => $file,
        ]);

        $student = Student::where('registration_number', '2022/0003')->first();
        $this->assertNotNull($student);
        $this->assertNull($student->gender);

        $batch = StudentRosterImport::first();
        $this->assertSame(1, $batch->created_count);
        $this->assertSame(1, $batch->flagged_count);
    }

    public function test_program_from_another_university_is_rejected(): void
    {
        $university = University::factory()->create(['enabled_addons' => ['academic_records']]);
        $campus = Campus::factory()->create(['university_id' => $university->id]);
        $otherUniversity = University::factory()->create();
        $otherCampus = Campus::factory()->create(['university_id' => $otherUniversity->id]);
        $otherSchool = School::factory()->create(['campus_id' => $otherCampus->id]);
        $otherProgram = Program::factory()->create(['school_id' => $otherSchool->id]);

        $user = $this->authorizedUser($university);

        $file = $this->buildRosterFile([
            ['2022/0004', 'CROSSTENANT', 'Attempt', '', 'M', ''],
        ]);

        $response = $this->actingAs($user)->post(route('students.import.upload'), [
            'campus_id' => $campus->id,
            'program_id' => $otherProgram->id,
            'graduation_year' => 2024,
            'file' => $file,
        ]);

        $response->assertStatus(422);
        $this->assertNull(Student::where('registration_number', '2022/0004')->first());
    }

    public function test_campus_from_another_university_is_rejected(): void
    {
        $university = University::factory()->create(['enabled_addons' => ['academic_records']]);
        $campus = Campus::factory()->create(['university_id' => $university->id]);
        $school = School::factory()->create(['campus_id' => $campus->id]);
        $program = Program::factory()->create(['school_id' => $school->id]);

        $otherUniversity = University::factory()->create();
        $otherCampus = Campus::factory()->create(['university_id' => $otherUniversity->id]);

        $user = $this->authorizedUser($university);

        $file = $this->buildRosterFile([
            ['2022/0005', 'CROSSCAMPUS', 'Attempt', '', 'M', ''],
        ]);

        $response = $this->actingAs($user)->post(route('students.import.upload'), [
            'campus_id' => $otherCampus->id,
            'program_id' => $program->id,
            'graduation_year' => 2024,
            'file' => $file,
        ]);

        $response->assertStatus(422);
        $this->assertNull(Student::where('registration_number', '2022/0005')->first());
    }

    public function test_file_with_wrong_headers_is_rejected_before_any_row_is_processed(): void
    {
        $university = University::factory()->create(['enabled_addons' => ['academic_records']]);
        $campus = Campus::factory()->create(['university_id' => $university->id]);
        $school = School::factory()->create(['campus_id' => $campus->id]);
        $program = Program::factory()->create(['school_id' => $school->id]);
        GraduationCeremony::factory()->create(['year' => 2024, 'sequence_number' => 15, 'ceremony_date' => '2024-11-21']);
        $user = $this->authorizedUser($university);

        $spreadsheet = new Spreadsheet;
        $spreadsheet->getActiveSheet()->fromArray([
            ['Staff ID', 'Name', 'Department'],
            ['STF001', 'Someone Else', 'HR'],
        ]);
        $path = tempnam(sys_get_temp_dir(), 'wrongfile').'.xlsx';
        (new Xlsx($spreadsheet))->save($path);
        $file = new UploadedFile($path, 'staff_list.xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', null, true);

        $response = $this->actingAs($user)->post(route('students.import.upload'), [
            'campus_id' => $campus->id,
            'program_id' => $program->id,
            'graduation_year' => 2024,
            'file' => $file,
        ]);

        $response->assertRedirect(route('students.import.index'));
        $response->assertSessionHas('error');

        $this->assertSame(0, Student::count());

        $batch = StudentRosterImport::first();
        $this->assertNotNull($batch);
        $this->assertSame(0, $batch->created_count);
        $this->assertSame(0, $batch->updated_count);
        $this->assertSame(1, $batch->flagged_count);
    }

    public function test_upload_is_blocked_without_import_permission(): void
    {
        $university = University::factory()->create(['enabled_addons' => ['academic_records']]);
        Permission::firstOrCreate(['name' => 'view_students', 'guard_name' => 'web']);

        $user = User::factory()->create(['university_id' => $university->id]);
        $user->givePermissionTo(['view_students']);

        $file = $this->buildRosterFile([]);

        $response = $this->actingAs($user)->post(route('students.import.upload'), ['file' => $file]);

        $response->assertForbidden();
    }

    // ------------------------------------------------------------------
    // Reset: undoes a batch. `upload()` (the plain form submit - still the
    // real <form action>, not just a JS-only path) must record
    // created/updated registration numbers exactly like the staged/JS flow
    // does, or Reset silently leaves stale, orphaned data behind.
    // ------------------------------------------------------------------

    public function test_reset_after_a_plain_non_staged_upload_fully_undoes_it(): void
    {
        $university = University::factory()->create(['enabled_addons' => ['academic_records']]);
        $campus = Campus::factory()->create(['university_id' => $university->id]);
        $program = Program::factory()->create(['school_id' => School::factory()->create(['campus_id' => $campus->id])->id]);
        GraduationCeremony::factory()->create(['year' => 2020, 'sequence_number' => 1, 'ceremony_date' => '2020-11-01']);
        GraduationCeremony::factory()->create(['year' => 2024, 'sequence_number' => 2, 'ceremony_date' => '2024-11-01']);

        // A person who already has an EARLIER, unrelated graduation - this
        // batch will add a second one for them. Resetting the batch must
        // remove only the second, leaving the first exactly as it was.
        $existing = Student::factory()->create(['university_id' => $university->id, 'registration_number' => '2010/0001']);
        StudentGraduation::create([
            'student_id' => $existing->id, 'university_id' => $university->id,
            'campus_id' => $campus->id, 'program_id' => $program->id, 'graduation_year' => 2020,
            'last_name' => 'ORIGINAL', 'first_name' => 'Name', 'is_graduated' => true,
        ]);

        $user = $this->authorizedUser($university);

        // Posts to the real <form action> route directly (the plain, non-staged path).
        $file = $this->buildRosterFile([
            ['2022/0001', 'BRANDNEW', 'Person', '', 'M', ''],
            ['2010/0001', 'SECONDGRAD', 'Name', '', 'M', ''],
        ]);
        $this->actingAs($user)->post(route('students.import.upload'), [
            'campus_id' => $campus->id, 'program_id' => $program->id, 'graduation_year' => 2024, 'file' => $file,
        ]);

        $batch = StudentRosterImport::latest('id')->firstOrFail();
        // Neither row matches an existing (Campus, Programme, 2024) graduation -
        // 2010/0001 already existed as a PERSON, but this is a genuinely new,
        // second graduation for them, so it correctly counts as "created" too.
        $this->assertEqualsCanonicalizing(['2022/0001', '2010/0001'], $batch->created_reg_numbers);
        $this->assertSame([], $batch->updated_reg_numbers);
        $this->assertSame(2, StudentGraduation::where('student_id', $existing->id)->count());
        $this->assertSame(2024, (int) $existing->fresh()->graduation_year);

        $this->actingAs($user)->post(route('students.import.reset', $batch->id))
            ->assertRedirect(route('students.import.index'));

        // The brand new student is gone entirely.
        $this->assertNull(Student::where('registration_number', '2022/0001')->first());

        // The pre-existing student survives with only their original (2020) graduation.
        $existing->refresh();
        $this->assertNotNull($existing);
        $this->assertSame(1, StudentGraduation::where('student_id', $existing->id)->count());
        $this->assertSame(2020, (int) $existing->graduation_year);
        $this->assertSame('ORIGINAL', $existing->last_name);
        $this->assertNull(StudentGraduation::where('roster_import_id', $batch->id)->first());
        $this->assertNull(StudentRosterImport::find($batch->id));
    }

    public function test_reset_is_refused_once_a_reference_is_closed(): void
    {
        $university = University::factory()->create(['enabled_addons' => ['academic_records']]);
        $campus = Campus::factory()->create(['university_id' => $university->id]);
        $program = Program::factory()->create(['school_id' => School::factory()->create(['campus_id' => $campus->id])->id]);
        GraduationCeremony::factory()->create(['year' => 2024, 'sequence_number' => 1, 'ceremony_date' => '2024-11-01']);
        $user = $this->authorizedUser($university);

        $file = $this->buildRosterFile([['2022/0001', 'PERSON', 'One', '', 'M', '']]);
        $this->actingAs($user)->post(route('students.import.upload'), [
            'campus_id' => $campus->id, 'program_id' => $program->id, 'graduation_year' => 2024, 'file' => $file,
        ]);
        $batch = StudentRosterImport::latest('id')->firstOrFail();

        $this->actingAs($user)->post(route('students.import.close', $batch->id));

        $this->actingAs($user)->post(route('students.import.reset', $batch->id))
            ->assertRedirect(route('students.import.index'))
            ->assertSessionHas('error');

        // Untouched: the graduate created by this now-closed batch is still there.
        $this->assertNotNull(Student::where('registration_number', '2022/0001')->first());
        $this->assertNotNull(StudentRosterImport::find($batch->id));
    }

    public function test_reset_confirmation_counts_alumni_letters_that_would_also_be_deleted(): void
    {
        $university = University::factory()->create(['enabled_addons' => ['academic_records']]);
        $campus = Campus::factory()->create(['university_id' => $university->id]);
        $program = Program::factory()->create(['school_id' => School::factory()->create(['campus_id' => $campus->id])->id]);
        GraduationCeremony::factory()->create(['year' => 2024, 'sequence_number' => 1, 'ceremony_date' => '2024-11-01']);
        $user = $this->authorizedUser($university);

        $file = $this->buildRosterFile([['2022/0001', 'PERSON', 'One', '', 'M', '']]);
        $this->actingAs($user)->post(route('students.import.upload'), [
            'campus_id' => $campus->id, 'program_id' => $program->id, 'graduation_year' => 2024, 'file' => $file,
        ]);
        $batch = StudentRosterImport::latest('id')->firstOrFail();
        $graduate = Student::where('registration_number', '2022/0001')->firstOrFail();

        GraduationLetter::create([
            'university_id' => $university->id,
            'student_id' => $graduate->id,
            'type' => 'confirmation',
            'letter_number' => 'SJUT/ALM/2024/0001',
            'secure_code' => 'TEST-SECURE-CODE-0001',
        ]);

        $response = $this->actingAs($user)->get(route('students.import.show', $batch->id));

        $response->assertOk();
        $this->assertSame(1, $response->viewData('lettersAffected'));
        $response->assertSee('will also delete 1 alumni letter');
    }
}
