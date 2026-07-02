<?php
// app/Http/Controllers/SettingsController.php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SettingsController extends Controller
{
    /**
     * Display settings index page (grouped by category)
     */
    public function index()
    {
        $user = auth()->user();
        $role = $user->getRoleNames()->first();
        
        // Get settings grouped by category
        $settings = Setting::where(function($q) use ($role, $user) {
                $q->whereNull('role')
                  ->orWhere('role', $role)
                  ->orWhere('user_id', $user->id);
            })
            ->orderBy('sort_order')
            ->orderBy('group')
            ->get()
            ->groupBy('group');
        
        $allSettings = Setting::orderBy('group')->orderBy('sort_order')->get();
        
        return view('settings.index', compact('settings', 'role', 'allSettings'));
    }

    /**
     * Show form to create new setting
     */
    public function create()
    {
        $groups = Setting::distinct()->pluck('group');
        $types = ['text', 'textarea', 'number', 'boolean', 'select', 'json'];
        
        return view('settings.create', compact('groups', 'types'));
    }

    /**
     * Store new setting
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'key' => 'required|string|max:191|unique:settings,key',
            'value' => 'nullable|string',
            'type' => 'required|in:text,textarea,number,boolean,select,json',
            'group' => 'required|string|max:100',
            'label' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'options' => 'nullable|array',
            'role' => 'nullable|string|max:100',
            'sort_order' => 'integer',
            'is_encrypted' => 'boolean',
        ]);
        
        DB::beginTransaction();
        
        try {
            $setting = Setting::create([
                'key' => $request->key,
                'value' => $request->value,
                'type' => $request->type,
                'group' => $request->group,
                'label' => $request->label,
                'description' => $request->description,
                'options' => $request->options,
                'role' => $request->role,
                'sort_order' => $request->sort_order ?? 0,
                'is_encrypted' => $request->is_encrypted ?? false,
            ]);
            
            DB::commit();
            
            return redirect()->route('settings.index')
                ->with('success', "Setting '{$setting->label}' created successfully.");
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create setting: ' . $e->getMessage());
            return back()->with('error', 'Failed to create setting: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Show single setting details
     */
    public function show($id)
    {
        $setting = Setting::findOrFail($id);
        
        return view('settings.show', compact('setting'));
    }

    /**
     * Show form to edit setting
     */
    public function edit($id)
    {
        $setting = Setting::findOrFail($id);
        $groups = Setting::distinct()->pluck('group');
        $types = ['text', 'textarea', 'number', 'boolean', 'select', 'json'];
        
        return view('settings.edit', compact('setting', 'groups', 'types'));
    }

    /**
     * Update setting
     */
    public function update(Request $request, $id)
    {
        $setting = Setting::findOrFail($id);
        
        $validated = $request->validate([
            'key' => 'required|string|max:191|unique:settings,key,' . $id,
            'value' => 'nullable|string',
            'type' => 'required|in:text,textarea,number,boolean,select,json',
            'group' => 'required|string|max:100',
            'label' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'options' => 'nullable|array',
            'role' => 'nullable|string|max:100',
            'sort_order' => 'integer',
            'is_encrypted' => 'boolean',
        ]);
        
        DB::beginTransaction();
        
        try {
            $setting->update([
                'key' => $request->key,
                'value' => $request->value,
                'type' => $request->type,
                'group' => $request->group,
                'label' => $request->label,
                'description' => $request->description,
                'options' => $request->options,
                'role' => $request->role,
                'sort_order' => $request->sort_order ?? 0,
                'is_encrypted' => $request->is_encrypted ?? false,
            ]);
            
            DB::commit();
            
            return redirect()->route('settings.index')
                ->with('success', "Setting '{$setting->label}' updated successfully.");
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update setting: ' . $e->getMessage());
            return back()->with('error', 'Failed to update setting: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Delete setting
     */
    public function destroy($id)
    {
        $setting = Setting::findOrFail($id);
        
        DB::beginTransaction();
        
        try {
            $settingName = $setting->label ?? $setting->key;
            $setting->delete();
            
            DB::commit();
            
            return redirect()->route('settings.index')
                ->with('success', "Setting '{$settingName}' deleted successfully.");
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete setting: ' . $e->getMessage());
            return back()->with('error', 'Failed to delete setting: ' . $e->getMessage());
        }
    }

    /**
     * Bulk update settings (existing functionality)
     */
    public function bulkUpdate(Request $request)
    {
        $validated = $request->validate([
            'settings' => 'required|array',
            'settings.*' => 'nullable|string',
        ]);
        
        DB::beginTransaction();
        
        try {
            foreach ($request->settings as $key => $value) {
                $setting = Setting::where('key', $key)->first();
                
                if ($setting) {
                    if ($setting->type === 'boolean') {
                        $value = $value === 'true' || $value === '1' || $value === true;
                    }
                    
                    $setting->update(['value' => $value]);
                }
            }
            
            DB::commit();
            
            return redirect()->back()->with('success', 'Settings saved successfully.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to save settings: ' . $e->getMessage());
        }
    }
}