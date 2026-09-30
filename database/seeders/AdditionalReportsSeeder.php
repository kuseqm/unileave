<?php

namespace Database\Seeders;

use App\Models\ReportDefinition;
use Illuminate\Database\Seeder;

class AdditionalReportsSeeder extends Seeder
{
    public function run()
    {
        $reports = [
            // ========== STAFF REPORTS (6 new) ==========
            [
                'name' => 'staff_by_department',
                'display_name' => 'Staff by Department',
                'description' => 'List of staff members grouped by department.',
                'filters' => ['department_id' => true, 'status' => true],
                'columns' => ['staff_id', 'name', 'email', 'phone', 'department', 'status', 'joining_date'],
                'chart_config' => ['type' => 'bar', 'title' => 'Staff Count by Department', 'x_field' => 'department', 'y_field' => 'count'],
                'permission' => 'view_reports',
                'is_active' => true,
                'sort_order' => 11,
            ],
            [
                'name' => 'staff_by_category',
                'display_name' => 'Staff by Category',
                'description' => 'Staff distribution across Academic, Administrative, and Supporting categories.',
                'filters' => ['department_id' => true],
                'columns' => ['staff_name', 'department', 'staff_category', 'status'],
                'chart_config' => ['type' => 'pie', 'title' => 'Staff by Category', 'labels_field' => 'staff_category', 'data_field' => 'count'],
                'permission' => 'view_reports',
                'is_active' => true,
                'sort_order' => 12,
            ],
            [
                'name' => 'staff_gender_distribution',
                'display_name' => 'Gender Distribution',
                'description' => 'Number of male and female staff members across the institution.',
                'filters' => ['department_id' => true],
                'columns' => ['department', 'male_count', 'female_count', 'total'],
                'chart_config' => ['type' => 'pie', 'title' => 'Gender Distribution', 'labels_field' => 'gender', 'data_field' => 'count'],
                'permission' => 'view_reports',
                'is_active' => true,
                'sort_order' => 13,
            ],
            [
                'name' => 'staff_joining_trend',
                'display_name' => 'Staff Joining Trend',
                'description' => 'Number of staff who joined each month/year.',
                'filters' => ['year' => true, 'department_id' => true],
                'columns' => ['month', 'year', 'joined_count', 'department'],
                'chart_config' => ['type' => 'line', 'title' => 'Staff Joining Trend', 'x_field' => 'month', 'y_field' => 'joined_count'],
                'permission' => 'view_reports',
                'is_active' => true,
                'sort_order' => 14,
            ],
            [
                'name' => 'staff_by_age_group',
                'display_name' => 'Staff by Age Group',
                'description' => 'Age distribution of staff members.',
                'filters' => ['department_id' => true],
                'columns' => ['age_group', 'count', 'percentage'],
                'chart_config' => ['type' => 'bar', 'title' => 'Age Distribution', 'x_field' => 'age_group', 'y_field' => 'count'],
                'permission' => 'view_reports',
                'is_active' => true,
                'sort_order' => 15,
            ],
            [
                'name' => 'staff_by_qualification',
                'display_name' => 'Staff by Qualification',
                'description' => 'Highest qualification attained by staff members.',
                'filters' => ['department_id' => true],
                'columns' => ['qualification', 'count', 'percentage'],
                'chart_config' => ['type' => 'pie', 'title' => 'Staff by Qualification', 'labels_field' => 'qualification', 'data_field' => 'count'],
                'permission' => 'view_reports',
                'is_active' => true,
                'sort_order' => 16,
            ],

            // ========== LEAVE REPORTS (7 new) ==========
            [
                'name' => 'leave_by_department',
                'display_name' => 'Leave by Department',
                'description' => 'Total leave days taken by each department.',
                'filters' => ['date_range' => true, 'leave_type_id' => true],
                'columns' => ['department', 'total_days', 'staff_count', 'avg_per_staff'],
                'chart_config' => ['type' => 'horizontalBar', 'title' => 'Leave Days by Department', 'x_field' => 'department', 'y_field' => 'total_days'],
                'permission' => 'view_reports',
                'is_active' => true,
                'sort_order' => 21,
            ],
            [
                'name' => 'leave_by_month',
                'display_name' => 'Leave by Month',
                'description' => 'Leave taken per month (January to December).',
                'filters' => ['year' => true, 'department_id' => true],
                'columns' => ['month', 'total_days', 'staff_count'],
                'chart_config' => ['type' => 'line', 'title' => 'Monthly Leave Trend', 'x_field' => 'month', 'y_field' => 'total_days'],
                'permission' => 'view_reports',
                'is_active' => true,
                'sort_order' => 22,
            ],
            [
                'name' => 'leave_by_staff',
                'display_name' => 'Leave by Individual Staff',
                'description' => 'Detailed leave taken by each staff member.',
                'filters' => ['date_range' => true, 'department_id' => true, 'leave_type_id' => true],
                'columns' => ['staff_name', 'department', 'leave_type', 'start_date', 'end_date', 'days', 'status'],
                'chart_config' => null,
                'permission' => 'view_reports',
                'is_active' => true,
                'sort_order' => 23,
            ],
            [
                'name' => 'leave_by_status',
                'display_name' => 'Leave by Status',
                'description' => 'Summary of leave requests by status (Pending, Approved, Rejected).',
                'filters' => ['date_range' => true, 'department_id' => true],
                'columns' => ['status', 'count', 'total_days'],
                'chart_config' => ['type' => 'pie', 'title' => 'Leave by Status', 'labels_field' => 'status', 'data_field' => 'count'],
                'permission' => 'view_reports',
                'is_active' => true,
                'sort_order' => 24,
            ],
            [
                'name' => 'leave_cancelled',
                'display_name' => 'Cancelled Leave Requests',
                'description' => 'List of leave requests that were cancelled.',
                'filters' => ['date_range' => true, 'department_id' => true],
                'columns' => ['staff_name', 'department', 'leave_type', 'start_date', 'end_date', 'days', 'cancelled_by', 'cancelled_at'],
                'chart_config' => null,
                'permission' => 'view_reports',
                'is_active' => true,
                'sort_order' => 25,
            ],
            [
                'name' => 'leave_acting_report',
                'display_name' => 'Acting Staff Report',
                'description' => 'List of staff who acted on behalf of others during leave.',
                'filters' => ['date_range' => true, 'department_id' => true],
                'columns' => ['acting_staff', 'original_staff', 'department', 'leave_type', 'start_date', 'end_date'],
                'chart_config' => null,
                'permission' => 'view_reports',
                'is_active' => true,
                'sort_order' => 26,
            ],
            [
                'name' => 'leave_approval_time',
                'display_name' => 'Approval Time by Level',
                'description' => 'Average approval time per approval level (HOD, Dean, DVC, HR).',
                'filters' => ['date_range' => true],
                'columns' => ['approval_level', 'avg_hours', 'min_hours', 'max_hours', 'total_approved'],
                'chart_config' => ['type' => 'bar', 'title' => 'Approval Time by Level', 'x_field' => 'approval_level', 'y_field' => 'avg_hours'],
                'permission' => 'view_reports',
                'is_active' => true,
                'sort_order' => 27,
            ],

            // ========== PERMISSION & TRAVEL REPORTS (4 new) ==========
            [
                'name' => 'short_permission_summary',
                'display_name' => 'Short Permission Summary',
                'description' => 'Summary of short permission requests (hours, late/early).',
                'filters' => ['date_range' => true, 'department_id' => true],
                'columns' => ['staff_name', 'department', 'permission_type', 'start_time', 'end_time', 'duration_hours', 'status'],
                'chart_config' => ['type' => 'bar', 'title' => 'Short Permission by Department', 'x_field' => 'department', 'y_field' => 'count'],
                'permission' => 'view_reports',
                'is_active' => true,
                'sort_order' => 31,
            ],
            [
                'name' => 'travel_permission_summary',
                'display_name' => 'Travel Permission Summary',
                'description' => 'Summary of travel permission requests.',
                'filters' => ['date_range' => true, 'department_id' => true],
                'columns' => ['staff_name', 'department', 'destination', 'start_date', 'end_date', 'purpose', 'status'],
                'chart_config' => null,
                'permission' => 'view_reports',
                'is_active' => true,
                'sort_order' => 32,
            ],
            [
                'name' => 'permission_by_month',
                'display_name' => 'Permission Trends',
                'description' => 'Trend analysis of permission requests over months.',
                'filters' => ['year' => true, 'department_id' => true],
                'columns' => ['month', 'short_permission_count', 'travel_permission_count', 'total'],
                'chart_config' => ['type' => 'line', 'title' => 'Permission Trends', 'x_field' => 'month', 'y_field' => 'total'],
                'permission' => 'view_reports',
                'is_active' => true,
                'sort_order' => 33,
            ],
            [
                'name' => 'pending_permissions',
                'display_name' => 'Pending Permission Approvals',
                'description' => 'List of permission requests awaiting approval.',
                'filters' => ['department_id' => true, 'permission_type' => true],
                'columns' => ['staff_name', 'department', 'permission_type', 'submitted_on', 'current_approver', 'waiting_days'],
                'chart_config' => null,
                'permission' => 'view_reports',
                'is_active' => true,
                'sort_order' => 34,
            ],

            // ========== DEPARTMENT & SCHOOL REPORTS (3 new) ==========
            [
                'name' => 'department_leave_summary',
                'display_name' => 'Department Leave Summary',
                'description' => 'Consolidated leave summary per department (total days, pending, approved).',
                'filters' => ['date_range' => true],
                'columns' => ['department', 'total_staff', 'total_leave_days', 'pending_requests', 'approved_requests', 'avg_days'],
                'chart_config' => ['type' => 'horizontalBar', 'title' => 'Leave Days by Department', 'x_field' => 'department', 'y_field' => 'total_leave_days'],
                'permission' => 'view_reports',
                'is_active' => true,
                'sort_order' => 41,
            ],
            [
                'name' => 'school_leave_summary',
                'display_name' => 'School Leave Summary',
                'description' => 'Consolidated leave summary per school/faculty.',
                'filters' => ['date_range' => true],
                'columns' => ['school', 'total_staff', 'total_leave_days', 'pending_requests', 'approved_requests'],
                'chart_config' => ['type' => 'bar', 'title' => 'Leave Days by School', 'x_field' => 'school', 'y_field' => 'total_leave_days'],
                'permission' => 'view_reports',
                'is_active' => true,
                'sort_order' => 42,
            ],
            [
                'name' => 'department_performance',
                'display_name' => 'Department Performance',
                'description' => 'Leave metrics per department: total days taken, average per staff, pending requests.',
                'filters' => ['date_range' => true],
                'columns' => ['department', 'total_staff', 'total_leave_days', 'avg_per_staff', 'pending_requests', 'approved_requests'],
                'chart_config' => ['type' => 'horizontalBar', 'title' => 'Leave Days by Department', 'x_field' => 'department', 'y_field' => 'total_leave_days'],
                'permission' => 'view_reports',
                'is_active' => true,
                'sort_order' => 43,
            ],
        ];

        foreach ($reports as $report) {
            ReportDefinition::updateOrCreate(
                ['name' => $report['name']],
                $report
            );
        }

        $this->command->info('20 additional reports seeded successfully!');
    }
}
