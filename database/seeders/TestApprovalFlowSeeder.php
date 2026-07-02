<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Department;
use App\Models\School;
use App\Models\University;
use App\Models\Campus;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\LeaveBalance;
use App\Models\HoldPositionHistory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TestApprovalFlowSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('TEST APPROVAL FLOW SEEDER - STARTING');
        
        // ============================================
        // GET EXISTING DATA
        // ============================================
        
        $university = University::first();
        if (!$university) {
            $this->command->error('No university found! Run UniversitySeeder first.');
            return;
        }
        
        $campus = Campus::first();
        if (!$campus) {
            $this->command->error('No campus found! Run CampusSeeder first.');
            return;
        }
        
        $school = School::first();
        if (!$school) {
            $this->command->error('No school found! Run SchoolSeeder first.');
            return;
        }
        
        $department = Department::first();
        if (!$department) {
            $this->command->error('No department found! Run DepartmentSeeder first.');
            return;
        }
        
        // Get leave types
        $annualLeave = LeaveType::where('code', 'AL')->first();
        if (!$annualLeave) {
            $annualLeave = LeaveType::first();
        }
        
        $sickLeave = LeaveType::where('code', 'SL')->first();
        if (!$sickLeave) {
            $sickLeave = LeaveType::skip(1)->first();
        }
        
        // Helper function to generate random staff ID
        $generateStaffId = function($prefix) {
            $random = rand(100, 999);
            $staffId = $prefix . $random;
            // Check if exists, if so add another random number
            while (User::where('staff_id', $staffId)->exists()) {
                $staffId = $prefix . rand(100, 999);
            }
            return $staffId;
        };
        
        // ============================================
        // ACADEMIC STAFF FLOW USERS
        // ============================================
        
        // 1. Academic Requester
        $academicRequester = User::firstOrCreate(
            ['email' => 'john.mwalimu@smut.ac.tz'],
            [
                'staff_id' => $generateStaffId('ACAD'),
                'university_id' => $university->id,
                'campus_id' => $campus->id,
                'school_id' => $school->id,
                'department_id' => $department->id,
                'first_name' => 'John',
                'last_name' => 'Mwalimu',
                'email' => 'john.mwalimu@smut.ac.tz',
                'password' => Hash::make('Temp@123'),
                'status' => 'active',
                'staff_category' => 'academic',
                'employment_status' => 'active',
                'joining_date' => now(),
            ]
        );
        $academicRequester->syncRoles(['staff']);
        $this->command->info('✓ John Mwalimu (Academic Requester) - ID: ' . $academicRequester->staff_id);
        
        // 2. Academic Acting Staff
        $academicActing = User::firstOrCreate(
            ['email' => 'sarah.mkufunzi@smut.ac.tz'],
            [
                'staff_id' => $generateStaffId('ACAD'),
                'university_id' => $university->id,
                'campus_id' => $campus->id,
                'school_id' => $school->id,
                'department_id' => $department->id,
                'first_name' => 'Sarah',
                'last_name' => 'Mkufunzi',
                'email' => 'sarah.mkufunzi@smut.ac.tz',
                'password' => Hash::make('Temp@123'),
                'status' => 'active',
                'staff_category' => 'academic',
                'employment_status' => 'active',
                'joining_date' => now(),
            ]
        );
        $academicActing->syncRoles(['staff']);
        $this->command->info('✓ Sarah Mkufunzi (Academic Acting) - ID: ' . $academicActing->staff_id);
        
        // 3. HOD
        $hod = User::firstOrCreate(
            ['email' => 'james.mwakyusa@smut.ac.tz'],
            [
                'staff_id' => $generateStaffId('HOD'),
                'university_id' => $university->id,
                'campus_id' => $campus->id,
                'school_id' => $school->id,
                'department_id' => $department->id,
                'first_name' => 'James',
                'last_name' => 'Mwakyusa',
                'email' => 'james.mwakyusa@smut.ac.tz',
                'password' => Hash::make('Temp@123'),
                'status' => 'active',
                'staff_category' => 'academic',
                'employment_status' => 'active',
                'joining_date' => now(),
            ]
        );
        $hod->syncRoles(['hod']);
        $department->update(['hod_staff_id' => $hod->id]);
        $this->command->info('✓ James Mwakyusa (HOD) - ID: ' . $hod->staff_id);
        
        // 4. Dean
        $dean = User::firstOrCreate(
            ['email' => 'mary.ndunguru@smut.ac.tz'],
            [
                'staff_id' => $generateStaffId('DEAN'),
                'university_id' => $university->id,
                'campus_id' => $campus->id,
                'school_id' => $school->id,
                'department_id' => $department->id,
                'first_name' => 'Mary',
                'last_name' => 'Ndunguru',
                'email' => 'mary.ndunguru@smut.ac.tz',
                'password' => Hash::make('Temp@123'),
                'status' => 'active',
                'staff_category' => 'academic',
                'employment_status' => 'active',
                'joining_date' => now(),
            ]
        );
        $dean->syncRoles(['dean']);
        $school->update(['dean_staff_id' => $dean->id]);
        $this->command->info('✓ Mary Ndunguru (Dean) - ID: ' . $dean->staff_id);
        
        // 5. DVCA
        $dvca = User::firstOrCreate(
            ['email' => 'robert.kibona@smut.ac.tz'],
            [
                'staff_id' => $generateStaffId('DVCA'),
                'university_id' => $university->id,
                'campus_id' => $campus->id,
                'school_id' => $school->id,
                'department_id' => $department->id,
                'first_name' => 'Robert',
                'last_name' => 'Kibona',
                'email' => 'robert.kibona@smut.ac.tz',
                'password' => Hash::make('Temp@123'),
                'status' => 'active',
                'staff_category' => 'academic',
                'employment_status' => 'active',
                'joining_date' => now(),
            ]
        );
        $dvca->syncRoles(['dvca']);
        $this->command->info('✓ Robert Kibona (DVCA) - ID: ' . $dvca->staff_id);
        
        // ============================================
        // ADMINISTRATIVE STAFF FLOW USERS
        // ============================================
        
        // 6. Administrative Requester
        $adminRequester = User::firstOrCreate(
            ['email' => 'peter.mushi@smut.ac.tz'],
            [
                'staff_id' => $generateStaffId('ADM'),
                'university_id' => $university->id,
                'campus_id' => $campus->id,
                'school_id' => $school->id,
                'department_id' => $department->id,
                'first_name' => 'Peter',
                'last_name' => 'Mushi',
                'email' => 'peter.mushi@smut.ac.tz',
                'password' => Hash::make('Temp@123'),
                'status' => 'active',
                'staff_category' => 'administrative',
                'employment_status' => 'active',
                'joining_date' => now(),
            ]
        );
        $adminRequester->syncRoles(['staff']);
        $this->command->info('✓ Peter Mushi (Admin Requester) - ID: ' . $adminRequester->staff_id);
        
        // 7. Administrative Acting Staff
        $adminActing = User::firstOrCreate(
            ['email' => 'anna.kivuyo@smut.ac.tz'],
            [
                'staff_id' => $generateStaffId('ADM'),
                'university_id' => $university->id,
                'campus_id' => $campus->id,
                'school_id' => $school->id,
                'department_id' => $department->id,
                'first_name' => 'Anna',
                'last_name' => 'Kivuyo',
                'email' => 'anna.kivuyo@smut.ac.tz',
                'password' => Hash::make('Temp@123'),
                'status' => 'active',
                'staff_category' => 'administrative',
                'employment_status' => 'active',
                'joining_date' => now(),
            ]
        );
        $adminActing->syncRoles(['staff']);
        $this->command->info('✓ Anna Kivuyo (Admin Acting) - ID: ' . $adminActing->staff_id);
        
        // 8. Head of Unit
        $unitHead = User::firstOrCreate(
            ['email' => 'joseph.mtaki@smut.ac.tz'],
            [
                'staff_id' => $generateStaffId('UNIT'),
                'university_id' => $university->id,
                'campus_id' => $campus->id,
                'school_id' => $school->id,
                'department_id' => $department->id,
                'first_name' => 'Joseph',
                'last_name' => 'Mtaki',
                'email' => 'joseph.mtaki@smut.ac.tz',
                'password' => Hash::make('Temp@123'),
                'status' => 'active',
                'staff_category' => 'administrative',
                'employment_status' => 'active',
                'joining_date' => now(),
            ]
        );
        $unitHead->syncRoles(['unit_head']);
        $this->command->info('✓ Joseph Mtaki (Head of Unit) - ID: ' . $unitHead->staff_id);
        
        // 9. Director
        $director = User::firstOrCreate(
            ['email' => 'catherine.ndunguru@smut.ac.tz'],
            [
                'staff_id' => $generateStaffId('DIR'),
                'university_id' => $university->id,
                'campus_id' => $campus->id,
                'school_id' => $school->id,
                'department_id' => $department->id,
                'first_name' => 'Catherine',
                'last_name' => 'Ndunguru',
                'email' => 'catherine.ndunguru@smut.ac.tz',
                'password' => Hash::make('Temp@123'),
                'status' => 'active',
                'staff_category' => 'administrative',
                'employment_status' => 'active',
                'joining_date' => now(),
            ]
        );
        $director->syncRoles(['dean']);
        $this->command->info('✓ Catherine Ndunguru (Director) - ID: ' . $director->staff_id);
        
        // 10. DVCPFA
        $dvcPfa = User::firstOrCreate(
            ['email' => 'grace.mtenga@smut.ac.tz'],
            [
                'staff_id' => $generateStaffId('DVC'),
                'university_id' => $university->id,
                'campus_id' => $campus->id,
                'school_id' => $school->id,
                'department_id' => $department->id,
                'first_name' => 'Grace',
                'last_name' => 'Mtenga',
                'email' => 'grace.mtenga@smut.ac.tz',
                'password' => Hash::make('Temp@123'),
                'status' => 'active',
                'staff_category' => 'administrative',
                'employment_status' => 'active',
                'joining_date' => now(),
            ]
        );
        $dvcPfa->syncRoles(['dvc-pfa']);
        $this->command->info('✓ Grace Mtenga (DVCPFA) - ID: ' . $dvcPfa->staff_id);
        
        // 11. HR Officer
        $hrOfficer = User::firstOrCreate(
            ['email' => 'sarah.johnson@smut.ac.tz'],
            [
                'staff_id' => $generateStaffId('HR'),
                'university_id' => $university->id,
                'campus_id' => $campus->id,
                'school_id' => $school->id,
                'department_id' => $department->id,
                'first_name' => 'Sarah',
                'last_name' => 'Johnson',
                'email' => 'sarah.johnson@smut.ac.tz',
                'password' => Hash::make('Temp@123'),
                'status' => 'active',
                'staff_category' => 'administrative',
                'employment_status' => 'active',
                'joining_date' => now(),
            ]
        );
        $hrOfficer->syncRoles(['hr-officer']);
        $this->command->info('✓ Sarah Johnson (HR Officer) - ID: ' . $hrOfficer->staff_id);
        
        // ============================================
        // CREATE LEAVE BALANCES
        // ============================================
        
        $currentYear = date('Y');
 
        $this->command->info('✓ Leave balances created');
        
        // ============================================
        // CREATE LEAVE REQUESTS
        // ============================================
        
        // Check if leave requests already exist
        $existingAcademicRequest = LeaveRequest::where('staff_id', $academicRequester->id)->first();
        if (!$existingAcademicRequest) {
            $academicLeaveRequest = LeaveRequest::create([
                'staff_id' => $academicRequester->id,
                'acting_staff_id' => $academicActing->id,
                'leave_type_id' => $annualLeave->id,
                'start_date' => now()->addDays(7)->format('Y-m-d'),
                'end_date' => now()->addDays(14)->format('Y-m-d'),
                'days' => 8,
                'reason' => 'Annual leave for family vacation - Academic Staff',
                'status' => 'pending',
                'acting_status' => 'pending',
                'acting_token' => Str::random(60),
                'current_approval_level' => 1,
                'created_at' => now(),
            ]);
            $this->command->info('✓ Academic Leave Request #' . $academicLeaveRequest->id);
            
            HoldPositionHistory::create([
                'leave_request_id' => $academicLeaveRequest->id,
                'requester_staff_id' => $academicRequester->id,
                'holder_staff_id' => $academicActing->id,
                'status' => 'pending',
                'requested_at' => now(),
                'token' => $academicLeaveRequest->acting_token,
            ]);
        } else {
            $this->command->info('⚠ Academic Leave Request already exists');
        }
        
        $existingAdminRequest = LeaveRequest::where('staff_id', $adminRequester->id)->first();
        if (!$existingAdminRequest) {
            $adminLeaveRequest = LeaveRequest::create([
                'staff_id' => $adminRequester->id,
                'acting_staff_id' => $adminActing->id,
                'leave_type_id' => $annualLeave->id,
                'start_date' => now()->addDays(14)->format('Y-m-d'),
                'end_date' => now()->addDays(21)->format('Y-m-d'),
                'days' => 8,
                'reason' => 'Annual leave for personal matters - Administrative Staff',
                'status' => 'pending',
                'acting_status' => 'pending',
                'acting_token' => Str::random(60),
                'current_approval_level' => 1,
                'created_at' => now(),
            ]);
            $this->command->info('✓ Admin Leave Request #' . $adminLeaveRequest->id);
            
            HoldPositionHistory::create([
                'leave_request_id' => $adminLeaveRequest->id,
                'requester_staff_id' => $adminRequester->id,
                'holder_staff_id' => $adminActing->id,
                'status' => 'pending',
                'requested_at' => now(),
                'token' => $adminLeaveRequest->acting_token,
            ]);
        } else {
            $this->command->info('⚠ Admin Leave Request already exists');
        }
        
        // ============================================
        // SUMMARY
        // ============================================
        
        $this->command->newLine();
        $this->command->info('========== TEST APPROVAL FLOW READY! ==========');
        $this->command->newLine();
        
        $this->command->info('ACADEMIC FLOW (Password: Temp@123):');
        $this->command->info('  1. John Mwalimu - john.mwalimu@smut.ac.tz (Staff)');
        $this->command->info('  2. Sarah Mkufunzi - sarah.mkufunzi@smut.ac.tz (Acting)');
        $this->command->info('  3. James Mwakyusa - james.mwakyusa@smut.ac.tz (HOD)');
        $this->command->info('  4. Mary Ndunguru - mary.ndunguru@smut.ac.tz (Dean)');
        $this->command->info('  5. Robert Kibona - robert.kibona@smut.ac.tz (DVCA)');
        $this->command->newLine();
        
        $this->command->info('ADMINISTRATIVE FLOW (Password: Temp@123):');
        $this->command->info('  1. Peter Mushi - peter.mushi@smut.ac.tz (Staff)');
        $this->command->info('  2. Anna Kivuyo - anna.kivuyo@smut.ac.tz (Acting)');
        $this->command->info('  3. Joseph Mtaki - joseph.mtaki@smut.ac.tz (Head of Unit)');
        $this->command->info('  4. Catherine Ndunguru - catherine.ndunguru@smut.ac.tz (Director)');
        $this->command->info('  5. Grace Mtenga - grace.mtenga@smut.ac.tz (DVCPFA)');
        $this->command->newLine();
        
        $this->command->info('SUPPORT ROLES:');
        $this->command->info('  Sarah Johnson - sarah.johnson@smut.ac.tz (HR Officer)');
        $this->command->newLine();
        
        $this->command->info('===============================================');
    }
}