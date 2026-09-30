<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('report_definitions', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., leave_summary, staff_directory
            $table->string('display_name'); // Human-readable title
            $table->text('description')->nullable();
            $table->json('filters')->nullable(); // e.g., {date_range: true, department: true}
            $table->json('columns')->nullable(); // which columns to display
            $table->json('chart_config')->nullable(); // for charts (type, labels, datasets)
            $table->string('permission')->default('view_reports'); // minimum permission to access
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('report_definitions');
    }
};
