<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingsTableSeeder extends Seeder
{
    public function run()
    {
        // Default global settings
        $settings = [
            // General Settings
            [
                'key' => 'app_name',
                'value' => 'UniLeave',
                'type' => 'text',
                'group' => 'general',
                'label' => 'Application Name',
                'description' => 'Name displayed throughout the system',
                'sort_order' => 1,
            ],
            [
                'key' => 'timezone',
                'value' => 'Africa/Dar_es_Salaam',
                'type' => 'text',
                'group' => 'general',
                'label' => 'Timezone',
                'description' => 'Default timezone for the system',
                'sort_order' => 2,
            ],
            [
                'key' => 'date_format',
                'value' => 'd M Y',
                'type' => 'text',
                'group' => 'general',
                'label' => 'Date Format',
                'description' => 'How dates are displayed',
                'sort_order' => 3,
            ],
            
            // Leave Policy Settings
            [
                'key' => 'max_annual_leave',
                'value' => '30',
                'type' => 'number',
                'group' => 'leave_policy',
                'label' => 'Maximum Annual Leave',
                'description' => 'Maximum annual leave days per staff',
                'sort_order' => 10,
            ],
            [
                'key' => 'max_sick_leave',
                'value' => '14',
                'type' => 'number',
                'group' => 'leave_policy',
                'label' => 'Maximum Sick Leave',
                'description' => 'Maximum sick leave days per year',
                'sort_order' => 11,
            ],
            [
                'key' => 'carryover_days',
                'value' => '5',
                'type' => 'number',
                'group' => 'leave_policy',
                'label' => 'Carryover Days',
                'description' => 'Maximum days that can be carried over to next year',
                'sort_order' => 12,
            ],
            
            // Notification Settings
            [
                'key' => 'email_notifications',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'notifications',
                'label' => 'Email Notifications',
                'description' => 'Send email notifications for approvals',
                'sort_order' => 20,
            ],
            [
                'key' => 'browser_notifications',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'notifications',
                'label' => 'Browser Notifications',
                'description' => 'Show browser notifications',
                'sort_order' => 21,
            ],
        ];

        foreach ($settings as $setting) {
            DB::table('settings')->updateOrInsert(
                ['key' => $setting['key']],
                $setting
            );
        }

        $this->command->info('Settings seeded successfully!');
        $this->command->info('Total settings: ' . DB::table('settings')->count());
    }
}