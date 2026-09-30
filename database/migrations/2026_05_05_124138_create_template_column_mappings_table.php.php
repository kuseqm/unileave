<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('template_column_mappings', function (Blueprint $table) {
            $table->id();
            $table->string('excel_header', 100)->unique();
            $table->string('database_column', 100);
            $table->boolean('is_required')->default(true);
            $table->string('validation_rule', 255)->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Seed default mappings
        DB::table('template_column_mappings')->insert([
            ['excel_header' => 'Staff ID', 'database_column' => 'staff_id', 'is_required' => true, 'sort_order' => 1],
            ['excel_header' => 'First Name', 'database_column' => 'first_name', 'is_required' => true, 'sort_order' => 2],
            ['excel_header' => 'Last Name', 'database_column' => 'last_name', 'is_required' => true, 'sort_order' => 3],
            ['excel_header' => 'Email Address', 'database_column' => 'email', 'is_required' => true, 'sort_order' => 4],
            ['excel_header' => 'Department', 'database_column' => 'department_name', 'is_required' => true, 'sort_order' => 5],
            ['excel_header' => 'Campus', 'database_column' => 'campus_name', 'is_required' => true, 'sort_order' => 6],
            ['excel_header' => 'Staff Category', 'database_column' => 'staff_category', 'is_required' => true, 'sort_order' => 7],
            ['excel_header' => 'Joining Date', 'database_column' => 'joining_date', 'is_required' => true, 'sort_order' => 8],
            ['excel_header' => 'Phone Number', 'database_column' => 'phone', 'is_required' => false, 'sort_order' => 9],
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('template_column_mappings');
    }
};
