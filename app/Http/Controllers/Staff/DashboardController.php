<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\BlackoutPeriod;
use App\Models\LeaveBalance;
use App\Models\LeaveRequest;

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

        $leaveContext = [
            'university_id' => $user->university_id,
            'staff_category' => $user->staff_category,
            'contract_type' => $user->contract_type,
        ];

        // Annual Leave (LC1)
        $annualLeaveBalance = $balances['LC1']->balance ?? 0;
        // Defaults for these come from the policy catalog (config/policy_catalog.php).
        $annualLeaveTotal = policy_value('max_annual_leave', null, $leaveContext, 'hr_policy');
        $annualLeaveUsed = max(0, $annualLeaveTotal - $annualLeaveBalance);
        $annualLeavePercentage = $annualLeaveTotal > 0 ? ($annualLeaveUsed / $annualLeaveTotal) * 100 : 0;

        // Sick Leave (LC2)
        $sickLeaveTotal = policy_value('max_sick_leave', null, $leaveContext, 'hr_policy');
        $sickLeaveBalance = $balances['LC2']->balance ?? $sickLeaveTotal;
        $sickLeaveUsed = max(0, $sickLeaveTotal - $sickLeaveBalance);
        $sickLeavePercentage = $sickLeaveTotal > 0 ? ($sickLeaveUsed / $sickLeaveTotal) * 100 : 0;

        // Compassionate Leave (LC3)
        $compassionateTotal = policy_value('max_compassionate_leave', null, $leaveContext, 'hr_policy');
        $compassionateBalance = $balances['LC3']->balance ?? $compassionateTotal;
        $compassionatePercentage = $compassionateTotal > 0 ? (($compassionateTotal - $compassionateBalance) / $compassionateTotal) * 100 : 0;

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
