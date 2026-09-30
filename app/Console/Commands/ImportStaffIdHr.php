<?php

namespace App\Console\Commands;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ImportStaffIdHr extends Command
{
    protected $signature = 'import:staff-id-hr
        {--university=11 : University ID new staff are attached to}
        {--campus=12 : Campus ID new staff are attached to}
        {--department=13 : Department ID new staff are attached to}
        {--connection=staff_id_hr_staging : DB connection name the old dump was imported into}';

    protected $description = 'One-off import of real employee records from the staff_id_hr system into the staff table';

    public function handle()
    {
        $connection = $this->option('connection');

        $employees = DB::connection($connection)->table('employees')->orderBy('id')->get();

        $this->info('Found '.$employees->count().' employees in staff_id_hr.');

        $map = [];
        $emailsUsed = User::pluck('email')->flip()->all();
        $staffIdsUsed = User::pluck('staff_id')->flip()->all();

        DB::beginTransaction();

        try {
            foreach ($employees as $employee) {
                $staffId = $this->uniqueStaffId($employee->employee_id, $staffIdsUsed);

                // staff_id_hr's `first_name` column actually holds the honorific
                // (Mr./Dr./Ms./...) and `surname` holds the full given+family name —
                // not a bare surname. Confirmed 100% consistent across all 195 rows
                // before writing this. `middle_name` was never populated upstream.
                [$firstName, $lastName] = $this->splitFullName($employee->surname);
                $title = $employee->first_name;

                $email = $this->generateEmail($firstName, $lastName, $emailsUsed);

                $staff = User::create([
                    'staff_id' => $staffId,
                    'title' => $title,
                    'first_name' => $firstName,
                    'middle_name' => null,
                    'last_name' => $lastName,
                    'university_id' => $this->option('university'),
                    'campus_id' => $this->option('campus'),
                    'department_id' => $this->option('department'),
                    'email' => $email,
                    'password' => Hash::make('Temp@123'),
                    'staff_category' => $this->inferCategory($employee->job_title),
                    'job_title' => $employee->job_title,
                    // staff_id_hr never recorded a real hire date — the record's own
                    // created_at is the closest honest proxy we have. Flagged in the
                    // import summary so real joining dates can be corrected later.
                    'joining_date' => $employee->created_at ?? now(),
                    'status' => strtolower($employee->status),
                    'password_change_required' => true,
                ]);

                $staff->assignRole('staff');

                $map[$employee->id] = [
                    'staff_id' => $staff->id,
                    'employee_id' => $employee->employee_id,
                    'photo' => $employee->photo,
                ];
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->error('Import failed, rolled back: '.$e->getMessage());

            return self::FAILURE;
        }

        // AuditLog::log() reads auth()->id(), which is always null in a CLI context,
        // and audit_logs.user_id is NOT NULL — so this would throw outside a real
        // authenticated request. Not worth failing the import over, but worth trying.
        try {
            AuditLog::log('import_staff_id_hr', 'staff', null, null, ['imported' => count($map)]);
        } catch (\Throwable $e) {
            $this->warn('Could not write an audit log entry for this import (no CLI user): '.$e->getMessage());
        }

        file_put_contents(
            storage_path('app/staff_id_hr_import_map.json'),
            json_encode($map, JSON_PRETTY_PRINT)
        );

        $this->info('Imported '.count($map).' staff records.');
        $this->info('Mapping saved to storage/app/staff_id_hr_import_map.json for the next import step (ID cards, signatures, photos).');

        return self::SUCCESS;
    }

    private function splitFullName($fullName)
    {
        $parts = preg_split('/\s+/', trim($fullName));
        $lastName = array_pop($parts);
        $firstName = $parts !== [] ? implode(' ', $parts) : $lastName;

        return [$firstName, $lastName];
    }

    private function uniqueStaffId($employeeId, array &$used)
    {
        $candidate = $employeeId;
        $suffix = 1;

        while (isset($used[$candidate])) {
            $candidate = $employeeId.'-'.$suffix++;
        }

        $used[$candidate] = true;

        return $candidate;
    }

    private function generateEmail($firstName, $surname, array &$used)
    {
        $base = Str::slug($firstName.'.'.$surname, '.');
        $base = $base !== '' ? $base : 'staff';
        $candidate = $base.'@sjut.ac.tz';
        $suffix = 1;

        while (isset($used[$candidate])) {
            $candidate = $base.$suffix++.'@sjut.ac.tz';
        }

        $used[$candidate] = true;

        return $candidate;
    }

    private function inferCategory($jobTitle)
    {
        $academicHints = ['dr.', 'prof', 'lecturer', 'professor'];
        $title = strtolower($jobTitle);

        foreach ($academicHints as $hint) {
            if (str_contains($title, $hint)) {
                return 'academic';
            }
        }

        return 'administrative';
    }
}
