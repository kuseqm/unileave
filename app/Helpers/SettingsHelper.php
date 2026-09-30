<?php

use App\Models\Setting;

if (! function_exists('setting')) {
    /**
     * Get a setting value with role hierarchy
     * Priority: User > Role > Global
     */
    function setting($key, $default = null, $role = null, $userId = null)
    {
        $query = Setting::where('key', $key);

        // Priority 1: User-specific setting
        if ($userId) {
            $userSetting = Setting::where('key', $key)
                ->where('user_id', $userId)
                ->first();
            if ($userSetting) {
                return getTypedValue($userSetting);
            }
        }

        // Priority 2: Role-specific setting
        if ($role) {
            $roleSetting = Setting::where('key', $key)
                ->where('role', $role)
                ->first();
            if ($roleSetting) {
                return getTypedValue($roleSetting);
            }
        }

        // Priority 3: Global setting
        $globalSetting = Setting::where('key', $key)
            ->whereNull('role')
            ->whereNull('user_id')
            ->first();

        if ($globalSetting) {
            return getTypedValue($globalSetting);
        }

        return $default;
    }
}

if (! function_exists('getTypedValue')) {
    function getTypedValue($setting)
    {
        switch ($setting->type) {
            case 'boolean':
                return filter_var($setting->value, FILTER_VALIDATE_BOOLEAN);
            case 'number':
                return floatval($setting->value);
            case 'json':
                return json_decode($setting->value, true);
            default:
                if ($setting->is_encrypted && $setting->value) {
                    try {
                        return decrypt($setting->value);
                    } catch (Exception $e) {
                        return $setting->value;
                    }
                }

                return $setting->value;
        }
    }
}

if (! function_exists('app_abbreviation')) {
    /**
     * Initials of config('app.name'), for the browser tab title - so it
     * follows APP_NAME automatically (Staff Management System -> SMS,
     * University Management System -> UMS) instead of a name hardcoded
     * separately in every layout. Strips a trailing "(...)" suffix like
     * "(DEMO)" first, since that shouldn't contribute its own initial.
     */
    function app_abbreviation(): string
    {
        $name = preg_replace('/\s*\(.*?\)\s*/', ' ', config('app.name', 'App'));

        return collect(preg_split('/\s+/', trim($name)))
            ->filter()
            ->map(fn ($word) => mb_strtoupper(mb_substr($word, 0, 1)))
            ->implode('');
    }
}

if (! function_exists('set_setting')) {
    function set_setting($key, $value, $type = 'text', $group = 'general', $role = null, $userId = null, $label = null, $description = null)
    {
        if ($type === 'text') {
            if (is_array($value)) {
                $type = 'json';
                $value = json_encode($value);
            } elseif (is_bool($value)) {
                $type = 'boolean';
                $value = $value ? 'true' : 'false';
            } elseif (is_numeric($value)) {
                $type = 'number';
            }
        }

        return Setting::updateOrCreate(
            [
                'key' => $key,
                'role' => $role,
                'user_id' => $userId,
            ],
            [
                'value' => $value,
                'type' => $type,
                'group' => $group,
                'label' => $label,
                'description' => $description,
            ]
        );
    }
}
