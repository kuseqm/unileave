<?php

namespace App\Http\Controllers;

use App\Models\Letter;
use App\Models\LetterFile;
use App\Models\ReferenceFormat;
use App\Models\LeaveLetter;
use App\Models\LeaveRequest;
use App\Models\University;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class LetterController extends Controller
{

public function index()
{
    $user = Auth::user();
    $role = $user->getRoleNames()->first();
    
    // Get regular letters
    if (in_array($role, ['admin', 'registry-officer'])) {
        $letters = Letter::with('recipient', 'letterable')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
    } elseif (in_array($role, ['hod', 'dean', 'dvca', 'dvc-pfa'])) {
        $letters = Letter::where('stage', 'pending_approval')
            ->orderBy('submitted_at', 'asc')
            ->paginate(15);
    } else {
        $letters = Letter::where('sender_id', $user->id)
            ->orWhere('recipient_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);
    }
    
    // Also get leave approval letters (LeaveLetter model)
    $leaveLetters = \App\Models\LeaveLetter::where('staff_id', $user->id)
        ->orWhereHas('leaveRequest', function($q) use ($user) {
            $q->where('acting_staff_id', $user->id);
        })
        ->orderBy('created_at', 'desc')
        ->get();
    
    $stats = [
        'total' => Letter::where('sender_id', $user->id)->count() + $leaveLetters->count(),
        'draft' => Letter::where('sender_id', $user->id)->where('stage', 'draft')->count(),
        'pending_approval' => Letter::where('sender_id', $user->id)->where('stage', 'pending_approval')->count(),
        'approved' => Letter::where('sender_id', $user->id)->where('stage', 'approved')->count(),
        'signed' => Letter::where('sender_id', $user->id)->where('stage', 'signed')->count(),
    ];
    
    return view('letters.index', compact('letters', 'stats', 'leaveLetters'));
}
    
    /**
     * Show the form for creating a new letter
     */
    public function create()
    {
        $referenceFormats = ReferenceFormat::where('is_active', true)->get();
        $recipients = User::where('status', 'active')->get();
        
        return view('letters.create', compact('referenceFormats', 'recipients'));
    }
    
    /**
     * Store a newly created letter
     */
    public function store(Request $request)
    {
        // Validate based on recipient type
        $rules = [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'reference_format_id' => 'nullable|exists:reference_formats,id',
            'your_ref' => 'nullable|string|max:255',
            'type' => 'nullable|string',
            'cc_recipients' => 'nullable|array',
            'cc_recipients.*' => 'exists:users,id',
            'auto_number_paragraphs' => 'boolean',
            'action' => 'nullable|string|in:submit',
            'recipient_type' => 'required|in:staff,custom,multiple',
        ];
        
        // Add conditional validation based on recipient_type
        if ($request->recipient_type === 'staff') {
            $rules['recipient_id'] = 'required|exists:users,id';
        } elseif ($request->recipient_type === 'custom') {
            $rules['recipient_address'] = 'required|string|max:1000';
        } elseif ($request->recipient_type === 'multiple') {
            $rules['recipients'] = 'required|array|min:1';
            $rules['recipients.*.name'] = 'required|string|max:255';
            $rules['recipients.*.address'] = 'nullable|string|max:1000';
        }
        
        $validated = $request->validate($rules);
        
        // Store raw content (user input without formatting)
        $rawContent = $validated['content'];
        
        // Format content if auto-numbering is enabled
        $autoNumber = $request->has('auto_number_paragraphs');
        $formattedContent = $autoNumber ? $this->formatParagraphs($rawContent) : $rawContent;
        
        // Determine recipient data based on type
        $recipientType = $request->recipient_type;
        $useCustomRecipient = false;
        $hasMultipleRecipients = false;
        $recipientId = null;
        $recipientAddress = null;
        $recipientName = null;
        $multipleRecipients = null;
        
        if ($recipientType === 'staff') {
            $recipientId = $validated['recipient_id'];
        } elseif ($recipientType === 'custom') {
            $useCustomRecipient = true;
            $recipientAddress = $validated['recipient_address'];
            // Extract recipient name from custom address (first line)
            if ($recipientAddress) {
                $lines = explode("\n", trim($recipientAddress));
                $recipientName = $lines[0] ?? null;
            }
        } elseif ($recipientType === 'multiple') {
            $hasMultipleRecipients = true;
            $multipleRecipients = json_encode($validated['recipients']);
        }
        
        DB::beginTransaction();
        
        try {
            $letter = Letter::create([
                'letterable_type' => 'App\\Models\\User',
                'letterable_id' => Auth::id(),
                'title' => $validated['title'],
                'content' => $formattedContent,
                'raw_content' => $rawContent,
                'auto_number_paragraphs' => $autoNumber,
                'type' => $validated['type'] ?? 'general',
                'sender_id' => Auth::id(),
                'recipient_id' => $recipientId,
                'recipient_address' => $recipientAddress,
                'use_custom_recipient' => $useCustomRecipient,
                'recipient_name' => $recipientName,
                'has_multiple_recipients' => $hasMultipleRecipients,
                'multiple_recipients' => $multipleRecipients,
                'your_ref' => $validated['your_ref'] ?? null,
                'cc_recipients' => json_encode($request->input('cc_recipients', [])),
                'stage' => ($request->input('action') === 'submit') ? 'pending_approval' : 'draft',
                'generated_by' => Auth::user()->full_name,
                'generated_by_role' => Auth::user()->getRoleNames()->first(),
            ]);
            
            // Generate reference number if format selected
            if (!empty($validated['reference_format_id'])) {
                $this->generateReferenceNumber($letter, $validated['reference_format_id']);
            }
            
            // If submitted, notify approvers
            if ($request->input('action') === 'submit') {
                // For staff recipients, notify their HOD
                if ($recipientType === 'staff' && $recipientId) {
                    $recipient = User::find($recipientId);
                    $hod = $recipient->department->hod ?? null;
                    if ($hod) {
                        notifyLetterApprover($letter, $hod->id, 'hod');
                    }
                }
                // For multiple recipients, no automatic approval flow
                notifyLetterSender($letter, 'submitted');
            }
            
            DB::commit();
            
            $message = ($request->input('action') === 'submit') 
                ? 'Letter submitted for approval successfully!' 
                : 'Letter saved as draft successfully!';
            
            return redirect()->route('letters.show', $letter->id)
                ->with('success', $message);
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create letter: ' . $e->getMessage());
        }
    }
    
    /**
     * Display the specified letter
     */
    public function show($id)
    {
        $letter = Letter::with(['sender', 'recipient', 'letterable', 'hodApprover', 'deanApprover', 'dvcApprover', 'vcSigner'])->findOrFail($id);
        $university = University::first();
        
        // Check permission
        if (!$this->canViewLetter($letter)) {
            abort(403, 'You do not have permission to view this letter.');
        }
        
        return view('letters.show', compact('letter', 'university'));
    }
    
    /**
     * Show the form for editing a letter
     */
    public function edit($id)
    {
        $letter = Letter::findOrFail($id);
        
        // Only draft letters can be edited
        if ($letter->stage !== 'draft') {
            return redirect()->route('letters.show', $letter->id)
                ->with('error', 'Only draft letters can be edited.');
        }
        
        // Check permission
        if ($letter->sender_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            abort(403, 'You do not have permission to edit this letter.');
        }
        
        $referenceFormats = ReferenceFormat::where('is_active', true)->get();
        $recipients = User::where('status', 'active')->get();
        
        return view('letters.edit', compact('letter', 'referenceFormats', 'recipients'));
    }
    
    /**
     * Update the specified letter
     */
    public function update(Request $request, $id)
    {
        $letter = Letter::findOrFail($id);
        
        // Only draft letters can be updated
        if ($letter->stage !== 'draft') {
            return redirect()->route('letters.show', $letter->id)
                ->with('error', 'Only draft letters can be updated.');
        }
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'recipient_id' => 'nullable|exists:users,id',
            'recipient_address' => 'nullable|string|max:1000',
            'recipients' => 'nullable|array',
            'recipients.*.name' => 'required_with:recipients|string|max:255',
            'recipients.*.address' => 'nullable|string|max:1000',
            'reference_format_id' => 'nullable|exists:reference_formats,id',
            'your_ref' => 'nullable|string',
            'cc_recipients' => 'nullable|array',
            'auto_number_paragraphs' => 'boolean',
            'recipient_type' => 'required|in:staff,custom,multiple',
        ]);
        
        // Format content if auto-numbering is enabled
        $autoNumber = $request->has('auto_number_paragraphs');
        $formattedContent = $autoNumber ? $this->formatParagraphs($validated['content']) : $validated['content'];
        
        $updateData = [
            'title' => $validated['title'],
            'content' => $formattedContent,
            'raw_content' => $validated['content'],
            'auto_number_paragraphs' => $autoNumber,
            'your_ref' => $validated['your_ref'] ?? null,
            'cc_recipients' => json_encode($request->input('cc_recipients', [])),
        ];
        
        // Handle recipient based on type
        if ($request->recipient_type === 'staff') {
            $updateData['recipient_id'] = $validated['recipient_id'];
            $updateData['recipient_address'] = null;
            $updateData['use_custom_recipient'] = false;
            $updateData['recipient_name'] = null;
            $updateData['has_multiple_recipients'] = false;
            $updateData['multiple_recipients'] = null;
        } elseif ($request->recipient_type === 'custom') {
            $updateData['recipient_id'] = null;
            $updateData['recipient_address'] = $validated['recipient_address'];
            $updateData['use_custom_recipient'] = true;
            $updateData['has_multiple_recipients'] = false;
            $updateData['multiple_recipients'] = null;
            
            // Extract name from address
            if ($updateData['recipient_address']) {
                $lines = explode("\n", trim($updateData['recipient_address']));
                $updateData['recipient_name'] = $lines[0] ?? null;
            }
        } elseif ($request->recipient_type === 'multiple') {
            $updateData['recipient_id'] = null;
            $updateData['recipient_address'] = null;
            $updateData['use_custom_recipient'] = false;
            $updateData['recipient_name'] = null;
            $updateData['has_multiple_recipients'] = true;
            $updateData['multiple_recipients'] = json_encode($validated['recipients']);
        }
        
        $letter->update($updateData);
        
        // Generate reference number if format selected and not already generated
        if (!empty($validated['reference_format_id']) && empty($letter->letter_number)) {
            $this->generateReferenceNumber($letter, $validated['reference_format_id']);
        }
        
        return redirect()->route('letters.show', $letter->id)
            ->with('success', 'Letter updated successfully!');
    }
    
    /**
     * Submit letter for approval
     */
    public function submit($id)
    {
        $letter = Letter::findOrFail($id);
        
        // Check permission
        if ($letter->sender_id !== Auth::id()) {
            abort(403, 'You cannot submit this letter.');
        }
        
        // Only draft letters can be submitted
        if ($letter->stage !== 'draft') {
            return back()->with('error', 'This letter has already been submitted.');
        }
        
        // Ensure reference number exists
        if (empty($letter->letter_number)) {
            return back()->with('error', 'Please generate a reference number before submitting.');
        }
        
        $letter->update([
            'stage' => 'pending_approval',
            'submitted_at' => now(),
        ]);
        
        // Notify approver based on recipient type
        if (!$letter->use_custom_recipient && !$letter->has_multiple_recipients && $letter->recipient_id) {
            $recipient = $letter->recipient;
            $hod = $recipient->department->hod ?? null;
            if ($hod) {
                notifyLetterApprover($letter, $hod->id, 'hod');
            }
        }
        notifyLetterSender($letter, 'submitted');
        
        return redirect()->route('letters.show', $letter->id)
            ->with('success', 'Letter submitted for approval successfully!');
    }
    
    /**
     * HOD Approval (Button click + IP logging)
     */
    public function hodApprove(Request $request, $id)
    {
        $letter = Letter::findOrFail($id);
        $user = Auth::user();
        
        // Check if letter has a staff recipient (not external or multiple)
        if ($letter->use_custom_recipient || $letter->has_multiple_recipients || !$letter->recipient_id) {
            abort(403, 'This letter does not require HOD approval.');
        }
        
        // Check if user is HOD of recipient's department
        $recipientDept = $letter->recipient->department_id;
        
        if ($user->department_id !== $recipientDept || !$user->hasRole('hod')) {
            abort(403, 'You are not authorized to approve this letter.');
        }
        
        // Check if letter is at correct stage
        if ($letter->stage !== 'pending_approval' || $letter->hod_approved_at) {
            return back()->with('error', 'This letter cannot be approved at this stage.');
        }
        
        $letter->update([
            'hod_approved_at' => now(),
            'hod_approved_by' => $user->id,
            'hod_ip_address' => $request->ip(),
            'hod_user_agent' => $request->userAgent(),
        ]);
        
        // Notify Dean
        $dean = $letter->recipient->department->school->dean ?? null;
        if ($dean) {
            notifyLetterApprover($letter, $dean->id, 'dean');
        }
        
        // Notify sender
        notifyLetterSender($letter, 'hod_approved');
        
        return redirect()->route('letters.pending.approval')
            ->with('success', 'Letter approved by HOD. Sent to Dean for approval.');
    }
    
    /**
     * Dean Approval (Button click + IP logging)
     */
    public function deanApprove(Request $request, $id)
    {
        $letter = Letter::findOrFail($id);
        $user = Auth::user();
        
        // Check if letter has a staff recipient (not external or multiple)
        if ($letter->use_custom_recipient || $letter->has_multiple_recipients || !$letter->recipient_id) {
            abort(403, 'This letter does not require Dean approval.');
        }
        
        if (!$user->hasRole('dean')) {
            abort(403, 'You are not authorized to approve this letter.');
        }
        
        // Check if letter is at correct stage
        if ($letter->stage !== 'pending_approval' || !$letter->hod_approved_at || $letter->dean_approved_at) {
            return back()->with('error', 'This letter cannot be approved at this stage.');
        }
        
        $letter->update([
            'dean_approved_at' => now(),
            'dean_approved_by' => $user->id,
            'dean_ip_address' => $request->ip(),
            'dean_user_agent' => $request->userAgent(),
        ]);
        
        // Determine which DVC based on staff category
        $dvcRole = $letter->recipient->staff_category === 'academic' ? 'dvca' : 'dvc-pfa';
        $dvc = User::role($dvcRole)->first();
        
        if ($dvc) {
            notifyLetterApprover($letter, $dvc->id, $dvcRole);
        }
        
        // Notify sender
        notifyLetterSender($letter, 'dean_approved');
        
        return redirect()->route('letters.pending.approval')
            ->with('success', 'Letter approved by Dean. Sent to DVC for approval.');
    }
    
    /**
     * DVC Approval (Button click + IP logging)
     */
    public function dvcApprove(Request $request, $id)
    {
        $letter = Letter::findOrFail($id);
        $user = Auth::user();
        $role = $user->getRoleNames()->first();
        
        // Check if letter has a staff recipient (not external or multiple)
        if ($letter->use_custom_recipient || $letter->has_multiple_recipients || !$letter->recipient_id) {
            abort(403, 'This letter does not require DVC approval.');
        }
        
        $expectedRole = $letter->recipient->staff_category === 'academic' ? 'dvca' : 'dvc-pfa';
        
        if ($role !== $expectedRole) {
            abort(403, 'You are not authorized to approve this letter.');
        }
        
        // Check if letter is at correct stage
        if ($letter->stage !== 'pending_approval' || !$letter->hod_approved_at || !$letter->dean_approved_at || $letter->dvc_approved_at) {
            return back()->with('error', 'This letter cannot be approved at this stage.');
        }
        
        $letter->update([
            'dvc_approved_at' => now(),
            'dvc_approved_by' => $user->id,
            'dvc_ip_address' => $request->ip(),
            'dvc_user_agent' => $request->userAgent(),
            'stage' => 'approved',
        ]);
        
        // Notify VC for signature
        $vc = User::role('vice-chancellor')->first();
        if ($vc) {
            notifyLetterApprover($letter, $vc->id, 'vice-chancellor');
        }
        
        // Notify sender
        notifyLetterSender($letter, 'dvc_approved');
        
        return redirect()->route('letters.pending.approval')
            ->with('success', 'Letter approved by DVC. Sent to VC for signature.');
    }
    
    /**
     * VC Signature (Password + IP logging)
     */
    public function vcSign(Request $request, $id)
    {
        $letter = Letter::findOrFail($id);
        $user = Auth::user();
        
        $request->validate([
            'signature_pin' => 'required|string',
        ]);
        
        // Verify PIN
        if (!Hash::check($request->signature_pin, $user->signature_pin)) {
            return back()->with('error', 'Invalid signature PIN. Please try again.');
        }
        
        // Check if letter is at correct stage
        if ($letter->stage !== 'approved' || $letter->vc_signed_at) {
            return back()->with('error', 'This letter cannot be signed at this stage.');
        }
        
        // Generate document hash for verification
        $documentHash = hash('sha256', 
            $letter->content . 
            $letter->letter_number . 
            now()->toDateTimeString()
        );
        
        $letter->update([
            'vc_signed_at' => now(),
            'vc_signed_by' => $user->id,
            'vc_ip_address' => $request->ip(),
            'vc_user_agent' => $request->userAgent(),
            'document_hash' => $documentHash,
            'stage' => 'signed',
            'signatory' => $user->full_name,
            'signatory_title' => 'Vice Chancellor',
            'signed_at' => now(),
            'signed_by' => $user->id,
        ]);
        
        // Notify sender
        notifyLetterSender($letter, 'signed');
        
        // Notify all approvers (if any)
        $approverIds = [];
        if ($letter->hod_approved_by) $approverIds[] = $letter->hod_approved_by;
        if ($letter->dean_approved_by) $approverIds[] = $letter->dean_approved_by;
        if ($letter->dvc_approved_by) $approverIds[] = $letter->dvc_approved_by;
        
        if (!empty($approverIds)) {
            notifyLetterCompleted($letter, $approverIds);
        }
        
        return redirect()->route('letters.show', $letter->id)
            ->with('success', 'Letter signed successfully! It is now official and verifiable.');
    }
    
    /**
     * Reject a letter
     */
    public function reject(Request $request, $id)
    {
        $letter = Letter::findOrFail($id);
        $user = Auth::user();
        
        $request->validate([
            'rejection_reason' => 'required|string|min:5|max:500',
        ]);
        
        // Determine who can reject
        $canReject = false;
        $rejectionLevel = null;
        
        if ($user->hasRole('hod') && !$letter->hod_approved_at && !$letter->use_custom_recipient && !$letter->has_multiple_recipients) {
            $canReject = true;
            $rejectionLevel = 'hod';
        } elseif ($user->hasRole('dean') && $letter->hod_approved_at && !$letter->dean_approved_at && !$letter->use_custom_recipient && !$letter->has_multiple_recipients) {
            $canReject = true;
            $rejectionLevel = 'dean';
        } elseif ($user->hasRole(['dvca', 'dvc-pfa']) && $letter->dean_approved_at && !$letter->dvc_approved_at && !$letter->use_custom_recipient && !$letter->has_multiple_recipients) {
            $canReject = true;
            $rejectionLevel = 'dvc';
        } elseif ($user->hasRole('vice-chancellor') && $letter->dvc_approved_at && !$letter->vc_signed_at) {
            $canReject = true;
            $rejectionLevel = 'vc';
        } elseif ($user->hasRole('admin')) {
            $canReject = true;
            $rejectionLevel = 'admin';
        }
        
        if (!$canReject) {
            return back()->with('error', 'You are not authorized to reject this letter at this stage.');
        }
        
        $letter->update([
            'stage' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
            'rejected_at' => now(),
            'rejected_by' => $user->id,
            'rejection_level' => $rejectionLevel,
        ]);
        
        // Notify sender
        sendNotification(
            $letter->sender_id,
            'letter_rejected',
            '❌ Letter Rejected',
            "Your letter '{$letter->title}' has been rejected. Reason: {$request->rejection_reason}",
            route('letters.show', $letter->id),
            'View Letter'
        );
        
        return redirect()->route('letters.index')
            ->with('success', 'Letter rejected successfully.');
    }
    
    /**
     * Print a letter
     */
    public function print($id)
    {
        $letter = Letter::with(['sender', 'recipient'])->findOrFail($id);
        $university = University::first();
        
        // Mark as printed
        $letter->markAsPrinted();
        
        return view('letters.print', compact('letter', 'university'));
    }
    
    /**
     * Delete a letter (draft only)
     */
    public function destroy($id)
    {
        $letter = Letter::findOrFail($id);
        
        // Only draft letters can be deleted
        if ($letter->stage !== 'draft') {
            return back()->with('error', 'Only draft letters can be deleted.');
        }
        
        $letter->delete();
        
        return redirect()->route('letters.index')
            ->with('success', 'Letter deleted successfully!');
    }
    
    /**
 * Display pending approvals (for approvers)
 */
