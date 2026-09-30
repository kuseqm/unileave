<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    public function up(): void
    {
        Permission::firstOrCreate(['name' => 'manage_team_members', 'guard_name' => 'web']);

        $permission = Permission::findByName('manage_team_members', 'web');

        foreach (['super-admin', 'admin'] as $roleName) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);

            if (! $role->hasPermissionTo('manage_team_members')) {
                $role->givePermissionTo($permission);
            }
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }

    public function down(): void
    {
        $permission = Permission::findByName('manage_team_members', 'web');

        if ($permission) {
            $permission->delete();
        }
    }
};
