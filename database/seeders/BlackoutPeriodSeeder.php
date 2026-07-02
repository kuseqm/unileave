<?php
// database/seeders/BlackoutPeriodSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\University;
use App\Models\User;
use App\Models\Department;
use App\Models\LeaveType;

class BlackoutPeriodSeeder extends Seeder
{
    public function run()
    {
        // Find university
        $university = University::first();
        if (!$university) {
            $this->command->error('No university found. Run UniversitySeeder first.');
            return;
        }

        // Find admin user
        $admin = User::where('email', 'admin@smut.ac.tz')->first();
        if (!$admin) {
            $this->command->error('No admin found. Run StaffSeeder first.');
            return;
        }

        // Find departments by names (instead of hardcoded IDs)
        $cseDept = Department::where('name', 'like', '%Computer Science%')->first();
        $mathDept = Department::where('name', 'like', '%Accounting%')->first();
        $hrDept = Department::where('name', 'like', '%Human%')->first();

        // Find leave types by code (LC1, LC2, etc.)
        $annualLeave = LeaveType::where('code', 'LC1')->first();
        $sickLeave = LeaveType::where('code', 'LC2')->first();
        $compassionateLeave = LeaveType::where('code', 'LC3')->first();

        // Prepare department IDs as JSON
        $deptIds = [];
        if ($cseDept && $mathDept) {
            $deptIds = [$cseDept->id, $mathDept->id];
        }

        $hrDeptIds = [];
        if ($hrDept) {
            $hrDeptIds = [$hrDept->id];
        }

        // Prepare leave type IDs as JSON
        $leaveTypeIds = [];
        if ($annualLeave && $sickLeave && $compassionateLeave) {
            $leaveTypeIds = [$annualLeave->id, $sickLeave->id, $compassionateLeave->id];
        }

        $annualOnlyIds = [];
        if ($annualLeave) {
            $annualOnlyIds = [$annualLeave->id];
        }

        // Insert blackout periods
        DB::table('blackout_periods')->insert([
            [
                'university_id' => $university->id,
                'name' => 'Final Examination Week',
                'start_date' => '2026-06-01',
                'end_date' => '2026-06-07',
                'restriction_type' => 'hard',
                'affected_departments' => json_encode($deptIds),
                'affected_leave_types' => json_encode($leaveTypeIds),
                'affected_categories' => json_encode(['academic']),
                'override_allowed' => false,
                'created_by' => $admin->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'university_id' => $university->id,
                'name' => 'Admission Period',
                'start_date' => '2026-08-15',
                'end_date' => '2026-08-30',
                'restriction_type' => 'soft',
                'affected_departments' => json_encode($hrDeptIds),
                'affected_leave_types' => json_encode($annualOnlyIds),
                'affected_categories' => json_encode(['administrative']),
                'override_allowed' => true,
                'created_by' => $admin->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'university_id' => $university->id,
                'name' => 'Accreditation Visit',
                'start_date' => '2026-10-10',
                'end_date' => '2026-10-15',
                'restriction_type' => 'hard',
                'affected_departments' => null, // null means all departments
                'affected_leave_types' => null, // null means all leave types
                'affected_categories' => json_encode(['academic', 'administrative']),
                'override_allowed' => true,
                'created_by' => $admin->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}