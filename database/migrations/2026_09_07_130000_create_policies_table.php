<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('policies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('university_id')->nullable()->constrained('universities')->nullOnDelete();
            $table->string('code')->unique();
            $table->string('title');
            $table->string('category')->default('general'); // handbook, examination_regulation, hr_policy, academic_policy, general
            $table->string('scope')->default('university-wide'); // university-wide, school, department, role
            $table->unsignedBigInteger('scope_id')->nullable();
            $table->text('description')->nullable();
            $table->longText('content')->nullable();
            $table->string('document_path')->nullable();
            $table->integer('version')->default(1);
            $table->string('status')->default('draft'); // draft, pending, approved, active, archived, revoked
            $table->date('effective_date')->nullable();
            $table->date('review_due_date')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->string('meeting_reference')->nullable();
            $table->string('chairperson_name')->nullable();
            $table->string('chairperson_signature_path')->nullable();
            $table->string('secretary_name')->nullable();
            $table->string('secretary_signature_path')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('staff')->nullOnDelete();
            $table->foreignId('created_by')->constrained('staff');
            $table->foreignId('updated_by')->nullable()->constrained('staff')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'category']);
            $table->index(['scope', 'scope_id']);
            $table->index('effective_date');
        });
    }

    public function down()
    {
        Schema::dropIfExists('policies');
    }
};
