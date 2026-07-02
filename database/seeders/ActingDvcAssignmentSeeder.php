<?php
// database/seeders/ActingDvcAssignmentSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class ActingDvcAssignmentSeeder extends Seeder
{
    public function run()
    {
        // Find users by email (no hardcoded IDs)
        $hod = User::where('email', 'michael.mushi@smut.ac.tz')->first();  // HOD Accounting
        $hr = User::where('email', 'sarah.johnson@smut.ac.tz')->first();   // HR Officer
        $admin = User::where('email', 'admin@smut.ac.tz')->first();         // Admin

        // Only insert if all required users exist
        if ($hod && $admin) {
            DB::table('acting_dvc_assignments')->insert([
                [
                    'staff_id' => $hod->id,
                    'role' => 'dvca',
                    'start_date' => '2026-05-01',
                    'end_date' => '2026-05-15',
                    'reason' => 'DVCA on conference leave',
                    'approved_by' => $admin->id,
                    'status' => 'active',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }

        if ($hr && $admin) {
            DB::table('acting_dvc_assignments')->insert([
                [
                    'staff_id' => $hr->id,
                    'role' => 'dvc-pfa',
                    'start_date' => '2026-06-01',
                    'end_date' => '2026-06-10',
                    'reason' => 'DVC-PFA on medical leave',
                    'approved_by' => $admin->id,
                    'status' => 'active',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }
    }
}