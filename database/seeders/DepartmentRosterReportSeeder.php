<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ReportDefinition;

class DepartmentRosterReportSeeder extends Seeder
{
    public function run()
    {
        ReportDefinition::updateOrCreate(
            ['name' => 'department_roster'],
            [
                'name' => 'department_roster',
                'display_name' => 'Department Roster',
                'description' => 'Complete list of staff in each department with HOD information.',
                'filters' => json_encode(['department_id' => true]),
                'columns' => json_encode([
                    'department_name', 'hod_name', 'hod_email',
                    'staff_name', 'staff_email', 'staff_category', 'staff_status', 'job_title'
                ]),
                'chart_config' => json_encode([
                    'type' => 'bar',
                    'title' => 'Staff Count by Department',
                    'x_field' => 'department_name',
                    'y_field' => 'staff_count'
                ]),
                'permission' => 'view_reports',
                'is_active' => true,
                'sort_order' => 5,
            ]
        );
        
        $this->command->info('Department Roster report added successfully!');
    }
}