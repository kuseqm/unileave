<?php

namespace Tests\Feature;

use App\Models\Campus;
use App\Models\GraduationLetter;
use App\Models\Program;
use App\Models\School;
use App\Models\Student;
use App\Models\University;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GraduateVerificationAndNamesTest extends TestCase
{
    use RefreshDatabase;

    public function test_surname_is_stored_and_displayed_in_capitals_however_it_was_typed(): void
    {
        $student = Student::factory()->create([
            'university_id' => University::factory()->create()->id,
            'last_name' => 'malecela',
            'first_name' => 'Nyagalu',
            'middle_name' => 'Michael',
        ]);

        $this->assertSame('MALECELA', $student->fresh()->last_name);
        $this->assertSame('MALECELA, Nyagalu Michael', $student->fresh()->formatted_name);

        $student->update(['last_name' => 'Mwanga-Ndege']);
        $this->assertSame('MWANGA-NDEGE', $student->fresh()->last_name);
    }

    public function test_public_verification_shows_what_a_verifier_compares_and_withholds_the_rest(): void
    {
        $university = University::factory()->create();
        $campus = Campus::factory()->create(['university_id' => $university->id]);
        $program = Program::factory()->create([
            'school_id' => School::factory()->create(['campus_id' => $campus->id])->id,
            'name' => 'Bachelor of Science with Education',
        ]);
        $student = Student::factory()->create([
            'university_id' => $university->id,
            'campus_id' => $campus->id,
            'program_id' => $program->id,
            'registration_number' => 'ZQX-REG-9917',
            'last_name' => 'Kusekwa',
            'first_name' => 'Martin',
            'gender' => 'M',
            'graduation_year' => 2010,
            'certificate_barcode' => 'CERT-778899',
            'classification' => 'Upper Second Class Honours',
            'gpa' => '3.85',
            'is_graduated' => true,
        ]);
        $letter = GraduationLetter::create([
            'university_id' => $university->id,
            'student_id' => $student->id,
            'type' => 'confirmation',
            'letter_number' => 'SJUT/ALM/2026/0001',
            'secure_code' => 'SMU-ALM-TESTCODE',
            'generated_by' => User::factory()->create(['university_id' => $university->id])->id,
        ]);

        $response = $this->get(route('verify.show', $letter->secure_code));

        $response->assertOk()
            ->assertSee('KUSEKWA, Martin')
            ->assertSee('Bachelor of Science with Education')
            ->assertSee('2010')
            ->assertSee('CERT-778899')
            ->assertSee('SJUT/ALM/2026/0001')
            ->assertDontSee('ZQX-REG-9917')
            ->assertDontSee('Upper Second Class Honours')
            ->assertDontSee('3.85');
    }
}
