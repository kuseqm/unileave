<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Fields identified directly from four real SJUT alumni letters (General
 * Introduction, English Proficiency "To Whom It May Concern", General
 * Recommendation, and a DataFlow-style multi-student verification
 * response) - see [[project_gmsa_graduation_verification_slice]] for the
 * full analysis. Everything here is nullable - `raw_data` on the base
 * students table already exists for anything not yet mapped, and old
 * demo rows should keep working without these.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            // Real letters format names as "LASTNAME, First Middle" - kept
            // as separate parts (not parsed out of full_name) so that
            // format can be produced reliably.
            $table->string('last_name')->nullable()->after('full_name');
            $table->string('first_name')->nullable()->after('last_name');
            $table->string('middle_name')->nullable()->after('first_name');

            // "Bachelor of Arts with Education - [BAED]" - name and
            // abbreviation kept separate so either can be used alone.
            $table->string('degree_name')->nullable()->after('program_id');
            $table->string('degree_abbreviation')->nullable()->after('degree_name');

            // "from 2010/2011 to 2012/2013 academic years" - stored as the
            // exact strings SIMS/the registry already uses (same "no
            // format validation, store as received" rule as registration
            // numbers), not derived from a single start year + duration.
            $table->string('programme_start_academic_year')->nullable()->after('academic_year');
            $table->string('programme_end_academic_year')->nullable()->after('programme_start_academic_year');
            // "Three-year Bachelor of Arts programme" - kept as its own
            // explicit field rather than computed from the start/end
            // academic years above, since the letters state it directly
            // and computing it back out risks disagreeing with the
            // source wording.
            $table->unsignedTinyInteger('programme_duration_years')->nullable()->after('programme_end_academic_year');

            // "Upper Second Class GPA of 3.5"
            $table->string('classification')->nullable()->after('graduation_date');
            $table->decimal('gpa', 3, 2)->nullable()->after('classification');

            // "the Three Graduation Ceremony" - the congregation's own
            // sequence number, spelled out via DateToWordsHelper::ordinalDay()
            // at render time rather than stored as text.
            $table->unsignedSmallInteger('graduation_ceremony_number')->nullable()->after('gpa');

            // The DataFlow-style verification-response letter's per-row
            // "BARCODE" column - a permanent code tied to the student's
            // own certificate, distinct from a letter's own secure_code
            // (which is generated fresh per issued document).
            $table->string('certificate_barcode')->nullable()->unique()->after('graduation_ceremony_number');
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn([
                'last_name', 'first_name', 'middle_name',
                'degree_name', 'degree_abbreviation',
                'programme_start_academic_year', 'programme_end_academic_year', 'programme_duration_years',
                'classification', 'gpa', 'graduation_ceremony_number', 'certificate_barcode',
            ]);
        });
    }
};
