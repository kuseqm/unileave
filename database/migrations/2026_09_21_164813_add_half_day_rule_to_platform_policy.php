<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Half-day leave is now a policy setting (allow_half_day) instead of
 * something the leave screens simply did. Give an existing platform HR leave
 * policy the value PolicySeeder gives a fresh one: allowed for annual, sick
 * and compassionate leave, not for the other types.
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

        $exists = DB::table('policy_rules')
            ->where('policy_id', $policy->id)
            ->where('key', 'allow_half_day')
            ->exists();

        if ($exists) {
            return;
        }

        DB::table('policy_rules')->insert([
            'policy_id' => $policy->id,
            'key' => 'allow_half_day',
            'label' => 'Half-day leave',
            'value' => 'true',
            'type' => 'boolean',
            'condition_field' => 'leave_type',
            'operator' => 'in',
            'condition_value' => 'LC1,LC2,LC3',
            'priority' => 10,
            'is_active' => true,
            'created_by' => $policy->created_by,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        // Data-only change: nothing to undo.
    }
};
