<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * Display list of all roles
     */
    public function index()
    {
        $roles = Role::all();
        $users = User::with('roles')->get();
        
        return view('admin.roles.index', compact('roles', 'users'));
    }
    
    /**
     * Show form to assign role to user
     */
    public function assignForm()
    {
        $users = User::all();
        $roles = Role::all();
        
        return view('admin.roles.assign', compact('users', 'roles'));
    }
    
    /**
     * Assign role to user
     */
    public function assignRole(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:staff,id',
            'role' => 'required|string|exists:roles,name',
        ]);
        
        $user = User::findOrFail($request->user_id);
        $role = $request->role;
        
        // Remove all existing roles and assign new one
        $user->syncRoles([$role]);
        
        return redirect()->route('admin.roles.index')
            ->with('success', "Role '{$role}' assigned to {$user->first_name} {$user->last_name}");
    }
    
    /**
     * Remove role from user
     */
    public function removeRole($userId, $roleName)
    {
        $user = User::findOrFail($userId);
        $user->removeRole($roleName);
        
        return redirect()->route('admin.roles.index')
            ->with('success', "Role '{$roleName}' removed from {$user->first_name} {$user->last_name}");
    }
    
    /**
     * Show form to create new role
     */
    public function createForm()
    {
        $permissions = Permission::all();
        return view('admin.roles.create', compact('permissions'));
    }
    
    /**
     * Create new role
     */
    public function storeRole(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name',
            'permissions' => 'array',
        ]);
        
        $role = Role::create([
            'name' => $request->name,
            'guard_name' => 'web',
        ]);
        
        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }
        
        return redirect()->route('admin.roles.index')
            ->with('success', "Role '{$request->name}' created successfully");
    }
    
    /**
     * Show form to edit role permissions
     */
    public function editRole($id)
    {
        $role = Role::findOrFail($id);
        $permissions = Permission::all();
        $rolePermissions = $role->permissions->pluck('name')->toArray();
        
        return view('admin.roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }
    
    /**
     * Update role permissions
     */
    public function updateRole(Request $request, $id)
    {
        $role = Role::findOrFail($id);
        
        $request->validate([
            'permissions' => 'array',
        ]);
        
        $role->syncPermissions($request->permissions);
        
        return redirect()->route('admin.roles.index')
            ->with('success', "Permissions updated for '{$role->name}'");
    }
    
    /**
     * Delete role
     */
    public function deleteRole($id)
    {
        $role = Role::findOrFail($id);
        
        // Prevent deleting system roles
        $systemRoles = ['admin', 'staff', 'hod', 'dean', 'dvca', 'dvc-pfa', 'acting-dvc', 'hr-officer', 'hr-manager'];
        
        if (in_array($role->name, $systemRoles)) {
            return redirect()->route('admin.roles.index')
                ->with('error', "Cannot delete system role '{$role->name}'");
        }
        
        $role->delete();
        
        return redirect()->route('admin.roles.index')
            ->with('success', "Role '{$role->name}' deleted successfully");
    }
}