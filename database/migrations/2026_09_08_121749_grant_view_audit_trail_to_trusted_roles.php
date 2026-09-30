<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    /**
     * AuditTrailController's own ROLES_VIEW_ALL constant has always trusted
     * hr-manager, vice-chancellor, and registrar (alongside admin and
     * internal-auditor) to see every user's audit trail - but the
     * view_audit_trail permission itself was only ever granted to
     * internal-auditor (2026_05_22_123724_add_new_roles_to_permission_table),
     * so those three roles hit a 403 before the controller's own logic ever
     * ran. This brings the permission grant in line with what the
     * controller already assumes.
     */
    public function up(): void
    {
        foreach (['hr-manager', 'vice-chancellor', 'registrar'] as $roleName) {
            $role = Role::where('name', $roleName)->where('guard_name', 'web')->first();

            if ($role && ! $role->hasPermissionTo('view_audit_trail')) {
                $role->givePermissionTo('view_audit_trail');
            }
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach (['hr-manager', 'vice-chancellor', 'registrar'] as $roleName) {
            $role = Role::where('name', $roleName)->where('guard_name', 'web')->first();

            if ($role && $role->hasPermissionTo('view_audit_trail')) {
                $role->revokePermissionTo('view_audit_trail');
            }
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
