<?php

namespace App\Http\Controllers;

use App\Helpers\AuditLogHelper;
use App\Models\AuditLog;
use App\Models\LeaveRequest;
use App\Models\Notification;
use App\Models\TravelLetter;
use App\Models\TravelPermission;  // ✅ ADD THIS
use App\Models\TravelPermissionDraft;
use App\Models\User;
use App\Traits\ActingStaffAvailabilityTrait;
use App\Traits\PerPageTrait;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class TravelPermissionController extends Controller
{
    use ActingStaffAvailabilityTrait, PerPageTrait;

    /**
     * Lightweight signature for the live-refresh poll on "my requests".
     */
    public function myRequestsHeartbeat()
    {
        $query = TravelPermission::where('staff_id', auth()->id());

        return response()->json(['signature' => $query->count().':'.$query->max('updated_at')]);
    }

    /**
     * Lightweight signature for the live-refresh poll on Pending Approvals -
     * reuses the same pendingForRole() scope the real page renders from.
     */
    public function pendingApprovalsHeartbeat()
    {
        $query = TravelPermission::where('status', 'pending')
            ->where('acting_status', 'accepted')
            ->pendingForRole(auth()->user());

        return response()->json(['signature' => $query->count().':'.$query->max('updated_at')]);
    }

    /**
     * Display staff's own travel requests
     */
    public function myRequests(Request $request)
    {
        $perPage = $this->getPerPage($request, 10);

        $query = TravelPermission::with(['staff', 'actingStaff'])
            ->where('staff_id', auth()->id())
            ->orderBy('created_at', 'desc');

        $grandTotal = (clone $query)->count();

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('destination', 'like', "%{$search}%")
                    ->orWhere('travel_reason', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $travelRequests = $query->paginate($perPage)->appends($request->query());

        if ($request->wantsJson()) {
            return response()->json([
                'html' => view('shared.travel-permissions._my-requests-results', compact('travelRequests'))->render(),
                'total' => $travelRequests->total(),
                'grand_total' => $grandTotal,
            ]);
        }

        return view('shared.travel-permissions.my-requests', compact('travelRequests'));
    }

    /**
     * Show form to create travel permission
     */
    public function create()
    {
        // Someone already accepted to hold another colleague's position
        // (Leave or Travel) right now is unavailable to take on a second one
        // - a person holds at most one position at a time. Exact date range
        // isn't known yet at this point (chosen next in the form), so this
        // only catches an assignment active today; store()'s own validation
        // below is the real, date-aware gate.
        $busyHolders = $this->busyHolderIds(now()->toDateString(), now()->toDateString());

        $colleagues = User::where('department_id', auth()->user()->department_id)
            ->where('id', '!=', auth()->id())
            ->where('status', 'active')
            // Someone already on approved leave right now won't actually be
            // present to act - exclude them from the picker. The exact date
            // range isn't known yet at this point (chosen next in the form),
            // so this only catches leave that's active today; store()'s own
            // validation below is the real, date-aware gate.
            ->whereDoesntHave('leaveRequests', function ($q) {
                $q->where('status', 'approved')
                    ->where('start_date', '<=', now())
                    ->where('end_date', '>=', now());
            })
            ->whereNotIn('id', $busyHolders)
            ->get();

        // Nobody left in the department to hold the position - don't block
        // the application, but let the requester know who'll be notified to
        // sort out coverage from elsewhere.
        $escalationContact = $colleagues->isEmpty() ? $this->unitEscalationContact(auth()->user()) : null;

        // The Dean of a school would otherwise have to approve their own
        // travel at the Dean stage - if that's who's applying, they pick
        // an HOD from their own school to stand in as acting Dean for
        // that one stage only.
        $actingDeanOptions = $this->isSchoolsDean(auth()->user())
            ? $this->eligibleActingDeans(auth()->user())
            : collect();

        $draft = TravelPermissionDraft::where('staff_id', auth()->id())->first();

        return view('shared.travel-permissions.create', compact('colleagues', 'escalationContact', 'actingDeanOptions', 'draft'));
    }

    /**
     * Save (or update) the applicant's single in-progress travel permission
     * draft, and move the wizard to whichever step it's headed to.
     * Deliberately lenient - a draft is allowed to be incomplete. Mirrors
     * LeaveRequestController::saveDraft().
     */
    public function saveDraft(Request $request)
    {
        $data = $request->only([
            'destination', 'start_date', 'end_date', 'travel_reason',
            'official_benefit', 'personal_description', 'cost_coverage',
            'acting_staff_id', 'acting_dean_id', 'additional_details',
            'handover_notes', 'letter_content',
        ]);

        // Same rule as LeaveRequestController::saveDraft(): "Next" skips
        // field-level validation so a draft can be saved exactly as typed,
        // however incomplete, but Review/Sign are built entirely around the
        // request actually having its essentials - clamp back to step 1
        // (data still saved) rather than let Review render on nothing.
        // Acting staff/dean are deliberately excluded here for the same
        // reason as Leave: their eligibility logic is already duplicated
        // in store()'s own validation, and the worst case of skipping them
        // is just an "Acting Staff: -" row on Review, not a blank page.
        $requestedStep = (int) $request->input('current_step', 1);
        $reasonExplanationFilled = ($data['travel_reason'] ?? null) === 'official'
            ? filled($data['official_benefit'] ?? null)
            : filled($data['personal_description'] ?? null);
        $coreFieldsPresent = filled($data['destination'] ?? null)
            && filled($data['start_date'] ?? null)
            && filled($data['end_date'] ?? null)
            && filled($data['travel_reason'] ?? null)
            && $reasonExplanationFilled
            && filled($data['cost_coverage'] ?? null)
            && filled($data['handover_notes'] ?? null);
        $currentStep = ($requestedStep > 1 && ! $coreFieldsPresent) ? 1 : $requestedStep;

        TravelPermissionDraft::updateOrCreate(
            ['staff_id' => auth()->id()],
            [
                'form_data' => $data,
                'current_step' => $currentStep,
            ]
        );

        $redirect = redirect()->route('travel-permissions.create');

        if ($currentStep === 1 && $requestedStep > 1) {
            $redirect->with('error', 'Please fill in the travel details and handover/coverage notes before continuing - what you\'ve entered so far has been saved.');
        }

        return $redirect;
    }

    /**
     * Discard the applicant's in-progress draft entirely ("Start Over").
     */
    public function discardDraft(Request $request)
    {
        TravelPermissionDraft::where('staff_id', auth()->id())->delete();

        return redirect()->route('travel-permissions.create');
    }

    /**
     * Render the applicant's own Travel Permission Application Letter on
     * the real letterhead from their current draft, for the Review step's
     * iframe. Nothing here is persisted - this is a preview only. Mirrors
     * LeaveRequestController::previewDraftLetter().
     */
    public function previewDraftLetter(Request $request)
    {
        $applicant = auth()->user();
        $draft = TravelPermissionDraft::where('staff_id', $applicant->id)->firstOrFail();
        $data = $draft->form_data ?? [];

        $travelRequest = new TravelPermission([
            'destination' => $data['destination'] ?? null,
            'start_date' => $data['start_date'] ?? null,
            'end_date' => $data['end_date'] ?? null,
            'travel_reason' => $data['travel_reason'] ?? null,
        ]);
        $travelRequest->setRelation('actingStaff', User::find($data['acting_staff_id'] ?? null));

        $signatureImage = $draft->signature_data ?: null;

        return view('components.letter.travel-application', [
            'travelRequest' => $travelRequest,
            'staff' => $applicant,
            'university' => $applicant->university,
            'letterContent' => $data['letter_content'] ?? null,
            'handoverNotes' => $data['handover_notes'] ?? null,
            'signatureImage' => $signatureImage,
            'embedded' => true,
            'isPreview' => true,
        ]);
    }

    /**
     * Letter number in the same institutional format as Leave's
     * ("SJUT/HR/{year}/{number}") but with its own prefix and sequence -
     * see LetterTrait::generateLetterNumber(), not reused directly since
     * that trait is hardcoded to LeaveLetter.
     */
    private function generateTravelLetterNumber(): string
    {
        $year = date('Y');
        $lastLetter = TravelLetter::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        if ($lastLetter && $lastLetter->letter_number) {
            $lastNumber = intval(substr($lastLetter->letter_number, -4));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return "SJUT/TRV/{$year}/{$newNumber}";
    }

    /**
     * Who to notify when a department has nobody left to hold a position:
     * the department's Head of Unit if one is set, otherwise the HOD.
     */
    private function unitEscalationContact(User $staff): ?User
    {
        $department = $staff->department;

        return $department?->unitHead ?? $department?->hod;
    }

    /**
     * Whether this staff member is the Dean of their own school - meaning
     * their own travel request would otherwise reach the Dean stage with
     * nobody but themselves able to approve it.
     */
    private function isSchoolsDean(User $staff): bool
    {
        $school = $staff->department?->school;

        return $school && $school->dean_staff_id == $staff->id;
    }

    /**
     * HODs within a Dean's own school, eligible to stand in as acting Dean
     * for their travel request.
     */
    private function eligibleActingDeans(User $dean): Collection
    {
        $schoolId = $dean->department?->school_id;

        if (! $schoolId) {
            return collect();
        }

        return User::role('hod')
            ->whereHas('department', function ($q) use ($schoolId) {
                $q->where('school_id', $schoolId);
            })
            ->where('status', 'active')
            ->get();
    }

    /**
     * Store travel permission request
     */
    public function store(Request $request)
    {
        // Precompute whether the department actually has anyone free to
        // hold this position for these exact dates. If not, the requester
        // isn't blocked - acting_staff_id becomes optional and the Head of
        // Unit (or HOD) is auto-assigned as the holder below, with a
        // notification asking them to arrange coverage.
        $department = auth()->user()->department;
        $hasEligibleColleague = false;
        $escalationContact = null;

        // The Dean of a school would otherwise have to approve their own
        // travel at the Dean stage - if that's who's applying, they must
        // name an HOD from their own school to stand in.
        $isSchoolsDean = $this->isSchoolsDean(auth()->user());
        $eligibleActingDeanIds = $isSchoolsDean
            ? $this->eligibleActingDeans(auth()->user())->pluck('id')
            : collect();

        if ($department && $request->filled(['start_date', 'end_date'])) {
            $busyHolders = $this->busyHolderIds($request->start_date, $request->end_date);

            $hasEligibleColleague = User::where('department_id', $department->id)
                ->where('id', '!=', auth()->id())
                ->where('status', 'active')
                ->whereDoesntHave('leaveRequests', function ($q) use ($request) {
                    $q->where('status', 'approved')
                        ->where('start_date', '<=', $request->end_date)
                        ->where('end_date', '>=', $request->start_date);
                })
                ->whereNotIn('id', $busyHolders)
                ->exists();

            if (! $hasEligibleColleague) {
                $escalationContact = $this->unitEscalationContact(auth()->user());

                if (! $escalationContact) {
                    return redirect()->back()
                        ->with('error', 'No colleague is available to hold your position, and no Head of Unit or HOD is set up for your department to escalate to. Please contact HR directly.')
                        ->withInput();
                }
            }
        }

        $request->validate([
            'destination' => 'required|string|max:255',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'travel_reason' => [
                'required',
                'in:official,personal',
                // "Stop and wait": nobody can have more than one travel
                // request in flight at a time - block a new one while an
                // earlier one hasn't concluded, either still pending
                // approval, or already approved but not yet ended.
                function ($attribute, $value, $fail) {
                    $unresolved = TravelPermission::where('staff_id', auth()->id())
                        ->where(function ($q) {
                            $q->where('status', 'pending')
                                ->orWhere(function ($q2) {
                                    $q2->where('status', 'approved')
                                        ->where('end_date', '>=', now()->toDateString());
                                });
                        })
                        ->first();

                    if ($unresolved) {
                        $statusLabel = $unresolved->status === 'pending'
                            ? 'still pending approval'
                            : 'approved and runs until '.Carbon::parse($unresolved->end_date)->format('d M Y');
                        $fail("You already have a travel request that is {$statusLabel}. You can't have more than one travel request at a time - please wait until it concludes.");
                    }
                },
            ],
            'official_benefit' => 'required_if:travel_reason,official|nullable|string',
            'personal_description' => 'required_if:travel_reason,personal|nullable|string',
            'cost_coverage' => 'required|in:organizers_full,organizers_partial,university,private',
            // The create form's colleague dropdown only ever lists people in
            // the applicant's own department, but that's a UI filter, not
            // enforcement - without this it could be bypassed by anyone
            // submitting the request directly.
            'acting_staff_id' => $hasEligibleColleague ? [
                'required',
                'different:staff_id',
                Rule::exists('staff', 'id')->where('department_id', auth()->user()->department_id),
                function ($attribute, $value, $fail) use ($request) {
                    $onLeave = LeaveRequest::where('staff_id', $value)
                        ->where('status', 'approved')
                        ->where('start_date', '<=', $request->end_date)
                        ->where('end_date', '>=', $request->start_date)
                        ->exists();
                    if ($onLeave) {
                        $fail('This staff member is already on approved leave during that period and cannot be selected as acting staff.');

                        return;
                    }

                    // A person holds at most one position at a time.
                    if ($this->isAlreadyHoldingPosition($value, $request->start_date, $request->end_date)) {
                        $fail('This staff member is already holding another colleague\'s position during that period and cannot hold two positions at once.');
                    }
                },
            ] : ['nullable'],
            'acting_dean_id' => $isSchoolsDean
                ? ['required', Rule::in($eligibleActingDeanIds)]
                : ['nullable'],
            'invitation_letter' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'endorsement' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'letter_content' => 'nullable|string',
            // Required for every travel request, same reasoning as Leave's
            // own Handover Notes: whoever is stepping away from the office
            // needs someone else briefed to hold their duties.
            'handover_notes' => 'required|string|min:5',
            'signature_data' => 'nullable|string',
        ]);

        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        // diffInWeekdays()+1 overcounts whenever the end date falls on a
        // weekend - count each calendar day in the range directly instead.
        $days = CarbonPeriod::create($startDate, $endDate)->filter(fn ($d) => ! $d->isWeekend())->count();

        DB::beginTransaction();

        try {
            $actingStaffId = $hasEligibleColleague ? $request->acting_staff_id : $escalationContact->id;

            $travelRequest = TravelPermission::create([
                'staff_id' => auth()->id(),
                'acting_staff_id' => $actingStaffId,
                'acting_dean_id' => $isSchoolsDean ? $request->acting_dean_id : null,
                'destination' => $request->destination,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'days' => $days,
                'travel_reason' => $request->travel_reason,
                'official_benefit' => $request->official_benefit,
                'personal_description' => $request->personal_description,
                'cost_coverage' => $request->cost_coverage,
                'additional_details' => $request->additional_details,
                'letter_content' => $request->letter_content,
                'handover_notes' => $request->handover_notes,
                'acting_status' => 'pending',
                'acting_token' => Str::random(60),
                'current_approval_level' => 'acting',
            ]);

            // ✅ AUDIT LOG: Travel permission created
            AuditLogHelper::log('create', 'travel', $travelRequest->id, null, $travelRequest->toArray());

            // Handle file uploads
            if ($request->hasFile('invitation_letter')) {
                $path = $request->file('invitation_letter')->store('travel_documents/'.$travelRequest->id, 'local');
                $travelRequest->update(['invitation_letter_path' => $path]);
            }

            if ($request->hasFile('endorsement')) {
                $path = $request->file('endorsement')->store('travel_documents/'.$travelRequest->id, 'local');
                $travelRequest->update(['endorsement_path' => $path]);
            }

            // Notify acting staff
            $actingStaff = $travelRequest->actingStaff;
            $requester = $travelRequest->staff;

            if ($hasEligibleColleague) {
                Notification::create([
                    'user_id' => $actingStaff->id,
                    'type' => 'acting_request_travel',
                    'title' => 'Travel Permission - Acting Request',
                    'message' => "{$requester->first_name} {$requester->last_name} has requested you to cover their duties during travel to {$travelRequest->destination} from ".
                        Carbon::parse($travelRequest->start_date)->format('d M Y').' to '.
                        Carbon::parse($travelRequest->end_date)->format('d M Y'),
                    'action_url' => route('travel-permissions.acting.response', $travelRequest->acting_token),
                    'action_text' => 'Review Request',
                    'is_read' => false,
                ]);
            } else {
                // Nobody was left in the department to cover - the
                // escalation contact is notified to arrange coverage from
                // elsewhere (another department/faculty via HR).
                Notification::create([
                    'user_id' => $actingStaff->id,
                    'type' => 'acting_request_travel',
                    'title' => 'No One Available to Cover Duties',
                    'message' => "No colleague in {$requester->department->name} is available to cover {$requester->first_name} {$requester->last_name}'s duties during travel to {$travelRequest->destination} from ".
                        Carbon::parse($travelRequest->start_date)->format('d M Y').' to '.
                        Carbon::parse($travelRequest->end_date)->format('d M Y').
                        '. Please arrange coverage, including from another department or faculty if needed.',
                    'action_url' => route('travel-permissions.acting.response', $travelRequest->acting_token),
                    'action_text' => 'Arrange Coverage',
                    'is_read' => false,
                ]);
            }

            // Optional: the applicant's own Travel Permission Application
            // Letter, signed by them at the wizard's Sign step - only
            // created when there's actually something to sign. Mirrors
            // LeaveRequestController::store()'s application-letter block.
            if ($request->filled('letter_content') || $request->filled('handover_notes')) {
                try {
                    $signaturePath = null;
                    if ($request->filled('signature_data')) {
                        $signatureData = preg_replace('/^data:image\/\w+;base64,/', '', $request->signature_data);
                        $signaturePath = 'signatures/travel_letter_'.uniqid().'.png';
                        Storage::disk('public')->put($signaturePath, base64_decode($signatureData));
                    }

                    $renderedContent = view('components.letter.travel-application-body', [
                        'travelRequest' => $travelRequest,
                        'staff' => auth()->user(),
                        'letterContent' => $request->letter_content,
                        'handoverNotes' => $request->handover_notes,
                    ])->render();

                    TravelLetter::create([
                        'travel_permission_id' => $travelRequest->id,
                        'letter_number' => $this->generateTravelLetterNumber(),
                        'secure_code' => 'SMU-LTR-'.Str::upper(Str::random(8)),
                        'title' => 'Travel Permission Application Letter - '.$travelRequest->destination,
                        'content' => $renderedContent,
                        'type' => 'application',
                        'stage' => 'generated',
                        'generated_by' => auth()->user()->full_name,
                        'generated_by_role' => 'Applicant',
                        'signature_path' => $signaturePath,
                        'signatories' => [[
                            'name' => auth()->user()->full_name,
                            'role' => 'Applicant',
                            'signed_at' => now()->toDateTimeString(),
                        ]],
                        'cc_recipients' => [],
                        'metadata' => [
                            'handover_notes' => $request->handover_notes,
                        ],
                    ]);
                } catch (\Exception $e) {
                    \Log::error('Failed to create travel application letter: '.$e->getMessage());
                }
            }

            TravelPermissionDraft::where('staff_id', auth()->id())->delete();

            DB::commit();

            $successMessage = $hasEligibleColleague
                ? 'Travel permission request submitted! Waiting for acting staff approval.'
                : "Travel permission request submitted! No colleague was available to cover your duties - {$actingStaff->first_name} {$actingStaff->last_name} has been notified to arrange coverage.";

            return redirect()->route('travel-permissions.my-requests')
                ->with('success', $successMessage);

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Failed to submit request: '.$e->getMessage())
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

        if (auth()->id() !== $travelRequest->acting_staff_id) {
            abort(403, 'You are not authorized to respond to this request.');
        }

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
                    'rejection_reason' => 'Acting staff declined: '.($request->comment ?? 'No reason provided'),
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

            return back()->with('error', 'Failed to process response: '.$e->getMessage());
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
            if (! $unitHead) {
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
        $nextApproverIds = $this->getNextApproverIds($travelRequest, $nextLevel);

        if (empty($nextApproverIds)) {
            return;
        }

        $roleNames = [
            'unit_head' => 'Unit Head',
            'hod' => 'Head of Department',
            'dean' => 'Dean',
            'dvc' => 'DVC',
            'hr_final' => 'HR Officer',
        ];

        foreach ($nextApproverIds as $approverId) {
            Notification::create([
                'user_id' => $approverId,
                'type' => 'approval_pending_travel',
                'title' => 'Travel Permission Pending Approval',
                'message' => "Travel request from {$travelRequest->staff->first_name} {$travelRequest->staff->last_name} to {$travelRequest->destination} requires your approval as {$roleNames[$nextLevel]}.",
                'action_url' => route('travel-permissions.pending'),
                'action_text' => 'Review Request',
                'is_read' => false,
            ]);
        }
    }

    /**
     * unit_head/hod/dean are single seats (one FK column each), but dvc/
     * hr_final roles can be held by more than one person - notify all of
     * them, not just whichever one a ->first() query happens to return.
     */
    private function getNextApproverIds($travelRequest, $level): array
    {
        $staff = $travelRequest->staff;

        switch ($level) {
            case 'unit_head':
                // The applicant is their own department's Unit Head - the
                // acting staff they already picked stands in for this stage.
                if (($staff->department->unit_head_staff_id ?? null) == $staff->id) {
                    return $travelRequest->acting_staff_id ? [$travelRequest->acting_staff_id] : [];
                }
                $id = $staff->department->unit_head_staff_id ?? null;

                return $id ? [$id] : [];
            case 'hod':
                if (($staff->department->hod_staff_id ?? null) == $staff->id) {
                    return $travelRequest->acting_staff_id ? [$travelRequest->acting_staff_id] : [];
                }
                $id = $staff->department->hod_staff_id ?? null;

                return $id ? [$id] : [];
            case 'dean':
                $deanId = $staff->department->school->dean_staff_id ?? null;

                // The applicant is their own school's Dean - the HOD they
                // picked from within the school stands in as acting Dean.
                if ($deanId == $staff->id) {
                    return $travelRequest->acting_dean_id ? [$travelRequest->acting_dean_id] : [];
                }

                return $deanId ? [$deanId] : [];
            case 'dvc':
                // Notify only this staff member's own institute's DVC(s) -
                // never another university's, and never themself.
                if ($staff->staff_category == 'academic') {
                    return User::role('dvca')
                        ->where('university_id', $staff->university_id)
                        ->where('id', '!=', $staff->id)
                        ->pluck('id')->all();
                } else {
                    return User::role('dvc-pfa')
                        ->where('university_id', $staff->university_id)
                        ->where('id', '!=', $staff->id)
                        ->pluck('id')->all();
                }
            case 'hr_final':
                return User::role(['hr-officer', 'hr-manager'])->where('university_id', $staff->university_id)->pluck('id')->all();
            default:
                return [];
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

        $grandTotal = (clone $query)->count();

        if ($search = $request->get('search')) {
            $query->whereHas('staff', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('staff_id', 'like', "%{$search}%");
            });
        }

        $pendingRequests = $query->orderBy('created_at', 'asc')->paginate($perPage)->appends($request->query());

        if ($request->wantsJson()) {
            return response()->json([
                'html' => view('shared.travel-permissions._pending-approvals-results', compact('pendingRequests'))->render(),
                'total' => $pendingRequests->total(),
                'grand_total' => $grandTotal,
            ]);
        }

        return view('shared.travel-permissions.pending-approvals', compact('pendingRequests'));
    }

    /**
     * History of travel requests this user has approved or rejected
     */
    public function approvalsHistory(Request $request)
    {
        $perPage = $this->getPerPage($request, 10);
        $userId = auth()->id();

        $query = AuditLog::where('user_id', $userId)
            ->where('module', 'travel')
            ->whereIn('action', ['approve', 'reject'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('action')) {
            $query->where('action', $request->action === 'approved' ? 'approve' : 'reject');
        }

        if ($search = $request->get('search')) {
            $matchingIds = TravelPermission::whereHas('staff', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('staff_id', 'like', "%{$search}%");
            })->pluck('id');
            $query->whereIn('record_id', $matchingIds);
        }

        $logs = $query->paginate($perPage)->appends($request->query());

        $travelRequests = TravelPermission::with('staff')
            ->whereIn('id', $logs->getCollection()->pluck('record_id'))
            ->get()
            ->keyBy('id');

        $logs->getCollection()->transform(function (AuditLog $log) use ($travelRequests) {
            $log->travelRequest = $travelRequests->get($log->record_id);

            return $log;
        });

        $stats = [
            'total' => AuditLog::where('user_id', $userId)->where('module', 'travel')->whereIn('action', ['approve', 'reject'])->count(),
            'approved' => AuditLog::where('user_id', $userId)->where('module', 'travel')->where('action', 'approve')->count(),
            'rejected' => AuditLog::where('user_id', $userId)->where('module', 'travel')->where('action', 'reject')->count(),
            'this_month' => AuditLog::where('user_id', $userId)->where('module', 'travel')->whereIn('action', ['approve', 'reject'])->whereMonth('created_at', now()->month)->count(),
        ];

        if ($request->wantsJson()) {
            return response()->json([
                'html' => view('shared.travel-permissions._approvals-history-results', ['approvals' => $logs])->render(),
                'total' => $logs->total(),
                'grand_total' => (clone $query)->count(),
            ]);
        }

        return view('shared.travel-permissions.approvals-history', ['approvals' => $logs, 'stats' => $stats]);
    }

    public function approve(Request $request, $id)
    {
        $travelRequest = TravelPermission::findOrFail($id);

        if (! $travelRequest->canUserApprove(auth()->user())) {
            return redirect()->back()->with('error', 'You are not authorized to approve this request.');
        }

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

        if (! $travelRequest->canUserApprove(auth()->user())) {
            return redirect()->back()->with('error', 'You are not authorized to reject this request.');
        }

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
            'message' => 'Your travel permission request has been rejected. Reason: '.$request->rejection_reason,
            'action_url' => route('travel-permissions.my-requests'),
            'action_text' => 'View Details',
            'is_read' => false,
        ]);

        return redirect()->back()->with('success', 'Travel permission rejected.');
    }

    /**
     * Staff - Cancel their own pending travel request. Mirrors
     * PermissionRequestController::cancel().
     */
    public function cancel($id)
    {
        $travelRequest = TravelPermission::where('staff_id', auth()->id())
            ->where('status', 'pending')
            ->findOrFail($id);

        $travelRequest->update([
            'status' => 'cancelled',
        ]);

        AuditLogHelper::log('update', 'travel', $travelRequest->id,
            ['status' => 'pending'],
            ['status' => 'cancelled']
        );

        if (request()->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Travel permission request cancelled successfully.');
    }

    public function show($id)
    {
        $user = auth()->user();
        $authorizedRoles = ['admin', 'hod', 'dean', 'dvca', 'dvc-pfa', 'unit_head', 'hr-officer', 'hr-manager', 'vice-chancellor', 'internal-auditor', 'registrar'];

        $query = TravelPermission::with(['staff', 'actingStaff']);

        if ($user->hasAnyRole($authorizedRoles)) {
            // These are institute-level oversight/approval seats, not a
            // cross-institute one - a DVC/HR/dean etc. must only reach
            // requests from their own university (true super-admin excepted).
            // Admin sees all.
            if (! $user->hasRole('super-admin') && ! $user->hasRole('admin')) {
                $query->whereHas('staff', function ($q) use ($user) {
                    $q->where('university_id', $user->university_id);
                });
            }
            $travelRequest = $query->findOrFail($id);
        } else {
            $travelRequest = $query->where(function ($q) use ($user) {
                $q->where('staff_id', $user->id)
                    ->orWhere('acting_staff_id', $user->id)
                    ->orWhere('acting_dean_id', $user->id);
            })->findOrFail($id);
        }

        if ($user->id !== $travelRequest->staff_id && $user->id !== $travelRequest->acting_staff_id) {
            AuditLogHelper::log('view', 'travel', $travelRequest->id);
        }

        return view('shared.travel-permissions.show', compact('travelRequest'));
    }

    /**
     * Lightweight signature for the live-refresh poll on a single request's
     * detail page. Authorization mirrors show().
     */
    public function showHeartbeat($id)
    {
        $user = auth()->user();
        $authorizedRoles = ['admin', 'hod', 'dean', 'dvca', 'dvc-pfa', 'unit_head', 'hr-officer', 'hr-manager', 'vice-chancellor', 'internal-auditor', 'registrar'];

        $query = TravelPermission::with('staff');

        if ($user->hasAnyRole($authorizedRoles)) {
            if (! $user->hasRole('super-admin') && ! $user->hasRole('admin')) {
                $query->whereHas('staff', function ($q) use ($user) {
                    $q->where('university_id', $user->university_id);
                });
            }
            $travelRequest = $query->findOrFail($id);
        } else {
            $travelRequest = $query->where(function ($q) use ($user) {
                $q->where('staff_id', $user->id)
                    ->orWhere('acting_staff_id', $user->id)
                    ->orWhere('acting_dean_id', $user->id);
            })->findOrFail($id);
        }

        return response()->json(['signature' => (string) $travelRequest->updated_at]);
    }

    /**
     * Download/stream a travel permission document (invitation letter or endorsement).
     * Authorization mirrors show(): only the requester (staff_id) may view.
     */
    public function downloadDocument($id, $type)
    {
        $travelRequest = TravelPermission::where('staff_id', auth()->id())
            ->findOrFail($id);

        $column = match ($type) {
            'invitation_letter' => 'invitation_letter_path',
            'endorsement' => 'endorsement_path',
            default => null,
        };

        if (! $column || ! $travelRequest->{$column}) {
            abort(404);
        }

        $path = $travelRequest->{$column};

        if (! Storage::disk('local')->exists($path)) {
            abort(404, 'File not found.');
        }

        return Storage::disk('local')->response($path);
    }
}
