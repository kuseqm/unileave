<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\AuditLogHelper;
use App\Http\Controllers\Controller;
use App\Models\LeaveLetter;
use App\Models\LeaveRequest;
use App\Traits\LetterTrait;
use App\Traits\PerPageTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LeaveDocumentController extends Controller
{
    use LetterTrait, PerPageTrait;

    /**
     * List approved leave requests that need a formal leave letter prepared.
     */
    public function index(Request $request)
    {
        $auth = auth()->user();

        $query = LeaveRequest::with(['staff', 'leaveType', 'latestLetter'])
            ->whereIn('status', ['approved', 'pending'])
            ->where(function ($q) {
                $q->where('status', 'approved')
                    ->orWhere('current_approval_level', LeaveRequest::LEVEL_HR_LOGISTICS);
            })
            ->orderBy('created_at', 'desc');

        if (! $auth->hasRole('super-admin')) {
            $query->whereHas('staff', function ($q) use ($auth) {
                $q->where('university_id', $auth->university_id);
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('staff', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('staff_id', 'like', "%{$search}%");
            });
        }

        $perPage = $this->getPerPage($request, 20);
        $grandTotal = (clone $query)->count();
        $leaveRequests = $query->paginate($perPage)->appends($request->query());

        if ($request->wantsJson()) {
            return response()->json([
                'html' => view('admin.leave-documents._results', compact('leaveRequests'))->render(),
                'total' => $leaveRequests->total(),
                'grand_total' => $grandTotal,
            ]);
        }

        return view('admin.leave-documents.index', compact('leaveRequests'));
    }

    /**
     * Show the document-preparation form for a leave request.
     */
    public function prepare($id)
    {
        $leaveRequest = LeaveRequest::with(['staff', 'leaveType'])->findOrFail($id);
        $letter = $leaveRequest->latestLetter;
        $content = $letter?->content ?? $this->defaultLetterContent($leaveRequest);

        return view('admin.leave-documents.prepare', compact('leaveRequest', 'letter', 'content'));
    }

    /**
     * Save or update the prepared leave document.
     */
    public function store(Request $request, $id)
    {
        $leaveRequest = LeaveRequest::with(['staff', 'leaveType'])->findOrFail($id);

        $request->validate([
            'content' => 'required|string|min:20',
            'stage' => 'nullable|in:draft,generated',
        ]);

        DB::beginTransaction();

        try {
            $user = auth()->user();
            $stage = $request->input('stage', 'draft');
            $existingLetter = $leaveRequest->latestLetter;

            if ($existingLetter) {
                $letter = $existingLetter;
                $letter->update([
                    'content' => $request->content,
                    'stage' => $stage,
                    'generated_by' => $user->full_name,
                    'generated_by_role' => $user->getRoleNames()->first(),
                ]);
            } else {
                $letter = LeaveLetter::create([
                    'leave_request_id' => $leaveRequest->id,
                    'staff_id' => $leaveRequest->staff_id,
                    'letter_number' => $this->generateLetterNumber(),
                    'secure_code' => 'SMU-LTR-'.Str::upper(Str::random(8)),
                    'title' => 'Leave Approval Letter - '.($leaveRequest->leaveType->name ?? 'Leave Request'),
                    'content' => $request->content,
                    'type' => 'approval',
                    'stage' => $stage,
                    'generated_by' => $user->full_name,
                    'generated_by_role' => $user->getRoleNames()->first(),
                    'payment_status' => 'paid',
                    'signatories' => [],
                    'cc_recipients' => [],
                    'metadata' => [
                        'leave_type' => $leaveRequest->leaveType->name ?? null,
                        'days' => $leaveRequest->days,
                        'start_date' => $leaveRequest->start_date?->toDateString(),
                        'end_date' => $leaveRequest->end_date?->toDateString(),
                    ],
                ]);
            }

            AuditLogHelper::log(
                $existingLetter ? 'update' : 'create',
                'leave_letter',
                $letter->id,
                null,
                $letter->toArray()
            );

            DB::commit();

            return redirect()->route('admin.leave-documents.show', $letter->id)
                ->with('success', 'Leave document prepared and saved as '.ucfirst($stage).'.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Failed to prepare leave document: '.$e->getMessage());

            return redirect()->back()->with('error', 'Failed to save leave document: '.$e->getMessage());
        }
    }

    /**
     * Display a prepared leave document.
     */
    public function show(LeaveLetter $letter)
    {
        $letter->load(['leaveRequest', 'staff']);

        // The route's own permission gate allows `view_own_leave`, which is
        // granted broadly - without an ownership check here, that gate lets
        // any staff member view any OTHER staff member's letter by simply
        // changing the {letter} id in the URL. Preparers/oversight roles
        // bypass this; everyone else must be the letter's own staff member
        // or the colleague currently holding their position.
        $user = auth()->user();
        $isOwner = $letter->staff_id === $user->id;
        $isActingStaff = $letter->leaveRequest && $letter->leaveRequest->acting_staff_id === $user->id;

        abort_unless(
            $isOwner || $isActingStaff || $user->can('prepare_leave_documents') || $user->can('view_all_leave'),
            403,
            'You do not have permission to view this letter.'
        );

        return view('admin.leave-documents.show', compact('letter'));
    }

    /**
     * Build a default approval letter body.
     */
    private function defaultLetterContent(LeaveRequest $leaveRequest): string
    {
        $staff = $leaveRequest->staff;
        $leaveType = $leaveRequest->leaveType?->name ?? 'LEAVE';
        $createdAt = $leaveRequest->created_at?->format('d F Y') ?? now()->format('d F Y');
        $startDate = $leaveRequest->start_date?->format('d F Y') ?? 'N/A';
        $endDate = $leaveRequest->end_date?->format('d F Y') ?? 'N/A';
        $returnDate = $leaveRequest->expected_return_date?->format('d F Y')
            ?? 'the next working day after the expiry of your leave';

        return <<<TXT
REF: ___________________

Date: {$createdAt}

To: {$staff->full_name}
Staff ID: {$staff->staff_id}

RE: APPROVAL OF {$leaveType} REQUEST

This is to inform you that your application for {$leaveType} from {$startDate} to {$endDate} ({$leaveRequest->days} day(s)) has been approved.

You are expected to resume duty on {$returnDate}.

Yours faithfully,

_________________________
Human Resources Manager
TXT;
    }
}
