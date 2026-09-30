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
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class StudentEditTest extends TestCase
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

    /**
     * A graduated student with a Campus, Programme (3 years) and a
     * Graduation Year that has a ceremony - the shape a roster import
     * leaves behind - plus the payload the Edit form would post unchanged.
     *
     * @return array{0: University, 1: Student, 2: array<string, mixed>}
     */
    private function graduateWithUnchangedForm(string $registrationNumber = 'DGA 01'): array
    {
        $university = University::factory()->create(['enabled_addons' => ['academic_records']]);
        $campus = Campus::factory()->create(['university_id' => $university->id]);
        $program = Program::factory()->create([
            'school_id' => School::factory()->create(['campus_id' => $campus->id])->id,
            'duration_years' => 3,
        ]);

        GraduationCeremony::factory()->create(['year' => 2010, 'sequence_number' => 1, 'ceremony_date' => '2010-11-06']);
        GraduationCeremony::factory()->create(['year' => 2012, 'sequence_number' => 3, 'ceremony_date' => '2012-11-10']);

        $student = Student::factory()->create([
            'university_id' => $university->id,
            'campus_id' => $campus->id,
            'program_id' => $program->id,
            'registration_number' => $registrationNumber,
            'is_graduated' => true,
            'graduation_year' => 2010,
            'graduation_date' => '2010-11-06',
            'graduation_ceremony_number' => 1,
            'programme_start_academic_year' => '2007/2008',
            'programme_end_academic_year' => '2009/2010',
            'programme_duration_years' => 3,
        ]);

        $payload = [
            'campus_id' => $campus->id,
            'program_id' => $program->id,
            'graduation_year' => 2010,
            'last_name' => $student->last_name,
            'first_name' => $student->first_name,
            'programme_duration_years' => 3,
            'programme_start_academic_year' => '2007/2008',
            'programme_end_academic_year' => '2009/2010',
            'graduation_date' => '2010-11-06',
        ];

        return [$university, $student, $payload];
    }

    public function test_missing_letter_fields_can_be_filled_in_via_edit(): void
    {
        [$university, $student, $payload] = $this->graduateWithUnchangedForm();
        $user = $this->authorizedUser($university);

        $response = $this->actingAs($user)->put(route('students.update', $student->id), $payload + [
            'degree_name' => 'Bachelor of Science in Information Technology',
            'degree_abbreviation' => 'BScIT',
            'classification' => 'Upper Second Class Honours',
            'gpa' => '3.85',
            'certificate_barcode' => 'CERT-0001',
        ]);

        $response->assertRedirect(route('students.show', $student->id));

        $student->refresh();
        $this->assertSame('Bachelor of Science in Information Technology', $student->degree_name);
        $this->assertSame('BScIT', $student->degree_abbreviation);
        $this->assertSame('Upper Second Class Honours', $student->classification);
        $this->assertSame('3.85', $student->gpa);
        $this->assertSame(3, $student->programme_duration_years);
        $this->assertSame('CERT-0001', $student->certificate_barcode);
    }

    public function test_campus_programme_and_graduation_year_can_be_corrected_and_derived_fields_follow(): void
    {
        [$university, $student, $payload] = $this->graduateWithUnchangedForm('DGA 01');
        $newCampus = Campus::factory()->create(['university_id' => $university->id]);
        $newProgram = Program::factory()->create([
            'school_id' => School::factory()->create(['campus_id' => $newCampus->id])->id,
            'duration_years' => 4,
        ]);
        $user = $this->authorizedUser($university);

        $this->actingAs($user)->put(route('students.update', $student->id), array_merge($payload, [
            'campus_id' => $newCampus->id,
            'program_id' => $newProgram->id,
            'graduation_year' => 2012,
        ]))->assertRedirect(route('students.show', $student->id));

        $student->refresh();
        $this->assertSame($newCampus->id, $student->campus_id);
        $this->assertSame($newProgram->id, $student->program_id);
        $this->assertSame(2012, (int) $student->graduation_year);
        $this->assertSame(4, (int) $student->programme_duration_years);
        $this->assertSame('2008/2009', $student->programme_start_academic_year);
        $this->assertSame('2011/2012', $student->programme_end_academic_year);
        $this->assertSame('2012-11-10', $student->graduation_date->format('Y-m-d'));
        $this->assertSame(3, (int) $student->graduation_ceremony_number);
    }

    public function test_values_typed_in_by_hand_are_not_overwritten_when_the_programme_changes(): void
    {
        [$university, $student, $payload] = $this->graduateWithUnchangedForm('DGA 01');
        $newProgram = Program::factory()->create([
            'school_id' => School::factory()->create(['campus_id' => $student->campus_id])->id,
            'duration_years' => 4,
        ]);
        $user = $this->authorizedUser($university);

        $this->actingAs($user)->put(route('students.update', $student->id), array_merge($payload, [
            'program_id' => $newProgram->id,
            'programme_duration_years' => 5,
            'programme_start_academic_year' => '2005/2006',
        ]))->assertRedirect();

        $student->refresh();
        $this->assertSame(5, (int) $student->programme_duration_years);
        $this->assertSame('2005/2006', $student->programme_start_academic_year);
    }

    public function test_programme_from_another_university_or_unknown_year_is_rejected(): void
    {
        [$university, $student, $payload] = $this->graduateWithUnchangedForm();
        $foreignCampus = Campus::factory()->create(['university_id' => University::factory()->create()->id]);
        $foreignProgram = Program::factory()->create(['school_id' => School::factory()->create(['campus_id' => $foreignCampus->id])->id]);
        $user = $this->authorizedUser($university);

        $this->actingAs($user)->put(route('students.update', $student->id), array_merge($payload, ['program_id' => $foreignProgram->id]))
            ->assertSessionHasErrors('program_id');
        $this->actingAs($user)->put(route('students.update', $student->id), array_merge($payload, ['campus_id' => $foreignCampus->id]))
            ->assertSessionHasErrors('campus_id');
        $this->actingAs($user)->put(route('students.update', $student->id), array_merge($payload, ['graduation_year' => 1999]))
            ->assertSessionHasErrors('graduation_year');
    }

    public function test_certificate_barcode_must_stay_unique(): void
    {
        [$university, $student, $payload] = $this->graduateWithUnchangedForm();
        Student::factory()->create(['university_id' => $university->id, 'certificate_barcode' => 'DUPLICATE-CODE']);
        $user = $this->authorizedUser($university);

        $response = $this->actingAs($user)->put(route('students.update', $student->id), $payload + [
            'certificate_barcode' => 'DUPLICATE-CODE',
        ]);

        $response->assertSessionHasErrors('certificate_barcode');
    }

    public function test_edit_requires_import_students_permission(): void
    {
        $university = University::factory()->create(['enabled_addons' => ['academic_records']]);
        $student = Student::factory()->create(['university_id' => $university->id]);
        Permission::firstOrCreate(['name' => 'view_students', 'guard_name' => 'web']);

        $user = User::factory()->create(['university_id' => $university->id]);
        $user->givePermissionTo(['view_students']);

        $this->actingAs($user)->get(route('students.edit', $student->id))->assertForbidden();
        $this->actingAs($user)->put(route('students.update', $student->id), [])->assertForbidden();
    }

    public function test_cannot_edit_a_student_from_another_university(): void
    {
        $university = University::factory()->create(['enabled_addons' => ['academic_records']]);
        $otherUniversity = University::factory()->create();
        $otherStudent = Student::factory()->create(['university_id' => $otherUniversity->id]);
        $user = $this->authorizedUser($university);

        $this->actingAs($user)->get(route('students.edit', $otherStudent->id))->assertForbidden();
    }

    public function test_edit_page_shows_the_current_campus_programme_and_year_as_selected(): void
    {
        [$university, $student] = $this->graduateWithUnchangedForm();
        $user = $this->authorizedUser($university);

        $this->actingAs($user)->get(route('students.edit', $student->id))
            ->assertOk()
            ->assertSee('EDIT GRADUATE RECORD')
            ->assertSee($student->campus->name)
            ->assertSee('2010 — Ceremony No. 1');
    }

    // ------------------------------------------------------------------
    // Realistic data: a Student with its own StudentGraduation row(s),
    // exactly what the roster importer actually produces. Every test above
    // this point uses a bare Student with NO graduation - which is why the
    // 2026-09-29 crash (StudentGraduation has no `full_name` column) was
    // never once exercised by the existing suite: `update()`'s "sync the
    // graduation too" branch was always skipped.
    // ------------------------------------------------------------------

    /**
     * @return array{0: University, 1: Student, 2: StudentGraduation, 3: array<string, mixed>}
     */
    private function graduateWithOneGraduation(string $registrationNumber = 'DGA 01'): array
    {
        [$university, $student, $payload] = $this->graduateWithUnchangedForm($registrationNumber);

        $graduation = StudentGraduation::create([
            'student_id' => $student->id,
            'university_id' => $university->id,
            'campus_id' => $student->campus_id,
            'program_id' => $student->program_id,
            'graduation_year' => $student->graduation_year,
            'graduation_date' => $student->graduation_date,
            'last_name' => $student->last_name,
            'first_name' => $student->first_name,
            'programme_start_academic_year' => $student->programme_start_academic_year,
            'programme_end_academic_year' => $student->programme_end_academic_year,
            'programme_duration_years' => $student->programme_duration_years,
            'is_graduated' => true,
        ]);

        return [$university, $student, $graduation, $payload];
    }

    public function test_saving_a_graduate_with_one_graduation_does_not_crash_and_keeps_both_records_in_sync(): void
    {
        [$university, $student, $graduation, $payload] = $this->graduateWithOneGraduation();
        $user = $this->authorizedUser($university);

        $this->actingAs($user)->put(route('students.update', $student->id), $payload + [
            'classification' => 'Upper Second Class Honours',
            'gpa' => '3.85',
        ])->assertRedirect(route('students.show', $student->id))->assertSessionDoesntHaveErrors();

        $student->refresh();
        $graduation->refresh();
        $this->assertSame('Upper Second Class Honours', $student->classification);
        $this->assertSame('Upper Second Class Honours', $graduation->classification);
        $this->assertSame('3.85', $student->gpa);
        $this->assertSame('3.85', $graduation->gpa);
    }

    public function test_a_graduate_with_two_graduations_requires_picking_which_one_to_edit(): void
    {
        [$university, $student, $first, $payload] = $this->graduateWithOneGraduation();
        $secondCampus = Campus::factory()->create(['university_id' => $university->id]);
        $secondProgram = Program::factory()->create(['school_id' => School::factory()->create(['campus_id' => $secondCampus->id])->id, 'duration_years' => 2]);
        GraduationCeremony::factory()->create(['year' => 2015, 'sequence_number' => 5, 'ceremony_date' => '2015-11-01']);

        $second = StudentGraduation::create([
            'student_id' => $student->id,
            'university_id' => $university->id,
            'campus_id' => $secondCampus->id,
            'program_id' => $secondProgram->id,
            'graduation_year' => 2015,
            'last_name' => $student->last_name,
            'first_name' => $student->first_name,
            'classification' => 'Distinction',
            'is_graduated' => true,
        ]);

        $user = $this->authorizedUser($university);

        // No graduation_id at all: rejected, not guessed.
        $this->actingAs($user)->put(route('students.update', $student->id), $payload)
            ->assertSessionHasErrors('graduation_id');

        // Correcting the FIRST (2010) graduation's classification must never touch the second (2015) one.
        $this->actingAs($user)->put(route('students.update', $student->id), $payload + [
            'graduation_id' => $first->id,
            'classification' => 'Lower Second Class Honours',
        ])->assertRedirect(route('students.show', $student->id));

        $first->refresh();
        $second->refresh();
        $this->assertSame('Lower Second Class Honours', $first->classification);
        $this->assertSame('Distinction', $second->classification);
        $this->assertSame(2010, (int) $first->graduation_year);
        $this->assertSame(2015, (int) $second->graduation_year);

        // The Student cache must reflect whichever graduation is truly latest (2015) - not whichever was just edited.
        $this->assertSame(2015, (int) $student->fresh()->graduation_year);
        $this->assertSame('Distinction', $student->fresh()->classification);

        // A graduation_id that belongs to someone else's graduation is rejected outright.
        $otherStudent = Student::factory()->create(['university_id' => $university->id]);
        $foreignGraduation = StudentGraduation::create([
            'student_id' => $otherStudent->id, 'university_id' => $university->id,
            'graduation_year' => 2010, 'last_name' => 'X', 'first_name' => 'Y', 'is_graduated' => true,
        ]);
        $this->actingAs($user)->put(route('students.update', $student->id), $payload + ['graduation_id' => $foreignGraduation->id])
            ->assertSessionHasErrors('graduation_id');
    }

    public function test_editing_a_graduation_into_a_year_programme_campus_already_used_by_another_of_theirs_is_rejected_cleanly(): void
    {
        [$university, $student, $first, $payload] = $this->graduateWithOneGraduation();

        $second = StudentGraduation::create([
            'student_id' => $student->id,
            'university_id' => $university->id,
            'campus_id' => $student->campus_id,
            'program_id' => $student->program_id,
            'graduation_year' => 2012,
            'last_name' => $student->last_name,
            'first_name' => $student->first_name,
            'is_graduated' => true,
        ]);

        $user = $this->authorizedUser($university);

        // Trying to move the FIRST graduation onto the exact same Campus/Programme/Year as the second: must not 500, must not corrupt either row.
        $response = $this->actingAs($user)->put(route('students.update', $student->id), array_merge($payload, [
            'graduation_id' => $first->id,
            'graduation_year' => 2012,
        ]));

        $response->assertSessionHasErrors('graduation_year');
        $this->assertSame(2010, (int) $first->fresh()->graduation_year);
        $this->assertSame(2012, (int) $second->fresh()->graduation_year);
    }
}
