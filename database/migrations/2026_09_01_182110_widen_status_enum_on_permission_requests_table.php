<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * The controller and model (getStatusLabelAttribute/getStatusColorAttribute)
     * have always written/expected 'hod_approved', 'hod_rejected', 'hr_approved',
     * 'hr_rejected' as intermediate statuses, but the enum was only ever
     * ('pending','approved','rejected','cancelled') - so every HOD approve/reject
     * and HR reject call has been failing with "Data truncated for column 'status'".
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE permission_requests MODIFY status ENUM(
            'pending','hod_approved','hod_rejected','hr_approved','hr_rejected','approved','rejected','cancelled'
        ) NOT NULL DEFAULT 'pending'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE permission_requests MODIFY status ENUM(
            'pending','approved','rejected','cancelled'
        ) NOT NULL DEFAULT 'pending'");
    }
};
