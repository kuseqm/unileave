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
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class GraduateStatisticsAndBookCoverTest extends TestCase
{
    use RefreshDatabase;

    private function userWith(University $university, array $permissions): User
    {
        foreach (['view_students', 'import_students'] as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        $user = User::factory()->create(['university_id' => $university->id]);
        $user->givePermissionTo($permissions);

        return $user;
    }

    public function test_statistics_break_graduates_down_by_year_faculty_programme_and_campus(): void
    {
        $university = University::factory()->create(['enabled_addons' => ['academic_records']]);
        $mazengo = Campus::factory()->create(['university_id' => $university->id, 'name' => 'Chief Mazengo Campus']);
        $msalato = Campus::factory()->create(['university_id' => $university->id, 'name' => 'Msalato Campus']);
        $school = School::factory()->create(['campus_id' => $mazengo->id, 'code' => 'FAHE']);
        $theology = Program::factory()->create(['school_id' => $school->id, 'name' => 'Bachelor of Arts in Theology']);
        $education = Program::factory()->create(['school_id' => $school->id, 'name' => 'Bachelor of Arts with Education']);
        GraduationCeremony::factory()->create(['year' => 2010]);
        GraduationCeremony::factory()->create(['year' => 2011]);

        $make = function (array $attributes, int $count = 1) use ($university) {
            return Student::factory()->count($count)->create($attributes + [
                'university_id' => $university->id, 'is_graduated' => true,
            ])->each(function (Student $student) use ($university) {
                StudentGraduation::create([
                    'student_id' => $student->id,
                    'university_id' => $university->id,
                    'campus_id' => $student->campus_id,
                    'program_id' => $student->program_id,
                    'graduation_year' => $student->graduation_year,
                    'gender' => $student->gender,
                    'last_name' => $student->last_name,
                    'first_name' => $student->first_name,
                    'is_graduated' => true,
                ]);
            });
        };
        $make(['program_id' => $theology->id, 'campus_id' => $mazengo->id, 'graduation_year' => 2010, 'gender' => 'M'], 3);
        $make(['program_id' => $theology->id, 'campus_id' => $mazengo->id, 'graduation_year' => 2010, 'gender' => 'F'], 2);
        $make(['program_id' => $education->id, 'campus_id' => $msalato->id, 'graduation_year' => 2011, 'gender' => 'F'], 4);
        $make(['program_id' => $education->id, 'campus_id' => $msalato->id, 'graduation_year' => 2011, 'gender' => null], 1);
        Student::factory()->create(['university_id' => $university->id, 'is_graduated' => false, 'graduation_year' => 2011, 'gender' => 'M', 'program_id' => $education->id]);
        Student::factory()->count(7)->create(['university_id' => University::factory()->create()->id, 'is_graduated' => true, 'graduation_year' => 2010, 'gender' => 'M']);

        $user = $this->userWith($university, ['view_students']);

        $response = $this->actingAs($user)->get(route('students.statistics'))->assertOk();

        $totals = $response->viewData('totals');
        $this->assertSame([10, 3, 6], [(int) $totals->total, (int) $totals->males, (int) $totals->females]);

        $byYear = $response->viewData('byYear');
        $this->assertSame([2011, 2010], $byYear->pluck('label')->map(fn ($year) => (int) $year)->all());
        $this->assertSame([5, 5], $byYear->pluck('total')->map(fn ($total) => (int) $total)->all());

        $this->assertCount(1, $response->viewData('byFaculty'));
        $this->assertSame(['Bachelor of Arts in Theology', 'Bachelor of Arts with Education'], $response->viewData('byProgramme')->pluck('label')->all());
        $this->assertEqualsCanonicalizing(['Chief Mazengo Campus', 'Msalato Campus'], $response->viewData('byCampus')->pluck('label')->all());

        $matrix = collect($response->viewData('matrix'))->keyBy('label');
        $this->assertSame([2011 => 5], $matrix['Bachelor of Arts with Education']['cells']);

        $females = $this->actingAs($user)->get(route('students.statistics', ['metric' => 'females']))->viewData('matrix');
        $this->assertSame(4, collect($females)->firstWhere('label', 'Bachelor of Arts with Education')['total']);

        $onlyMsalato = $this->actingAs($user)->get(route('students.statistics', ['campus_id' => $msalato->id]))->viewData('totals');
        $this->assertSame(5, (int) $onlyMsalato->total);
    }

    public function test_statistics_need_permission_to_view_students(): void
    {
        $university = University::factory()->create(['enabled_addons' => ['academic_records']]);
        $user = $this->userWith($university, []);

        $this->actingAs($user)->get(route('students.statistics'))->assertForbidden();
    }

    public function test_a_book_cover_can_be_uploaded_replaced_and_seen_by_graduate_viewers(): void
    {
        Storage::fake('local');

        $university = University::factory()->create(['enabled_addons' => ['academic_records']]);
        $ceremony = GraduationCeremony::factory()->create(['year' => 2010]);
        $editor = $this->userWith($university, ['view_students', 'import_students']);
        $viewer = $this->userWith($university, ['view_students']);

        $this->actingAs($editor)->post(route('students.catalog.graduation-ceremonies.book-cover.update', $ceremony->id), [
            'book_cover' => UploadedFile::fake()->image('book.jpg', 300, 400),
        ])->assertRedirect(route('students.catalog.index'));

        $first = $ceremony->fresh()->book_cover_path;
        Storage::disk('local')->assertExists($first);

        $this->actingAs($viewer)->get(route('students.book-cover', $ceremony->id))->assertOk();

        $this->actingAs($editor)->post(route('students.catalog.graduation-ceremonies.book-cover.update', $ceremony->id), [
            'book_cover' => UploadedFile::fake()->image('new.png', 300, 400),
        ]);
        Storage::disk('local')->assertMissing($first);
        Storage::disk('local')->assertExists($ceremony->fresh()->book_cover_path);

        $graduate = Student::factory()->create(['university_id' => $university->id, 'is_graduated' => true, 'graduation_year' => 2010]);
        $this->actingAs($viewer)->get(route('students.show', $graduate->id))->assertOk()->assertSee('2010 graduation book');
    }

    public function test_a_book_cover_must_be_a_small_image_and_needs_the_import_permission(): void
    {
        Storage::fake('local');

        $university = University::factory()->create(['enabled_addons' => ['academic_records']]);
        $ceremony = GraduationCeremony::factory()->create(['year' => 2010]);
        $editor = $this->userWith($university, ['view_students', 'import_students']);
        $viewer = $this->userWith($university, ['view_students']);

        $this->actingAs($editor)->post(route('students.catalog.graduation-ceremonies.book-cover.update', $ceremony->id), [
            'book_cover' => UploadedFile::fake()->create('notes.pdf', 100, 'application/pdf'),
        ])->assertSessionHasErrors('book_cover');

        $this->actingAs($editor)->post(route('students.catalog.graduation-ceremonies.book-cover.update', $ceremony->id), [
            'book_cover' => UploadedFile::fake()->image('huge.jpg')->size(6000),
        ])->assertSessionHasErrors('book_cover');

        $this->actingAs($viewer)->post(route('students.catalog.graduation-ceremonies.book-cover.update', $ceremony->id), [
            'book_cover' => UploadedFile::fake()->image('ok.jpg'),
        ])->assertForbidden();

        $this->actingAs($viewer)->get(route('students.book-cover', $ceremony->id))->assertNotFound();
        $this->assertNull($ceremony->fresh()->book_cover_path);
    }
}
