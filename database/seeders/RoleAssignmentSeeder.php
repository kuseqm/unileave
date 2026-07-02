<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class RoleAssignmentSeeder extends Seeder
{
    public function run()
    {
        // Create all roles (if not exist) - UPDATED WITH NEW ROLES
        $roles = [
            // Existing roles
            'admin' => 'System Administrator - Full system control',
            'staff' => 'Regular staff - Apply leave, view balance',
            'hod' => 'Head of Department - Approve departmental requests',
            'dean' => 'Faculty Dean - Approve faculty leave',
            'dvca' => 'DVC Academic - Approve academic location permissions',
            'dvc-pfa' => 'DVC PFA - Approve admin/support location permissions',
            'acting-dvc' => 'Acting DVC - Temporary DVC',
            'hr-officer' => 'HR Officer - Final approvals, reports',
            'hr-manager' => 'HR Manager - Override balance, audit logs',
            
            // NEW ROLES
            'vice-chancellor' => 'Vice Chancellor - Institution-wide oversight dashboard',
            'internal-auditor' => 'Internal Auditor - Read-only access to all leave data for compliance',
            'deputy-hod' => 'Deputy Head of Department - Approve when HOD is on leave',
            'dept-secretary' => 'Department Secretary - View department leave, prepare documents',
        ];

        foreach ($roles as $roleName => $description) {
            Role::firstOrCreate([
                'name' => $roleName,
                'guard_name' => 'web',
            ]);
            $this->command->info("Role '{$roleName}' created/verified.");
        }

        // Create new permissions for the new roles
        $permissions = [
            // Vice Chancellor permissions
            ['name' => 'view_institution_dashboard', 'guard_name' => 'web'],
            ['name' => 'view_all_leave_summary', 'guard_name' => 'web'],
            ['name' => 'view_institution_reports', 'guard_name' => 'web'],
            
            // Internal Auditor permissions
            ['name' => 'view_audit_trail', 'guard_name' => 'web'],
            ['name' => 'view_compliance_reports', 'guard_name' => 'web'],
            ['name' => 'export_audit_data', 'guard_name' => 'web'],
            ['name' => 'view_all_leave_history', 'guard_name' => 'web'],
            
            // Deputy HOD permissions
            ['name' => 'approve_department_leave', 'guard_name' => 'web'],
            ['name' => 'delegate_approval', 'guard_name' => 'web'],
            ['name' => 'view_department_reports', 'guard_name' => 'web'],
            
            // Department Secretary permissions
            ['name' => 'prepare_leave_documents', 'guard_name' => 'web'],
            ['name' => 'submit_leave_on_behalf', 'guard_name' => 'web'],
            ['name' => 'track_department_leave', 'guard_name' => 'web'],
        ];

        foreach ($permissions as $perm) {
            \Spatie\Permission\Models\Permission::firstOrCreate($perm);
        }

        // Assign permissions to new roles
        $vcRole = Role::findByName('vice-chancellor');
        $vcRole->givePermissionTo([
            'view_institution_dashboard',
            'view_all_leave_summary',
            'view_institution_reports',
        ]);

        $auditorRole = Role::findByName('internal-auditor');
        $auditorRole->givePermissionTo([
            'view_audit_trail',
            'view_compliance_reports',
            'export_audit_data',
            'view_all_leave_history',
        ]);

        $deputyHodRole = Role::findByName('deputy-hod');
        $deputyHodRole->givePermissionTo([
            'approve_department_leave',
            'delegate_approval',
            'view_department_reports',
        ]);

        $deptSecretaryRole = Role::findByName('dept-secretary');
        $deptSecretaryRole->givePermissionTo([
            'prepare_leave_documents',
            'submit_leave_on_behalf',
            'track_department_leave',
        ]);

        // Assign roles to users by email - USING CORRECT DOMAIN @smut.ac.tz
        $assignments = [
            // Existing assignments
            'admin@smut.ac.tz' => 'admin',
            'sarah.johnson@smut.ac.tz' => 'hr-officer',
            'robert.kibona@smut.ac.tz' => 'dvca',
            'grace.mtenga@smut.ac.tz' => 'dvc-pfa',
            'michael.mushi@smut.ac.tz' => 'hod',
            'david.mrema@smut.ac.tz' => 'hod',
            'catherine.nnko@smut.ac.tz' => 'hod',
            'elizabeth.mlay@smut.ac.tz' => 'hod',
            'robert.kivuyo@smut.ac.tz' => 'hod',
            'joseph.mtaki@smut.ac.tz' => 'hod',
            'mary.mwita@smut.ac.tz' => 'hod',
            'patricia.mwakyusa@smut.ac.tz' => 'hod',
            'richard.mwakipesile@smut.ac.tz' => 'dean',
            'catherine.ndunguru@smut.ac.tz' => 'dean',
            'peter.msoffe@smut.ac.tz' => 'dean',
            'alice.lema@smut.ac.tz' => 'dean',
            'james.mwakyusa@smut.ac.tz' => 'dean',
            'john.mushi@smut.ac.tz' => 'staff',
            'mary.lema@smut.ac.tz' => 'staff',
            'peter.ndyetabura@smut.ac.tz' => 'staff',
            'anna.kivuyo@smut.ac.tz' => 'staff',
            'james.mwita@smut.ac.tz' => 'staff',
            'grace.mkude@smut.ac.tz' => 'staff',
            'thomas.mloka@smut.ac.tz' => 'staff',
            'esther.mrema@smut.ac.tz' => 'staff',
            'daniel.mlay@smut.ac.tz' => 'staff',
            'sarah.mwaipopo@smut.ac.tz' => 'staff',
            
            // NEW ASSIGNMENTS FOR NEW ROLES (update emails as needed)
            // Vice Chancellor - needs a user email
            // 'vice.chancellor@smut.ac.tz' => 'vice-chancellor',
            
            // Internal Auditor - needs a user email
            // 'auditor@smut.ac.tz' => 'internal-auditor',
            
            // Deputy HOD - example (replace with actual emails)
            // 'deputy.hod.accounting@smut.ac.tz' => 'deputy-hod',
            
            // Department Secretary - example (replace with actual emails)
            // 'secretary.accounting@smut.ac.tz' => 'dept-secretary',
        ];

        foreach ($assignments as $email => $roleName) {
            $user = User::where('email', $email)->first();
            if ($user) {
                // Check if user already has roles
                $existingRoles = $user->getRoleNames();
                if ($existingRoles->count() > 0 && $existingRoles->first() !== $roleName) {
                    $this->command->warn("User {$email} already has role '{$existingRoles->first()}', skipping '{$roleName}'");
                    continue;
                }
                $user->assignRole($roleName);
                $this->command->info("Assigned '{$roleName}' to {$email}");
            } else {
                $this->command->warn("User not found: {$email}");
            }
        }

        // Display summary of all roles and their counts
        $this->command->newLine();
        $this->command->info('=== ROLE ASSIGNMENT SUMMARY ===');
        foreach (Role::all() as $role) {
            $count = User::role($role->name)->count();
            $this->command->line("{$role->name}: {$count} users");
        }
    }
}