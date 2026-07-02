<?php

namespace App\Http\Controllers\Registry;

use App\Http\Controllers\Controller;
use App\Models\IncomingLetter;
use App\Models\Office;
use App\Models\User;
use App\Helpers\AuditLogHelper;
use App\Traits\PerPageTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class IncomingLetterController extends Controller
{
    use PerPageTrait;

    /**
     * Display a listing of incoming letters.
     */
    public function index(Request $request)
    {
        $perPage = $this->getPerPage($request, 15);
        
        $query = IncomingLetter::with(['office', 'assignedTo', 'createdBy']);
        
        // Filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }
        
        if ($request->filled('office_id')) {
            $query->where('office_id', $request->office_id);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('ref_number', 'like', "%{$search}%")
                  ->orWhere('sender', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%");
            });
        }
        
        $letters = $query->orderBy('date_received', 'desc')->paginate($perPage);
        
        // For filter dropdowns
        $offices = Office::where('is_active', true)->orderBy('name')->get();
        
        return view('registry.incoming.index', compact('letters', 'offices'));
    }

    /**
     * Show the form for creating a new incoming letter.
     */
    public function create()
    {
        $offices = Office::where('is_active', true)
            ->where('receives_letters', true)
            ->orderBy('name')
            ->get();
        
        return view('registry.incoming.create', compact('offices'));
    }

    /**
     * Store a newly created incoming letter.
     */
    public function store(Request $request)
    {
        $request->validate([
            'sender' => 'required|string|max:200',
            'office_id' => 'required|exists:offices,id',
            'subject' => 'required|string',
            'date_received' => 'required|date',
            'pdf_file' => 'nullable|file|mimes:pdf|max:10240',
        ]);

        DB::beginTransaction();

        try {
            // Generate reference number
            $year = date('Y');
            $lastLetter = IncomingLetter::whereYear('created_at', $year)
                ->orderBy('id', 'desc')
                ->first();

            if ($lastLetter) {
                $lastNum = intval(substr($lastLetter->ref_number, -5));
                $newNum = str_pad($lastNum + 1, 5, '0', STR_PAD_LEFT);
            } else {
                $newNum = '00001';
            }

            $refNumber = "UC-IN-{$year}-{$newNum}";

            // Handle PDF upload
            $pdfPath = null;
            if ($request->hasFile('pdf_file')) {
                $file = $request->file('pdf_file');
                $filename = $refNumber . '.pdf';
                $pdfPath = $file->storeAs('incoming_letters/' . $year, $filename, 'public');
            }

            // Create letter
            $letter = IncomingLetter::create([
                'ref_number' => $refNumber,
                'date_received' => $request->date_received,
                'sender' => $request->sender,
                'office_id' => $request->office_id,
                'subject' => $request->subject,
                'pdf_path' => $pdfPath,
                'assigned_to' => null,
                'status' => 'received',
                'priority' => $request->priority ?? 'normal',
                'remarks' => $request->remarks,
                'created_by' => Auth::id(),
            ]);

            AuditLogHelper::log('create', 'incoming_letter', $letter->id, null, $letter->toArray());

            DB::commit();

            return redirect()->route('registry.incoming.show', $letter->id)
                ->with('success', "Letter {$refNumber} created successfully.");

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Incoming letter creation failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to create letter: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified incoming letter.
     */
    public function show($id)
    {
        $letter = IncomingLetter::with(['office', 'assignedTo', 'createdBy', 'outgoingLetters'])
            ->findOrFail($id);
        
        $availableOfficers = User::where('status', 'active')
            ->orderBy('first_name')
            ->take(50)
            ->get();
        
        return view('registry.incoming.show', compact('letter', 'availableOfficers'));
    }

    /**
     * Show the form for editing the specified incoming letter.
     */
    public function edit($id)
    {
        $letter = IncomingLetter::findOrFail($id);
        $offices = Office::where('is_active', true)->orderBy('name')->get();
        
        return view('registry.incoming.edit', compact('letter', 'offices'));
    }

    /**
     * Update the specified incoming letter.
     */
    public function update(Request $request, $id)
    {
        $letter = IncomingLetter::findOrFail($id);

        $request->validate([
            'sender' => 'required|string|max:200',
            'office_id' => 'required|exists:offices,id',
            'subject' => 'required|string',
            'date_received' => 'required|date',
            'status' => 'required|in:received,assigned,in_progress,replied,closed',
            'priority' => 'required|in:normal,urgent,confidential',
        ]);

        $oldValues = $letter->toArray();

        $letter->update([
            'sender' => $request->sender,
            'office_id' => $request->office_id,
            'subject' => $request->subject,
            'date_received' => $request->date_received,
            'status' => $request->status,
            'priority' => $request->priority,
            'remarks' => $request->remarks,
            'closed_at' => $request->status === 'closed' ? now() : $letter->closed_at,
        ]);

        AuditLogHelper::log('update', 'incoming_letter', $letter->id, $oldValues, $letter->toArray());

        return redirect()->route('registry.incoming.show', $letter->id)
            ->with('success', 'Letter updated successfully.');
    }

    /**
     * Remove the specified incoming letter.
     */
    public function destroy($id)
    {
        $letter = IncomingLetter::findOrFail($id);

        // Delete PDF if exists
        if ($letter->pdf_path && Storage::disk('public')->exists($letter->pdf_path)) {
            Storage::disk('public')->delete($letter->pdf_path);
        }

        $letter->delete();

        AuditLogHelper::log('delete', 'incoming_letter', $letter->id, $letter->toArray(), null);

        return redirect()->route('registry.incoming.index')
            ->with('success', 'Letter deleted successfully.');
    }

    /**
     * Assign letter to an action officer.
     */
    public function assign(Request $request, $id)
    {
        $request->validate([
            'assigned_to' => 'required|exists:staff,id',
        ]);

        $letter = IncomingLetter::findOrFail($id);
        $oldAssignedTo = $letter->assigned_to;

        $letter->update([
            'assigned_to' => $request->assigned_to,
            'status' => 'assigned',
        ]);

        AuditLogHelper::log('assign', 'incoming_letter', $letter->id, 
            ['assigned_to' => $oldAssignedTo, 'status' => 'received'], 
            ['assigned_to' => $request->assigned_to, 'status' => 'assigned']
        );

        return redirect()->route('registry.incoming.show', $letter->id)
            ->with('success', 'Letter assigned successfully.');
    }

    /**
     * Update letter status.
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:received,assigned,in_progress,replied,closed',
        ]);

        $letter = IncomingLetter::findOrFail($id);
        $oldStatus = $letter->status;

        $letter->update([
            'status' => $request->status,
            'closed_at' => $request->status === 'closed' ? now() : $letter->closed_at,
        ]);

        AuditLogHelper::log('status_update', 'incoming_letter', $letter->id, 
            ['status' => $oldStatus], 
            ['status' => $request->status]
        );

        return redirect()->route('registry.incoming.show', $letter->id)
            ->with('success', "Status updated to {$request->status}.");
    }
}