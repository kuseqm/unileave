<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('reference_formats', function (Blueprint $table) {
            $table->id();

            // Basic Info
            $table->string('name'); // Human readable name
            $table->foreignId('university_id')->constrained();

            // Entity Association (optional - can be NULL for general formats)
            $table->string('entity_type')->nullable(); // department, school, faculty, null
            $table->unsignedBigInteger('entity_id')->nullable();

            // Document Type
            $table->string('document_type'); // leave, permission, travel, contract, general
            $table->string('action_type')->nullable(); // approval, rejection, recall, offer

            // Format Pattern (Registry defines their own pattern)
            $table->string('format_pattern'); // e.g., "HR/LVE/{year}/{serial}"
            $table->string('separator')->default('/');
            $table->integer('serial_length')->default(4);
            $table->integer('serial_start')->default(1);
            $table->integer('current_serial')->default(0);

            // File Management
            $table->integer('file_capacity')->default(500); // Letters per physical file
            $table->string('file_naming_pattern')->default('{prefix}-VOL-{number:03d}');
            $table->boolean('auto_create_new_file')->default(true);
            $table->boolean('track_physical_location')->default(true);

            // Status
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index(['entity_type', 'entity_id']);
            $table->index('document_type');
        });
    }

    public function down()
    {
        Schema::dropIfExists('reference_formats');
    }
};
