<?php

// database/seeders/CampusSeeder.php

namespace Database\Seeders;

use App\Models\Campus;
use App\Models\University;
use Illuminate\Database\Seeder;

class CampusSeeder extends Seeder
{
    public function run()
    {
        $university = University::where('name', 'St Martine\'s University of Testing UniLeave')->first();

        if (! $university) {
            $this->command->error('No university found. Run UniversitySeeder first.');

            return;
        }

        $campuses = [
            ['name' => 'Main Campus - Dodoma', 'code' => 'DOD', 'address' => 'Dodoma City Centre, Dodoma, Tanzania'],
            ['name' => 'Kampala Campus', 'code' => 'KLA', 'address' => 'Kampala, Uganda'],
            ['name' => 'Arusha Campus', 'code' => 'ARA', 'address' => 'Arusha, Tanzania'],
            ['name' => 'Mwanza Campus', 'code' => 'MWZ', 'address' => 'Mwanza, Tanzania'],
            ['name' => 'Zanzibar Campus', 'code' => 'ZNZ', 'address' => 'Zanzibar, Tanzania'],
        ];

        foreach ($campuses as $campus) {
            Campus::updateOrCreate(
                [
                    'university_id' => $university->id,
                    'name' => $campus['name'],
                ],
                [
                    'code' => $campus['code'],
                    'address' => $campus['address'],
                    'status' => 'active',
                ]
            );
        }
    }
}
