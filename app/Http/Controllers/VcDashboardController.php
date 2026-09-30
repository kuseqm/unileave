<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class VcDashboardController extends Controller
{
    /**
     * Display Vice Chancellor Dashboard
     */
    public function index(Request $request)
    {
        // Get filter values
        $dateRange = $request->get('date_range', 'this_year');
        $departmentId = $request->get('department_id');
        $leaveTypeId = $request->get('leave_type_id');

        // Get current year and previous year for comparison
        $currentYear = date('Y');
        $previousYear = $currentYear - 1;

        // Build base query with filters
        $baseQuery = LeaveRequest::query();
        $this->applyFilters($baseQuery, $dateRange, $departmentId, $leaveTypeId);

        // ============================================
        // STATS CARDS
        // ============================================

        // Total Leave Requests (filtered)
        $totalRequests = $baseQuery->count();

        // Active Staff on Leave Today (filtered)
        $activeOnLeave = (clone $baseQuery)
            ->where('status', 'approved')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->count();

        // Pending Approvals (filtered)
        $pendingApprovals = (clone $baseQuery)
            ->where('status', 'pending')
            ->where('acting_status', 'accepted')
            ->count();

        // Pending by Level
        $pendingByLevel = [
            'hod' => (clone $baseQuery)->where('status', 'pending')->where('acting_status', 'accepted')->where('current_approval_level', 1)->count(),
            'dean' => (clone $baseQuery)->where('status', 'pending')->where('acting_status', 'accepted')->where('current_approval_level', 2)->count(),
            'hr' => (clone $baseQuery)->where('status', 'pending')->where('acting_status', 'accepted')->where('current_approval_level', 3)->count(),
            'dvc' => (clone $baseQuery)->where('status', 'pending')->where('acting_status', 'accepted')->where('current_approval_level', 4)->count(),
            'hr_final' => (clone $baseQuery)->where('status', 'pending')->where('acting_status', 'accepted')->where('current_approval_level', 5)->count(),
        ];

        // Average Approval Time
        $avgApprovalTime = $this->calculateAverageApprovalTime($baseQuery);

        // ============================================
        // CHARTS DATA
        // ============================================

        // Monthly Trends (Last 12 months)
        $monthlyTrends = $this->getMonthlyTrends($dateRange, $departmentId, $leaveTypeId);

        // Department Comparison (Top 10)
        $departmentCompare = $this->getDepartmentComparison($currentYear, $previousYear, $departmentId, $leaveTypeId);

        // Leave by Month (Heatmap style)
        $monthlyHeatmap = $this->getMonthlyHeatmap($currentYear, $departmentId, $leaveTypeId);

        // ============================================
        // FILTERS DATA
        // ============================================

        // Get departments for filter dropdown
        $departments = Department::whereHas('staff.leaveRequests')->get();
        $leaveTypes = LeaveType::whereHas('leaveRequests')->get();

        // ============================================
        // STATS BY CATEGORY
        // ============================================

        // Leave by Staff Category
        $categoryStats = [
            'academic' => (clone $baseQuery)->whereHas('staff', function ($q) {
                $q->where('staff_category', 'academic');
            })->count(),
            'administrative' => (clone $baseQuery)->whereHas('staff', function ($q) {
                $q->where('staff_category', 'administrative');
            })->count(),
            'supporting' => (clone $baseQuery)->whereHas('staff', function ($q) {
                $q->where('staff_category', 'supporting');
            })->count(),
        ];

        // Payment Status Summary
        $paymentStats = [
            'paid' => (clone $baseQuery)->where('payment_status', 'paid')->count(),
            'partial' => (clone $baseQuery)->where('payment_status', 'partial')->count(),
            'unpaid' => (clone $baseQuery)->where('payment_status', 'unpaid')->count(),
        ];

        $totalWithPayment = array_sum($paymentStats);
        $paymentStatsPercentage = [
            'paid' => $totalWithPayment > 0 ? round(($paymentStats['paid'] / $totalWithPayment) * 100) : 0,
            'partial' => $totalWithPayment > 0 ? round(($paymentStats['partial'] / $totalWithPayment) * 100) : 0,
            'unpaid' => $totalWithPayment > 0 ? round(($paymentStats['unpaid'] / $totalWithPayment) * 100) : 0,
        ];

        // Top Leave Types
        $topLeaveTypes = LeaveType::withCount(['leaveRequests' => function ($q) use ($dateRange, $departmentId, $leaveTypeId) {
            $this->applyFilters($q, $dateRange, $departmentId, $leaveTypeId);
        }])->orderBy('leave_requests_count', 'desc')->limit(5)->get();

        // Recent Approvals
        $recentApprovals = (clone $baseQuery)
            ->with(['staff', 'leaveType'])
            ->where('status', 'approved')
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();

        // Alerts
        $alerts = $this->getAlerts($baseQuery);

        // Monthly Projection
        $projection = $this->getMonthlyProjection();

        // Current filter values for view
        $currentFilters = [
            'date_range' => $dateRange,
            'department_id' => $departmentId,
            'leave_type_id' => $leaveTypeId,
        ];

        return view('vc.dashboard', compact(
            'totalRequests',
            'activeOnLeave',
            'pendingApprovals',
            'pendingByLevel',
            'avgApprovalTime',
            'monthlyTrends',
            'departmentCompare',
            'monthlyHeatmap',
            'categoryStats',
            'paymentStats',
            'paymentStatsPercentage',
            'topLeaveTypes',
            'recentApprovals',
            'alerts',
            'projection',
            'departments',
            'leaveTypes',
            'currentFilters',
            'currentYear',
            'previousYear'
        ));
    }

    /**
     * Export report as Excel
     */
    public function exportExcel(Request $request)
    {
        $dateRange = $request->get('date_range', 'this_year');
        $departmentId = $request->get('department_id');
        $leaveTypeId = $request->get('leave_type_id');

        $query = LeaveRequest::with(['staff', 'leaveType', 'staff.department']);
        $this->applyFilters($query, $dateRange, $departmentId, $leaveTypeId);

        $requests = $query->orderBy('created_at', 'desc')->get();

        // Prepare data for export
        $data = [];
        foreach ($requests as $request) {
            $data[] = [
                'Request ID' => $request->id,
                'Staff Name' => $request->staff->full_name ?? 'N/A',
                'Staff ID' => $request->staff->staff_id ?? 'N/A',
                'Department' => $request->staff->department->name ?? 'N/A',
                'Leave Type' => $request->leaveType->name ?? 'N/A',
                'Start Date' => $request->start_date,
                'End Date' => $request->end_date,
                'Days' => $request->days,
                'Status' => ucfirst($request->status),
                'Payment Status' => ucfirst($request->payment_status ?? 'paid'),
                'Created At' => $request->created_at,
                'Approved At' => $request->updated_at,
            ];
        }

        // Export as CSV
        $fileName = 'leave-report-'.date('Y-m-d-His').'.csv';
        $handle = fopen('php://temp', 'w+');

        // Add headers
        fputcsv($handle, array_keys($data[0] ?? []));

        // Add data
        foreach ($data as $row) {
            fputcsv($handle, $row);
        }

        rewind($handle);
        $csvContent = stream_get_contents($handle);
        fclose($handle);

        return response($csvContent, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.$fileName.'"',
        ]);
    }

    /**
     * Export report as PDF
     */
    public function exportPdf(Request $request)
    {
        $dateRange = $request->get('date_range', 'this_year');
        $departmentId = $request->get('department_id');
        $leaveTypeId = $request->get('leave_type_id');

        $query = LeaveRequest::with(['staff', 'leaveType', 'staff.department']);
        $this->applyFilters($query, $dateRange, $departmentId, $leaveTypeId);

        $requests = $query->orderBy('created_at', 'desc')->get();

        $summary = [
            'total' => $requests->count(),
            'approved' => $requests->where('status', 'approved')->count(),
            'pending' => $requests->where('status', 'pending')->count(),
            'rejected' => $requests->where('status', 'rejected')->count(),
            'total_days' => $requests->sum('days'),
        ];

        $pdf = Pdf::loadView('vc.reports.pdf', compact('requests', 'summary'));

        return $pdf->download('leave-report-'.date('Y-m-d-His').'.pdf');
    }

    /**
     * Apply filters to query
     */
    private function applyFilters($query, $dateRange, $departmentId, $leaveTypeId)
    {
        // Date range filter
        if ($dateRange) {
            switch ($dateRange) {
                case 'this_month':
                    $query->whereYear('created_at', date('Y'))->whereMonth('created_at', date('m'));
                    break;
                case 'last_month':
                    $query->whereYear('created_at', date('Y'))->whereMonth('created_at', date('m') - 1);
                    break;
                case 'this_year':
                    $query->whereYear('created_at', date('Y'));
                    break;
                case 'last_year':
                    $query->whereYear('created_at', date('Y') - 1);
                    break;
                case 'all':
                default:
                    // No date filter
                    break;
            }
        }

        // Department filter
        if ($departmentId) {
            $query->whereHas('staff', function ($q) use ($departmentId) {
                $q->where('department_id', $departmentId);
            });
        }

        // Leave type filter
        if ($leaveTypeId) {
            $query->where('leave_type_id', $leaveTypeId);
        }
    }

    /**
     * Calculate average approval time
     */
    private function calculateAverageApprovalTime($baseQuery)
    {
        $approvedRequests = (clone $baseQuery)
            ->where('status', 'approved')
            ->whereNotNull('created_at')
            ->whereNotNull('updated_at')
            ->get();

        if ($approvedRequests->isEmpty()) {
            return 0;
        }

        $totalDays = 0;
        foreach ($approvedRequests as $request) {
            $totalDays += $request->created_at->diffInDays($request->updated_at);
        }

        return round($totalDays / $approvedRequests->count(), 1);
    }

    /**
     * Get monthly trends for chart
     */
    private function getMonthlyTrends($dateRange, $departmentId, $leaveTypeId)
    {
        $trends = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $month = $date->format('M Y');
            $year = $date->format('Y');
            $monthNum = $date->format('m');

            $query = LeaveRequest::whereYear('created_at', $year)->whereMonth('created_at', $monthNum);
            $this->applyFilters($query, $dateRange, $departmentId, $leaveTypeId);

            $trends[] = [
                'month' => $month,
                'total' => (clone $query)->count(),
                'approved' => (clone $query)->where('status', 'approved')->count(),
                'rejected' => (clone $query)->where('status', 'rejected')->count(),
                'pending' => (clone $query)->where('status', 'pending')->count(),
            ];
        }

        return $trends;
    }

    /**
     * Get department comparison for chart
     */
    private function getDepartmentComparison($currentYear, $previousYear, $departmentId, $leaveTypeId)
    {
        $departments = Department::has('staff.leaveRequests')->limit(10)->get();
        $comparison = [];

        foreach ($departments as $dept) {
            if ($departmentId && $dept->id != $departmentId) {
                continue;
            }

            $currentQuery = LeaveRequest::whereHas('staff', function ($q) use ($dept) {
                $q->where('department_id', $dept->id);
            })->whereYear('created_at', $currentYear);

            $previousQuery = LeaveRequest::whereHas('staff', function ($q) use ($dept) {
                $q->where('department_id', $dept->id);
            })->whereYear('created_at', $previousYear);

            $this->applyFilters($currentQuery, 'this_year', null, $leaveTypeId);
            $this->applyFilters($previousQuery, 'last_year', null, $leaveTypeId);

            $current = $currentQuery->count();
            $previous = $previousQuery->count();

            $change = $previous > 0 ? round((($current - $previous) / $previous) * 100, 1) : ($current > 0 ? 100 : 0);

            $comparison[] = [
                'name' => $dept->name,
                'current' => $current,
                'previous' => $previous,
                'change' => $change,
            ];
        }

        return $comparison;
    }

    /**
     * Get monthly heatmap data
     */
    private function getMonthlyHeatmap($year, $departmentId, $leaveTypeId)
    {
        $heatmap = [];

        for ($month = 1; $month <= 12; $month++) {
            $query = LeaveRequest::whereYear('created_at', $year)->whereMonth('created_at', $month);
            $this->applyFilters($query, 'this_year', $departmentId, $leaveTypeId);

            $heatmap[] = [
                'month' => date('F', mktime(0, 0, 0, $month, 1)),
                'count' => $query->count(),
            ];
        }

        return $heatmap;
    }

    /**
     * Get alerts for VC
     */
    private function getAlerts($baseQuery)
    {
        $alerts = [];

        // Stale pending requests
        $staleRequests = (clone $baseQuery)
            ->where('status', 'pending')
            ->where('created_at', '<', now()->subDays(5))
            ->count();

        if ($staleRequests > 0) {
            $alerts[] = [
                'type' => 'danger',
                'icon' => 'ti ti-alert-triangle',
                'title' => 'Stale Pending Requests',
                'message' => "{$staleRequests} leave request(s) have been pending for over 5 days (exceeds SLA).",
                'action_url' => route('leave-requests.pending'),
                'action_text' => 'Review Now',
            ];
        }

        // High unpaid leave percentage
        $totalWithPayment = (clone $baseQuery)->whereIn('payment_status', ['paid', 'partial', 'unpaid'])->count();
        $unpaidCount = (clone $baseQuery)->where('payment_status', 'unpaid')->count();
        $unpaidPercentage = $totalWithPayment > 0 ? round(($unpaidCount / $totalWithPayment) * 100) : 0;

        if ($unpaidPercentage > 10) {
            $alerts[] = [
                'type' => 'warning',
                'icon' => 'ti ti-currency-dollar',
                'title' => 'High Unpaid Leave Rate',
                'message' => "{$unpaidPercentage}% of leave requests are unpaid. Review if this aligns with policy.",
                'action_url' => route('reports.index'),
                'action_text' => 'View Reports',
            ];
        }

        return $alerts;
    }

    /**
     * Get monthly projection based on historical data
     */
    private function getMonthlyProjection()
    {
        $projections = [];

        for ($i = 1; $i <= 3; $i++) {
            $date = now()->addMonths($i);
            $month = $date->format('M Y');
            $year = $date->format('Y');
            $monthNum = $date->format('m');

            // Get average from last 2 years for this month
            $avgCount = LeaveRequest::whereMonth('created_at', $monthNum)
                ->whereYear('created_at', '>=', $year - 2)
                ->count() / 2;

            $projections[] = [
                'month' => $month,
                'projected' => round($avgCount),
                'confidence' => 'medium',
            ];
        }

        return $projections;
    }
}
