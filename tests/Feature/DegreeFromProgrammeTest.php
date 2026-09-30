<?php

namespace Tests\Feature;

use App\Models\Campus;
use App\Models\Program;
use App\Models\School;
use App\Models\Student;
use App\Models\University;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DegreeFromProgrammeTest extends TestCase
{
    use RefreshDatabase;

    private function school(University $university): School
    {
        return School::factory()->create([
            'campus_id' => Campus::factory()->create(['university_id' => $university->id])->id,
        ]);
    }

    public function test_a_subject_combination_earns_the_base_degree_title_and_abbreviation(): void
    {
        $university = University::factory()->create();
        $school = $this->school($university);
        Program::factory()->create(['school_id' => $school->id, 'name' => 'Bachelor of Science with Education', 'code' => 'BScEd']);
        $combination = Program::factory()->create([
            'school_id' => $school->id,
            'name' => 'Bachelor of Science with Education (Physics - Mathematics)',
            'code' => 'SJ0022',
        ]);

        $this->assertSame('Bachelor of Science with Education', $combination->degree_name);
        $this->assertSame('BScEd', $combination->degree_abbreviation);
    }

    public function test_a_plain_programme_is_its_own_degree_and_internal_codes_are_not_abbreviations(): void
    {
        $university = University::factory()->create();
        $school = $this->school($university);
        $plain = Program::factory()->create(['school_id' => $school->id, 'name' => 'Bachelor of Pharmacy', 'code' => 'BPharm']);
        $internal = Program::factory()->create(['school_id' => $school->id, 'name' => 'Some Odd Programme (Variant)', 'code' => 'SJ00404']);

        $this->assertSame('Bachelor of Pharmacy', $plain->degree_name);
        $this->assertSame('BPharm', $plain->degree_abbreviation);
        $this->assertSame('Some Odd Programme (Variant)', $internal->degree_name);
        $this->assertNull($internal->degree_abbreviation);
    }

    public function test_a_base_programme_from_another_university_is_not_used(): void
    {
        $university = University::factory()->create();
        $other = University::factory()->create();
        Program::factory()->create(['school_id' => $this->school($other)->id, 'name' => 'Bachelor of Arts', 'code' => 'BA']);
        $mine = Program::factory()->create(['school_id' => $this->school($university)->id, 'name' => 'Bachelor of Arts (Drama)', 'code' => 'BAD']);

        $this->assertSame('Bachelor of Arts (Drama)', $mine->degree_name);
    }

    public function test_a_graduate_gets_the_degree_from_their_programme_unless_their_own_is_typed_in(): void
    {
        $university = University::factory()->create();
        $school = $this->school($university);
        Program::factory()->create(['school_id' => $school->id, 'name' => 'Bachelor of Business Administration', 'code' => 'BBA']);
        $combination = Program::factory()->create([
            'school_id' => $school->id,
            'name' => 'Bachelor of Business Administration (Marketing Management)',
            'code' => 'SJ00401',
        ]);

        $graduate = Student::factory()->create([
            'university_id' => $university->id,
            'program_id' => $combination->id,
            'degree_name' => null,
            'degree_abbreviation' => null,
        ]);

        $this->assertSame('Bachelor of Business Administration - [BBA]', $graduate->degree_with_abbreviation);

        $graduate->update(['degree_name' => 'Bachelor of Commerce', 'degree_abbreviation' => 'BCom']);
        $this->assertSame('Bachelor of Commerce - [BCom]', $graduate->fresh()->degree_with_abbreviation);
    }
}
