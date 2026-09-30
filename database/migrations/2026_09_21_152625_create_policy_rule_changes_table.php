<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * A permanent record of every change to a policy rule: what it was, what
     * it became, who did it and why. Rules of a published policy can be
     * changed without re-approving the whole policy, so this is what makes
     * those changes accountable. The rule id is deliberately not a foreign
     * key - the history has to outlive a deleted rule.
     */
    public function up(): void
    {
        Schema::create('policy_rule_changes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('policy_id')->constrained('policies')->cascadeOnDelete();
            $table->unsignedBigInteger('policy_rule_id')->nullable();
            $table->string('key');
            $table->string('action', 20); // created, updated, deleted
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->text('reason')->nullable();
            $table->foreignId('changed_by')->nullable()->constrained('staff')->nullOnDelete();
            $table->timestamps();

            $table->index(['policy_id', 'created_at']);
            $table->index('policy_rule_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('policy_rule_changes');
    }
};
