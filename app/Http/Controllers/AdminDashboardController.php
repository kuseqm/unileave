<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use App\Models\User;

class AdminDashboardController extends Controller
{
    public function getStats()
    {
        if (! auth()->user()->hasRole('admin')) {
            return response()->json(['error' => 'Access denied'], 403);
        }

        return response()->json([
            'total_staff' => User::count(),
            'pending_leave' => LeaveRequest::where('status', 'pending')->count(),
            'on_leave' => LeaveRequest::where('status', 'approved')
                ->where('start_date', '<=', now())
                ->where('end_date', '>=', now())
                ->count(),
        ]);
    }
}
