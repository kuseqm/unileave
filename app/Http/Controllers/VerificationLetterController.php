<?php

namespace App\Http\Controllers;

use App\Models\DocumentVerificationLog;
use App\Models\LetterSignatory;
use App\Models\ReferenceFormat;
use App\Models\Student;
use App\Models\VerificationLetter;
use App\Models\VerificationLetterItem;
use App\Services\VerificationLetterWording;
use App\Traits\GeneratesQrCodeTrait;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * Letters that confirm to an outside party that a graduate's certificate
 * (and transcript) is authentic. The Registry or Examinations Office fills in
 * who is asking, what they wrote, which graduates it is about and who signs;
 * the number comes from the Registry's own reference format for this kind of
 * letter, so a volume that is full rolls over to the next one by itself.
 */
class VerificationLetterController extends Controller
{
    use GeneratesQrCodeTrait;

    public function index(Request $request)
    {
        $university = auth()->user()->university;

        $letters = VerificationLetter::where('university_id', $university->id)
            ->with(['signatory', 'items'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $term = '%'.$request->query('search').'%';
                $query->where(fn ($inner) => $inner
                    ->where('letter_number', 'like', $term)
                    ->orWhere('recipient_organisation', 'like', $term)
                    ->orWhere('received_reference', 'like', $term)
                    ->orWhereHas('items', fn ($items) => $items->where('name', 'like', $term)->orWhere('number', 'like', $term)));
            })
            ->latest('id')
            ->paginate(in_array($request->integer('per_page'), [10, 25, 50]) ? $request->integer('per_page') : 10)
            ->withQueryString();

        return view('verification-letters.index', compact('letters'));
    }

    public function create(Request $request)
    {
        $university = auth()->user()->university;

        // Coming from a graduate's record: start with that graduate already on the letter.
        $prefill = $request->filled('student')
            ? Student::with('program')->where('university_id', $university->id)->where('is_graduated', true)->find($request->integer('student'))
            : null;

        return view('verification-letters.create', [
            'prefill' => $prefill ? $this->graduatePayload($prefill) : null,
            'signatories' => LetterSignatory::where('university_id', $university->id)->active()->orderBy('id')->get(),
            'format' => $this->referenceFormat($university->id),
            'templates' => VerificationLetter::TEMPLATES,
            'languages' => VerificationLetter::LANGUAGES,
            'receivedVia' => VerificationLetter::RECEIVED_VIA,
            'numberTypes' => VerificationLetter::NUMBER_TYPES,
        ]);
    }

    /** Live search for the graduates a letter is about. */
    public function graduates(Request $request)
    {
        $term = trim((string) $request->query('q'));

        abort_if(mb_strlen($term) < 2, 422, 'Type at least 2 characters.');

        $students = Student::with('program')
            ->where('university_id', auth()->user()->university_id)
            ->where('is_graduated', true)
            ->where(fn ($query) => $query
                ->where('registration_number', 'like', "%{$term}%")
                ->orWhere('certificate_barcode', 'like', "%{$term}%")
                ->orWhere('full_name', 'like', "%{$term}%"))
            ->orderBy('full_name')
            ->limit(15)
            ->get();

        return response()->json($students->map(fn (Student $student) => $this->graduatePayload($student)));
    }

    /** @return array<string, mixed> */
    private function graduatePayload(Student $student): array
    {
        return [
            'student_id' => $student->id,
            'label' => $student->formatted_name.' · '.$student->registration_number,
            'name' => $student->formatted_name,
            'gender' => $student->gender,
            'registration_number' => $student->registration_number,
            'certificate_number' => $student->certificate_barcode ?: $student->registration_number,
            'programme' => $student->program->name ?? '',
            'graduation_year' => (string) $student->graduation_year,
            'study_from' => $student->programme_start_academic_year,
            'study_to' => $student->programme_end_academic_year,
            'duration_years' => $student->programme_duration_years,
        ];
    }

