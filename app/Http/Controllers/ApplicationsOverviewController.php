<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use App\Models\Notification;
use App\Models\PermissionRequest;
use App\Models\TravelPermission;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

/**
 * Read-only, cross-module oversight: every Leave, Permission, and Travel
 * request currently in flight, who it's waiting on, and how long it's been
 * sitting there. Nothing here can approve, reject, or otherwise touch a
 * request - the only action available is nudging whoever's turn it is.
 */
class ApplicationsOverviewController extends Controller
{
    /**
     * Waiting-on labels bucketed into who-type, for the stat tiles below.
     */
    private const DEPARTMENTAL_LABELS = ['Head of Department', 'Dean', 'DVC Academic', 'DVC PFA', 'Unit Head'];

    public function index(Request $request)
    {
        $user = auth()->user();
        // Oversight is an institute-level seat, not a cross-institute one -
        // this must only ever surface the viewer's own university's
        // activity (true super-admin excepted).
        $universityId = ($user->hasRole('super-admin') || $user->hasRole('admin')) ? null : $user->university_id;

        $activeTab = $request->get('tab', 'in-progress');
        if (! in_array($activeTab, ['in-progress', 'completed'])) {
            $activeTab = 'in-progress';
        }

        $activeSubtab = $request->get('subtab', 'all');
        if (! in_array($activeSubtab, ['all', 'leave', 'permission', 'travel'])) {
            $activeSubtab = 'all';
        }

        $completed = $activeTab === 'completed';

        // Cheap totals for the main tab header badges
        $pendingCounts = $this->counts($universityId, false);
        $completedCounts = $this->counts($universityId, true);

        // Load rows for the active tab and module only
        $activeItems = match ($activeSubtab) {
            'leave' => $this->leaveItems($universityId, $completed),
            'permission' => $this->permissionItems($universityId, $completed),
            'travel' => $this->travelItems($universityId, $completed),
            default => $this->items($universityId, $completed),
        };

        $activeStats = $this->stats($activeItems);
        $paginator = $this->paginateCollection($activeItems, 'page', 'per_page');

        return view('shared.applications-overview.index', compact(
            'activeTab', 'activeSubtab', 'completed', 'pendingCounts', 'completedCounts', 'activeStats', 'paginator'
        ));
    }

    private function items(?int $universityId, bool $completed): Collection
    {
        return collect()
            ->merge($this->leaveItems($universityId, $completed))
            ->merge($this->permissionItems($universityId, $completed))
            ->merge($this->travelItems($universityId, $completed))
            ->sortByDesc('submitted_at')
            ->values();
    }

    private function stats(Collection $items): array
    {
        return [
            'total' => $items->count(),
            'leave' => $items->where('module', 'leave')->count(),
            'permission' => $items->where('module', 'permission')->count(),
            'travel' => $items->where('module', 'travel')->count(),
            'overdue' => $items->where('days_pending', '>=', 2)->count(),
            'acting' => $items->where('waiting_on_label', 'Acting Staff')->count(),
            'departmental' => $items->whereIn('waiting_on_label', self::DEPARTMENTAL_LABELS)->count(),
            'unassigned' => $items->where('waiting_on_count', 0)->count(),
        ];
    }

    private function counts(?int $universityId, bool $completed): array
    {
        $leave = $this->moduleQuery('leave', $universityId, $completed)->count();
        $permission = $this->moduleQuery('permission', $universityId, $completed)->count();
        $travel = $this->moduleQuery('travel', $universityId, $completed)->count();

        return [
            'total' => $leave + $permission + $travel,
            'leave' => $leave,
            'permission' => $permission,
            'travel' => $travel,
            'overdue' => 0,
            'acting' => 0,
            'departmental' => 0,
            'unassigned' => 0,
        ];
    }

    private function moduleQuery(string $module, ?int $universityId, bool $completed)
    {
        $modelClass = match ($module) {
            'leave' => LeaveRequest::class,
            'permission' => PermissionRequest::class,
            'travel' => TravelPermission::class,
        };

        return $modelClass::query()
            ->whereIn('status', $this->statusesFor($module, $completed))
            ->when($universityId, function ($q) use ($universityId) {
                $q->whereHas('staff', fn ($s) => $s->where('university_id', $universityId));
            });
    }

    private function statusesFor(string $module, bool $completed): array
    {
        if ($completed) {
            return ['approved', 'rejected'];
        }

        return match ($module) {
            'permission' => ['pending', 'hod_approved', 'hr_approved'],
            default => ['pending'],
        };
    }

    private function paginateCollection(Collection $items, string $pageName, string $perPageName): LengthAwarePaginator
    {
        $perPage = (int) request($perPageName, 10);
        if (! in_array($perPage, [10, 25, 50])) {
            $perPage = 10;
        }

        $page = (int) request($pageName, 1);
        if ($page <= 0) {
            $page = 1;
        }

        $paginator = new LengthAwarePaginator(
            $items->forPage($page, $perPage)->values(),
            $items->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'pageName' => $pageName]
        );

