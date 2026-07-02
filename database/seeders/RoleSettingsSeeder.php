<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class RoleSettingsSeeder extends Seeder
{
    public function run()
    {
        // ============================================
        // HOD (Head of Department) Settings
        // ============================================
        $hodSettings = [
            [
                'key' => 'hod_auto_approve_days',
                'value' => '3',
                'type' => 'number',
                'group' => 'hod_approvals',
                'label' => 'Auto-Approve Days',
                'description' => 'Leave requests under this many days auto-approved by HOD',
                'role' => 'hod',
                'sort_order' => 1,
            ],
            [
                'key' => 'hod_notify_on_leave',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'hod_notifications',
                'label' => 'Notify Deputy on Own Leave',
                'description' => 'Notify deputy HOD when you are on leave',
                'role' => 'hod',
                'sort_order' => 2,
            ],
            [
                'key' => 'hod_max_consecutive_days',
                'value' => '14',
                'type' => 'number',
                'group' => 'hod_approvals',
                'label' => 'Max Consecutive Days',
                'description' => 'Maximum consecutive days HOD can approve without Dean',
                'role' => 'hod',
                'sort_order' => 3,
            ],
            [
                'key' => 'hod_daily_summary',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'hod_notifications',
                'label' => 'Daily Summary Email',
                'description' => 'Receive daily summary of pending approvals',
                'role' => 'hod',
                'sort_order' => 4,
            ],
        ];

        // ============================================
        // Dean Settings
        // ============================================
        $deanSettings = [
            [
                'key' => 'dean_auto_approve_days',
                'value' => '7',
                'type' => 'number',
                'group' => 'dean_approvals',
                'label' => 'Auto-Approve Days',
                'description' => 'Leave requests under this many days auto-approved by Dean',
                'role' => 'dean',
                'sort_order' => 1,
            ],
            [
                'key' => 'dean_require_dvc_above_days',
                'value' => '21',
                'type' => 'number',
                'group' => 'dean_approvals',
                'label' => 'Require DVC Above Days',
                'description' => 'Leave above this many days requires DVC approval',
                'role' => 'dean',
                'sort_order' => 2,
            ],
            [
                'key' => 'dean_weekly_report',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'dean_notifications',
                'label' => 'Weekly Summary Report',
                'description' => 'Receive weekly summary of faculty leave',
                'role' => 'dean',
                'sort_order' => 3,
            ],
        ];

        // ============================================
        // HR Officer Settings
        // ============================================
        $hrSettings = [
            [
                'key' => 'hr_require_documents',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'hr_policies',
                'label' => 'Require Supporting Documents',
                'description' => 'Staff must upload documents for leave requests',
                'role' => 'hr-officer',
                'sort_order' => 1,
            ],
            [
                'key' => 'hr_auto_approve_logistics',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'hr_approvals',
                'label' => 'Auto-Approve Logistics',
                'description' => 'Automatically approve HR logistics step after DVC',
                'role' => 'hr-officer',
                'sort_order' => 2,
            ],
            [
                'key' => 'hr_notify_on_completion',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'hr_notifications',
                'label' => 'Notify on Completion',
                'description' => 'Notify HR when leave is fully approved',
                'role' => 'hr-officer',
                'sort_order' => 3,
            ],
        ];

        // ============================================
        // Deputy HOD Settings
        // ============================================
        $deputyHodSettings = [
            [
                'key' => 'deputy_auto_activate',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'deputy_approvals',
                'label' => 'Auto-Activate on HOD Leave',
                'description' => 'Automatically grant approval rights when HOD is on leave',
                'role' => 'deputy-hod',
                'sort_order' => 1,
            ],
            [
                'key' => 'deputy_notify_on_activation',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'deputy_notifications',
                'label' => 'Notify on Activation',
                'description' => 'Notify deputy when HOD goes on leave',
                'role' => 'deputy-hod',
                'sort_order' => 2,
            ],
        ];

        // ============================================
        // Department Secretary Settings
        // ============================================
        $secretarySettings = [
            [
                'key' => 'secretary_can_submit_on_behalf',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'secretary_permissions',
                'label' => 'Submit on Behalf',
                'description' => 'Allow secretary to submit leave on behalf of staff',
                'role' => 'dept-secretary',
                'sort_order' => 1,
            ],
            [
                'key' => 'secretary_daily_digest',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'secretary_notifications',
                'label' => 'Daily Digest Email',
                'description' => 'Receive daily digest of department leave activity',
                'role' => 'dept-secretary',
                'sort_order' => 2,
            ],
        ];

        // ============================================
        // DVC Settings
        // ============================================
        $dvcSettings = [
            [
                'key' => 'dvc_require_hr_after_approval',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'dvc_approvals',
                'label' => 'Require HR Logistics',
                'description' => 'Send to HR logistics after DVC approval',
                'role' => 'dvca',
                'sort_order' => 1,
            ],
            [
                'key' => 'dvc_weekly_highlights',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'dvc_notifications',
                'label' => 'Weekly Highlights',
                'description' => 'Receive weekly highlights of leave statistics',
                'role' => 'dvca',
                'sort_order' => 2,
            ],
        ];

        // Insert all settings
        foreach ($hodSettings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key'], 'role' => $setting['role']],
                $setting
            );
        }

        foreach ($deanSettings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key'], 'role' => $setting['role']],
                $setting
            );
        }

        foreach ($hrSettings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key'], 'role' => $setting['role']],
                $setting
            );
        }

        foreach ($deputyHodSettings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key'], 'role' => $setting['role']],
                $setting
            );
        }

        foreach ($secretarySettings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key'], 'role' => $setting['role']],
                $setting
            );
        }

        foreach ($dvcSettings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key'], 'role' => $setting['role']],
                $setting
            );
        }

        $this->command->info('Role-specific settings seeded successfully!');
        $this->command->info('Total settings: ' . Setting::count());
    }
}