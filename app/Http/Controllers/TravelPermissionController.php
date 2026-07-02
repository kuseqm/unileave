<?php

namespace App\Http\Controllers;

use App\Models\TravelPermission;
use App\Models\User;
use App\Models\Notification;
use App\Helpers\AuditLogHelper;  // ✅ ADD THIS
use App\Traits\PerPageTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TravelPermissionController extends Controller
{
    use PerPageTrait;

    /**
     * Display staff's own travel requests
     */
    public function myRequests(Request $request)
    {
        $perPage = $this->getPerPage($request, 10);
        
        $travelRequests = TravelPermission::with(['staff', 'actingStaff'])
            ->where('staff_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
        
        return view('shared.travel-permissions.my-requests', compact('travelRequests'));
    }

    /**
     * Show form to create travel permission
     */
    public function create()
    {
        $colleagues = User::where('department_id', auth()->user()->department_id)
            ->where('id', '!=', auth()->id())
            ->where('status', 'active')
            ->get();
        
        return view('shared.travel-permissions.create', compact('colleagues'));
    }

    /**
     * Store travel permission request
     */
    public function store(Request $request)
    {
        $request->validate([
            'destination' => 'required|string|max:255',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'travel_reason' => 'required|in:official,personal',
            'official_benefit' => 'required_if:travel_reason,official|nullable|string',
            'personal_description' => 'required_if:travel_reason,personal|nullable|string',
            'cost_coverage' => 'required|in:organizers_full,organizers_partial,university,private',
            'acting_staff_id' => 'required|exists:staff,id|different:staff_id',
            'invitation_letter' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'endorsement' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $startDate = \Carbon\Carbon::parse($request->start_date);
        $endDate = \Carbon\Carbon::parse($request->end_date);
        $days = $startDate->diffInWeekdays($endDate) + 1;

        DB::beginTransaction();

        try {
            $travelRequest = TravelPermission::create([
                'staff_id' => auth()->id(),
                'acting_staff_id' => $request->acting_staff_id,
                'destination' => $request->destination,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'days' => $days,
                'travel_reason' => $request->travel_reason,
                'official_benefit' => $request->official_benefit,
                'personal_description' => $request->personal_description,
                'cost_coverage' => $request->cost_coverage,
                'additional_details' => $request->additional_details,
                'acting_status' => 'pending',
                'acting_token' => Str::random(60),
                'current_approval_level' => 'acting',
            ]);

            // ✅ AUDIT LOG: Travel permission created
            AuditLogHelper::log('create', 'travel', $travelRequest->id, null, $travelRequest->toArray());

            // Handle file uploads
            if ($request->hasFile('invitation_letter')) {
                $path = $request->file('invitation_letter')->store('travel_documents/' . $travelRequest->id, 'public');
                $travelRequest->update(['invitation_letter_path' => $path]);
            }
            
            if ($request->hasFile('endorsement')) {
                $path = $request->file('endorsement')->store('travel_documents/' . $travelRequest->id, 'public');
                $travelRequest->update(['endorsement_path' => $path]);
            }

            // Notify acting staff
            $actingStaff = $travelRequest->actingStaff;
            $requester = $travelRequest->staff;
            
            Notification::create([
                'user_id' => $actingStaff->id,
                'type' => 'acting_request_travel',
                'title' => 'Travel Permission - Acting Request',
                'message' => "{$requester->first_name} {$requester->last_name} has requested you to cover their duties during travel to {$travelRequest->destination} from " . 
                    \Carbon\Carbon::parse($travelRequest->start_date)->format('d M Y') . " to " . 
                    \Carbon\Carbon::parse($travelRequest->end_date)->format('d M Y'),
                'action_url' => route('travel-permissions.acting.response', $travelRequest->acting_token),
                'action_text' => 'Review Request',
                'is_read' => false,
            ]);

            DB::commit();

            return redirect()->route('travel-permissions.my-requests')
                ->with('success', 'Travel permission request submitted! Waiting for acting staff approval.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to submit request: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Acting staff response page (modal)
     */
    public function actingResponse($token)
    {
        $travelRequest = TravelPermission::where('acting_token', $token)
            ->where('acting_status', 'pending')
            ->firstOrFail();
        
        if (request()->ajax() || request()->expectsJson()) {
            return view('components.travel-acting-response-modal', compact('travelRequest'))->render();
        }
        
        return view('shared.travel-permissions.acting-response', compact('travelRequest'));
    }

    /**
     * Process acting staff response
     */
    public function processActingResponse(Request $request, $id)
    {
        $travelRequest = TravelPermission::findOrFail($id);
        
        $request->validate([
            'action' => 'required|in:accept,decline',
            'comment' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();

        try {
            if ($request->action === 'accept') {
                $nextLevel = $this->getNextLevelAfter('acting', $travelRequest->staff);
                
                $oldValues = $travelRequest->getOriginal();
                
                $travelRequest->update([
                    'acting_status' => 'accepted',
                    'acting_responded_at' => now(),
                    'current_approval_level' => $nextLevel,
                ]);
                
                // ✅ AUDIT LOG: Acting staff accepted travel
                AuditLogHelper::log('approve', 'travel_acting', $travelRequest->id,
                    ['acting_status' => 'pending', 'current_approval_level' => 'acting'],
                    ['acting_status' => 'accepted', 'current_approval_level' => $nextLevel]
                );

                // Notify requester
                Notification::create([
                    'user_id' => $travelRequest->staff_id,
                    'type' => 'acting_accepted_travel',
                    'title' => 'Acting Staff Accepted',
                    'message' => "{$travelRequest->actingStaff->first_name} {$travelRequest->actingStaff->last_name} has accepted to cover your duties during your travel.",
                    'action_url' => route('travel-permissions.show', $travelRequest->id),
                    'action_text' => 'View Request',
                    'is_read' => false,
                ]);

                // Notify next approver
                $this->notifyNextApprover($travelRequest, $nextLevel);

            } else {
                $oldValues = $travelRequest->getOriginal();
                
                $travelRequest->update([
                    'acting_status' => 'declined',
                    'acting_responded_at' => now(),
                    'status' => 'rejected',
                    'rejection_reason' => "Acting staff declined: " . ($request->comment ?? 'No reason provided'),
                ]);
                
                // ✅ AUDIT LOG: Acting staff declined travel
                AuditLogHelper::log('reject', 'travel_acting', $travelRequest->id,
                    ['acting_status' => 'pending'],
                    ['acting_status' => 'declined', 'status' => 'rejected']
                );

                Notification::create([
                    'user_id' => $travelRequest->staff_id,
                    'type' => 'acting_declined_travel',
                    'title' => 'Acting Staff Declined',
                    'message' => "{$travelRequest->actingStaff->first_name} {$travelRequest->actingStaff->last_name} has declined to cover your duties.",
                    'action_url' => route('travel-permissions.my-requests'),
                    'action_text' => 'View Requests',
                    'is_read' => false,
                ]);
            }

            DB::commit();

            if ($request->ajax() || $request->expectsJson()) {
                return response()->json(['success' => true]);
            }

            return redirect()->route('dashboard')->with('success', 'Response recorded.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to process response: ' . $e->getMessage());
        }
    }

    private function getNextLevelAfter($currentLevel, $staff)
    {
        $sequence = [
            'acting' => 'unit_head',
            'unit_head' => 'hod',
            'hod' => 'dean',
            'dean' => 'dvc',
            'dvc' => 'hr_final',
            'hr_final' => 'completed',
        ];
        
        if ($currentLevel === 'acting') {
            if ($staff->staff_category === 'academic') {
                return 'hod';
            }
            $unitHead = $staff->department->unitHead ?? null;
            if (!$unitHead) {
                return 'hod';
            }
        }
        
        if ($currentLevel === 'hod' && $staff->staff_category !== 'academic') {
            return 'dvc';
        }
        
        return $sequence[$currentLevel] ?? 'completed';
    }

    private function notifyNextApprover($travelRequest, $nextLevel)
    {
        $nextApproverId = $this->getNextApproverId($travelRequest, $nextLevel);
        
        if ($nextApproverId) {
            $roleNames = [
                'unit_head' => 'Unit Head',
                'hod' => 'Head of Department',
                'dean' => 'Dean',
                'dvc' => 'DVC',
                'hr_final' => 'HR Officer',
            ];
            
            Notification::create([
                'user_id' => $nextApproverId,
                'type' => 'approval_pending_travel',
                'title' => 'Travel Permission Pending Approval',
                'message' => "Travel request from {$travelRequest->staff->first_name} {$travelRequest->staff->last_name} to {$travelRequest->destination} requires your approval as {$roleNames[$nextLevel]}.",
                'action_url' => route('travel-permissions.pending'),
                'action_text' => 'Review Request',
                'is_read' => false,
            ]);
        }
    }

    private function getNextApproverId($travelRequest, $level)
    {
        $staff = $travelRequest->staff;
        
        switch ($level) {
            case 'unit_head':
                return $staff->department->unit_head_staff_id ?? null;
            case 'hod':
                return $staff->department->hod_staff_id ?? null;
            case 'dean':
                return $staff->department->school->dean_staff_id ?? null;
            case 'dvc':
                if ($staff->staff_category == 'academic') {
                    $dvca = User::role('dvca')->first();
                    return $dvca ? $dvca->id : null;
                } else {
                    $dvcPfa = User::role('dvc-pfa')->first();
                    return $dvcPfa ? $dvcPfa->id : null;
                }
            case 'hr_final':
                $hrOfficer = User::role('hr-officer')->first();
                return $hrOfficer ? $hrOfficer->id : null;
            default:
                return null;
        }
    }

    public function pendingApprovals(Request $request)
    {
        $perPage = $this->getPerPage($request, 10);
        $user = auth()->user();
        
        $query = TravelPermission::with(['staff', 'actingStaff'])
            ->where('status', 'pending')
            ->where('acting_status', 'accepted')
            ->pendingForRole($user);
        
        $pendingRequests = $query->orderBy('created_at', 'asc')->paginate($perPage);
        
        return view('shared.travel-permissions.pending-approvals', compact('pendingRequests'));
    }

    public function approve(Request $request, $id)
    {
        $travelRequest = TravelPermission::findOrFail($id);
        $currentLevel = $travelRequest->current_approval_level;
        $nextLevel = $this->getNextLevelAfter($currentLevel, $travelRequest->staff);
        
        $oldValues = $travelRequest->getOriginal();
        
        $travelRequest->update(['current_approval_level' => $nextLevel]);
        
        // ✅ AUDIT LOG: Travel permission approved
        AuditLogHelper::log('approve', 'travel', $travelRequest->id,
            ['current_approval_level' => $currentLevel],
            ['current_approval_level' => $nextLevel]
        );
        
        if ($nextLevel === 'completed') {
            $travelRequest->update(['status' => 'approved']);
            
            // ✅ AUDIT LOG: Travel permission fully approved
            AuditLogHelper::log('update', 'travel', $travelRequest->id,
                ['status' => 'pending'],
                ['status' => 'approved']
            );
            
            return redirect()->back()->with('success', 'Travel permission fully approved.');
        }
        
        $this->notifyNextApprover($travelRequest, $nextLevel);
        
        return redirect()->back()->with('success', 'Travel permission approved and forwarded.');
    }

    public function reject(Request $request, $id)
    {
        $request->validate(['rejection_reason' => 'required|string|min:10']);
        
        $travelRequest = TravelPermission::findOrFail($id);
        
        $oldValues = $travelRequest->getOriginal();
        
        $travelRequest->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
        ]);
        
        // ✅ AUDIT LOG: Travel permission rejected
        AuditLogHelper::log('reject', 'travel', $travelRequest->id,
            ['status' => 'pending'],
            ['status' => 'rejected', 'reason' => $request->rejection_reason]
        );
        
        Notification::create([
            'user_id' => $travelRequest->staff_id,
            'type' => 'travel_rejected',
            'title' => 'Travel Permission Rejected',
            'message' => 'Your travel permission request has been rejected. Reason: ' . $request->rejection_reason,
            'action_url' => route('travel-permissions.my-requests'),
            'action_text' => 'View Details',
            'is_read' => false,
        ]);
        
        return redirect()->back()->with('success', 'Travel permission rejected.');
    }

    public function show($id)
    {
        $travelRequest = TravelPermission::with(['staff', 'actingStaff'])
            ->where('staff_id', auth()->id())
            ->findOrFail($id);
        
        return view('shared.travel-permissions.show', compact('travelRequest'));
    }
}