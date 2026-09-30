<?php

namespace Database\Seeders;

use App\Models\Campus;
use App\Models\Department;
use App\Models\LeaveRequest;
use App\Models\School;
use App\Models\University;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class HodSetupSeeder extends Seeder
{
    public function run()
    {
        // 1. Get or create a university (find the first one)
        $university = University::first();
        if (! $university) {
            $university = University::create([
                'name' => 'Saint Mary\'s University',
                'status' => 'active',
            ]);
            $this->command->info('✓ Created default university');
        }

        // 2. Get or create a campus
        $campus = Campus::first();
        if (! $campus) {
            $campus = Campus::create([
                'university_id' => $university->id,
                'name' => 'Main Campus',
                'status' => 'active',
            ]);
            $this->command->info('✓ Created default campus');
        }

        // 3. Get or create a school
        $school = School::first();
        if (! $school) {
            $school = School::create([
                'campus_id' => $campus->id,
                'name' => 'School of Business',
                'status' => 'active',
            ]);
            $this->command->info('✓ Created default school');
        }

        // 4. Create HOD user with ALL required fields
        $hodUser = User::firstOrCreate(
            ['email' => 'hod@smut.ac.tz'],
            [
                'staff_id' => 'HOD001',
                'university_id' => $university->id,
                'campus_id' => $campus->id,
                'school_id' => $school->id,
                'department_id' => 1,
                'first_name' => 'John',
                'last_name' => 'HOD',
                'email' => 'hod@smut.ac.tz',
                'password' => Hash::make('password'),
                'status' => 'active',
                'staff_category' => 'administrative',
                'employment_status' => 'active',
                'joining_date' => now(),
            ]
        );

        $this->command->info('✓ HOD user created/verified: '.$hodUser->email);
        $this->command->info('  User ID: '.$hodUser->id);

        // 5. Assign HOD to department
        $department = Department::find(1);
        if ($department) {
            $department->update(['hod_staff_id' => $hodUser->id]);
            $this->command->info('✓ HOD assigned to department: '.$department->name);
        }

        // 6. Assign HOD role
        if (! $hodUser->hasRole('hod')) {
            $hodUser->assignRole('hod');
            $this->command->info('✓ HOD role assigned');
        }

        // 7. Fix leave request level
        $leaveRequest = LeaveRequest::find(1);
        if ($leaveRequest) {
            $oldLevel = $leaveRequest->current_approval_level;
            $leaveRequest->update(['current_approval_level' => 1]);
            $this->command->info('✓ Leave request level fixed: '.$oldLevel.' → 1 (HOD)');
        }

        // 8. Summary
        $this->command->newLine();
        $this->command->info('═══════════════════════════════════════════');
        $this->command->info('           HOD SETUP COMPLETE              ');
        $this->command->info('═══════════════════════════════════════════');
        $this->command->info('📧 Email: hod@smut.ac.tz');
        $this->command->info('🔑 Password: password');
        $this->command->info('🏢 Department: '.($department->name ?? 'N/A'));
        $this->command->info('🆔 Department HOD ID: '.($department->hod_staff_id ?? 'N/A'));
        $this->command->info('📋 Leave Level: '.($leaveRequest->current_approval_level ?? 'N/A'));
        $this->command->info('═══════════════════════════════════════════');
    }
}
