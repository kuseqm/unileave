<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('letters', function (Blueprint $table) {
            // HOD Approval
            $table->timestamp('hod_approved_at')->nullable();
            $table->unsignedBigInteger('hod_approved_by')->nullable();
            $table->string('hod_ip_address')->nullable();
            $table->string('hod_user_agent')->nullable();
            
            // Dean Approval
            $table->timestamp('dean_approved_at')->nullable();
            $table->unsignedBigInteger('dean_approved_by')->nullable();
            $table->string('dean_ip_address')->nullable();
            $table->string('dean_user_agent')->nullable();
            
            // DVC Approval
            $table->timestamp('dvc_approved_at')->nullable();
            $table->unsignedBigInteger('dvc_approved_by')->nullable();
            $table->string('dvc_ip_address')->nullable();
            $table->string('dvc_user_agent')->nullable();
            
            // VC Signature (Password protected)
            $table->timestamp('vc_signed_at')->nullable();
            $table->unsignedBigInteger('vc_signed_by')->nullable();
            $table->string('vc_ip_address')->nullable();
            $table->string('vc_user_agent')->nullable();
            
            // Document Security
            $table->string('document_hash')->nullable();
            $table->boolean('is_revoked')->default(false);
            $table->timestamp('revoked_at')->nullable();
            $table->string('revoked_by')->nullable();
            $table->string('revoked_reason')->nullable();
            
            // Approval Comments
            $table->json('approval_comments')->nullable();
        });
    }

    public function down()
    {
        Schema::table('letters', function (Blueprint $table) {
            $table->dropColumn([
                'hod_approved_at', 'hod_approved_by', 'hod_ip_address', 'hod_user_agent',
                'dean_approved_at', 'dean_approved_by', 'dean_ip_address', 'dean_user_agent',
                'dvc_approved_at', 'dvc_approved_by', 'dvc_ip_address', 'dvc_user_agent',
                'vc_signed_at', 'vc_signed_by', 'vc_ip_address', 'vc_user_agent',
                'document_hash', 'is_revoked', 'revoked_at', 'revoked_by', 'revoked_reason',
                'approval_comments'
            ]);
        });
    }
};