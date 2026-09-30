<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuditTrailController extends Controller
{
    /**
     * Roles trusted to see every user's audit trail. Everyone else only
     * ever sees their own actions.
     */
    private const ROLES_VIEW_ALL = ['admin', 'hr-manager', 'vice-chancellor', 'internal-auditor', 'registrar'];

    /**
     * Friendly filter key => actual value stored in audit_logs.module
     * (casing here matches what's really in the column - it's inconsistent
     * upstream, e.g. 'Staff' and 'Import' are capitalized).
     */
    private const ENTITY_MAP = [
        'leave' => 'leave',
        'permission' => 'permission',
        'travel' => 'travel',
        'acting' => 'acting',
        'acting_escalation' => 'acting_escalation',
        'incoming_letter' => 'incoming_letter',
        'staff' => 'Staff',
        'import' => 'Import',
        'auth' => 'auth',
    ];

    private function canViewAll(User $user): bool
    {
        return $user->hasAnyRole(self::ROLES_VIEW_ALL);
    }

    /**
     * "View all" is institute-wide, not cross-institute - a registrar, HR
     * manager, VC, etc. at one university must never see another's audit
     * trail. Only a true super-admin is exempt.
     */
    private function isCrossInstitute(User $user): bool
    {
        return $user->hasRole('super-admin');
    }

    private function baseQuery(Request $request, bool $canViewAll, User $user)
    {
        $query = AuditLog::with('user')->orderBy('created_at', 'desc');

        if (! $canViewAll) {
            $query->where('user_id', $user->id);
        } elseif (! $this->isCrossInstitute($user)) {
            $query->whereHas('user', function ($q) use ($user) {
                $q->where('university_id', $user->university_id);
            });
        }

        if ($request->filled('entity_type') && $request->entity_type != 'all' && isset(self::ENTITY_MAP[$request->entity_type])) {
            $query->where('module', self::ENTITY_MAP[$request->entity_type]);
        }

        if ($request->filled('event') && $request->event != 'all') {
            $query->where('action', $request->event);
        }

        if ($canViewAll && $request->filled('user_id') && $request->user_id != 'all') {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('action', 'like', "%{$search}%")
                    ->orWhere('ip_address', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($sq) use ($search) {
                        $sq->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        return $query;
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $canViewAll = $this->canViewAll($user);

        $audits = $this->baseQuery($request, $canViewAll, $user)
            ->paginate($request->get('per_page', 50))
            ->appends($request->query());

        $crossInstitute = $this->isCrossInstitute($user);
        $ownUniversityScope = fn ($query) => $crossInstitute ? $query : $query->whereHas('user', function ($q) use ($user) {
            $q->where('university_id', $user->university_id);
        });

        if ($request->wantsJson()) {
            return response()->json([
                'html' => view('shared.audit-trail._results', compact('audits'))->render(),
                'total' => $audits->total(),
                'grand_total' => $canViewAll ? $ownUniversityScope(AuditLog::query())->count() : AuditLog::where('user_id', $user->id)->count(),
            ]);
        }

        $statsScope = fn () => $canViewAll
            ? $ownUniversityScope(AuditLog::query())
            : AuditLog::where('user_id', $user->id);

        $stats = [
            'total' => $statsScope()->count(),
            'today' => $statsScope()->whereDate('created_at', today())->count(),
            'this_week' => $statsScope()->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'this_month' => $statsScope()->whereMonth('created_at', now()->month)->count(),
            'by_event' => $statsScope()->select('action as event', DB::raw('count(*) as total'))->groupBy('action')->get(),
            'by_type' => $statsScope()->select('module as auditable_type', DB::raw('count(*) as total'))->groupBy('module')->get(),
        ];

        $usersQuery = User::orderBy('first_name');
        if (! $crossInstitute) {
            $usersQuery->where('university_id', $user->university_id);
        }
        $users = $canViewAll ? $usersQuery->get(['id', 'first_name', 'last_name']) : collect();

        return view('shared.audit-trail.index', compact('audits', 'stats', 'users', 'canViewAll'));
    }

    public function show($id)
    {
        $audit = AuditLog::with('user')->findOrFail($id);

        $user = auth()->user();

        if (! $this->canViewAll($user) && $audit->user_id !== $user->id) {
            abort(403);
        }

        if ($this->canViewAll($user) && ! $this->isCrossInstitute($user)
            && $audit->user && $audit->user->university_id !== $user->university_id) {
            abort(403);
        }

        return view('shared.audit-trail.show', compact('audit'));
    }

    public function export(Request $request)
    {
        $user = auth()->user();
        $canViewAll = $this->canViewAll($user);

        $audits = $this->baseQuery($request, $canViewAll, $user)->get();

        $filename = 'audit-trail-'.date('Y-m-d-His').'.csv';
        $handle = fopen('php://temp', 'w+');

        fputcsv($handle, ['ID', 'Date/Time', 'User', 'Role', 'Entity Type', 'Event', 'Action', 'IP Address', 'User Agent']);

        foreach ($audits as $audit) {
            fputcsv($handle, [
                $audit->id,
                $audit->created_at->format('Y-m-d H:i:s'),
                ($audit->user->first_name ?? '').' '.($audit->user->last_name ?? ''),
                $audit->user_role,
                $audit->entity_type,
                $audit->event,
                $audit->action,
                $audit->ip_address,
                $audit->user_agent,
            ]);
        }

        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        return response($content, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }
}
