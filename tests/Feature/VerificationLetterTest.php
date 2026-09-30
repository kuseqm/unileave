<?php

namespace Tests\Feature;

use App\Models\Campus;
use App\Models\LetterSignatory;
use App\Models\Program;
use App\Models\ReferenceFormat;
use App\Models\School;
use App\Models\Student;
use App\Models\University;
use App\Models\User;
use App\Models\VerificationLetter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class VerificationLetterTest extends TestCase
{
    use RefreshDatabase;

    private University $university;

    private LetterSignatory $dvca;

    private function setUpUniversity(int $capacity = 200, int $startVolume = 4, int $issued = 194): void
    {
        $this->university = University::factory()->create(['enabled_addons' => ['academic_records'], 'code' => 'SJUT']);

        $this->dvca = LetterSignatory::create([
            'university_id' => $this->university->id,
            'name' => 'Professor Valence M. Kessy Ndesendo, PhD',
            'title_en' => 'Deputy Vice Chancellor (Academic)',
            'unit_en' => "St John's University of Tanzania",
            'title_sw' => 'Naibu Makamu Mkuu wa Chuo (Taaluma)',
            'unit_sw' => "Chuo Kikuu cha St John's cha Tanzania",
            'office_en' => 'Office of the Deputy Vice Chancellor (Academic)',
        ]);

        ReferenceFormat::create([
            'university_id' => $this->university->id,
            'name' => 'Verification',
            'document_type' => 'verification',
            'format_pattern' => '{university_code}/SVAC/013/04/{volume}/{serial}',
            'serial_length' => 3,
            'serial_start' => 1,
            'current_serial' => $issued,
            'file_capacity' => $capacity,
            'volume_start' => $startVolume,
            'is_active' => true,
        ]);
    }

    private function user(array $permissions = ['view_students', 'prepare_verification_letters', 'manage_letter_signatories']): User
    {
        foreach (['view_students', 'prepare_verification_letters', 'manage_letter_signatories'] as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        $user = User::factory()->create(['university_id' => $this->university->id]);
        $user->givePermissionTo($permissions);

        return $user;
    }

    private function graduate(array $attributes = []): Student
    {
        $campus = Campus::factory()->create(['university_id' => $this->university->id]);
        $program = Program::factory()->create(['school_id' => School::factory()->create(['campus_id' => $campus->id])->id, 'name' => 'Bachelor of Business Administration (Finance)']);

        return Student::factory()->create($attributes + [
            'university_id' => $this->university->id,
            'program_id' => $program->id,
            'is_graduated' => true,
            'graduation_year' => 2011,
            'gender' => 'M',
            'registration_number' => '2008/1138',
            'last_name' => 'Bavuma',
            'first_name' => 'Nuru',
            'middle_name' => 'Daud',
            'full_name' => 'BAVUMA Nuru Daud',
        ]);
    }

    /** @return array<string, mixed> */
    private function payload(array $students, array $overrides = []): array
    {
        return array_merge([
            'template' => 'standard',
            'language' => 'en',
            'letter_date' => '2026-07-24',
            'recipient_title' => 'Human Resource Manager',
            'recipient_organisation' => 'TEF Consult',
            'recipient_address' => "P. O. BOX 33542, 23rd FLOOR\nMILLENIUM TOWER, MAKUMBUSHO",
            'recipient_city' => 'Dar es Salaam',
            'recipient_email' => 'info@tef.co.tz',
            'received_reference' => 'TEF/ACC VER/2026/003',
            'received_date' => '2026-07-06',
            'received_via' => 'letter',
            'documents' => ['certificate'],
            'number_type' => 'certificate',
            'signatory_id' => $this->dvca->id,
            'items' => collect($students)->map(fn (Student $student) => [
                'student_id' => $student->id,
                'name' => $student->formatted_name,
                'gender' => $student->gender,
                'number' => $student->registration_number,
                'programme' => $student->program->name,
                'graduation_year' => (string) $student->graduation_year,
            ])->all(),
        ], $overrides);
    }

    public function test_a_letter_takes_its_number_from_the_registry_format_and_moves_on(): void
    {
        $this->setUpUniversity();
        $student = $this->graduate();

        $response = $this->actingAs($this->user())->post(route('verification-letters.store'), $this->payload([$student]));

        $letter = VerificationLetter::firstOrFail();
        $response->assertRedirect(route('verification-letters.show', $letter->id));
        $this->assertSame('SJUT/SVAC/013/04/VOL IV/195', $letter->letter_number);
        $this->assertCount(1, $letter->items);
        $this->assertSame(195, (int) ReferenceFormat::first()->current_serial);

        $this->actingAs($this->user())->post(route('verification-letters.store'), $this->payload([$student]));
        $this->assertSame('SJUT/SVAC/013/04/VOL IV/196', VerificationLetter::latest('id')->first()->letter_number);
    }

    public function test_a_full_volume_rolls_over_to_the_next_one_and_starts_again_at_one(): void
    {
        $this->setUpUniversity(capacity: 200, startVolume: 4, issued: 199);
        $student = $this->graduate();

        $this->actingAs($this->user())->post(route('verification-letters.store'), $this->payload([$student]));
        $this->actingAs($this->user())->post(route('verification-letters.store'), $this->payload([$student]));

        $numbers = VerificationLetter::orderBy('id')->pluck('letter_number')->all();
        $this->assertSame(['SJUT/SVAC/013/04/VOL IV/200', 'SJUT/SVAC/013/04/VOL V/001'], $numbers);
    }

    public function test_no_letter_is_made_and_no_number_used_when_the_registry_has_no_format(): void
    {
        $this->setUpUniversity();
        ReferenceFormat::query()->delete();
        $student = $this->graduate();

        $this->actingAs($this->user())->post(route('verification-letters.store'), $this->payload([$student]))
            ->assertSessionHas('error');

        $this->assertSame(0, VerificationLetter::count());
    }

    public function test_a_preview_shows_the_draft_without_using_up_a_number(): void
    {
        $this->setUpUniversity();
        $student = $this->graduate();

        $this->actingAs($this->user())->post(route('verification-letters.preview'), $this->payload([$student]))
            ->assertOk()
            ->assertSee('DRAFT')
            ->assertSee('BAVUMA, Nuru Daud');

        $this->assertSame(194, (int) ReferenceFormat::first()->current_serial);
        $this->assertSame(0, VerificationLetter::count());
    }

    public function test_the_english_letter_follows_the_sample_wording_for_one_graduate_and_for_several(): void
    {
        $this->setUpUniversity();
        $one = $this->graduate();
        $two = $this->graduate(['registration_number' => '2015/0374', 'last_name' => 'Godwin', 'first_name' => 'Jackline', 'middle_name' => null, 'gender' => 'F']);

        $single = $this->actingAs($this->user())->post(route('verification-letters.preview'), $this->payload([$one]))->getContent();
        $this->assertStringContainsString('HUMAN RESOURCE MANAGER', $single);
        $this->assertStringContainsString('This refers to your correspondence letter bearing reference number', $single);
        $this->assertStringContainsString("requests confirmation of the certificate for a graduate of St John's University of Tanzania", $single);
        $this->assertStringContainsString('documents presented to you by our graduate are genuine. He completed studies', $single);
        $this->assertStringContainsString('I confirm authenticity of the presented certificate.', $single);
        $this->assertStringContainsString('Office of the Deputy Vice Chancellor (Academic)', $single);
        $this->assertStringContainsString('Cert NO', $single);

        $several = $this->actingAs($this->user())->post(route('verification-letters.preview'), $this->payload([$one, $two], ['documents' => ['certificate', 'transcript'], 'received_via' => 'email']))->getContent();
        $this->assertStringContainsString('your email correspondence, including the attached letter', $several);
        $this->assertStringContainsString('confirmation of the certificates and transcripts for graduates of', $several);
        $this->assertStringContainsString('our graduates are genuine. They completed studies', $several);
        $this->assertStringContainsString('presented certificates and Transcripts.', $several);
    }

    public function test_the_swahili_letter_names_a_single_graduate_and_counts_several(): void
    {
        $this->setUpUniversity();
        $one = $this->graduate(['first_name' => 'Agness', 'middle_name' => 'Ernest', 'last_name' => 'Mang\'ana']);
        $payload = $this->payload([$one], ['language' => 'sw', 'received_via' => 'letter', 'received_reference' => 'CGA. 144/659/01\'C\'/388', 'number_type' => 'registration']);
        $payload['items'][0]['programme'] = 'Stashahada ya Usimamizi wa Biashara – Uhasibu';

        $single = $this->actingAs($this->user())->post(route('verification-letters.preview'), $payload)->getContent();
        $this->assertStringContainsString('YAH: UHAKIKI WA CHETI CHA TAALUMA', $single);
        $this->assertStringContainsString('ikiomba kupatiwa taarifa za uhakiki wa cheti cha mhitimu wa Chuo hiki, ndugu Agness Ernest MANG', $single);
        $this->assertStringContainsString('vigezo vyote vya kutunukiwa Stashahada ya Usimamizi wa Biashara', $single);
        $this->assertStringContainsString('NA YA USAJILI', $single);
        $this->assertStringContainsString('Nashukuru kwa ushirikiano.', $single);
        $this->assertStringContainsString('Naibu Makamu Mkuu wa Chuo (Taaluma)', $single);

        $students = collect(range(1, 5))->map(fn ($i) => $this->graduate(['registration_number' => "2016/000{$i}"]));
        $several = $this->actingAs($this->user())->post(route('verification-letters.preview'), $this->payload($students->all(), ['language' => 'sw']))->getContent();
        $this->assertStringContainsString('YAH: UHAKIKI WA VYETI', $several);
        $this->assertStringContainsString('uhakiki wa vyeti vitano (5) vya wahitimu wa Chuo hiki', $several);
        $this->assertStringContainsString('kwa ajili ya wahitimu wetu', $several);
    }

    public function test_the_dataflow_letter_lists_barcodes_mode_duration_and_period_of_study(): void
    {
        $this->setUpUniversity();
        $nurse = $this->graduate(['registration_number' => '2015/1059']);
        $payload = $this->payload([$nurse], [
            'template' => 'dataflow',
            'recipient_title' => 'Senior Associate, Case Validation',
            'recipient_organisation' => 'DataFlow Group',
            'received_via' => 'email',
            'received_reference' => null,
            'received_date' => '2026-06-23',
            'mode_of_study' => 'Full Time',
        ]);
        $payload['items'][0] += ['barcode' => 'S003-260608-274F9-E64', 'study_from' => '2015/2016', 'study_to' => '2018/2019', 'duration_years' => 4];

        $html = $this->actingAs($this->user())->post(route('verification-letters.preview'), $payload)->getContent();

        $this->assertStringContainsString('RE: VERIFICATION REQUEST', $html);
        $this->assertStringContainsString('S003-260608-274F9-E64', $html);
        $this->assertStringContainsString('Mode of study was <strong>Full Time</strong>', $html);
        $this->assertStringContainsString('Duration of the programme is four (4) years.', $html);
        $this->assertStringContainsString('from 2015/2016 to 2018/2019, four academic years', $html);
        $this->assertStringContainsString('BARCODE', $html);
    }

    public function test_a_dataflow_letter_needs_a_barcode_for_every_graduate(): void
    {
        $this->setUpUniversity();
        $payload = $this->payload([$this->graduate()], ['template' => 'dataflow', 'recipient_organisation' => 'DataFlow Group']);

        $this->actingAs($this->user())->post(route('verification-letters.store'), $payload)
            ->assertSessionHasErrors('items.0.barcode');
    }

    public function test_graduates_and_signatories_must_belong_to_the_same_university(): void
    {
        $this->setUpUniversity();
        $foreign = University::factory()->create();
        $foreignStudent = Student::factory()->create(['university_id' => $foreign->id, 'is_graduated' => true]);
        $foreignSignatory = LetterSignatory::create(['university_id' => $foreign->id, 'name' => 'X', 'title_en' => 'X', 'title_sw' => 'X', 'office_en' => 'X']);

        $this->actingAs($this->user())->post(route('verification-letters.store'), $this->payload([$foreignStudent]))
            ->assertSessionHasErrors('items.0.student_id');

        $this->actingAs($this->user())->post(route('verification-letters.store'), $this->payload([$this->graduate()], ['signatory_id' => $foreignSignatory->id]))
            ->assertSessionHasErrors('signatory_id');
    }

    public function test_only_people_who_may_generate_letters_can_write_one_and_viewers_can_read_them(): void
    {
        $this->setUpUniversity();
        $student = $this->graduate();
        $viewer = $this->user(['view_students']);

        $this->actingAs($viewer)->get(route('verification-letters.create'))->assertForbidden();
        $this->actingAs($viewer)->post(route('verification-letters.store'), $this->payload([$student]))->assertForbidden();

        $this->actingAs($this->user())->post(route('verification-letters.store'), $this->payload([$student]));
        $letter = VerificationLetter::firstOrFail();

        $this->actingAs($viewer)->get(route('verification-letters.index'))->assertOk()->assertSee($letter->letter_number);
        $this->actingAs($viewer)->get(route('verification-letters.show', $letter->id))->assertOk();
        $this->actingAs($viewer)->get(route('verification-letters.letter', $letter->id))->assertOk()->assertSee('Our Ref:')->assertSee($letter->letter_number);

        $otherUniversity = University::factory()->create(['enabled_addons' => ['academic_records']]);
        $outsider = User::factory()->create(['university_id' => $otherUniversity->id]);
        $outsider->givePermissionTo('view_students');
        $this->actingAs($outsider)->get(route('verification-letters.show', $letter->id))->assertForbidden();
    }

    public function test_the_graduate_search_finds_by_name_and_number_and_stays_in_the_university(): void
    {
        $this->setUpUniversity();
        $this->graduate();
        Student::factory()->create(['university_id' => University::factory()->create()->id, 'is_graduated' => true, 'full_name' => 'BAVUMA Someone Else']);

        $byName = $this->actingAs($this->user())->getJson(route('verification-letters.graduates', ['q' => 'bavuma']))->assertOk()->json();
        $this->assertCount(1, $byName);
        $this->assertSame('2008/1138', $byName[0]['certificate_number']);

        $this->actingAs($this->user())->getJson(route('verification-letters.graduates', ['q' => '2008/11']))->assertOk()->assertJsonCount(1);
        $this->actingAs($this->user())->getJson(route('verification-letters.graduates', ['q' => 'b']))->assertStatus(422);
    }

    public function test_the_pdf_downloads_and_the_public_page_confirms_the_letter(): void
    {
        $this->setUpUniversity();
        $student = $this->graduate();
        $this->actingAs($this->user())->post(route('verification-letters.store'), $this->payload([$student]));
        $letter = VerificationLetter::firstOrFail();

        $this->actingAs($this->user())->get(route('verification-letters.download-pdf', $letter->id))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');

        auth()->logout();
        $this->get(route('verify.show', $letter->secure_code))
            ->assertOk()
            ->assertSee($letter->letter_number)
            ->assertSee('TEF Consult')
            ->assertSee('BAVUMA, Nuru Daud');

        $this->assertNotNull($letter->fresh()->verified_at);
    }

    public function test_only_the_examinations_officer_prepares_letters_and_only_an_administrator_manages_signatories(): void
    {
        $this->setUpUniversity();
        $student = $this->graduate();
        Permission::firstOrCreate(['name' => 'generate_graduation_letters', 'guard_name' => 'web']);

        // Someone who may write the alumni letters (e.g. Registry) is not thereby allowed to prepare these.
        $registry = $this->user(['view_students', 'generate_graduation_letters']);
        $this->actingAs($registry)->get(route('verification-letters.create'))->assertForbidden();
        $this->actingAs($registry)->post(route('verification-letters.store'), $this->payload([$student]))->assertForbidden();
        $this->actingAs($registry)->get(route('verification-letters.index'))->assertOk()->assertDontSee('New Letter');

        $examinationsOfficer = $this->user(['view_students', 'prepare_verification_letters']);
        $this->actingAs($examinationsOfficer)->get(route('verification-letters.create'))->assertOk();
        $this->actingAs($examinationsOfficer)->post(route('verification-letters.store'), $this->payload([$student]))->assertRedirect();

        // Choosing from the signatories is theirs; setting up who may sign is not.
        $this->actingAs($examinationsOfficer)->get(route('verification-letters.signatories'))->assertForbidden();
        $this->actingAs($examinationsOfficer)->post(route('verification-letters.signatories.toggle', $this->dvca->id))->assertForbidden();
        $this->assertTrue($this->dvca->fresh()->is_active);
    }

    public function test_our_ref_is_this_letters_number_and_your_ref_is_the_requesters_and_no_verify_link_text_is_printed(): void
    {
        $this->setUpUniversity();
        $student = $this->graduate();
        $officer = $this->user();

        $english = $this->actingAs($officer)->post(route('verification-letters.preview'), $this->payload([$student]))->getContent();
        $this->assertStringContainsString('Our Ref:', $english);
        $this->assertStringContainsString('Your Ref:</strong>', $english);
        $this->assertStringContainsString('TEF/ACC VER/2026/003', $english);
        $this->assertStringContainsString('Letter Unique Code:', $english);
        $this->assertStringNotContainsString('or visit', $english);
        $this->assertStringNotContainsString('/verify', $english);

        $swahili = $this->actingAs($officer)->post(route('verification-letters.preview'), $this->payload([$student], ['language' => 'sw', 'received_reference' => null]))->getContent();
        $this->assertStringContainsString('Kumb. Yetu:', $swahili);
        $this->assertStringContainsString('Kumb. Yako:</strong>', $swahili);
        $this->assertStringContainsString('Msimbo wa Barua:', $swahili);
    }

    public function test_only_the_numbered_line_of_a_paragraph_is_indented_and_text_is_justified(): void
    {
        $this->setUpUniversity();
        $html = $this->actingAs($this->user())->post(route('verification-letters.preview'), $this->payload([$this->graduate()]))->getContent();

        $this->assertStringContainsString('text-align: justify;">2.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;I hereby verify', $html);
        $this->assertStringNotContainsString('<td style="width: 34px; vertical-align: top; padding: 0;">2.</td>', $html);
    }

    public function test_the_graduate_page_offers_a_verification_letter_that_starts_with_that_graduate(): void
    {
        $this->setUpUniversity();
        $student = $this->graduate();
        $officer = $this->user(['view_students', 'prepare_verification_letters']);

        $this->actingAs($officer)->get(route('students.show', $student->id))
            ->assertOk()
            ->assertSee('Verification Letter')
            ->assertSee(route('verification-letters.create', ['student' => $student->id]), false);

        $this->actingAs($officer)->get(route('verification-letters.create', ['student' => $student->id]))
            ->assertOk()
            ->assertSee('BAVUMA, Nuru Daud');

        $viewer = $this->user(['view_students']);
        $this->actingAs($viewer)->get(route('students.show', $student->id))->assertOk()->assertDontSee('Confirms the certificate and transcript');
    }

    public function test_the_old_graduation_confirmation_now_leads_to_the_verification_letter(): void
    {
        $this->setUpUniversity();
        $student = $this->graduate();
        Permission::firstOrCreate(['name' => 'generate_graduation_letters', 'guard_name' => 'web']);
        $user = $this->user(['view_students', 'generate_graduation_letters']);

        $this->actingAs($user)->get(route('students.compose-letter', ['student' => $student->id, 'type' => 'confirmation']))
            ->assertRedirect(route('verification-letters.create', ['student' => $student->id]));

        $this->actingAs($user)->get(route('students.compose-letter', ['student' => $student->id]))
            ->assertOk()
            ->assertSee('COMPOSE GRADUATE LETTER')
            ->assertDontSee('COMPOSE ALUMNI LETTER');
    }

    public function test_an_extra_paragraph_is_slotted_after_the_table_and_the_closing_lines_are_renumbered(): void
    {
        $this->setUpUniversity();
        $student = $this->graduate();

        $html = $this->actingAs($this->user())
            ->post(route('verification-letters.preview'), $this->payload([$student], ['extra_text' => "The graduate also completed a certificate in ICT.\nHe was a class representative."]))
            ->getContent();

        $this->assertStringContainsString('The graduate also completed a certificate in ICT.', $html);
        $this->assertStringContainsString('He was a class representative.', $html);
        $this->assertLessThan(strpos($html, 'I trust that, this Information will be helpful to you.'), strpos($html, 'The graduate also completed'));

        preg_match_all('/text-align: justify;">(\d)\.&nbsp;/', $html, $numbers);
        $this->assertSame(['2', '3', '4', '5'], $numbers[1]);
    }

    public function test_a_whole_letter_can_be_typed_when_no_sample_fits(): void
    {
        $this->setUpUniversity();

        $payload = [
            'template' => 'custom',
            'language' => 'en',
            'letter_date' => '2026-09-24',
            'recipient_organisation' => 'Ministry of Education',
            'recipient_title' => 'The Permanent Secretary',
            'subject' => 'RE: REPLACEMENT OF LOST CERTIFICATE',
            'body_text' => "We write about your request of 3rd September 2026.\nThe University has reviewed the case.\nA replacement will be issued once the police report is received.",
            'signatory_id' => $this->dvca->id,
        ];

        $preview = $this->actingAs($this->user())->post(route('verification-letters.preview'), $payload)->getContent();
        $this->assertStringContainsString('RE: REPLACEMENT OF LOST CERTIFICATE', $preview);
        $this->assertStringContainsString('We write about your request of 3rd September 2026.', $preview);
        $this->assertStringContainsString('2.', $preview);
        $this->assertStringContainsString('A replacement will be issued', $preview);
        $this->assertStringNotContainsString('verification-table', str_replace('.verification-table', '', $preview));

        $this->actingAs($this->user())->post(route('verification-letters.store'), $payload)->assertRedirect();
        $letter = VerificationLetter::firstOrFail();
        $this->assertSame('custom', $letter->template);
        $this->assertCount(0, $letter->items);
        $this->assertSame('SJUT/SVAC/013/04/VOL IV/195', $letter->letter_number);

        $this->actingAs($this->user())->get(route('verification-letters.letter', $letter->id))->assertOk()->assertSee('Our Ref:')->assertSee($letter->letter_number);
    }

    public function test_a_whole_letter_needs_a_subject_and_text(): void
    {
        $this->setUpUniversity();

        $this->actingAs($this->user())->post(route('verification-letters.store'), [
            'template' => 'custom', 'language' => 'en', 'letter_date' => '2026-09-24',
            'recipient_organisation' => 'Ministry', 'signatory_id' => $this->dvca->id,
        ])->assertSessionHasErrors(['subject', 'body_text']);
    }

    public function test_signatories_can_be_added_and_hidden_from_the_choice(): void
    {
        $this->setUpUniversity();
        $user = $this->user();

        $this->actingAs($user)->post(route('verification-letters.signatories.store'), [
            'name' => 'Dr Laurent Kaburire, PhD',
            'title_en' => 'Director of Undergraduate Studies (DUS)',
            'title_sw' => 'Mkurugenzi',
            'unit_sw' => 'Kurugenzi ya Taaluma za Awali (DUS)',
            'office_en' => 'Directorate of Undergraduate Studies (DUS)',
        ])->assertRedirect(route('verification-letters.signatories'));

        $dus = LetterSignatory::where('name', 'Dr Laurent Kaburire, PhD')->firstOrFail();
        $this->assertSame(2, $this->actingAs($user)->get(route('verification-letters.create'))->viewData('signatories')->count());

        $this->actingAs($user)->post(route('verification-letters.signatories.toggle', $dus->id));
        $this->assertSame(1, $this->actingAs($user)->get(route('verification-letters.create'))->viewData('signatories')->count());
    }
}
