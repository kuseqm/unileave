<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdminSettingsSeeder extends Seeder
{
    public function run()
    {
        $adminSettings = [
            // ============================================
            // SYSTEM CONFIGURATION
            // ============================================
            [
                'key' => 'system_name',
                'value' => 'UniLeave Management System',
                'type' => 'text',
                'group' => 'system',
                'label' => 'System Name',
                'description' => 'Official name of the system',
                'sort_order' => 1,
            ],
            [
                'key' => 'system_logo',
                'value' => '/images/logo.png',
                'type' => 'text',
                'group' => 'system',
                'label' => 'System Logo',
                'description' => 'Path to system logo image',
                'sort_order' => 2,
            ],
            [
                'key' => 'system_favicon',
                'value' => '/images/favicon.ico',
                'type' => 'text',
                'group' => 'system',
                'label' => 'Favicon',
                'description' => 'Browser tab icon',
                'sort_order' => 3,
            ],
            [
                'key' => 'system_theme',
                'value' => 'light',
                'type' => 'select',
                'group' => 'system',
                'label' => 'System Theme',
                'description' => 'Default theme for all users',
                'options' => json_encode(['light' => 'Light', 'dark' => 'Dark', 'auto' => 'Auto']),
                'sort_order' => 4,
            ],

            // ============================================
            // LEAVE POLICY SETTINGS
            // ============================================
            [
                'key' => 'default_annual_leave',
                'value' => '30',
                'type' => 'number',
                'group' => 'leave_policy',
                'label' => 'Default Annual Leave Days',
                'description' => 'Standard annual leave allocation per staff',
                'sort_order' => 10,
            ],
            [
                'key' => 'default_sick_leave',
                'value' => '14',
                'type' => 'number',
                'group' => 'leave_policy',
                'label' => 'Default Sick Leave Days',
                'description' => 'Standard sick leave allocation per year',
                'sort_order' => 11,
            ],
            [
                'key' => 'default_casual_leave',
                'value' => '10',
                'type' => 'number',
                'group' => 'leave_policy',
                'label' => 'Default Casual Leave Days',
                'description' => 'Standard casual leave allocation per year',
                'sort_order' => 12,
            ],
            [
                'key' => 'default_study_leave',
                'value' => '12',
                'type' => 'number',
                'group' => 'leave_policy',
                'label' => 'Default Study Leave Days',
                'description' => 'Standard study leave allocation per year',
                'sort_order' => 13,
            ],
            [
                'key' => 'carryover_limit',
                'value' => '10',
                'type' => 'number',
                'group' => 'leave_policy',
                'label' => 'Maximum Carryover Days',
                'description' => 'Maximum days that can be carried to next year',
                'sort_order' => 14,
            ],
            [
                'key' => 'advance_leave_limit',
                'value' => '5',
                'type' => 'number',
                'group' => 'leave_policy',
                'label' => 'Advance Leave Limit',
                'description' => 'Maximum days that can be taken in advance',
                'sort_order' => 15,
            ],
            [
                'key' => 'min_notice_days',
                'value' => '7',
                'type' => 'number',
                'group' => 'leave_policy',
                'label' => 'Minimum Notice Period',
                'description' => 'Minimum days before leave required for application',
                'sort_order' => 16,
            ],
            [
                'key' => 'max_consecutive_days',
                'value' => '30',
                'type' => 'number',
                'group' => 'leave_policy',
                'label' => 'Maximum Consecutive Leave',
                'description' => 'Maximum consecutive days that can be taken',
                'sort_order' => 17,
            ],

            // ============================================
            // APPROVAL WORKFLOW SETTINGS
            // ============================================
            [
                'key' => 'auto_approve_short_days',
                'value' => '3',
                'type' => 'number',
                'group' => 'approvals',
                'label' => 'Auto-approve Short Leave',
                'description' => 'Leave requests under this many days auto-approved at HOD level',
                'sort_order' => 20,
            ],
            [
                'key' => 'require_dean_above_days',
                'value' => '14',
                'type' => 'number',
                'group' => 'approvals',
                'label' => 'Dean Required Above Days',
                'description' => 'Leave above this many days requires dean approval',
                'sort_order' => 21,
            ],
            [
                'key' => 'require_dvc_above_days',
                'value' => '21',
                'type' => 'number',
                'group' => 'approvals',
                'label' => 'DVC Required Above Days',
                'description' => 'Leave above this many days requires DVC approval',
                'sort_order' => 22,
            ],
            [
                'key' => 'hod_approval_timeout_hours',
                'value' => '48',
                'type' => 'number',
                'group' => 'approvals',
                'label' => 'HOD Approval Timeout',
                'description' => 'Hours before HOD approval escalates',
                'sort_order' => 23,
            ],
            [
                'key' => 'dean_approval_timeout_hours',
                'value' => '72',
                'type' => 'number',
                'group' => 'approvals',
                'label' => 'Dean Approval Timeout',
                'description' => 'Hours before dean approval escalates',
                'sort_order' => 24,
            ],

            // ============================================
            // NOTIFICATION SETTINGS
            // ============================================
            [
                'key' => 'email_notifications_enabled',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'notifications',
                'label' => 'Enable Email Notifications',
                'description' => 'Send email notifications for approvals',
                'sort_order' => 30,
            ],
            [
                'key' => 'browser_notifications_enabled',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'notifications',
                'label' => 'Enable Browser Notifications',
                'description' => 'Show browser notifications',
                'sort_order' => 31,
            ],
            [
                'key' => 'sms_notifications_enabled',
                'value' => 'false',
                'type' => 'boolean',
                'group' => 'notifications',
                'label' => 'Enable SMS Notifications',
                'description' => 'Send SMS for urgent approvals',
                'sort_order' => 32,
            ],
            [
                'key' => 'notification_reminder_days',
                'value' => '3',
                'type' => 'number',
                'group' => 'notifications',
                'label' => 'Reminder Days',
                'description' => 'Days before leave start to send reminder',
                'sort_order' => 33,
            ],

            // ============================================
            // DOCUMENT SETTINGS
            // ============================================
            [
                'key' => 'require_supporting_documents',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'documents',
                'label' => 'Require Supporting Documents',
                'description' => 'Require documents for leave applications',
                'sort_order' => 40,
            ],
            [
                'key' => 'max_file_size_mb',
                'value' => '5',
                'type' => 'number',
                'group' => 'documents',
                'label' => 'Maximum File Size (MB)',
                'description' => 'Maximum allowed file size for uploads',
                'sort_order' => 41,
            ],
            [
                'key' => 'allowed_file_types',
                'value' => 'pdf,jpg,jpeg,png,doc,docx',
                'type' => 'text',
                'group' => 'documents',
                'label' => 'Allowed File Types',
                'description' => 'Comma-separated list of allowed file extensions',
                'sort_order' => 42,
            ],

            // ============================================
            // SECURITY SETTINGS
            // ============================================
            [
                'key' => 'session_timeout_minutes',
                'value' => '60',
                'type' => 'number',
                'group' => 'security',
                'label' => 'Session Timeout (minutes)',
                'description' => 'Inactive session timeout duration',
                'sort_order' => 50,
            ],
            [
                'key' => 'max_login_attempts',
                'value' => '5',
                'type' => 'number',
                'group' => 'security',
                'label' => 'Max Login Attempts',
                'description' => 'Maximum failed login attempts before lockout',
                'sort_order' => 51,
            ],
            [
                'key' => 'require_password_change_days',
                'value' => '90',
                'type' => 'number',
                'group' => 'security',
                'label' => 'Password Change Interval (days)',
                'description' => 'Days after which password must be changed',
                'sort_order' => 52,
            ],
            [
                'key' => 'qr_code_expiry_hours',
                'value' => '24',
                'type' => 'number',
                'group' => 'security',
                'label' => 'QR Code Expiry (hours)',
                'description' => 'Hours after which QR code expires',
                'sort_order' => 53,
            ],

            // ============================================
            // REPORT SETTINGS
            // ============================================
            [
                'key' => 'default_report_format',
                'value' => 'pdf',
                'type' => 'select',
                'group' => 'reports',
                'label' => 'Default Report Format',
                'description' => 'Default export format for reports',
                'options' => json_encode(['pdf' => 'PDF', 'excel' => 'Excel', 'csv' => 'CSV']),
                'sort_order' => 60,
            ],
            [
                'key' => 'reports_retention_days',
                'value' => '365',
                'type' => 'number',
                'group' => 'reports',
                'label' => 'Report Retention (days)',
                'description' => 'Days to keep generated reports',
                'sort_order' => 61,
            ],

            // ============================================
            // BACKUP SETTINGS
            // ============================================
            [
                'key' => 'auto_backup_enabled',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'backup',
                'label' => 'Auto Backup Enabled',
                'description' => 'Automatically backup system daily',
                'sort_order' => 70,
            ],
            [
                'key' => 'backup_retention_days',
                'value' => '30',
                'type' => 'number',
                'group' => 'backup',
                'label' => 'Backup Retention (days)',
                'description' => 'Days to keep old backups',
                'sort_order' => 71,
            ],

            // ============================================
            // ACADEMIC CALENDAR SETTINGS
            // ============================================
            [
                'key' => 'academic_year_start_month',
                'value' => '1',
                'type' => 'number',
                'group' => 'academic',
                'label' => 'Academic Year Start Month',
                'description' => 'Month when academic year starts (1-12)',
                'sort_order' => 80,
            ],
            [
                'key' => 'block_leave_during_exams',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'academic',
                'label' => 'Block Leave During Exams',
                'description' => 'Prevent leave during examination period',
                'sort_order' => 81,
            ],
        ];

        foreach ($adminSettings as $setting) {
            DB::table('settings')->updateOrInsert(
                ['key' => $setting['key'], 'role' => null, 'user_id' => null],
                array_merge($setting, ['role' => null, 'user_id' => null])
            );
        }

        $this->command->info('Admin settings seeded successfully!');
        $this->command->info('Total settings: '.DB::table('settings')->count());
    }
}
