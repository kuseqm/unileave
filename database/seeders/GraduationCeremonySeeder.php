<?php

namespace Database\Seeders;

use App\Models\GraduationCeremony;
use Illuminate\Database\Seeder;

/**
 * Real historical SJUT graduation ceremonies, transcribed from the
 * university's own "Graduation numbers" sheet (duscussion.xlsx) - the
 * sheet gives sequence number, year, and the exact date spelled out in
 * words with its weekday (e.g. "Friday, the Twenty-Third day of November
 * in the year Two Thousand and Eighteen"); the calendar date below is that
 * same date in Y-m-d form, and `DateToWordsHelper` regenerates the words
 * (including weekday) from it at render time rather than storing text.
 */
class GraduationCeremonySeeder extends Seeder
{
    public function run(): void
    {
        $ceremonies = [
            ['year' => 2010, 'sequence_number' => 1, 'ceremony_date' => '2010-11-06'],
            ['year' => 2011, 'sequence_number' => 2, 'ceremony_date' => '2011-11-05'],
            ['year' => 2012, 'sequence_number' => 3, 'ceremony_date' => '2012-11-24'],
            ['year' => 2013, 'sequence_number' => 4, 'ceremony_date' => '2013-11-23'],
            ['year' => 2014, 'sequence_number' => 5, 'ceremony_date' => '2014-12-06'],
            ['year' => 2015, 'sequence_number' => 6, 'ceremony_date' => '2015-12-05'],
            ['year' => 2016, 'sequence_number' => 7, 'ceremony_date' => '2016-12-10'],
            ['year' => 2017, 'sequence_number' => 8, 'ceremony_date' => '2017-12-12'],
            ['year' => 2018, 'sequence_number' => 9, 'ceremony_date' => '2018-11-23'],
            ['year' => 2019, 'sequence_number' => 10, 'ceremony_date' => '2019-11-23'],
            ['year' => 2020, 'sequence_number' => 11, 'ceremony_date' => '2020-12-12'],
            ['year' => 2021, 'sequence_number' => 12, 'ceremony_date' => '2021-12-11'],
            ['year' => 2022, 'sequence_number' => 13, 'ceremony_date' => '2022-12-02'],
            ['year' => 2023, 'sequence_number' => 14, 'ceremony_date' => '2023-12-01'],
            ['year' => 2024, 'sequence_number' => 15, 'ceremony_date' => '2024-11-21'],
            ['year' => 2025, 'sequence_number' => 16, 'ceremony_date' => '2025-12-06'],
        ];

        foreach ($ceremonies as $ceremony) {
            GraduationCeremony::updateOrCreate(['year' => $ceremony['year']], $ceremony);
        }
    }
}
