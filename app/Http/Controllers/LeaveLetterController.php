<?php

namespace App\Http\Controllers;

use App\Models\LeaveLetter;
use App\Models\University;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeaveLetterController extends Controller
{
    /**
     * Display user's leave letters
     */
    public function index()
    {
        $user = Auth::user();
        
        $letters = LeaveLetter::with(['leaveRequest', 'leaveRequest.leaveType', 'staff'])
            ->where('staff_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        $stats = [
            'total' => LeaveLetter::where('staff_id', $user->id)->count(),
            'draft' => LeaveLetter::where('staff_id', $user->id)->where('stage', 'draft')->count(),
            'generated' => LeaveLetter::where('staff_id', $user->id)->where('stage', 'generated')->count(),
            'printed' => LeaveLetter::where('staff_id', $user->id)->where('is_printed', true)->count(),
        ];
        
        return view('leave-letters.index', compact('letters', 'stats'));
    }
    
    /**
     * Show a specific leave letter
     */
    public function show($id)
    {
        $letter = LeaveLetter::with(['leaveRequest', 'leaveRequest.staff', 'leaveRequest.leaveType'])
            ->findOrFail($id);
        
        if ($letter->staff_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
            abort(403, 'You do not have permission to view this letter.');
        }
        
        $university = University::first();
        
        return view('leave-letters.show', compact('letter', 'university'));
    }
    
/**
 * Print a leave letter
 */
public function print($id)
{
    $letter = LeaveLetter::with(['leaveRequest', 'leaveRequest.staff', 'leaveRequest.leaveType'])
        ->findOrFail($id);
    
    if ($letter->staff_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
        abort(403, 'You do not have permission to print this letter.');
    }
    
    $letter->markAsPrinted();
    $university = University::first();
    
    return view('leave-letters.print', compact('letter', 'university'));
}
    /**
     * Download as PDF
     */

public function downloadPdf($id)
{
    $letter = LeaveLetter::with(['leaveRequest', 'leaveRequest.staff', 'leaveRequest.leaveType'])
        ->findOrFail($id);
    
    if ($letter->staff_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
        abort(403, 'You do not have permission to download this letter.');
    }
    
    $letter->markAsPrinted();
    $university = University::first();
    
    $html = view('leave-letters.print', compact('letter', 'university'))->render();
    
    if (class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
        // Clean the letter number - remove any slashes or special characters
        $cleanLetterNumber = preg_replace('/[\/\\\\]/', '-', $letter->letter_number);
        $filename = 'leave-letter-' . $cleanLetterNumber . '.pdf';
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html);
        return $pdf->download($filename);
    }
    
    return view('leave-letters.print', compact('letter', 'university'));
}
}