<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\LeaveRequest;
use App\Models\LeaveBalance;
use App\Models\BlackoutPeriod;
use App\Models\Department;
use App\Models\LeaveType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $role = $user->getRoleNames()->first();
        
        $data = [];
        
        // ============================================
        // COMMON DATA FOR ALL ROLES
        // ============================================
        
        // On Leave Today
        $data['onLeaveToday'] = LeaveRequest::where('status', 'approved')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->count();
        
        // Blackout periods
        try {
            $data['blackoutPeriods'] = BlackoutPeriod::where('end_date', '>=', now())
                ->orderBy('start_date')
                ->limit(5)
                ->get();
        } catch (\Exception $e) {
            $data['blackoutPeriods'] = collect();
        }
        
        // Upcoming Leave (next 7 days)
        $data['upcomingLeave'] = LeaveRequest::where('status', 'approved')
            ->where('start_date', '>=', now())
            ->where('start_date', '<=', now()->addDays(7))
            ->count();
        
        // ============================================
        // STATS LINKS FOR CLICKABLE CARDS
        // ============================================
        $data['statsLinks'] = [];
        
        // ============================================
        // ROLE-SPECIFIC DATA
        // ============================================
        
        if ($role == 'staff') {
            // Staff dashboard
            $data['myTotalRequests'] = LeaveRequest::where('staff_id', $user->id)->count();
            $data['myPendingRequests'] = LeaveRequest::where('staff_id', $user->id)->where('status', 'pending')->count();
            $data['myApprovedRequests'] = LeaveRequest::where('staff_id', $user->id)->where('status', 'approved')->count();
            $data['myRecentRequests'] = LeaveRequest::where('staff_id', $user->id)->latest()->limit(5)->get();
            
            $balance = LeaveBalance::where('staff_id', $user->id)->where('year', date('Y'))->first();
            $data['myLeaveBalance'] = $balance ? $balance->balance : 0;
            
            // For stat cards
            $data['totalStaff'] = 0;
            $data['pendingApprovalsCount'] = $data['myPendingRequests'];
            $data['leaveUsed'] = LeaveRequest::where('staff_id', $user->id)->where('status', 'approved')->sum('days');
            $data['lowBalanceCount'] = $data['myLeaveBalance'] < 5 && $data['myLeaveBalance'] > 0 ? 1 : 0;
            $data['totalDepartments'] = 0;
            $data['leaveTypesCount'] = LeaveType::where('status', 'active')->count();
            $data['newStaffThisMonth'] = 0;
            $data['leaveIncrease'] = 0;
            $data['departmentLeaveStats'] = [];
            $data['lowBalanceStaff'] = [];
            $data['recentApprovals'] = [];
            $data['leaveTypeStats'] = [];
            
            // STAFF STATS LINKS
            $data['statsLinks'] = [
                'total_requests' => route('leave-requests.status', ['filter' => 'all']),
                'pending_requests' => route('leave-requests.status', ['filter' => 'pending']),
                'approved_requests' => route('leave-requests.status', ['filter' => 'approved']),
                'rejected_requests' => route('leave-requests.status', ['filter' => 'rejected']),
                'cancelled_requests' => route('leave-requests.status', ['filter' => 'cancelled']),
                'my_leave_balance' => null,
            ];
            
        } elseif ($role == 'unit_head') {
            // Unit Head dashboard
            $unitHeadDept = $user->unitHeadDepartment;
            
            if ($unitHeadDept) {
                // Pending approvals for Unit Head
                $data['pendingApprovalsCount'] = LeaveRequest::whereHas('staff', function($q) use ($unitHeadDept) {
                    $q->where('department_id', $unitHeadDept->id);
                })->where('status', 'pending')
                  ->where('acting_status', 'accepted')
                  ->where('current_approval_level', 'unit_head')
                  ->count();
                
                $data['pendingApprovals'] = LeaveRequest::with(['staff', 'leaveType'])
                    ->whereHas('staff', function($q) use ($unitHeadDept) {
                        $q->where('department_id', $unitHeadDept->id);
                    })->where('status', 'pending')
                    ->where('acting_status', 'accepted')
                    ->where('current_approval_level', 'unit_head')
                    ->latest()->limit(5)->get();
                
                // Total staff in unit
                $data['totalStaff'] = User::where('department_id', $unitHeadDept->id)->count();
                
                // Leave used by unit staff
                $data['leaveUsed'] = LeaveRequest::whereHas('staff', function($q) use ($unitHeadDept) {
                    $q->where('department_id', $unitHeadDept->id);
                })->where('status', 'approved')->sum('days');
                
                // Low balance count in unit
                $data['lowBalanceCount'] = User::where('department_id', $unitHeadDept->id)
                    ->whereHas('leaveBalances', function($q) {
                        $q->where('leave_type_id', 1)->where('balance', '<', 5)->where('balance', '>', 0);
                    })->count();
                
                // Upcoming leave in unit
                $data['upcomingLeave'] = LeaveRequest::whereHas('staff', function($q) use ($unitHeadDept) {
                    $q->where('department_id', $unitHeadDept->id);
                })->where('status', 'approved')
                  ->where('start_date', '>=', now())
                  ->where('start_date', '<=', now()->addDays(7))
                  ->count();
            } else {
                $data['pendingApprovalsCount'] = 0;
                $data['totalStaff'] = 0;
                $data['leaveUsed'] = 0;
                $data['lowBalanceCount'] = 0;
                $data['pendingApprovals'] = collect();
            }
            
            $data['totalDepartments'] = 1;
            $data['leaveTypesCount'] = LeaveType::where('status', 'active')->count();
            $data['newStaffThisMonth'] = 0;
            $data['leaveIncrease'] = 0;
            $data['departmentLeaveStats'] = [];
            $data['lowBalanceStaff'] = [];
            $data['recentApprovals'] = [];
            $data['leaveTypeStats'] = [];
            $data['myRecentRequests'] = [];
            
            // UNIT HEAD STATS LINKS
            $data['statsLinks'] = [
                'total_staff' => route('staff.index'),
                'pending_approvals' => route('leave-requests.pending'),
                'leave_used' => null,
                'low_balance' => route('staff.index', ['filter' => 'low_balance']),
                'upcoming_leave' => null,
            ];
            
        } elseif ($role == 'hod') {
            // HOD dashboard
            $data['pendingApprovalsCount'] = LeaveRequest::whereHas('staff.department', function($q) use ($user) {
                $q->where('hod_staff_id', $user->id);
            })->where('status', 'pending')->where('acting_status', 'accepted')->where('current_approval_level', 'hod')->count();
            
            $data['pendingApprovals'] = LeaveRequest::with(['staff', 'leaveType'])
                ->whereHas('staff.department', function($q) use ($user) {
                    $q->where('hod_staff_id', $user->id);
                })->where('status', 'pending')->where('acting_status', 'accepted')->where('current_approval_level', 'hod')
                ->latest()->limit(5)->get();
            
            // For stat cards
            $data['totalStaff'] = User::whereHas('department', function($q) use ($user) {
                $q->where('hod_staff_id', $user->id);
            })->count();
            $data['leaveUsed'] = LeaveRequest::whereHas('staff.department', function($q) use ($user) {
                $q->where('hod_staff_id', $user->id);
            })->where('status', 'approved')->sum('days');
            $data['lowBalanceCount'] = User::whereHas('department', function($q) use ($user) {
                $q->where('hod_staff_id', $user->id);
            })->whereHas('leaveBalances', function($q) {
                $q->where('leave_type_id', 1)->where('balance', '<', 5)->where('balance', '>', 0);
            })->count();
            $data['upcomingLeave'] = LeaveRequest::whereHas('staff.department', function($q) use ($user) {
                $q->where('hod_staff_id', $user->id);
            })->where('status', 'approved')->where('start_date', '>=', now())->where('start_date', '<=', now()->addDays(7))->count();
            $data['totalDepartments'] = 1;
            $data['leaveTypesCount'] = LeaveType::where('status', 'active')->count();
            $data['newStaffThisMonth'] = User::whereHas('department', function($q) use ($user) {
                $q->where('hod_staff_id', $user->id);
            })->whereMonth('created_at', now()->month)->count();
            $data['leaveIncrease'] = 0;
            $data['departmentLeaveStats'] = [];
            $data['lowBalanceStaff'] = [];
            $data['recentApprovals'] = [];
            $data['leaveTypeStats'] = [];
            $data['myRecentRequests'] = [];
            
            // HOD STATS LINKS
            $data['statsLinks'] = [
                'total_staff' => route('staff.index'),
                'pending_approvals' => route('leave-requests.pending'),
                'leave_used' => null,
                'low_balance' => route('staff.index', ['filter' => 'low_balance']),
                'upcoming_leave' => null,
                'new_staff' => route('staff.create'),
            ];
            
        } elseif ($role == 'dean') {
            // Dean dashboard
            $data['pendingApprovalsCount'] = LeaveRequest::whereHas('staff.department.school', function($q) use ($user) {
                $q->where('dean_staff_id', $user->id);
            })->where('status', 'pending')->where('acting_status', 'accepted')->where('current_approval_level', 'dean')->count();
            
            $data['pendingApprovals'] = LeaveRequest::with(['staff', 'leaveType'])
                ->whereHas('staff.department.school', function($q) use ($user) {
                    $q->where('dean_staff_id', $user->id);
                })->where('status', 'pending')->where('acting_status', 'accepted')->where('current_approval_level', 'dean')
                ->latest()->limit(5)->get();
            
            // For stat cards
            $data['totalStaff'] = User::whereHas('department.school', function($q) use ($user) {
                $q->where('dean_staff_id', $user->id);
            })->count();
            $data['leaveUsed'] = LeaveRequest::whereHas('staff.department.school', function($q) use ($user) {
                $q->where('dean_staff_id', $user->id);
            })->where('status', 'approved')->sum('days');
            $data['lowBalanceCount'] = User::whereHas('department.school', function($q) use ($user) {
                $q->where('dean_staff_id', $user->id);
            })->whereHas('leaveBalances', function($q) {
                $q->where('leave_type_id', 1)->where('balance', '<', 5)->where('balance', '>', 0);
            })->count();
            $data['upcomingLeave'] = LeaveRequest::whereHas('staff.department.school', function($q) use ($user) {
                $q->where('dean_staff_id', $user->id);
            })->where('status', 'approved')->where('start_date', '>=', now())->where('start_date', '<=', now()->addDays(7))->count();
            $data['totalDepartments'] = Department::whereHas('school', function($q) use ($user) {
                $q->where('dean_staff_id', $user->id);
            })->count();
            $data['leaveTypesCount'] = LeaveType::where('status', 'active')->count();
            $data['newStaffThisMonth'] = User::whereHas('department.school', function($q) use ($user) {
                $q->where('dean_staff_id', $user->id);
            })->whereMonth('created_at', now()->month)->count();
            $data['leaveIncrease'] = 0;
            $data['departmentLeaveStats'] = [];
            $data['lowBalanceStaff'] = [];
            $data['recentApprovals'] = [];
            $data['leaveTypeStats'] = [];
            $data['myRecentRequests'] = [];
            
            // DEAN STATS LINKS
            $data['statsLinks'] = [
                'total_staff' => route('staff.index'),
                'pending_approvals' => route('leave-requests.pending'),
                'leave_used' => null,
                'low_balance' => route('staff.index', ['filter' => 'low_balance']),
                'upcoming_leave' => null,
                'total_departments' => route('admin.departments.index'),
            ];
            
        } elseif ($role == 'dvca') {
            // DVCA dashboard
            $data['pendingApprovalsCount'] = LeaveRequest::whereHas('staff', function($q) {
                $q->where('staff_category', 'academic');
            })->where('status', 'pending')
              ->where('acting_status', 'accepted')
              ->where('current_approval_level', 'dvc')
              ->count();
            
            $data['pendingApprovals'] = LeaveRequest::with(['staff', 'leaveType'])
                ->whereHas('staff', function($q) {
                    $q->where('staff_category', 'academic');
                })->where('status', 'pending')
                ->where('acting_status', 'accepted')
                ->where('current_approval_level', 'dvc')
                ->latest()->limit(5)->get();
            
            // For stat cards
            $data['totalStaff'] = User::where('staff_category', 'academic')->count();
            $data['leaveUsed'] = LeaveRequest::whereHas('staff', function($q) {
                $q->where('staff_category', 'academic');
            })->where('status', 'approved')->sum('days');
            $data['lowBalanceCount'] = User::where('staff_category', 'academic')
                ->whereHas('leaveBalances', function($q) {
                    $q->where('leave_type_id', 1)->where('balance', '<', 5)->where('balance', '>', 0);
                })->count();
            $data['upcomingLeave'] = LeaveRequest::whereHas('staff', function($q) {
                $q->where('staff_category', 'academic');
            })->where('status', 'approved')->where('start_date', '>=', now())->where('start_date', '<=', now()->addDays(7))->count();
            $data['totalDepartments'] = Department::count();
            $data['leaveTypesCount'] = LeaveType::where('status', 'active')->count();
            $data['newStaffThisMonth'] = User::where('staff_category', 'academic')->whereMonth('created_at', now()->month)->count();
            $data['leaveIncrease'] = 0;
            $data['departmentLeaveStats'] = [];
            $data['lowBalanceStaff'] = [];
            $data['recentApprovals'] = [];
            $data['leaveTypeStats'] = [];
            $data['myRecentRequests'] = [];
            
            // DVCA STATS LINKS
            $data['statsLinks'] = [
                'total_staff' => route('staff.index', ['category' => 'academic']),
                'pending_approvals' => route('leave-requests.pending'),
                'leave_used' => null,
                'low_balance' => route('staff.index', ['filter' => 'low_balance', 'category' => 'academic']),
                'upcoming_leave' => null,
            ];
            
        } elseif ($role == 'dvc-pfa') {
            // DVC-PFA dashboard
            $data['pendingApprovalsCount'] = LeaveRequest::whereHas('staff', function($q) {
                $q->where('staff_category', 'administrative');
            })->where('status', 'pending')
              ->where('acting_status', 'accepted')
              ->where('current_approval_level', 'dvc')
              ->count();
            
            $data['pendingApprovals'] = LeaveRequest::with(['staff', 'leaveType'])
                ->whereHas('staff', function($q) {
                    $q->where('staff_category', 'administrative');
                })->where('status', 'pending')
                ->where('acting_status', 'accepted')
                ->where('current_approval_level', 'dvc')
                ->latest()->limit(5)->get();
            
            // For stat cards
            $data['totalStaff'] = User::where('staff_category', 'administrative')->count();
            $data['leaveUsed'] = LeaveRequest::whereHas('staff', function($q) {
                $q->where('staff_category', 'administrative');
            })->where('status', 'approved')->sum('days');
            $data['lowBalanceCount'] = User::where('staff_category', 'administrative')
                ->whereHas('leaveBalances', function($q) {
                    $q->where('leave_type_id', 1)->where('balance', '<', 5)->where('balance', '>', 0);
                })->count();
            $data['upcomingLeave'] = LeaveRequest::whereHas('staff', function($q) {
                $q->where('staff_category', 'administrative');
            })->where('status', 'approved')->where('start_date', '>=', now())->where('start_date', '<=', now()->addDays(7))->count();
            $data['totalDepartments'] = Department::count();
            $data['leaveTypesCount'] = LeaveType::where('status', 'active')->count();
            $data['newStaffThisMonth'] = User::where('staff_category', 'administrative')->whereMonth('created_at', now()->month)->count();
            $data['leaveIncrease'] = 0;
            $data['departmentLeaveStats'] = [];
            $data['lowBalanceStaff'] = [];
            $data['recentApprovals'] = [];
            $data['leaveTypeStats'] = [];
            $data['myRecentRequests'] = [];
            
            // DVC-PFA STATS LINKS
            $data['statsLinks'] = [
                'total_staff' => route('staff.index', ['category' => 'administrative']),
                'pending_approvals' => route('leave-requests.pending'),
                'leave_used' => null,
                'low_balance' => route('staff.index', ['filter' => 'low_balance', 'category' => 'administrative']),
                'upcoming_leave' => null,
            ];
            
        } elseif ($role == 'hr-officer' || $role == 'hr-manager') {
            // HR dashboard
            $data['pendingLogisticsCount'] = LeaveRequest::where('status', 'approved')
                ->where('current_approval_level', 'hr_final')->count();
            
            $data['pendingApprovalsCount'] = LeaveRequest::where('status', 'pending')
                ->where('acting_status', 'accepted')
                ->whereIn('current_approval_level', ['hr_approval', 'hr_final'])
                ->count();
            
            $data['pendingApprovals'] = LeaveRequest::with(['staff', 'leaveType'])
                ->where('status', 'pending')
                ->where('acting_status', 'accepted')
                ->whereIn('current_approval_level', ['hr_approval', 'hr_final'])
                ->latest()->limit(5)->get();
            
            // For stat cards
            $data['totalStaff'] = User::count();
            $data['leaveUsed'] = LeaveRequest::where('status', 'approved')->sum('days');
            $data['lowBalanceCount'] = User::whereHas('leaveBalances', function($q) {
                $q->where('leave_type_id', 1)->where('balance', '<', 5)->where('balance', '>', 0);
            })->count();
            $data['totalDepartments'] = Department::count();
            $data['leaveTypesCount'] = LeaveType::where('status', 'active')->count();
            $data['newStaffThisMonth'] = User::whereMonth('created_at', now()->month)->count();
            $data['leaveIncrease'] = 0;
            $data['myRecentRequests'] = [];
            
            // Department stats for chart
            $departmentLeaveStats = Department::withCount(['staff as request_count' => function($q) {
                $q->whereHas('leaveRequests', function($sub) {
                    $sub->where('status', 'pending');
                });
            }])->get();
            
            $maxRequests = $departmentLeaveStats->max('request_count') ?: 1;
            foreach ($departmentLeaveStats as $dept) {
                $dept->percentage = round(($dept->request_count / $maxRequests) * 100);
            }
            $data['departmentLeaveStats'] = $departmentLeaveStats;
            
            // Low balance staff list
            $data['lowBalanceStaff'] = User::whereHas('leaveBalances', function($q) {
                $q->where('leave_type_id', 1)->where('balance', '<', 5)->where('balance', '>', 0);
            })->with('leaveBalances')->limit(5)->get();
            
            // Leave type distribution
            $leaveTypeStats = LeaveType::withCount(['leaveRequests as request_count' => function($q) {
                $q->whereYear('created_at', now()->year);
            }])->get();
            
            $maxTypeRequests = $leaveTypeStats->max('request_count') ?: 1;
            foreach ($leaveTypeStats as $type) {
                $type->percentage = round(($type->request_count / $maxTypeRequests) * 100);
            }
            $data['leaveTypeStats'] = $leaveTypeStats;
            
            $data['recentApprovals'] = LeaveRequest::with(['staff', 'leaveType'])
                ->where('status', '!=', 'pending')
                ->latest()
                ->limit(5)
                ->get();
            
            // HR STATS LINKS
            $data['statsLinks'] = [
                'total_staff' => route('staff.index'),
                'pending_approvals' => route('leave-requests.pending'),
                'leave_used' => null,
                'low_balance' => route('staff.index', ['filter' => 'low_balance']),
                'total_departments' => route('admin.departments.index'),
                'leave_types' => route('admin.leave-types.index'),
                'new_staff' => route('staff.create'),
                'pending_logistics' => route('leave-requests.pending'),
            ];
                
        } elseif ($role == 'admin') {
            // Admin dashboard - full access
            $data['totalStaff'] = User::count();
            $data['pendingApprovalsCount'] = LeaveRequest::where('status', 'pending')->where('acting_status', 'accepted')->count();
            $data['leaveTypesCount'] = LeaveType::where('status', 'active')->count();
            $data['totalDepartments'] = Department::count();
            $data['leaveUsed'] = LeaveRequest::where('status', 'approved')->sum('days');
            $data['lowBalanceCount'] = User::whereHas('leaveBalances', function($q) {
                $q->where('leave_type_id', 1)->where('balance', '<', 5)->where('balance', '>', 0);
            })->count();
            $data['newStaffThisMonth'] = User::whereMonth('created_at', now()->month)->count();
            
            // Calculate leave increase percentage
            $lastMonthStart = now()->subMonth()->startOfMonth();
            $lastMonthEnd = now()->subMonth()->endOfMonth();
            $lastMonthUsed = LeaveRequest::where('status', 'approved')
                ->whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])
                ->sum('days');
            $currentMonthUsed = LeaveRequest::where('status', 'approved')
                ->whereMonth('created_at', now()->month)
                ->sum('days');
            $data['leaveIncrease'] = $currentMonthUsed - $lastMonthUsed;
            
            $data['pendingApprovals'] = LeaveRequest::with(['staff', 'leaveType'])
                ->where('status', 'pending')->where('acting_status', 'accepted')
                ->latest()->limit(5)->get();
            
            // Department stats for chart
            $departmentLeaveStats = Department::withCount(['staff as request_count' => function($q) {
                $q->whereHas('leaveRequests', function($sub) {
                    $sub->where('status', 'pending');
                });
            }])->get();
            
            $maxRequests = $departmentLeaveStats->max('request_count') ?: 1;
            foreach ($departmentLeaveStats as $dept) {
                $dept->percentage = round(($dept->request_count / $maxRequests) * 100);
            }
            $data['departmentLeaveStats'] = $departmentLeaveStats;
            
            // Low balance staff list
            $data['lowBalanceStaff'] = User::whereHas('leaveBalances', function($q) {
                $q->where('leave_type_id', 1)->where('balance', '<', 5)->where('balance', '>', 0);
            })->with('leaveBalances')->limit(5)->get();
            
            // Leave type distribution
            $leaveTypeStats = LeaveType::withCount(['leaveRequests as request_count' => function($q) {
                $q->whereYear('created_at', now()->year);
            }])->get();
            
            $maxTypeRequests = $leaveTypeStats->max('request_count') ?: 1;
            foreach ($leaveTypeStats as $type) {
                $type->percentage = round(($type->request_count / $maxTypeRequests) * 100);
            }
            $data['leaveTypeStats'] = $leaveTypeStats;
            
            $data['recentApprovals'] = LeaveRequest::with(['staff', 'leaveType'])
                ->where('status', '!=', 'pending')
                ->latest()
                ->limit(5)
                ->get();
                
            $data['myRecentRequests'] = [];
            
            // ADMIN STATS LINKS
            $data['statsLinks'] = [
                'total_staff' => route('staff.index'),
                'pending_approvals' => route('leave-requests.pending'),
                'leave_types' => route('admin.leave-types.index'),
                'total_departments' => route('admin.departments.index'),
                'leave_used' => null,
                'low_balance' => route('staff.index', ['filter' => 'low_balance']),
                'new_staff' => route('staff.create'),
            ];
        }
        
        // Add acting requests for the current user
        $data['actingRequests'] = LeaveRequest::where('acting_staff_id', $user->id)
            ->where('acting_status', 'pending')
            ->get();
        
        // Recent leave requests for the main table (role-aware)
        $data['recentRequests'] = $this->getRecentLeaveRequests($user, 5);
        
        return view('shared.dashboard', $data);
    }
    
    /**
     * Get recent leave requests based on user role
     */
    private function getRecentLeaveRequests($user, $limit = 5)
    {
        $role = $user->getRoleNames()->first();
        
        $query = LeaveRequest::with(['staff', 'leaveType']);
        
        switch ($role) {
            case 'staff':
                $query->where('staff_id', $user->id);
                break;
                
            case 'unit_head':
                $unitHeadDept = $user->unitHeadDepartment;
                if ($unitHeadDept) {
                    $query->whereHas('staff', function($q) use ($unitHeadDept) {
                        $q->where('department_id', $unitHeadDept->id);
                    });
                } else {
                    $query->whereRaw('1 = 0');
                }
                break;
                
            case 'hod':
                $query->whereHas('staff.department', function($q) use ($user) {
                    $q->where('hod_staff_id', $user->id);
                });
                break;
                
            case 'dean':
                $query->whereHas('staff.department.school', function($q) use ($user) {
                    $q->where('dean_staff_id', $user->id);
                });
                break;
                
            case 'dvca':
                $query->whereHas('staff', function($q) {
                    $q->where('staff_category', 'academic');
                });
                break;
                
            case 'dvc-pfa':
                $query->whereHas('staff', function($q) {
                    $q->where('staff_category', 'administrative');
                });
                break;
                
            case 'hr-officer':
            case 'hr-manager':
            case 'admin':
                // No filter - see all
                break;
                
            default:
                $query->where('staff_id', $user->id);
                break;
        }
        
        return $query->latest()->limit($limit)->get();
    }
    
    /**
     * Get real-time dashboard stats (for AJAX refresh)
     */
    public function getStats(Request $request)
    {
        $user = auth()->user();
        $role = $user->getRoleNames()->first();
        
        $stats = [];
        
        // Common stats
        $stats['onLeaveToday'] = LeaveRequest::where('status', 'approved')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->count();
        
        $stats['upcomingLeave'] = LeaveRequest::where('status', 'approved')
            ->where('start_date', '>=', now())
            ->where('start_date', '<=', now()->addDays(7))
            ->count();
        
        // Role-specific stats
        switch ($role) {
            case 'staff':
                $stats['myPendingRequests'] = LeaveRequest::where('staff_id', $user->id)
                    ->where('status', 'pending')
                    ->count();
                $stats['pendingApprovalsCount'] = $stats['myPendingRequests'];
                $stats['totalStaff'] = 0;
                $stats['leaveUsed'] = LeaveRequest::where('staff_id', $user->id)
                    ->where('status', 'approved')
                    ->sum('days');
                break;
                
            case 'unit_head':
                $unitHeadDept = $user->unitHeadDepartment;
                if ($unitHeadDept) {
                    $stats['pendingApprovalsCount'] = LeaveRequest::whereHas('staff', function($q) use ($unitHeadDept) {
                        $q->where('department_id', $unitHeadDept->id);
                    })->where('status', 'pending')
                      ->where('acting_status', 'accepted')
                      ->where('current_approval_level', 'unit_head')
                      ->count();
                    $stats['totalStaff'] = User::where('department_id', $unitHeadDept->id)->count();
                } else {
                    $stats['pendingApprovalsCount'] = 0;
                    $stats['totalStaff'] = 0;
                }
                break;
                
            case 'hod':
                $stats['pendingApprovalsCount'] = LeaveRequest::whereHas('staff.department', function($q) use ($user) {
                    $q->where('hod_staff_id', $user->id);
                })->where('status', 'pending')->where('acting_status', 'accepted')->where('current_approval_level', 'hod')->count();
                $stats['totalStaff'] = User::whereHas('department', function($q) use ($user) {
                    $q->where('hod_staff_id', $user->id);
                })->count();
                break;
                
            case 'dean':
                $stats['pendingApprovalsCount'] = LeaveRequest::whereHas('staff.department.school', function($q) use ($user) {
                    $q->where('dean_staff_id', $user->id);
                })->where('status', 'pending')->where('acting_status', 'accepted')->where('current_approval_level', 'dean')->count();
                $stats['totalStaff'] = User::whereHas('department.school', function($q) use ($user) {
                    $q->where('dean_staff_id', $user->id);
                })->count();
                break;
                
            case 'dvca':
                $stats['pendingApprovalsCount'] = LeaveRequest::whereHas('staff', function($q) {
                    $q->where('staff_category', 'academic');
                })->where('status', 'pending')->where('acting_status', 'accepted')->where('current_approval_level', 'dvc')->count();
                $stats['totalStaff'] = User::where('staff_category', 'academic')->count();
                break;
                
            case 'dvc-pfa':
                $stats['pendingApprovalsCount'] = LeaveRequest::whereHas('staff', function($q) {
                    $q->where('staff_category', 'administrative');
                })->where('status', 'pending')->where('acting_status', 'accepted')->where('current_approval_level', 'dvc')->count();
                $stats['totalStaff'] = User::where('staff_category', 'administrative')->count();
                break;
                
            case 'hr-officer':
            case 'hr-manager':
                $stats['pendingApprovalsCount'] = LeaveRequest::where('status', 'pending')
                    ->where('acting_status', 'accepted')
                    ->whereIn('current_approval_level', ['hr_approval', 'hr_final'])
                    ->count();
                $stats['totalStaff'] = User::count();
                break;
                
            case 'admin':
                $stats['pendingApprovalsCount'] = LeaveRequest::where('status', 'pending')
                    ->where('acting_status', 'accepted')
                    ->count();
                $stats['totalStaff'] = User::count();
                $stats['leaveUsed'] = LeaveRequest::where('status', 'approved')->sum('days');
                $stats['lowBalanceCount'] = User::whereHas('leaveBalances', function($q) {
                    $q->where('leave_type_id', 1)->where('balance', '<', 5)->where('balance', '>', 0);
                })->count();
                break;
        }
        
        $stats['lastUpdated'] = now()->format('H:i:s');
        
        return response()->json($stats);
    }
}