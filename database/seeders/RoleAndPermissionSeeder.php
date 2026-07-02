<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RoleAndPermissionSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

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
            
            // Audit & Compliance Permissions
            'view_audit_logs',
            'view_compliance_reports',
            'export_audit_data',
            
            // Settings Permissions
            'view_settings',
            'edit_settings',
            'manage_leave_types',
            'manage_blackout_periods',
            
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
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $this->command->info('Created ' . count($permissions) . ' permissions.');

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
                    'view_all_leave',
                    'view_reports',
                    'view_leave_summary',
                    'view_staff_directory',
                    'view_leave_balance',
                    'view_pending_approvals',
                    'view_audit_logs',
                    'export_pdf',
                    'export_excel',
                ],
            ],
            'internal-auditor' => [
                'description' => 'Internal Auditor - Read-only access for compliance',
                'permissions' => [
                    'view_staff',
                    'view_all_leave',
                    'view_reports',
                    'view_leave_summary',
                    'view_audit_logs',
                    'view_compliance_reports',
                    'export_audit_data',
                ],
            ],
            'dvca' => [
                'description' => 'Deputy Vice Chancellor (Academic)',
                'permissions' => [
                    'view_staff',
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
                    'view_department_leave',
                    'view_reports',
                    'export_pdf',
                ],
            ],
            'hr-officer' => [
                'description' => 'Human Resources Officer',
                'permissions' => [
                    'view_staff',
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
                ],
            ],
            'hr-manager' => [
                'description' => 'Human Resources Manager',
                'permissions' => [
                    'view_staff',
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
                ],
            ],
            'acting-dvc' => [
                'description' => 'Acting DVC - Temporary assignment',
                'permissions' => [
                    'view_staff',
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
            $this->command->info("Role '{$roleName}' created with " . count($roleData['permissions']) . " permissions.");
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