<?php

namespace App\Http\Controllers;

use App\Helpers\AuditLogHelper;
use App\Models\ActingDvcAssignment;
use App\Models\ApprovalDelegation;
use App\Models\ApprovalLog;
use App\Models\BlackoutPeriod;
use App\Models\Document;
use App\Models\HoldPositionHistory;
use App\Models\LeaveApprovalStage;
use App\Models\LeaveBalance;
use App\Models\LeaveLetter;
use App\Models\LeaveRequest;
use App\Models\LeaveRequestDraft;
use App\Models\LeaveType;
use App\Models\Letter;
use App\Models\Notification;
use App\Models\School;
use App\Models\StaffEmergencyContact;
use App\Models\University;
use App\Models\User;
use App\Services\LeavePolicy;
use App\Traits\ActingStaffAvailabilityTrait;
use App\Traits\LetterTrait;
use App\Traits\PerPageTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class LeaveRequestController extends Controller
{
    use ActingStaffAvailabilityTrait, LetterTrait, PerPageTrait;

    /**
     * Block acting on a leave request that belongs to a different institute -
     * a DVC/HR seat at one university must never reach another's requests,
     * even by guessing the URL directly.
     */
    private function assertSameUniversity(LeaveRequest $leaveRequest): void
    {
        $user = auth()->user();
        abort_unless(
            $user->hasRole('super-admin') || $leaveRequest->staff->university_id === $user->university_id,
            403,
            'You do not have permission to act on this request.'
        );
    }

    /**
     * Block cross-institute selection for an arbitrary user.
     */
    private function assertSameUniversityForUser(User $target): void
    {
        $user = auth()->user();
        abort_unless(
            $user->hasRole('super-admin') || $target->university_id === $user->university_id,
            403,
            'You do not have permission to act for this staff member.'
        );
    }

    /**
     * Display staff's own leave requests
     */
    public function myRequests(Request $request)
    {
        $perPage = $this->getPerPage($request, 10);

        $query = LeaveRequest::with(['leaveType', 'actingStaff'])
            ->where('staff_id', auth()->id())
            ->orderBy('created_at', 'desc');

        $grandTotal = (clone $query)->count();

        if ($search = $request->get('search')) {
            $query->whereHas('leaveType', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $leaveRequests = $query->paginate($perPage)->appends($request->query());

        $stats = [
            'total' => LeaveRequest::where('staff_id', auth()->id())->count(),
            'pending' => LeaveRequest::where('staff_id', auth()->id())->where('status', 'pending')->count(),
            'approved' => LeaveRequest::where('staff_id', auth()->id())->where('status', 'approved')->count(),
            'rejected' => LeaveRequest::where('staff_id', auth()->id())->where('status', 'rejected')->count(),
        ];

        if ($request->wantsJson()) {
            return response()->json([
                'html' => view('shared.leave-requests._my-requests-results', compact('leaveRequests'))->render(),
                'total' => $leaveRequests->total(),
                'grand_total' => $grandTotal,
            ]);
        }

        return view('shared.leave-requests.my-requests', compact('leaveRequests', 'stats'));
    }

    /**
     * Staff - Create Request Form
     */
    public function create(Request $request)
    {
        $leaveTypes = LeaveType::where('status', 'active')->get();

        // Support submitting leave on behalf of another staff member
        $onBehalfStaff = null;
        if ($request->filled('behalf_staff_id') && auth()->user()->can('submit_leave_on_behalf')) {
            $onBehalfStaff = User::findOrFail($request->behalf_staff_id);
            $this->assertSameUniversityForUser($onBehalfStaff);
        }

        $applicant = $onBehalfStaff ?? auth()->user();

        // Someone already accepted to hold another colleague's position
        // (Leave or Travel) right now is unavailable to take on a second one
        // - a person holds at most one position at a time. Exact date range
        // isn't known yet at this point (chosen next in the form), so this
        // only catches an assignment active today; store()'s own validation
        // below is the real, date-aware gate.
        $busyHolders = $this->busyHolderIds(now()->toDateString(), now()->toDateString());

        $colleagues = $this->eligibleActingHolders($applicant, $busyHolders);

        // Nobody left in the department to hold the position - don't block
        // the application, but let the requester know who'll be notified to
        // sort out coverage from elsewhere.
        $escalationContact = $colleagues->isEmpty() ? $this->escalationTargetFor($applicant) : null;

        // The Dean of a school would otherwise have to approve their own
        // leave at the Dean stage - if that's who's applying, they pick an
        // HOD from their own school to stand in as acting Dean for that
        // one stage only.
        $isSchoolsDean = $this->isSchoolsDean($applicant);
        $actingDeanOptions = $isSchoolsDean
            ? $this->eligibleActingDeans($applicant)
            : collect();

        $processSteps = $this->processStepsFor($applicant, $isSchoolsDean);

        $draft = LeaveRequestDraft::where('staff_id', $applicant->id)->first();
        $draftDocuments = Document::where('user_id', $applicant->id)->whereNull('leave_request_id')->get();

        // The applicant's own limits (the on-behalf target's, when filing for
        // someone else) so the form can steer the choice; store() enforces.
        $policyLimits = app(LeavePolicy::class)->forForm($applicant, $leaveTypes);

        return view('shared.leave-requests.create', compact('leaveTypes', 'colleagues', 'escalationContact', 'actingDeanOptions', 'processSteps', 'onBehalfStaff', 'draft', 'draftDocuments', 'policyLimits'));
    }

    /**
     * Save (or update) the applicant's single in-progress leave request
     * draft, and move the wizard to whichever step it's headed to.
     * Deliberately lenient - a draft is allowed to be incomplete.
     */
    public function saveDraft(Request $request)
    {
        $applicant = auth()->user();

        $data = $request->only([
            'leave_type_id', 'start_date', 'end_date', 'expected_return_date', 'half_day',
            'acting_staff_id', 'acting_dean_id',
            'emergency_contact_id', 'emergency_contact_name', 'emergency_contact_phone',
            'contact_number_during_leave', 'leave_address', 'travel_destination',
            'benefit_to_university', 'cost_coverage', 'is_first_time_applicant',
            'handover_notes', 'reason', 'letter_content',
        ]);

        // "Next" deliberately skips field-level validation so a draft can be
        // saved exactly as typed, however incomplete - the applicant must
        // always be able to save partial progress and come back later. But
        // Review and Sign are built entirely around the request actually
        // having a type, dates and an explanation - reaching either one
        // without those isn't "an incomplete draft", it's an empty page.
        // The data is saved regardless; only which step it opens on is
        // clamped back to 1 when the essentials aren't there yet.
        // saveDraft() is only ever reached from the self-service wizard (an
        // on-behalf submission has no draft/Review/Sign steps at all), so
        // Emergency Contact is always one of the required cards here.
        $requestedStep = (int) $request->input('current_step', 1);
        $coreFieldsPresent = filled($data['leave_type_id'] ?? null)
            && filled($data['start_date'] ?? null)
            && filled($data['end_date'] ?? null)
            && filled($data['emergency_contact_name'] ?? null)
            && filled($data['emergency_contact_phone'] ?? null)
            && filled($data['contact_number_during_leave'] ?? null)
            && filled($data['leave_address'] ?? null)
            && filled($data['travel_destination'] ?? null)
            && filled($data['handover_notes'] ?? null)
            && filled($data['reason'] ?? null);
        $currentStep = ($requestedStep > 1 && ! $coreFieldsPresent) ? 1 : $requestedStep;

        LeaveRequestDraft::updateOrCreate(
            ['staff_id' => $applicant->id],
            [
                'form_data' => $data,
                'current_step' => $currentStep,
            ]
        );

        $redirect = redirect()->route('leave-requests.create');

        if ($currentStep === 1 && $requestedStep > 1) {
            $redirect->with('error', 'Please fill in the leave details, emergency and leave contacts, handover notes and reason before continuing - what you\'ve entered so far has been saved.');
        }

        return $redirect;
    }

    /**
     * Discard the applicant's in-progress draft entirely ("Start Over").
     * Pre-uploaded supporting documents that were never attached to a real
     * request are removed too, so they don't linger as orphaned uploads.
     */
    public function discardDraft(Request $request)
    {
        $applicant = auth()->user();

        Document::where('user_id', $applicant->id)->whereNull('leave_request_id')->get()
            ->each(function (Document $document) {
                Storage::disk('local')->delete($document->file_path);
                $document->delete();
            });

        LeaveRequestDraft::where('staff_id', $applicant->id)->delete();

        return redirect()->route('leave-requests.create');
    }

    /**
     * Upload a supporting document ahead of final submission, so it
     * survives a resumed draft session (an HTML file input can't otherwise
     * be repopulated). Attached to a real LeaveRequest only at store().
     */
    public function uploadDraftDocument(Request $request)
    {
        $request->validate([
            'document' => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120',
        ]);

        $applicant = auth()->user();

        // The JS widget only stops offering the file picker after 5, which
        // is a UI nicety, not enforcement - without a server-side check,
        // repeated direct requests to this endpoint could pile up documents
        // with no limit.
        $existingCount = Document::where('user_id', $applicant->id)->whereNull('leave_request_id')->count();
        abort_if($existingCount >= 5, 422, 'You can attach at most 5 supporting documents.');

        $file = $request->file('document');
        $path = $file->store('leave_documents/draft-'.$applicant->id, 'local');

        $extension = strtolower($file->getClientOriginalExtension());
        $fileType = $extension === 'jpeg' ? 'jpg' : $extension;

        $document = Document::create([
            'user_id' => $applicant->id,
            'leave_request_id' => null,
            'original_name' => $file->getClientOriginalName(),
            'stored_name' => basename($path),
            'file_path' => $path,
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'file_type' => $fileType,
            'uploaded_by' => $applicant->id,
        ]);

        return response()->json([
            'id' => $document->id,
            'name' => $document->original_name,
            'size' => $document->file_size,
        ]);
    }

    /**
     * Remove one pre-uploaded draft document (before it's attached to a
     * real request).
     */
    public function deleteDraftDocument(Document $document)
    {
        abort_unless(
            $document->user_id === auth()->id() && is_null($document->leave_request_id),
            403
        );

        Storage::disk('local')->delete($document->file_path);
        $document->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Render the applicant's own Leave Application Letter on the real
     * letterhead from their current draft, for the Review step's iframe.
     * Nothing here is persisted - this is a preview only.
     */
    public function previewDraftLetter(Request $request)
    {
        $applicant = auth()->user();
        $draft = LeaveRequestDraft::where('staff_id', $applicant->id)->firstOrFail();
        $data = $draft->form_data ?? [];

        $leaveRequest = new LeaveRequest([
            'start_date' => $data['start_date'] ?? null,
            'end_date' => $data['end_date'] ?? null,
            'days' => (isset($data['start_date'], $data['end_date']) && $data['start_date'] && $data['end_date'])
                ? (in_array($data['half_day'] ?? 'full', ['morning', 'afternoon'], true)
                    ? 0.5
                    : Carbon::parse($data['start_date'])->diffInDays(Carbon::parse($data['end_date'])) + 1)
                : null,
            'half_day' => $data['half_day'] ?? 'full',
            'contact_number_during_leave' => $data['contact_number_during_leave'] ?? null,
            'leave_address' => $data['leave_address'] ?? null,
            'travel_destination' => $data['travel_destination'] ?? null,
        ]);
        $leaveRequest->setRelation('leaveType', LeaveType::find($data['leave_type_id'] ?? null));
        $leaveRequest->setRelation('actingStaff', User::find($data['acting_staff_id'] ?? null));

        $signatureImage = $draft->signature_data ?: null;

        return view('components.letter.leave-application', [
            'leaveRequest' => $leaveRequest,
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
     * Select a staff member to submit leave on behalf of.
     */
    public function onBehalfForm(Request $request)
    {
        $search = $request->get('search');

        $query = User::where('status', 'active')
            ->where('id', '!=', auth()->id())
            ->orderBy('last_name')
            ->orderBy('first_name');

        if (! auth()->user()->hasRole('super-admin')) {
            $query->where('university_id', auth()->user()->university_id);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('middle_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('staff_id', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $perPage = $this->getPerPage($request, 20);
        $grandTotal = (clone $query)->count();
        $staffMembers = $query->paginate($perPage)->appends($request->query());

        if ($request->wantsJson()) {
            return response()->json([
                'html' => view('shared.leave-requests._on-behalf-results', compact('staffMembers'))->render(),
                'total' => $staffMembers->total(),
                'grand_total' => $grandTotal,
            ]);
        }

        return view('shared.leave-requests.on-behalf', compact('staffMembers'));
    }

    /**
     * The "what happens next" list shown on the application form - built
     * from the same skip rules the routing itself uses (initialApprovalLevel
     * / getNextLevelAfterApproval), so it can't drift out of sync with what
     * actually happens once the request is submitted.
     */
    private function processStepsFor(User $staff, bool $isSchoolsDean): array
    {
        $steps = ['Select colleague to hold your position', 'Colleague accepts/declines (notification sent)'];

        if ($staff->hasRole('vice-chancellor')) {
            $steps[] = 'Review chain is skipped entirely - HR Logistics processes your final balance directly';

            return $steps;
        }

        if ($staff->hasRole('dvca') || $staff->hasRole('dvc-pfa')) {
            $steps[] = 'HOD, Dean, HR and DVC review stages are skipped - HR Logistics processes your final balance directly';

            return $steps;
        }

        if ($this->isDean($staff) || $isSchoolsDean) {
            $steps[] = 'HOD and Dean stages are skipped';
            $steps[] = 'HR reviews';
            $steps[] = 'DVC reviews';
            $steps[] = 'A different HR officer processes final balance';

            return $steps;
        }

        if ($staff->hasRole('hr-officer') || $staff->hasRole('hr-manager')) {
            $steps[] = 'HOD and Dean stages are skipped - goes straight to another HR officer to review';
            $steps[] = 'HR reviews (a different HR officer/manager - not you)';
            $steps[] = 'DVC reviews';
            $steps[] = 'A different HR officer processes final balance';

            return $steps;
        }

        $isOwnHod = ($staff->department->hod_staff_id ?? null) == $staff->id;
        $hasDean = (bool) ($staff->department->school->dean_staff_id ?? null);

        $steps[] = $isOwnHod
            ? 'HOD stage is skipped (you hold that seat) - goes straight to Dean'
            : 'Head of Department reviews';

        $steps[] = match (true) {
            $isSchoolsDean => 'Dean stage is skipped (you hold that seat) - goes straight to HR',
            ! $hasDean => 'Dean stage is skipped (no Dean assigned to your school yet) - goes straight to HR',
            default => 'Dean reviews',
        };

        $steps[] = 'HR reviews';
        $steps[] = 'DVC reviews';
        $steps[] = 'HR Officer processes final balance';

        return $steps;
    }

    /**
     * Whether this staff member is the Dean of their own school - meaning
     * their own leave would otherwise reach the Dean stage with nobody but
     * themselves able to approve it.
     */
    private function isSchoolsDean(User $staff): bool
    {
        $school = $staff->department?->school;

        return $school && $school->dean_staff_id == $staff->id;
    }

    /**
     * Whether this staff member holds a Dean seat in any school, either by
     * the Spatie role or by being assigned as dean_staff_id on a school.
     */
    private function isDean(User $staff): bool
    {
        return $staff->hasRole('dean')
            || School::where('dean_staff_id', $staff->id)->exists();
    }

    /**
     * The school over which this staff member presides as Dean.
     * Falls back to their own department's school if no direct seat exists.
     */
    private function deanSchoolId(User $staff): ?int
    {
        $deanSchool = School::where('dean_staff_id', $staff->id)->first();

        return $deanSchool?->id ?? $staff->department?->school_id;
    }

    /**
     * HODs within a Dean's own school, eligible to stand in as acting Dean
     * for their leave request.
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
     * Who to hand the "find someone to hold this position" problem to when
     * the requester's own eligible pool is completely exhausted (everyone
     * either busy or already away) - one rung up the organogram from
     * whichever seat the requester themselves holds, since a lower seat
     * can't be asked to arrange coverage for a higher one. Nobody sits
     * above the Vice Chancellor in this system, and DVC has no clean "one
     * rung up" that isn't the VC itself (which would loop back to the
     * request's own applicant chain) - both fall back to HR, the same
     * last-resort contact already used when no escalation target exists
     * at all.
     */
    private function escalationTargetFor(User $staff): ?User
    {
        if ($staff->hasRole('vice-chancellor')) {
            return User::role('hr-manager')->where('university_id', $staff->university_id)->first();
        }

        if ($this->isSchoolsDean($staff)) {
            return $staff->staff_category === 'academic'
                ? User::role('dvca')->where('university_id', $staff->university_id)->first()
                : User::role('dvc-pfa')->where('university_id', $staff->university_id)->first();
        }

        if ($staff->hasRole('dvca') || $staff->hasRole('dvc-pfa')) {
            return User::role('hr-manager')->where('university_id', $staff->university_id)->first();
        }

        if (($staff->department->hod_staff_id ?? null) == $staff->id) {
            return $staff->department->school->dean ?? null;
        }

        $department = $staff->department;

        return $department?->unitHead ?? $department?->hod;
    }

    /**
     * Who's eligible to hold this staff member's position while they're
     * away - purely administrative coverage, not an approval role (that's
     * handled separately by the review chain, which skips or reassigns
     * around self-collisions on its own).
     *
     * Acting staff eligibility is role-specific:
     * - Vice Chancellor: covered by DVCs only.
     * - DVCs: covered by fellow DVCs, Deans or Director-level staff.
     * - Deans: covered by Heads of Department within the same school/faculty.
     * - Everyone else: covered by active colleagues in the same department.
     */
    private function eligibleActingHolders(User $staff, Collection|array $busyHolderIds = [], $startDate = null, $endDate = null): Collection
    {
        $excludeOnLeave = function ($q) use ($startDate, $endDate) {
            $q->where('status', 'approved')
                ->where('start_date', '<=', $endDate ?? now())
                ->where('end_date', '>=', $startDate ?? now());
        };

        if ($staff->hasRole('vice-chancellor')) {
            return User::role(['dvca', 'dvc-pfa'])
                ->where('university_id', $staff->university_id)
                ->where('id', '!=', $staff->id)
                ->where('status', 'active')
                ->whereDoesntHave('leaveRequests', $excludeOnLeave)
                ->whereNotIn('id', $busyHolderIds)
                ->get();
        }

        if ($staff->hasRole('dvca') || $staff->hasRole('dvc-pfa')) {
            return User::role(['dvca', 'dvc-pfa', 'dean', 'registrar'])
                ->where('university_id', $staff->university_id)
                ->where('id', '!=', $staff->id)
                ->where('status', 'active')
                ->whereDoesntHave('leaveRequests', $excludeOnLeave)
                ->whereNotIn('id', $busyHolderIds)
                ->get();
        }

        if ($this->isDean($staff)) {
            $schoolId = $this->deanSchoolId($staff);

            if (! $schoolId) {
                return collect();
            }

            return User::role('hod')
                ->whereHas('department', function ($q) use ($schoolId) {
                    $q->where('school_id', $schoolId);
                })
                ->where('id', '!=', $staff->id)
                ->where('status', 'active')
                ->whereDoesntHave('leaveRequests', $excludeOnLeave)
                ->whereNotIn('id', $busyHolderIds)
                ->get();
        }

        if (! $staff->department_id) {
            return collect();
        }

        return User::where('department_id', $staff->department_id)
            ->where('id', '!=', $staff->id)
            ->where('status', 'active')
            ->whereDoesntHave('leaveRequests', $excludeOnLeave)
            ->whereNotIn('id', $busyHolderIds)
            ->get();
    }

    /**
     * The level a leave request's review chain starts at once the acting
     * staff arrangement is settled. Normally HOD, the first rung of the
     * chain - but the Vice Chancellor has no HOD/Dean/DVC above them, so
     * their own leave skips the review chain entirely and goes straight to
     * HR Logistics for administrative recording (balance deducted, letter
     * generated) rather than being "approved" by one of their subordinates.
     *
     * DVCs are similarly senior - their leave skips the review chain and
     * goes straight to HR Logistics, with acting cover drawn from fellow
     * DVCs, Deans or Director-level staff.
     *
     * Deans sit above the HOD/Dean review stages - their own leave starts
     * at HR review, with acting cover drawn from HODs in the same school.
     *
     * An HOD's own leave has the same problem one rung down: nobody else
     * holds their HOD seat, so that stage is skipped too - straight to
     * Dean, or to HR if the school has no Dean assigned either.
     *
     * HR staff sit administratively parallel to HOD/Dean rather than under
     * them - even when the real HOD/Dean is a completely different person
     * with no collision at all, an HR officer's own leave still skips
     * straight to the HR stage, where a different HR officer reviews it.
     */
    private function initialApprovalLevel(User $staff): int
    {
        if ($staff->hasRole('vice-chancellor')) {
            return LeaveRequest::LEVEL_HR_LOGISTICS;
        }

        if ($staff->hasRole('dvca') || $staff->hasRole('dvc-pfa')) {
            return LeaveRequest::LEVEL_HR_LOGISTICS;
        }

        if ($this->isDean($staff)) {
            // Dean's own leave can't pass through the Dean stage itself -
            // skip to whatever the configured chain order has right after
            // Dean (normally HR, but that depends on the admin's ordering).
            return LeaveApprovalStage::nextLevelAfter(LeaveRequest::LEVEL_DEAN) ?? LeaveRequest::LEVEL_HR_LOGISTICS;
        }

        if ($staff->hasRole('hr-officer') || $staff->hasRole('hr-manager')) {
            return LeaveRequest::LEVEL_HR;
        }

        if (($staff->department->hod_staff_id ?? null) == $staff->id) {
            return ($staff->department->school->dean_staff_id ?? null)
                ? LeaveRequest::LEVEL_DEAN
                // No Dean assigned to the school - same as the
                // Dean-self-collision case above, skip to whatever comes
                // after Dean in the configured chain.
                : (LeaveApprovalStage::nextLevelAfter(LeaveRequest::LEVEL_DEAN) ?? LeaveRequest::LEVEL_HR_LOGISTICS);
        }

        // Ordinary staff enter at whichever stage is actually first in the
        // configured chain - not hardcoded to HOD, so that reordering the
        // chain (e.g. putting HR before HOD) takes effect for new
        // submissions too, not just for requests already mid-chain.
        return LeaveApprovalStage::firstLevel() ?? LeaveRequest::LEVEL_HOD;
    }

    /**
     * Staff - Store Request
     */
    public function store(Request $request)
    {
        try {
            // Precompute whether there's actually anyone eligible to hold
            // this position for these exact dates (same-department for most
            // staff, DVCs only for the Vice Chancellor). If not, the
            // requester isn't blocked - acting_staff_id becomes optional and
            // the Head of Unit (or HOD) is auto-assigned as the holder below,
            // with a notification asking them to arrange coverage.
            $hasEligibleColleague = false;
            $escalationContact = null;
            $eligibleHolderIds = collect();

            $isOnBehalf = $request->filled('behalf_staff_id') && auth()->user()->can('submit_leave_on_behalf');
            $applicant = $isOnBehalf ? User::findOrFail($request->behalf_staff_id) : auth()->user();
            $submitter = auth()->user();
            $this->assertSameUniversityForUser($applicant);

            if ($request->filled(['start_date', 'end_date'])) {
                $busyHolders = $this->busyHolderIds($request->start_date, $request->end_date);

                $eligibleHolderIds = $this->eligibleActingHolders(
                    $applicant, $busyHolders, $request->start_date, $request->end_date
                )->pluck('id');

                $hasEligibleColleague = $eligibleHolderIds->isNotEmpty();

                if (! $hasEligibleColleague) {
                    $escalationContact = $this->escalationTargetFor($applicant);

                    if (! $escalationContact) {
                        return redirect()->back()
                            ->with('error', 'No colleague is available to hold your position, and nobody is set up to escalate to. Please contact HR directly.')
                            ->withInput();
                    }
                }
            }

            // The Dean of a school would otherwise have to approve their
            // own leave at the Dean stage - if that's who's applying, they
            // must name an HOD from their own school to stand in.
            $isSchoolsDean = $this->isSchoolsDean($applicant);
            $eligibleActingDeanIds = $isSchoolsDean
                ? $this->eligibleActingDeans($applicant)->pluck('id')
                : collect();

            $request->validate([
                'leave_type_id' => [
                    'required',
                    'exists:leave_types,id',
                    // "Stop and wait": nobody can have more than one leave
                    // request in flight at a time, regardless of type - block
                    // a new one while an earlier leave (any type) hasn't
                    // concluded yet, either still working through approval,
                    // or already approved but its end date hasn't passed.
                    function ($attribute, $value, $fail) use ($applicant) {
                        $unresolved = LeaveRequest::where('staff_id', $applicant->id)
                            ->where(function ($q) {
                                $q->where('status', 'pending')
                                    ->orWhere(function ($q2) {
                                        $q2->where('status', 'approved')
                                            ->where('end_date', '>=', now()->toDateString());
                                    });
                            })
                            ->first();

                        if ($unresolved) {
                            $typeName = $unresolved->leaveType->name ?? 'Unknown';
                            $statusLabel = $unresolved->status === 'pending'
                                ? 'still pending approval'
                                : 'approved and runs until '.Carbon::parse($unresolved->end_date)->format('d M Y');
                            $fail("This staff already has a leave request ({$typeName}) that is {$statusLabel}. They can't have more than one leave request at a time - please wait until it concludes.");
                        }
                    },
                ],
                'start_date' => 'required|date|after_or_equal:today',
                'end_date' => 'required|date|after_or_equal:start_date',
                'reason' => 'required|string|min:10',
                // The create form's dropdown only ever lists the eligible
                // pool (same-department, or DVCs only for the VC), but
                // that's a UI filter, not enforcement - without this the
                // same restriction could be bypassed by anyone submitting
                // the request directly.
                'acting_staff_id' => $hasEligibleColleague ? [
                    'required',
                    'different:staff_id',
                    Rule::in($eligibleHolderIds),
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
                // Whether it's used, and how far after the leave it may fall,
                // is the university's return-date policy (LeavePolicy).
                'expected_return_date' => 'nullable|date',
                // Whether half a day is allowed is the university's policy
                // (LeavePolicy); this only checks it is one of the known values.
                'half_day' => 'nullable|in:full,morning,afternoon',
                // The Emergency Contact card doesn't exist at all for an
                // on-behalf submission, so nothing can be required there.
                'emergency_contact_name' => $isOnBehalf ? 'nullable|string|max:100' : 'required|string|max:100',
                'emergency_contact_phone' => $isOnBehalf ? 'nullable|string|max:20' : 'required|string|max:20',
                'contact_number_during_leave' => 'required|string|max:20',
                'leave_address' => 'required|string|max:255',
                'travel_destination' => 'required|string|max:255',
                // Required for every leave type - whoever holds the applicant's
                // position while they're away needs this regardless of why they're gone.
                'handover_notes' => 'required|string|min:5',
                'supporting_documents.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120',
                'benefit_to_university' => 'nullable|string',
                'cost_coverage' => 'nullable|in:organizers_full,organizers_partial,university,private',
                'is_first_time_applicant' => 'nullable|boolean',
                'letter_content' => 'nullable|string',
                'signature_data' => 'nullable|string',
            ]);

            // BLACKOUT PERIOD CHECK
            $blackout = BlackoutPeriod::where('start_date', '<=', $request->end_date)
                ->where('end_date', '>=', $request->start_date)
                ->where('status', 'active')
                ->first();

            if ($blackout && $blackout->restriction_type == 'hard') {
                return redirect()->back()
                    ->with('error', "Cannot apply for leave during blackout period: {$blackout->name} (From {$blackout->start_date} to {$blackout->end_date})")
                    ->withInput();
            }

            $startDate = Carbon::parse($request->start_date);
            $endDate = Carbon::parse($request->end_date);

            // HR POLICY: notice, longest request, yearly entitlement and the
            // return date all come from the applicant's university policy.
            $policyCheck = app(LeavePolicy::class)->evaluate(
                $applicant,
                LeaveType::findOrFail($request->leave_type_id),
                $startDate,
                $endDate,
                $request->filled('expected_return_date') ? Carbon::parse($request->expected_return_date) : null,
                null,
                $request->input('half_day', 'full') ?? 'full'
            );

            if ($policyCheck['errors']) {
                throw ValidationException::withMessages($policyCheck['errors']);
            }

            // Every calendar day in the range counts (weekends and public
            // holidays are NOT excluded, per institution policy), or half a
            // day where the policy allows one.
            $days = $policyCheck['days'];

            DB::beginTransaction();

            $nextLevel = $this->initialApprovalLevel($applicant);
            $actingStaffId = $hasEligibleColleague ? $request->acting_staff_id : $escalationContact->id;

            $leaveRequest = LeaveRequest::create([
                'staff_id' => $applicant->id,
                'acting_staff_id' => $actingStaffId,
                'is_escalation_holder' => ! $hasEligibleColleague,
                'acting_dean_id' => $isSchoolsDean ? $request->acting_dean_id : null,
                'leave_type_id' => $request->leave_type_id,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'days' => $days,
                'half_day' => $policyCheck['half_day'],
                'reason' => $request->reason,
                'status' => 'pending',
                'acting_status' => 'pending',
                'acting_token' => Str::random(60),
                'current_approval_level' => $nextLevel,
                'letter_content' => $request->letter_content,
                'expected_return_date' => $policyCheck['return_date']->toDateString(),
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

            // A contact typed by hand (rather than picked from the saved
            // list) is worth keeping for next time, on the applicant's own
            // profile - not the submitter's, for an on-behalf request. Best
            // effort: this is a convenience, not something that should ever
            // sink the leave request itself if it fails.
            if (! $request->filled('emergency_contact_id') && $request->filled('emergency_contact_name') && $request->filled('emergency_contact_phone')) {
                try {
                    $this->saveEmergencyContactIfNew($applicant, $request->emergency_contact_name, $request->emergency_contact_phone);
                } catch (\Exception $e) {
                    \Log::error('Failed to save emergency contact for reuse: '.$e->getMessage());
                }
            }

            // Create draft letter
            if ($request->filled('letter_content')) {
                try {
                    $letterNumber = $this->generateLetterNumber();

                    $letter = LeaveLetter::create([
                        'leave_request_id' => $leaveRequest->id,
                        'staff_id' => $applicant->id,
                        'letter_number' => $letterNumber,
                        'secure_code' => 'SMU-LTR-'.Str::upper(Str::random(8)),
                        'title' => 'Leave Approval Letter - '.($leaveRequest->leaveType->name ?? 'Leave Request'),
                        'content' => $request->letter_content,
                        'type' => 'approval',
                        'stage' => 'draft',
                        'generated_by' => $submitter->full_name,
                        'generated_by_role' => $submitter->getRoleNames()->first(),
                        'payment_status' => 'paid',
                        // LeaveLetter casts these columns as 'array' -
                        // Eloquent encodes them to JSON on save, so an
                        // already-encoded string here would double-encode.
                        'signatories' => [],
                        'cc_recipients' => [],
                        'metadata' => [
                            'leave_type' => $leaveRequest->leaveType->name ?? null,
                            'days' => $days,
                            'start_date' => $request->start_date,
                            'end_date' => $request->end_date,
                        ],
                    ]);

                    \Log::info('Draft leave letter created', [
                        'leave_request_id' => $leaveRequest->id,
                        'letter_id' => $letter->id,
                        'letter_number' => $letterNumber,
                    ]);

                } catch (\Exception $e) {
                    \Log::error('Failed to create draft letter: '.$e->getMessage());
                }
            }

            AuditLogHelper::log('create', 'leave', $leaveRequest->id, null, $leaveRequest->toArray());

            HoldPositionHistory::create([
                'leave_request_id' => $leaveRequest->id,
                'requester_staff_id' => $applicant->id,
                // Not $request->acting_staff_id - that's null in the
                // escalation branch, since nobody was actually picked yet.
                // $actingStaffId is whichever of the two actually applies.
                'holder_staff_id' => $actingStaffId,
                'status' => 'pending',
                'requested_at' => now(),
                'token' => $leaveRequest->acting_token,
            ]);

            if ($request->hasFile('supporting_documents')) {
                foreach ($request->file('supporting_documents') as $file) {
                    $path = $file->store('leave_documents/'.$leaveRequest->id, 'local');

                    // file_type is a fixed enum (pdf/jpg/png/doc/docx) on this
                    // table, not a free-form mime string - "jpeg" (a valid
                    // upload extension) has to normalize to "jpg" to fit it.
                    $extension = strtolower($file->getClientOriginalExtension());
                    $fileType = $extension === 'jpeg' ? 'jpg' : $extension;

                    Document::create([
                        'user_id' => $applicant->id,
                        'leave_request_id' => $leaveRequest->id,
                        'original_name' => $file->getClientOriginalName(),
                        'stored_name' => basename($path),
                        'file_path' => $path,
                        'file_size' => $file->getSize(),
                        'mime_type' => $file->getMimeType(),
                        'file_type' => $fileType,
                        'uploaded_by' => $submitter->id,
                    ]);
                }
            }

            // The draft wizard (upload-now documents, the self-signed
            // application letter below, the draft row itself) only exists
            // on the self-service path - it's keyed by $applicant, and an
            // on-behalf submission's $applicant is the TARGET staff member,
            // not the person submitting. Without this guard, someone
            // submitting on behalf of a colleague would silently sweep up
            // and delete that colleague's own unrelated, unfinished wizard
            // draft and documents.
            if (! $isOnBehalf) {
                // Any documents uploaded ahead of time from the draft wizard
                // (upload-now-attach-later, since a file input can't
                // survive a resumed session) get attached to the real
                // request here.
                Document::where('user_id', $applicant->id)
                    ->whereNull('leave_request_id')
                    ->update(['leave_request_id' => $leaveRequest->id]);

                // Optional: the applicant's own Leave Application Letter,
                // signed by them at the wizard's Sign step - separate from
                // the HR approval letter above (distinguished by `type`),
                // and only created when there's actually something to sign.
                if ($request->filled('letter_content') || $request->filled('handover_notes')) {
                    try {
                        $signaturePath = null;
                        if ($request->filled('signature_data')) {
                            $signatureData = preg_replace('/^data:image\/\w+;base64,/', '', $request->signature_data);
                            $signaturePath = 'signatures/leave_letter_'.uniqid().'.png';
                            Storage::disk('public')->put($signaturePath, base64_decode($signatureData));
                        }

                        // `content` is NOT NULL - handover_notes alone (with
                        // no letter_content) can trigger this block, so it
                        // can't just be $request->letter_content verbatim.
                        // Render the real body the letter shows, matching
                        // this column's own "Full HTML content" convention
                        // (see migration).
                        $renderedContent = view('components.letter.application-body', [
                            'leaveRequest' => $leaveRequest,
                            'staff' => $applicant,
                            'letterContent' => $request->letter_content,
                            'handoverNotes' => $request->handover_notes,
                        ])->render();

                        LeaveLetter::create([
                            'leave_request_id' => $leaveRequest->id,
                            'staff_id' => $applicant->id,
                            'letter_number' => $this->generateLetterNumber(),
                            'secure_code' => 'SMU-LTR-'.Str::upper(Str::random(8)),
                            'title' => 'Leave Application Letter - '.($leaveRequest->leaveType->name ?? 'Leave Request'),
                            'content' => $renderedContent,
                            'type' => 'application',
                            'stage' => 'generated',
                            'generated_by' => $applicant->full_name,
                            'generated_by_role' => 'Applicant',
                            'signature_path' => $signaturePath,
                            // LeaveLetter casts these columns as 'array' -
                            // Eloquent encodes them to JSON on save, so
                            // passing already-encoded strings here would
                            // double-encode them.
                            'signatories' => [[
                                'name' => $applicant->full_name,
                                'role' => 'Applicant',
                                'signed_at' => now()->toDateTimeString(),
                            ]],
                            'cc_recipients' => [],
                            'metadata' => [
                                'handover_notes' => $request->handover_notes,
                            ],
                        ]);
                    } catch (\Exception $e) {
                        \Log::error('Failed to create leave application letter: '.$e->getMessage());
                    }
                }

                LeaveRequestDraft::where('staff_id', $applicant->id)->delete();
            }

            $actingStaff = $leaveRequest->actingStaff;
            $requester = $leaveRequest->staff;

            if ($hasEligibleColleague) {
                Notification::create([
                    'user_id' => $actingStaff->id,
                    'type' => 'acting_request',
                    'title' => 'Hold Position Request',
                    'message' => "{$requester->first_name} {$requester->last_name} has requested you to hold their position during leave from ".
                        Carbon::parse($leaveRequest->start_date)->format('d M Y').' to '.
                        Carbon::parse($leaveRequest->end_date)->format('d M Y'),
                    'action_url' => route('leave-requests.acting.response', $leaveRequest->acting_token),
                    'action_text' => 'Review Request',
                    'is_read' => false,
                ]);
            } else {
                // Nobody was left in the eligible pool to hold the position
                // - the escalation contact is notified to assign someone
                // from elsewhere. The acting-response page shows them an
                // "assign someone else" tool instead of a plain accept/
                // decline, since is_escalation_holder is set on this
                // request.
                Notification::create([
                    'user_id' => $actingStaff->id,
                    'type' => 'acting_request',
                    'title' => 'No One Available to Hold Position',
                    'message' => "Nobody was available to hold {$requester->first_name} {$requester->last_name}'s position from ".
                        Carbon::parse($leaveRequest->start_date)->format('d M Y').' to '.
                        Carbon::parse($leaveRequest->end_date)->format('d M Y').
                        '. Please assign someone to cover it, from another department if needed.',
                    'action_url' => route('leave-requests.acting.response', $leaveRequest->acting_token),
                    'action_text' => 'Assign Coverage',
                    'is_read' => false,
                ]);
            }

            DB::commit();

            $successDetails = [
                'title' => 'Leave Request Submitted',
                'lines' => [
                    $hasEligibleColleague ? [
                        'icon' => 'user-clock',
                        'text' => "Waiting for {$actingStaff->first_name} {$actingStaff->last_name} to accept holding your position",
                    ] : [
                        'icon' => 'user-exclamation',
                        'text' => "No colleague was available to hold your position - {$actingStaff->first_name} {$actingStaff->last_name} has been notified to arrange coverage",
                    ],
                ],
            ];
            if ($request->filled('letter_content')) {
                $successDetails['lines'][] = [
                    'icon' => 'file-text',
                    'text' => 'Your letter draft has been saved',
                ];
            }

            return redirect()->route('leave-requests.my-requests')
                ->with('success', $successDetails['title'].'!')
                ->with('success_details', $successDetails);

        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Leave request submission failed: '.$e->getMessage());

            return redirect()->back()
                ->with('error', 'Failed to submit request: '.$e->getMessage())
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

        $authorizedRoles = ['admin', 'vice-chancellor', 'hod', 'dean', 'hr-officer', 'hr-manager', 'dvca', 'dvc-pfa', 'internal-auditor', 'registrar'];

        $query = LeaveRequest::with([
            'leaveType',
            'staff',
            'actingStaff',
            'approvalLogs.approver',
            'documents',
            'letters',
        ]);

        if (in_array($role, $authorizedRoles)) {
            // Institute-level oversight/approval seats only reach requests
            // from their own university (true super-admin excepted).
            // Admin sees all requests, not limited by university.
            if (! $user->hasRole('super-admin') && $role !== 'admin') {
                $query->whereHas('staff', function ($q) use ($user) {
                    $q->where('university_id', $user->university_id);
                });
            }
            $leaveRequest = $query->findOrFail($id);
        } else {
            $leaveRequest = $query->where(function ($q) use ($user) {
                $q->where('staff_id', $user->id)
                    ->orWhere('acting_staff_id', $user->id);
            })->findOrFail($id);
        }

        // Only worth recording when someone other than the applicant/acting
        // staff looks at it - an approver or auditor opening someone else's
        // request is the audit-worthy event, not the applicant checking their own.
        if ($user->id !== $leaveRequest->staff_id && $user->id !== $leaveRequest->acting_staff_id) {
            AuditLogHelper::log('view', 'leave', $leaveRequest->id);
        }

        $approvalLogs = $leaveRequest->approvalLogs;
        $totalApprovalTime = null;

        if ($approvalLogs && $approvalLogs->count() > 0) {
            $firstLog = $approvalLogs->first();
            $lastLog = $approvalLogs->last();
            if ($firstLog && $lastLog) {
                $totalMinutes = $firstLog->created_at->diffInMinutes($lastLog->created_at);
                if ($totalMinutes < 60) {
                    $totalApprovalTime = $totalMinutes.' minutes';
                } elseif ($totalMinutes < 1440) {
                    $totalApprovalTime = round($totalMinutes / 60, 1).' hours';
                } else {
                    $totalApprovalTime = round($totalMinutes / 1440, 1).' days';
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
        $authorizedRoles = ['admin', 'vice-chancellor', 'hod', 'dean', 'hr-officer', 'hr-manager', 'dvca', 'dvc-pfa', 'internal-auditor', 'registrar'];

        if (! in_array($role, $authorizedRoles) && $leaveRequest->staff_id !== $user->id && $leaveRequest->acting_staff_id !== $user->id) {
            abort(403, 'You do not have permission to view this letter.');
        }

        if ($leaveRequest->status !== 'approved') {
            return redirect()->route('leave-requests.show', $leaveRequest->id)
                ->with('error', 'Letter is only available after the leave request is approved.');
        }

        // A request can carry two letters now - HR's own approval letter and
        // the applicant's optional, separately-signed application letter
        // (see LeaveRequestController::store()). This legacy route only
        // ever showed the HR one, so pin it to that type explicitly instead
        // of "whichever was created last".
        $letter = $leaveRequest->letters()->where('type', 'approval')->latest()->first();

        if (! $letter) {
            return redirect()->route('leave-requests.show', $leaveRequest->id)
                ->with('error', 'Approval letter not found. Please contact HR.');
        }

        // The letterhead must reflect the applicant's own institute, not
        // whichever university happens to be first in a multi-tenant DB.
        $university = $leaveRequest->staff->university;

        return view('components.letter.leave-approval', [
            'leaveRequest' => $leaveRequest,
            'staff' => $leaveRequest->staff,
            'university' => $university,
            'letterNumber' => $letter->letter_number,
            'secureCode' => $letter->secure_code,
            'signatory' => $letter->generated_by ?? 'Human Resources Department',
            'signatoryTitle' => $letter->generated_by_role ?? 'HR Officer',
            'cc' => [
                'Head of Department - '.($leaveRequest->staff->department->name ?? 'N/A'),
                'Dean - '.($leaveRequest->staff->department->school->name ?? 'Faculty'),
                'HR Department',
                'DVC Academic',
            ],
        ]);
    }

    /**
     * Acting Staff Response Page
     */
    public function actingResponse($token)
    {
        $leaveRequest = LeaveRequest::with('staff')
            ->where('acting_token', $token)
            ->where('acting_status', 'pending')
            ->firstOrFail();

        // This person was set as an escalation contact, not picked by the
        // requester - offer them a way to assign someone else (any
        // department) instead of only a plain accept/decline for themselves.
        $reassignOptions = collect();
        if ($leaveRequest->is_escalation_holder) {
            $busyHolders = $this->busyHolderIds($leaveRequest->start_date, $leaveRequest->end_date);

            $reassignOptions = User::where('university_id', $leaveRequest->staff->university_id)
                ->where('status', 'active')
                ->where('id', '!=', $leaveRequest->staff_id)
                ->where('id', '!=', $leaveRequest->acting_staff_id)
                ->whereDoesntHave('leaveRequests', function ($q) use ($leaveRequest) {
                    $q->where('status', 'approved')
                        ->where('start_date', '<=', $leaveRequest->end_date)
                        ->where('end_date', '>=', $leaveRequest->start_date);
                })
                ->whereNotIn('id', $busyHolders)
                ->orderBy('first_name')
                ->get();
        }

        if (request()->ajax() || request()->expectsJson()) {
            return view('components.acting-response-modal', compact('leaveRequest', 'reassignOptions'))->render();
        }

        return view('shared.leave-requests.acting-response', compact('leaveRequest', 'reassignOptions'));
    }

    /**
     * Process Acting Staff Response
     */
    public function processActingResponse(Request $request, $id)
    {
        $leaveRequest = LeaveRequest::findOrFail($id);

        if (auth()->id() !== $leaveRequest->acting_staff_id) {
            abort(403, 'You are not authorized to respond to this request.');
        }

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
                $nextLevel = $this->initialApprovalLevel($leaveRequest->staff);

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

                Notification::create([
                    'user_id' => $leaveRequest->staff_id,
                    'type' => 'hold_accepted',
                    'title' => 'Hold Position Accepted',
                    'message' => $leaveRequest->actingStaff->first_name.' '.$leaveRequest->actingStaff->last_name.' has accepted to hold your position.',
                    'action_url' => route('leave-requests.show', $leaveRequest->id),
                    'action_text' => 'View Request',
                    'is_read' => false,
                ]);

                $this->notifyNextApprover($leaveRequest, $nextLevel);

                $hod = $leaveRequest->staff->department->hod ?? null;
                if ($hod) {
                    Notification::create([
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
                    'rejection_reason' => 'Acting staff declined: '.($request->comment ?? 'No reason provided'),
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

                Notification::create([
                    'user_id' => $leaveRequest->staff_id,
                    'type' => 'hold_declined',
                    'title' => 'Hold Position Declined',
                    'message' => $leaveRequest->actingStaff->first_name.' '.$leaveRequest->actingStaff->last_name.' has declined to hold your position. Your leave request has been cancelled.',
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

            return back()->with('error', 'Failed to process response: '.$e->getMessage());
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
        if ($currentLevel === null || ! is_numeric($currentLevel)) {
            return LeaveApprovalStage::firstLevel();
        }

        // The Dean stage has nobody to review it when the applicant IS
        // their school's Dean (nobody else holds that seat), or when the
        // school has no Dean assigned at all - skip straight past it to
        // whatever the configured chain has after Dean, rather than
        // advancing to a stage that will sit forever.
        if ((int) $currentLevel === LeaveRequest::LEVEL_HOD
            && ($this->isSchoolsDean($staff) || ! ($staff->department->school->dean_staff_id ?? null))) {
            return LeaveApprovalStage::nextLevelAfter(LeaveRequest::LEVEL_DEAN);
        }

        // Single source of truth for chain order - see LeaveApprovalStage.
        // Reordering or disabling a stage on the admin config page changes
        // this immediately, everywhere in the controller that asks "what's
        // next", instead of only in whichever hardcoded switch happened to
        // get updated.
        return LeaveApprovalStage::nextLevelAfter((int) $currentLevel);
    }

    /**
     * Pending Approvals (Role-based using numeric levels)
     */
    public function pendingApprovals(Request $request)
    {
        $perPage = $this->getPerPage($request, 10);
        $user = auth()->user();

        $query = $this->buildPendingApprovalsQuery($user);

        if (! $query) {
            return redirect()->route('dashboard');
        }

        $query->orderBy('created_at', 'asc');

        $stats = [
            'total' => (clone $query)->count(),
            'this_week' => (clone $query)->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'this_month' => (clone $query)->whereMonth('created_at', now()->month)->count(),
            'overdue' => (clone $query)->where('created_at', '<', now()->subHours(48))->count(),
        ];

        if ($search = $request->get('search')) {
            $query->whereHas('staff', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('staff_id', 'like', "%{$search}%");
            });
        }

        $pendingRequests = $query->paginate($perPage)->appends($request->query());

        if ($request->wantsJson()) {
            return response()->json([
                'html' => view('shared.leave-requests._pending-approvals-results', compact('pendingRequests'))->render(),
                'total' => $pendingRequests->total(),
                'grand_total' => $stats['total'],
            ]);
        }

        return view('shared.leave-requests.pending-approvals', compact('pendingRequests', 'stats'));
    }

    /**
     * Lightweight signature for the live-refresh poll - changes whenever the
     * viewer's pending-approvals list would look different (a new request
     * arrived, one moved stage, or one dropped off).
     */
    public function pendingApprovalsHeartbeat()
    {
        $query = $this->buildPendingApprovalsQuery(auth()->user());

        if (! $query) {
            return response()->json(['signature' => null]);
        }

        return response()->json([
            'signature' => $query->count().':'.$query->max('updated_at'),
        ]);
    }

    /**
     * Builds the role-scoped pending-approvals query. Returns null when the
     * role has no pending-approvals view at all.
     */
    private function buildPendingApprovalsQuery(User $user): ?Builder
    {
        $role = $user->getRoleNames()->first();

        $query = LeaveRequest::with(['staff', 'leaveType', 'actingStaff'])
            ->where('status', 'pending')
            ->where('acting_status', 'accepted');

        switch ($role) {
            case 'vice-chancellor':
            case 'internal-auditor':
                $query->whereHas('staff', function ($q) use ($user) {
                    $q->where('university_id', $user->university_id);
                });
                break;
            case 'admin':
                // Admin sees all pending approvals across the system.
                break;
            case 'unit_head':
            case 'hod':
                $query->where('current_approval_level', LeaveRequest::LEVEL_HOD)
                    ->whereHas('staff.department', function ($q) use ($user) {
                        $q->where('hod_staff_id', $user->id);
                    });
                break;
            case 'deputy-hod':
                $query->where('current_approval_level', LeaveRequest::LEVEL_HOD)
                    ->whereHas('staff.department', function ($q) use ($user) {
                        $q->where('hod_staff_id', $user->department->hod_staff_id ?? 0);
                    });
                break;
            case 'dept-secretary':
                $query->where('current_approval_level', LeaveRequest::LEVEL_HOD)
                    ->whereHas('staff.department', function ($q) use ($user) {
                        $q->where('id', $user->department_id);
                    });
                break;
            case 'dean':
                $query->where('current_approval_level', LeaveRequest::LEVEL_DEAN)
                    ->whereHas('staff.department.school', function ($q) use ($user) {
                        $q->where('dean_staff_id', $user->id);
                    });
                break;
            case 'dvca':
                // staff_category alone has no institute boundary - a DVC at
                // one university must never see leave for another.
                $query->where('current_approval_level', LeaveRequest::LEVEL_DVC)
                    ->whereHas('staff', function ($q) use ($user) {
                        $q->where('staff_category', 'academic')
                            ->where('university_id', $user->university_id);
                    });
                break;
            case 'dvc-pfa':
                $query->where('current_approval_level', LeaveRequest::LEVEL_DVC)
                    ->whereHas('staff', function ($q) use ($user) {
                        $q->whereIn('staff_category', ['administrative', 'supporting'])
                            ->where('university_id', $user->university_id);
                    });
                break;
            case 'hr-officer':
            case 'hr-manager':
                // Multi-seat role: excluding self is enough, no stand-in
                // needed - another HR officer/manager at the university can
                // always review someone's own HR-stage request.
                $query->whereIn('current_approval_level', [LeaveRequest::LEVEL_HR, LeaveRequest::LEVEL_HR_LOGISTICS])
                    ->where('staff_id', '!=', $user->id)
                    ->whereHas('staff', function ($q) use ($user) {
                        $q->where('university_id', $user->university_id);
                    });
                break;
            default:
                return null;
        }

        return $query;
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

        if (! $this->canUserApprove($user, $leaveRequest)) {
            return redirect()->back()->with('error', 'You are not authorized to approve this request.');
        }

        $nextLevel = $this->getNextLevelAfterApproval($currentLevel, $leaveRequest->staff);

        if (! $nextLevel) {
            // Already at the final stage (HR Logistics) - that stage isn't
            // a plain "approve", it requires the dedicated page (signature
            // capture, letter generation). Redirect there instead of
            // recording a fake self-referential approval log entry for
            // an action that didn't actually happen.
            return redirect()->route('leave-requests.hr-logistics', $leaveRequest->id)
                ->with('info', 'This request is at HR Logistics - the final stage. Complete it from this page.');
        }

        ApprovalLog::create([
            'request_type' => 'leave',
            'request_id' => $leaveRequest->id,
            'from_role' => $this->getLevelName($currentLevel),
            'to_role' => $this->getLevelName($nextLevel),
            'action' => 'approved',
            'comment' => $request->comment,
            'approver_staff_id' => $user->id,
            'ip_address' => $request->ip(),
        ]);

        $leaveRequest->update([
            'current_approval_level' => $nextLevel,
        ]);

        AuditLogHelper::log('approve', 'leave', $leaveRequest->id,
            ['current_approval_level' => $currentLevel],
            ['current_approval_level' => $nextLevel]
        );

        Notification::create([
            'user_id' => $leaveRequest->staff_id,
            'type' => 'approval_granted',
            'title' => 'Leave Request Approved at '.$this->getLevelName($currentLevel),
            'message' => 'Your leave request has been approved by '.$user->first_name.' '.$user->last_name,
            'action_url' => route('leave-requests.show', $leaveRequest->id),
            'action_text' => 'View Request',
            'is_read' => false,
        ]);

        $this->notifyNextApprover($leaveRequest, $nextLevel);

        return redirect()->back()->with('success', 'Leave request approved and forwarded to '.$this->getLevelName($nextLevel).'.');
    }

    /**
     * HR Logistics - Final processing page
     */
    public function hrLogistics($id)
    {
        $leaveRequest = LeaveRequest::with(['staff', 'leaveType', 'actingStaff', 'approvalLogs'])
            ->where('current_approval_level', LeaveRequest::LEVEL_HR_LOGISTICS)
            ->findOrFail($id);
        $this->assertSameUniversity($leaveRequest);

        // HR Logistics is the one stage that can't be skipped for anyone,
        // VC included - which means it's also the one stage an HR
        // officer/manager could otherwise finalize on their own request.
        if ($leaveRequest->staff_id === auth()->id()) {
            abort(403, 'You cannot process your own leave request at HR Logistics - another HR officer or manager must complete this stage.');
        }

        $university = $leaveRequest->staff->university;

        return view('shared.leave-requests.hr-logistics', compact('leaveRequest', 'university'));
    }

    /**
     * Process HR Logistics - Final approval
     */
    public function processHrLogistics(Request $request, $id)
    {
        $leaveRequest = LeaveRequest::with(['staff', 'leaveType', 'actingStaff'])->findOrFail($id);
        $this->assertSameUniversity($leaveRequest);

        if ($leaveRequest->staff_id === auth()->id()) {
            abort(403, 'You cannot process your own leave request at HR Logistics - another HR officer or manager must complete this stage.');
        }

        $request->validate([
            'hr_comment' => 'nullable|string|max:500',
            'generate_letter' => 'nullable|boolean',
            // The officer's live signature, captured on this page, required
            // whenever the approval letter is being generated/finalized.
            'signature_data' => 'required_if:generate_letter,1|string',
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

            Notification::create([
                'user_id' => $leaveRequest->staff_id,
                'type' => 'approval_complete',
                'title' => 'Leave Request Fully Approved',
                'message' => 'Congratulations! Your leave request has been fully approved. HR has completed final processing.',
                'action_url' => route('leave-requests.show', $leaveRequest->id),
                'action_text' => 'View Details',
                'is_read' => false,
            ]);

            $this->updateLeaveBalance($leaveRequest);

            if ($request->boolean('generate_letter')) {
                $this->finalizeLeaveLetter($leaveRequest, $request);
            }

            DB::commit();

            return redirect()->route('leave-requests.pending')
                ->with('success', 'Leave request fully approved and processed. Staff has been notified.');

        } catch (\Throwable $e) {
            DB::rollBack();

            \Log::error('HR Logistics processing failed: '.$e->getMessage(), [
                'leave_request_id' => $id,
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return redirect()->back()->with('error', 'Failed to process: '.$e->getMessage());
        }
    }

    /**
     * Finalize the leave approval letter at the point of actual approval (HR
     * Logistics final processing) - not at submission time. Content is
     * authored here, by the approving officer, not carried over from
     * whatever the applicant typed when they applied. A letter may already
     * exist as a 'draft' from submission (see store()); if so it's
     * repurposed rather than duplicated, keeping its original letter number.
     */
    private function finalizeLeaveLetter(LeaveRequest $leaveRequest, Request $request): void
    {
        $officer = auth()->user();
        $officerRole = ucwords(str_replace(['-', '_'], ' ', $officer->getRoleNames()->first() ?? 'HR Officer'));

        $signatureData = $request->signature_data;
        if (str_contains($signatureData, ',')) {
            $signatureData = explode(',', $signatureData)[1];
        }
        $signaturePath = 'signatures/leave_letter_'.uniqid().'.png';
        Storage::disk('public')->put($signaturePath, base64_decode($signatureData));

        // Pinned to type='approval' - a request can also carry a separate
        // type='application' letter (the applicant's own, self-signed at
        // submission; see store()). Without this filter, a request
        // submitted with only handover_notes (no letter_content, so no
        // 'approval' draft letter exists yet) would fetch that application
        // letter here and overwrite it below - destroying the applicant's
        // own signed letter and replacing its signature with the officer's.
        $letter = LeaveLetter::where('leave_request_id', $leaveRequest->id)
            ->where('type', 'approval')
            ->first();
        $letterNumber = $letter->letter_number ?? $this->generateLetterNumber();

        // No Ref/Date lines here - the print/show views already render those
        // from letter_number/created_at in their own header block. Numbered
        // clauses match the institution's own correspondence convention.
        $content = "To: {$leaveRequest->staff->full_name}\n".
            "{$leaveRequest->staff->staff_id}\n".
            ($leaveRequest->staff->department->name ?? 'N/A')."\n\n".
            "Dear {$leaveRequest->staff->first_name},\n\n".
            "RE: LEAVE APPROVAL - {$leaveRequest->leaveType->name}\n\n".
            "1.\tYour leave request has been APPROVED as follows:\n\n".
            "\tLeave Type: {$leaveRequest->leaveType->name}\n".
            "\tStart Date: ".Carbon::parse($leaveRequest->start_date)->format('jS F Y')."\n".
            "\tEnd Date: ".Carbon::parse($leaveRequest->end_date)->format('jS F Y')."\n".
            "\tTotal Days: {$leaveRequest->days} day(s)\n\n".
            '2.'."\tDuring your leave, ".($leaveRequest->actingStaff->full_name ?? 'a colleague').' will hold your position.'.
            "\n\n3.\tPlease ensure proper handover before proceeding on leave.";

        // Handover notes, laid out as an aligned block (a real HTML table
        // isn't practical here - the letter body is plain text shared
        // verbatim between the browser view, print view, and PDF).
        if (filled($leaveRequest->handover_notes)) {
            $content .= "\n\n4.\tHandover Notes:\n\t".str_replace("\n", "\n\t", trim($leaveRequest->handover_notes));
        }

        // Honor the applicant's own "Letter Content" note from submission
        // (see store()) by quoting it here, under its own clause - it's
        // included, but clearly attributed to the applicant, not folded into
        // HR's own approval clauses above.
        if (filled($leaveRequest->letter_content)) {
            $noteClause = filled($leaveRequest->handover_notes) ? '5' : '4';
            $content .= "\n\n{$noteClause}.\tApplicant's note:\n\t\"".trim($leaveRequest->letter_content).'"';
        }

        // CC: the colleague holding the position, plus the Head of
        // Department and Dean where known - so everyone who needs to act on
        // this approval gets it, not just the applicant.
        $ccRecipients = [];
        if ($leaveRequest->actingStaff) {
            $ccRecipients[] = ['name' => $leaveRequest->actingStaff->full_name, 'role' => 'Acting/Holding Staff'];
        }
        $department = $leaveRequest->staff->department;
        if ($department?->hod) {
            $ccRecipients[] = ['name' => $department->hod->full_name, 'role' => 'Head of Department'];
        }
        if ($department?->school?->dean) {
            $ccRecipients[] = ['name' => $department->school->dean->full_name, 'role' => 'Dean, '.$department->school->name];
        }

        if (! empty($ccRecipients)) {
            $content .= "\n\ncc:\n";
            foreach ($ccRecipients as $cc) {
                $content .= "\t{$cc['name']} - {$cc['role']}\n";
            }
            $content = rtrim($content);
        }

        $letterData = [
            'leave_request_id' => $leaveRequest->id,
            'staff_id' => $leaveRequest->staff_id,
            'letter_number' => $letterNumber,
            'secure_code' => $letter->secure_code ?? $this->generateSecureCode($leaveRequest->id),
            'title' => 'Leave Approval Letter',
            'content' => $content,
            'type' => 'approval',
            'stage' => 'generated',
            'generated_by' => $officer->full_name,
            'generated_by_role' => $officerRole,
            'signature_path' => $signaturePath,
            'signatories' => [[
                'name' => $officer->full_name,
                'role' => $officerRole,
                'signed_at' => now()->toDateTimeString(),
            ]],
            'cc_recipients' => $ccRecipients,
            'metadata' => [
                'leave_type' => $leaveRequest->leaveType->name ?? null,
                'days' => $leaveRequest->days,
                'start_date' => (string) $leaveRequest->start_date,
                'end_date' => (string) $leaveRequest->end_date,
            ],
        ];

        if ($letter) {
            $letter->update($letterData);
        } else {
            LeaveLetter::create($letterData);
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
    private function canUserApprove($user, $leaveRequest, $checkDelegation = true)
    {
        $role = $user->getRoleNames()->first();
        $currentLevel = $leaveRequest->current_approval_level;

        if ($checkDelegation && $this->isDelegatedApprover($user, $leaveRequest)) {
            return true;
        }

        if ($role == 'admin') {
            return $user->hasRole('super-admin') || $leaveRequest->staff->university_id == $user->university_id;
        }

        if ($role == 'vice-chancellor') {
            return $leaveRequest->staff->university_id == $user->university_id;
        }

        if ($role == 'internal-auditor') {
            return false;
        }

        if ($role == 'hod') {
            $isHodLevel = ($currentLevel == LeaveRequest::LEVEL_HOD) || ($currentLevel == 'hod' || $currentLevel == '1');
            if (! $isHodLevel) {
                return false;
            }
            $hodId = $leaveRequest->staff->department->hod_staff_id ?? null;

            return $hodId == $user->id;
        }

        if ($role == 'deputy-hod') {
            $isHodLevel = ($currentLevel == LeaveRequest::LEVEL_HOD) || ($currentLevel == 'hod' || $currentLevel == '1');
            if (! $isHodLevel) {
                return false;
            }
            if ($leaveRequest->staff->department_id != $user->department_id) {
                return false;
            }
            $hod = $leaveRequest->staff->department->hod;
            if (! $hod) {
                return false;
            }
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
            if (! $isDeanLevel) {
                return false;
            }
            $deanId = $leaveRequest->staff->department->school->dean_staff_id ?? null;

            return $deanId == $user->id;
        }

        if ($role == 'dvca') {
            $isDvcLevel = ($currentLevel == LeaveRequest::LEVEL_DVC) || ($currentLevel == 'dvc' || $currentLevel == '4');
            if (! $isDvcLevel) {
                return false;
            }

            // staff_category alone has no institute boundary - a DVC at one
            // university must never approve leave for another. A DVC also
            // can't approve their own leave - getNextApproverIds() excludes
            // them from the notified list too, but any other dvca at the
            // same university can still act.
            return $leaveRequest->staff->staff_category == 'academic'
                && $leaveRequest->staff->university_id == $user->university_id
                && $leaveRequest->staff_id != $user->id;
        }

        if ($role == 'dvc-pfa') {
            $isDvcLevel = ($currentLevel == LeaveRequest::LEVEL_DVC) || ($currentLevel == 'dvc' || $currentLevel == '4');
            if (! $isDvcLevel) {
                return false;
            }

            return in_array($leaveRequest->staff->staff_category, ['administrative', 'supporting'])
                && $leaveRequest->staff->university_id == $user->university_id
                && $leaveRequest->staff_id != $user->id;
        }

        if ($user->hasAnyRole(['hr-officer', 'hr-manager'])) {
            $isHrLevel = ($currentLevel == LeaveRequest::LEVEL_HR) || ($currentLevel == 'hr' || $currentLevel == '3');
            $isHrLogisticsLevel = ($currentLevel == LeaveRequest::LEVEL_HR_LOGISTICS) || ($currentLevel == 'hr_logistics' || $currentLevel == '5');

            // Multi-seat role, same as DVC: an HR officer/manager can't
            // approve their own request, but any other one at the
            // university still can.
            return ($isHrLevel || $isHrLogisticsLevel)
                && $leaveRequest->staff->university_id == $user->university_id
                && $leaveRequest->staff_id != $user->id;
        }

        if ($user->can('approve_department_leave')) {
            $isHodLevel = ($currentLevel == LeaveRequest::LEVEL_HOD) || ($currentLevel == 'hod' || $currentLevel == '1');
            if (! $isHodLevel) {
                return false;
            }
            if ($user->hasRole('super-admin')) {
                return true;
            }

            return $leaveRequest->staff->department_id
                && $leaveRequest->staff->department_id == $user->department_id
                && $leaveRequest->staff_id != $user->id;
        }

        return false;
    }

    /**
     * Determine whether an active approval delegation lets this user approve
     * on behalf of the real approver.
     */
    private function isDelegatedApprover($user, $leaveRequest): bool
    {
        // A delegation grants someone else's approval authority - it can
        // never grant authority to approve your own request. Without this,
        // a delegate who later applies for their own leave during their
        // delegation window could approve it themselves, bypassing every
        // other self-collision guard in canUserApprove().
        if ($user->id === $leaveRequest->staff_id) {
            return false;
        }

        $currentLevel = $leaveRequest->current_approval_level;

        $levelMap = [
            LeaveRequest::LEVEL_HOD => 'hod',
            LeaveRequest::LEVEL_DEAN => 'dean',
            LeaveRequest::LEVEL_HR => 'hr',
            LeaveRequest::LEVEL_DVC => 'dvc',
            LeaveRequest::LEVEL_HR_LOGISTICS => 'hr_logistics',
        ];

        $levelName = $levelMap[$currentLevel] ?? null;

        if (! $levelName) {
            return false;
        }

        $delegations = ApprovalDelegation::active()
            ->where('delegate_staff_id', $user->id)
            ->where('level', $levelName)
            ->get();

        foreach ($delegations as $delegation) {
            $delegator = $delegation->delegator;

            if ($delegator && $this->canUserApprove($delegator, $leaveRequest, false)) {
                return true;
            }
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

        if (! $this->canUserApprove(auth()->user(), $leaveRequest)) {
            return redirect()->back()->with('error', 'You are not authorized to reject this request.');
        }

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

        Notification::create([
            'user_id' => $leaveRequest->staff_id,
            'type' => 'approval_rejected',
            'title' => 'Leave Request Rejected',
            'message' => 'Your leave request has been rejected. Reason: '.$request->rejection_reason,
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
            // leave_balances has no separate "used" column - the remaining
            // balance itself already reflects days consumed via decrement.
            $leaveBalance->decrement('balance', $leaveRequest->days);
        }
    }

    /**
     * Save a hand-typed emergency contact to the applicant's own saved list,
     * so it's there to pick from next time instead of being retyped. Matches
     * on name + phone (case/space-insensitive) first, so submitting the same
     * person again and again doesn't pile up duplicates.
     */
    private function saveEmergencyContactIfNew(User $applicant, string $name, ?string $phone): void
    {
        $name = trim($name);

        if ($name === '') {
            return;
        }

        $normalizedPhone = $phone ? preg_replace('/\s+/', '', $phone) : null;

        $exists = StaffEmergencyContact::where('staff_id', $applicant->id)
            ->whereRaw('LOWER(TRIM(full_name)) = ?', [Str::lower($name)])
            ->when($normalizedPhone, fn ($query) => $query->whereRaw("REPLACE(phone_primary, ' ', '') = ?", [$normalizedPhone]))
            ->exists();

        if ($exists) {
            return;
        }

        StaffEmergencyContact::create([
            'staff_id' => $applicant->id,
            'full_name' => $name,
            // The leave form only collects a name and phone number - this
            // column has no database default, so it needs something.
            'relationship' => 'Not specified',
            'phone_primary' => $phone ?: '',
            'priority' => 5,
        ]);
    }

    /**
     * Notify the next approver(s)
     */
    private function notifyNextApprover($leaveRequest, $nextLevel)
    {
        $nextApproverIds = $this->getNextApproverIds($leaveRequest, $nextLevel);

        if (empty($nextApproverIds)) {
            return;
        }

        $levelName = $this->getLevelName($nextLevel);
        $priority = ($nextLevel == LeaveRequest::LEVEL_HOD || $nextLevel == LeaveRequest::LEVEL_DEAN) ? 'high' : 'medium';
        $icon = ($nextLevel == LeaveRequest::LEVEL_HOD || $nextLevel == LeaveRequest::LEVEL_DEAN) ? 'alert-triangle' : 'clock';
        $iconColor = ($nextLevel == LeaveRequest::LEVEL_HOD || $nextLevel == LeaveRequest::LEVEL_DEAN) ? 'text-warning' : 'text-info';

        foreach ($nextApproverIds as $approverId) {
            Notification::create([
                'user_id' => $approverId,
                'type' => 'approval_pending',
                'title' => 'Leave Request Pending Your Approval',
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
     * Get next approver ID(s) based on numeric level. HOD/Dean are single
     * seats (one FK column per department/school), but HR and DVC roles can
     * be held by more than one person - notify all of them, not just
     * whichever one a ->first() query happens to return.
     */
    private function getNextApproverIds($leaveRequest, $level): array
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
                            return [$deputyHod->id];
                        }
                    }
                }

                $hodId = $staff->department->hod_staff_id ?? null;

                return $hodId ? [$hodId] : [];

            case LeaveRequest::LEVEL_DEAN:
                $deanId = $staff->department->school->dean_staff_id ?? null;

                return $deanId ? [$deanId] : [];

            case LeaveRequest::LEVEL_HR:
            case LeaveRequest::LEVEL_HR_LOGISTICS:
                // Notify only this staff member's own institute's HR
                // officers - never another university's, and never the
                // requester themselves if they happen to hold an HR role.
                return User::role(['hr-officer', 'hr-manager'])
                    ->where('university_id', $staff->university_id)
                    ->where('id', '!=', $staff->id)
                    ->pluck('id')->all();

            case LeaveRequest::LEVEL_DVC:
                if ($staff->staff_category == 'academic') {
                    $actingDvca = ActingDvcAssignment::where('role', 'dvca')
                        ->where('status', 'active')
                        ->where('start_date', '<=', now())
                        ->where('end_date', '>=', now())
                        ->whereHas('staff', function ($q) use ($staff) {
                            $q->where('university_id', $staff->university_id);
                        })
                        ->first();

                    // A DVC can't approve their own leave - fall through to
                    // any other dvca at the same university instead.
                    if ($actingDvca && $actingDvca->staff_id != $staff->id) {
                        return [$actingDvca->staff_id];
                    }

                    return User::role('dvca')
                        ->where('university_id', $staff->university_id)
                        ->where('id', '!=', $staff->id)
                        ->pluck('id')->all();
                } else {
                    $actingDvcPfa = ActingDvcAssignment::where('role', 'dvc-pfa')
                        ->where('status', 'active')
                        ->where('start_date', '<=', now())
                        ->where('end_date', '>=', now())
                        ->whereHas('staff', function ($q) use ($staff) {
                            $q->where('university_id', $staff->university_id);
                        })
                        ->first();

                    if ($actingDvcPfa && $actingDvcPfa->staff_id != $staff->id) {
                        return [$actingDvcPfa->staff_id];
                    }

                    return User::role('dvc-pfa')
                        ->where('university_id', $staff->university_id)
                        ->where('id', '!=', $staff->id)
                        ->pluck('id')->all();
                }

            default:
                return [];
        }
    }

    /**
     * Display staff's leave requests with status timeline
     */
    /**
     * Lightweight signature for the live-refresh poll on "my requests" -
     * changes whenever any of the viewer's own leave requests move stage.
     */
    public function myRequestStatusHeartbeat()
    {
        $query = LeaveRequest::where('staff_id', auth()->id());

        return response()->json([
            'signature' => $query->count().':'.$query->max('updated_at'),
        ]);
    }

    /**
     * Lightweight signature for the live-refresh poll on a single request's
     * detail page - changes when the record itself, or its approval trail,
     * moves (an approval log is appended without touching the parent row).
     */
    public function showHeartbeat($id)
    {
        $leaveRequest = LeaveRequest::with('staff')->findOrFail($id);
        $user = auth()->user();

        $isOwnRequest = $leaveRequest->staff_id === $user->id || $leaveRequest->acting_staff_id === $user->id;
        if (! $isOwnRequest) {
            $authorizedRoles = ['admin', 'vice-chancellor', 'hod', 'dean', 'hr-officer', 'hr-manager', 'dvca', 'dvc-pfa', 'internal-auditor', 'registrar'];
            $inAuthorizedRole = in_array($user->getRoleNames()->first(), $authorizedRoles);
            $sameUniversity = $user->hasRole('super-admin') || $leaveRequest->staff->university_id === $user->university_id;
            abort_unless($inAuthorizedRole && $sameUniversity, 403);
        }

        $lastLogAt = ApprovalLog::where('request_type', 'leave')
            ->where('request_id', $id)
            ->max('created_at');

        return response()->json([
            'signature' => $leaveRequest->updated_at.':'.$lastLogAt,
        ]);
    }

    public function myRequestStatus(Request $request)
    {
        $perPage = $this->getPerPage($request, 10);

        $query = LeaveRequest::with([
            'leaveType',
            'actingStaff',
            'approvalLogs.approver',
            'documents',
        ])
            ->where('staff_id', auth()->id())
            ->orderBy('created_at', 'desc');

        $grandTotal = (clone $query)->count();

        // 'filter' is the legacy query param name (older bookmarks/links may still use it).
        $status = $request->get('status', $request->get('filter', 'all'));

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($search = $request->get('search')) {
            $query->whereHas('leaveType', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        $leaveRequests = $query->paginate($perPage)->appends($request->query());

        $stats = [
            'total' => LeaveRequest::where('staff_id', auth()->id())->count(),
            'pending' => LeaveRequest::where('staff_id', auth()->id())->where('status', 'pending')->count(),
            'approved' => LeaveRequest::where('staff_id', auth()->id())->where('status', 'approved')->count(),
            'rejected' => LeaveRequest::where('staff_id', auth()->id())->where('status', 'rejected')->count(),
            'cancelled' => LeaveRequest::where('staff_id', auth()->id())->where('status', 'cancelled')->count(),
        ];

        if ($request->wantsJson()) {
            return response()->json([
                'html' => view('shared.leave-requests._status-results', compact('leaveRequests'))->render(),
                'total' => $leaveRequests->total(),
                'grand_total' => $grandTotal,
            ]);
        }

        return view('shared.leave-requests.status', compact('leaveRequests', 'stats'));
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
        $query = HoldPositionHistory::with(['holder.department', 'leaveRequest.leaveType', 'leaveRequest.documents'])
            ->where('requester_staff_id', auth()->id())
            ->whereNotNull('leave_request_id')
            ->orderBy('requested_at', 'desc');

        $grandTotal = (clone $query)->count();

        if ($search = $request->get('search')) {
            $query->whereHas('holder', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $holdHistories = $query->paginate($perPage)->appends($request->query());

        if ($request->wantsJson()) {
            return response()->json([
                'html' => view('shared.leave-requests._hold-position-for-me-results', compact('holdHistories'))->render(),
                'total' => $holdHistories->total(),
                'grand_total' => $grandTotal,
            ]);
        }

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
        $actingQuery = LeaveRequest::where('acting_staff_id', auth()->id())
            ->where('acting_status', 'pending')
            ->with(['staff', 'leaveType', 'staff.department', 'documents'])
            ->orderBy('created_at', 'desc');

        $grandTotal = (clone $actingQuery)->count();

        if ($search = $request->get('search')) {
            $actingQuery->whereHas('staff', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%");
            });
        }

        $actingRequests = $actingQuery->paginate($perPage)->appends($request->query());

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

        if ($request->wantsJson()) {
            return response()->json([
                'html' => view('shared.leave-requests._i-held-position-for-results', compact('actingRequests'))->render(),
                'total' => $actingRequests->total(),
                'grand_total' => $grandTotal,
            ]);
        }

        return view('shared.leave-requests.i-held-position-for', compact(
            'actingRequests',
            'holdHistories',
            'unresponsiveCount'
        ));
    }

    /**
     * Show allowed public details of the acting holder for a specific hold request.
     */
    public function showHolder($id)
    {
        $history = HoldPositionHistory::with(['holder.department', 'leaveRequest.leaveType'])
            ->where('requester_staff_id', auth()->id())
            ->findOrFail($id);

        return view('shared.leave-requests.holder', compact('history'));
    }

    /**
     * System-wide queue of leave requests whose designated acting staff has not
     * responded within 48 hours. Visible to HOD/HR-officer/admin — matches the
     * count shown by the "Unresponsive Acting Staff" dashboard widget.
     */
    public function unresponsiveActing(Request $request)
    {
        $perPage = $this->getPerPage($request, 10);

        // acting_requested_at is set on reassignment so the 48h clock restarts for
        // the new person; older rows never had it populated, so fall back to created_at.
        // scopeActingOverdue() on the model is the single source of truth for this —
        // the dashboard widget and sidebar badge use it too, so they can't drift apart.
        $waitingSince = 'COALESCE(acting_requested_at, created_at)';
        $view = $request->get('view') === 'dismissed' ? 'dismissed' : 'active';

        $query = LeaveRequest::actingOverdue()
            ->with(['staff', 'actingStaff', 'leaveType', 'dismissedBy'])
            ->orderByRaw($waitingSince.' asc');

        if ($view === 'dismissed') {
            $query->whereNotNull('acting_escalation_dismissed_at');
        } else {
            $query->whereNull('acting_escalation_dismissed_at');
        }

        $grandTotal = (clone $query)->count();

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('staff', function ($sq) use ($search) {
                    $sq->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%");
                })->orWhereHas('actingStaff', function ($sq) use ($search) {
                    $sq->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%");
                });
            });
        }

        $unresponsiveRequests = $query->paginate($perPage)->appends($request->query());

        if ($request->wantsJson()) {
            return response()->json([
                'html' => view('shared.leave-requests._unresponsive-acting-results', compact('unresponsiveRequests', 'view'))->render(),
                'total' => $unresponsiveRequests->total(),
                'grand_total' => $grandTotal,
            ]);
        }

        $activeTotal = LeaveRequest::actingOverdueVisible()->count();
        $dismissedTotal = LeaveRequest::actingOverdue()->whereNotNull('acting_escalation_dismissed_at')->count();

        $stats = [
            'total' => $activeTotal,
            'over_72h' => LeaveRequest::actingOverdueVisible(72)->count(),
            'over_week' => LeaveRequest::actingOverdueVisible(24 * 7)->count(),
            'departments' => LeaveRequest::actingOverdueVisible()
                ->with('staff:id,department_id')
                ->get()
                ->pluck('staff.department_id')
                ->filter()
                ->unique()
                ->count(),
        ];

        return view('shared.leave-requests.unresponsive-acting', compact('unresponsiveRequests', 'stats', 'view', 'activeTotal', 'dismissedTotal'));
    }

    /**
     * Acknowledge a stuck request from the escalation list without changing its
     * acting-staff state — soft-delete style, fully reversible via restoreUnresponsive().
     */
    public function dismissUnresponsive(Request $request, $id)
    {
        $leaveRequest = LeaveRequest::findOrFail($id);

        $request->validate([
            'dismissal_reason' => 'required|string|min:5|max:500',
        ]);

        $leaveRequest->update([
            'acting_escalation_dismissed_at' => now(),
            'acting_escalation_dismissed_by' => auth()->id(),
            'acting_escalation_dismissal_reason' => $request->dismissal_reason,
        ]);

        AuditLogHelper::log('dismiss', 'acting_escalation', $leaveRequest->id,
            ['acting_escalation_dismissed_at' => null],
            ['acting_escalation_dismissed_at' => now()->toDateTimeString(), 'reason' => $request->dismissal_reason]
        );

        return back()->with('success', 'Dismissed from the escalation list. You can restore it anytime from the Dismissed tab.');
    }

    /**
     * Undo a dismissal — brings the request back into the active escalation list.
     */
    public function restoreUnresponsive($id)
    {
        $leaveRequest = LeaveRequest::findOrFail($id);

        $leaveRequest->update([
            'acting_escalation_dismissed_at' => null,
            'acting_escalation_dismissed_by' => null,
            'acting_escalation_dismissal_reason' => null,
        ]);

        AuditLogHelper::log('restore', 'acting_escalation', $leaveRequest->id,
            ['acting_escalation_dismissed_at' => 'set'],
            ['acting_escalation_dismissed_at' => null]
        );

        return back()->with('success', 'Restored to the active escalation list.');
    }

    /**
     * HOD/HR/admin escalation action: close out the stale assignment (recorded as
     * declined — they never responded) and hand the hold-position request to a new
     * colleague with a fresh 48h clock.
     */
    public function reassignActingStaff(Request $request, $id)
    {
        $leaveRequest = LeaveRequest::with('staff')->findOrFail($id);

        if ($leaveRequest->acting_status !== 'pending') {
            return back()->with('error', 'This request is no longer awaiting an acting-staff response.');
        }

        $request->validate([
            'new_acting_staff_id' => ['required', 'exists:staff,id'],
        ]);

        if ((int) $request->new_acting_staff_id === (int) $leaveRequest->acting_staff_id) {
            return back()->with('error', 'Please choose someone other than the current acting staff.');
        }

        if ((int) $request->new_acting_staff_id === (int) $leaveRequest->staff_id) {
            return back()->with('error', 'The requester cannot hold their own position.');
        }

        DB::beginTransaction();

        try {
            $oldActingStaff = $leaveRequest->actingStaff;
            $newActingStaff = User::findOrFail($request->new_acting_staff_id);
            $newToken = Str::random(60);

            HoldPositionHistory::where('leave_request_id', $leaveRequest->id)
                ->where('holder_staff_id', $leaveRequest->acting_staff_id)
                ->where('status', 'pending')
                ->update([
                    'status' => 'declined',
                    'responded_at' => now(),
                    'comment' => 'Reassigned by '.auth()->user()->full_name.' — no response after 48+ hours.',
                ]);

            $leaveRequest->update([
                'acting_staff_id' => $newActingStaff->id,
                'acting_status' => 'pending',
                'acting_requested_at' => now(),
                'acting_responded_at' => null,
                'acting_token' => $newToken,
                'acting_escalation_dismissed_at' => null,
                'acting_escalation_dismissed_by' => null,
                'acting_escalation_dismissal_reason' => null,
            ]);

            HoldPositionHistory::create([
                'leave_request_id' => $leaveRequest->id,
                'requester_staff_id' => $leaveRequest->staff_id,
                'holder_staff_id' => $newActingStaff->id,
                'status' => 'pending',
                'requested_at' => now(),
                'token' => $newToken,
            ]);

            AuditLogHelper::log('reassign', 'acting', $leaveRequest->id,
                ['acting_staff_id' => $oldActingStaff?->id],
                ['acting_staff_id' => $newActingStaff->id]
            );

            Notification::create([
                'user_id' => $newActingStaff->id,
                'type' => 'acting_request',
                'title' => 'Hold Position Request',
                'message' => "{$leaveRequest->staff->first_name} {$leaveRequest->staff->last_name} has requested you to hold their position during leave from ".
                    Carbon::parse($leaveRequest->start_date)->format('d M Y').' to '.
                    Carbon::parse($leaveRequest->end_date)->format('d M Y').' (reassigned by '.auth()->user()->full_name.').',
                'action_url' => route('leave-requests.acting.response', $newToken),
                'action_text' => 'Review Request',
                'is_read' => false,
            ]);

            if ($oldActingStaff) {
                Notification::create([
                    'user_id' => $oldActingStaff->id,
                    'type' => 'acting_reassigned',
                    'title' => 'Hold Position Request Reassigned',
                    'message' => 'You did not respond to a hold-position request in time, so it has been reassigned to someone else.',
                    'action_url' => route('leave-requests.show', $leaveRequest->id),
                    'action_text' => 'View Request',
                    'is_read' => false,
                ]);
            }

            DB::commit();

            return back()->with('success', "Reassigned to {$newActingStaff->first_name} {$newActingStaff->last_name}.");
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Acting staff reassignment failed: '.$e->getMessage());

            return back()->with('error', 'Failed to reassign: '.$e->getMessage());
        }
    }

    /**
     * HOD/HR/admin escalation action: skip the acting-staff response requirement
     * entirely and push the request to HOD approval. Recorded distinctly from a
     * real acceptance in both the audit log and the hold-position history comment.
     */
    public function overrideActingApproval(Request $request, $id)
    {
        $leaveRequest = LeaveRequest::with(['staff', 'actingStaff'])->findOrFail($id);

        if ($leaveRequest->acting_status !== 'pending') {
            return back()->with('error', 'This request is no longer awaiting an acting-staff response.');
        }

        $request->validate([
            'override_reason' => 'required|string|min:5|max:500',
        ]);

        DB::beginTransaction();

        try {
            $nextLevel = $this->initialApprovalLevel($leaveRequest->staff);

            $leaveRequest->update([
                'acting_status' => 'accepted',
                'acting_responded_at' => now(),
                'current_approval_level' => $nextLevel,
                'status' => 'pending',
                'acting_escalation_dismissed_at' => null,
                'acting_escalation_dismissed_by' => null,
                'acting_escalation_dismissal_reason' => null,
            ]);

            HoldPositionHistory::where('leave_request_id', $leaveRequest->id)
                ->where('holder_staff_id', $leaveRequest->acting_staff_id)
                ->where('status', 'pending')
                ->update([
                    'status' => 'accepted',
                    'responded_at' => now(),
                    'comment' => 'Administratively approved by '.auth()->user()->full_name.
                        ' — acting staff did not respond within 48+ hours. Reason: '.$request->override_reason,
                ]);

            // The approval timeline reads from approval_logs, not
            // acting_status directly - without this row, the "Acting Staff"
            // step silently vanishes from the activity feed even though the
            // stage stepper (which does read acting_status) shows it done.
            ApprovalLog::create([
                'request_type' => 'leave',
                'request_id' => $leaveRequest->id,
                'from_role' => 'Acting Staff',
                'to_role' => 'HOD',
                'action' => 'approved',
                'comment' => 'Approved on behalf of '.($leaveRequest->actingStaff->full_name ?? 'the acting staff').' by '.auth()->user()->full_name.'. Reason: '.$request->override_reason,
                'approver_staff_id' => auth()->id(),
                'ip_address' => $request->ip(),
            ]);

            AuditLogHelper::log('override_approve', 'acting', $leaveRequest->id,
                ['acting_status' => 'pending'],
                ['acting_status' => 'accepted', 'overridden_by' => auth()->id(), 'reason' => $request->override_reason]
            );

            Notification::create([
                'user_id' => $leaveRequest->staff_id,
                'type' => 'hold_accepted',
                'title' => 'Hold Position Administratively Approved',
                'message' => 'Your acting staff did not respond in time, so '.auth()->user()->full_name.
                    ' approved the hold-position step on their behalf. Your request has moved to HOD approval.',
                'action_url' => route('leave-requests.show', $leaveRequest->id),
                'action_text' => 'View Request',
                'is_read' => false,
            ]);

            if ($leaveRequest->actingStaff) {
                Notification::create([
                    'user_id' => $leaveRequest->actingStaff->id,
                    'type' => 'acting_overridden',
                    'title' => 'Hold Position Request Administratively Closed',
                    'message' => 'You did not respond to a hold-position request in time, so it was administratively approved by '.auth()->user()->full_name.'.',
                    'action_url' => route('leave-requests.show', $leaveRequest->id),
                    'action_text' => 'View Request',
                    'is_read' => false,
                ]);
            }

            $this->notifyNextApprover($leaveRequest, $nextLevel);

            $hod = $leaveRequest->staff->department->hod ?? null;
            if ($hod) {
                Notification::create([
                    'user_id' => $hod->id,
                    'type' => 'approval_pending_urgent',
                    'title' => 'URGENT: Leave Request Pending Your Approval',
                    'message' => "URGENT: {$leaveRequest->staff->first_name} {$leaveRequest->staff->last_name}'s leave request was administratively approved at the acting stage and is now awaiting your approval as Head of Department.",
                    'action_url' => route('leave-requests.pending'),
                    'action_text' => 'Approve Now',
                    'is_read' => false,
                    'priority' => 'urgent',
                ]);
            }

            DB::commit();

            return back()->with('success', 'Approved on behalf of acting staff. Request forwarded to HOD.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Acting approval override failed: '.$e->getMessage());

            return back()->with('error', 'Failed to override: '.$e->getMessage());
        }
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
            'documents',
        ])
            ->where('current_approval_level', LeaveRequest::LEVEL_HR)
            ->findOrFail($id);
        $this->assertSameUniversity($leaveRequest);

        $staffLeaveHistory = LeaveRequest::where('staff_id', $leaveRequest->staff_id)
            ->where('created_at', '>=', now()->subMonths(12))
            ->with('leaveType')
            ->orderBy('created_at', 'desc')
            ->get();

        $leaveBalances = LeaveBalance::where('staff_id', $leaveRequest->staff_id)->get();

        $departmentStaffOnLeave = LeaveRequest::where('status', 'approved')
            ->where('staff_id', '!=', $leaveRequest->staff_id)
            ->whereHas('staff', function ($q) use ($leaveRequest) {
                $q->where('department_id', $leaveRequest->staff->department_id);
            })
            ->where(function ($q) use ($leaveRequest) {
                $q->whereBetween('start_date', [$leaveRequest->start_date, $leaveRequest->end_date])
                    ->orWhereBetween('end_date', [$leaveRequest->start_date, $leaveRequest->end_date]);
            })
            ->with('staff')
            ->get();

        $blackoutPeriod = BlackoutPeriod::where('start_date', '<=', $leaveRequest->end_date)
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

        // Whether HR may grant part of a day is the university's policy too.
        $allowsHalfDays = app(LeavePolicy::class)->limitsFor($leaveRequest->staff, $leaveRequest->leaveType)['allow_half_day'];

        return view('shared.leave-requests.hr-review', compact(
            'leaveRequest',
            'allowsHalfDays',
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
        $leaveRequest = LeaveRequest::with('staff')->findOrFail($id);
        $this->assertSameUniversity($leaveRequest);

        $request->validate([
            'action' => 'required|in:approve,reject,request_info,draft',
            'adjusted_days' => 'nullable|numeric|min:0.5|max:365',
            'adjustment_reason' => 'nullable|string|max:255',
            'hr_comment' => 'nullable|string|max:1000',
            'conditions' => 'nullable|string|max:500',
            'payment_status' => 'required|in:paid,partial,unpaid',
            'payment_percentage' => 'required_if:payment_status,partial|nullable|numeric|min:0|max:100',
            'payment_notes' => 'required_if:payment_status,partial,unpaid|nullable|string|max:500',
            'travel_allowance_granted' => 'nullable|boolean',
            'travel_allowance_amount' => 'required_if:travel_allowance_granted,1|nullable|numeric|min:0|max:99999999.99',
            'travel_allowance_notes' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();

        try {
            $paymentNote = '';
            if ($request->payment_status == 'unpaid') {
                $paymentNote = "\n\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n⚠️ **PAYMENT NOTICE:** This leave is **UNPAID**.\n**Reason:** ".($request->payment_notes ?? 'Not specified')."\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━";
            } elseif ($request->payment_status == 'partial') {
                $paymentNote = "\n\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n⚠️ **PAYMENT NOTICE:** This leave is **PARTIALLY PAID** at {$request->payment_percentage}%.\n**Reason:** ".($request->payment_notes ?? 'Not specified')."\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━";
            }

            $travelAllowanceGranted = $request->boolean('travel_allowance_granted');

            $leaveRequest->update([
                'payment_status' => $request->payment_status,
                'payment_percentage' => $request->payment_percentage ?? 100,
                'payment_notes' => $request->payment_notes,
                'payment_decision_at' => now(),
                'payment_decided_by' => auth()->id(),

                'travel_allowance_granted' => $travelAllowanceGranted,
                'travel_allowance_amount' => $travelAllowanceGranted ? $request->travel_allowance_amount : 0,
                'travel_allowance_notes' => $request->travel_allowance_notes,
                'travel_allowance_decided_at' => now(),
                'travel_allowance_decided_by' => auth()->id(),
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

                Notification::create([
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
                $originalDays = $leaveRequest->days;
                $adjustedDays = $request->adjusted_days ?? $originalDays;
                $adjustmentReason = $request->adjustment_reason ?? null;
                $daysAdjusted = (float) $adjustedDays !== (float) $originalDays;

                if ($daysAdjusted) {
                    // Granting fewer days than were asked for is always fine;
                    // granting more is held to the university's leave policy.
                    if ($limitError = app(LeavePolicy::class)->adjustmentError($leaveRequest, (float) $adjustedDays)) {
                        DB::rollBack();

                        return redirect()->back()->with('error', $limitError)->withInput();
                    }

                    $leaveRequest->update([
                        'days' => $adjustedDays,
                        // A half-day request granted as something else is no longer a half day.
                        'half_day' => (float) $adjustedDays === 0.5 ? $leaveRequest->half_day : 'full',
                    ]);
                }

                // Was hardcoded to LEVEL_DVC - now follows whatever the
                // admin-configured chain actually has after HR.
                $nextLevel = LeaveApprovalStage::nextLevelAfter(LeaveRequest::LEVEL_HR) ?? LeaveRequest::LEVEL_HR_LOGISTICS;
                $leaveRequest->update([
                    'current_approval_level' => $nextLevel,
                ]);

                $hrComment = $request->hr_comment;
                // Every change of days is written into the request's history,
                // with or without a stated reason - and with the days it
                // started from ($leaveRequest->days is already the new value).
                if ($daysAdjusted) {
                    $hrComment = ($hrComment ? $hrComment."\n\n" : '')."📅 Day Adjustment: {$originalDays} → {$adjustedDays} days".($adjustmentReason ? ". Reason: {$adjustmentReason}" : '');
                }
                if ($request->conditions) {
                    $hrComment = ($hrComment ? $hrComment."\n\n" : '').'📋 Conditions: '.$request->conditions;
                }
                $hrComment .= $paymentNote;

                $nextLevelName = LeaveApprovalStage::displayNameFor($nextLevel) ?? 'the next stage';

                ApprovalLog::create([
                    'request_type' => 'leave',
                    'request_id' => $leaveRequest->id,
                    'from_role' => 'HR',
                    'to_role' => $nextLevelName,
                    'action' => 'approved',
                    'comment' => $hrComment,
                    'approver_staff_id' => auth()->id(),
                    'ip_address' => $request->ip(),
                ]);

                $this->notifyNextApprover($leaveRequest, $nextLevel);

                $message = "Leave request approved and forwarded to {$nextLevelName}.";
            }

            DB::commit();

            if ($request->action == 'draft') {
                return redirect()->back()->with('info', $message);
            }

            return redirect()->route('leave-requests.pending')
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with('error', 'Failed to process: '.$e->getMessage());
        }
    }

    /**
     * DVC Review Page - Level 4 (DVC reviews after HR)
     */
    public function dvcReview($id)
    {
        $user = auth()->user();
        $role = $user->getRoleNames()->first();

        if (! in_array($role, ['admin', 'dvca', 'dvc-pfa'])) {
            abort(403, 'You do not have permission to view this page.');
        }

        $leaveRequest = LeaveRequest::with([
            'staff',
            'leaveType',
            'actingStaff',
            'approvalLogs.approver',
            'documents',
        ])
            ->where('current_approval_level', LeaveRequest::LEVEL_DVC)
            ->findOrFail($id);
        $this->assertSameUniversity($leaveRequest);

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
            ->whereHas('staff', function ($q) use ($leaveRequest) {
                $q->where('department_id', $leaveRequest->staff->department_id);
            })
            ->where(function ($q) use ($leaveRequest) {
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

        if (! in_array($role, ['admin', 'dvca', 'dvc-pfa'])) {
            abort(403, 'You do not have permission to perform this action.');
        }

        $leaveRequest = LeaveRequest::with('staff')->findOrFail($id);
        $this->assertSameUniversity($leaveRequest);

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
                // Was hardcoded to LEVEL_HR_LOGISTICS - now follows whatever
                // the admin-configured chain actually has after DVC. In
                // practice this still lands on HR Logistics almost always,
                // since that stage stays fixed as the terminal step, but it
                // no longer assumes DVC is specifically the stage right
                // before it.
                $nextLevel = LeaveApprovalStage::nextLevelAfter(LeaveRequest::LEVEL_DVC) ?? LeaveRequest::LEVEL_HR_LOGISTICS;
                $leaveRequest->update([
                    'current_approval_level' => $nextLevel,
                ]);

                $nextLevelName = LeaveApprovalStage::displayNameFor($nextLevel) ?? 'HR Logistics';

                ApprovalLog::create([
                    'request_type' => 'leave',
                    'request_id' => $leaveRequest->id,
                    'from_role' => 'DVC',
                    'to_role' => $nextLevelName,
                    'action' => 'approved',
                    'comment' => $request->dvc_comment,
                    'approver_staff_id' => $user->id,
                    'ip_address' => $request->ip(),
                ]);

                $this->notifyNextApprover($leaveRequest, $nextLevel);

                $message = "Leave request approved. Forwarded to {$nextLevelName} for final processing.";
            }

            DB::commit();

            return redirect()->route('leave-requests.pending')
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with('error', 'Failed to process: '.$e->getMessage());
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

        if ($search = $request->get('search')) {
            $query->whereHas('leaveRequest.staff', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('staff_id', 'like', "%{$search}%");
            });
        }

        $approvals = $query->paginate($perPage)->appends($request->query());

        $stats = [
            'total' => ApprovalLog::where('approver_staff_id', $user->id)->count(),
            'approved' => ApprovalLog::where('approver_staff_id', $user->id)->where('action', 'approved')->count(),
            'rejected' => ApprovalLog::where('approver_staff_id', $user->id)->where('action', 'rejected')->count(),
            'this_month' => ApprovalLog::where('approver_staff_id', $user->id)->whereMonth('created_at', now()->month)->count(),
        ];

        if ($request->wantsJson()) {
            return response()->json([
                'html' => view('shared.leave-requests._approvals-history-results', compact('approvals'))->render(),
                'total' => $approvals->total(),
                'grand_total' => (clone $query)->count(),
            ]);
        }

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
            'letters',
        ])->findOrFail($id);

        $user = auth()->user();
        $userActionRecord = ApprovalLog::where('request_id', $leaveRequest->id)
            ->where('approver_staff_id', $user->id)
            ->first();

        if (! $userActionRecord && ! $user->hasAnyRole(['admin', 'vice-chancellor'])) {
            abort(403, 'You are not authorized to view this approval details.');
        }

        $userAction = $userActionRecord ? ucfirst($userActionRecord->action) : 'Viewed';
        $userActionDate = $userActionRecord ? $userActionRecord->created_at : $leaveRequest->updated_at;

        return view('shared.leave-requests.approval-details', compact('leaveRequest', 'userAction', 'userActionDate'));
    }

    /**
     * Download/stream a supporting document for a leave request.
     * Authorization mirrors show(): admin/vice-chancellor/hod/dean/hr-officer/dvca/dvc-pfa
     * roles, or the requester/acting staff on the parent leave request.
     */
    public function downloadDocument($documentId)
    {
        $document = Document::with('leaveRequest')->findOrFail($documentId);
        $leaveRequest = $document->leaveRequest;

        if (! $leaveRequest) {
            abort(404);
        }

        $user = auth()->user();
        $role = $user->getRoleNames()->first();

        $authorizedRoles = ['admin', 'vice-chancellor', 'hod', 'dean', 'hr-officer', 'hr-manager', 'dvca', 'dvc-pfa', 'internal-auditor', 'registrar'];

        $isAuthorized = in_array($role, $authorizedRoles)
            || $leaveRequest->staff_id === $user->id
            || $leaveRequest->acting_staff_id === $user->id;

        if (! $isAuthorized) {
            abort(403, 'You do not have permission to view this document.');
        }

        if (! Storage::disk('local')->exists($document->file_path)) {
            abort(404, 'File not found.');
        }

        return Storage::disk('local')->response($document->file_path, $document->file_name);
    }
}
