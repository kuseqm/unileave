<?php

namespace App\Services;

use App\Models\AcademicYear;
use App\Models\Department;
use App\Models\LeaveBalance;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\PermissionRequest;
use App\Models\TravelPermission;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ReportService
{
    /**
     * Main dispatcher: calls the appropriate method based on report name.
     */
    public function getData($definition, $filters = [])
    {
        $method = 'get'.str_replace(' ', '', ucwords(str_replace('_', ' ', $definition->name)));
        if (method_exists($this, $method)) {
            return $this->$method($filters);
        }

        // Fallback: return empty data
        return ['rows' => collect(), 'chart' => null];
    }

    /**
     * Whitelisted data sources for the custom report builder: which
     * columns a user can choose to include, which generic filters apply,
     * and the status options to offer for that source. Kept as a single
     * source of truth so the builder form and getCustomReport() below
     * can never drift apart.
     *
     * @return array<string, array{label: string, columns: array<string, string>, filters: array<int, string>, statuses: array<int, string>}>
     */
    public static function customSources(): array
    {
        return [
            'leave_requests' => [
                'label' => 'Leave Requests',
                'columns' => [
                    'staff_name' => 'Staff Name',
                    'department' => 'Department',
                    'leave_type' => 'Leave Type',
                    'start_date' => 'Start Date',
                    'end_date' => 'End Date',
                    'days' => 'Days',
                    'status' => 'Status',
                ],
                'filters' => ['department_id', 'leave_type_id', 'status', 'date_from', 'date_to'],
                'statuses' => ['pending', 'approved', 'rejected', 'cancelled'],
            ],
            'staff_directory' => [
                'label' => 'Staff Directory',
                'columns' => [
                    'staff_id' => 'Staff ID',
                    'name' => 'Name',
                    'email' => 'Email',
                    'phone' => 'Phone',
                    'department' => 'Department',
                    'status' => 'Status',
                    'joining_date' => 'Joining Date',
                ],
                'filters' => ['department_id', 'status'],
                'statuses' => ['active', 'inactive'],
            ],
            'short_permissions' => [
                'label' => 'Short Permission Requests',
                'columns' => [
                    'staff_name' => 'Staff Name',
                    'department' => 'Department',
                    'duration_hours' => 'Duration (Hours)',
                    'status' => 'Status',
                ],
                'filters' => ['department_id', 'status', 'date_from', 'date_to'],
                'statuses' => ['pending', 'approved', 'rejected'],
            ],
            'travel_permissions' => [
                'label' => 'Travel Permissions',
                'columns' => [
                    'staff_name' => 'Staff Name',
                    'department' => 'Department',
                    'destination' => 'Destination',
                    'start_date' => 'Start Date',
                    'end_date' => 'End Date',
                    'purpose' => 'Purpose',
                    'status' => 'Status',
                ],
                'filters' => ['department_id', 'status', 'date_from', 'date_to'],
                'statuses' => ['pending', 'approved', 'rejected'],
            ],
        ];
    }

    /**
     * Build a report from a user-chosen source, column selection and
     * filter combination, for the custom report builder. $columns is
     * always intersected against the source's whitelist below, so a
     * tampered request can never pull in a field outside what that
     * source already exposes.
     */
    public function getCustomReport(string $source, array $columns, array $filters): array
    {
        $sources = self::customSources();
        if (! isset($sources[$source])) {
            return ['rows' => collect(), 'chart' => null];
        }

        $allColumns = array_keys($sources[$source]['columns']);
        $selectedColumns = array_values(array_intersect($allColumns, $columns));
        if (empty($selectedColumns)) {
            $selectedColumns = $allColumns;
        }

        $rows = match ($source) {
            'leave_requests' => $this->getLeaveSummary($filters)['rows'],
            'staff_directory' => $this->getStaffDirectory($filters)['rows'],
            'short_permissions' => $this->getShortPermissionSummary($filters)['rows'],
            'travel_permissions' => $this->getTravelPermissionSummary($filters)['rows'],
            default => collect(),
        };

        $data = $rows->map(fn ($row) => collect($row)->only($selectedColumns)->all());

        return ['rows' => $data, 'chart' => null];
    }

    /**
     * 1. Leave Summary Report
     */
    protected function getLeaveSummary($filters)
    {
        $query = LeaveRequest::with(['staff.department', 'leaveType'])
            ->when($filters['department_id'] ?? null, function ($q, $deptId) {
                return $q->whereHas('staff', fn ($sq) => $sq->where('department_id', $deptId));
            })
            ->when($filters['leave_type_id'] ?? null, function ($q, $typeId) {
                return $q->where('leave_type_id', $typeId);
            })
            ->when($filters['status'] ?? null, function ($q, $status) {
                return $q->where('status', $status);
            })
            ->when($filters['date_from'] ?? null, function ($q, $from) {
                return $q->whereDate('start_date', '>=', $from);
            })
            ->when($filters['date_to'] ?? null, function ($q, $to) {
                return $q->whereDate('end_date', '<=', $to);
            });

        $data = $query->get()->map(function ($leave) {
            return [
                'staff_name' => $leave->staff->full_name,
                'department' => $leave->staff->department->name ?? 'N/A',
                'leave_type' => $leave->leaveType->name ?? 'N/A',
                'start_date' => $leave->start_date->format('d M Y'),
                'end_date' => $leave->end_date->format('d M Y'),
                'days' => $leave->days,
                'status' => ucfirst($leave->status),
            ];
        });

        $chartData = $data->groupBy('department')->map(fn ($items) => $items->sum('days'));
        $chart = ['labels' => $chartData->keys(), 'series' => $chartData->values()];

        return ['rows' => $data, 'chart' => $chart];
    }

    /**
     * 2. Staff Directory Report
     */
    protected function getStaffDirectory($filters)
    {
        $query = User::with('department')
            ->when($filters['department_id'] ?? null, fn ($q, $deptId) => $q->where('department_id', $deptId))
            ->when($filters['status'] ?? null, fn ($q, $status) => $q->where('status', $status))
            ->when($filters['staff_category'] ?? null, fn ($q, $cat) => $q->where('staff_category', $cat));

        $data = $query->get()->map(function ($staff) {
            return [
                'staff_id' => $staff->staff_id,
                'name' => $staff->full_name,
                'email' => $staff->email,
                'phone' => $staff->phone,
                'department' => $staff->department->name ?? 'N/A',
                'status' => ucfirst($staff->status),
                'joining_date' => optional($staff->joining_date)->format('d M Y'),
            ];
        });

        return ['rows' => $data, 'chart' => null];
    }

    /**
     * 3. Leave Balance Report
     */
    protected function getLeaveBalance($filters)
    {
        // leave_balances.year is a plain calendar year (set by date('Y') when
        // a balance row is created/deducted - see LeaveRequestController::
        // updateLeaveBalance()), not the academic year's start year, so it
        // must be matched the same way here.
        $year = $filters['year'] ?? now()->year;
        $annualTypeId = LeaveType::where('name', 'like', '%Annual%')->value('id');
        $sickTypeId = LeaveType::where('name', 'like', '%Sick%')->value('id');
        $compassionateTypeId = LeaveType::where('name', 'like', '%Compassionate%')->value('id');

        $query = LeaveBalance::with(['staff.department', 'leaveType'])
            ->where('year', $year)
            ->when($filters['department_id'] ?? null, function ($q, $deptId) {
                return $q->whereHas('staff', fn ($sq) => $sq->where('department_id', $deptId));
            });

        $balances = $query->get()->groupBy('staff_id');
        $data = $balances->map(function ($items) use ($annualTypeId, $sickTypeId, $compassionateTypeId) {
            $staff = $items->first()->staff;
            $annual = $items->where('leave_type_id', $annualTypeId)->first()->balance ?? 0;
            $sick = $items->where('leave_type_id', $sickTypeId)->first()->balance ?? 0;
            $compassionate = $items->where('leave_type_id', $compassionateTypeId)->first()->balance ?? 0;

            return [
                'staff_name' => $staff->full_name,
                'department' => $staff->department->name ?? 'N/A',
                'annual_leave' => $annual,
                'sick_leave' => $sick,
                'compassionate_leave' => $compassionate,
                'remaining' => $annual + $sick + $compassionate,
            ];
        })->values();

        return ['rows' => $data, 'chart' => null];
    }

    /**
     * 4. Pending Approvals Report
     */
    protected function getPendingApprovals($filters)
    {
        $user = auth()->user();
        $role = $user->getRoleNames()->first();

        $query = LeaveRequest::with(['staff.department', 'leaveType'])
            ->where('status', 'pending')
            ->where('acting_status', 'accepted')
            ->when($filters['department_id'] ?? null, function ($q, $deptId) {
                return $q->whereHas('staff', fn ($sq) => $sq->where('department_id', $deptId));
            })
            ->when($filters['approval_level'] ?? null, function ($q, $level) {
                return $q->where('current_approval_level', $level);
            })
            ->when($filters['date_from'] ?? null, fn ($q, $from) => $q->whereDate('created_at', '>=', $from))
            ->when($filters['date_to'] ?? null, fn ($q, $to) => $q->whereDate('created_at', '<=', $to));

        if ($role == 'hod') {
            $query->whereHas('staff.department', fn ($q) => $q->where('hod_staff_id', $user->id));
        } elseif ($role == 'dean') {
            $query->whereHas('staff.department.school', fn ($q) => $q->where('dean_staff_id', $user->id));
        } elseif ($role == 'dvca') {
            $query->whereHas('staff', fn ($q) => $q->where('staff_category', 'academic'));
        } elseif ($role == 'dvc-pfa') {
            $query->whereHas('staff', fn ($q) => $q->where('staff_category', 'administrative'));
        }

        $data = $query->get()->map(function ($leave) {
            return [
                'staff_name' => $leave->staff->full_name,
                'department' => $leave->staff->department->name ?? 'N/A',
                'leave_type' => $leave->leaveType->name ?? 'N/A',
                'start_date' => $leave->start_date->format('d M Y'),
                'end_date' => $leave->end_date->format('d M Y'),
                'days' => $leave->days,
                'submitted_on' => $leave->created_at->format('d M Y'),
                'current_approver' => $leave->current_approval_level,
            ];
        });

        return ['rows' => $data, 'chart' => null];
    }

    /**
     * 5. Leave Taken Report
     */
    protected function getLeaveTaken($filters)
    {
        $startDate = $filters['date_from'] ?? $this->currentLeaveYearStartDate();
        $endDate = $filters['date_to'] ?? now()->format('Y-m-d');

        $query = LeaveRequest::with(['staff.department', 'leaveType'])
            ->where('status', 'approved')
            ->whereBetween('start_date', [$startDate, $endDate]);

        $this->applyRoleScope($query);

        $data = $query->get()->map(function ($leave) {
            return [
                'staff_name' => $leave->staff->full_name,
                'department' => $leave->staff->department->name ?? 'N/A',
                'leave_type' => $leave->leaveType->name ?? 'N/A',
                'start_date' => $leave->start_date->format('d M Y'),
                'end_date' => $leave->end_date->format('d M Y'),
                'days' => $leave->days,
            ];
        });

        $chartData = $data->groupBy('department')->map(fn ($items) => $items->sum('days'));
        $chart = ['labels' => $chartData->keys(), 'series' => $chartData->values()];

        return ['rows' => $data, 'chart' => $chart];
    }

    /**
     * 6. Leave Forecast Report
     */
    protected function getLeaveForecast($filters)
    {
        $next30Days = now()->addDays(30)->format('Y-m-d');
        $next60Days = now()->addDays(60)->format('Y-m-d');
        $next90Days = now()->addDays(90)->format('Y-m-d');

        $query = LeaveRequest::where('status', 'approved');
        $this->applyRoleScope($query);

        $upcoming30 = (clone $query)->whereBetween('start_date', [now(), $next30Days])->count();
        $upcoming60 = (clone $query)->whereBetween('start_date', [now()->addDays(31), $next60Days])->count();
        $upcoming90 = (clone $query)->whereBetween('start_date', [now()->addDays(61), $next90Days])->count();

        $data = collect([
            ['period' => 'Next 30 days', 'count' => $upcoming30],
            ['period' => 'Next 31-60 days', 'count' => $upcoming60],
            ['period' => 'Next 61-90 days', 'count' => $upcoming90],
        ]);

        $chart = ['labels' => $data->pluck('period'), 'series' => $data->pluck('count')];

        return ['rows' => $data, 'chart' => $chart];
    }

    /**
     * 7. Low Balance Alert Report
     */
    protected function getLowBalanceAlerts($filters)
    {
        $threshold = $filters['threshold'] ?? 5;
        // Same calendar-year convention as getLeaveBalance() above.
        $year = $filters['year'] ?? now()->year;
        $annualTypeId = LeaveType::where('name', 'like', '%Annual%')->value('id');
        $sickTypeId = LeaveType::where('name', 'like', '%Sick%')->value('id');
        $compassionateTypeId = LeaveType::where('name', 'like', '%Compassionate%')->value('id');

        $query = User::whereHas('leaveBalances', function ($q) use ($threshold, $year, $annualTypeId) {
            $q->where('leave_type_id', $annualTypeId)
                ->where('year', $year)
                ->where('balance', '<', $threshold)
                ->where('balance', '>', 0);
        });
        $this->applyRoleScopeToStaff($query);

        $staff = $query->with(['leaveBalances' => fn ($q) => $q->where('year', $year)])->get();

        $data = $staff->map(function ($staff) use ($annualTypeId, $sickTypeId, $compassionateTypeId) {
            $annual = $staff->leaveBalances->where('leave_type_id', $annualTypeId)->first()->balance ?? 0;
            $sick = $staff->leaveBalances->where('leave_type_id', $sickTypeId)->first()->balance ?? 0;
            $compassionate = $staff->leaveBalances->where('leave_type_id', $compassionateTypeId)->first()->balance ?? 0;

            return [
                'staff_name' => $staff->full_name,
                'department' => $staff->department->name ?? 'N/A',
                'annual_balance' => $annual,
                'sick_balance' => $sick,
                'compassionate_balance' => $compassionate,
            ];
        });

        return ['rows' => $data, 'chart' => null];
    }

    /**
     * 8. Exceeded Leave Report
     */
    protected function getExceededLeave($filters)
    {
        $query = LeaveRequest::with(['staff.department', 'leaveType'])
            ->where('status', 'approved')
            ->where('end_date', '<', now());
        $this->applyRoleScope($query);

        $data = $query->get()->map(function ($leave) {
            return [
                'staff_name' => $leave->staff->full_name,
                'department' => $leave->staff->department->name ?? 'N/A',
                'leave_type' => $leave->leaveType->name ?? 'N/A',
                'start_date' => $leave->start_date->format('d M Y'),
                'end_date' => $leave->end_date->format('d M Y'),
                'days' => $leave->days,
            ];
        });

        return ['rows' => $data, 'chart' => null];
    }

    /**
     * 9. Approval Time Report
     */
    protected function getApprovalTime($filters)
    {
        $approvalStats = DB::table('approval_logs')
            ->select('from_role', DB::raw('AVG(TIMESTAMPDIFF(HOUR, created_at, updated_at)) as avg_hours'))
            ->where('action', 'approved')
            ->groupBy('from_role')
            ->get();

        $data = $approvalStats->map(function ($stat) {
            return [
                'approver_role' => $stat->from_role,
                'avg_approval_hours' => round($stat->avg_hours, 2),
            ];
        });

        $chart = ['labels' => $data->pluck('approver_role'), 'series' => $data->pluck('avg_approval_hours')];

        return ['rows' => $data, 'chart' => $chart];
    }

    /**
     * 10. Leave Type Distribution Report
     */
    protected function getLeaveTypeDistribution($filters)
    {
        [$rangeStart, $rangeEnd] = $this->currentLeaveYearRange();
        $leaveTypes = LeaveType::withCount(['leaveRequests as request_count' => function ($q) use ($rangeStart, $rangeEnd) {
            $q->where('status', 'approved')
                ->whereBetween('created_at', [$rangeStart, $rangeEnd]);
        }])->get();

        $totalRequests = $leaveTypes->sum('request_count');
        foreach ($leaveTypes as $type) {
            $type->percentage = $totalRequests > 0 ? round(($type->request_count / $totalRequests) * 100, 1) : 0;
        }

        $data = $leaveTypes->map(function ($type) {
            return [
                'leave_type' => $type->name,
                'request_count' => $type->request_count,
                'percentage' => $type->percentage,
            ];
        });

        $chart = ['labels' => $data->pluck('leave_type'), 'series' => $data->pluck('request_count')];

        return ['rows' => $data, 'chart' => $chart];
    }

    // ========== STAFF REPORTS ==========

    protected function getStaffByDepartment($filters)
    {
        $query = User::with('department')
            ->when($filters['department_id'] ?? null, fn ($q, $deptId) => $q->where('department_id', $deptId))
            ->when($filters['status'] ?? null, fn ($q, $status) => $q->where('status', $status));

        $data = $query->get()->groupBy('department.name')->map(function ($items, $deptName) {
            return [
                'department' => $deptName ?: 'Unassigned',
                'count' => $items->count(),
            ];
        })->values();

        $chart = ['labels' => $data->pluck('department'), 'series' => $data->pluck('count')];

        return ['rows' => $data, 'chart' => $chart];
    }

    protected function getStaffByCategory($filters)
    {
        $query = User::query()
            ->when($filters['department_id'] ?? null, fn ($q, $deptId) => $q->where('department_id', $deptId));

        $data = $query->get()->groupBy('staff_category')->map(function ($items, $category) {
            return [
                'staff_category' => ucfirst($category ?: 'Not Set'),
                'count' => $items->count(),
            ];
        })->values();

        $chart = ['labels' => $data->pluck('staff_category'), 'series' => $data->pluck('count')];

        return ['rows' => $data, 'chart' => $chart];
    }

    protected function getStaffGenderDistribution($filters)
    {
        // Check if gender column exists
        if (! Schema::hasColumn('staff', 'gender')) {
            // Return placeholder data if column doesn't exist
            $data = collect([
                ['gender' => 'Male', 'count' => 0, 'percentage' => 0],
                ['gender' => 'Female', 'count' => 0, 'percentage' => 0],
                ['gender' => 'Not Specified', 'count' => 0, 'percentage' => 0],
            ]);

            $chart = ['labels' => $data->pluck('gender'), 'series' => $data->pluck('count')];

            return ['rows' => $data, 'chart' => $chart];
        }

        $query = User::query()
            ->when($filters['department_id'] ?? null, fn ($q, $deptId) => $q->where('department_id', $deptId));

        $maleCount = (clone $query)->where('gender', 'male')->count();
        $femaleCount = (clone $query)->where('gender', 'female')->count();
        $otherCount = (clone $query)->where('gender', 'other')->count();
        $notSpecified = (clone $query)->whereNull('gender')->count();

        $total = $maleCount + $femaleCount + $otherCount + $notSpecified;

        $data = collect([
            ['gender' => 'Male', 'count' => $maleCount, 'percentage' => $total > 0 ? round(($maleCount / $total) * 100, 1) : 0],
            ['gender' => 'Female', 'count' => $femaleCount, 'percentage' => $total > 0 ? round(($femaleCount / $total) * 100, 1) : 0],
            ['gender' => 'Other', 'count' => $otherCount, 'percentage' => $total > 0 ? round(($otherCount / $total) * 100, 1) : 0],
            ['gender' => 'Not Specified', 'count' => $notSpecified, 'percentage' => $total > 0 ? round(($notSpecified / $total) * 100, 1) : 0],
        ])->filter(fn ($item) => $item['count'] > 0);

        $chart = ['labels' => $data->pluck('gender'), 'series' => $data->pluck('count')];

        return ['rows' => $data, 'chart' => $chart];
    }

    protected function getStaffJoiningTrend($filters)
    {
        $year = $filters['year'] ?? now()->year;

        $query = User::whereYear('joining_date', $year)
            ->when($filters['department_id'] ?? null, fn ($q, $deptId) => $q->where('department_id', $deptId));

        $data = collect(range(1, 12))->map(function ($month) use ($query, $year) {
            $count = (clone $query)->whereMonth('joining_date', $month)->count();

            return [
                'month' => date('F', mktime(0, 0, 0, $month, 1)),
                'year' => $year,
                'joined_count' => $count,
            ];
        });

        $chart = ['labels' => $data->pluck('month'), 'series' => $data->pluck('joined_count')];

        return ['rows' => $data, 'chart' => $chart];
    }

    protected function getStaffByAgeGroup($filters)
    {
        $ageGroups = ['18-25', '26-35', '36-45', '46-55', '56+'];
        $data = collect();

        foreach ($ageGroups as $group) {
            $data->push(['age_group' => $group, 'count' => 0, 'percentage' => 0]);
        }

        $chart = ['labels' => $ageGroups, 'series' => [0, 0, 0, 0, 0]];

        return ['rows' => $data, 'chart' => $chart];
    }

    protected function getStaffByQualification($filters)
    {
        $data = collect([
            ['qualification' => 'PhD', 'count' => 0, 'percentage' => 0],
            ['qualification' => "Master's", 'count' => 0, 'percentage' => 0],
            ['qualification' => "Bachelor's", 'count' => 0, 'percentage' => 0],
            ['qualification' => 'Diploma', 'count' => 0, 'percentage' => 0],
            ['qualification' => 'Certificate', 'count' => 0, 'percentage' => 0],
        ]);

        $chart = ['labels' => $data->pluck('qualification'), 'series' => $data->pluck('count')];

        return ['rows' => $data, 'chart' => $chart];
    }

    // ========== LEAVE REPORTS ==========

    protected function getLeaveByDepartment($filters)
    {
        $query = LeaveRequest::with(['staff.department', 'leaveType'])
            ->where('status', 'approved')
            ->when($filters['date_from'] ?? null, fn ($q, $from) => $q->whereDate('start_date', '>=', $from))
            ->when($filters['date_to'] ?? null, fn ($q, $to) => $q->whereDate('end_date', '<=', $to))
            ->when($filters['leave_type_id'] ?? null, fn ($q, $typeId) => $q->where('leave_type_id', $typeId));

        $data = $query->get()->groupBy('staff.department.name')->map(function ($items, $deptName) {
            return [
                'department' => $deptName ?: 'Unassigned',
                'total_days' => $items->sum('days'),
                'staff_count' => $items->unique('staff_id')->count(),
                'avg_per_staff' => round($items->sum('days') / max(1, $items->unique('staff_id')->count()), 1),
            ];
        })->values();

        $chart = ['labels' => $data->pluck('department'), 'series' => $data->pluck('total_days')];

        return ['rows' => $data, 'chart' => $chart];
    }

    protected function getLeaveByMonth($filters)
    {
        $year = $filters['year'] ?? now()->year;

        $query = LeaveRequest::where('status', 'approved')
            ->whereYear('start_date', $year)
            ->when($filters['department_id'] ?? null, function ($q, $deptId) {
                return $q->whereHas('staff', fn ($sq) => $sq->where('department_id', $deptId));
            });

        $data = collect(range(1, 12))->map(function ($month) use ($query) {
            $monthRequests = (clone $query)->whereMonth('start_date', $month)->get();

            return [
                'month' => date('F', mktime(0, 0, 0, $month, 1)),
                'total_days' => $monthRequests->sum('days'),
                'staff_count' => $monthRequests->unique('staff_id')->count(),
            ];
        });

        $chart = ['labels' => $data->pluck('month'), 'series' => $data->pluck('total_days')];

        return ['rows' => $data, 'chart' => $chart];
    }

    protected function getLeaveByStaff($filters)
    {
        $query = LeaveRequest::with(['staff.department', 'leaveType'])
            ->when($filters['date_from'] ?? null, fn ($q, $from) => $q->whereDate('start_date', '>=', $from))
            ->when($filters['date_to'] ?? null, fn ($q, $to) => $q->whereDate('end_date', '<=', $to))
            ->when($filters['department_id'] ?? null, function ($q, $deptId) {
                return $q->whereHas('staff', fn ($sq) => $sq->where('department_id', $deptId));
            })
            ->when($filters['leave_type_id'] ?? null, fn ($q, $typeId) => $q->where('leave_type_id', $typeId));

        $data = $query->get()->map(function ($leave) {
            return [
                'staff_name' => $leave->staff->full_name,
                'department' => $leave->staff->department->name ?? 'N/A',
                'leave_type' => $leave->leaveType->name ?? 'N/A',
                'start_date' => $leave->start_date->format('d M Y'),
                'end_date' => $leave->end_date->format('d M Y'),
                'days' => $leave->days,
                'status' => ucfirst($leave->status),
            ];
        });

        return ['rows' => $data, 'chart' => null];
    }

    protected function getLeaveByStatus($filters)
    {
        $query = LeaveRequest::query()
            ->when($filters['date_from'] ?? null, fn ($q, $from) => $q->whereDate('created_at', '>=', $from))
            ->when($filters['date_to'] ?? null, fn ($q, $to) => $q->whereDate('created_at', '<=', $to))
            ->when($filters['department_id'] ?? null, function ($q, $deptId) {
                return $q->whereHas('staff', fn ($sq) => $sq->where('department_id', $deptId));
            });

        $data = $query->get()->groupBy('status')->map(function ($items, $status) {
            return [
                'status' => ucfirst($status),
                'count' => $items->count(),
                'total_days' => $items->sum('days'),
            ];
        })->values();

        $chart = ['labels' => $data->pluck('status'), 'series' => $data->pluck('count')];

        return ['rows' => $data, 'chart' => $chart];
    }

    protected function getLeaveCancelled($filters)
    {
        $query = LeaveRequest::with(['staff.department', 'leaveType'])
            ->where('status', 'cancelled');

        $data = $query->get()->map(function ($leave) {
            return [
                'staff_name' => $leave->staff->full_name,
                'department' => $leave->staff->department->name ?? 'N/A',
                'leave_type' => $leave->leaveType->name ?? 'N/A',
                'start_date' => $leave->start_date->format('d M Y'),
                'end_date' => $leave->end_date->format('d M Y'),
                'days' => $leave->days,
            ];
        });

        return ['rows' => $data, 'chart' => null];
    }

    protected function getLeaveActingReport($filters)
    {
        $query = LeaveRequest::with(['staff.department', 'leaveType', 'actingStaff'])
            ->whereNotNull('acting_staff_id')
            ->where('status', 'approved');

        $data = $query->get()->map(function ($leave) {
            return [
                'acting_staff' => $leave->actingStaff->full_name ?? 'N/A',
                'original_staff' => $leave->staff->full_name,
                'department' => $leave->staff->department->name ?? 'N/A',
                'leave_type' => $leave->leaveType->name ?? 'N/A',
                'start_date' => $leave->start_date->format('d M Y'),
                'end_date' => $leave->end_date->format('d M Y'),
            ];
        });

        return ['rows' => $data, 'chart' => null];
    }

    protected function getLeaveApprovalTime($filters)
    {
        $data = DB::table('approval_logs')
            ->select('from_role as approval_level', DB::raw('AVG(TIMESTAMPDIFF(HOUR, created_at, updated_at)) as avg_hours'))
            ->where('action', 'approved')
            ->groupBy('from_role')
            ->get()
            ->map(function ($item) {
                return [
                    'approval_level' => ucfirst($item->approval_level),
                    'avg_hours' => round($item->avg_hours, 2),
                ];
            });

        $chart = ['labels' => $data->pluck('approval_level'), 'series' => $data->pluck('avg_hours')];

        return ['rows' => $data, 'chart' => $chart];
    }

    // ========== PERMISSION & TRAVEL REPORTS ==========

    protected function getShortPermissionSummary($filters)
    {
        $query = PermissionRequest::with(['staff.department'])
            ->when($filters['department_id'] ?? null, function ($q, $deptId) {
                return $q->whereHas('staff', fn ($sq) => $sq->where('department_id', $deptId));
            })
            ->when($filters['status'] ?? null, fn ($q, $status) => $q->where('status', $status))
            ->when($filters['date_from'] ?? null, fn ($q, $from) => $q->whereDate('date', '>=', $from))
            ->when($filters['date_to'] ?? null, fn ($q, $to) => $q->whereDate('date', '<=', $to));

        $data = $query->get()->map(function ($permission) {
            return [
                'staff_name' => $permission->staff->full_name,
                'department' => $permission->staff->department->name ?? 'N/A',
                'permission_type' => 'Short Permission',
                'duration_hours' => $permission->duration_hours ?? 0,
                'status' => ucfirst($permission->status),
            ];
        });

        return ['rows' => $data, 'chart' => null];
    }

    protected function getTravelPermissionSummary($filters)
    {
        $query = TravelPermission::with(['staff.department'])
            ->when($filters['department_id'] ?? null, function ($q, $deptId) {
                return $q->whereHas('staff', fn ($sq) => $sq->where('department_id', $deptId));
            })
            ->when($filters['status'] ?? null, fn ($q, $status) => $q->where('status', $status))
            ->when($filters['date_from'] ?? null, fn ($q, $from) => $q->whereDate('start_date', '>=', $from))
            ->when($filters['date_to'] ?? null, fn ($q, $to) => $q->whereDate('end_date', '<=', $to));

        $data = $query->get()->map(function ($travel) {
            return [
                'staff_name' => $travel->staff->full_name,
                'department' => $travel->staff->department->name ?? 'N/A',
                'destination' => $travel->destination,
                'start_date' => $travel->start_date->format('d M Y'),
                'end_date' => $travel->end_date->format('d M Y'),
                'purpose' => $travel->purpose,
                'status' => ucfirst($travel->status),
            ];
        });

        return ['rows' => $data, 'chart' => null];
    }

    protected function getPermissionByMonth($filters)
    {
        $year = $filters['year'] ?? now()->year;

        $data = collect(range(1, 12))->map(function ($month) use ($year) {
            $shortCount = PermissionRequest::whereYear('created_at', $year)->whereMonth('created_at', $month)->count();
            $travelCount = TravelPermission::whereYear('created_at', $year)->whereMonth('created_at', $month)->count();

            return [
                'month' => date('F', mktime(0, 0, 0, $month, 1)),
                'short_permission_count' => $shortCount,
                'travel_permission_count' => $travelCount,
                'total' => $shortCount + $travelCount,
            ];
        });

        $chart = ['labels' => $data->pluck('month'), 'series' => $data->pluck('total')];

        return ['rows' => $data, 'chart' => $chart];
    }

    protected function getPendingPermissions($filters)
    {
        $shortPending = PermissionRequest::with(['staff.department'])
            ->where('status', 'pending')
            ->get()
            ->map(function ($item) {
                return [
                    'staff_name' => $item->staff->full_name,
                    'department' => $item->staff->department->name ?? 'N/A',
                    'permission_type' => 'Short Permission',
                    'submitted_on' => $item->created_at->format('d M Y'),
                    'waiting_days' => $item->created_at->diffInDays(now()),
                ];
            });

        $travelPending = TravelPermission::with(['staff.department'])
            ->where('status', 'pending')
            ->get()
            ->map(function ($item) {
                return [
                    'staff_name' => $item->staff->full_name,
                    'department' => $item->staff->department->name ?? 'N/A',
                    'permission_type' => 'Travel Permission',
                    'submitted_on' => $item->created_at->format('d M Y'),
                    'waiting_days' => $item->created_at->diffInDays(now()),
                ];
            });

        $data = $shortPending->concat($travelPending)->sortBy('submitted_on')->values();

        return ['rows' => $data, 'chart' => null];
    }

    // ========== DEPARTMENT & SCHOOL REPORTS ==========

    protected function getDepartmentLeaveSummary($filters)
    {
        return $this->getLeaveByDepartment($filters);
    }

    protected function getSchoolLeaveSummary($filters)
    {
        $query = LeaveRequest::with(['staff.department.school'])
            ->where('status', 'approved');

        $data = $query->get()->groupBy('staff.department.school.name')->map(function ($items, $schoolName) {
            return [
                'school' => $schoolName ?: 'Unassigned',
                'total_staff' => $items->unique('staff_id')->count(),
                'total_leave_days' => $items->sum('days'),
                'pending_requests' => 0,
                'approved_requests' => $items->count(),
            ];
        })->values();

        $chart = ['labels' => $data->pluck('school'), 'series' => $data->pluck('total_leave_days')];

        return ['rows' => $data, 'chart' => $chart];
    }

    protected function getDepartmentPerformance($filters)
    {
        return $this->getLeaveByDepartment($filters);
    }

    // ========== DEPARTMENT ROSTER REPORT ==========

    /**
     * Department Roster Report - Shows staff in each department with HOD information
     */
    public function getDepartmentRoster($filters)
    {
        $query = DB::table('departments')
            ->leftJoin('staff as hod', 'departments.hod_staff_id', '=', 'hod.id')
            ->leftJoin('staff', 'staff.department_id', '=', 'departments.id')
            ->select(
                'departments.name as department_name',
                'hod.first_name as hod_first_name',
                'hod.last_name as hod_last_name',
                'hod.email as hod_email',
                'staff.first_name as staff_first_name',
                'staff.last_name as staff_last_name',
                'staff.email as staff_email',
                'staff.staff_category',
                'staff.status as staff_status',
                'staff.job_title'
            )
            ->orderBy('departments.name')
            ->orderBy('staff.last_name');

        // Apply department filter if provided
        if (! empty($filters['department_id'])) {
            $query->where('departments.id', $filters['department_id']);
        }

        $results = $query->get();

        // Format the data as a COLLECTION (compatible with show.blade.php)
        $rows = collect();
        foreach ($results as $row) {
            // Only include rows that have staff (skip departments with no staff if not filtered)
            if ($row->staff_first_name) {
                $rows->push([
                    'Department' => $row->department_name,
                    'HOD Name' => trim($row->hod_first_name.' '.$row->hod_last_name),
                    'HOD Email' => $row->hod_email ?? '-',
                    'Staff Name' => trim($row->staff_first_name.' '.$row->staff_last_name),
                    'Staff Email' => $row->staff_email ?? '-',
                    'Category' => ucfirst($row->staff_category ?? 'N/A'),
                    'Status' => ucfirst($row->staff_status ?? 'N/A'),
                    'Job Title' => $row->job_title ?? '-',
                ]);
            }
        }

        // Calculate stats
        $stats = [
            'total_departments' => DB::table('departments')->count(),
            'total_staff' => DB::table('staff')->where('status', 'active')->count(),
            'departments_with_hod' => DB::table('departments')->whereNotNull('hod_staff_id')->count(),
        ];

        // Chart data (staff count by department)
        $departmentCounts = DB::table('departments')
            ->leftJoin('staff', 'staff.department_id', '=', 'departments.id')
            ->select('departments.name', DB::raw('COUNT(staff.id) as staff_count'))
            ->groupBy('departments.id', 'departments.name')
            ->get();

        // Return in same format as other reports (rows as collection, chart as standard format)
        return [
            'rows' => $rows,  // COLLECTION - compatible with show.blade.php
            'stats' => $stats,
            'chart' => [
                'labels' => $departmentCounts->pluck('name')->toArray(),
                'series' => $departmentCounts->pluck('staff_count')->toArray(),
            ],
            'total' => $rows->count(),
        ];
    }

    // ========== HELPER METHODS ==========

    /**
     * [start, end] datetime bounds of the current academic/leave year, for
     * reports scoping a query to "this leave year" without the whereYear()
     * boundary bug (a leave year spans two calendar years, e.g. Oct 2025 -
     * Sep 2026). Falls back to the plain calendar year if no academic year
     * has been activated yet.
     *
     * @return array{0: Carbon, 1: Carbon}
     */
    private function currentLeaveYearRange(): array
    {
        $academicYear = AcademicYear::current();

        return $academicYear
            ? [$academicYear->start_date->copy()->startOfDay(), $academicYear->end_date->copy()->endOfDay()]
            : [now()->startOfYear(), now()->endOfYear()];
    }

    /**
     * Start-of-leave-year date, for reports that default to "since the
     * leave year began" rather than a fixed lookback window.
     */
    private function currentLeaveYearStartDate(): string
    {
        return optional(AcademicYear::current())->start_date?->format('Y-m-d')
            ?? now()->startOfYear()->format('Y-m-d');
    }

    private function applyRoleScope(&$query)
    {
        $user = auth()->user();
        $role = $user->getRoleNames()->first();

        if ($role == 'hod') {
            $query->whereHas('staff.department', fn ($q) => $q->where('hod_staff_id', $user->id));
        } elseif ($role == 'dean') {
            $query->whereHas('staff.department.school', fn ($q) => $q->where('dean_staff_id', $user->id));
        } elseif ($role == 'dvca') {
            $query->whereHas('staff', fn ($q) => $q->where('staff_category', 'academic'));
        } elseif ($role == 'dvc-pfa') {
            $query->whereHas('staff', fn ($q) => $q->where('staff_category', 'administrative'));
        }
    }

    private function applyRoleScopeToStaff(&$query)
    {
        $user = auth()->user();
        $role = $user->getRoleNames()->first();

        if ($role == 'hod') {
            $query->whereHas('department', fn ($q) => $q->where('hod_staff_id', $user->id));
        } elseif ($role == 'dean') {
            $query->whereHas('department.school', fn ($q) => $q->where('dean_staff_id', $user->id));
        }
    }
}
