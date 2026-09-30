<?php

namespace App\Services;

use App\Models\ImportSequence;

class SequenceNumberService
{
    /**
     * Generate unique sequence number for download/upload
     * Format: {ROLE}{YYMMDD}{HH}{SEQUENCE}
     * Example: ADM260502150001 (never resets, continues forever)
     */
    public function generate($roleCode)
    {
        // Get or create sequence record for this role
        $sequence = ImportSequence::firstOrCreate(
            ['role_code' => $roleCode],
            ['last_sequence' => 0]
        );

        // Increment sequence (never resets)
        $sequence->increment('last_sequence');
        $sequence->refresh();

        $seqNumber = str_pad($sequence->last_sequence, 4, '0', STR_PAD_LEFT);
        $date = now()->format('ymd');
        $hour = now()->format('H');

        return $roleCode.$date.$hour.$seqNumber;
    }

    /**
     * Get role code from user's role
     */
    public function getRoleCode($user)
    {
        if ($user->hasRole('admin')) {
            return 'ADM';
        } elseif ($user->hasRole('hr-manager')) {
            return 'HRM';
        } elseif ($user->hasRole('hr-officer')) {
            return 'HRO';
        }

        return 'STA';
    }
}
