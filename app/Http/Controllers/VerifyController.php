<?php

namespace App\Http\Controllers;

use App\Models\ReportLog;
use App\Models\ReportDefinition;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

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
     * Validate a verification code and display the document details.
     */
    public function show($code)
    {
        // Look up the log without throwing an exception
        $log = ReportLog::with(['user', 'reportDefinition'])
            ->where('verification_code', $code)
            ->first();

        // If not found, show a friendly error page
        if (!$log) {
            return view('verify.not-found', compact('code'));
        }

        // Mark as verified the first time it is viewed
        if (!$log->verified_at) {
            $log->update(['verified_at' => now()]);
        }

        // Display the standalone verification card (no layout, no sidebar)
        return view('verify.public-show', compact('log'));
    }

    /**
     * Generate a printable PDF certificate for the verified document.
     */
    public function print($code)
    {
        $log = ReportLog::with('reportDefinition')
            ->where('verification_code', $code)
            ->firstOrFail();

        if (!$log->verified_at) {
            $log->update(['verified_at' => now()]);
        }

        $pdf = Pdf::loadView('verify.print', compact('log'));
        return $pdf->download('verification_' . $code . '.pdf');
    }
}