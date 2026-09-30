<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    public function up(): void
    {
        $permissions = [
            'manage_registry',
            'admin_access',
            'manage_tenants',
        ];

        foreach ($permissions as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        $assignments = [
            'manage_registry' => ['super-admin', 'admin', 'registry-officer'],
            'admin_access' => ['super-admin', 'admin'],
            'manage_tenants' => ['super-admin', 'admin'],
        ];

        foreach ($assignments as $permissionName => $roleNames) {
            $permission = Permission::findByName($permissionName, 'web');

            foreach ($roleNames as $roleName) {
                $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);

                if (! $role->hasPermissionTo($permissionName)) {
                    $role->givePermissionTo($permission);
                }
            }
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }

    public function down(): void
    {
        $permissions = ['manage_registry', 'admin_access', 'manage_tenants'];

        foreach ($permissions as $name) {
            $permission = Permission::findByName($name, 'web');

            if ($permission) {
                $permission->delete();
            }
        }
    }
};
