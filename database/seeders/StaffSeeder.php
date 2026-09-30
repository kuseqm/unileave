<?php

namespace Database\Seeders;

use App\Models\Campus;
use App\Models\Department;
use App\Models\University;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class StaffSeeder extends Seeder
{
    public function run()
    {
        // CREATE ROLES FIRST
        $roles = ['admin', 'staff', 'hod', 'dean', 'dvca', 'dvc-pfa', 'acting-dvc', 'hr-officer', 'hr-manager'];

        foreach ($roles as $roleName) {
            Role::firstOrCreate([
                'name' => $roleName,
                'guard_name' => 'web',
            ]);
        }

        $this->command->info('✓ Roles created successfully!');

        // FIND REFERENCES
        $university = University::where('name', 'St Martine\'s University of Testing UniLeave')->first();
        if (! $university) {
            $this->command->error('University not found. Run UniversitySeeder first.');

            return;
        }

        $mainCampus = Campus::where('name', 'Main Campus - Dodoma')->first();
        if (! $mainCampus) {
            $this->command->error('Main Campus not found. Run CampusSeeder first.');

            return;
        }

        // Get departments
        $cseDept = Department::where('name', 'Department of Computer Science')->first();
        $accDept = Department::where('name', 'Department of Accounting and Finance')->first();
        $mktDept = Department::where('name', 'Department of Marketing and Entrepreneurship')->first();
        $pubDept = Department::where('name', 'Department of Public Law')->first();
        $priDept = Department::where('name', 'Department of Private Law')->first();
        $hrDept = Department::where('name', 'Human Resources')->first();
        $epsDept = Department::where('name', 'Department of Educational Psychology')->first();
        $curDept = Department::where('name', 'Department of Curriculum Studies')->first();

        $defaultDept = $cseDept ?? $accDept ?? Department::first();

        $tempPassword = Hash::make('Temp@123');

        // ============================================
        // CREATE ALL 27 USERS WITH TITLE AND MIDDLE NAME
        // ============================================

        $users = [
            // 1. Admin
            [
                'staff_id' => '1000',
                'email' => 'admin@smut.ac.tz',
                'title' => 'Mr.',
                'first_name' => 'John',
                'middle_name' => 'M.',
                'last_name' => 'Mbeki',
                'department_id' => $defaultDept->id,
                'staff_category' => 'administrative',
                'joining_date' => '2020-01-01',
                'role' => 'admin',
            ],

            // 2. HR Officer
            [
                'staff_id' => '1001',
                'email' => 'sarah.johnson@smut.ac.tz',
                'title' => 'Ms.',
                'first_name' => 'Sarah',
                'middle_name' => 'A.',
                'last_name' => 'Johnson',
                'department_id' => $hrDept ? $hrDept->id : $defaultDept->id,
                'staff_category' => 'administrative',
                'joining_date' => '2018-01-15',
                'role' => 'hr-officer',
            ],

            // 3. DVCA
            [
                'staff_id' => '1002',
                'email' => 'robert.kibona@smut.ac.tz',
                'title' => 'Prof.',
                'first_name' => 'Robert',
                'middle_name' => '',
                'last_name' => 'Kibona',
                'department_id' => $defaultDept->id,
                'staff_category' => 'academic',
                'joining_date' => '2008-01-15',
                'role' => 'dvca',
            ],

            // 4. DVC-PFA
            [
                'staff_id' => '1003',
                'email' => 'grace.mtenga@smut.ac.tz',
                'title' => 'Dr.',
                'first_name' => 'Grace',
                'middle_name' => '',
                'last_name' => 'Mtenga',
                'department_id' => $accDept ? $accDept->id : $defaultDept->id,
                'staff_category' => 'administrative',
                'joining_date' => '2008-01-15',
                'role' => 'dvc-pfa',
            ],

            // 5. Dean - Business
            [
                'staff_id' => '1004',
                'email' => 'richard.mwakipesile@smut.ac.tz',
                'title' => 'Prof.',
                'first_name' => 'Richard',
                'middle_name' => '',
                'last_name' => 'Mwakipesile',
                'department_id' => $accDept ? $accDept->id : $defaultDept->id,
                'staff_category' => 'academic',
                'joining_date' => '2010-01-15',
                'role' => 'dean',
            ],

            // 6. Dean - Law
            [
                'staff_id' => '1005',
                'email' => 'catherine.ndunguru@smut.ac.tz',
                'title' => 'Prof.',
                'first_name' => 'Catherine',
                'middle_name' => '',
                'last_name' => 'Ndunguru',
                'department_id' => $pubDept ? $pubDept->id : $defaultDept->id,
                'staff_category' => 'academic',
                'joining_date' => '2010-06-01',
                'role' => 'dean',
            ],

            // 7. Dean - Science & Tech
            [
                'staff_id' => '1006',
                'email' => 'peter.msoffe@smut.ac.tz',
                'title' => 'Dr.',
                'first_name' => 'Peter',
                'middle_name' => '',
                'last_name' => 'Msoffe',
                'department_id' => $cseDept ? $cseDept->id : $defaultDept->id,
                'staff_category' => 'academic',
                'joining_date' => '2010-03-15',
                'role' => 'dean',
            ],

            // 8. Dean - Education
            [
                'staff_id' => '1007',
                'email' => 'alice.lema@smut.ac.tz',
                'title' => 'Dr.',
                'first_name' => 'Alice',
                'middle_name' => '',
                'last_name' => 'Lema',
                'department_id' => $epsDept ? $epsDept->id : $defaultDept->id,
                'staff_category' => 'academic',
                'joining_date' => '2011-01-10',
                'role' => 'dean',
            ],

            // 9. Dean - Health Sciences
            [
                'staff_id' => '1008',
                'email' => 'james.mwakyusa@smut.ac.tz',
                'title' => 'Prof.',
                'first_name' => 'James',
                'middle_name' => '',
                'last_name' => 'Mwakyusa',
                'department_id' => $defaultDept->id,
                'staff_category' => 'academic',
                'joining_date' => '2012-07-20',
                'role' => 'dean',
            ],

            // 10. HOD - Accounting
            [
                'staff_id' => '1009',
                'email' => 'michael.mushi@smut.ac.tz',
                'title' => 'Dr.',
                'first_name' => 'Michael',
                'middle_name' => '',
                'last_name' => 'Mushi',
                'department_id' => $accDept ? $accDept->id : $defaultDept->id,
                'staff_category' => 'academic',
                'joining_date' => '2015-01-15',
                'role' => 'hod',
            ],

            // 11. HOD - Marketing
            [
                'staff_id' => '1010',
                'email' => 'david.mrema@smut.ac.tz',
                'title' => 'Mr.',
                'first_name' => 'David',
                'middle_name' => '',
                'last_name' => 'Mrema',
                'department_id' => $mktDept ? $mktDept->id : $defaultDept->id,
                'staff_category' => 'academic',
                'joining_date' => '2016-02-10',
                'role' => 'hod',
            ],

            // 12. HOD - Public Law
            [
                'staff_id' => '1011',
                'email' => 'catherine.nnko@smut.ac.tz',
                'title' => 'Dr.',
                'first_name' => 'Catherine',
                'middle_name' => '',
                'last_name' => 'Nnko',
                'department_id' => $pubDept ? $pubDept->id : $defaultDept->id,
                'staff_category' => 'academic',
                'joining_date' => '2015-06-20',
                'role' => 'hod',
            ],

            // 13. HOD - Private Law
            [
                'staff_id' => '1012',
                'email' => 'elizabeth.mlay@smut.ac.tz',
                'title' => 'Ms.',
                'first_name' => 'Elizabeth',
                'middle_name' => '',
                'last_name' => 'Mlay',
                'department_id' => $priDept ? $priDept->id : $defaultDept->id,
                'staff_category' => 'academic',
                'joining_date' => '2017-03-15',
                'role' => 'hod',
            ],

            // 14. HOD - Computer Science
            [
                'staff_id' => '1013',
                'email' => 'robert.kivuyo@smut.ac.tz',
                'title' => 'Dr.',
                'first_name' => 'Robert',
                'middle_name' => '',
                'last_name' => 'Kivuyo',
                'department_id' => $cseDept ? $cseDept->id : $defaultDept->id,
                'staff_category' => 'academic',
                'joining_date' => '2014-09-01',
                'role' => 'hod',
            ],

            // 15. HOD - IT
            [
                'staff_id' => '1014',
                'email' => 'joseph.mtaki@smut.ac.tz',
                'title' => 'Mr.',
                'first_name' => 'Joseph',
                'middle_name' => '',
                'last_name' => 'Mtaki',
                'department_id' => $cseDept ? $cseDept->id : $defaultDept->id,
                'staff_category' => 'academic',
                'joining_date' => '2016-07-15',
                'role' => 'hod',
            ],

            // 16. HOD - Psychology
            [
                'staff_id' => '1015',
                'email' => 'mary.mwita@smut.ac.tz',
                'title' => 'Dr.',
                'first_name' => 'Mary',
                'middle_name' => '',
                'last_name' => 'Mwita',
                'department_id' => $epsDept ? $epsDept->id : $defaultDept->id,
                'staff_category' => 'academic',
                'joining_date' => '2018-01-10',
                'role' => 'hod',
            ],

            // 17. HOD - Curriculum
            [
                'staff_id' => '1016',
                'email' => 'patricia.mwakyusa@smut.ac.tz',
                'title' => 'Ms.',
                'first_name' => 'Patricia',
                'middle_name' => '',
                'last_name' => 'Mwakyusa',
                'department_id' => $curDept ? $curDept->id : $defaultDept->id,
                'staff_category' => 'academic',
                'joining_date' => '2017-09-20',
                'role' => 'hod',
            ],

            // 18-22. Academic Staff
            [
                'staff_id' => '1017',
                'email' => 'john.mushi@smut.ac.tz',
                'title' => 'Mr.',
                'first_name' => 'John',
                'middle_name' => '',
                'last_name' => 'Mushi',
                'department_id' => $cseDept ? $cseDept->id : $defaultDept->id,
                'staff_category' => 'academic',
                'joining_date' => '2020-05-10',
                'role' => 'staff',
            ],
            [
                'staff_id' => '1018',
                'email' => 'mary.lema@smut.ac.tz',
                'title' => 'Ms.',
                'first_name' => 'Mary',
                'middle_name' => '',
                'last_name' => 'Lema',
                'department_id' => $accDept ? $accDept->id : $defaultDept->id,
                'staff_category' => 'academic',
                'joining_date' => '2021-02-15',
                'role' => 'staff',
            ],
            [
                'staff_id' => '1019',
                'email' => 'peter.ndyetabura@smut.ac.tz',
                'title' => 'Mr.',
                'first_name' => 'Peter',
                'middle_name' => '',
                'last_name' => 'Ndyetabura',
                'department_id' => $pubDept ? $pubDept->id : $defaultDept->id,
                'staff_category' => 'academic',
                'joining_date' => '2019-08-20',
                'role' => 'staff',
            ],
            [
                'staff_id' => '1020',
                'email' => 'anna.kivuyo@smut.ac.tz',
                'title' => 'Ms.',
                'first_name' => 'Anna',
                'middle_name' => '',
                'last_name' => 'Kivuyo',
                'department_id' => $priDept ? $priDept->id : $defaultDept->id,
                'staff_category' => 'academic',
                'joining_date' => '2022-01-10',
                'role' => 'staff',
            ],
            [
                'staff_id' => '1021',
                'email' => 'james.mwita@smut.ac.tz',
                'title' => 'Mr.',
                'first_name' => 'James',
                'middle_name' => '',
                'last_name' => 'Mwita',
                'department_id' => $mktDept ? $mktDept->id : $defaultDept->id,
                'staff_category' => 'academic',
                'joining_date' => '2021-06-30',
                'role' => 'staff',
            ],

            // 23-27. Administrative Staff
            [
                'staff_id' => '1022',
                'email' => 'grace.mkude@smut.ac.tz',
                'title' => 'Ms.',
                'first_name' => 'Grace',
                'middle_name' => '',
                'last_name' => 'Mkude',
                'department_id' => $hrDept ? $hrDept->id : $defaultDept->id,
                'staff_category' => 'administrative',
                'joining_date' => '2022-01-10',
                'role' => 'staff',
            ],
            [
                'staff_id' => '1023',
                'email' => 'thomas.mloka@smut.ac.tz',
                'title' => 'Mr.',
                'first_name' => 'Thomas',
                'middle_name' => '',
                'last_name' => 'Mloka',
                'department_id' => $accDept ? $accDept->id : $defaultDept->id,
                'staff_category' => 'administrative',
                'joining_date' => '2021-03-15',
                'role' => 'staff',
            ],
            [
                'staff_id' => '1024',
                'email' => 'esther.mrema@smut.ac.tz',
                'title' => 'Ms.',
                'first_name' => 'Esther',
                'middle_name' => '',
                'last_name' => 'Mrema',
                'department_id' => $mktDept ? $mktDept->id : $defaultDept->id,
                'staff_category' => 'administrative',
                'joining_date' => '2020-11-20',
                'role' => 'staff',
            ],
            [
                'staff_id' => '1025',
                'email' => 'daniel.mlay@smut.ac.tz',
                'title' => 'Mr.',
                'first_name' => 'Daniel',
                'middle_name' => '',
                'last_name' => 'Mlay',
                'department_id' => $hrDept ? $hrDept->id : $defaultDept->id,
                'staff_category' => 'administrative',
                'joining_date' => '2023-01-05',
                'role' => 'staff',
            ],
            [
                'staff_id' => '1026',
                'email' => 'sarah.mwaipopo@smut.ac.tz',
                'title' => 'Ms.',
                'first_name' => 'Sarah',
                'middle_name' => '',
                'last_name' => 'Mwaipopo',
                'department_id' => $accDept ? $accDept->id : $defaultDept->id,
                'staff_category' => 'administrative',
                'joining_date' => '2021-09-12',
                'role' => 'staff',
            ],
        ];

        foreach ($users as $userData) {
            $role = $userData['role'];
            unset($userData['role']);

            $userData['password'] = $tempPassword;
            $userData['university_id'] = $university->id;
            $userData['campus_id'] = $mainCampus->id;
            $userData['status'] = 'active';
            $userData['password_change_required'] = true;
            $userData['created_at'] = now();
            $userData['updated_at'] = now();

            $existing = User::where('email', $userData['email'])->first();

            if (! $existing) {
                $user = User::create($userData);
                $user->assignRole($role);
                $this->command->info("✓ Created: {$userData['email']} as {$role}");
            } else {
                $this->command->warn("User already exists: {$userData['email']}");
            }
        }

        // Update HOD references in departments table
        $hodUser = User::where('email', 'michael.mushi@smut.ac.tz')->first();
        if ($hodUser && $cseDept) {
            $cseDept->update(['hod_staff_id' => $hodUser->id]);
            $this->command->info('✓ Updated HOD for Computer Science department');
        }

        $this->command->info('✓ Staff seeded successfully!');
    }
}
