<?php

namespace App\Http\Controllers;

use App\Models\DocumentVerificationLog;
use App\Models\LeaveLetter;
use App\Traits\GeneratesQrCodeTrait;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class LeaveLetterController extends Controller
{
    use GeneratesQrCodeTrait;

    /**
     * Display user's leave letters
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = LeaveLetter::with(['leaveRequest', 'leaveRequest.leaveType', 'staff'])
            ->where('staff_id', $user->id)
            ->orderBy('created_at', 'desc');

        $grandTotal = (clone $query)->count();

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('letter_number', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%");
            });
        }

        if ($request->filled('stage') && $request->stage != 'all') {
            $query->where('stage', $request->stage);
        }

        $letters = $query->paginate($request->get('per_page', 15))->appends($request->query());

        $stats = [
            'total' => LeaveLetter::where('staff_id', $user->id)->count(),
            'draft' => LeaveLetter::where('staff_id', $user->id)->where('stage', 'draft')->count(),
            'generated' => LeaveLetter::where('staff_id', $user->id)->where('stage', 'generated')->count(),
            'printed' => LeaveLetter::where('staff_id', $user->id)->where('is_printed', true)->count(),
        ];

        if ($request->wantsJson()) {
            return response()->json([
                'html' => view('leave-letters._results', compact('letters'))->render(),
                'total' => $letters->total(),
                'grand_total' => $grandTotal,
            ]);
        }

        return view('leave-letters.index', compact('letters', 'stats'));
    }

    /**
     * Show a specific leave letter
     */
    public function show($id)
    {
        $letter = $this->resolveLetter($id);

        $university = $letter->leaveRequest->staff->university;

        return view('leave-letters.show', compact('letter', 'university'));
    }

    /**
     * Render the letter for the read-only preview embedded on the show
     * page - same document as print(), but doesn't count as a print.
     */
    public function preview($id)
    {
        $letter = $this->resolveLetter($id);

        return $this->renderLetterView($letter, forPdf: false);
    }

    /**
     * Print a leave letter
     */
    public function print($id)
    {
        $letter = $this->resolveLetter($id);
        $letter->markAsPrinted();

        return $this->renderLetterView($letter, forPdf: false, autoPrint: request()->boolean('embedded'));
    }

    /**
     * Public, unauthenticated verification by secure code - the target of
     * the unified /verify/{code} link (see VerifyController). A letter's
     * content is confidential to its recipient and the approval chain, so
     * this proves authenticity only - it never renders the actual letter
     * (staff name, dates, handover notes, etc.), just enough metadata to
     * confirm the code was genuinely issued by this system.
     */
    public function publicVerify(string $code): View
    {
        $letter = LeaveLetter::with(['leaveRequest', 'leaveRequest.staff.university'])
            ->where('secure_code', $code)
            ->firstOrFail();

        abort_if(! $letter->leaveRequest, 404, 'The leave request behind this letter no longer exists.');

        $letter->markAsVerified(request()->ip());

        $staff = $letter->leaveRequest->staff;

        DocumentVerificationLog::create([
            'document_type' => 'leave_letter',
            'document_id' => $letter->id,
            'university_id' => $staff->university_id,
            'code' => $code,
            'successful' => true,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return view('verify.letter-show', [
            'documentType' => $letter->type === 'application' ? 'Leave Application Letter' : 'Leave Approval Letter',
            'letterNumber' => $letter->letter_number,
            'secureCode' => $letter->secure_code,
            'issuedBy' => $staff->university->name ?? 'St. John\'s University of Tanzania',
            'issuedOn' => $letter->created_at->format('d M Y'),
            'verifiedAt' => now()->format('d M Y H:i'),
            'university' => $staff->university,
            'recipientName' => $staff->full_name,
            // Only the HR-issued approval letter has a real approver - the
            // applicant's own application letter is their own submission,
            // nobody has "approved" it yet at the point it's verifiable.
            'approvedBy' => $letter->type === 'approval' ? ($letter->generated_by ?? null) : null,
        ]);
    }

    /**
     * Look up a letter by id, checking both that the caller may see it and
     * that its leave request still exists - a letter can outlive its leave
     * request (e.g. demo data reset), and reading straight through a
     * missing relation crashes instead of giving a clean 404.
     */
    private function resolveLetter($id): LeaveLetter
    {
        $letter = LeaveLetter::with(['leaveRequest', 'leaveRequest.staff', 'leaveRequest.leaveType'])
            ->findOrFail($id);

        // The applicant, the colleague holding their position while they're
        // away (they need to see what they're taking on), and admins may
        // view it - nobody else.
        $isActingStaff = $letter->leaveRequest && $letter->leaveRequest->acting_staff_id === auth()->id();

        if ($letter->staff_id !== auth()->id() && ! $isActingStaff && ! auth()->user()->hasAnyRole(['admin', 'hr-manager'])) {
            abort(403, 'You do not have permission to view this letter.');
        }

        if (! $letter->leaveRequest) {
            abort(404, 'The leave request behind this letter no longer exists.');
        }

        return $letter;
    }

    /**
     * Build the props the shared letterhead component needs from a
     * LeaveLetter row - kept in one place so print() and downloadPdf()
     * (browser view vs DomPDF render) can never drift apart.
     */
    private function letterProps(LeaveLetter $letter, bool $forPdf): array
    {
        $leaveRequest = $letter->leaveRequest;
        $staff = $leaveRequest->staff;

        return [
            'leaveRequest' => $leaveRequest,
            'staff' => $staff,
            'university' => $staff->university,
            'letterNumber' => $letter->letter_number,
            'secureCode' => $letter->secure_code,
            'signatory' => $letter->generated_by ?? 'Human Resources Department',
            'signatoryTitle' => $letter->generated_by_role ?? 'HR Officer',
            'cc' => [
                'Head of Department - '.($staff->department->name ?? 'N/A'),
                'Dean - '.($staff->department->school->name ?? 'Faculty'),
            ],
            'qrCodeImage' => $this->generateQrCode(route('verify.show', $letter->secure_code), $staff->university),
            // Same DomPDF quirk as the header logo and the applicant
            // letter's own signature (see renderLetterView()): remote/HTTP
            // image fetching is off by default, so a PDF render needs a
            // real filesystem path instead of the /storage/... URL a
            // browser can fetch.
            'signatureImage' => $letter->signature_path
                ? ($forPdf
                    ? public_path('storage/'.$letter->signature_path)
                    : Storage::disk('public')->url($letter->signature_path))
                : null,
            'forPdf' => $forPdf,
            'downloadUrl' => route('leave-letters.download-pdf', $letter->id),
            'officeTitle' => 'Human Resources Department',
            'embedded' => false,
            'autoPrint' => false,
        ];
    }

    /**
     * Pick the right Blade template and props for a letter, since a leave
     * request can now carry two different kinds (see
     * LeaveRequestController::store()): HR's own approval letter, and the
     * applicant's separately-signed application letter. Both share
     * resolveLetter()'s authorization/lookup, but need very different
     * bodies and voices - this is the one place that decides which.
     */
    private function renderLetterView(LeaveLetter $letter, bool $forPdf, bool $autoPrint = false): View
    {
        if ($letter->type === 'application') {
            $leaveRequest = $letter->leaveRequest;
            $staff = $leaveRequest->staff;

            return view('components.letter.leave-application', [
                'leaveRequest' => $leaveRequest,
                'staff' => $staff,
                'university' => $staff->university,
                'letterContent' => $leaveRequest->letter_content,
                'handoverNotes' => $leaveRequest->handover_notes,
                // Same DomPDF quirk as the header logo (see
                // header.blade.php): remote/HTTP image fetching is off by
                // default, so a PDF render needs a real filesystem path
                // instead of the /storage/... URL a browser can fetch.
                'signatureImage' => $letter->signature_path
                    ? ($forPdf
                        ? public_path('storage/'.$letter->signature_path)
                        : Storage::disk('public')->url($letter->signature_path))
                    : null,
                'secureCode' => $letter->secure_code,
                'qrCodeImage' => $this->generateQrCode(route('verify.show', $letter->secure_code), $staff->university),
                'downloadUrl' => route('leave-letters.download-pdf', $letter->id),
                'forPdf' => $forPdf,
                'embedded' => request()->boolean('embedded'),
                'autoPrint' => $autoPrint,
                'isPreview' => false,
            ]);
        }

        $props = $this->letterProps($letter, $forPdf);
        $props['embedded'] = request()->boolean('embedded');
        $props['autoPrint'] = $autoPrint;

        return view('components.letter.leave-approval', $props);
    }

    /**
     * Download as PDF
     */
    public function downloadPdf($id)
    {
        $letter = $this->resolveLetter($id);
        $letter->markAsPrinted();

        // DomPDF doesn't fetch remote/HTTP images by default (isRemoteEnabled
        // is off) - forPdf=true switches the component's logo (and, for an
        // application letter, signature) image to a real filesystem path
        // instead of the /storage/... URL a browser can fetch.
        $html = $this->renderLetterView($letter, forPdf: true)->render();

        // Clean the letter number - remove any slashes or special characters
        $cleanLetterNumber = preg_replace('/[\/\\\\]/', '-', $letter->letter_number);
        $filename = 'leave-letter-'.$cleanLetterNumber.'.pdf';

        // isPhpEnabled is off by default in DomPDF - needed here only to let
        // the letter's own <script type="text/php"> block draw the "page 2
        // onward" page number directly on the canvas. The HTML reaching
        // DomPDF is always our own static Blade output, never
        // user-supplied, so this doesn't open up arbitrary PHP execution
        // from untrusted content.
        return Pdf::setOptions(['isPhpEnabled' => true], mergeWithDefaults: true)->loadHTML($html)->download($filename);
    }
}
