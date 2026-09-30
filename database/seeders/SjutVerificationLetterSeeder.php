<?php

namespace Database\Seeders;

use App\Models\LetterSignatory;
use App\Models\ReferenceFormat;
use App\Models\University;
use Illuminate\Database\Seeder;

/**
 * SJUT's two usual signatories of certificate / transcript verification
 * letters (from the real samples in Desktop/KUSEKWA/VERIFICATION), and the
 * Registry reference format their letters follow, e.g.
 * "SJUT/SVAC/013/04/VOL IV/181".
 *
 * The volume size (200), the volume the file is on (IV) and the starting count
 * (194, the highest number seen in the samples) are starting values - the Registry sets the real ones under
 * Reference Formats.
 */
class SjutVerificationLetterSeeder extends Seeder
{
    public function run(): void
    {
        $university = University::where('code', 'SJUT')->firstOrFail();

        LetterSignatory::firstOrCreate(
            ['university_id' => $university->id, 'name' => 'Professor Valence M. Kessy Ndesendo, PhD'],
            [
                'title_en' => 'Deputy Vice Chancellor (Academic)',
                'unit_en' => "St John's University of Tanzania",
                'title_sw' => 'Naibu Makamu Mkuu wa Chuo (Taaluma)',
                'unit_sw' => "Chuo Kikuu cha St John's cha Tanzania",
                'office_en' => 'Office of the Deputy Vice Chancellor (Academic)',
                'phone' => '+255 67 70 86094',
                'email' => 'dvca@sjut.ac.tz',
            ]
        );

        LetterSignatory::firstOrCreate(
            ['university_id' => $university->id, 'name' => 'Dr Laurent Kaburire, PhD'],
            [
                'title_en' => 'Director of Undergraduate Studies (DUS)',
                'unit_en' => null,
                'title_sw' => 'Mkurugenzi',
                'unit_sw' => 'Kurugenzi ya Taaluma za Awali (DUS)',
                'office_en' => 'Directorate of Undergraduate Studies (DUS)',
                'phone' => '+255677086095',
                'email' => 'admin@sjut.ac.tz',
            ]
        );

        ReferenceFormat::firstOrCreate(
            ['university_id' => $university->id, 'document_type' => 'verification'],
            [
                'name' => 'Certificate & Transcript Verification Letters',
                'format_pattern' => '{university_code}/SVAC/013/04/{volume}/{serial}',
                'separator' => '/',
                'serial_length' => 3,
                'serial_start' => 1,
                'current_serial' => 194,
                'file_capacity' => 200,
                'volume_start' => 4,
                'file_naming_pattern' => 'SJUT-SVAC-VER',
                'auto_create_new_file' => true,
                'track_physical_location' => false,
                'is_active' => true,
            ]
        );
    }
}
