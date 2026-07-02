<?php

namespace App\Http\Controllers\Registry;

use App\Http\Controllers\Controller;
use App\Models\OutgoingLetter;
use App\Models\IncomingLetter;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class OutgoingLetterController extends Controller
{
    /**
     * Display a listing of outgoing letters.
     */
    public function index(Request $request)
    {
        $query = OutgoingLetter::with(['incomingLetter', 'draftedBy', 'approvedBy']);
        
        // Filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('ref_number', 'like', "%{$search}%")
                  ->orWhere('recipient', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%");
            });
        }
        
        $letters = $query->orderBy('created_at', 'desc')->paginate(15);
        
        return view('registry.outgoing.index', compact('letters'));
    }
    
    /**
     * Show the form for creating a new outgoing letter.
     */
    public function create($incoming_id = null)
    {
        $incomingLetter = null;
        if ($incoming_id) {
            $incomingLetter = IncomingLetter::findOrFail($incoming_id);
        }
        
        return view('registry.outgoing.create', compact('incomingLetter'));
    }
    
    /**
     * Store a newly created outgoing letter.
     */
    public function store(Request $request)
    {
        $request->validate([
            'recipient' => 'required|string|max:200',
            'subject' => 'required|string',
            'content' => 'required|string',
            'incoming_letter_id' => 'nullable|exists:incoming_letters,id',
        ]);
        
        DB::beginTransaction();
        
        try {
            // Generate reference number
            $year = date('Y');
            $lastLetter = OutgoingLetter::whereYear('created_at', $year)
                ->orderBy('id', 'desc')
                ->first();
            
            if ($lastLetter) {
                $lastNum = intval(substr($lastLetter->ref_number, -5));
                $newNum = str_pad($lastNum + 1, 5, '0', STR_PAD_LEFT);
            } else {
                $newNum = '00001';
            }
            
            $refNumber = "UC-OUT-{$year}-{$newNum}";
            
            // Create letter
            $letter = OutgoingLetter::create([
                'ref_number' => $refNumber,
                'date_sent' => null,
                'recipient' => $request->recipient,
                'subject' => $request->subject,
                'content' => $request->content,
                'pdf_path' => null,
                'dispatched_via' => 'email',
                'incoming_letter_id' => $request->incoming_letter_id,
                'drafted_by' => Auth::id(),
                'status' => 'draft',
                'requires_print' => $request->has('requires_print'),
            ]);
            
            DB::commit();
            
            return redirect()->route('registry.outgoing.show', $letter->id)
                ->with('success', "Outgoing letter {$refNumber} created as draft.");
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create letter: ' . $e->getMessage());
        }
    }
    
    /**
     * Display the specified outgoing letter.
     */
    public function show($id)
    {
        $letter = OutgoingLetter::with(['incomingLetter', 'draftedBy', 'approvedBy'])->findOrFail($id);
        
        return view('registry.outgoing.show', compact('letter'));
    }
    
    /**
     * Submit letter for approval.
     */
    public function submitForApproval($id)
    {
        $letter = OutgoingLetter::findOrFail($id);
        
        if ($letter->status !== 'draft') {
            return back()->with('error', 'Only draft letters can be submitted for approval.');
        }
        
        $letter->update([
            'status' => 'pending_approval',
        ]);
        
        return redirect()->route('registry.outgoing.show', $letter->id)
            ->with('success', 'Letter submitted for approval.');
    }
    
    /**
     * Approve letter.
     */
    public function approve($id)
    {
        $letter = OutgoingLetter::findOrFail($id);
        
        if ($letter->status !== 'pending_approval') {
            return back()->with('error', 'Only pending letters can be approved.');
        }
        
        $letter->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
        ]);
        
        return redirect()->route('registry.outgoing.show', $letter->id)
            ->with('success', 'Letter approved.');
    }
    
    /**
     * Reject letter.
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string',
        ]);
        
        $letter = OutgoingLetter::findOrFail($id);
        
        if ($letter->status !== 'pending_approval') {
            return back()->with('error', 'Only pending letters can be rejected.');
        }
        
        $letter->update([
            'status' => 'draft',
            'approval_comments' => $request->reason,
        ]);
        
        return redirect()->route('registry.outgoing.show', $letter->id)
            ->with('warning', 'Letter rejected. Reason: ' . $request->reason);
    }
    
    /**
     * Dispatch letter.
     */
    public function dispatch($id)
    {
        $letter = OutgoingLetter::findOrFail($id);
        
        if ($letter->status !== 'approved') {
            return back()->with('error', 'Only approved letters can be dispatched.');
        }
        
        // Generate PDF if not exists
        if (!$letter->pdf_path) {
            $pdf = Pdf::loadView('registry.outgoing.pdf', ['letter' => $letter]);
            $pdfPath = 'outgoing_letters/' . $letter->ref_number . '.pdf';
            \Storage::put($pdfPath, $pdf->output());
            $letter->pdf_path = $pdfPath;
        }
        
        $letter->update([
            'status' => 'dispatched',
            'dispatched_via' => 'email',
            'dispatched_at' => now(),
            'date_sent' => now(),
        ]);
        
        // If requires print, add to print queue
        if ($letter->requires_print) {
            \App\Models\PrintQueue::create([
                'outgoing_letter_id' => $letter->id,
                'recipient_address' => $letter->recipient,
                'status' => 'pending',
            ]);
        }
        
        return redirect()->route('registry.outgoing.show', $letter->id)
            ->with('success', 'Letter dispatched successfully.');
    }
}