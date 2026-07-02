<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('letter_files', function (Blueprint $table) {
            $table->id();
            $table->string('file_number')->unique();
            $table->string('file_name');
            $table->foreignId('reference_format_id')->constrained('reference_formats')->onDelete('cascade');
            $table->integer('start_serial');
            $table->integer('end_serial')->nullable();
            $table->integer('current_serial')->default(0);
            $table->boolean('is_active')->default(true);
            $table->string('physical_location')->nullable();
            $table->string('cabinet')->nullable();
            $table->string('shelf')->nullable();
            $table->string('box_number')->nullable();
            $table->timestamp('opened_at')->useCurrent();
            $table->timestamp('closed_at')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index('is_active');
            $table->index('file_number');
        });
    }

    public function down()
    {
        Schema::dropIfExists('letter_files');
    }
};