public function pendingApproval()
{
    $user = Auth::user();
    $role = $user->getRoleNames()->first();
    
    // If user is admin, show all pending letters
    if ($role === 'admin') {
        $letters = Letter::where('stage', 'pending_approval')
            ->where('use_custom_recipient', false)
            ->where('has_multiple_recipients', false)
            ->whereNotNull('recipient_id')
            ->orderBy('submitted_at', 'asc')
            ->paginate(15);
        
        return view('letters.pending-approval', compact('letters'));
    }
    
    // If user is HOD, Dean, or DVC
    if (!in_array($role, ['hod', 'dean', 'dvca', 'dvc-pfa'])) {
        return redirect()->route('dashboard')->with('error', 'You are not authorized to view pending approvals.');
    }
    
    $query = Letter::where('stage', 'pending_approval')
        ->where('use_custom_recipient', false)
        ->where('has_multiple_recipients', false)
        ->whereNotNull('recipient_id');
    
    // Filter based on role
    if ($role === 'hod') {
        $query->whereNull('hod_approved_at')
              ->whereHas('recipient', function($q) use ($user) {
                  $q->where('department_id', $user->department_id);
              });
    } elseif ($role === 'dean') {
        $query->whereNotNull('hod_approved_at')
              ->whereNull('dean_approved_at');
    } elseif (in_array($role, ['dvca', 'dvc-pfa'])) {
        $query->whereNotNull('hod_approved_at')
              ->whereNotNull('dean_approved_at')
              ->whereNull('dvc_approved_at');
    }
    
    $letters = $query->orderBy('submitted_at', 'asc')->paginate(15);
    
    return view('letters.pending-approval', compact('letters'));
}
    
    /**
     * Display pending signatures (for VC)
     */
    public function pendingSignature()
    {
        $user = Auth::user();
        $role = $user->getRoleNames()->first();
        
        if ($role !== 'vice-chancellor') {
            abort(403, 'You are not authorized to sign letters.');
        }
        
        $letters = Letter::where('stage', 'approved')
            ->whereNotNull('hod_approved_at')
            ->whereNotNull('dean_approved_at')
            ->whereNotNull('dvc_approved_at')
            ->whereNull('vc_signed_at')
            ->orderBy('approved_at', 'asc')
            ->paginate(15);
        
        return view('letters.pending-signature', compact('letters'));
    }
    
    /**
     * View a letter (public verification) - YOUR EXISTING METHOD
     */
    public function view($id)
    {
        $letter = LeaveLetter::with('leaveRequest.staff')->findOrFail($id);
        $leaveRequest = $letter->leaveRequest;
        $university = University::first();
        
        return view('components.letter.leave-approval', [
            'leaveRequest' => $leaveRequest,
            'staff' => $leaveRequest->staff,
            'university' => $university,
            'letterNumber' => $letter->letter_number,
            'secureCode' => $letter->secure_code,
            'signatory' => $letter->generated_by ?? 'Human Resources Department',
            'signatoryTitle' => $letter->generated_by_role ?? 'HR Officer',
            'cc' => [
                'Head of Department - ' . ($leaveRequest->staff->department->name ?? 'N/A'),
                'Dean - ' . ($leaveRequest->staff->department->school->name ?? 'Faculty'),
                'HR Department',
                'DVC Academic'
            ]
        ]);
    }
    
    /**
     * Verify letter by secure code (public) - YOUR EXISTING METHOD
     */
    public function verify($code)
    {
        $letter = LeaveLetter::where('secure_code', $code)->firstOrFail();
        
        // Mark as verified
        $letter->markAsVerified(request()->ip());
        
        $leaveRequest = $letter->leaveRequest;
        $university = University::first();
        
        return view('components.letter.leave-approval', [
            'leaveRequest' => $leaveRequest,
            'staff' => $leaveRequest->staff,
            'university' => $university,
            'letterNumber' => $letter->letter_number,
            'secureCode' => $letter->secure_code,
            'signatory' => $letter->generated_by ?? 'Human Resources Department',
            'signatoryTitle' => $letter->generated_by_role ?? 'HR Officer',
            'cc' => [
                'Head of Department - ' . ($leaveRequest->staff->department->name ?? 'N/A'),
                'Dean - ' . ($leaveRequest->staff->department->school->name ?? 'Faculty'),
                'HR Department',
                'DVC Academic'
            ]
        ]);
    }
    
    /**
     * Download letter as PDF - YOUR EXISTING METHOD
     */
    public function downloadPdf($id)
    {
        // Try new Letter model first
        $letter = Letter::find($id);
        
        if ($letter) {
            $university = University::first();
            $letter->markAsPrinted();
            
            $html = view('letters.print', compact('letter', 'university'))->render();
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html);
            return $pdf->download('letter-' . $letter->letter_number . '.pdf');
        }
        
        // Fallback to old LeaveLetter model
        $letter = LeaveLetter::with('leaveRequest.staff')->findOrFail($id);
        $letter->markAsPrinted();
        
        $leaveRequest = $letter->leaveRequest;
        $university = University::first();
        
        $html = view('components.letter.leave-approval', [
            'leaveRequest' => $leaveRequest,
            'staff' => $leaveRequest->staff,
            'university' => $university,
            'letterNumber' => $letter->letter_number,
            'secureCode' => $letter->secure_code,
            'signatory' => $letter->generated_by ?? 'Human Resources Department',
            'signatoryTitle' => $letter->generated_by_role ?? 'HR Officer',
            'cc' => [
                'Head of Department - ' . ($leaveRequest->staff->department->name ?? 'N/A'),
                'Dean - ' . ($leaveRequest->staff->department->school->name ?? 'Faculty'),
                'HR Department',
                'DVC Academic'
            ]
        ])->render();
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html);
        return $pdf->download('leave-letter-' . $letter->letter_number . '.pdf');
    }
    
    // ============================================
    // PRIVATE HELPER METHODS
    // ============================================
    
    /**
     * Format paragraphs with automatic numbering
     */
    private function formatParagraphs($content)
    {
        $paragraphs = preg_split('/\n\s*\n/', trim($content));
        $formatted = [];
        
        foreach ($paragraphs as $index => $paragraph) {
            $paragraph = trim($paragraph);
            if (empty($paragraph)) continue;
            
            if ($index === 0) {
                $formatted[] = $paragraph;
            } else {
                $formatted[] = ($index + 1) . ". " . $paragraph;
            }
        }
        
        return implode("\n\n", $formatted);
    }
    
    /**
     * Generate reference number for letter
     */
    private function generateReferenceNumber($letter, $formatId)
    {
        $format = ReferenceFormat::find($formatId);
        
        if (!$format) {
            return;
        }
        
        $file = LetterFile::where('reference_format_id', $format->id)
            ->where('is_active', true)
            ->first();
        
        if (!$file || !$file->hasCapacity()) {
            if ($format->auto_create_new_file) {
                $file = $this->createNewFile($format);
            } else {
                return;
            }
        }
        
        $newSerial = $file->current_serial + 1;
        $serial = str_pad($newSerial, $format->serial_length, '0', STR_PAD_LEFT);
        $year = date('Y');
        
        $reference = str_replace(
            ['{year}', '{serial}', '{prefix}', '{type}', '{entity_code}'],
            [$year, $serial, $this->getFormatPrefix($format), $this->getDocumentTypeCode($letter->type), $this->getEntityCode($format)],
            $format->format_pattern
        );
        
        $settings = \App\Models\LetterSetting::getForUniversity($format->university_id);
        $codeLength = $settings->secure_code_length ?? 12;
        $random = strtoupper(substr(md5(uniqid()), 0, $codeLength));
        $prefix = $settings->secure_code_prefix ?? '';
        $secureCode = $prefix . $random . $serial;
        
        $letter->update([
            'letter_number' => $reference,
            'secure_code' => $secureCode,
            'our_ref_format_id' => $format->id,
            'file_number' => $file->file_number,
        ]);
        
        $file->increment('current_serial');
    }
    
    /**
     * Get document type code for reference number
     */
    private function getDocumentTypeCode($type)
    {
        $codes = [
            'general' => 'GEN',
            'application' => 'APP',
            'request' => 'REQ',
            'approval' => 'APR',
            'notification' => 'NTF',
            'circular' => 'CIR',
        ];
        
        return $codes[$type] ?? 'LTR';
    }
    
    /**
     * Get prefix from format
     */
    private function getFormatPrefix($format)
    {
        if ($format->entity_type === 'department' && $format->entity) {
            return $format->entity->code ?? 'DEPT';
        }
        if ($format->entity_type === 'school' && $format->entity) {
            return $format->entity->code ?? 'SCH';
        }
        return 'GEN';
    }
    
    /**
     * Get entity code for reference format
     */
    private function getEntityCode($format)
    {
        if ($format->entity_type === 'department' && $format->entity) {
            return $format->entity->code ?? 'DEPT';
        }
        if ($format->entity_type === 'school' && $format->entity) {
            return $format->entity->code ?? 'SCH';
        }
        return 'GEN';
    }
    
    /**
     * Create new physical file for reference format
     */
    private function createNewFile($format)
    {
        $lastFile = LetterFile::where('reference_format_id', $format->id)
            ->orderBy('id', 'desc')
            ->first();
        
        $pattern = $format->file_naming_pattern ?? '{prefix}-VOL-{number:03d}';
        $lastNumber = $lastFile ? $this->extractNumber($lastFile->file_number) : 0;
        $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        $fileNumber = str_replace('{number:03d}', $newNumber, $pattern);
        $fileNumber = str_replace('{prefix}', $this->getFormatPrefix($format), $fileNumber);
        
        $startSerial = $lastFile ? $lastFile->end_serial + 1 : $format->serial_start;
        $endSerial = $format->file_capacity > 0 ? $startSerial + $format->file_capacity - 1 : null;
        
        return LetterFile::create([
            'reference_format_id' => $format->id,
            'file_number' => $fileNumber,
            'file_name' => $format->name . ' - Volume ' . ($lastNumber + 1),
            'start_serial' => $startSerial,
            'end_serial' => $endSerial,
            'current_serial' => 0,
            'is_active' => true,
            'opened_at' => now(),
            'created_by' => Auth::id(),
        ]);
    }
    
    /**
     * Extract number from file number string
     */
    private function extractNumber($fileNumber)
    {
        preg_match('/\d+/', $fileNumber, $matches);
        return isset($matches[0]) ? (int)$matches[0] : 0;
    }
    
    /**
     * Check if user can view letter
     */
    private function canViewLetter($letter)
    {
        $user = Auth::user();
        $role = $user->getRoleNames()->first();
        
        if (in_array($role, ['admin', 'registry-officer'])) {
            return true;
        }
        
        if ($letter->sender_id === $user->id || $letter->recipient_id === $user->id) {
            return true;
        }
        
        return false;
    }
}