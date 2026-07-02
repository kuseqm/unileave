<?php
// database/seeders/LeaveBalanceSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\LeaveType;
use App\Models\LeaveBalance;

class LeaveBalanceSeeder extends Seeder
{
    public function run()
    {
        $currentYear = date('Y');
        
        $staffMembers = User::whereIn('staff_category', ['academic', 'administrative'])->get();
        
        $annualLeave = LeaveType::where('code', 'LC1')->first();
        $sickLeave = LeaveType::where('code', 'LC2')->first();
        $compassionateLeave = LeaveType::where('code', 'LC3')->first();
        $maternityLeave = LeaveType::where('code', 'LC4')->first();

        if (!$annualLeave || !$sickLeave || !$compassionateLeave || !$maternityLeave) {
            $this->command->error('Leave types not found. Run LeaveTypeSeeder first.');
            return;
        }

        $balanceRules = [
            ['leave_type' => $annualLeave, 'balance' => 28],
            ['leave_type' => $sickLeave, 'balance' => 12],
            ['leave_type' => $compassionateLeave, 'balance' => 3],
            ['leave_type' => $maternityLeave, 'balance' => 84],
        ];

        foreach ($staffMembers as $staff) {
            foreach ($balanceRules as $rule) {
                LeaveBalance::updateOrCreate(
                    [
                        'staff_id' => $staff->id,
                        'leave_type_id' => $rule['leave_type']->id,
                        'year' => $currentYear,
                    ],
                    [
                        'balance' => $rule['balance'],
                        'carried_forward' => 0,
                    ]
                );
            }
        }

        $this->command->info('✓ Leave balances created for ' . $staffMembers->count() . ' staff members');
    }
}