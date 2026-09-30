<?php

namespace App\Traits;

use App\Models\ImportSequence;

/**
 * One shared reference-number scheme for every trackable upload/download
 * batch in the app ({role_code}{ymdH}{serial}, e.g. ROS26092912345,
 * CRT26092912346) - each feature picks its own 3-letter role code and gets
 * a unique, human-readable, chronologically-sortable reference from the
 * same counter table (import_sequences).
 */
trait GeneratesImportReference
{
    private function generateImportReference(string $roleCode): string
    {
        $sequence = ImportSequence::firstOrCreate(['role_code' => $roleCode], ['last_sequence' => 0]);
        $sequence->increment('last_sequence');
        $sequence->refresh();

        return $roleCode.now()->format('ymdH').str_pad($sequence->last_sequence, 4, '0', STR_PAD_LEFT);
    }
}
