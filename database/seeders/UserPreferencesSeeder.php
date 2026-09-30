<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class UserPreferencesSeeder extends Seeder
{
    public function run()
    {
        // ============================================
        // DEFAULT PREFERENCES (For all users)
        // ============================================

        $defaultPreferences = [
            // Display Preferences
            [
                'key' => 'dashboard_layout',
                'value' => 'grid',
                'type' => 'select',
                'group' => 'display',
                'label' => 'Dashboard Layout',
                'description' => 'Choose how dashboard items are displayed',
                'options' => json_encode(['grid' => 'Grid View', 'list' => 'List View', 'compact' => 'Compact View']),
                'sort_order' => 1,
            ],
            [
                'key' => 'items_per_page',
                'value' => '25',
                'type' => 'select',
                'group' => 'display',
                'label' => 'Items Per Page',
                'description' => 'Number of items shown in tables',
                'options' => json_encode(['10' => '10 items', '25' => '25 items', '50' => '50 items', '100' => '100 items']),
                'sort_order' => 2,
            ],
            [
                'key' => 'compact_view',
                'value' => 'false',
                'type' => 'boolean',
                'group' => 'display',
                'label' => 'Compact View',
                'description' => 'Reduce spacing for more content',
                'sort_order' => 3,
            ],
            [
                'key' => 'show_week_numbers',
                'value' => 'false',
                'type' => 'boolean',
                'group' => 'display',
                'label' => 'Show Week Numbers',
                'description' => 'Display week numbers in calendar views',
                'sort_order' => 4,
            ],
            [
                'key' => 'first_day_of_week',
                'value' => '1',
                'type' => 'select',
                'group' => 'display',
                'label' => 'First Day of Week',
                'description' => 'Which day starts the week',
                'options' => json_encode(['0' => 'Sunday', '1' => 'Monday', '6' => 'Saturday']),
                'sort_order' => 5,
            ],

            // Date & Time Preferences
            [
                'key' => 'date_format',
                'value' => 'd M Y',
                'type' => 'select',
                'group' => 'datetime',
                'label' => 'Date Format',
                'description' => 'How dates are displayed',
                'options' => json_encode([
                    'd M Y' => '15 Jan 2024',
                    'Y-m-d' => '2024-01-15',
                    'm/d/Y' => '01/15/2024',
                    'd/m/Y' => '15/01/2024',
                    'M d, Y' => 'Jan 15, 2024',
                ]),
                'sort_order' => 10,
            ],
            [
                'key' => 'time_format',
                'value' => 'H:i',
                'type' => 'select',
                'group' => 'datetime',
                'label' => 'Time Format',
                'description' => 'How times are displayed',
                'options' => json_encode(['H:i' => '24 Hour (14:30)', 'h:i A' => '12 Hour (02:30 PM)']),
                'sort_order' => 11,
            ],
            [
                'key' => 'timezone',
                'value' => 'Africa/Dar_es_Salaam',
                'type' => 'select',
                'group' => 'datetime',
                'label' => 'Timezone',
                'description' => 'Your local timezone',
                'options' => json_encode([
                    'Africa/Dar_es_Salaam' => 'Dar es Salaam (EAT)',
                    'Africa/Nairobi' => 'Nairobi (EAT)',
                    'Africa/Kampala' => 'Kampala (EAT)',
                    'UTC' => 'UTC',
                ]),
                'sort_order' => 12,
            ],
            [
                'key' => 'language',
                'value' => 'en',
                'type' => 'select',
                'group' => 'datetime',
                'label' => 'Language',
                'description' => 'Preferred display language',
                'options' => json_encode(['en' => 'English', 'sw' => 'Swahili']),
                'sort_order' => 13,
            ],

            // Notification Preferences
            [
                'key' => 'email_notifications',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'notifications',
                'label' => 'Email Notifications',
                'description' => 'Receive email notifications for approvals and updates',
                'sort_order' => 20,
            ],
            [
                'key' => 'browser_notifications',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'notifications',
                'label' => 'Browser Notifications',
                'description' => 'Show desktop/push notifications',
                'sort_order' => 21,
            ],
            [
                'key' => 'sound_notifications',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'notifications',
                'label' => 'Sound Notifications',
                'description' => 'Play sound for new notifications',
                'sort_order' => 22,
            ],
            [
                'key' => 'reminder_days',
                'value' => '3',
                'type' => 'select',
                'group' => 'notifications',
                'label' => 'Leave Reminder',
                'description' => 'Days before leave to send reminder',
                'options' => json_encode(['0' => 'No reminders', '1' => '1 day before', '3' => '3 days before', '7' => '7 days before', '14' => '14 days before']),
                'sort_order' => 23,
            ],
            [
                'key' => 'email_digest_frequency',
                'value' => 'daily',
                'type' => 'select',
                'group' => 'notifications',
                'label' => 'Email Digest',
                'description' => 'How often to send summary emails',
                'options' => json_encode(['daily' => 'Daily', 'weekly' => 'Weekly', 'never' => 'Never']),
                'sort_order' => 24,
            ],
            [
                'key' => 'notify_on_approval',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'notifications',
                'label' => 'Notify on Approval',
                'description' => 'Receive notification when your request is approved',
                'sort_order' => 25,
            ],
            [
                'key' => 'notify_on_rejection',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'notifications',
                'label' => 'Notify on Rejection',
                'description' => 'Receive notification when your request is rejected',
                'sort_order' => 26,
            ],

            // Theme & Appearance
            [
                'key' => 'theme_mode',
                'value' => 'light',
                'type' => 'select',
                'group' => 'appearance',
                'label' => 'Theme Mode',
                'description' => 'Light or dark theme',
                'options' => json_encode(['light' => 'Light', 'dark' => 'Dark', 'auto' => 'Auto (Follow System)']),
                'sort_order' => 30,
            ],
            [
                'key' => 'sidebar_collapsed',
                'value' => 'false',
                'type' => 'boolean',
                'group' => 'appearance',
                'label' => 'Collapsed Sidebar',
                'description' => 'Start with sidebar collapsed',
                'sort_order' => 31,
            ],
            [
                'key' => 'show_avatars',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'appearance',
                'label' => 'Show Avatars',
                'description' => 'Display user avatars throughout the system',
                'sort_order' => 32,
            ],
            [
                'key' => 'animations_enabled',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'appearance',
                'label' => 'Enable Animations',
                'description' => 'Enable UI animations and transitions',
                'sort_order' => 33,
            ],

            // Report Preferences
            [
                'key' => 'default_report_type',
                'value' => 'pdf',
                'type' => 'select',
                'group' => 'reports',
                'label' => 'Default Report Format',
                'description' => 'Preferred format when exporting reports',
                'options' => json_encode(['pdf' => 'PDF', 'excel' => 'Excel', 'csv' => 'CSV']),
                'sort_order' => 40,
            ],
            [
                'key' => 'auto_refresh_interval',
                'value' => '0',
                'type' => 'number',
                'group' => 'reports',
                'label' => 'Auto Refresh Interval',
                'description' => 'Seconds between auto-refresh (0 = disabled)',
                'sort_order' => 41,
            ],
            [
                'key' => 'show_charts_on_dashboard',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'reports',
                'label' => 'Show Charts',
                'description' => 'Display charts on dashboard',
                'sort_order' => 42,
            ],
            [
                'key' => 'export_include_filters',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'reports',
                'label' => 'Include Filters in Export',
                'description' => 'Include applied filters in exported reports',
                'sort_order' => 43,
            ],

            // Accessibility
            [
                'key' => 'high_contrast_mode',
                'value' => 'false',
                'type' => 'boolean',
                'group' => 'accessibility',
                'label' => 'High Contrast Mode',
                'description' => 'Increase contrast for better visibility',
                'sort_order' => 50,
            ],
            [
                'key' => 'large_fonts',
                'value' => 'false',
                'type' => 'boolean',
                'group' => 'accessibility',
                'label' => 'Large Fonts',
                'description' => 'Increase text size throughout the system',
                'sort_order' => 51,
            ],
            [
                'key' => 'reduced_motion',
                'value' => 'false',
                'type' => 'boolean',
                'group' => 'accessibility',
                'label' => 'Reduced Motion',
                'description' => 'Minimize animations and motion effects',
                'sort_order' => 52,
            ],

            // Workflow Preferences
            [
                'key' => 'default_view',
                'value' => 'dashboard',
                'type' => 'select',
                'group' => 'workflow',
                'label' => 'Default Landing Page',
                'description' => 'Where to go after login',
                'options' => json_encode([
                    'dashboard' => 'Dashboard',
                    'leave-requests.my-requests' => 'My Leave Requests',
                    'staff.index' => 'Staff Directory',
                ]),
                'sort_order' => 60,
            ],
            [
                'key' => 'auto_save_requests',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'workflow',
                'label' => 'Auto-Save Drafts',
                'description' => 'Automatically save draft leave requests',
                'sort_order' => 61,
            ],
            [
                'key' => 'confirm_before_submit',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'workflow',
                'label' => 'Confirm Before Submit',
                'description' => 'Show confirmation dialog before submitting leave',
                'sort_order' => 62,
            ],

            // Security
            [
                'key' => 'session_timeout_warning',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'security',
                'label' => 'Session Timeout Warning',
                'description' => 'Show warning before session expires',
                'sort_order' => 70,
            ],
            [
                'key' => 'remember_last_search',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'security',
                'label' => 'Remember Last Search',
                'description' => 'Save your last search filters',
                'sort_order' => 71,
            ],
        ];

        // Seed default preferences (global templates) - WITHOUT tenant_id
        foreach ($defaultPreferences as $pref) {
            Setting::updateOrCreate(
                [
                    'key' => $pref['key'],
                    'role' => null,
                    'user_id' => null,
                ],
                $pref
            );
        }

        $this->command->info('Default preferences seeded: '.count($defaultPreferences).' preferences');

        $this->command->newLine();
        $this->command->info('=== PREFERENCES SUMMARY ===');
        $this->command->line('Total preference definitions: '.Setting::whereNull('user_id')->whereNull('role')->count());
        $this->command->line('Total user-specific settings: '.Setting::whereNotNull('user_id')->count());
    }
}
