<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('letter_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('university_id')->constrained();

            // Global Settings
            $table->integer('default_file_capacity')->default(500);
            $table->boolean('auto_create_new_file')->default(true);
            $table->boolean('require_approval_for_new_file')->default(false);
            $table->boolean('enable_qr_code')->default(true);
            $table->boolean('enable_secure_code')->default(true);

            // Verification Settings
            $table->integer('secure_code_length')->default(12);
            $table->string('secure_code_prefix')->nullable();

            // Default Values
            $table->string('default_footer_text')->default('This is a computer-generated letter. No signature is required.');
            $table->string('default_header_bg_color')->default('#f8f9fa');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('letter_settings');
    }
};
