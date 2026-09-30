<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The `settings` table exists on the running demo database but no
     * migration ever created it, so a brand-new database (a new server, a
     * fresh tenant install, the test database) had no such table and almost
     * every page failed with "Base table or view not found". This creates it
     * with the exact shape the existing table already has, and does nothing
     * where it's already there.
     */
    public function up(): void
    {
        if (Schema::hasTable('settings')) {
            return;
        }

        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key');
            $table->text('value')->nullable();
            $table->string('type')->default('text');
            $table->string('group')->default('general');
            $table->string('label')->nullable();
            $table->text('description')->nullable();
            $table->json('options')->nullable();
            $table->string('role')->nullable();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_encrypted')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Deliberately left empty: this table predates the migration and holds
     * live settings, so rolling this migration back must never drop it.
     */
    public function down(): void {}
};
