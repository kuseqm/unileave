<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ReportDefinition;

class ReportDefinitionsSeeder extends Seeder
{
    public function run()
    {
        $reports = [
            // 1. Leave Summary Report
            [
                'name' => 'leave_summary',
                'display_name' => 'Leave Summary Report',
                'description' => 'Summary of leave taken by staff, including leave type, duration, and status. Useful for HR and management to track leave patterns.',
                'filters' => json_encode([
                    'date_range' => true,
                    'department_id' => true,
                    'leave_type_id' => true,
                    'status' => true,
                ]),
                'columns' => json_encode([
                    'staff_name', 'department', 'leave_type', 'start_date', 'end_date', 'days', 'status'
                ]),
                'chart_config' => json_encode([
                    'type' => 'bar',
                    'title' => 'Leave Taken by Department',
                    'x_field' => 'department',
                    'y_field' => 'days',
                ]),
                'permission' => 'view_reports',
                'is_active' => true,
                'sort_order' => 1,
            ],

            // 2. Staff Directory Report
            [
                'name' => 'staff_directory',
                'display_name' => 'Staff Directory',
                'description' => 'Complete list of all staff members with contact information, department, and status.',
                'filters' => json_encode([
                    'department_id' => true,
                    'status' => true,
                    'staff_category' => true,
                ]),
                'columns' => json_encode([
                    'staff_id', 'name', 'email', 'phone', 'department', 'status', 'joining_date'
                ]),
                'chart_config' => null,
                'permission' => 'view_reports',
                'is_active' => true,
                'sort_order' => 2,
            ],

            // 3. Leave Balance Report
            [
                'name' => 'leave_balance',
                'display_name' => 'Leave Balance Report',
                'description' => 'Current leave balances for all staff, showing annual, sick, and casual leave remaining.',
                'filters' => json_encode([
                    'department_id' => true,
                    'staff_category' => true,
                ]),
                'columns' => json_encode([
                    'staff_name', 'department', 'annual_leave', 'sick_leave', 'casual_leave', 'remaining'
                ]),
                'chart_config' => json_encode([
                    'type' => 'pie',
                    'title' => 'Leave Balance Distribution',
                    'labels_field' => 'staff_name',
                    'data_field' => 'remaining',
                ]),
                'permission' => 'view_reports',
                'is_active' => true,
                'sort_order' => 3,
            ],

            // 4. Pending Approvals Report
            [
                'name' => 'pending_approvals',
                'display_name' => 'Pending Approvals Report',
                'description' => 'List of all leave requests pending approval, with details and waiting time.',
                'filters' => json_encode([
                    'department_id' => true,
                    'approval_level' => true,
                    'date_range' => true,
                ]),
                'columns' => json_encode([
                    'staff_name', 'department', 'leave_type', 'start_date', 'end_date', 'days', 'submitted_on', 'current_approver'
                ]),
                'chart_config' => json_encode([
                    'type' => 'bar',
                    'title' => 'Pending Requests by Department',
                    'x_field' => 'department',
                    'y_field' => 'count',
                ]),
                'permission' => 'view_reports',
                'is_active' => true,
                'sort_order' => 4,
            ],

            // 5. Leave Taken by Month (Trend Analysis)
            [
                'name' => 'leave_trend',
                'display_name' => 'Leave Trend Analysis',
                'description' => 'Monthly leave taken trends to identify peak leave periods.',
                'filters' => json_encode([
                    'year' => true,
                'department_id' => true,
                ]),
                'columns' => json_encode([
                    'month', 'total_days', 'staff_count', 'avg_days_per_staff'
                ]),
                'chart_config' => json_encode([
                    'type' => 'line',
                    'title' => 'Monthly Leave Trends',
                    'x_field' => 'month',
                    'y_field' => 'total_days',
                ]),
                'permission' => 'view_reports',
                'is_active' => true,
                'sort_order' => 5,
            ],

            // 6. Department Performance Report
            [
                'name' => 'department_performance',
                'display_name' => 'Department Performance',
                'description' => 'Leave metrics per department: total days taken, average per staff, pending requests.',
                'filters' => json_encode([
                    'date_range' => true,
                ]),
                'columns' => json_encode([
                    'department', 'total_staff', 'total_leave_days', 'avg_per_staff', 'pending_requests', 'approved_requests'
                ]),
                'chart_config' => json_encode([
                    'type' => 'horizontalBar',
                    'title' => 'Leave Days by Department',
                    'x_field' => 'department',
                    'y_field' => 'total_leave_days',
                ]),
                'permission' => 'view_reports',
                'is_active' => true,
                'sort_order' => 6,
            ],

            // 7. Staff Leave History (Individual)
            [
                'name' => 'staff_leave_history',
                'display_name' => 'Staff Leave History',
                'description' => 'Detailed leave history for a specific staff member.',
                'filters' => json_encode([
                    'staff_id' => true,
                    'date_range' => true,
                ]),
                'columns' => json_encode([
                    'leave_type', 'start_date', 'end_date', 'days', 'status', 'approved_by', 'approved_at'
                ]),
                'chart_config' => null,
                'permission' => 'view_reports',
                'is_active' => true,
                'sort_order' => 7,
            ],

            // 8. Leave Type Distribution
            [
                'name' => 'leave_type_distribution',
                'display_name' => 'Leave Type Distribution',
                'description' => 'Percentage breakdown of leave taken by leave type (annual, sick, casual, etc.).',
                'filters' => json_encode([
                    'date_range' => true,
                    'department_id' => true,
                ]),
                'columns' => json_encode([
                    'leave_type', 'total_days', 'percentage', 'staff_count'
                ]),
                'chart_config' => json_encode([
                    'type' => 'pie',
                    'title' => 'Leave Types Distribution',
                    'labels_field' => 'leave_type',
                    'data_field' => 'total_days',
                ]),
                'permission' => 'view_reports',
                'is_active' => true,
                'sort_order' => 8,
            ],

            // 9. Approval Time Report
            [
                'name' => 'approval_time',
                'display_name' => 'Approval Time Report',
                'description' => 'Average time taken to approve leave requests, by department and approver.',
                'filters' => json_encode([
                    'date_range' => true,
                    'department_id' => true,
                ]),
                'columns' => json_encode([
                    'department', 'avg_approval_hours', 'min_approval_hours', 'max_approval_hours', 'total_approved'
                ]),
                'chart_config' => json_encode([
                    'type' => 'bar',
                    'title' => 'Average Approval Time by Department',
                    'x_field' => 'department',
                    'y_field' => 'avg_approval_hours',
                ]),
                'permission' => 'view_reports',
                'is_active' => true,
                'sort_order' => 9,
            ],

            // 10. Low Balance Alerts
            [
                'name' => 'low_balance_alerts',
                'display_name' => 'Low Balance Alerts',
                'description' => 'Staff members with leave balance below threshold (e.g., less than 5 days).',
                'filters' => json_encode([
                    'department_id' => true,
                    'threshold' => true, // configurable threshold
                ]),
                'columns' => json_encode([
                    'staff_name', 'department', 'annual_remaining', 'sick_remaining', 'casual_remaining'
                ]),
                'chart_config' => null,
                'permission' => 'view_reports',
                'is_active' => true,
                'sort_order' => 10,
            ],
        ];

        foreach ($reports as $report) {
            ReportDefinition::updateOrCreate(
                ['name' => $report['name']],
                $report
            );
        }

        $this->command->info('Report definitions seeded successfully!');
    }
}