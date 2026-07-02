<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\School;
use App\Models\Campus;

class SchoolSeeder extends Seeder
{
    public function run()
    {
        // Find campuses
        $mainCampus = Campus::where('name', 'Main Campus - Dodoma')->first();
        $kampalaCampus = Campus::where('name', 'Kampala Campus')->first();

        if (!$mainCampus) {
            $this->command->error('Main Campus not found. Run CampusSeeder first.');
            return;
        }

        $schools = [
            // Main Campus Schools
            [
                'campus_id' => $mainCampus->id,
                'name' => 'School of Business and Economics',
                'code' => 'SBE',
                'status' => 'active'
            ],
            [
                'campus_id' => $mainCampus->id,
                'name' => 'School of Law',
                'code' => 'LAW',
                'status' => 'active'
            ],
            [
                'campus_id' => $mainCampus->id,
                'name' => 'School of Science and Technology',
                'code' => 'SST',
                'status' => 'active'
            ],
            [
                'campus_id' => $mainCampus->id,
                'name' => 'School of Education',
                'code' => 'EDU',
                'status' => 'active'
            ],
        ];

        // Kampala Campus Schools
        if ($kampalaCampus) {
            $schools[] = [
                'campus_id' => $kampalaCampus->id,
                'name' => 'School of Health Sciences',
                'code' => 'SHS',
                'status' => 'active'
            ];
        }

        foreach ($schools as $schoolData) {
            $school = School::updateOrCreate(
                [
                    'campus_id' => $schoolData['campus_id'],
                    'name' => $schoolData['name']
                ],
                [
                    'code' => $schoolData['code'],
                    'status' => $schoolData['status']
                ]
            );
            $this->command->info('✓ School created: ' . $school->name);
        }
    }
}