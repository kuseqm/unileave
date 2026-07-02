<?php
// database/seeders/UniversitySeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\University;

class UniversitySeeder extends Seeder
{
    public function run()
    {
        $universities = [
            [
                'name' => 'St Martine\'s University of Testing UniLeave',
                'code' => 'SMUT',
                'gps_lat' => -6.1670,
                'gps_lng' => 35.7500,
                'timezone' => 'Africa/Dar_es_Salaam',
                'address' => 'P.O. Box 47, Dodoma, Tanzania',
                'phone' => '+255 26 232 0001',
                'email' => 'info@smut.ac.tz',
                'status' => 'active',
            ],
            [
                'name' => 'University of Dar es Salaam',
                'code' => 'UDSM',
                'gps_lat' => -6.7800,
                'gps_lng' => 39.2020,
                'timezone' => 'Africa/Dar_es_Salaam',
                'address' => 'P.O. Box 35091, Dar es Salaam, Tanzania',
                'phone' => '+255 22 2410500',
                'email' => 'info@udsm.ac.tz',
                'status' => 'active',
            ],
        ];

        foreach ($universities as $uniData) {
            $university = University::updateOrCreate(
                ['name' => $uniData['name']],
                $uniData
            );
            $this->command->info('✓ University: ' . $university->name);
        }
    }
}