<?php

// database/migrations/2026_05_05_000001_create_schools_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('schools', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campus_id')->constrained()->onDelete('cascade');
            $table->string('name', 255);
            $table->string('code', 50);
            $table->foreignId('dean_staff_id')->nullable()->constrained('staff')->onDelete('set null');
            $table->text('description')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();

            $table->unique(['campus_id', 'code']);
            $table->index('dean_staff_id');
            $table->index('status');
        });
    }

    public function down()
    {
        Schema::dropIfExists('schools');
    }
};
