<?php

namespace Tests\Feature;

use App\Models\Campus;
use App\Models\GraduationCeremony;
use App\Models\Program;
use App\Models\School;
use App\Models\Student;
use App\Models\StudentGraduation;
use App\Models\University;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class StudentImportUpdateReviewTest extends TestCase
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

    /**
     * @return array{0: University, 1: Campus, 2: Program, 3: User}
     */
    private function setUpCatalog(): array
    {
        $university = University::factory()->create(['enabled_addons' => ['academic_records']]);
        $campus = Campus::factory()->create(['university_id' => $university->id]);
        $program = Program::factory()->create([
            'school_id' => School::factory()->create(['campus_id' => $campus->id])->id,
            'duration_years' => 3,
        ]);
        GraduationCeremony::factory()->create(['year' => 2010, 'sequence_number' => 1, 'ceremony_date' => '2010-11-06']);
        $user = $this->authorizedUser($university);

        return [$university, $campus, $program, $user];
    }

    private function uploadRoster(User $user, Campus $campus, Program $program, array $dataRows): void
    {
        $this->actingAs($user)->post(route('students.import.upload'), [
            'campus_id' => $campus->id,
            'program_id' => $program->id,
            'graduation_year' => 2010,
            'file' => $this->buildRosterFile($dataRows),
        ]);
    }

    public function test_a_re_import_that_changes_a_field_is_recorded_as_a_pending_review(): void
    {
        [$university, $campus, $program, $user] = $this->setUpCatalog();

        $this->uploadRoster($user, $campus, $program, [['DGA 01', 'MADINDA', 'Emmanuel', 'Godfrey', 'M', '']]);
        $this->uploadRoster($user, $campus, $program, [['DGA 01', 'MADINDA', 'Emmanuel', 'Godfrey', 'F', '']]);

        $graduation = StudentGraduation::whereHas('student', fn ($q) => $q->where('registration_number', 'DGA 01'))->firstOrFail();

        $this->assertSame('pending', $graduation->import_review_status);
        $this->assertNotEmpty($graduation->import_diff);
        $this->assertSame('M', $graduation->import_diff_raw['gender']['old']);
        $this->assertSame('F', $graduation->import_diff_raw['gender']['new']);
    }

    public function test_accepting_an_update_records_the_reviewer_without_changing_any_data(): void
    {
        [$university, $campus, $program, $user] = $this->setUpCatalog();

        $this->uploadRoster($user, $campus, $program, [['DGA 01', 'MADINDA', 'Emmanuel', 'Godfrey', 'M', '']]);
        $this->uploadRoster($user, $campus, $program, [['DGA 01', 'MADINDA', 'Emmanuel', 'Godfrey', 'F', '']]);
        $graduation = StudentGraduation::whereHas('student', fn ($q) => $q->where('registration_number', 'DGA 01'))->firstOrFail();

        $this->actingAs($user)->post(route('students.import.updates.accept', $graduation->id))
            ->assertRedirect();

        $graduation->refresh();
        $this->assertSame('accepted', $graduation->import_review_status);
        $this->assertSame($user->id, $graduation->import_reviewed_by);
        $this->assertNotNull($graduation->import_reviewed_at);
        $this->assertSame('F', $graduation->gender);
    }

    public function test_rejecting_an_update_restores_the_previous_values_and_refreshes_the_student_cache(): void
    {
        [$university, $campus, $program, $user] = $this->setUpCatalog();

        $this->uploadRoster($user, $campus, $program, [['DGA 01', 'MADINDA', 'Emmanuel', 'Godfrey', 'M', '']]);
        $this->uploadRoster($user, $campus, $program, [['DGA 01', 'MADINDA', 'Emmanuel', 'Godfrey', 'F', '']]);
        $graduation = StudentGraduation::whereHas('student', fn ($q) => $q->where('registration_number', 'DGA 01'))->firstOrFail();
        $this->assertSame('F', $graduation->gender);

        $this->actingAs($user)->post(route('students.import.updates.reject', $graduation->id))
            ->assertRedirect();

        $graduation->refresh();
        $this->assertSame('rejected', $graduation->import_review_status);
        $this->assertSame($user->id, $graduation->import_reviewed_by);
        $this->assertSame('M', $graduation->gender);

        $student = Student::where('registration_number', 'DGA 01')->firstOrFail();
        $this->assertSame('M', $student->gender);
    }

    public function test_rejecting_a_legacy_update_with_no_raw_diff_is_marked_rejected_without_crashing(): void
    {
        [$university, $campus, $program, $user] = $this->setUpCatalog();

        $student = Student::factory()->create([
            'university_id' => $university->id,
            'registration_number' => 'DGA 09',
            'campus_id' => $campus->id,
            'program_id' => $program->id,
            'gender' => 'F',
        ]);
        $graduation = StudentGraduation::create([
            'student_id' => $student->id,
            'university_id' => $university->id,
            'campus_id' => $campus->id,
            'program_id' => $program->id,
            'graduation_year' => 2010,
            'last_name' => $student->last_name,
            'first_name' => $student->first_name,
            'gender' => 'F',
            'is_graduated' => true,
            // Simulates a row updated before this review feature existed:
            // a display diff was recorded, but no raw revert snapshot.
            'import_diff' => ['gender' => ['old' => 'M', 'new' => 'F']],
            'import_diff_raw' => null,
        ]);

        $response = $this->actingAs($user)->post(route('students.import.updates.reject', $graduation->id));

        $response->assertRedirect();
        $graduation->refresh();
        $this->assertSame('rejected', $graduation->import_review_status);
        // Nothing to replay - the field is left exactly as it was, not guessed at.
        $this->assertSame('F', $graduation->gender);
    }

    public function test_a_fresh_update_resets_a_previously_accepted_graduation_back_to_pending(): void
    {
        [$university, $campus, $program, $user] = $this->setUpCatalog();

        $this->uploadRoster($user, $campus, $program, [['DGA 01', 'MADINDA', 'Emmanuel', 'Godfrey', 'M', '']]);
        $this->uploadRoster($user, $campus, $program, [['DGA 01', 'MADINDA', 'Emmanuel', 'Godfrey', 'F', '']]);
        $graduation = StudentGraduation::whereHas('student', fn ($q) => $q->where('registration_number', 'DGA 01'))->firstOrFail();
        $this->actingAs($user)->post(route('students.import.updates.accept', $graduation->id));
        $this->assertSame('accepted', $graduation->fresh()->import_review_status);

        $this->uploadRoster($user, $campus, $program, [['DGA 01', 'MADINDA', 'Emmanuel', 'Godfrey', 'M', '']]);

        $this->assertSame('pending', $graduation->fresh()->import_review_status);
        $this->assertNull($graduation->fresh()->import_reviewed_by);
    }

    public function test_reviewing_another_universitys_update_is_forbidden(): void
    {
        [$university, $campus, $program, $user] = $this->setUpCatalog();
        $this->uploadRoster($user, $campus, $program, [['DGA 01', 'MADINDA', 'Emmanuel', 'Godfrey', 'M', '']]);
        $this->uploadRoster($user, $campus, $program, [['DGA 01', 'MADINDA', 'Emmanuel', 'Godfrey', 'F', '']]);
        $graduation = StudentGraduation::whereHas('student', fn ($q) => $q->where('registration_number', 'DGA 01'))->firstOrFail();

        $otherUniversity = University::factory()->create(['enabled_addons' => ['academic_records']]);
        $outsider = $this->authorizedUser($otherUniversity);

        $this->actingAs($outsider)->post(route('students.import.updates.accept', $graduation->id))->assertForbidden();
        $this->actingAs($outsider)->post(route('students.import.updates.reject', $graduation->id))->assertForbidden();
    }
}
