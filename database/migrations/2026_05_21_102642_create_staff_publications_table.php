<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('staff_publications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained('staff')->onDelete('cascade');
            $table->string('publication_title', 500);
            $table->enum('publication_type', ['journal', 'conference', 'book', 'book_chapter', 'technical_report', 'thesis']);
            $table->string('journal_name', 255)->nullable();
            $table->string('publisher', 255)->nullable();
            $table->year('publication_year')->nullable();
            $table->string('doi', 100)->nullable();
            $table->string('url', 500)->nullable();
            $table->boolean('is_peer_reviewed')->default(true);
            $table->json('authors')->nullable();
            $table->integer('citations_count')->default(0);
            $table->string('file_path', 500)->nullable();
            $table->timestamps();
            
            $table->index('staff_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('staff_publications');
    }
};