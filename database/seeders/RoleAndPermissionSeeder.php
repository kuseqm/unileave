<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleAndPermissionSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // ============================================
        // CREATE ALL PERMISSIONS
        // ============================================

        $permissions = [
            // Staff Management Permissions
            'view_staff',
            'create_staff',
            'edit_staff',
            'delete_staff',
            'import_staff',
            'export_staff',
            'reset_staff_password',

            // Leave Request Permissions
            'apply_leave',
            'view_own_leave',
            'view_department_leave',
            'view_school_leave',
            'view_all_leave',
            'approve_hod_level',
            'approve_dean_level',
            'approve_hr_level',
            'approve_dvc_level',
            'approve_hr_logistics',
            'reject_leave',
            'cancel_own_leave',

            // Acting/Hold Position Permissions
            'request_acting_staff',
            'accept_acting_request',
            'view_acting_requests',
            'view_acting_assignments',

            // Report Permissions
            'view_reports',
            'export_pdf',
            'export_excel',
            'view_leave_summary',
            'view_staff_directory',
            'view_leave_balance',
            'view_pending_approvals',

            // Institution Dashboard Permission
            'view_institution_dashboard',

            // Letter/Document Permissions
            'sign_vc_letters',

            // Audit & Compliance Permissions
            'view_audit_logs',
            'view_compliance_reports',
            'export_audit_data',

            // Settings Permissions
            'view_settings',
            'edit_settings',
            'manage_leave_types',
            'manage_blackout_periods',

            // Policy Management Permissions
            'view_policies',
            'create_policies',
            'edit_policies',
            'delete_policies',
            'approve_policies',
            'publish_policies',
            'archive_policies',

            // Institute Structure Permissions
            'manage_universities',
            'manage_campuses',
            'manage_schools',
            'manage_departments',

            // Role Management Permissions
            'manage_roles',
            'assign_roles',
            'view_roles',

            // Backup Permissions
            'create_backup',
            'restore_backup',
            'delete_backup',

            // Acting DVC Permissions
            'manage_acting_dvc',
            'approve_acting_dvc',

            // Leave orphan permissions
            'submit_leave_on_behalf',
            'prepare_leave_documents',
            'approve_department_leave',
            'delegate_approval',
            'track_department_leave',

            // ID Card Permissions (no edit/update route exists — cards are immutable once issued)
            'view_id_cards',
            'create_id_cards',
            'delete_id_cards',

            // Authorized Signature Permissions
            'view_authorized_signatures',
            'create_authorized_signatures',
            'edit_authorized_signatures',
            'delete_authorized_signatures',

            // Route Group Permissions
            'admin_access',
            'manage_registry',
            'manage_tenants',

            // Academic Records Permissions (students / graduation verification)
            'view_students',
            'generate_graduation_letters',
            'import_students',

            // Certificate / transcript verification letters: only the Examinations
            // Officer prepares them; choosing who may sign is an administrator's job.
            'prepare_verification_letters',
            'manage_letter_signatories',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $this->command->info('Created '.count($permissions).' permissions.');

        // ============================================
        // CREATE ALL ROLES
        // ============================================

        $roles = [
            'super-admin' => [
                'description' => 'Super Administrator - Full system control across all institutions',
                'permissions' => Permission::pluck('name')->toArray(),
            ],
            'admin' => [
                'description' => 'System Administrator - Full system control',
                'permissions' => Permission::pluck('name')->toArray(),
            ],
            'vice-chancellor' => [
                'description' => 'Vice Chancellor - Institution-wide oversight',
                'permissions' => [
                    'view_staff',
                    'apply_leave',
                    'view_own_leave',
                    'view_all_leave',
                    'view_reports',
                    'view_leave_summary',
                    'view_staff_directory',
                    'view_leave_balance',
                    'view_pending_approvals',
                    'view_audit_logs',
                    'export_pdf',
                    'export_excel',
                    'sign_vc_letters',
                    'view_institution_dashboard',
                    'view_policies',
                    'approve_policies',
                ],
            ],
            'internal-auditor' => [
                'description' => 'Internal Auditor - Read-only access for compliance',
                'permissions' => [
                    'view_staff',
                    'apply_leave',
                    'view_own_leave',
                    'view_all_leave',
                    'view_reports',
                    'view_leave_summary',
                    'view_audit_logs',
                    'view_compliance_reports',
                    'export_audit_data',
                ],
            ],
            'registrar' => [
                'description' => 'Registrar - Read-only oversight of every in-flight leave, permission, and travel request; cannot approve, reject, or stop any of them',
                'permissions' => [
                    'view_staff',
                    'apply_leave',
                    'view_own_leave',
                    'view_all_leave',
                    'view_pending_approvals',
                    'view_reports',
                    'view_students',
                ],
            ],
            'examinations-officer' => [
                'description' => 'Examinations Officer - Owns the Graduation Roster: imports graduated-student data from the registry\'s graduation books and generates alumni letters from it',
                'permissions' => [
                    'view_staff',
                    'apply_leave',
                    'view_own_leave',
                    'view_students',
                    'import_students',
                    'generate_graduation_letters',
                    'prepare_verification_letters',
                ],
            ],
            'dvca' => [
                'description' => 'Deputy Vice Chancellor (Academic)',
                'permissions' => [
                    'view_staff',
                    'apply_leave',
                    'view_own_leave',
                    'view_school_leave',
                    'approve_dvc_level',
                    'reject_leave',
                    'view_reports',
                    'view_pending_approvals',
                    'export_pdf',
                ],
            ],
            'dvc-pfa' => [
                'description' => 'Deputy Vice Chancellor (Planning, Finance & Admin)',
                'permissions' => [
                    'view_staff',
                    'apply_leave',
                    'view_own_leave',
                    'view_school_leave',
                    'approve_dvc_level',
                    'reject_leave',
                    'view_reports',
                    'view_pending_approvals',
                    'export_pdf',
                ],
            ],
            'dean' => [
                'description' => 'Dean of Faculty',
                'permissions' => [
                    'view_staff',
                    'apply_leave',
                    'view_own_leave',
                    'view_school_leave',
                    'approve_dean_level',
                    'reject_leave',
                    'view_reports',
                    'view_pending_approvals',
                    'export_pdf',
                ],
            ],
            'hod' => [
                'description' => 'Head of Department',
                'permissions' => [
                    'view_staff',
                    'apply_leave',
                    'view_own_leave',
                    'view_department_leave',
                    'approve_hod_level',
                    'reject_leave',
                    'view_reports',
                    'view_pending_approvals',
                    'export_pdf',
                ],
            ],
            'deputy-hod' => [
                'description' => 'Deputy Head of Department - Approves when HOD is on leave',
                'permissions' => [
                    'view_staff',
                    'apply_leave',
                    'view_own_leave',
                    'view_department_leave',
                    'approve_hod_level',
                    'view_reports',
                    'view_pending_approvals',
                ],
            ],
            'dept-secretary' => [
                'description' => 'Department Secretary - Administrative support',
                'permissions' => [
                    'view_staff',
                    'apply_leave',
                    'view_own_leave',
                    'view_department_leave',
                    'view_reports',
                    'export_pdf',
                ],
            ],
            'hr-officer' => [
                'description' => 'Human Resources Officer',
                'permissions' => [
                    'view_staff',
                    'apply_leave',
                    'view_own_leave',
                    'create_staff',
                    'edit_staff',
                    'import_staff',
                    'export_staff',
                    'view_all_leave',
                    'approve_hr_level',
                    'approve_hr_logistics',
                    'reject_leave',
                    'view_reports',
                    'export_pdf',
                    'export_excel',
                    'view_audit_logs',
                    'view_settings',
                    'manage_blackout_periods',
                    'view_id_cards',
                    'create_id_cards',
                    'view_policies',
                    'create_policies',
                ],
            ],
            'hr-manager' => [
                'description' => 'Human Resources Manager',
                'permissions' => [
                    'view_staff',
                    'apply_leave',
                    'view_own_leave',
                    'create_staff',
                    'edit_staff',
                    'delete_staff',
                    'import_staff',
                    'export_staff',
                    'reset_staff_password',
                    'view_all_leave',
                    'approve_hr_level',
                    'approve_hr_logistics',
                    'reject_leave',
                    'view_reports',
                    'export_pdf',
                    'export_excel',
                    'view_audit_logs',
                    'view_settings',
                    'edit_settings',
                    'manage_leave_types',
                    'manage_blackout_periods',
                    'create_backup',
                    'view_id_cards',
                    'create_id_cards',
                    'delete_id_cards',
                    'view_authorized_signatures',
                    'create_authorized_signatures',
                    'edit_authorized_signatures',
                    'view_policies',
                    'create_policies',
                    'edit_policies',
                    'delete_policies',
                    'approve_policies',
                    'publish_policies',
                    'archive_policies',
                ],
            ],
            'acting-dvc' => [
                'description' => 'Acting DVC - Temporary assignment',
                'permissions' => [
                    'view_staff',
                    'apply_leave',
                    'view_own_leave',
                    'view_all_leave',
                    'approve_dvc_level',
                    'reject_leave',
                    'view_reports',
                ],
            ],
            'staff' => [
                'description' => 'Regular Staff Member',
                'permissions' => [
                    'apply_leave',
                    'view_own_leave',
                    'cancel_own_leave',
                    'request_acting_staff',
                    'view_acting_requests',
                    'view_acting_assignments',
                ],
            ],
        ];

        foreach ($roles as $roleName => $roleData) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
            $role->syncPermissions($roleData['permissions']);
            $this->command->info("Role '{$roleName}' created with ".count($roleData['permissions']).' permissions.');
        }

        // ============================================
        // ASSIGN SUPER ADMIN ROLE TO FIRST USER (Optional)
        // ============================================

        $superAdminEmail = 'admin@smut.ac.tz';
        $superAdmin = User::where('email', $superAdminEmail)->first();

        if ($superAdmin) {
            $superAdmin->assignRole('super-admin');
            $this->command->info("Assigned 'super-admin' role to {$superAdminEmail}");
        } else {
            $this->command->warn("User not found: {$superAdminEmail}");
        }

        // ============================================
        // DISPLAY SUMMARY
        // ============================================

        $this->command->newLine();
        $this->command->info('=== ROLE ASSIGNMENT SUMMARY ===');
        $this->command->line(str_repeat('-', 40));

        foreach (Role::all() as $role) {
            $count = User::role($role->name)->count();
            $permCount = $role->permissions->count();
            $this->command->line("{$role->name}: {$count} users, {$permCount} permissions");
        }

        $this->command->newLine();
        $this->command->info('Role and Permission seeder completed successfully!');
    }
}
