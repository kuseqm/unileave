<?php

namespace App\Http\Controllers;

use App\Models\UnifiedAuditTrail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuditTrailController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $role = $user->getRoleNames()->first();
        
        if (!in_array($role, ['admin', 'hr_manager', 'vice-chancellor', 'internal-auditor'])) {
            abort(403, 'You do not have permission to view audit trails.');
        }
        
        $query = UnifiedAuditTrail::with('user')->orderBy('created_at', 'desc');
        
        if ($request->has('entity_type') && $request->entity_type != 'all') {
            $entityMap = [
                'message' => 'App\Models\Message',
                'leave' => 'App\Models\LeaveRequest',
                'permission' => 'App\Models\PermissionRequest',
                'travel' => 'App\Models\TravelPermission',
                'letter' => 'App\Models\Letter',
            ];
            if (isset($entityMap[$request->entity_type])) {
                $query->where('auditable_type', $entityMap[$request->entity_type]);
            }
        }
        
        if ($request->has('event') && $request->event != 'all') {
            $query->where('event', $request->event);
        }
        
        if ($request->has('user_id') && $request->user_id != 'all') {
            $query->where('user_id', $request->user_id);
        }
        
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        $audits = $query->paginate(50);
        
        $stats = [
            'total' => UnifiedAuditTrail::count(),
            'today' => UnifiedAuditTrail::whereDate('created_at', today())->count(),
            'this_week' => UnifiedAuditTrail::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'this_month' => UnifiedAuditTrail::whereMonth('created_at', now()->month)->count(),
            'by_event' => UnifiedAuditTrail::select('event', DB::raw('count(*) as total'))->groupBy('event')->get(),
            'by_type' => UnifiedAuditTrail::select('auditable_type', DB::raw('count(*) as total'))->groupBy('auditable_type')->get(),
        ];
        
        $users = User::orderBy('first_name')->get(['id', 'first_name', 'last_name']);
        
        return view('shared.audit-trail.index', compact('audits', 'stats', 'users'));
    }
    
    public function show($id)
    {
        $audit = UnifiedAuditTrail::with('user')->findOrFail($id);
        
        $user = auth()->user();
        $role = $user->getRoleNames()->first();
        
        if (!in_array($role, ['admin', 'hr_manager', 'vice-chancellor', 'internal-auditor'])) {
            abort(403);
        }
        
        return view('shared.audit-trail.show', compact('audit'));
    }
    
    public function export(Request $request)
    {
        $user = auth()->user();
        $role = $user->getRoleNames()->first();
        
        if (!in_array($role, ['admin', 'hr_manager', 'vice-chancellor', 'internal-auditor'])) {
            abort(403);
        }
        
        $query = UnifiedAuditTrail::with('user')->orderBy('created_at', 'desc');
        
        if ($request->has('entity_type') && $request->entity_type != 'all') {
            $entityMap = [
                'message' => 'App\Models\Message',
                'leave' => 'App\Models\LeaveRequest',
                'permission' => 'App\Models\PermissionRequest',
                'travel' => 'App\Models\TravelPermission',
                'letter' => 'App\Models\Letter',
            ];
            if (isset($entityMap[$request->entity_type])) {
                $query->where('auditable_type', $entityMap[$request->entity_type]);
            }
        }
        
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        $audits = $query->get();
        
        $filename = 'audit-trail-' . date('Y-m-d-His') . '.csv';
        $handle = fopen('php://temp', 'w+');
        
        fputcsv($handle, ['ID', 'Date/Time', 'User', 'Role', 'Entity Type', 'Event', 'Action', 'IP Address', 'User Agent', 'Metadata']);
        
        foreach ($audits as $audit) {
            fputcsv($handle, [
                $audit->id,
                $audit->created_at->format('Y-m-d H:i:s'),
                ($audit->user->first_name ?? '') . ' ' . ($audit->user->last_name ?? ''),
                $audit->user_role,
                $audit->entity_type,
                $audit->event,
                $audit->action,
                $audit->ip_address,
                $audit->user_agent,
                json_encode($audit->metadata),
            ]);
        }
        
        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);
        
        return response($content, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}