<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApprovalLog;
use App\Models\LeaveRequest;
use App\Models\Notification;
use App\Models\PermissionRequest;
use App\Models\TravelPermission;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * Admin-only bulk deletion for Leave/Permission/Travel requests - built so
 * demo or test data (seeded before a presentation, created while QA-ing a
 * feature, etc.) can be wiped in one pass instead of one row at a time.
 * This is a real, permanent delete - there is no undo.
 */
class RequestCleanupController extends Controller
{
    public function index(Request $request)
    {
        $module = $request->get('module', '');
        if ($module === '') {
            $module = 'all';
        }
        $search = $request->get('search');
        $from = $request->get('from');
        $to = $request->get('to');

        $items = collect()
            ->when($module === 'all' || $module === 'leave', fn ($c) => $c->merge($this->leaveRows($search, $from, $to)))
            ->when($module === 'all' || $module === 'permission', fn ($c) => $c->merge($this->permissionRows($search, $from, $to)))
            ->when($module === 'all' || $module === 'travel', fn ($c) => $c->merge($this->travelRows($search, $from, $to)))
            ->sortByDesc('created_at')
            ->values();

        $counts = [
            'leave' => LeaveRequest::count(),
            'permission' => PermissionRequest::count(),
            'travel' => TravelPermission::count(),
            'total' => LeaveRequest::count() + PermissionRequest::count() + TravelPermission::count(),
        ];

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'html' => view('admin.cleanup._results', compact('items'))->render(),
                'total' => $items->count(),
                'grand_total' => $this->grandTotal($module, $from, $to),
            ]);
        }

        return view('admin.cleanup.index', compact('items', 'module', 'search', 'from', 'to', 'counts'));
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*' => 'string',
        ]);

        $deleted = ['leave' => 0, 'permission' => 0, 'travel' => 0];

        foreach ($request->items as $item) {
            [$module, $id] = array_pad(explode(':', $item, 2), 2, null);

            if (! $id || ! isset($deleted[$module])) {
                continue;
            }

            match ($module) {
                'leave' => $this->deleteLeave((int) $id),
                'permission' => $this->deletePermission((int) $id),
                'travel' => $this->deleteTravel((int) $id),
                default => null,
            };

            $deleted[$module]++;
        }

        $summary = collect($deleted)->filter()->map(fn ($n, $module) => "{$n} {$module}")->implode(', ');

        return back()->with('success', $summary ? "Deleted: {$summary}." : 'Nothing was deleted.');
    }

    private function leaveRows(?string $search, ?string $from, ?string $to)
    {
        $q = LeaveRequest::with('staff')->latest();
        if ($search) {
            $q->where('reason', 'like', "%{$search}%");
        }
        $this->applyDateRange($q, $from, $to);

        return $q->get()->map(fn ($r) => [
            'module' => 'leave', 'id' => $r->id,
            'applicant' => $r->staff->full_name ?? 'N/A',
            'detail' => Str::limit($r->reason, 60),
            'status' => $r->status, 'created_at' => $r->created_at,
        ]);
    }

    private function permissionRows(?string $search, ?string $from, ?string $to)
    {
        $q = PermissionRequest::with('staff')->latest();
        if ($search) {
            $q->where('reason', 'like', "%{$search}%");
        }
        $this->applyDateRange($q, $from, $to);

        return $q->get()->map(fn ($r) => [
            'module' => 'permission', 'id' => $r->id,
            'applicant' => $r->staff->full_name ?? 'N/A',
            'detail' => Str::limit($r->reason, 60),
            'status' => $r->status, 'created_at' => $r->created_at,
        ]);
    }

    private function travelRows(?string $search, ?string $from, ?string $to)
    {
        $q = TravelPermission::with('staff')->latest();
        if ($search) {
            $q->where(function ($sq) use ($search) {
                $sq->where('destination', 'like', "%{$search}%")
                    ->orWhere('official_benefit', 'like', "%{$search}%");
            });
        }
        $this->applyDateRange($q, $from, $to);

        return $q->get()->map(fn ($r) => [
            'module' => 'travel', 'id' => $r->id,
            'applicant' => $r->staff->full_name ?? 'N/A',
            'detail' => 'Travel to '.$r->destination,
            'status' => $r->status, 'created_at' => $r->created_at,
        ]);
    }

    private function deleteLeave(int $id): void
    {
        ApprovalLog::where('request_type', 'leave')->where('request_id', $id)->delete();
        Notification::where('action_url', 'like', "%/leave-requests/{$id}%")->delete();
        LeaveRequest::where('id', $id)->delete();
    }

    private function deletePermission(int $id): void
    {
        Notification::where('action_url', 'like', "%/permissions/{$id}%")->delete();
        PermissionRequest::where('id', $id)->delete();
    }

    private function deleteTravel(int $id): void
    {
        Notification::where('action_url', 'like', "%/travel-permissions/{$id}%")->delete();
        TravelPermission::where('id', $id)->delete();
    }

    /**
     * Total records matching module and date filters (ignoring search).
     */
    private function grandTotal(string $module, ?string $from, ?string $to): int
    {
        $count = fn ($model) => $this->countModel($model, $from, $to);

        return match ($module) {
            'leave' => $count(LeaveRequest::class),
            'permission' => $count(PermissionRequest::class),
            'travel' => $count(TravelPermission::class),
            default => $count(LeaveRequest::class) + $count(PermissionRequest::class) + $count(TravelPermission::class),
        };
    }

    private function countModel(string $model, ?string $from, ?string $to): int
    {
        $query = $model::query();
        $this->applyDateRange($query, $from, $to);

        return $query->count();
    }

    /**
     * Filter a query to the user-selected created_at date/time range.
     */
    private function applyDateRange($query, ?string $from, ?string $to): void
    {
        if ($from) {
            $query->where('created_at', '>=', str_replace('T', ' ', $from));
        }
        if ($to) {
            $query->where('created_at', '<=', str_replace('T', ' ', $to));
        }
    }
}
