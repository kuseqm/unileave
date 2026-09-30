<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Migrate existing sensitive data to encrypted columns
        DB::beginTransaction();

        try {
            // Use DB query to avoid model issues
            $staffRecords = DB::table('staff')->get();

            foreach ($staffRecords as $person) {
                $updateData = [];

                if ($person->national_id) {
                    $updateData['national_id_encrypted'] = Crypt::encrypt($person->national_id);
                }

                if ($person->tin_number) {
                    $updateData['tin_number_encrypted'] = Crypt::encrypt($person->tin_number);
                }

                if ($person->nssf_number) {
                    $updateData['nssf_number_encrypted'] = Crypt::encrypt($person->nssf_number);
                }

                if ($person->basic_salary) {
                    $updateData['basic_salary_encrypted'] = Crypt::encrypt($person->basic_salary);
                }

                if (! empty($updateData)) {
                    DB::table('staff')->where('id', $person->id)->update($updateData);
                }
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Clear encrypted columns (keep original data for rollback)
        DB::table('staff')->update([
            'national_id_encrypted' => null,
            'tin_number_encrypted' => null,
            'nssf_number_encrypted' => null,
            'basic_salary_encrypted' => null,
        ]);
    }
};
