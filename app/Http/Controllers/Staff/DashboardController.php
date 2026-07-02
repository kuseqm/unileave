<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\LeaveRequest;
use App\Models\LeaveBalance;
use App\Models\BlackoutPeriod;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Get leave balances
        $balances = LeaveBalance::with('leaveType')
            ->where('staff_id', $user->id)
            ->where('year', date('Y'))
            ->get()
            ->keyBy(function ($item) {
                return $item->leaveType->code;
            });
        
        // Annual Leave (LC1)
        $annualLeaveBalance = $balances['LC1']->balance ?? 0;
        $annualLeaveTotal = 28;
        $annualLeaveUsed = $annualLeaveTotal - $annualLeaveBalance;
        $annualLeavePercentage = ($annualLeaveUsed / $annualLeaveTotal) * 100;
        
        // Sick Leave (LC2)
        $sickLeaveBalance = $balances['LC2']->balance ?? 12;
        $sickLeaveTotal = 12;
        $sickLeaveUsed = $sickLeaveTotal - $sickLeaveBalance;
        $sickLeavePercentage = ($sickLeaveUsed / $sickLeaveTotal) * 100;
        
        // Compassionate Leave (LC3)
        $compassionateBalance = $balances['LC3']->balance ?? 3;
        $compassionateTotal = 3;
        $compassionatePercentage = (($compassionateTotal - $compassionateBalance) / $compassionateTotal) * 100;
        
        // Pending requests
        $pendingRequests = LeaveRequest::where('staff_id', $user->id)
            ->where('status', 'pending')
            ->count();
        
        // Approved this year
        $approvedThisYear = LeaveRequest::where('staff_id', $user->id)
            ->where('status', 'approved')
            ->whereYear('created_at', date('Y'))
            ->sum('days');
        
        // Recent leave requests
        $recentRequests = LeaveRequest::with('leaveType')
            ->where('staff_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        // Upcoming blackouts
        $upcomingBlackouts = BlackoutPeriod::where('end_date', '>=', now())
            ->orderBy('start_date')
            ->get();
        
        return view('staff.dashboard', compact(
            'annualLeaveBalance',
            'annualLeaveTotal',
            'annualLeaveUsed',
            'annualLeavePercentage',
            'sickLeaveBalance',
            'sickLeaveTotal',
            'sickLeaveUsed',
            'sickLeavePercentage',
            'compassionateBalance',
            'compassionateTotal',
            'compassionatePercentage',
            'pendingRequests',
            'approvedThisYear',
            'recentRequests',
            'upcomingBlackouts'
        ));
    }
}