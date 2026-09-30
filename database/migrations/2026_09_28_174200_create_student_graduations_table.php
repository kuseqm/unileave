<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Splits graduation-specific facts out of the students table so that one
 * person (one registration number) can have multiple graduations over time
 * - e.g. NTA Level 5 in one year and NTA Level 6 in another - without
 * overwriting the earlier record. Existing rows are copied 1:1 into
 * student_graduations on migration, with students remaining as the person
 * record.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_graduations', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('university_id')->constrained()->cascadeOnDelete();
            $table->foreignId('campus_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('program_id')->nullable()->constrained()->nullOnDelete();

            $table->unsignedSmallInteger('graduation_year');
            $table->date('graduation_date')->nullable();
            $table->string('academic_year')->nullable();

            $table->string('last_name')->nullable();
            $table->string('first_name')->nullable();
            $table->string('middle_name')->nullable();
            $table->string('gender', 1)->nullable();

            $table->string('degree_name')->nullable();
            $table->string('degree_abbreviation')->nullable();

            $table->string('programme_start_academic_year')->nullable();
            $table->string('programme_end_academic_year')->nullable();
            $table->unsignedTinyInteger('programme_duration_years')->nullable();

            $table->string('classification')->nullable();
            $table->decimal('gpa', 3, 2)->nullable();

            $table->unsignedSmallInteger('graduation_ceremony_number')->nullable();
            $table->string('certificate_barcode')->nullable();

            $table->json('raw_data')->nullable();
            $table->foreignId('imported_by')->nullable()->constrained('staff')->nullOnDelete();
            $table->unsignedBigInteger('roster_import_id')->nullable()->index();
            $table->json('import_diff')->nullable();

            $table->boolean('is_graduated')->default(true);

            $table->timestamps();

            $table->unique(['student_id', 'graduation_year', 'program_id', 'campus_id'], 'sg_student_year_program_campus_unique');
            $table->index('certificate_barcode', 'sg_cert_barcode_idx');
            $table->index(['university_id', 'graduation_year'], 'sg_uni_year_idx');
            $table->index(['university_id', 'program_id', 'graduation_year'], 'sg_uni_program_year_idx');
        });

        // Backfill: every current student row becomes one graduation row.
        // Any overwritten earlier graduations are not recoverable here -
        // they must be re-imported once the new schema is live.
        DB::table('students')
            ->where('is_graduated', true)
            ->whereNotNull('graduation_year')
            ->orderBy('id')
            ->chunk(200, function ($rows) {
                $inserts = [];

                foreach ($rows as $row) {
                    $inserts[] = [
                        'student_id' => $row->id,
                        'university_id' => $row->university_id,
                        'campus_id' => $row->campus_id,
                        'program_id' => $row->program_id,
                        'graduation_year' => $row->graduation_year,
                        'graduation_date' => $row->graduation_date,
                        'academic_year' => $row->academic_year,
                        'last_name' => $row->last_name,
                        'first_name' => $row->first_name,
                        'middle_name' => $row->middle_name,
                        'gender' => $row->gender,
                        'degree_name' => $row->degree_name,
                        'degree_abbreviation' => $row->degree_abbreviation,
                        'programme_start_academic_year' => $row->programme_start_academic_year,
                        'programme_end_academic_year' => $row->programme_end_academic_year,
                        'programme_duration_years' => $row->programme_duration_years,
                        'classification' => $row->classification,
                        'gpa' => $row->gpa,
                        'graduation_ceremony_number' => $row->graduation_ceremony_number,
                        'certificate_barcode' => $row->certificate_barcode,
                        'raw_data' => $row->raw_data,
                        'imported_by' => $row->imported_by,
                        'roster_import_id' => $row->roster_import_id,
                        'import_diff' => $row->import_diff,
                        'is_graduated' => $row->is_graduated,
                        'created_at' => $row->created_at,
                        'updated_at' => $row->updated_at,
                    ];
                }

                if (! empty($inserts)) {
                    DB::table('student_graduations')->insert($inserts);
                }
            });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_graduations');
    }
};
