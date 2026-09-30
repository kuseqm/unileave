<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    /**
     * New permission gating the leave-approval chain reorder page - kept to
     * admin/super-admin only, since it affects every stage of the chain
     * (HOD/Dean/DVC included), not just HR's own domain.
     */
    public function up(): void
    {
        Permission::firstOrCreate(['name' => 'manage_leave_approval_chain', 'guard_name' => 'web']);

        $permission = Permission::findByName('manage_leave_approval_chain', 'web');

        foreach (['super-admin', 'admin'] as $roleName) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);

            if (! $role->hasPermissionTo('manage_leave_approval_chain')) {
                $role->givePermissionTo($permission);
            }
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }

    public function down(): void
    {
        $permission = Permission::findByName('manage_leave_approval_chain', 'web');

        if ($permission) {
            $permission->delete();
        }
    }
};
