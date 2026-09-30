<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('policy_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('policy_id')->constrained('policies')->cascadeOnDelete();
            $table->integer('version');
            $table->string('title');
            $table->string('category');
            $table->string('scope')->nullable();
            $table->unsignedBigInteger('scope_id')->nullable();
            $table->text('description')->nullable();
            $table->longText('content')->nullable();
            $table->string('document_path')->nullable();
            $table->string('status');
            $table->date('effective_date')->nullable();
            $table->date('review_due_date')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->string('meeting_reference')->nullable();
            $table->string('chairperson_name')->nullable();
            $table->string('chairperson_signature_path')->nullable();
            $table->string('secretary_name')->nullable();
            $table->string('secretary_signature_path')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('staff')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('staff')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('staff')->nullOnDelete();
            $table->timestamps();

            $table->unique(['policy_id', 'version']);
            $table->index('policy_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('policy_versions');
    }
};
