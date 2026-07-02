<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('universities', function (Blueprint $table) {
            // Check if column exists before adding
            if (!Schema::hasColumn('universities', 'office_title')) {
                $table->string('office_title')->nullable()->after('name');
            }
            if (!Schema::hasColumn('universities', 'office_title_font_size')) {
                $table->string('office_title_font_size')->default('12pt')->after('office_title');
            }
            if (!Schema::hasColumn('universities', 'office_title_font_color')) {
                $table->string('office_title_font_color')->default('#000000')->after('office_title_font_size');
            }
            if (!Schema::hasColumn('universities', 'office_title_font_weight')) {
                $table->string('office_title_font_weight')->default('normal')->after('office_title_font_color');
            }
            
            if (!Schema::hasColumn('universities', 'uni_name_font_size')) {
                $table->string('uni_name_font_size')->default('18pt')->after('name');
            }
            if (!Schema::hasColumn('universities', 'uni_name_font_weight')) {
                $table->string('uni_name_font_weight')->default('bold')->after('uni_name_font_size');
            }
            
            if (!Schema::hasColumn('universities', 'primary_color')) {
                $table->string('primary_color')->default('#000000')->after('status');
            }
            if (!Schema::hasColumn('universities', 'secondary_color')) {
                $table->string('secondary_color')->default('#cccccc')->after('primary_color');
            }
            
            if (!Schema::hasColumn('universities', 'phone_secondary')) {
                $table->string('phone_secondary')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('universities', 'email_secondary')) {
                $table->string('email_secondary')->nullable()->after('email');
            }
            if (!Schema::hasColumn('universities', 'website')) {
                $table->string('website')->nullable()->after('email_secondary');
            }
            
            if (!Schema::hasColumn('universities', 'social_media_1_name')) {
                $table->string('social_media_1_name')->nullable()->after('website');
            }
            if (!Schema::hasColumn('universities', 'social_media_1_url')) {
                $table->string('social_media_1_url')->nullable()->after('social_media_1_name');
            }
            if (!Schema::hasColumn('universities', 'social_media_2_name')) {
                $table->string('social_media_2_name')->nullable()->after('social_media_1_url');
            }
            if (!Schema::hasColumn('universities', 'social_media_2_url')) {
                $table->string('social_media_2_url')->nullable()->after('social_media_2_name');
            }
            if (!Schema::hasColumn('universities', 'social_media_3_name')) {
                $table->string('social_media_3_name')->nullable()->after('social_media_2_url');
            }
            if (!Schema::hasColumn('universities', 'social_media_3_url')) {
                $table->string('social_media_3_url')->nullable()->after('social_media_3_name');
            }
            
            if (!Schema::hasColumn('universities', 'postal_box')) {
                $table->string('postal_box')->nullable()->after('address');
            }
            if (!Schema::hasColumn('universities', 'city')) {
                $table->string('city')->nullable()->after('postal_box');
            }
            if (!Schema::hasColumn('universities', 'country')) {
                $table->string('country')->default('Tanzania')->after('city');
            }
            
            if (!Schema::hasColumn('universities', 'motto')) {
                $table->string('motto')->nullable()->after('country');
            }
            if (!Schema::hasColumn('universities', 'motto_font_size')) {
                $table->string('motto_font_size')->default('9pt')->after('motto');
            }
            if (!Schema::hasColumn('universities', 'motto_font_color')) {
                $table->string('motto_font_color')->default('#666666')->after('motto_font_size');
            }
            if (!Schema::hasColumn('universities', 'motto_font_style')) {
                $table->string('motto_font_style')->default('italic')->after('motto_font_color');
            }
        });
    }

    public function down()
    {
        Schema::table('universities', function (Blueprint $table) {
            $columns = [
                'office_title', 'office_title_font_size', 'office_title_font_color', 'office_title_font_weight',
                'uni_name_font_size', 'uni_name_font_weight', 'primary_color', 'secondary_color',
                'phone_secondary', 'email_secondary', 'website',
                'social_media_1_name', 'social_media_1_url', 'social_media_2_name', 'social_media_2_url',
                'social_media_3_name', 'social_media_3_url', 'postal_box', 'city', 'country',
                'motto', 'motto_font_size', 'motto_font_color', 'motto_font_style'
            ];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('universities', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};