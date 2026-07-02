<?php
// app/Http/Controllers/Admin/TenantSettingsController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TenantSettingsController extends Controller
{
    // Remove the __construct method entirely
    // Middleware is handled in routes file

    public function index()
    {
        $tenants = Tenant::all();
        return view('admin.tenants.index', compact('tenants'));
    }

    public function create()
    {
        return view('admin.tenants.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:tenants',
            'domain' => 'nullable|string|unique:tenants',
            'database' => 'nullable|string|unique:tenants',
            'status' => 'required|in:active,inactive,pending',
        ]);

        $tenant = Tenant::create($request->all());

        return redirect()->route('admin.tenants.index')
            ->with('success', 'Tenant created successfully.');
    }

    public function edit($id)
    {
        $tenant = Tenant::findOrFail($id);
        return view('admin.tenants.edit', compact('tenant'));
    }

    public function update(Request $request, $id)
    {
        $tenant = Tenant::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:tenants,name,' . $id,
            'domain' => 'nullable|string|unique:tenants,domain,' . $id,
            'database' => 'nullable|string|unique:tenants,database,' . $id,
            'status' => 'required|in:active,inactive,pending',
        ]);

        $tenant->update($request->all());

        return redirect()->route('admin.tenants.index')
            ->with('success', 'Tenant updated successfully.');
    }

    public function destroy($id)
    {
        $tenant = Tenant::findOrFail($id);
        
        // Check if tenant has users
        if ($tenant->users()->count() > 0) {
            return back()->with('error', 'Cannot delete tenant with active users.');
        }
        
        $tenant->delete();

        return redirect()->route('admin.tenants.index')
            ->with('success', 'Tenant deleted successfully.');
    }

    public function settings($id)
    {
        $tenant = Tenant::findOrFail($id);
        $settings = Setting::where('tenant_id', $id)
            ->orderBy('group')
            ->get()
            ->groupBy('group');
            
        return view('admin.tenants.settings', compact('tenant', 'settings'));
    }

    public function updateSettings(Request $request, $id)
    {
        $tenant = Tenant::findOrFail($id);
        
        foreach ($request->settings as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key, 'tenant_id' => $id],
                ['value' => $value, 'type' => 'text', 'group' => 'tenant']
            );
        }

        return redirect()->back()->with('success', 'Tenant settings updated.');
    }
}