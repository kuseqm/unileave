<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Traits\PerPageTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuditLogController extends Controller
{
    use PerPageTrait;
    
    public function index(Request $request)
    {
        $user = auth()->user();
        $role = $user->getRoleNames()->first();
        
        // Role-based access - Staff cannot view audit logs
        if ($role == 'staff') {
            return redirect()->route('dashboard')->with('error', 'Access denied');
        }
        
        $query = AuditLog::with('user')->orderBy('created_at', 'desc');
        
        // Role-based filtering for audit logs
        switch ($role) {
            case 'admin':
                // Admin sees all logs
                break;
                
            case 'hr-officer':
                // HR sees logs of their university only
                $query->whereHas('user', function($q) use ($user) {
                    $q->where('university_id', $user->university_id);
                });
                break;
                
            case 'dean':
                // Dean sees logs of their school only
                $query->whereHas('user.department.school', function($q) use ($user) {
                    $q->where('dean_staff_id', $user->id);
                });
                break;
                
            case 'hod':
            case 'unit_head':
                // HOD sees logs of their department only
                $query->whereHas('user', function($q) use ($user) {
                    $q->where('department_id', $user->department_id);
                });
                break;
                
            case 'dvca':
                // DVCA sees academic staff logs only
                $query->whereHas('user', function($q) {
                    $q->where('staff_category', 'academic');
                });
                break;
                
            case 'dvc-pfa':
                // DVC-PFA sees admin/supporting staff logs only
                $query->whereHas('user', function($q) {
                    $q->whereIn('staff_category', ['administrative', 'supporting']);
                });
                break;
                
            default:
                return redirect()->route('dashboard')->with('error', 'Access denied');
        }
        
        // Apply filters
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        if ($request->filled('module')) {
            $query->where('module', $request->module);
        }
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }
        if ($request->filled('search')) {
            $search = '%' . $request->search . '%';
            $query->where(function($q) use ($search) {
                $q->where('details', 'like', $search)
                  ->orWhereHas('user', function($sq) use ($search) {
                      $sq->where('first_name', 'like', $search)
                         ->orWhere('last_name', 'like', $search)
                         ->orWhere('email', 'like', $search);
                  });
            });
        }
        
        $logs = $query->paginate($this->getPerPage($request, 50));
        
        // Get filter options
        $modules = AuditLog::distinct()->pluck('module');
        $actions = AuditLog::distinct()->pluck('action');
        
        return view('shared.audit-logs.index', compact('logs', 'role', 'modules', 'actions'));
    }
    
    public function user($userId)
    {
        $user = auth()->user();
        $role = $user->getRoleNames()->first();
        
        // Staff can only view their own logs
        if ($role == 'staff' && $userId != $user->id) {
            return redirect()->route('dashboard')->with('error', 'Access denied');
        }
        
        $logs = AuditLog::with('user')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->paginate(50);
        
        return view('shared.audit-logs.user', compact('logs', 'userId'));
    }
    
    public function export(Request $request)
    {
        // This will be implemented later
        return redirect()->back()->with('info', 'Export feature coming soon');
    }
}