    /** Shows the letter as it will look, without saving it or using up a number. */
    public function preview(Request $request)
    {
        $data = $this->validated($request);
        $university = auth()->user()->university;

        $letter = new VerificationLetter(Arr::except($data, ['items']));
        $letter->letter_number = 'DRAFT - number assigned when generated';
        $letter->secure_code = 'DRAFT';
        $letter->setRelation('signatory', LetterSignatory::findOrFail($data['signatory_id']));
        $letter->setRelation('items', collect($data['items'])->map(fn ($item, $index) => new VerificationLetterItem($item + ['position' => $index + 1]))->values());

        return $this->render($letter, $university, forPdf: false, isDraft: true);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $university = auth()->user()->university;

        $format = $this->referenceFormat($university->id);

        if (! $format) {
            return back()->withInput()->with('error', 'No reference format exists for verification letters yet. Ask the Registry to add one (Registry > Reference Formats > Certificate / Transcript Verification Letter).');
        }

        $letter = DB::transaction(function () use ($data, $format, $university) {
            $letter = VerificationLetter::create(Arr::except($data, ['items']) + [
                'university_id' => $university->id,
                'letter_number' => $format->generateForCurrentVolume(),
                'secure_code' => 'SMU-VER-'.Str::upper(Str::random(8)),
                'generated_by' => auth()->id(),
            ]);

            foreach ($data['items'] as $index => $item) {
                $letter->items()->create($item + ['position' => $index + 1]);
            }

            return $letter;
        });

        return redirect()->route('verification-letters.show', $letter->id)
            ->with('success', "Letter {$letter->letter_number} generated.");
    }

    public function show($id)
    {
        $letter = $this->resolveLetter($id);

        return view('verification-letters.show', compact('letter'));
    }

    public function letter($id)
    {
        return $this->render($this->resolveLetter($id), auth()->user()->university, forPdf: false);
    }

    public function print($id)
    {
        return $this->render($this->resolveLetter($id), auth()->user()->university, forPdf: false, autoPrint: request()->boolean('embedded'));
    }

    public function downloadPdf($id)
    {
        $letter = $this->resolveLetter($id);
        $html = $this->render($letter, auth()->user()->university, forPdf: true)->render();

        $filename = 'verification-letter-'.preg_replace('/[\/\\\\ ]+/', '-', $letter->letter_number).'.pdf';

        return Pdf::setOptions(['isPhpEnabled' => true], mergeWithDefaults: true)->loadHTML($html)->download($filename);
    }

    /** Public, unauthenticated page a QR scan or the printed code leads to. */
    public function publicVerify(string $code): View
    {
        $letter = VerificationLetter::with(['university', 'items'])->where('secure_code', $code)->firstOrFail();

        $letter->markAsVerified(request()->ip());

        DocumentVerificationLog::create([
            'document_type' => 'verification_letter',
            'document_id' => $letter->id,
            'university_id' => $letter->university_id,
            'code' => $code,
            'successful' => true,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return view('verify.letter-show', [
            'documentType' => 'Certificate / Transcript Verification Letter',
            'letterNumber' => $letter->letter_number,
            'secureCode' => $letter->secure_code,
            'issuedBy' => $letter->university->name ?? 'St. John\'s University of Tanzania',
            'issuedOn' => $letter->letter_date->format('d M Y'),
            'verifiedAt' => now()->format('d M Y H:i'),
            'university' => $letter->university,
            'recipientName' => $letter->recipient_organisation,
            'verifiedGraduates' => $letter->items->map(fn ($item) => $item->name.' - '.$item->number.' ('.$item->graduation_year.')')->all(),
        ]);
    }

    // ------------------------------------------------------------ signatories

    public function signatories()
    {
        return view('verification-letters.signatories', [
            'signatories' => LetterSignatory::where('university_id', auth()->user()->university_id)->orderBy('id')->get(),
        ]);
    }

    public function storeSignatory(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'title_en' => 'required|string|max:255',
            'unit_en' => 'nullable|string|max:255',
            'title_sw' => 'required|string|max:255',
            'unit_sw' => 'nullable|string|max:255',
            'office_en' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
        ]);

        LetterSignatory::create($validated + ['university_id' => auth()->user()->university_id]);

        return redirect()->route('verification-letters.signatories')->with('success', "{$validated['name']} added as a signatory.");
    }

    public function toggleSignatory(LetterSignatory $signatory)
    {
        abort_unless($signatory->university_id === auth()->user()->university_id, 403);

        $signatory->update(['is_active' => ! $signatory->is_active]);

        return redirect()->route('verification-letters.signatories')
            ->with('success', $signatory->name.($signatory->is_active ? ' can sign letters again.' : ' will no longer appear when choosing who signs.'));
    }

    // ------------------------------------------------------------ helpers

