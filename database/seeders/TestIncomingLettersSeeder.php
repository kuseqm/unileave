<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TestIncomingLettersSeeder extends Seeder
{
    public function run()
    {
        // Get first office and staff for test data
        $firstOffice = DB::table('offices')->first();
        $firstStaff = DB::table('staff')->first();

        if (! $firstOffice || ! $firstStaff) {
            $this->command->error('No offices or staff found. Run OfficesTableSeeder first.');

            return;
        }

        // Generate test reference numbers
        $year = date('Y');

        $letters = [
            [
                'ref_number' => 'UC-IN-'.$year.'-00001',
                'date_received' => '2025-06-10',
                'sender' => 'Ministry of Education',
                'office_id' => $firstOffice->id,
                'subject' => 'Request for Accreditation Visit',
                'pdf_path' => 'test/accreditation_request.pdf',
                'assigned_to' => $firstStaff->id,
                'status' => 'assigned',
                'priority' => 'urgent',
                'remarks' => 'Please expedite',
                'created_by' => $firstStaff->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ref_number' => 'UC-IN-'.$year.'-00002',
                'date_received' => '2025-06-11',
                'sender' => 'National Council for Technical Education',
                'office_id' => $firstOffice->id,
                'subject' => 'Programme Reaccreditation Submission',
                'pdf_path' => 'test/reaccreditation.pdf',
                'assigned_to' => $firstStaff->id,
                'status' => 'received',
                'priority' => 'normal',
                'remarks' => null,
                'created_by' => $firstStaff->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ref_number' => 'UC-IN-'.$year.'-00003',
                'date_received' => '2025-06-12',
                'sender' => 'Student Representative Council',
                'office_id' => $firstOffice->id,
                'subject' => 'Complaint about Examination Results',
                'pdf_path' => 'test/complaint.pdf',
                'assigned_to' => null,
                'status' => 'received',
                'priority' => 'confidential',
                'remarks' => 'Handle with care',
                'created_by' => $firstStaff->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($letters as $letter) {
            DB::table('incoming_letters')->insert($letter);
        }

        $this->command->info('Test incoming letters seeded successfully.');
    }
}
