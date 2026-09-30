<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(UniversitySeeder::class);           // 1. Universities
        $this->call(CampusSeeder::class);               // 2. Campuses
        $this->call(SchoolSeeder::class);               // 3. Schools
        $this->call(DepartmentSeeder::class);           // 4. Departments (needs school_id)
        $this->call(StaffSeeder::class);                // 5. Staff
        $this->call(LeaveTypeSeeder::class);            // 6. Leave types
        $this->call(LeaveBalanceSeeder::class);         // 7. Leave balances
        $this->call(RoleAssignmentSeeder::class);       // 8. Assign roles
        $this->call(BlackoutPeriodSeeder::class);       // 9. Blackout periods
        $this->call(ActingDvcAssignmentSeeder::class);  // 10. Acting DVC
        $this->call(UserPreferencesSeeder::class);      // 11. User preferences
        $this->call(HodSetupSeeder::class);             // 12. HOD Setup (Fixes approval flow)
        $this->call(TestApprovalFlowSeeder::class);     // 13. TEST APPROVAL FLOW USERS
        $this->call(PolicySeeder::class);               // 14. Sample university policies and rules
        $this->call(GraduationCeremonySeeder::class);    // 15. Real historical graduation ceremonies
    }
}
