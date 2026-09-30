<?php

namespace App\Http\Controllers;

use App\Helpers\DateToWordsHelper;
use App\Models\DocumentVerificationLog;
use App\Models\GraduationLetter;
use App\Models\ReferenceFormat;
use App\Models\Student;
use App\Traits\GeneratesQrCodeTrait;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class GraduationLetterController extends Controller
{
    use GeneratesQrCodeTrait;

    /**
     * Every single-student alumni letter type identified from real SJUT
     * samples - see [[project_gmsa_graduation_verification_slice]]. Each
     * entry drives the compose form (which fields to show, what to
     * pre-fill) and the rendered letter (subject/recipient/office/
     * numbering). `recipient_heading` is a fixed "TO WHOM IT MAY CONCERN"-
     * style stand-in for an addressee; `requires_recipient_input` means
     * the opposite - staff must type a real external address (no
     * sensible default exists, e.g. Introduction letters naming a
     * specific institution).
     */
    private const LETTER_TYPES = [
        'confirmation' => [
            'label' => 'Graduation Confirmation Letter',
            'title' => 'Graduation Confirmation Letter',
            'numbered' => true,
            'office_title' => 'Directorate of Undergraduate Studies (DUS)',
            'signatory_title' => 'Directorate of Undergraduate Studies',
            'subject' => 'GRADUATION CONFIRMATION LETTER',
            'recipient_heading' => null,
            'requires_recipient_input' => false,
        ],
        'introduction' => [
            'label' => 'General Introduction Letter',
            'title' => 'Introduction Letter',
            'numbered' => true,
            'office_title' => 'Office of the Deputy Vice Chancellor (Academic)',
            'signatory_title' => 'Deputy Vice Chancellor (Academic)',
            'subject' => null,
            'recipient_heading' => null,
            'requires_recipient_input' => true,
        ],
        'english_proficiency' => [
            'label' => 'English Proficiency Letter',
            'title' => 'English Proficiency Letter',
            'numbered' => true,
            'office_title' => 'Office of the Deputy Vice Chancellor (Academic)',
            'signatory_title' => 'Deputy Vice Chancellor (Academic)',
            'subject' => null,
            'recipient_heading' => 'TO WHOM IT MAY CONCERN',
            'requires_recipient_input' => false,
        ],
        'recommendation' => [
            'label' => 'General Recommendation Letter',
            'title' => 'Recommendation Letter',
            'numbered' => true,
            'office_title' => 'Directorate of Undergraduate Studies',
            'signatory_title' => 'Dean',
            'subject' => null,
            'recipient_heading' => 'TO WHOM IT MAY CONCERN',
            'requires_recipient_input' => false,
        ],
    ];

    public static function letterTypes(): array
    {
        return self::LETTER_TYPES;
    }

    /**
     * Show the compose form - type selector plus a body pre-filled with
     * the facts we already know, which staff can edit before submitting
     * (same "auto-fill the facts, let a human write the purpose" pattern
     * as the existing Leave application letter wizard).
     */
    public function create(Student $student, Request $request)
    {
        abort_unless($student->is_graduated, 422, 'This student is not recorded as graduated.');

        $type = $request->get('type', 'introduction');
        abort_unless(array_key_exists($type, self::LETTER_TYPES), 404);

        // Confirming a certificate / transcript is now done with a Verification
        // Letter (it carries the table of graduates); this page is for the rest.
        if ($type === 'confirmation') {
            return redirect()->route('verification-letters.create', ['student' => $student->id]);
        }

        $config = self::LETTER_TYPES[$type];

        return view('students.compose-letter', [
            'student' => $student,
            'type' => $type,
            'config' => $config,
            'letterTypes' => Arr::except(self::LETTER_TYPES, 'confirmation'),
            'defaultBody' => $this->defaultBodyContent($student, $type),
            'defaultSubject' => $this->defaultSubject($student, $type),
        ]);
    }

    public function store(Student $student, Request $request)
    {
        abort_unless($student->is_graduated, 422, 'This student is not recorded as graduated.');

        $type = $request->input('type');
        abort_unless(array_key_exists($type, self::LETTER_TYPES), 404);
        $config = self::LETTER_TYPES[$type];

        $validated = $request->validate([
            'type' => 'required|string',
            'subject' => 'nullable|string|max:255',
            'body_content' => 'required|string',
            'recipient_name' => $config['requires_recipient_input'] ? 'required|string|max:255' : 'nullable|string|max:255',
            'recipient_address' => $config['requires_recipient_input'] ? 'required|string' : 'nullable|string',
            'signatory_title' => 'nullable|string|max:255',
        ]);

        $letter = GraduationLetter::create([
            'university_id' => $student->university_id,
            'student_id' => $student->id,
            'type' => $type,
            'recipient_name' => $validated['recipient_name'] ?? null,
            'recipient_address' => $config['requires_recipient_input']
                ? $validated['recipient_address']
                : ($config['recipient_heading'] ?? $this->defaultRecipientAddress($student)),
            'subject' => $validated['subject'] ?: $config['subject'],
            'office_title' => $config['office_title'],
            'signatory_title' => $validated['signatory_title'] ?: $config['signatory_title'],
            'body_content' => $validated['body_content'],
            'letter_number' => $this->resolveLetterNumber($student, $type),
            'secure_code' => 'SMU-ALM-'.Str::upper(Str::random(8)),
            'generated_by' => auth()->id(),
        ]);

        return redirect()->route('graduation-letters.show', $letter->id)
            ->with('success', ucfirst(str_replace('_', ' ', $type)).' letter generated.');
    }

    /**
     * Quick one-click path for the Confirmation letter only (the one type
     * with no free-text content to write) - kept for the existing
     * "Generate Confirmation Letter" button on the student page.
     */
    public function generate(Student $student)
    {
        abort_unless($student->is_graduated, 422, 'This student is not recorded as graduated.');

        $type = 'confirmation';
        $config = self::LETTER_TYPES[$type];

        $letter = GraduationLetter::create([
            'university_id' => $student->university_id,
            'student_id' => $student->id,
            'type' => $type,
            'recipient_address' => $this->defaultRecipientAddress($student),
            'subject' => $config['subject'],
            'office_title' => $config['office_title'],
            'signatory_title' => $config['signatory_title'],
            'body_content' => $this->defaultBodyContent($student, $type),
            'letter_number' => $this->resolveLetterNumber($student, $type),
            'secure_code' => 'SMU-ALM-'.Str::upper(Str::random(8)),
            'generated_by' => auth()->id(),
        ]);

        return redirect()->route('graduation-letters.show', $letter->id)
            ->with('success', 'Graduation confirmation letter generated.');
    }

    private function resolveLetterNumber(Student $student, string $type): string
    {
        $format = ReferenceFormat::where('university_id', $student->university_id)
            ->where('document_type', 'graduation')
            ->where('is_active', true)
            ->first();

        if ($format) {
            return $format->generateAndIncrement();
        }

        return 'SMU-ALM/'.date('Y').'/'.Str::upper(Str::random(6));
    }

    private function defaultRecipientAddress(Student $student): string
    {
        return $student->formatted_name."\n".
            'Registration Number: '.$student->registration_number.
            ($student->program ? "\n".'Programme: '.$student->program->name : '');
    }

    private function defaultSubject(Student $student, string $type): ?string
    {
        return match ($type) {
            'introduction' => 'INTRODUCING '.($student->salutation ? $student->salutation.' ' : '').$student->formatted_name,
            'recommendation' => 'LETTER OF RECOMMENDATION FOR '.($student->salutation ? $student->salutation.' ' : '').$student->formatted_name,
            default => self::LETTER_TYPES[$type]['subject'],
        };
    }

    /**
     * A sensible starting draft per type, built from facts already on
     * file - staff edit this before submitting (same pattern as the
     * existing numbered-letter-field used for Leave application letters).
     */
    private function defaultBodyContent(Student $student, string $type): string
    {
        $salutation = $student->salutation ? $student->salutation.' ' : '';
        $name = $student->formatted_name;
        $pronoun = $student->pronoun;
        $university = $student->university->name ?? "St John's University of Tanzania";
        $degree = $student->degree_with_abbreviation ?? $student->program->name ?? 'their programme';
        $studyLevel = $student->program->study_level ?? null;
        $gradDate = $student->graduation_date ? DateToWordsHelper::fullDate($student->graduation_date) : '[graduation date]';
        $gradDateWithWeekday = $student->graduation_date ? DateToWordsHelper::fullDate($student->graduation_date, includeWeekday: true) : '[graduation date]';

        return match ($type) {
            'confirmation' => "This is to confirm that {$name}, Registration Number {$student->registration_number}, was a bona fide student of this University".($degree ? " in the {$degree} programme" : '')." and graduated on {$gradDate}.\nThis letter is issued upon request for whatever legitimate purpose it may serve.",

            'introduction' => "Please refer to the above subject matter.\n".
                "I am pleased to formally introduce {$salutation}{$name} (registration number {$student->registration_number}), a distinguished".($studyLevel ? " {$studyLevel}'s" : '')." student of {$university}. {$this->capitalize($pronoun)} will be conferred the degree of {$degree} at the congregation that will take place on {$gradDate} at this University.\n".
                "[Add the specific purpose of this introduction here - e.g. the position or opportunity being pursued.]\n".
                "I dutifully request your esteemed office to assist {$student->object_pronoun} with {$student->possessive_pronoun} endeavors.\n".
                'Thank you for your cooperation.',

            'english_proficiency' => "I am writing to certify that {$name} ({$student->registration_number}), bonafide graduate of {$university}, successfully completed".
                ($student->programme_duration_years ? " a {$student->programme_duration_years}-year" : '')." {$degree} programme".
                ($student->programme_start_academic_year ? ", from {$student->programme_start_academic_year} to {$student->programme_end_academic_year} academic years" : '').
                ($student->ordinal_graduation_ceremony ? ". {$this->capitalize($pronoun)} was subsequently awarded {$degree} during the {$student->ordinal_graduation_ceremony} Graduation Ceremony of {$university} held on {$gradDateWithWeekday}." : '.')."\n".
                "It is worthwhile to note that, {$university} uses English as the medium of Instruction and Communication for all the conducted programmes, and mode of study is full time. This is an assurance that {$name} has the required fluency to communicate in English.",

            'recommendation' => "I am pleased to recommend {$salutation}{$name} ({$student->registration_number}) for admission to your esteemed Institution. It has been my honor to know {$student->object_pronoun} during {$student->possessive_pronoun} time at {$university}, where {$pronoun} earned {$degree}".
                ($student->programme_start_academic_year ? " from {$student->programme_start_academic_year} to {$student->programme_end_academic_year}" : '').
                ($student->classification ? ", achieving {$student->classification}".($student->gpa ? " with a GPA of {$student->gpa}" : '') : '').".\n".
                '[Add specific remarks on academic performance and personal qualities here.]'."\n".
                'I fully support this recommendation.',

            default => '',
        };
    }

    private function capitalize(string $word): string
    {
        return ucfirst($word);
    }

    public function show($id)
    {
        $letter = $this->resolveLetter($id);
        $university = $letter->student->university;
        $typeLabel = self::LETTER_TYPES[$letter->type]['label'] ?? 'Graduate Letter';

        return view('graduation-letters.show', compact('letter', 'university', 'typeLabel'));
    }

    public function preview($id)
    {
        $letter = $this->resolveLetter($id);

        return $this->renderLetterView($letter, forPdf: false);
    }

    public function print($id)
    {
        $letter = $this->resolveLetter($id);

        return $this->renderLetterView($letter, forPdf: false, autoPrint: request()->boolean('embedded'));
    }

    public function downloadPdf($id)
    {
        $letter = $this->resolveLetter($id);

        $html = $this->renderLetterView($letter, forPdf: true)->render();

        $cleanLetterNumber = preg_replace('/[\/\\\\]/', '-', $letter->letter_number);
        $filename = 'graduate-letter-'.$cleanLetterNumber.'.pdf';

        return Pdf::setOptions(['isPhpEnabled' => true], mergeWithDefaults: true)->loadHTML($html)->download($filename);
    }

    /**
     * Public, unauthenticated verification by secure code - mirrors
     * LeaveLetterController::publicVerify() exactly, including the
     * DocumentVerificationLog write.
     */
    public function publicVerify(string $code): View
    {
        $letter = GraduationLetter::with(['student.university', 'student.program'])
            ->where('secure_code', $code)
            ->firstOrFail();

        $letter->markAsVerified(request()->ip());

        $student = $letter->student;

        DocumentVerificationLog::create([
            'document_type' => 'graduation_letter',
            'document_id' => $letter->id,
            'university_id' => $student->university_id,
            'code' => $code,
            'successful' => true,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return view('verify.letter-show', [
            'documentType' => self::LETTER_TYPES[$letter->type]['label'] ?? 'Graduate Letter',
            'letterNumber' => $letter->letter_number,
            'secureCode' => $letter->secure_code,
            'issuedBy' => $student->university->name ?? 'St. John\'s University of Tanzania',
            'issuedOn' => $letter->created_at->format('d M Y'),
            'verifiedAt' => now()->format('d M Y H:i'),
            'university' => $student->university,
            'recipientName' => $student->formatted_name,
            'programme' => $student->degree_with_abbreviation ?? $student->program->name ?? null,
            'graduationYear' => $student->graduation_year,
            'certificateNumber' => $student->certificate_barcode,
        ]);
    }

    private function resolveLetter($id): GraduationLetter
    {
        $letter = GraduationLetter::with(['student.program', 'student.university'])->findOrFail($id);

        $user = auth()->user();
        abort_unless(
            $user->hasRole('super-admin') || $letter->university_id === $user->university_id,
            403
        );

        return $letter;
    }

    private function renderLetterView(GraduationLetter $letter, bool $forPdf, bool $autoPrint = false): View
    {
        $student = $letter->student;
        $university = $student->university;
        $config = self::LETTER_TYPES[$letter->type] ?? self::LETTER_TYPES['confirmation'];

        // body_content is frozen on the letter row at issue time (see
        // store()/generate()) - a legacy row from before that column
        // existed falls back to a freshly-generated default rather than
        // showing a blank letter.
        $bodyContent = $letter->body_content ?? $this->defaultBodyContent($student, $letter->type);

        return view('components.letter.alumni-letter', [
            'student' => $student,
            'university' => $university,
            'letterNumber' => $letter->letter_number,
            'secureCode' => $letter->secure_code,
            'title' => $config['title'],
            'subject' => $letter->subject,
            'recipientAddress' => $letter->type === 'confirmation' || $config['requires_recipient_input']
                ? ($letter->recipient_address ?? $this->defaultRecipientAddress($student))
                : null,
            'recipientHeading' => $config['recipient_heading'],
            'bodyContent' => $bodyContent,
            'numbered' => $config['numbered'],
            'officeTitle' => $letter->office_title,
            'signatory' => auth()->user()->full_name ?? null,
            'signatoryTitle' => $letter->signatory_title,
            'qrCodeImage' => $this->generateQrCode(route('verify.show', $letter->secure_code), $university),
            'downloadUrl' => route('graduation-letters.download-pdf', $letter->id),
            'forPdf' => $forPdf,
            'embedded' => request()->boolean('embedded'),
            'autoPrint' => $autoPrint,
        ]);
    }
}