        return $paginator->withQueryString();
    }

    /**
     * Re-send the same "pending your approval" notification to whoever the
     * request is currently waiting on - a nudge, not an action on the record.
     */
    public function remind(Request $request, string $module, int $id)
    {
        $user = auth()->user();

        [$userIds, $label] = match ($module) {
            'leave' => $this->leaveWaitingOn($this->findOwnUniversityOrFail(LeaveRequest::class, $id, $user)),
            'permission' => $this->permissionWaitingOn($this->findOwnUniversityOrFail(PermissionRequest::class, $id, $user)),
            'travel' => $this->travelWaitingOn($this->findOwnUniversityOrFail(TravelPermission::class, $id, $user)),
            default => [[], null],
        };

        if (empty($userIds)) {
            return back()->with('error', 'No one is currently assigned to act on this request.');
        }

        foreach ($userIds as $userId) {
            Notification::create([
                'user_id' => $userId,
                'type' => 'reminder',
                'title' => 'Reminder: Approval Pending',
                'message' => "A {$module} request has been waiting on your approval as {$label}. Please review it when you can.",
                'action_url' => $this->pendingUrlFor($module),
                'action_text' => 'Review Now',
                'is_read' => false,
                'priority' => 'high',
            ]);
        }

        return back()->with('success', 'Reminder sent.');
    }

    /**
     * @param  class-string<LeaveRequest|PermissionRequest|TravelPermission>  $modelClass
     */
    private function findOwnUniversityOrFail(string $modelClass, int $id, User $user)
    {
        $query = $modelClass::with('staff')->where('id', $id);

        if (! $user->hasRole('super-admin') && ! $user->hasRole('admin')) {
            $query->whereHas('staff', function ($q) use ($user) {
                $q->where('university_id', $user->university_id);
            });
        }

        return $query->firstOrFail();
    }

    private function pendingUrlFor(string $module): string
    {
        return match ($module) {
            'leave' => route('leave-requests.pending'),
            'permission' => route('permissions.pending'),
            'travel' => route('travel-permissions.pending'),
            default => route('dashboard'),
        };
    }

    /**
     * @return array{0: array<int>, 1: string} user ids currently responsible, and a display label
     */
    private function leaveWaitingOn(LeaveRequest $leave): array
    {
        if ($leave->acting_status === 'pending') {
            return $leave->acting_staff_id ? [[$leave->acting_staff_id], 'Acting Staff'] : [[], null];
        }

        $staff = $leave->staff;

        if (! $staff) {
            return [[], null];
        }

        return match ($leave->current_approval_level) {
            LeaveRequest::LEVEL_HOD => [array_filter([$staff->department?->hod_staff_id ?? null]), 'Head of Department'],
            LeaveRequest::LEVEL_DEAN => [array_filter([$staff->department?->school?->dean_staff_id ?? null]), 'Dean'],
            LeaveRequest::LEVEL_HR, LeaveRequest::LEVEL_HR_LOGISTICS => [User::role('hr-officer')->where('university_id', $staff->university_id)->pluck('id')->all(), 'HR'],
            LeaveRequest::LEVEL_DVC => $staff->staff_category === 'academic'
                ? [User::role('dvca')->where('university_id', $staff->university_id)->pluck('id')->all(), 'DVC Academic']
                : [User::role('dvc-pfa')->where('university_id', $staff->university_id)->pluck('id')->all(), 'DVC PFA'],
            default => [[], null],
        };
    }

    private function permissionWaitingOn(PermissionRequest $permission): array
    {
        $staff = $permission->staff;

        if (! $staff) {
            return [[], null];
        }

        if ($permission->hod_status === 'pending') {
            $id = $staff->department?->hod_staff_id ?? null;

            return [array_filter([$id]), 'Head of Department'];
        }

        if ($permission->hod_status === 'approved' && $permission->hr_status === 'pending') {
            return [User::role('hr-officer')->where('university_id', $staff->university_id)->pluck('id')->all(), 'HR'];
        }

        return [[], null];
    }

    private function travelWaitingOn(TravelPermission $travel): array
    {
        if ($travel->acting_status === 'pending') {
            return $travel->acting_staff_id ? [[$travel->acting_staff_id], 'Acting Staff'] : [[], null];
        }

        $staff = $travel->staff;

        if (! $staff) {
            return [[], null];
        }

        return match ($travel->current_approval_level) {
            'unit_head' => [array_filter([$staff->department?->unit_head_staff_id ?? null]), 'Unit Head'],
            'hod' => [array_filter([$staff->department?->hod_staff_id ?? null]), 'Head of Department'],
            'dean' => [array_filter([$staff->department?->school?->dean_staff_id ?? null]), 'Dean'],
            'dvc' => $staff->staff_category === 'academic'
                ? [User::role('dvca')->where('university_id', $staff->university_id)->pluck('id')->all(), 'DVC Academic']
                : [User::role('dvc-pfa')->where('university_id', $staff->university_id)->pluck('id')->all(), 'DVC PFA'],
            'hr_final' => [User::role('hr-officer')->where('university_id', $staff->university_id)->pluck('id')->all(), 'HR'],
            default => [[], null],
        };
    }

    private function leaveItems(?int $universityId, bool $completed)
    {
        $query = LeaveRequest::with(['staff', 'leaveType'])
            ->when($completed, function ($q) {
                $q->whereIn('status', ['approved', 'rejected']);
            }, function ($q) {
                $q->where('status', 'pending');
            })
            ->when($universityId, function ($q) use ($universityId) {
                $q->whereHas('staff', fn ($s) => $s->where('university_id', $universityId));
            })
            ->get();

        return $query->map(function (LeaveRequest $leave) use ($completed) {
            if ($completed) {
                $userIds = [];
                $label = null;
                $stage = ucfirst($leave->status);
            } else {
                [$userIds, $label] = $this->leaveWaitingOn($leave);
                $stage = $leave->acting_status === 'pending'
                    ? 'Awaiting Acting Staff'
                    : 'Awaiting '.$leave->getLevelName();
            }

            $reportingDate = $completed
                ? ($leave->expected_return_date ? $leave->expected_return_date->format('d M Y') : ($leave->end_date ? $leave->end_date->format('d M Y') : null))
                : null;

            return $this->row(
                module: 'leave',
                id: $leave->id,
                applicant: $leave->staff->full_name ?? 'N/A',
                typeLabel: $leave->leaveType->name ?? 'Leave',
                stageLabel: $stage,
                waitingOnLabel: $label,
                waitingOnCount: count($userIds),
                submittedAt: $leave->created_at,
                showRoute: route('leave-requests.show', $leave->id),
                reportingDate: $reportingDate,
            );
        });
    }

    private function permissionItems(?int $universityId, bool $completed)
    {
        $query = PermissionRequest::with(['staff'])
            ->when($completed, function ($q) {
                $q->whereIn('status', ['approved', 'rejected']);
            }, function ($q) {
                $q->whereIn('status', ['pending', 'hod_approved', 'hr_approved']);
            })
            ->when($universityId, function ($q) use ($universityId) {
                $q->whereHas('staff', fn ($s) => $s->where('university_id', $universityId));
            })
            ->get();

        return $query->map(function (PermissionRequest $permission) use ($completed) {
            if ($completed) {
                $userIds = [];
                $label = null;
                $stage = ucfirst($permission->status);
            } else {
                [$userIds, $label] = $this->permissionWaitingOn($permission);
                $stage = $permission->hod_status === 'pending'
                    ? 'Awaiting Head of Department'
                    : 'Awaiting HR';
            }

            $reportingDate = $completed && $permission->date ? $permission->date->format('d M Y') : null;

            return $this->row(
                module: 'permission',
                id: $permission->id,
                applicant: $permission->staff->full_name ?? 'N/A',
                typeLabel: $permission->permission_type_label,
                stageLabel: $stage,
                waitingOnLabel: $label,
                waitingOnCount: count($userIds),
                submittedAt: $permission->created_at,
                showRoute: route('permissions.show', $permission->id),
                reportingDate: $reportingDate,
            );
        });
    }

    private function travelItems(?int $universityId, bool $completed)
    {
        $query = TravelPermission::with(['staff'])
            ->when($completed, function ($q) {
                $q->whereIn('status', ['approved', 'rejected']);
            }, function ($q) {
                $q->where('status', 'pending');
            })
            ->when($universityId, function ($q) use ($universityId) {
                $q->whereHas('staff', fn ($s) => $s->where('university_id', $universityId));
            })
            ->get();

        return $query->map(function (TravelPermission $travel) use ($completed) {
            if ($completed) {
                $userIds = [];
                $label = null;
                $stage = ucfirst($travel->status);
            } else {
                [$userIds, $label] = $this->travelWaitingOn($travel);
                $stage = $travel->acting_status === 'pending'
                    ? 'Awaiting Acting Staff'
                    : 'Awaiting '.str_replace('_', ' ', ucfirst($travel->current_approval_level));
            }

            $reportingDate = $completed && $travel->end_date ? $travel->end_date->format('d M Y') : null;

            return $this->row(
                module: 'travel',
                id: $travel->id,
                applicant: $travel->staff->full_name ?? 'N/A',
                typeLabel: 'Travel to '.$travel->destination,
                stageLabel: $stage,
                waitingOnLabel: $label,
                waitingOnCount: count($userIds),
                submittedAt: $travel->created_at,
                showRoute: route('travel-permissions.show', $travel->id),
                reportingDate: $reportingDate,
            );
        });
    }

    private function row(
        string $module,
        int $id,
        string $applicant,
        string $typeLabel,
        string $stageLabel,
        ?string $waitingOnLabel,
        int $waitingOnCount,
        $submittedAt,
        string $showRoute,
        ?string $reportingDate = null,
    ): array {
        return [
            'module' => $module,
            'id' => $id,
            'applicant' => $applicant,
            'type_label' => $typeLabel,
            'stage_label' => $stageLabel,
            'waiting_on_label' => $waitingOnLabel,
            'waiting_on_count' => $waitingOnCount,
            'submitted_at' => $submittedAt,
            'days_pending' => (int) $submittedAt->diffInDays(now()),
            'show_route' => $showRoute,
            'reporting_date' => $reportingDate,
        ];
    }
}
