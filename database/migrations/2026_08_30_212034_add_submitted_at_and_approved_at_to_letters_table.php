<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('letters', function (Blueprint $table) {
            // Declared in App\Models\Letter's $fillable/$casts (and referenced by
            // LetterController::submit()) but never actually created by a migration —
            // every letter submission was throwing "Unknown column 'submitted_at'".
            if (! Schema::hasColumn('letters', 'submitted_at')) {
                $table->timestamp('submitted_at')->nullable()->after('stage');
            }
            if (! Schema::hasColumn('letters', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('submitted_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('letters', function (Blueprint $table) {
            $table->dropColumn(['submitted_at', 'approved_at']);
        });
    }
};
