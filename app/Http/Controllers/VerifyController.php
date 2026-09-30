<?php

namespace App\Http\Controllers;

use App\Models\DocumentVerificationLog;
use App\Models\GraduationLetter;
use App\Models\LeaveLetter;
use App\Models\ReportLog;
use App\Models\TravelLetter;
use App\Models\VerificationLetter;
use Barryvdh\DomPDF\Facade\Pdf;

class VerifyController extends Controller
{
    /**
     * Show the verification form (optional – mainly used as a fallback).
     * The main search form is on the landing page.
     */
    public function index()
    {
        return view('verify.index');
    }

    /**
     * Validate a verification code and display the matching document. This
     * is the one public link meant to verify any verifiable code the
     * system issues - a report, a Leave letter, or a Travel Permission
     * letter - without the caller needing to know which kind of code they
     * have. Each code format is unique across these sources (report codes
     * and secure_code letters never collide), so a single sequential
     * lookup is enough; whichever table matches renders its own view.
     */
    public function show($code)
    {
        $log = ReportLog::with(['user.university', 'reportDefinition'])
            ->where('verification_code', $code)
            ->first();

        if ($log) {
            if (! $log->verified_at) {
                $log->update(['verified_at' => now()]);
            }

            DocumentVerificationLog::create([
                'document_type' => 'report',
                'document_id' => $log->id,
                'university_id' => $log->user->university_id ?? null,
                'code' => $code,
                'successful' => true,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            // Same minimal-metadata treatment as a letter (see
            // LeaveLetterController::publicVerify()) - this proves the
            // report is genuine without disclosing who ran it or what
            // filters they used. Deliberately no recipientName/approvedBy
            // here (unlike a letter) - a report has no "recipient" in
            // that sense, and who generated it is exactly the detail this
            // page intentionally withholds. university is still passed,
            // purely for the logo/brand-color treatment on this
            // multi-tenant page - that's cosmetic, not disclosure.
            return view('verify.letter-show', [
                'documentType' => $log->reportDefinition->display_name ?? 'Report',
                'letterNumber' => $log->verification_code,
                'secureCode' => $log->verification_code,
                'issuedBy' => $log->user->university->name ?? 'St. John\'s University of Tanzania',
                'issuedOn' => $log->created_at->format('d M Y'),
                'verifiedAt' => now()->format('d M Y H:i'),
                'printUrl' => route('verify.print', $log->verification_code),
                'university' => $log->user->university,
            ]);
        }

        if (LeaveLetter::where('secure_code', $code)->exists()) {
            return app(LeaveLetterController::class)->publicVerify($code);
        }

        if (TravelLetter::where('secure_code', $code)->exists()) {
            return app(TravelLetterController::class)->publicVerify($code);
        }

        if (GraduationLetter::where('secure_code', $code)->exists()) {
            return app(GraduationLetterController::class)->publicVerify($code);
        }

        if (VerificationLetter::where('secure_code', $code)->exists()) {
            return app(VerificationLetterController::class)->publicVerify($code);
        }

        // No document_id - nothing matched, there's no row to point at.
        // The raw code is still worth keeping: a cluster of failed
        // attempts against similar-looking codes reads very differently
        // from one isolated typo.
        DocumentVerificationLog::create([
            'document_type' => 'unknown',
            'document_id' => null,
            'code' => $code,
            'successful' => false,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return view('verify.not-found', compact('code'));
    }

    /**
     * Generate a printable PDF certificate for the verified document.
     */
    public function print($code)
    {
        $log = ReportLog::with('reportDefinition')
            ->where('verification_code', $code)
            ->firstOrFail();

        if (! $log->verified_at) {
            $log->update(['verified_at' => now()]);
        }

        $pdf = Pdf::loadView('verify.print', compact('log'));

        return $pdf->download('verification_'.$code.'.pdf');
    }
}
