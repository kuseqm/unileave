<?php

namespace App\Http\Controllers;

use App\Models\DocumentVerificationLog;
use Illuminate\Http\Request;

/**
 * Admin-facing browser for document_verification_logs - the "how can we
 * see it" answer to the question that led to that table existing at all
 * (see DocumentVerificationLog's own comment). Deliberately mirrors
 * AuditTrailController's shape (multi-tenant scoping, filters, stats,
 * pagination) rather than inventing a different pattern, since this is
 * the same kind of "browse a log of system events" page - reuses that
 * controller's own `view_audit_trail` permission too, instead of adding
 * a new permission + deciding which roles get it from scratch.
 */
class VerificationLogController extends Controller
{
    /**
     * Multi-tenant scoping mirrors AuditTrailController::isCrossInstitute()
     * - only a true super-admin sees every institution's scan activity;
     * everyone else (even with view_audit_trail) is confined to their own
     * university, resolved via the log row's own denormalized
     * university_id (see the migration's comment for why it's stored
     * directly rather than joined per document_type on every read).
     */
    private function isCrossInstitute($user): bool
    {
        return $user->hasRole('super-admin');
    }

    private function baseQuery(Request $request, $user)
    {
        $query = DocumentVerificationLog::with('university')->orderBy('created_at', 'desc');

        if (! $this->isCrossInstitute($user)) {
            $query->where('university_id', $user->university_id);
        }

        // Archived rows are set aside, not deleted - kept out of the
        // default list so the page stays readable, but still one filter
        // away from view() (see the "Archived" toggle in index.blade.php).
        $query->where('is_archived', $request->get('view') === 'archived');

        if ($request->filled('document_type') && $request->document_type !== 'all') {
            $query->where('document_type', $request->document_type);
        }

        if ($request->filled('outcome') && $request->outcome !== 'all') {
            $query->where('successful', $request->outcome === 'successful');
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                    ->orWhere('ip_address', 'like', "%{$search}%");
            });
        }

        return $query;
    }

    /**
     * Look up one log row, applying the same multi-tenant boundary as the
     * list - a non-super-admin can't view/archive another institution's
     * row just by guessing its id.
     */
    private function resolveLog(int $id, $user): DocumentVerificationLog
    {
        $query = DocumentVerificationLog::with('university');

        if (! $this->isCrossInstitute($user)) {
            $query->where('university_id', $user->university_id);
        }

        return $query->findOrFail($id);
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $isArchivedView = $request->get('view') === 'archived';

        $logs = $this->baseQuery($request, $user)
            ->paginate($request->get('per_page', 25))
            ->appends($request->query());

        if ($request->wantsJson()) {
            return response()->json([
                'html' => view('shared.verification-logs._results', compact('logs', 'isArchivedView'))->render(),
                'total' => $logs->total(),
                'grand_total' => $this->isCrossInstitute($user)
                    ? DocumentVerificationLog::where('is_archived', $isArchivedView)->count()
                    : DocumentVerificationLog::where('university_id', $user->university_id)->where('is_archived', $isArchivedView)->count(),
            ]);
        }

        // Stats always summarize the active set, regardless of which view
        // ("Active"/"Archived") the table below is currently showing - an
        // archived row is set aside precisely so it stops counting toward
        // "what needs attention today".
        $statsScope = fn () => $this->isCrossInstitute($user)
            ? DocumentVerificationLog::query()
            : DocumentVerificationLog::where('university_id', $user->university_id);

        $stats = [
            'total' => $statsScope()->where('is_archived', false)->count(),
            'successful' => $statsScope()->where('is_archived', false)->where('successful', true)->count(),
            'failed' => $statsScope()->where('is_archived', false)->where('successful', false)->count(),
            'today' => $statsScope()->where('is_archived', false)->whereDate('created_at', today())->count(),
            'archived' => $statsScope()->where('is_archived', true)->count(),
        ];

        return view('shared.verification-logs.index', [
            'logs' => $logs,
            'stats' => $stats,
            'isCrossInstitute' => $this->isCrossInstitute($user),
            'isArchivedView' => $isArchivedView,
        ]);
    }

    public function show(int $id)
    {
        $log = $this->resolveLog($id, auth()->user());

        return view('shared.verification-logs.show', compact('log'));
    }

    public function archive(int $id)
    {
        $log = $this->resolveLog($id, auth()->user());
        $log->update(['is_archived' => true]);

        return back()->with('success', 'Verification log archived. It is kept permanently and can be viewed under "Archived".');
    }

    public function unarchive(int $id)
    {
        $log = $this->resolveLog($id, auth()->user());
        $log->update(['is_archived' => false]);

        return back()->with('success', 'Verification log restored to the active list.');
    }
}
