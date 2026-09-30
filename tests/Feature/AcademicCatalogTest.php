<?php

namespace Tests\Feature;

use App\Models\Campus;
use App\Models\Program;
use App\Models\School;
use App\Models\Student;
use App\Models\University;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class AcademicCatalogTest extends TestCase
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

    public function test_school_can_be_added_with_a_type(): void
    {
        $university = University::factory()->create(['enabled_addons' => ['academic_records']]);
        $campus = Campus::factory()->create(['university_id' => $university->id]);
        $user = $this->authorizedUser($university);

        $response = $this->actingAs($user)->post(route('students.catalog.schools.store'), [
            'name' => 'Centre for Innovation',
            'type' => 'Centre',
            'code' => 'CFI',
            'campus_id' => $campus->id,
        ]);

        $response->assertRedirect(route('students.catalog.index'));

        $school = School::where('code', 'CFI')->first();
        $this->assertNotNull($school);
        $this->assertSame('Centre', $school->type);
        $this->assertSame($campus->id, $school->campus_id);
    }

    public function test_school_type_must_be_one_of_the_known_values(): void
    {
        $university = University::factory()->create(['enabled_addons' => ['academic_records']]);
        $campus = Campus::factory()->create(['university_id' => $university->id]);
        $user = $this->authorizedUser($university);

        $response = $this->actingAs($user)->post(route('students.catalog.schools.store'), [
            'name' => 'Made Up Unit',
            'type' => 'Something Else',
            'code' => 'MUU',
            'campus_id' => $campus->id,
        ]);

        $response->assertSessionHasErrors('type');
        $this->assertNull(School::where('code', 'MUU')->first());
    }

    public function test_program_can_be_added_under_a_school(): void
    {
        $university = University::factory()->create(['enabled_addons' => ['academic_records']]);
        $campus = Campus::factory()->create(['university_id' => $university->id]);
        $school = School::factory()->create(['campus_id' => $campus->id]);
        $user = $this->authorizedUser($university);

        $response = $this->actingAs($user)->post(route('students.catalog.programs.store'), [
            'name' => 'Bachelor of Science in Statistics',
            'code' => 'BSTAT',
            'study_level' => 'Bachelor',
            'duration_years' => 3,
            'school_id' => $school->id,
        ]);

        $response->assertRedirect(route('students.catalog.index'));

        $program = Program::where('code', 'BSTAT')->first();
        $this->assertNotNull($program);
        $this->assertSame($school->id, $program->school_id);
        $this->assertSame(3, (int) $program->duration_years);
    }

    public function test_a_programme_name_cannot_be_added_twice_in_the_same_university(): void
    {
        $university = University::factory()->create(['enabled_addons' => ['academic_records']]);
        $campus = Campus::factory()->create(['university_id' => $university->id]);
        $school = School::factory()->create(['campus_id' => $campus->id]);
        Program::factory()->create(['school_id' => $school->id, 'name' => 'Bachelor of Arts in Theology', 'code' => 'BATh']);
        $user = $this->authorizedUser($university);

        $this->actingAs($user)->post(route('students.catalog.programs.store'), [
            'name' => '  bachelor of arts in theology ',
            'code' => 'BAT',
            'study_level' => 'Bachelor',
            'duration_years' => 3,
            'school_id' => $school->id,
        ])->assertSessionHasErrors('name');

        $this->assertSame(1, Program::where('school_id', $school->id)->count());

        $otherUniversity = University::factory()->create();
        $otherSchool = School::factory()->create(['campus_id' => Campus::factory()->create(['university_id' => $otherUniversity->id])->id]);
        Program::factory()->create(['school_id' => $otherSchool->id, 'name' => 'Same Name Elsewhere']);
        $this->actingAs($user)->post(route('students.catalog.programs.store'), [
            'name' => 'Same Name Elsewhere',
            'study_level' => 'Bachelor',
            'duration_years' => 3,
            'school_id' => $school->id,
        ])->assertSessionHasNoErrors();
    }

    public function test_a_programme_can_be_moved_to_the_right_faculty_and_its_details_corrected(): void
    {
        $university = University::factory()->create(['enabled_addons' => ['academic_records']]);
        $campus = Campus::factory()->create(['university_id' => $university->id]);
        $wrong = School::factory()->create(['campus_id' => $campus->id, 'code' => 'FAHE']);
        $right = School::factory()->create(['campus_id' => $campus->id, 'code' => 'FOCB']);
        $program = Program::factory()->create(['school_id' => $wrong->id, 'name' => 'Bachelor of Marketing', 'code' => 'BoM', 'duration_years' => 3]);
        $graduate = Student::factory()->create(['university_id' => $university->id, 'program_id' => $program->id]);
        $user = $this->authorizedUser($university);

        $this->actingAs($user)->put(route('students.catalog.programs.update', $program->id), [
            'name' => 'Bachelor of Marketing',
            'code' => 'BMKT',
            'study_level' => 'Bachelor',
            'duration_years' => 4,
            'school_id' => $right->id,
        ])->assertRedirect(route('students.catalog.index'))->assertSessionHas('success');

        $program->refresh();
        $this->assertSame($right->id, $program->school_id);
        $this->assertSame('BMKT', $program->code);
        $this->assertSame(4, (int) $program->duration_years);
        $this->assertSame($right->id, $graduate->fresh()->program->school_id);
    }

    public function test_editing_a_programme_cannot_take_another_programmes_name_or_cross_universities(): void
    {
        $university = University::factory()->create(['enabled_addons' => ['academic_records']]);
        $campus = Campus::factory()->create(['university_id' => $university->id]);
        $school = School::factory()->create(['campus_id' => $campus->id]);
        Program::factory()->create(['school_id' => $school->id, 'name' => 'Bachelor of Finance', 'code' => 'BF']);
        $program = Program::factory()->create(['school_id' => $school->id, 'name' => 'Bachelor of Marketing', 'code' => 'BoM']);
        $user = $this->authorizedUser($university);

        $payload = ['name' => 'bachelor of finance', 'study_level' => 'Bachelor', 'duration_years' => 3, 'school_id' => $school->id];
        $this->actingAs($user)->put(route('students.catalog.programs.update', $program->id), $payload)->assertSessionHasErrors('name');

        $otherCampus = Campus::factory()->create(['university_id' => University::factory()->create()->id]);
        $otherSchool = School::factory()->create(['campus_id' => $otherCampus->id]);
        $foreign = Program::factory()->create(['school_id' => $otherSchool->id]);

        $this->actingAs($user)->put(route('students.catalog.programs.update', $foreign->id), $payload)->assertForbidden();
        $this->actingAs($user)->put(route('students.catalog.programs.update', $program->id), ['school_id' => $otherSchool->id] + $payload)->assertStatus(404);
    }

    public function test_program_requires_a_valid_duration(): void
    {
        $university = University::factory()->create(['enabled_addons' => ['academic_records']]);
        $campus = Campus::factory()->create(['university_id' => $university->id]);
        $school = School::factory()->create(['campus_id' => $campus->id]);
        $user = $this->authorizedUser($university);

        foreach ([null, 0, 7] as $invalid) {
            $this->actingAs($user)->post(route('students.catalog.programs.store'), [
                'name' => 'Bad Duration Programme',
                'study_level' => 'Bachelor',
                'duration_years' => $invalid,
                'school_id' => $school->id,
            ])->assertSessionHasErrors('duration_years');
        }

        $this->assertNull(Program::where('name', 'Bad Duration Programme')->first());
    }

    public function test_program_duration_can_be_corrected_but_not_across_universities(): void
    {
        $university = University::factory()->create(['enabled_addons' => ['academic_records']]);
        $campus = Campus::factory()->create(['university_id' => $university->id]);
        $program = Program::factory()->create(['school_id' => School::factory()->create(['campus_id' => $campus->id])->id, 'duration_years' => 3]);

        $otherUniversity = University::factory()->create();
        $otherCampus = Campus::factory()->create(['university_id' => $otherUniversity->id]);
        $otherProgram = Program::factory()->create(['school_id' => School::factory()->create(['campus_id' => $otherCampus->id])->id, 'duration_years' => 3]);

        $user = $this->authorizedUser($university);

        $this->actingAs($user)
            ->put(route('students.catalog.programs.duration.update', $program->id), ['duration_years' => 4])
            ->assertRedirect(route('students.catalog.index'));
        $this->assertSame(4, (int) $program->fresh()->duration_years);

        $this->actingAs($user)
            ->put(route('students.catalog.programs.duration.update', $otherProgram->id), ['duration_years' => 1])
            ->assertForbidden();
        $this->assertSame(3, (int) $otherProgram->fresh()->duration_years);
    }

    public function test_program_cannot_be_added_under_another_universitys_school(): void
    {
        $university = University::factory()->create(['enabled_addons' => ['academic_records']]);
        $otherUniversity = University::factory()->create();
        $otherCampus = Campus::factory()->create(['university_id' => $otherUniversity->id]);
        $otherSchool = School::factory()->create(['campus_id' => $otherCampus->id]);
        $user = $this->authorizedUser($university);

        $response = $this->actingAs($user)->post(route('students.catalog.programs.store'), [
            'name' => 'Cross Tenant Programme',
            'study_level' => 'Bachelor',
            'duration_years' => 3,
            'school_id' => $otherSchool->id,
        ]);

        $response->assertStatus(404);
        $this->assertNull(Program::where('name', 'Cross Tenant Programme')->first());
    }

    public function test_graduation_ceremony_can_be_added_and_year_must_be_unique(): void
    {
        $university = University::factory()->create(['enabled_addons' => ['academic_records']]);
        $user = $this->authorizedUser($university);

        $response = $this->actingAs($user)->post(route('students.catalog.graduation-ceremonies.store'), [
            'year' => 2026,
            'sequence_number' => 17,
            'ceremony_date' => '2026-12-05',
        ]);

        $response->assertRedirect(route('students.catalog.index'));
        $this->assertDatabaseHas('graduation_ceremonies', ['year' => 2026, 'sequence_number' => 17]);

        $duplicate = $this->actingAs($user)->post(route('students.catalog.graduation-ceremonies.store'), [
            'year' => 2026,
            'sequence_number' => 18,
            'ceremony_date' => '2026-12-06',
        ]);

        $duplicate->assertSessionHasErrors('year');
    }

    public function test_catalog_requires_import_students_permission(): void
    {
        $university = University::factory()->create(['enabled_addons' => ['academic_records']]);
        Permission::firstOrCreate(['name' => 'view_students', 'guard_name' => 'web']);

        $user = User::factory()->create(['university_id' => $university->id]);
        $user->givePermissionTo(['view_students']);

        $this->actingAs($user)->get(route('students.catalog.index'))->assertForbidden();
    }
}
