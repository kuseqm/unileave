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
        $this->call(DepartmentSeeder::class);           // 4. Departments
        $this->call(StaffSeeder::class);                // 5. Staff
        $this->call(LeaveTypeSeeder::class);            // 6. Leave types
        $this->call(LeaveBalanceSeeder::class);         // 7. Leave balances
        $this->call(RoleAndPermissionSeeder::class);    // 8. Roles & Permissions (UPDATED)
        $this->call(RoleAssignmentSeeder::class);       // 9. Assign roles to users
        $this->call(BlackoutPeriodSeeder::class);       // 10. Blackout periods
        $this->call(ActingDvcAssignmentSeeder::class);  // 11. Acting DVC
        $this->call(SettingsSeeder::class);             // 12. Settings
    }
}