<?php

// database/seeders/DepartmentSeeder.php

namespace Database\Seeders;

use App\Models\Campus;
use App\Models\Department;
use App\Models\School;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run()
    {
        // Find campuses first
        $mainCampus = Campus::where('name', 'Main Campus - Dodoma')->first();
        $kampalaCampus = Campus::where('name', 'Kampala Campus')->first();

        if (! $mainCampus) {
            $this->command->error('Main Campus not found. Run CampusSeeder first.');

            return;
        }

        // Find schools by name/code
        $sbe = School::where('code', 'SBE')->first();  // School of Business and Economics
        $law = School::where('code', 'LAW')->first();  // School of Law
        $sst = School::where('code', 'SST')->first();  // School of Science and Technology
        $edu = School::where('code', 'EDU')->first();  // School of Education
        $shs = School::where('code', 'SHS')->first();  // School of Health Sciences

        if (! $sbe || ! $law || ! $sst || ! $edu) {
            $this->command->error('Schools not found. Run SchoolSeeder first.');

            return;
        }

        // Department data
        $departments = [
            // School of Business and Economics (SBE)
            [
                'school' => $sbe,
                'name' => 'Department of Accounting and Finance',
                'code' => 'ACC',
                'description' => 'Department of Accounting and Finance',
            ],
            [
                'school' => $sbe,
                'name' => 'Department of Marketing and Entrepreneurship',
                'code' => 'MKT',
                'description' => 'Department of Marketing and Entrepreneurship',
            ],

            // School of Law (LAW)
            [
                'school' => $law,
                'name' => 'Department of Public Law',
                'code' => 'PUB',
                'description' => 'Department of Public Law',
            ],
            [
                'school' => $law,
                'name' => 'Department of Private Law',
                'code' => 'PRI',
                'description' => 'Department of Private Law',
            ],

            // School of Science and Technology (SST)
            [
                'school' => $sst,
                'name' => 'Department of Computer Science',
                'code' => 'CSC',
                'description' => 'Department of Computer Science',
            ],
            [
                'school' => $sst,
                'name' => 'Department of Information Technology',
                'code' => 'ITE',
                'description' => 'Department of Information Technology',
            ],

            // School of Education (EDU)
            [
                'school' => $edu,
                'name' => 'Department of Educational Psychology',
                'code' => 'EPS',
                'description' => 'Department of Educational Psychology',
            ],
            [
                'school' => $edu,
                'name' => 'Department of Curriculum Studies',
                'code' => 'CUR',
                'description' => 'Department of Curriculum Studies',
            ],
        ];

        // Add Health Sciences departments if campus exists
        if ($shs && $kampalaCampus) {
            $departments[] = [
                'school' => $shs,
                'name' => 'Department of Nursing',
                'code' => 'NRS',
                'description' => 'Department of Nursing',
            ];
            $departments[] = [
                'school' => $shs,
                'name' => 'Department of Public Health',
                'code' => 'PBH',
                'description' => 'Department of Public Health',
            ];
        }

        // Create departments
        foreach ($departments as $deptData) {
            $department = Department::updateOrCreate(
                [
                    'school_id' => $deptData['school']->id,
                    'name' => $deptData['name'],
                ],
                [
                    'code' => $deptData['code'],
                    'campus_id' => $deptData['school']->campus_id,
                    'description' => $deptData['description'],
                    'status' => 'active',
                ]
            );
            $this->command->info('✓ Department created: '.$department->name);
        }

        $this->command->info('✓ Total departments created: '.count($departments));
    }
}
