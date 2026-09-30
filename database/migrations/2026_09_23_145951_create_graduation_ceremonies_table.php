<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Preliminary reference table for the Graduation Roster import - one row
 * per real historical/future graduation ceremony (seeded from the
 * university's own "Graduation numbers" sheet). A student's `graduation_year`
 * is matched against `year` here to auto-fill the exact ceremony date and
 * sequence number, instead of asking the person uploading the roster to
 * know or type either one.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('graduation_ceremonies', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->unsignedSmallInteger('year')->unique();
            $table->unsignedSmallInteger('sequence_number');
            $table->date('ceremony_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('graduation_ceremonies');
    }
};
