<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('import_history', function (Blueprint $table) {
            $table->foreignId('university_id')->nullable()->after('user_id')->constrained('universities')->onDelete('set null');
            $table->foreignId('campus_id')->nullable()->after('university_id')->constrained('campuses')->onDelete('set null');
            $table->foreignId('school_id')->nullable()->after('campus_id')->constrained('schools')->onDelete('set null');
            $table->foreignId('department_id')->nullable()->after('school_id')->constrained('departments')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('import_history', function (Blueprint $table) {
            $table->dropForeign(['university_id']);
            $table->dropForeign(['campus_id']);
            $table->dropForeign(['school_id']);
            $table->dropForeign(['department_id']);
            $table->dropColumn(['university_id', 'campus_id', 'school_id', 'department_id']);
        });
    }
};
