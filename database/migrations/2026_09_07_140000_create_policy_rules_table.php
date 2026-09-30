<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('policy_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('policy_id')->constrained('policies')->cascadeOnDelete();
            $table->string('key');
            $table->string('label')->nullable();
            $table->text('value');
            $table->string('type')->default('string'); // string, numeric, boolean, date, json
            $table->string('operator')->nullable(); // eq, neq, gt, gte, lt, lte, in, between
            $table->string('condition_field')->nullable();
            $table->text('condition_value')->nullable();
            $table->unsignedInteger('priority')->default(0);
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('staff')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('staff')->nullOnDelete();
            $table->timestamps();

            $table->index(['policy_id', 'key']);
            $table->index(['policy_id', 'priority']);
            $table->index('is_active');
        });
    }

    public function down()
    {
        Schema::dropIfExists('policy_rules');
    }
};
