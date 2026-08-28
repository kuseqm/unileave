<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\User;
use App\Models\LeaveBalance;
use App\Models\ApprovalLog;
use App\Models\HoldPositionHistory;
use App\Models\Document;
use App\Models\ActingDvcAssignment;
use App\Models\University;
use App\Models\Letter;
use App\Helpers\AuditLogHelper; 
use App\Traits\PerPageTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class LeaveRequestController extends Controller
{
    use PerPageTrait;

    /**
     * Display staff's own leave requests
     */
    public function myRequests(Request $request)
    {
        $perPage = $this->getPerPage($request, 10);
        
        $leaveRequests = LeaveRequest::with(['leaveType', 'actingStaff'])
            ->where('staff_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
        
        $stats = [
            'total' => LeaveRequest::where('staff_id', auth()->id())->count(),
            'pending' => LeaveRequest::where('staff_id', auth()->id())->where('status', 'pending')->count(),
            'approved' => LeaveRequest::where('staff_id', auth()->id())->where('status', 'approved')->count(),
            'rejected' => LeaveRequest::where('staff_id', auth()->id())->where('status', 'rejected')->count(),
        ];

        return view('shared.leave-requests.my-requests', compact('leaveRequests', 'stats'));
    }

    /**
     * Staff - Create Request Form
     */
    public function create()
    {
        $leaveTypes = LeaveType::where('status', 'active')->get();
        
        $colleagues = User::where('department_id', auth()->user()->department_id)
            ->where('id', '!=', auth()->id())
            ->where('status', 'active')
            ->get();
        
        return view('shared.leave-requests.create', compact('leaveTypes', 'colleagues'));
    }

    /**
     * Staff - Store Request
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'leave_type_id' => 'required|exists:leave_types,id',
                'start_date' => 'required|date|after_or_equal:today',
                'end_date' => 'required|date|after_or_equal:start_date',
                'reason' => 'required|string|min:10',
                'acting_staff_id' => 'required|exists:staff,id|different:staff_id',
                'half_day' => 'nullable|in:full,morning,afternoon',
                'expected_return_date' => 'nullable|date|after_or_equal:end_date',
                'emergency_contact_name' => 'nullable|string|max:100',
                'emergency_contact_phone' => 'nullable|string|max:20',
                'contact_number_during_leave' => 'nullable|string|max:20',
                'leave_address' => 'nullable|string|max:255',
                'travel_destination' => 'nullable|string|max:255',
                'handover_notes' => 'nullable|string',
                'supporting_documents.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120',
                'benefit_to_university' => 'nullable|string',
                'cost_coverage' => 'nullable|in:organizers_full,organizers_partial,university,private',
                'is_first_time_applicant' => 'nullable|boolean',
                'letter_content' => 'nullable|string',
            ]);

            // BLACKOUT PERIOD CHECK
            $blackout = \App\Models\BlackoutPeriod::where('start_date', '<=', $request->end_date)
                ->where('end_date', '>=', $request->start_date)
                ->where('status', 'active')
                ->first();

            if ($blackout && $blackout->restriction_type == 'hard') {
                return redirect()->back()
                    ->with('error', "Cannot apply for leave during blackout period: {$blackout->name} (From {$blackout->start_date} to {$blackout->end_date})")
                    ->withInput();
            }

            $startDate = \Carbon\Carbon::parse($request->start_date);
            $endDate = \Carbon\Carbon::parse($request->end_date);
            
            $days = $startDate->diffInWeekdays($endDate) + 1;
            $halfDay = $request->half_day ?? 'full';
            
            if ($halfDay !== 'full') {
                $days = $days - 0.5;
            }

            DB::beginTransaction();
            
            $nextLevel = LeaveRequest::LEVEL_HOD;
            
            $leaveRequest = LeaveRequest::create([
                'staff_id' => auth()->id(),
                'acting_staff_id' => $request->acting_staff_id,
                'leave_type_id' => $request->leave_type_id,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'days' => $days,
                'reason' => $request->reason,
                'status' => 'pending',
                'acting_status' => 'pending',
                'acting_token' => Str::random(60),
                'current_approval_level' => $nextLevel,
                'letter_content' => $request->letter_content,
                'half_day' => $halfDay,
                'expected_return_date' => $request->expected_return_date,
                'emergency_contact_id' => $request->emergency_contact_id,
                'emergency_contact_name' => $request->emergency_contact_name,
                'emergency_contact_phone' => $request->emergency_contact_phone,
                'contact_number_during_leave' => $request->contact_number_during_leave,
                'leave_address' => $request->leave_address,
                'travel_destination' => $request->travel_destination,
                'handover_notes' => $request->handover_notes,
                'benefit_to_university' => $request->benefit_to_university,
                'cost_coverage' => $request->cost_coverage,
                'is_first_time_applicant' => $request->has('is_first_time_applicant'),
            ]);

            // Create draft letter
            if ($request->filled('letter_content')) {
                try {
                    $year = date('Y');
                    $lastLetter = \App\Models\LeaveLetter::whereYear('created_at', $year)->count();
                    $letterNumber = 'LTR/' . $year . '/' . str_pad($lastLetter + 1, 4, '0', STR_PAD_LEFT);
                    
                    $letter = \App\Models\LeaveLetter::create([
                        'leave_request_id' => $leaveRequest->id,
                        'staff_id' => auth()->id(),
                        'letter_number' => $letterNumber,
                        'secure_code' => 'SMU-LTR-' . strtoupper(substr(md5($leaveRequest->id . now()), 0, 8)),
                        'title' => 'Leave Approval Letter - ' . ($leaveRequest->leaveType->name ?? 'Leave Request'),
                        'content' => $request->letter_content,
                        'type' => 'approval',
                        'stage' => 'draft',
                        'generated_by' => auth()->user()->full_name,
                        'generated_by_role' => auth()->user()->getRoleNames()->first(),
                        'payment_status' => 'paid',
                        'signatories' => json_encode([]),
                        'cc_recipients' => json_encode([]),
                        'metadata' => json_encode([
                            'leave_type' => $leaveRequest->leaveType->name ?? null,
                            'days' => $days,
                            'start_date' => $request->start_date,
                            'end_date' => $request->end_date,
                        ]),
                    ]);
                    
                    \Log::info('Draft leave letter created', [
                        'leave_request_id' => $leaveRequest->id,
                        'letter_id' => $letter->id,
                        'letter_number' => $letterNumber
                    ]);
                    
                } catch (\Exception $e) {
                    \Log::error('Failed to create draft letter: ' . $e->getMessage());
                }
            }

            AuditLogHelper::log('create', 'leave', $leaveRequest->id, null, $leaveRequest->toArray());

            HoldPositionHistory::create([
                'leave_request_id' => $leaveRequest->id,
                'requester_staff_id' => auth()->id(),
                'holder_staff_id' => $request->acting_staff_id,
                'status' => 'pending',
                'requested_at' => now(),
                'token' => $leaveRequest->acting_token,
            ]);

            if ($request->hasFile('supporting_documents')) {
                foreach ($request->file('supporting_documents') as $file) {
                    $path = $file->store('leave_documents/' . $leaveRequest->id, 'public');
                    
                    Document::create([
                        'user_id' => auth()->id(),
                        'leave_request_id' => $leaveRequest->id,
                        'category_id' => null,
                        'file_name' => $file->getClientOriginalName(),
                        'file_path' => $path,
                        'file_type' => $file->getMimeType(),
                        'file_size' => $file->getSize(),
                        'is_verified' => false,
                    ]);
                }
            }

            $actingStaff = $leaveRequest->actingStaff;
            $requester = $leaveRequest->staff;
            
            \App\Models\Notification::create([
                'user_id' => $actingStaff->id,
                'type' => 'acting_request',
                'title' => 'Hold Position Request',
                'message' => "{$requester->first_name} {$requester->last_name} has requested you to hold their position during leave from " . 
                    \Carbon\Carbon::parse($leaveRequest->start_date)->format('d M Y') . " to " . 
                    \Carbon\Carbon::parse($leaveRequest->end_date)->format('d M Y'),
                'action_url' => route('leave-requests.acting.response', $leaveRequest->acting_token),
                'action_text' => 'Review Request',
                'is_read' => false,
            ]);
            
            DB::commit();

            $successMessage = 'Leave request submitted successfully! Waiting for acting staff approval.';
            if ($request->filled('letter_content')) {
                $successMessage .= ' Your letter draft has been saved.';
            }
            
            return redirect()->route('leave-requests.my-requests')
                ->with('success', $successMessage);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
            
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Leave request submission failed: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to submit request: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Staff - Show Request Details (Comprehensive view with timeline)
     */
    public function show($id)
    {
        $user = auth()->user();
        $role = $user->getRoleNames()->first();
        
        $authorizedRoles = ['admin', 'vice-chancellor', 'hod', 'dean', 'hr-officer', 'dvca', 'dvc-pfa'];
        
        $query = LeaveRequest::with([
            'leaveType', 
            'staff', 
            'actingStaff', 
            'approvalLogs.approver',
            'documents',
            'letters'
        ]);
        
        if (in_array($role, $authorizedRoles)) {
            $leaveRequest = $query->findOrFail($id);
        } else {
            $leaveRequest = $query->where(function($q) use ($user) {
                $q->where('staff_id', $user->id)
                  ->orWhere('acting_staff_id', $user->id);
            })->findOrFail($id);
        }
        
        $approvalLogs = $leaveRequest->approvalLogs;
        $totalApprovalTime = null;
        
        if ($approvalLogs && $approvalLogs->count() > 0) {
            $firstLog = $approvalLogs->first();
            $lastLog = $approvalLogs->last();
            if ($firstLog && $lastLog) {
                $totalMinutes = $firstLog->created_at->diffInMinutes($lastLog->created_at);
                if ($totalMinutes < 60) {
                    $totalApprovalTime = $totalMinutes . ' minutes';
                } elseif ($totalMinutes < 1440) {
                    $totalApprovalTime = round($totalMinutes / 60, 1) . ' hours';
                } else {
                    $totalApprovalTime = round($totalMinutes / 1440, 1) . ' days';
                }
            }
        }
        
        return view('shared.leave-requests.details', compact('leaveRequest', 'totalApprovalTime'));
    }

    /**
     * View the approval letter for a leave request
     */
    public function viewLetter($id)
    {
        $leaveRequest = LeaveRequest::with(['staff', 'leaveType', 'actingStaff', 'letters'])->findOrFail($id);
        
        $user = auth()->user();
        $role = $user->getRoleNames()->first();
        $authorizedRoles = ['admin', 'vice-chancellor', 'hod', 'dean', 'hr-officer', 'dvca', 'dvc-pfa'];
        
        if (!in_array($role, $authorizedRoles) && $leaveRequest->staff_id !== $user->id) {
            abort(403, 'You do not have permission to view this letter.');
        }
        
        if ($leaveRequest->status !== 'approved') {
            return redirect()->route('leave-requests.show', $leaveRequest->id)
                ->with('error', 'Letter is only available after the leave request is approved.');
        }
        
        $letter = $leaveRequest->letters()->latest()->first();
        
        if (!$letter) {
            return redirect()->route('leave-requests.show', $leaveRequest->id)
                ->with('error', 'Approval letter not found. Please contact HR.');
        }
        
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
     * Acting Staff Response Page
     */
    public function actingResponse($token)
    {
        $leaveRequest = LeaveRequest::where('acting_token', $token)
            ->where('acting_status', 'pending')
            ->firstOrFail();
        
        if (request()->ajax() || request()->expectsJson()) {
            return view('components.acting-response-modal', compact('leaveRequest'))->render();
        }
        
        return view('shared.leave-requests.acting-response', compact('leaveRequest'));
    }

    /**
     * Process Acting Staff Response
     */
    public function processActingResponse(Request $request, $id)
    {
        $leaveRequest = LeaveRequest::findOrFail($id);
        
        $request->validate([
            'action' => 'required|in:accept,decline',
            'comment' => 'nullable|string|max:500',
        ]);
        
        DB::beginTransaction();
        
        try {
            $history = HoldPositionHistory::where('leave_request_id', $leaveRequest->id)
                ->where('holder_staff_id', auth()->id())
                ->first();

            if ($request->action === 'accept') {
                $nextLevel = LeaveRequest::LEVEL_HOD;
                
                $leaveRequest->update([
                    'acting_status' => 'accepted',
                    'acting_responded_at' => now(),
                    'current_approval_level' => $nextLevel,
                    'status' => 'pending',
                ]);
                
                AuditLogHelper::log('approve', 'acting', $leaveRequest->id, 
                    ['acting_status' => 'pending', 'current_approval_level' => 'acting'],
                    ['acting_status' => 'accepted', 'current_approval_level' => $nextLevel]
                );
                
                $message = 'You have accepted to hold position. Request forwarded to HOD.';
                
                if ($history) {
                    $history->update([
                        'status' => 'accepted',
                        'responded_at' => now(),
                        'comment' => $request->comment,
                    ]);
                }

                \App\Models\Notification::create([
                    'user_id' => $leaveRequest->staff_id,
                    'type' => 'hold_accepted',
                    'title' => 'Hold Position Accepted',
                    'message' => $leaveRequest->actingStaff->first_name . ' ' . $leaveRequest->actingStaff->last_name . ' has accepted to hold your position.',
                    'action_url' => route('leave-requests.show', $leaveRequest->id),
                    'action_text' => 'View Request',
                    'is_read' => false,
                ]);
                
                $this->notifyNextApprover($leaveRequest, $nextLevel);
                
                $hod = $leaveRequest->staff->department->hod ?? null;
                if ($hod) {
                    \App\Models\Notification::create([
                        'user_id' => $hod->id,
                        'type' => 'approval_pending_urgent',
                        'title' => 'URGENT: Leave Request Pending Your Approval',
                        'message' => "URGENT: {$leaveRequest->staff->first_name} {$leaveRequest->staff->last_name}'s leave request has been accepted by acting staff and is now awaiting your approval as Head of Department.",
                        'action_url' => route('leave-requests.pending'),
                        'action_text' => 'Approve Now',
                        'is_read' => false,
                        'priority' => 'urgent',
                    ]);
                }
                
            } else {
                $leaveRequest->update([
                    'acting_status' => 'declined',
                    'acting_responded_at' => now(),
                    'status' => 'rejected',
                    'rejection_reason' => "Acting staff declined: " . ($request->comment ?? 'No reason provided'),
                ]);
                
                AuditLogHelper::log('reject', 'acting', $leaveRequest->id,
                    ['acting_status' => 'pending', 'status' => 'pending'],
                    ['acting_status' => 'declined', 'status' => 'rejected']
                );
                
                $message = 'You have declined to hold position. Leave request cancelled.';
                
                if ($history) {
                    $history->update([
                        'status' => 'declined',
                        'responded_at' => now(),
                        'comment' => $request->comment,
                    ]);
                }
                
                \App\Models\Notification::create([
                    'user_id' => $leaveRequest->staff_id,
                    'type' => 'hold_declined',
                    'title' => 'Hold Position Declined',
                    'message' => $leaveRequest->actingStaff->first_name . ' ' . $leaveRequest->actingStaff->last_name . ' has declined to hold your position. Your leave request has been cancelled.',
                    'action_url' => route('leave-requests.my-requests'),
                    'action_text' => 'View Requests',
                    'is_read' => false,
                ]);
            }
            
            DB::commit();
            
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json(['success' => true, 'message' => $message]);
            }
            
            return redirect()->route('dashboard')->with('success', $message);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to process response: ' . $e->getMessage());
        }
    }

    /**
     * Get next level after acting staff approval
     */
    private function getNextLevelAfterActing($actingStaffId)
    {
        return LeaveRequest::LEVEL_HOD;
    }

    /**
     * Get next approval level after current level
     */
    private function getNextLevelAfterApproval($currentLevel, $staff)
    {
        if ($currentLevel === null || !is_numeric($currentLevel)) {
            return LeaveRequest::LEVEL_HOD;
        }
        
        $sequence = [
            LeaveRequest::LEVEL_HOD => LeaveRequest::LEVEL_DEAN,
            LeaveRequest::LEVEL_DEAN => LeaveRequest::LEVEL_HR,
            LeaveRequest::LEVEL_HR => LeaveRequest::LEVEL_DVC,
            LeaveRequest::LEVEL_DVC => LeaveRequest::LEVEL_HR_LOGISTICS,
            LeaveRequest::LEVEL_HR_LOGISTICS => null,
        ];
        
        return $sequence[$currentLevel] ?? null;
    }

    /**
     * Pending Approvals (Role-based using numeric levels)
     */
    public function pendingApprovals(Request $request)
    {
        $perPage = $this->getPerPage($request, 10);
        $user = auth()->user();
        $role = $user->getRoleNames()->first();
        
        $query = LeaveRequest::with(['staff', 'leaveType', 'actingStaff'])
            ->where('status', 'pending')
            ->where('acting_status', 'accepted');
        
        switch ($role) {
            case 'vice-chancellor':
                break;
            case 'internal-auditor':
                break;
            case 'admin':
                break;
            case 'unit_head':
            case 'hod':
                $query->where('current_approval_level', LeaveRequest::LEVEL_HOD)
                    ->whereHas('staff.department', function($q) use ($user) {
                        $q->where('hod_staff_id', $user->id);
                    });
                break;
            case 'deputy-hod':
                $query->where('current_approval_level', LeaveRequest::LEVEL_HOD)
                    ->whereHas('staff.department', function($q) use ($user) {
                        $q->where('hod_staff_id', $user->department->hod_staff_id ?? 0);
                    });
                break;
            case 'dept-secretary':
                $query->where('current_approval_level', LeaveRequest::LEVEL_HOD)
                    ->whereHas('staff.department', function($q) use ($user) {
                        $q->where('id', $user->department_id);
                    });
                break;
            case 'dean':
                $query->where('current_approval_level', LeaveRequest::LEVEL_DEAN)
                    ->whereHas('staff.department.school', function($q) use ($user) {
                        $q->where('dean_staff_id', $user->id);
                    });
                break;
            case 'dvca':
                $query->where('current_approval_level', LeaveRequest::LEVEL_DVC)
                    ->whereHas('staff', function($q) {
                        $q->where('staff_category', 'academic');
                    });
                break;
            case 'dvc-pfa':
                $query->where('current_approval_level', LeaveRequest::LEVEL_DVC)
                    ->whereHas('staff', function($q) {
                        $q->whereIn('staff_category', ['administrative', 'supporting']);
                    });
                break;
            case 'hr-officer':
            case 'hr-manager':
                $query->whereIn('current_approval_level', [LeaveRequest::LEVEL_HR, LeaveRequest::LEVEL_HR_LOGISTICS]);
                break;
            default:
                return redirect()->route('dashboard');
        }
        
        $pendingRequests = $query->orderBy('created_at', 'asc')->paginate($perPage);
        
        return view('shared.leave-requests.pending-approvals', compact('pendingRequests'));
    }

    /**
     * Approve Request
     */
    public function approve(Request $request, $id)
    {
        $leaveRequest = LeaveRequest::findOrFail($id);
        
        $request->validate(['comment' => 'nullable|string|max:500']);
        
        $user = auth()->user();
        $currentLevel = $leaveRequest->current_approval_level;
        
        if (!$this->canUserApprove($user, $leaveRequest)) {
            return redirect()->back()->with('error', 'You are not authorized to approve this request.');
        }
        
        $nextLevel = $this->getNextLevelAfterApproval($currentLevel, $leaveRequest->staff);
        
        ApprovalLog::create([
            'request_type' => 'leave',
            'request_id' => $leaveRequest->id,
            'from_role' => $this->getLevelName($currentLevel),
            'to_role' => $nextLevel ? $this->getLevelName($nextLevel) : 'HR Logistics',
            'action' => 'approved',
            'comment' => $request->comment,
            'approver_staff_id' => $user->id,
            'ip_address' => $request->ip(),
        ]);
        
        if ($nextLevel) {
            $leaveRequest->update([
                'current_approval_level' => $nextLevel,
            ]);
            
            AuditLogHelper::log('approve', 'leave', $leaveRequest->id,
                ['current_approval_level' => $currentLevel],
                ['current_approval_level' => $nextLevel]
            );
            
            \App\Models\Notification::create([
                'user_id' => $leaveRequest->staff_id,
                'type' => 'approval_granted',
                'title' => 'Leave Request Approved at ' . $this->getLevelName($currentLevel),
                'message' => 'Your leave request has been approved by ' . $user->first_name . ' ' . $user->last_name,
                'action_url' => route('leave-requests.show', $leaveRequest->id),
                'action_text' => 'View Request',
                'is_read' => false,
            ]);
            
            $this->notifyNextApprover($leaveRequest, $nextLevel);
            
            return redirect()->back()->with('success', 'Leave request approved and forwarded to ' . $this->getLevelName($nextLevel) . '.');
            
        } else {
            return redirect()->back()->with('success', 'Leave request approved by DVC. Awaiting HR Logistics final processing.');
        }
    }

    /**
     * HR Logistics - Final processing page
     */
    public function hrLogistics($id)
    {
        $leaveRequest = LeaveRequest::with(['staff', 'leaveType', 'actingStaff', 'approvalLogs'])
            ->where('current_approval_level', LeaveRequest::LEVEL_HR_LOGISTICS)
            ->findOrFail($id);
        
        $university = \App\Models\University::first();
        
        return view('shared.leave-requests.hr-logistics', compact('leaveRequest', 'university'));
    }

    /**
     * Process HR Logistics - Final approval
     */
    public function processHrLogistics(Request $request, $id)
    {
        $leaveRequest = LeaveRequest::findOrFail($id);
        
        $request->validate([
            'hr_comment' => 'nullable|string|max:500',
        ]);
        
        DB::beginTransaction();
        
        try {
            $leaveRequest->update([
                'status' => 'approved',
                'current_approval_level' => null,
            ]);
            
            ApprovalLog::create([
                'request_type' => 'leave',
                'request_id' => $leaveRequest->id,
                'from_role' => 'HR Logistics',
                'to_role' => 'Completed',
                'action' => 'approved',
                'comment' => $request->hr_comment,
                'approver_staff_id' => auth()->id(),
                'ip_address' => $request->ip(),
            ]);
            
            AuditLogHelper::log('approve', 'leave', $leaveRequest->id,
                ['status' => 'pending'],
                ['status' => 'approved']
            );
            
            \App\Models\Notification::create([
                'user_id' => $leaveRequest->staff_id,
                'type' => 'approval_complete',
                'title' => 'Leave Request Fully Approved',
                'message' => 'Congratulations! Your leave request has been fully approved. HR has completed final processing.',
                'action_url' => route('leave-requests.show', $leaveRequest->id),
                'action_text' => 'View Details',
                'is_read' => false,
            ]);
            
            $this->updateLeaveBalance($leaveRequest);
            
            DB::commit();
            
            return redirect()->route('leave-requests.pending')
                ->with('success', 'Leave request fully approved and processed. Staff has been notified.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to process: ' . $e->getMessage());
        }
    }

    /**
     * Get level name for display
     */
    private function getLevelName($level)
    {
        $names = [
            LeaveRequest::LEVEL_HOD => 'HOD',
            LeaveRequest::LEVEL_DEAN => 'Dean',
            LeaveRequest::LEVEL_HR => 'HR',
            LeaveRequest::LEVEL_DVC => 'DVC',
            LeaveRequest::LEVEL_HR_LOGISTICS => 'HR Logistics',
        ];
        
        if (is_string($level)) {
            $stringNames = [
                'hod' => 'HOD',
                'dean' => 'Dean',
                'hr' => 'HR',
                'dvc' => 'DVC',
                'hr_logistics' => 'HR Logistics',
            ];
            return $stringNames[$level] ?? ucfirst($level);
        }
        
        return $names[$level] ?? 'Unknown';
    }

    /**
     * Check if user can approve a leave request
     */
    private function canUserApprove($user, $leaveRequest)
    {
        $role = $user->getRoleNames()->first();
        $currentLevel = $leaveRequest->current_approval_level;
        
        if (in_array($role, ['admin', 'vice-chancellor'])) {
            return true;
        }
        
        if ($role == 'internal-auditor') {
            return false;
        }
        
        if ($role == 'hod') {
            $isHodLevel = ($currentLevel == LeaveRequest::LEVEL_HOD) || ($currentLevel == 'hod' || $currentLevel == '1');
            if (!$isHodLevel) return false;
            $hodId = $leaveRequest->staff->department->hod_staff_id ?? null;
            return $hodId == $user->id;
        }
        
        if ($role == 'deputy-hod') {
            $isHodLevel = ($currentLevel == LeaveRequest::LEVEL_HOD) || ($currentLevel == 'hod' || $currentLevel == '1');
            if (!$isHodLevel) return false;
            if ($leaveRequest->staff->department_id != $user->department_id) return false;
            $hod = $leaveRequest->staff->department->hod;
            if (!$hod) return false;
            $hodOnLeave = LeaveRequest::where('staff_id', $hod->id)
                ->where('status', 'approved')
                ->where('start_date', '<=', now())
                ->where('end_date', '>=', now())
                ->exists();
            return $hodOnLeave;
        }
        
        if ($role == 'dept-secretary') {
            return false;
        }
        
        if ($role == 'dean') {
            $isDeanLevel = ($currentLevel == LeaveRequest::LEVEL_DEAN) || ($currentLevel == 'dean' || $currentLevel == '2');
            if (!$isDeanLevel) return false;
            $deanId = $leaveRequest->staff->department->school->dean_staff_id ?? null;
            return $deanId == $user->id;
        }
        
        if ($role == 'dvca') {
            $isDvcLevel = ($currentLevel == LeaveRequest::LEVEL_DVC) || ($currentLevel == 'dvc' || $currentLevel == '4');
            if (!$isDvcLevel) return false;
            return $leaveRequest->staff->staff_category == 'academic';
        }
        
        if ($role == 'dvc-pfa') {
            $isDvcLevel = ($currentLevel == LeaveRequest::LEVEL_DVC) || ($currentLevel == 'dvc' || $currentLevel == '4');
            if (!$isDvcLevel) return false;
            return in_array($leaveRequest->staff->staff_category, ['administrative', 'supporting']);
        }
        
        if ($role == 'hr-officer') {
            $isHrLevel = ($currentLevel == LeaveRequest::LEVEL_HR) || ($currentLevel == 'hr' || $currentLevel == '3');
            $isHrLogisticsLevel = ($currentLevel == LeaveRequest::LEVEL_HR_LOGISTICS) || ($currentLevel == 'hr_logistics' || $currentLevel == '5');
            return $isHrLevel || $isHrLogisticsLevel;
        }
        
        return false;
    }

    /**
     * Reject Request
     */
    public function reject(Request $request, $id)
    {
        $request->validate(['rejection_reason' => 'required|string|min:10|max:500']);
        
        $leaveRequest = LeaveRequest::findOrFail($id);
        
        ApprovalLog::create([
            'request_type' => 'leave',
            'request_id' => $leaveRequest->id,
            'from_role' => $this->getLevelName($leaveRequest->current_approval_level),
            'to_role' => 'Rejected',
            'action' => 'rejected',
            'comment' => $request->rejection_reason,
            'approver_staff_id' => auth()->id(),
            'ip_address' => $request->ip(),
        ]);
        
        $leaveRequest->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
            'current_approval_level' => null,
        ]);
        
        AuditLogHelper::log('reject', 'leave', $leaveRequest->id,
            ['status' => 'pending'],
            ['status' => 'rejected', 'reason' => $request->rejection_reason]
        );
        
        \App\Models\Notification::create([
            'user_id' => $leaveRequest->staff_id,
            'type' => 'approval_rejected',
            'title' => 'Leave Request Rejected',
            'message' => 'Your leave request has been rejected. Reason: ' . $request->rejection_reason,
            'action_url' => route('leave-requests.show', $leaveRequest->id),
            'action_text' => 'View Details',
            'is_read' => false,
        ]);
        
        return redirect()->back()->with('success', 'Leave request rejected.');
    }

    /**
     * Update leave balance after final approval
     */
    private function updateLeaveBalance($leaveRequest)
    {
        $currentYear = date('Y');
        $leaveBalance = LeaveBalance::where('staff_id', $leaveRequest->staff_id)
            ->where('leave_type_id', $leaveRequest->leave_type_id)
            ->where('year', $currentYear)
            ->first();
        
        if ($leaveBalance) {
            $leaveBalance->decrement('balance', $leaveRequest->days);
            $leaveBalance->increment('used', $leaveRequest->days);
        }
    }

    /**
     * Notify the next approver
     */
    private function notifyNextApprover($leaveRequest, $nextLevel)
    {
        $nextApproverId = $this->getNextApproverId($leaveRequest, $nextLevel);
        
        if ($nextApproverId) {
            $levelName = $this->getLevelName($nextLevel);
            $priority = ($nextLevel == LeaveRequest::LEVEL_HOD || $nextLevel == LeaveRequest::LEVEL_DEAN) ? 'high' : 'medium';
            $icon = ($nextLevel == LeaveRequest::LEVEL_HOD || $nextLevel == LeaveRequest::LEVEL_DEAN) ? 'alert-triangle' : 'clock';
            $iconColor = ($nextLevel == LeaveRequest::LEVEL_HOD || $nextLevel == LeaveRequest::LEVEL_DEAN) ? 'text-warning' : 'text-info';
            
            \App\Models\Notification::create([
                'user_id' => $nextApproverId,
                'type' => 'approval_pending',
                'title' => "Leave Request Pending Your Approval",
                'message' => "Leave request from {$leaveRequest->staff->first_name} {$leaveRequest->staff->last_name} requires your approval as {$levelName}.",
                'action_url' => route('leave-requests.pending'),
                'action_text' => 'Review Request',
                'is_read' => false,
                'priority' => $priority,
                'icon' => $icon,
                'icon_color' => $iconColor,
            ]);
        }
    }

    /**
     * Get next approver ID based on numeric level
     */
    private function getNextApproverId($leaveRequest, $level)
    {
        $staff = $leaveRequest->staff;
        
        switch ($level) {
            case LeaveRequest::LEVEL_HOD:
                $hod = $staff->department->hod;
                if ($hod) {
                    $hodOnLeave = LeaveRequest::where('staff_id', $hod->id)
                        ->where('status', 'approved')
                        ->where('start_date', '<=', now())
                        ->where('end_date', '>=', now())
                        ->exists();
                    
                    if ($hodOnLeave) {
                        $deputyHod = User::role('deputy-hod')
                            ->where('department_id', $staff->department_id)
                            ->first();
                        if ($deputyHod) {
                            return $deputyHod->id;
                        }
                    }
                }
                return $staff->department->hod_staff_id ?? null;
                
            case LeaveRequest::LEVEL_DEAN:
                return $staff->department->school->dean_staff_id ?? null;
                
            case LeaveRequest::LEVEL_HR:
                $hrOfficer = User::role('hr-officer')->first();
                return $hrOfficer ? $hrOfficer->id : null;
                
            case LeaveRequest::LEVEL_DVC:
                if ($staff->staff_category == 'academic') {
                    $actingDvca = ActingDvcAssignment::where('role', 'dvca')
                        ->where('status', 'active')
                        ->where('start_date', '<=', now())
                        ->where('end_date', '>=', now())
                        ->first();
                    
                    if ($actingDvca) {
                        return $actingDvca->staff_id;
                    }
                    
                    $dvca = User::role('dvca')->first();
                    return $dvca ? $dvca->id : null;
                } else {
                    $actingDvcPfa = ActingDvcAssignment::where('role', 'dvc-pfa')
                        ->where('status', 'active')
                        ->where('start_date', '<=', now())
                        ->where('end_date', '>=', now())
                        ->first();
                    
                    if ($actingDvcPfa) {
                        return $actingDvcPfa->staff_id;
                    }
                    
                    $dvcPfa = User::role('dvc-pfa')->first();
                    return $dvcPfa ? $dvcPfa->id : null;
                }
                
            case LeaveRequest::LEVEL_HR_LOGISTICS:
                $hrOfficer = User::role('hr-officer')->first();
                return $hrOfficer ? $hrOfficer->id : null;
                
            default:
                return null;
        }
    }

    /**
     * Display staff's leave requests with status timeline
     */
    public function myRequestStatus(Request $request)
    {
        $perPage = $this->getPerPage($request, 10);
        
        $query = LeaveRequest::with([
            'leaveType', 
            'actingStaff', 
            'approvalLogs.approver',
            'documents'
        ])
        ->where('staff_id', auth()->id());
        
        $filter = $request->get('filter', 'all');
        
        switch ($filter) {
            case 'pending':
                $query->where('status', 'pending');
                break;
            case 'approved':
                $query->where('status', 'approved');
                break;
            case 'rejected':
                $query->where('status', 'rejected');
                break;
            case 'cancelled':
                $query->where('status', 'cancelled');
                break;
            case 'all':
            default:
                break;
        }
        
        $leaveRequests = $query->orderBy('created_at', 'desc')->paginate($perPage);
        
        $stats = [
            'total' => LeaveRequest::where('staff_id', auth()->id())->count(),
            'pending' => LeaveRequest::where('staff_id', auth()->id())->where('status', 'pending')->count(),
            'approved' => LeaveRequest::where('staff_id', auth()->id())->where('status', 'approved')->count(),
            'rejected' => LeaveRequest::where('staff_id', auth()->id())->where('status', 'rejected')->count(),
            'cancelled' => LeaveRequest::where('staff_id', auth()->id())->where('status', 'cancelled')->count(),
        ];
        
        $currentFilter = $filter;
        
        return view('shared.leave-requests.status', compact('leaveRequests', 'stats', 'currentFilter'));
    }

    /**
     * Cancel a pending leave request
     */
    public function cancel($id)
    {
        $leaveRequest = LeaveRequest::where('staff_id', auth()->id())
            ->where('status', 'pending')
            ->findOrFail($id);
        
        $leaveRequest->update([
            'status' => 'cancelled',
            'rejection_reason' => 'Cancelled by staff',
        ]);
        
        AuditLogHelper::log('update', 'leave', $leaveRequest->id,
            ['status' => 'pending'],
            ['status' => 'cancelled']
        );
        
        if (request()->expectsJson()) {
            return response()->json(['success' => true]);
        }
        
        return redirect()->back()->with('success', 'Leave request cancelled successfully.');
    }

    // ============================================
    // HOLD POSITION HISTORY METHODS (UPDATED)
    // ============================================

    /**
     * Display hold position requests where user is the requester
     * UPDATED: Now shows from HoldPositionHistory but with better data
     */
    public function holdPositionForMe(Request $request)
    {
        $perPage = $this->getPerPage($request, 10);
        
        // Get hold histories where user is the requester
        $holdHistories = HoldPositionHistory::with(['holder', 'leaveRequest.leaveType', 'leaveRequest.documents'])
            ->where('requester_staff_id', auth()->id())
            ->orderBy('requested_at', 'desc')
            ->paginate($perPage);
        
        return view('shared.leave-requests.hold-position-for-me', compact('holdHistories'));
    }

    /**
     * Display hold position requests where user is the holder (acting staff)
     * UPDATED: Shows pending acting requests from LeaveRequest directly
     */
    public function iHeldPositionFor(Request $request)
    {
        $perPage = $this->getPerPage($request, 10);
        
        // Get leave requests where this user is the acting staff and status is pending
        $actingRequests = LeaveRequest::where('acting_staff_id', auth()->id())
            ->where('acting_status', 'pending')
            ->with(['staff', 'leaveType', 'staff.department', 'documents'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
        
        // Count unresponsive ones (over 48 hours)
        $unresponsiveCount = LeaveRequest::where('acting_staff_id', auth()->id())
            ->where('acting_status', 'pending')
            ->where('created_at', '<', now()->subHours(48))
            ->count();
        
        // Get hold histories where user is the holder (for history/archive)
        $holdHistories = HoldPositionHistory::with(['requester', 'leaveRequest.leaveType'])
            ->where('holder_staff_id', auth()->id())
            ->where('status', '!=', 'pending')
            ->orderBy('requested_at', 'desc')
            ->limit(10)
            ->get();
        
        return view('shared.leave-requests.i-held-position-for', compact(
            'actingRequests', 
            'holdHistories',
            'unresponsiveCount'
        ));
    }

    /**
     * HR Review Page - Level 3 (Initial HR review before DVC)
     */
    public function hrReview($id)
    {
        $leaveRequest = LeaveRequest::with([
            'staff', 
            'leaveType', 
            'actingStaff',
            'approvalLogs.approver',
            'documents'
        ])
        ->where('current_approval_level', LeaveRequest::LEVEL_HR)
        ->findOrFail($id);
        
        $staffLeaveHistory = LeaveRequest::where('staff_id', $leaveRequest->staff_id)
            ->where('created_at', '>=', now()->subMonths(12))
            ->with('leaveType')
            ->orderBy('created_at', 'desc')
            ->get();
        
        $leaveBalances = LeaveBalance::where('staff_id', $leaveRequest->staff_id)->get();
        
        $departmentStaffOnLeave = LeaveRequest::where('status', 'approved')
            ->where('staff_id', '!=', $leaveRequest->staff_id)
            ->whereHas('staff', function($q) use ($leaveRequest) {
                $q->where('department_id', $leaveRequest->staff->department_id);
            })
            ->where(function($q) use ($leaveRequest) {
                $q->whereBetween('start_date', [$leaveRequest->start_date, $leaveRequest->end_date])
                  ->orWhereBetween('end_date', [$leaveRequest->start_date, $leaveRequest->end_date]);
            })
            ->with('staff')
            ->get();
        
        $blackoutPeriod = \App\Models\BlackoutPeriod::where('start_date', '<=', $leaveRequest->end_date)
            ->where('end_date', '>=', $leaveRequest->start_date)
            ->where('status', 'active')
            ->first();
        
        $actedForOthers = HoldPositionHistory::where('holder_staff_id', $leaveRequest->staff_id)
            ->with('leaveRequest')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        $othersActedFor = HoldPositionHistory::where('requester_staff_id', $leaveRequest->staff_id)
            ->with('holder')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        return view('shared.leave-requests.hr-review', compact(
            'leaveRequest', 
            'staffLeaveHistory', 
            'leaveBalances',
            'departmentStaffOnLeave',
            'blackoutPeriod',
            'actedForOthers',
            'othersActedFor'
        ));
    }

    /**
     * Process HR Review
     */
    public function processHrReview(Request $request, $id)
    {
        $leaveRequest = LeaveRequest::findOrFail($id);
        
        $request->validate([
            'action' => 'required|in:approve,reject,request_info,draft',
            'adjusted_days' => 'nullable|numeric|min:0.5|max:365',
            'adjustment_reason' => 'nullable|string|max:255',
            'hr_comment' => 'nullable|string|max:1000',
            'conditions' => 'nullable|string|max:500',
            'payment_status' => 'required|in:paid,partial,unpaid',
            'payment_percentage' => 'required_if:payment_status,partial|nullable|numeric|min:0|max:100',
            'payment_notes' => 'required_if:payment_status,partial,unpaid|nullable|string|max:500',
        ]);
        
        DB::beginTransaction();
        
        try {
            $paymentNote = "";
            if ($request->payment_status == 'unpaid') {
                $paymentNote = "\n\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n⚠️ **PAYMENT NOTICE:** This leave is **UNPAID**.\n**Reason:** " . ($request->payment_notes ?? 'Not specified') . "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━";
            } elseif ($request->payment_status == 'partial') {
                $paymentNote = "\n\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n⚠️ **PAYMENT NOTICE:** This leave is **PARTIALLY PAID** at {$request->payment_percentage}%.\n**Reason:** " . ($request->payment_notes ?? 'Not specified') . "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━";
            }
            
            $leaveRequest->update([
                'payment_status' => $request->payment_status,
                'payment_percentage' => $request->payment_percentage ?? 100,
                'payment_notes' => $request->payment_notes,
                'payment_decision_at' => now(),
                'payment_decided_by' => auth()->id(),
            ]);
            
            if ($request->action == 'reject') {
                $leaveRequest->update([
                    'status' => 'rejected',
                    'rejection_reason' => $request->hr_comment ?? 'Rejected by HR',
                    'current_approval_level' => null,
                ]);
                
                $message = 'Leave request rejected.';
                
            } elseif ($request->action == 'request_info') {
                $leaveRequest->update([
                    'status' => 'pending',
                    'rejection_reason' => $request->hr_comment ?? 'Additional information requested by HR',
                ]);
                
                \App\Models\Notification::create([
                    'user_id' => $leaveRequest->staff_id,
                    'type' => 'info_requested',
                    'title' => 'Additional Information Required',
                    'message' => 'HR has requested additional information regarding your leave request.',
                    'action_url' => route('leave-requests.show', $leaveRequest->id),
                    'action_text' => 'View Request',
                    'is_read' => false,
                ]);
                
                $message = 'Additional information requested from staff.';
                
            } elseif ($request->action == 'draft') {
                $message = 'HR review saved as draft.';
                
            } else {
                $adjustedDays = $request->adjusted_days ?? $leaveRequest->days;
                $adjustmentReason = $request->adjustment_reason ?? null;
                
                if ($adjustedDays != $leaveRequest->days) {
                    $leaveRequest->update([
                        'days' => $adjustedDays,
                    ]);
                }
                
                $nextLevel = LeaveRequest::LEVEL_DVC;
                $leaveRequest->update([
                    'current_approval_level' => $nextLevel,
                ]);
                
                $hrComment = $request->hr_comment;
                if ($adjustmentReason) {
                    $hrComment = ($hrComment ? $hrComment . "\n\n" : '') . "📅 Day Adjustment: {$leaveRequest->days} → {$adjustedDays} days. Reason: {$adjustmentReason}";
                }
                if ($request->conditions) {
                    $hrComment = ($hrComment ? $hrComment . "\n\n" : '') . "📋 Conditions: " . $request->conditions;
                }
                $hrComment .= $paymentNote;
                
                ApprovalLog::create([
                    'request_type' => 'leave',
                    'request_id' => $leaveRequest->id,
                    'from_role' => 'HR',
                    'to_role' => 'DVC',
                    'action' => 'approved',
                    'comment' => $hrComment,
                    'approver_staff_id' => auth()->id(),
                    'ip_address' => $request->ip(),
                ]);
                
                $this->notifyNextApprover($leaveRequest, $nextLevel);
                
                $message = 'Leave request approved and forwarded to DVC.';
            }
            
            DB::commit();
            
            if ($request->action == 'draft') {
                return redirect()->back()->with('info', $message);
            }
            
            return redirect()->route('leave-requests.pending')
                ->with('success', $message);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to process: ' . $e->getMessage());
        }
    }

    /**
     * DVC Review Page - Level 4 (DVC reviews after HR)
     */
    public function dvcReview($id)
    {
        $user = auth()->user();
        $role = $user->getRoleNames()->first();
        
        if (!in_array($role, ['admin', 'dvca', 'dvc-pfa'])) {
            abort(403, 'You do not have permission to view this page.');
        }
        
        $leaveRequest = LeaveRequest::with([
            'staff', 
            'leaveType', 
            'actingStaff',
            'approvalLogs.approver',
            'documents'
        ])
        ->where('current_approval_level', LeaveRequest::LEVEL_DVC)
        ->findOrFail($id);
        
        $hrLog = ApprovalLog::where('request_id', $leaveRequest->id)
            ->where('from_role', 'HR')
            ->where('action', 'approved')
            ->first();
        
        $staffLeaveHistory = LeaveRequest::where('staff_id', $leaveRequest->staff_id)
            ->where('created_at', '>=', now()->subMonths(12))
            ->with('leaveType')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        $departmentStaffOnLeave = LeaveRequest::where('status', 'approved')
            ->where('staff_id', '!=', $leaveRequest->staff_id)
            ->whereHas('staff', function($q) use ($leaveRequest) {
                $q->where('department_id', $leaveRequest->staff->department_id);
            })
            ->where(function($q) use ($leaveRequest) {
                $q->whereBetween('start_date', [$leaveRequest->start_date, $leaveRequest->end_date])
                  ->orWhereBetween('end_date', [$leaveRequest->start_date, $leaveRequest->end_date]);
            })
            ->with('staff')
            ->get();
        
        return view('shared.leave-requests.dvc-review', compact(
            'leaveRequest',
            'hrLog',
            'staffLeaveHistory',
            'departmentStaffOnLeave'
        ));
    }

    /**
     * Process DVC Review
     */
    public function processDvcReview(Request $request, $id)
    {
        $user = auth()->user();
        $role = $user->getRoleNames()->first();
        
        if (!in_array($role, ['admin', 'dvca', 'dvc-pfa'])) {
            abort(403, 'You do not have permission to perform this action.');
        }
        
        $leaveRequest = LeaveRequest::findOrFail($id);
        
        $request->validate([
            'action' => 'required|in:approve,reject',
            'dvc_comment' => 'nullable|string|max:500',
        ]);
        
        DB::beginTransaction();
        
        try {
            if ($request->action == 'reject') {
                $leaveRequest->update([
                    'status' => 'rejected',
                    'rejection_reason' => $request->dvc_comment ?? 'Rejected by DVC',
                    'current_approval_level' => null,
                ]);
                
                $message = 'Leave request rejected.';
                
            } else {
                $nextLevel = LeaveRequest::LEVEL_HR_LOGISTICS;
                $leaveRequest->update([
                    'current_approval_level' => $nextLevel,
                ]);
                
                ApprovalLog::create([
                    'request_type' => 'leave',
                    'request_id' => $leaveRequest->id,
                    'from_role' => 'DVC',
                    'to_role' => 'HR Logistics',
                    'action' => 'approved',
                    'comment' => $request->dvc_comment,
                    'approver_staff_id' => $user->id,
                    'ip_address' => $request->ip(),
                ]);
                
                $this->notifyNextApprover($leaveRequest, $nextLevel);
                
                $message = 'Leave request approved. Forwarded to HR Logistics for final processing.';
            }
            
            DB::commit();
            
            return redirect()->route('leave-requests.pending')
                ->with('success', $message);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to process: ' . $e->getMessage());
        }
    }

    /**
     * Display approval history for the logged-in approver
     */
    public function approvalsHistory(Request $request)
    {
        $perPage = $this->getPerPage($request, 10);
        $user = auth()->user();
        $role = $user->getRoleNames()->first();
        
        $query = ApprovalLog::with(['leaveRequest.staff', 'leaveRequest.leaveType'])
            ->where('approver_staff_id', $user->id)
            ->where('request_type', 'leave')
            ->orderBy('created_at', 'desc');
        
        if ($request->has('action') && $request->action) {
            $query->where('action', $request->action);
        }
        
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        $approvals = $query->paginate($perPage);
        
        $stats = [
            'total' => ApprovalLog::where('approver_staff_id', $user->id)->count(),
            'approved' => ApprovalLog::where('approver_staff_id', $user->id)->where('action', 'approved')->count(),
            'rejected' => ApprovalLog::where('approver_staff_id', $user->id)->where('action', 'rejected')->count(),
        ];
        
        return view('shared.leave-requests.approvals-history', compact('approvals', 'stats', 'role'));
    }

    /**
     * Display approval details page with letter
     */
    public function approvalDetails($id)
    {
        $leaveRequest = LeaveRequest::with([
            'staff', 
            'leaveType', 
            'actingStaff', 
            'approvalLogs.approver',
            'documents',
            'letters'
        ])->findOrFail($id);
        
        $user = auth()->user();
        $userActionRecord = ApprovalLog::where('request_id', $leaveRequest->id)
            ->where('approver_staff_id', $user->id)
            ->first();
        
        if (!$userActionRecord && !$user->hasAnyRole(['admin', 'vice-chancellor'])) {
            abort(403, 'You are not authorized to view this approval details.');
        }
        
        $userAction = $userActionRecord ? ucfirst($userActionRecord->action) : 'Viewed';
        $userActionDate = $userActionRecord ? $userActionRecord->created_at : $leaveRequest->updated_at;
        
        return view('shared.leave-requests.approval-details', compact('leaveRequest', 'userAction', 'userActionDate'));
    }
}