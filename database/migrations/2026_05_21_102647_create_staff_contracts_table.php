<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('staff_contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained('staff')->onDelete('cascade');
            $table->string('contract_number', 100)->nullable();
            $table->enum('contract_type', ['permanent', 'fixed_term', 'temporary', 'probation', 'consultant'])->nullable();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->decimal('salary', 15, 2)->nullable();
            $table->string('salary_currency', 3)->default('TZS');
            $table->string('file_path', 500)->nullable();
            $table->foreignId('signed_by')->nullable()->constrained('staff')->onDelete('set null');
            $table->boolean('is_current')->default(false);
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->index('staff_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('staff_contracts');
    }
};
