<?php

// database/migrations/xxxx_add_new_roles_to_permission_table.php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    public function up()
    {
        // Create new roles
        $roles = [
            ['name' => 'vice-chancellor', 'guard_name' => 'web'],
            ['name' => 'internal-auditor', 'guard_name' => 'web'],
            ['name' => 'deputy-hod', 'guard_name' => 'web'],
            ['name' => 'dept-secretary', 'guard_name' => 'web'],
            ['name' => 'security-officer', 'guard_name' => 'web'],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate($role);
        }

        // Create new permissions
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

            // Security Officer permissions
            ['name' => 'verify_qr_codes', 'guard_name' => 'web'],
            ['name' => 'view_gate_entry_logs', 'guard_name' => 'web'],
            ['name' => 'record_entry_exit', 'guard_name' => 'web'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate($permission);
        }

        // Assign permissions to roles
        // Vice Chancellor gets view permissions
        $vcRole = Role::findByName('vice-chancellor');
        $vcRole->givePermissionTo([
            'view_institution_dashboard',
            'view_all_leave_summary',
            'view_institution_reports',
        ]);

        // Internal Auditor gets read-only permissions
        $auditorRole = Role::findByName('internal-auditor');
        $auditorRole->givePermissionTo([
            'view_audit_trail',
            'view_compliance_reports',
            'export_audit_data',
            'view_all_leave_history',
        ]);
    }

    public function down()
    {
        // Remove permissions first
        $permissions = [
            'view_institution_dashboard',
            'view_all_leave_summary',
            'view_institution_reports',
            'view_audit_trail',
            'view_compliance_reports',
            'export_audit_data',
            'view_all_leave_history',
            'approve_department_leave',
            'delegate_approval',
            'view_department_reports',
            'prepare_leave_documents',
            'submit_leave_on_behalf',
            'track_department_leave',
            'verify_qr_codes',
            'view_gate_entry_logs',
            'record_entry_exit',
        ];

        foreach ($permissions as $permission) {
            $perm = Permission::findByName($permission);
            if ($perm) {
                $perm->delete();
            }
        }

        // Remove roles
        $roles = [
            'vice-chancellor',
            'internal-auditor',
            'deputy-hod',
            'dept-secretary',
            'security-officer',
        ];

        foreach ($roles as $role) {
            $r = Role::findByName($role);
            if ($r) {
                $r->delete();
            }
        }
    }
};