    private function referenceFormat(int $universityId): ?ReferenceFormat
    {
        return ReferenceFormat::where('university_id', $universityId)
            ->where('document_type', 'verification')
            ->where('is_active', true)
            ->first();
    }

    private function resolveLetter($id): VerificationLetter
    {
        $letter = VerificationLetter::with(['items', 'signatory', 'university', 'generatedBy'])->findOrFail($id);

        abort_unless(
            auth()->user()->hasRole('super-admin') || $letter->university_id === auth()->user()->university_id,
            403
        );

        return $letter;
    }

    private function render(VerificationLetter $letter, $university, bool $forPdf, bool $autoPrint = false, bool $isDraft = false): View
    {
        $secureCode = $letter->secure_code;

        return view('verification-letters.letter', [
            'letter' => $letter,
            'content' => app(VerificationLetterWording::class)->build($letter),
            'university' => $letter->university ?? $university,
            'forPdf' => $forPdf,
            'embedded' => request()->boolean('embedded'),
            'autoPrint' => $autoPrint,
            'isDraft' => $isDraft,
            'downloadUrl' => $letter->exists ? route('verification-letters.download-pdf', $letter->id) : null,
            'qrCodeImage' => $isDraft ? null : $this->generateQrCode(route('verify.show', $secureCode), $letter->university ?? $university),
        ]);
    }

    /** @return array<string, mixed> */
    private function validated(Request $request): array
    {
        $universityId = auth()->user()->university_id;
        $custom = $request->input('template') === 'custom';

        $data = $request->validate([
            'template' => ['required', Rule::in(array_keys(VerificationLetter::TEMPLATES))],
            'language' => ['required', Rule::in(array_keys(VerificationLetter::LANGUAGES))],
            'letter_date' => 'required|date',
            'recipient_title' => 'nullable|string|max:255',
            'recipient_organisation' => 'required|string|max:255',
            'recipient_address' => 'nullable|string|max:1000',
            'recipient_city' => 'nullable|string|max:255',
            'recipient_country' => 'nullable|string|max:255',
            'recipient_email' => 'nullable|string|max:255',
            'ufs' => 'nullable|string|max:500',
            'received_reference' => 'nullable|string|max:255',
            'received_date' => 'nullable|date',
            'received_via' => [$custom ? 'nullable' : 'required', Rule::in(array_keys(VerificationLetter::RECEIVED_VIA))],
            'documents' => [$custom ? 'nullable' : 'required', 'array'],
            'documents.*' => Rule::in(['certificate', 'transcript']),
            'number_type' => [$custom ? 'nullable' : 'required', Rule::in(array_keys(VerificationLetter::NUMBER_TYPES))],
            'subject' => [$custom ? 'required' : 'nullable', 'string', 'max:255'],
            'mode_of_study' => 'nullable|string|max:100',
            'extra_text' => 'nullable|string|max:2000',
            'body_text' => [$custom ? 'required' : 'nullable', 'string', 'max:10000'],
            'signatory_id' => ['required', Rule::exists('letter_signatories', 'id')->where('university_id', $universityId)->where('is_active', true)],
            'items' => [$custom ? 'nullable' : 'required', 'array', 'max:50'],
            'items.*.student_id' => ['required', 'integer', Rule::exists('students', 'id')->where('university_id', $universityId)->where('is_graduated', true)],
            'items.*.name' => 'required|string|max:255',
            'items.*.gender' => 'nullable|in:M,F',
            'items.*.number' => 'required|string|max:100',
            'items.*.programme' => 'required|string|max:255',
            'items.*.graduation_year' => 'required|string|max:10',
            'items.*.barcode' => 'nullable|string|max:100',
            'items.*.study_from' => 'nullable|string|max:20',
            'items.*.study_to' => 'nullable|string|max:20',
            'items.*.duration_years' => 'nullable|integer|min:1|max:10',
        ]);

        if ($data['template'] === 'dataflow') {
            $request->validate(['items.*.barcode' => 'required|string|max:100']);
            $data['language'] = 'en';
        }

        // A custom letter is just words: no request details, no graduates table.
        $data['items'] = $custom ? [] : array_values($data['items'] ?? []);
        $data['documents'] = $custom ? [] : array_values(array_unique($data['documents'] ?? []));
        $data['received_via'] = $data['received_via'] ?? 'letter';
        $data['number_type'] = $data['number_type'] ?? 'certificate';

        return $data;
    }
}
