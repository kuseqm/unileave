<?php

namespace Database\Seeders;

use App\Models\Program;
use App\Models\School;
use App\Models\University;
use Illuminate\Database\Seeder;

/**
 * SJUT's real programme catalog, transcribed from the registry's own
 * `duscussion1.xlsx` (S/No | Short Name | Name | Level | School/Faculty).
 * Programmes own by School directly, per [[project_gmsa_graduation_roster_import]]
 * - the "department" layer was corrected away since a single programme's
 * curriculum draws courses from several departments.
 *
 * A programme with subject "combinations" (e.g. BAED - Bachelor of Arts
 * with Education) gets BOTH its own base row AND one row per combination,
 * each with the combination's own code and a composed name - e.g.
 * "Bachelor of Arts with Education (Geography - History)". Both exist so a
 * roster row can match whichever level of detail the registration record
 * actually states: some historical records only cite the base programme,
 * newer ones cite the exact combination - per the user's explicit
 * request ("tukimverify mtu tuseme na combination yake kabisa").
 */
class SjutProgramCatalogSeeder extends Seeder
{
    /**
     * @var array<int, array{code: string, name: string, level: string, school: string, combos?: array<int, array{code: string, name: string}>}>
     */
    private const CATALOG = [
        ['code' => 'CCD', 'name' => 'Basic Technician Certificate in Community Development', 'level' => 'NTA Level 4', 'school' => 'IDS'],
        ['code' => 'BTCMLT', 'name' => 'Basic Technician Certificate in Medical Laboratory Sciences', 'level' => 'NTA Level 4', 'school' => 'SONU'],
        ['code' => 'BTCNU', 'name' => 'Basic Technician Certificate in Nursing and Midwifery', 'level' => 'NTA Level 4', 'school' => 'SONU'],
        ['code' => 'BTCPH', 'name' => 'Basic Technician Certificate in Pharmaceutical Sciences', 'level' => 'NTA Level 4', 'school' => 'SOPH'],
        ['code' => 'BTCSLT', 'name' => 'Basic Technician Certificate in Science and Laboratory Technology', 'level' => 'NTA Level 4', 'school' => 'SONU'],
        ['code' => 'CBA', 'name' => 'Certificate in Business Administration', 'level' => 'NTA Level 4', 'school' => 'FOCB'],
        ['code' => 'CL', 'name' => 'Certificate in Laws', 'level' => 'NTA Level 4', 'school' => 'FOCB'],
        ['code' => 'CTMLT', 'name' => 'Technician Certificate in Medical Laboratory Sciences', 'level' => 'NTA Level 5', 'school' => 'SONU'],
        ['code' => 'TCSLT', 'name' => 'Technician Certificate in Science Laboratory Technology', 'level' => 'NTA Level 5', 'school' => 'SONU'],
        ['code' => 'TCCD', 'name' => 'Technician Certificate in Community Development', 'level' => 'NTA Level 5', 'school' => 'IDS'],
        ['code' => 'TCNU', 'name' => 'Technician Certificate in Nursing and Midwifery', 'level' => 'NTA Level 5', 'school' => 'SONU'],
        ['code' => 'TCPH', 'name' => 'Technician Certificate in Pharmaceutical Science', 'level' => 'NTA Level 5', 'school' => 'SOPH'],
        ['code' => 'DBA', 'name' => 'Diploma in Business Administration', 'level' => 'NTA Level 6', 'school' => 'FOCB', 'combos' => [
            ['code' => 'SJD021', 'name' => 'Accounting'],
            ['code' => 'SJD022', 'name' => 'Human Resource Management'],
            ['code' => 'SJD023', 'name' => 'Marketing Management'],
            ['code' => 'SJD024', 'name' => 'Procurement and Supply Chain Management'],
        ]],
        ['code' => 'DL', 'name' => 'Diploma in Laws', 'level' => 'NTA Level 6', 'school' => 'FOCB'],
        ['code' => 'DPPM', 'name' => 'Diploma in Project Planning Management', 'level' => 'NTA Level 6', 'school' => 'FAHE'],
        ['code' => 'DCD', 'name' => 'Ordinary Diploma in Community Development', 'level' => 'NTA Level 6', 'school' => 'IDS'],
        ['code' => 'ODMLT', 'name' => 'Ordinary Diploma in Medical Laboratory Technology', 'level' => 'NTA Level 6', 'school' => 'SONU'],
        ['code' => 'ODNU', 'name' => 'Ordinary Diploma in Nursing and Midwifery', 'level' => 'NTA Level 6', 'school' => 'SONU'],
        ['code' => 'ODPH', 'name' => 'Ordinary Diploma in Pharmaceutical Science', 'level' => 'NTA Level 6', 'school' => 'SOPH'],
        ['code' => 'ODSLT', 'name' => 'Ordinary Diploma in Science Laboratory Technology', 'level' => 'NTA Level 6', 'school' => 'SONU'],
        ['code' => 'BAF', 'name' => 'Bachelor of Accounting and Finance', 'level' => 'Bachelor', 'school' => 'FOCB'],
        ['code' => 'BAAT', 'name' => 'Bachelor of Arts in Applied Theology', 'level' => 'Bachelor', 'school' => 'FAHE'],
        ['code' => 'BATh', 'name' => 'Bachelor of Arts in Theology', 'level' => 'Bachelor', 'school' => 'FAHE'],
        ['code' => 'BAED', 'name' => 'Bachelor of Arts with Education', 'level' => 'Bachelor', 'school' => 'FAHE', 'combos' => [
            ['code' => 'SJ00112', 'name' => 'English - Kiswahili'],
            ['code' => 'SJ00113', 'name' => 'History - Kiswahili'],
            ['code' => 'SJ00114', 'name' => 'English Language'],
            ['code' => 'SJ001111', 'name' => 'Geography - English'],
            ['code' => 'SJ001112', 'name' => 'Geography - History'],
            ['code' => 'SJ01117', 'name' => 'Geography - Kiswahili'],
            ['code' => 'SJ01118', 'name' => 'History - English'],
        ]],
        ['code' => 'BBA', 'name' => 'Bachelor of Business Administration', 'level' => 'Bachelor', 'school' => 'FOCB', 'combos' => [
            ['code' => 'SJ00401', 'name' => 'Marketing Management'],
            ['code' => 'SJ00402', 'name' => 'Procurement and Supply Chain Management'],
            ['code' => 'SJ00404', 'name' => 'Health Services Management'],
            ['code' => 'SJ00405', 'name' => 'Human Resource Management'],
        ]],
        ['code' => 'BComEd', 'name' => 'Bachelor of Commerce with Education', 'level' => 'Bachelor', 'school' => 'FOCB'],
        ['code' => 'BHSM', 'name' => 'Bachelor of Health Services Management', 'level' => 'Bachelor', 'school' => 'FOCB'],
        ['code' => 'BPharm', 'name' => 'Bachelor of Pharmacy', 'level' => 'Bachelor', 'school' => 'SOPH'],
        ['code' => 'BSc.IT', 'name' => 'Bachelor of Science in Information Technology', 'level' => 'Bachelor', 'school' => 'CICT'],
        ['code' => 'BScN', 'name' => 'Bachelor of Science in Nursing', 'level' => 'Bachelor', 'school' => 'SONU'],
        ['code' => 'BScEd', 'name' => 'Bachelor of Science with Education', 'level' => 'Bachelor', 'school' => 'FaNAS', 'combos' => [
            ['code' => 'SJ00111', 'name' => 'Geography - Chemistry'],
            ['code' => 'SJ0021', 'name' => 'Chemistry - Biology'],
            ['code' => 'SJ0022', 'name' => 'Physics - Mathematics'],
            ['code' => 'SJ0023', 'name' => 'Physics - Biology'],
            ['code' => 'SJ0024', 'name' => 'Mathematics - Chemistry'],
            ['code' => 'SJ001115', 'name' => 'Chemistry - Physics'],
            ['code' => 'SJ01116', 'name' => 'Biology - Geography'],
            ['code' => 'SJ002', 'name' => 'Mathematics and Geography'],
        ]],
        ['code' => 'MAAL', 'name' => 'Master of Arts in Applied Linguistics', 'level' => 'Masters', 'school' => 'FAHE'],
        ['code' => 'MACD', 'name' => 'Master of Arts in Community Development', 'level' => 'Masters', 'school' => 'IDS'],
        ['code' => 'MAED', 'name' => 'Master of Arts in Education', 'level' => 'Masters', 'school' => 'FAHE'],
        ['code' => 'MBA', 'name' => 'Master of Business Administration', 'level' => 'Masters', 'school' => 'FOCB', 'combos' => [
            ['code' => 'SJM031', 'name' => 'Corporate Management'],
            ['code' => 'SJM032', 'name' => 'Human Resource Management'],
            ['code' => 'SJM033', 'name' => 'Marketing Management'],
            ['code' => 'SJM034', 'name' => 'Procurement and Supply Chain Management'],
        ]],
        ['code' => 'MPH', 'name' => 'Master of Pharmacy in Pharmaceutical Public Health Management', 'level' => 'Masters', 'school' => 'SOPH'],
    ];

