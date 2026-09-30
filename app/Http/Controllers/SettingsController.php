<?php

// app/Http/Controllers/SettingsController.php

namespace App\Http\Controllers;

use App\Models\LetterSetting;
use App\Models\Setting;
use App\Models\University;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SettingsController extends Controller
{
    /**
     * Display settings index page (grouped by category), with the
     * Letter Branding / Letter & Registry / HR tabs each carrying the
     * exact data their own dedicated controller's index() gathers - see
     * the "one Settings hub, tabbed" write-up in project memory for why
     * this reuses those controllers' own routes/validation for saving
     * (each tab's <form> still posts to its original route) rather than
     * merging their logic into this controller.
     */
    public function index()
    {
        $user = auth()->user();
        $role = $user->getRoleNames()->first();

        // Get settings grouped by category. A "global" row is
        // role IS NULL *and* user_id IS NULL - `whereNull('role')` alone
        // used to match every OTHER user's personal preference row too
        // (those only set user_id, never role), so this admin's own
        // General tab was silently pulling in random other staff members'
        // saved toggles under blank labels, and saving the form here could
        // overwrite whichever row Setting::where('key', ...)->first()
        // happened to pick (see bulkUpdate() below).
        $settings = Setting::where(function ($q) use ($role, $user) {
            $q->where(function ($global) {
                $global->whereNull('role')->whereNull('user_id');
            })
                ->orWhere('role', $role)
                ->orWhere('user_id', $user->id);
        })
            ->orderBy('sort_order')
            ->orderBy('group')
            ->get()
            ->groupBy('group');

        // Only the master catalog rows (user_id null) belong in the
        // "Settings Definitions" management table below - without this
        // filter, every individual user's own saved preference (same key,
        // one row per user_id, no label) shows up as what looks like a
        // duplicate entry, and its Edit/Delete buttons would act on that
        // user's personal value instead of the shared definition.
        $allSettings = Setting::whereNull('user_id')->orderBy('group')->orderBy('sort_order')->get();

        $canLetterhead = $user->can('admin_access');
        $canRegistry = $user->can('manage_registry');

        $university = null;
        if ($canLetterhead) {
            $university = $user->university ?? University::first();
        }

        $registryUniversities = collect();
        $registrySettings = [];
        $registrySelectedUniversity = null;
        if ($canRegistry) {
            $registryUniversities = University::where('status', 'active')->get();
            $registrySelectedUniversity = old('university_id', request()->get('university_id', $registryUniversities->first()->id ?? null));
            foreach ($registryUniversities as $uni) {
                $registrySettings[$uni->id] = LetterSetting::getForUniversity($uni->id);
            }
        }

        $hrSettings = Setting::where(function ($q) {
            $q->where(function ($global) {
                $global->whereNull('role')->whereNull('user_id');
            })->orWhere('role', 'hr-officer');
        })
            ->whereIn('group', ['hr', 'hr_approvals', 'hr_notifications'])
            ->orderBy('sort_order')
            ->get()
            ->groupBy('group');

        return view('settings.index', compact(
            'settings', 'role', 'allSettings',
            'canLetterhead', 'university',
            'canRegistry', 'registryUniversities', 'registrySettings', 'registrySelectedUniversity',
            'hrSettings'
        ));
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
            Log::error('Failed to create setting: '.$e->getMessage());

            return back()->with('error', 'Failed to create setting: '.$e->getMessage())->withInput();
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
            'key' => 'required|string|max:191|unique:settings,key,'.$id,
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
            Log::error('Failed to update setting: '.$e->getMessage());

            return back()->with('error', 'Failed to update setting: '.$e->getMessage())->withInput();
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
            Log::error('Failed to delete setting: '.$e->getMessage());

            return back()->with('error', 'Failed to delete setting: '.$e->getMessage());
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

        $user = auth()->user();
        $role = $user->getRoleNames()->first();

        DB::beginTransaction();

        try {
            foreach ($request->settings as $key => $value) {
                // Same specificity order as index()'s own scoping: prefer
                // this user's own row, then the role's row, then the true
                // global row - never the first row matching the key by
                // sheer luck, which could silently overwrite a different
                // user's personal preference.
                $setting = Setting::where('key', $key)->where('user_id', $user->id)->first()
                    ?? Setting::where('key', $key)->where('role', $role)->whereNull('user_id')->first()
                    ?? Setting::where('key', $key)->whereNull('role')->whereNull('user_id')->first();

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

            return redirect()->back()->with('error', 'Failed to save settings: '.$e->getMessage());
        }
    }
}
