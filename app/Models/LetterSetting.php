<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LetterSetting extends Model
{
    use HasFactory;

    protected $table = 'letter_settings';

    protected $fillable = [
        'university_id',
        'default_file_capacity',
        'auto_create_new_file',
        'require_approval_for_new_file',
        'enable_qr_code',
        'enable_secure_code',
        'secure_code_length',
        'secure_code_prefix',
        'default_footer_text',
        'default_header_bg_color',
    ];

    protected $casts = [
        'auto_create_new_file' => 'boolean',
        'require_approval_for_new_file' => 'boolean',
        'enable_qr_code' => 'boolean',
        'enable_secure_code' => 'boolean',
        'secure_code_length' => 'integer',
        'default_file_capacity' => 'integer',
    ];

    // ============================================
    // RELATIONSHIPS
    // ============================================

    /**
     * University that owns these settings
     */
    public function university()
    {
        return $this->belongsTo(University::class);
    }

    // ============================================
    // METHODS
    // ============================================

    /**
     * Get settings for a university (create default if not exist)
     */
    public static function getForUniversity($universityId)
    {
        $settings = self::where('university_id', $universityId)->first();

        if (! $settings) {
            $settings = self::create([
                'university_id' => $universityId,
                'default_file_capacity' => 500,
                'auto_create_new_file' => true,
                'require_approval_for_new_file' => false,
                'enable_qr_code' => true,
                'enable_secure_code' => true,
                'secure_code_length' => 12,
                'secure_code_prefix' => null,
                'default_footer_text' => 'This is a computer-generated letter. No signature is required.',
                'default_header_bg_color' => '#f8f9fa',
            ]);
        }

        return $settings;
    }
}
