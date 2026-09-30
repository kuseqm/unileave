<?php

// database/seeders/LeaveTypeSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LeaveTypeSeeder extends Seeder
{
    public function run()
    {
        DB::table('leave_types')->insert([
            [
                'id' => 1,
                'code' => 'LC1',
                'name' => 'Annual Leave',
                'days_allowed' => 28,  // ✅ ADD THIS
                'description' => 'Standard paid annual leave for vacation and rest',
                'requires_certificate' => false,
                'requires_letter' => false,
                'deduct_balance' => true,
                'vc_discretion' => false,
                'max_consecutive_days' => 28,
                'is_configurable' => false,
                'status' => 'active',
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'code' => 'LC2',
                'name' => 'Sick Leave',
                'days_allowed' => 28,  // ✅ ADD THIS
                'description' => 'Leave due to personal illness or injury',
                'requires_certificate' => true,
                'requires_letter' => false,
                'deduct_balance' => true,
                'vc_discretion' => false,
                'max_consecutive_days' => 28,
                'is_configurable' => false,
                'status' => 'active',
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'code' => 'LC3',
                'name' => 'Compassionate Leave',
                'days_allowed' => 5,  // ✅ ADD THIS
                'description' => 'Leave due to death of immediate family member or urgent family matters',
                'requires_certificate' => false,
                'requires_letter' => false,
                'deduct_balance' => true,
                'vc_discretion' => false,
                'max_consecutive_days' => 5,
                'is_configurable' => false,
                'status' => 'active',
                'sort_order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'code' => 'LC4',
                'name' => 'Maternity/Paternity Leave',
                'days_allowed' => 84,  // ✅ ADD THIS
                'description' => 'Leave for childbirth and childcare',
                'requires_certificate' => true,
                'requires_letter' => true,
                'deduct_balance' => true,
                'vc_discretion' => false,
                'max_consecutive_days' => 84,
                'is_configurable' => false,
                'status' => 'active',
                'sort_order' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 5,
                'code' => 'LC5',
                'name' => 'Special Leave',
                'days_allowed' => 10,  // ✅ ADD THIS
                'description' => 'Leave for sporting events, conferences, seminars, examinations',
                'requires_certificate' => false,
                'requires_letter' => false,
                'deduct_balance' => true,
                'vc_discretion' => true,
                'max_consecutive_days' => 10,
                'is_configurable' => false,
                'status' => 'active',
                'sort_order' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 6,
                'code' => 'LC6',
                'name' => 'Study Leave',
                'days_allowed' => 365,  // ✅ ADD THIS
                'description' => 'Leave for pursuing formal education',
                'requires_certificate' => false,
                'requires_letter' => true,
                'deduct_balance' => true,
                'vc_discretion' => false,
                'max_consecutive_days' => 365,
                'is_configurable' => false,
                'status' => 'active',
                'sort_order' => 6,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 7,
                'code' => 'LC7',
                'name' => 'Sabbatical Leave',
                'days_allowed' => 365,  // ✅ ADD THIS
                'description' => 'Leave for research or professional development',
                'requires_certificate' => false,
                'requires_letter' => true,
                'deduct_balance' => true,
                'vc_discretion' => false,
                'max_consecutive_days' => 365,
                'is_configurable' => false,
                'status' => 'active',
                'sort_order' => 7,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 8,
                'code' => 'LC8',
                'name' => 'Leave Without Pay',
                'days_allowed' => 90,  // ✅ ADD THIS
                'description' => 'Unpaid leave requested by staff',
                'requires_certificate' => false,
                'requires_letter' => true,
                'deduct_balance' => false,
                'vc_discretion' => false,
                'max_consecutive_days' => 90,
                'is_configurable' => false,
                'status' => 'active',
                'sort_order' => 8,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 9,
                'code' => 'LC9',
                'name' => 'Care\'s Leave',
                'days_allowed' => 28,  // ✅ ADD THIS
                'description' => 'Leave to care for sick family member',
                'requires_certificate' => true,
                'requires_letter' => false,
                'deduct_balance' => true,
                'vc_discretion' => false,
                'max_consecutive_days' => 28,
                'is_configurable' => false,
                'status' => 'active',
                'sort_order' => 9,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 10,
                'code' => 'LC10',
                'name' => 'University Business/Travel',
                'days_allowed' => null,  // ✅ ADD THIS
                'description' => 'Official duty travel',
                'requires_certificate' => false,
                'requires_letter' => false,
                'deduct_balance' => false,
                'vc_discretion' => false,
                'max_consecutive_days' => null,
                'is_configurable' => false,
                'status' => 'active',
                'sort_order' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
