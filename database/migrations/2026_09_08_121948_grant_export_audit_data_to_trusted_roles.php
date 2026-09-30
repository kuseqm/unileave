<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    /**
     * Same gap as view_audit_trail (see the migration next to this one):
     * AuditTrailController::export() also gates on canViewAll(), which
     * trusts hr-manager, vice-chancellor, and registrar - but the "Export
     * CSV" button was visible to them with no permission behind it,
     * so clicking it 403'd.
     */
    public function up(): void
    {
        foreach (['hr-manager', 'vice-chancellor', 'registrar'] as $roleName) {
            $role = Role::where('name', $roleName)->where('guard_name', 'web')->first();

            if ($role && ! $role->hasPermissionTo('export_audit_data')) {
                $role->givePermissionTo('export_audit_data');
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

            if ($role && $role->hasPermissionTo('export_audit_data')) {
                $role->revokePermissionTo('export_audit_data');
            }
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
