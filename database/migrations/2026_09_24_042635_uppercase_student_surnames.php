<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Surnames are shown in capitals on every SJUT letter ("MALECELA, Nyagalu
 * Michael") but were stored exactly as typed in the roster. New writes are
 * now uppercased by the Student model; this brings the existing rows in
 * line, and rebuilds the searchable `full_name` to match.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('students')
            ->whereNotNull('last_name')
            ->orderBy('id')
            ->each(function ($student) {
                $surname = mb_strtoupper(trim($student->last_name));

                DB::table('students')->where('id', $student->id)->update([
                    'last_name' => $surname,
                    'full_name' => trim("{$surname} {$student->first_name} {$student->middle_name}"),
                ]);
            });
    }

    public function down(): void
    {
        // Original letter-case of surnames is not recoverable, and uppercase is the intended form.
    }
};
