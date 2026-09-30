<?php

namespace App\Http\Controllers;

use App\Models\DocumentVerificationLog;
use App\Models\TravelLetter;
use App\Traits\GeneratesQrCodeTrait;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;

class TravelLetterController extends Controller
{
    use GeneratesQrCodeTrait;

    /**
     * Show a specific travel permission letter
     */
    public function show($id)
    {
        $letter = $this->resolveLetter($id);

        $university = $letter->travelPermission->staff->university;

        return view('travel-letters.show', compact('letter', 'university'));
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
     * Print a travel permission letter
     */
    public function print($id)
    {
        $letter = $this->resolveLetter($id);
        $letter->markAsPrinted();

        return $this->renderLetterView($letter, forPdf: false, autoPrint: request()->boolean('embedded'));
    }

    /**
     * Public, unauthenticated verification by secure code - the target of
     * the unified /verify/{code} link (see VerifyController). Mirrors
     * LeaveLetterController::publicVerify() - proves authenticity only,
     * never renders the confidential letter content.
     */
    public function publicVerify(string $code): View
    {
        $letter = TravelLetter::with(['travelPermission', 'travelPermission.staff.university'])
            ->where('secure_code', $code)
            ->firstOrFail();

        abort_if(! $letter->travelPermission, 404, 'The travel request behind this letter no longer exists.');

        $letter->markAsVerified(request()->ip());

        $staff = $letter->travelPermission->staff;

        DocumentVerificationLog::create([
            'document_type' => 'travel_letter',
            'document_id' => $letter->id,
            'university_id' => $staff->university_id,
            'code' => $code,
            'successful' => true,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return view('verify.letter-show', [
            'documentType' => 'Travel Permission Application Letter',
            'letterNumber' => $letter->letter_number,
            'secureCode' => $letter->secure_code,
            'issuedBy' => $staff->university->name ?? 'St. John\'s University of Tanzania',
            'issuedOn' => $letter->created_at->format('d M Y'),
            'verifiedAt' => now()->format('d M Y H:i'),
            'university' => $staff->university,
            'recipientName' => $staff->full_name,
            // Always the applicant's own submission, never an HR-issued
            // approval letter (no such type exists for Travel yet) - no
            // separate approver to show.
        ]);
    }

    /**
     * Look up a letter by id, checking both that the caller may see it and
     * that its travel request still exists. Mirrors
     * LeaveLetterController::resolveLetter().
     */
    private function resolveLetter($id): TravelLetter
    {
        $letter = TravelLetter::with(['travelPermission', 'travelPermission.staff'])
            ->findOrFail($id);

        // The applicant, the colleague covering their duties while they're
        // away, and admins may view it - nobody else.
        $isActingStaff = $letter->travelPermission && $letter->travelPermission->acting_staff_id === auth()->id();

        if ($letter->travelPermission?->staff_id !== auth()->id() && ! $isActingStaff && ! auth()->user()->hasRole('admin')) {
            abort(403, 'You do not have permission to view this letter.');
        }

        if (! $letter->travelPermission) {
            abort(404, 'The travel request behind this letter no longer exists.');
        }

        return $letter;
    }

    /**
     * Render the applicant's Travel Permission Application Letter - unlike
     * Leave, a travel request only ever carries this one letter type, so
     * there's no type-branching needed here.
     */
    private function renderLetterView(TravelLetter $letter, bool $forPdf, bool $autoPrint = false): View
    {
        $travelRequest = $letter->travelPermission;
        $staff = $travelRequest->staff;

        return view('components.letter.travel-application', [
            'travelRequest' => $travelRequest,
            'staff' => $staff,
            'university' => $staff->university,
            'letterContent' => $travelRequest->letter_content,
            'handoverNotes' => $travelRequest->handover_notes,
            // Same DomPDF quirk as the header logo (see header.blade.php):
            // remote/HTTP image fetching is off by default, so a PDF
            // render needs a real filesystem path instead of the
            // /storage/... URL a browser can fetch.
            'signatureImage' => $letter->signature_path
                ? ($forPdf
                    ? public_path('storage/'.$letter->signature_path)
                    : Storage::disk('public')->url($letter->signature_path))
                : null,
            'secureCode' => $letter->secure_code,
            'qrCodeImage' => $this->generateQrCode(route('verify.show', $letter->secure_code), $staff->university),
            'downloadUrl' => route('travel-letters.download-pdf', $letter->id),
            'forPdf' => $forPdf,
            'embedded' => request()->boolean('embedded'),
            'autoPrint' => $autoPrint,
            'isPreview' => false,
        ]);
    }

    /**
     * Download as PDF
     */
    public function downloadPdf($id)
    {
        $letter = $this->resolveLetter($id);
        $letter->markAsPrinted();

        $html = $this->renderLetterView($letter, forPdf: true)->render();

        $cleanLetterNumber = preg_replace('/[\/\\\\]/', '-', $letter->letter_number);
        $filename = 'travel-letter-'.$cleanLetterNumber.'.pdf';

        return Pdf::setOptions(['isPhpEnabled' => true], mergeWithDefaults: true)->loadHTML($html)->download($filename);
    }
}
