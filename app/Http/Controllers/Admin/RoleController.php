<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\PerPageTrait;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    use PerPageTrait;

    /**
     * Display list of all roles
     */
    public function index(Request $request)
    {
        // Store per_page in session when changed
        if ($request->has('per_page')) {
            session(['roles_per_page' => $request->per_page]);
        }
        
        // Get per_page from session or request or default
        $perPage = $request->get('per_page', session('roles_per_page', 10));
        
        $roles = Role::withCount('users')
            ->orderBy('name')
            ->paginate($perPage)
            ->appends($request->query());
        
        $allRoles = Role::orderBy('name')->get();
        $users = User::with('roles')->get();
        
        $totalRoles = Role::count();
        $totalPermissions = Permission::count();
        $totalUsers = User::count();
        $usersWithRoles = User::has('roles')->count();
        $usersWithoutRoles = User::doesntHave('roles')->count();
        
        return view('admin.roles.index', compact(
            'roles', 'allRoles', 'users', 'totalRoles', 'totalPermissions',
            'totalUsers', 'usersWithRoles', 'usersWithoutRoles'
        ));
    }
    
    /**
     * Show form to assign role to user (WITH PAGINATION)
     */
    public function assignForm(Request $request)
    {
        // Store per_page in session when changed
        if ($request->has('per_page')) {
            session(['role_assign_per_page' => $request->per_page]);
        }
        
        // Get per_page from session or request or default
        $perPage = $request->get('per_page', session('role_assign_per_page', 10));
        
        $users = User::with('roles')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->paginate($perPage)
            ->appends($request->query());
        
        $roles = Role::orderBy('name')->get();
        
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
        
        if ($request->has('permissions')) {
            foreach ($request->permissions as $permName) {
                Permission::firstOrCreate([
                    'name' => $permName,
                    'guard_name' => 'web',
                ]);
            }
        }
        
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
        
        if ($request->has('permissions')) {
            foreach ($request->permissions as $permName) {
                Permission::firstOrCreate([
                    'name' => $permName,
                    'guard_name' => 'web',
                ]);
            }
        }
        
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