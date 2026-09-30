<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OfficeActionOfficersTableSeeder extends Seeder
{
    public function run()
    {
        // Get all active offices
        $offices = DB::table('offices')->where('is_active', true)->get();

        if ($offices->isEmpty()) {
            $this->command->error('No offices found. Run OfficesTableSeeder first.');

            return;
        }

        // Get any active staff member
        $defaultStaff = DB::table('staff')->where('status', 'active')->first();

        if (! $defaultStaff) {
            $this->command->error('No active staff found. Please add staff first.');

            return;
        }

        // Build staff name from available columns
        $staffName = '';
        if (isset($defaultStaff->first_name) && isset($defaultStaff->last_name)) {
            $staffName = $defaultStaff->first_name.' '.$defaultStaff->last_name;
        } elseif (isset($defaultStaff->name)) {
            $staffName = $defaultStaff->name;
        } elseif (isset($defaultStaff->full_name)) {
            $staffName = $defaultStaff->full_name;
        } else {
            $staffName = 'Staff ID: '.$defaultStaff->id;
        }

        $officerCount = 0;

        foreach ($offices as $office) {
            // Check if office already has action officers assigned
            $existing = DB::table('office_action_officers')
                ->where('office_id', $office->id)
                ->exists();

            if ($existing) {
                continue;
            }

            // Assign the default staff as action officer for all offices
            DB::table('office_action_officers')->insert([
                'office_id' => $office->id,
                'staff_id' => $defaultStaff->id,
                'role_in_office' => 'Action Officer',
                'is_default' => true,
                'can_approve' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $officerCount++;
        }

        $this->command->info('Office Action Officers seeded successfully. Total assignments: '.$officerCount);
        $this->command->warn('Note: All offices assigned to the same staff member ('.$staffName.'). You can reassign manually in the admin panel.');
    }
}
