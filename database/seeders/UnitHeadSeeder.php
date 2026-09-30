<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class UnitHeadSeeder extends Seeder
{
    public function run()
    {
        // Create unit_head role if not exists
        $role = Role::firstOrCreate([
            'name' => 'unit_head',
            'guard_name' => 'web',
        ]);

        $this->command->info('✓ unit_head role created/exists');

        // Assign a Unit Head to each department that doesn't have one
        // Using existing administrative staff as Unit Heads

        $assignments = [
            // Department name => Unit Head email
            'Human Resources' => 'sarah.johnson@smut.ac.tz',
            'Department of Accounting and Finance' => 'grace.mtenga@smut.ac.tz',
            'Department of Marketing and Entrepreneurship' => 'thomas.mloka@smut.ac.tz',
            'Department of Computer Science' => 'robert.kivuyo@smut.ac.tz',
            'Department of Information Technology' => 'joseph.mtaki@smut.ac.tz',
            'Department of Educational Psychology' => 'mary.mwita@smut.ac.tz',
            'Department of Curriculum Studies' => 'patricia.mwakyusa@smut.ac.tz',
            'Department of Public Law' => 'catherine.nnko@smut.ac.tz',
            'Department of Private Law' => 'elizabeth.mlay@smut.ac.tz',
        ];

        foreach ($assignments as $deptName => $email) {
            $department = Department::where('name', $deptName)->first();
            $unitHead = User::where('email', $email)->first();

            if ($department && $unitHead) {
                $department->update(['unit_head_staff_id' => $unitHead->id]);
                $unitHead->assignRole('unit_head');
                $this->command->info("✓ Assigned {$email} as Unit Head of {$deptName}");
            } else {
                if (! $department) {
                    $this->command->warn("Department not found: {$deptName}");
                }
                if (! $unitHead) {
                    $this->command->warn("User not found: {$email}");
                }
            }
        }

        $this->command->info('✓ Unit Head assignments completed!');
    }
}
