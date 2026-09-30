<?php

namespace Database\Seeders;

use App\Models\Office;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OfficesTableSeeder extends Seeder
{
    public function run()
    {
        // Find an admin user using Spatie Permission
        $adminUser = null;

        // Method 1: Get user with 'admin' role via Spatie
        $adminRoleId = DB::table('roles')->where('name', 'admin')->value('id');

        if ($adminRoleId) {
            $adminUserId = DB::table('model_has_roles')
                ->where('role_id', $adminRoleId)
                ->value('model_id');

            if ($adminUserId) {
                $adminUser = User::find($adminUserId);
            }
        }

        // Method 2: Fallback – get any active user
        if (! $adminUser) {
            $adminUser = User::where('status', 'active')->first();
        }

        // Method 3: Final fallback – create a temporary admin user (for seeding only)
        if (! $adminUser) {
            $this->command->warn('No users found. Offices will be created with created_by = NULL. Please assign manually later.');
        }

        $adminUserId = $adminUser ? $adminUser->id : null;

        // Get existing departments as array
        $departments = DB::table('departments')->pluck('id', 'name')->toArray();

        // Get campuses as array
        $campusesArray = DB::table('campuses')->pluck('id', 'name')->toArray();

        // Find Main Campus ID - FIXED: $campusesArray is an array, not a Collection
        $mainCampusId = null;
        if (isset($campusesArray['Main Campus'])) {
            $mainCampusId = $campusesArray['Main Campus'];
        } else {
            // If 'Main Campus' not found, get the first campus ID from the array
            $firstCampusId = ! empty($campusesArray) ? reset($campusesArray) : null;
            $mainCampusId = $firstCampusId;
        }

        // Define all offices to seed
        $offices = [
            // ========== CENTRAL OFFICES (no department_id) ==========
            [
                'name' => "Vice Chancellor's Office",
                'short_name' => 'VC',
                'department_id' => null,
                'campus_id' => $mainCampusId,
                'school_id' => null,
                'email_alias' => 'vc@university.ac.tz',
                'receives_letters' => true,
                'sort_order' => 1,
            ],
            [
                'name' => "Deputy Vice Chancellor's Office (Academic)",
                'short_name' => 'DVCA',
                'department_id' => null,
                'campus_id' => $mainCampusId,
                'school_id' => null,
                'email_alias' => 'dvca@university.ac.tz',
                'receives_letters' => true,
                'sort_order' => 2,
            ],
            [
                'name' => "Deputy Vice Chancellor's Office (Planning, Finance & Administration)",
                'short_name' => 'DVC-PFA',
                'department_id' => null,
                'campus_id' => $mainCampusId,
                'school_id' => null,
                'email_alias' => 'dvcpfa@university.ac.tz',
                'receives_letters' => true,
                'sort_order' => 3,
            ],
            [
                'name' => "Registrar's Office",
                'short_name' => 'Registrar',
                'department_id' => null,
                'campus_id' => $mainCampusId,
                'school_id' => null,
                'email_alias' => 'registrar@university.ac.tz',
                'receives_letters' => true,
                'sort_order' => 4,
            ],
            [
                'name' => "Deputy Registrar's Office (Academic)",
                'short_name' => 'Dep Reg Academic',
                'department_id' => null,
                'campus_id' => $mainCampusId,
                'school_id' => null,
                'email_alias' => 'academic@university.ac.tz',
                'receives_letters' => true,
                'sort_order' => 5,
            ],
            [
                'name' => "Deputy Registrar's Office (Administration)",
                'short_name' => 'Dep Reg Admin',
                'department_id' => null,
                'campus_id' => $mainCampusId,
                'school_id' => null,
                'email_alias' => 'admin@university.ac.tz',
                'receives_letters' => true,
                'sort_order' => 6,
            ],
            [
                'name' => 'Finance Office',
                'short_name' => 'Finance',
                'department_id' => null,
                'campus_id' => $mainCampusId,
                'school_id' => null,
                'email_alias' => 'finance@university.ac.tz',
                'receives_letters' => true,
                'sort_order' => 7,
            ],
            [
                'name' => 'Human Resources Office',
                'short_name' => 'HR',
                'department_id' => null,
                'campus_id' => $mainCampusId,
                'school_id' => null,
                'email_alias' => 'hr@university.ac.tz',
                'receives_letters' => true,
                'sort_order' => 8,
            ],
            [
                'name' => 'Legal Office',
                'short_name' => 'Legal',
                'department_id' => null,
                'campus_id' => $mainCampusId,
                'school_id' => null,
                'email_alias' => 'legal@university.ac.tz',
                'receives_letters' => true,
                'sort_order' => 9,
            ],
            [
                'name' => 'Internal Audit Office',
                'short_name' => 'Audit',
                'department_id' => null,
                'campus_id' => $mainCampusId,
                'school_id' => null,
                'email_alias' => 'audit@university.ac.tz',
                'receives_letters' => true,
                'sort_order' => 10,
            ],
            [
                'name' => 'Procurement Office',
                'short_name' => 'Procurement',
                'department_id' => null,
                'campus_id' => $mainCampusId,
                'school_id' => null,
                'email_alias' => 'procurement@university.ac.tz',
                'receives_letters' => true,
                'sort_order' => 11,
            ],
            [
                'name' => 'ICT Office',
                'short_name' => 'ICT',
                'department_id' => null,
                'campus_id' => $mainCampusId,
                'school_id' => null,
                'email_alias' => 'ict@university.ac.tz',
                'receives_letters' => true,
                'sort_order' => 12,
            ],
            [
                'name' => 'Library Office',
                'short_name' => 'Library',
                'department_id' => null,
                'campus_id' => $mainCampusId,
                'school_id' => null,
                'email_alias' => 'library@university.ac.tz',
                'receives_letters' => true,
                'sort_order' => 13,
            ],
            [
                'name' => 'Research & Innovation Office',
                'short_name' => 'Research',
                'department_id' => null,
                'campus_id' => $mainCampusId,
                'school_id' => null,
                'email_alias' => 'research@university.ac.tz',
                'receives_letters' => true,
                'sort_order' => 14,
            ],
            [
                'name' => 'Quality Assurance Office',
                'short_name' => 'QA',
                'department_id' => null,
                'campus_id' => $mainCampusId,
                'school_id' => null,
                'email_alias' => 'qa@university.ac.tz',
                'receives_letters' => true,
                'sort_order' => 15,
            ],
            [
                'name' => 'International Office',
                'short_name' => 'International',
                'department_id' => null,
                'campus_id' => $mainCampusId,
                'school_id' => null,
                'email_alias' => 'international@university.ac.tz',
                'receives_letters' => true,
                'sort_order' => 16,
            ],
            [
                'name' => 'Public Relations Office',
                'short_name' => 'PR',
                'department_id' => null,
                'campus_id' => $mainCampusId,
                'school_id' => null,
                'email_alias' => 'pr@university.ac.tz',
                'receives_letters' => true,
                'sort_order' => 17,
            ],
            [
                'name' => 'Students Affairs Office',
                'short_name' => 'Students',
                'department_id' => null,
                'campus_id' => $mainCampusId,
                'school_id' => null,
                'email_alias' => 'students@university.ac.tz',
                'receives_letters' => true,
                'sort_order' => 18,
            ],
        ];

        // Add department-based offices (HOD Office for each department)
        $sortOrder = 100;
        foreach ($departments as $deptName => $deptId) {
            // Clean short_name: remove special characters, limit to 30 chars
            $cleanShortName = 'HOD-'.substr(preg_replace('/[^A-Za-z0-9]/', '', $deptName), 0, 20);

            $offices[] = [
                'name' => "HOD's Office - ".$deptName,
                'short_name' => $cleanShortName,
                'department_id' => $deptId,
                'campus_id' => $mainCampusId,
                'school_id' => null,
                'email_alias' => null,
                'receives_letters' => true,
                'sort_order' => $sortOrder++,
            ];
        }

        // Insert offices
        $officeCount = 0;
        foreach ($offices as $office) {
            // Check if office already exists to avoid duplicates
            $exists = DB::table('offices')
                ->where('name', $office['name'])
                ->when($office['department_id'], function ($query, $deptId) {
                    return $query->where('department_id', $deptId);
                }, function ($query) {
                    return $query->whereNull('department_id');
                })
                ->exists();

            if (! $exists) {
                DB::table('offices')->insert([
                    'name' => $office['name'],
                    'short_name' => $office['short_name'],
                    'department_id' => $office['department_id'],
                    'campus_id' => $office['campus_id'],
                    'school_id' => $office['school_id'],
                    'email_alias' => $office['email_alias'],
                    'is_active' => true,
                    'receives_letters' => $office['receives_letters'],
                    'sort_order' => $office['sort_order'],
                    'created_by' => $adminUserId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $officeCount++;
            }
        }

        $this->command->info('Offices table seeded successfully. Total offices: '.$officeCount);
    }
}
