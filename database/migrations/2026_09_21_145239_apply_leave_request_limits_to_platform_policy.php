<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * The leave application form now enforces the HR policy's notice period and
 * request-length limits (App\Services\LeavePolicy). Bring an existing platform
 * HR leave policy in line, the same way PolicySeeder builds a fresh one:
 *
 *  - drop the old "30 days for every request" rule, which was never enforced
 *    and would now wrongly cap maternity (84 days) and study leave;
 *  - ask for three days' notice, except for sick and compassionate leave.
 *
 * A fresh database has no policy yet at this point; PolicySeeder covers that.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('policies') || ! Schema::hasTable('policy_rules')) {
            return;
        }

        $policy = DB::table('policies')
            ->whereNull('university_id')
            ->where('code', 'HR-LEAVE-001')
            ->first();

        if (! $policy) {
            return;
        }

        DB::table('policy_rules')
            ->where('policy_id', $policy->id)
            ->where('key', 'max_consecutive_leave_days')
            ->whereNull('condition_field')
            ->where('value', '30')
            ->delete();

        $rules = [
            ['value' => '3', 'priority' => 0, 'condition_field' => null, 'operator' => null, 'condition_value' => null],
            ['value' => '0', 'priority' => 10, 'condition_field' => 'leave_type', 'operator' => 'in', 'condition_value' => 'LC2,LC3'],
        ];

        foreach ($rules as $rule) {
            $exists = DB::table('policy_rules')
                ->where('policy_id', $policy->id)
                ->where('key', 'min_notice_days')
                ->where('condition_field', $rule['condition_field'])
                ->exists();

            if ($exists) {
                continue;
            }

            DB::table('policy_rules')->insert($rule + [
                'policy_id' => $policy->id,
                'key' => 'min_notice_days',
                'label' => 'Minimum notice before leave starts',
                'type' => 'numeric',
                'is_active' => true,
                'created_by' => $policy->created_by,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        // Data-only change: nothing to undo (the removed rule was never enforced).
    }
};
