<?php
// app/Http/Controllers/HrSettingsController.php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HrSettingsController extends Controller
{
    /**
     * Display HR settings page
     */
    public function index()
    {
        // Get HR-specific settings
        $settings = Setting::where(function($q) {
                $q->whereNull('role')
                  ->orWhere('role', 'hr-officer');
            })
            ->whereIn('group', ['hr', 'hr_approvals', 'hr_notifications'])
            ->orderBy('sort_order')
            ->get()
            ->groupBy('group');
        
        return view('settings.hr.index', compact('settings'));
    }

    /**
     * Display leave policies page
     */
    public function policies()
    {
        // Get leave policy settings
        $settings = Setting::where(function($q) {
                $q->whereNull('role')
                  ->orWhere('role', 'hr-officer');
            })
            ->where('group', 'hr_policies')
            ->orderBy('sort_order')
            ->get();
        
        return view('settings.hr.policies', compact('settings'));
    }

    /**
     * Update HR settings
     */
    public function update(Request $request)
    {
        $request->validate([
            'settings' => 'required|array',
        ]);
        
        DB::beginTransaction();
        
        try {
            foreach ($request->settings as $key => $value) {
                $setting = Setting::where('key', $key)->first();
                
                if ($setting) {
                    if ($setting->type === 'boolean') {
                        $value = $value === 'true' || $value === '1' || $value === true;
                        $value = $value ? 'true' : 'false';
                    }
                    $setting->update(['value' => $value]);
                } else {
                    // Create setting if doesn't exist
                    Setting::create([
                        'key' => $key,
                        'value' => $value,
                        'type' => 'text',
                        'group' => 'hr',
                        'role' => 'hr-officer',
                    ]);
                }
            }
            
            DB::commit();
            return redirect()->back()->with('success', 'HR settings updated successfully.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to update settings: ' . $e->getMessage());
        }
    }
}