    public function run(): void
    {
        $university = University::where('code', 'SJUT')->first();

        if (! $university) {
            $this->command->error('SJUT university (code=SJUT) not found - aborting.');

            return;
        }

        $schools = School::whereHas('campus', fn ($q) => $q->where('university_id', $university->id))
            ->get()
            ->keyBy(fn (School $school) => mb_strtolower($school->code));

        foreach (self::CATALOG as $entry) {
            $school = $schools->get(mb_strtolower($entry['school']));

            if (! $school) {
                $this->command->warn("School code '{$entry['school']}' not found for {$entry['name']} - skipped.");

                continue;
            }

            Program::updateOrCreate(
                ['school_id' => $school->id, 'code' => $entry['code']],
                [
                    'name' => $entry['name'],
                    'study_level' => $entry['level'],
                    'status' => 'active',
                ]
            );

            foreach ($entry['combos'] ?? [] as $combo) {
                Program::updateOrCreate(
                    ['school_id' => $school->id, 'code' => $combo['code']],
                    [
                        'name' => "{$entry['name']} ({$combo['name']})",
                        'study_level' => $entry['level'],
                        'status' => 'active',
                    ]
                );
            }
        }

        $this->command->info('SJUT programme catalog seeded: '.Program::whereHas('school.campus', fn ($q) => $q->where('university_id', $university->id))->count().' programmes total.');
    }
}
