<?php
// app/Models/University.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class University extends Model
{
    use HasFactory;
    
    protected $table = 'universities';
    
    protected $fillable = [
        'name', 'code', 'gps_lat', 'gps_lng', 'timezone', 
        'logo_path', 'address', 'phone', 'email', 'status',
        
        // Letterhead & Branding Fields
        'office_title',
        'office_title_font_size',
        'office_title_font_color',
        'office_title_font_weight',
        'uni_name_font_size',
        'uni_name_font_weight',
        'primary_color',
        'secondary_color',
        
        // Contact Fields
        'phone_secondary',
        'email_secondary',
        'website',
        
        // Social Media
        'social_media_1_name',
        'social_media_1_url',
        'social_media_2_name',
        'social_media_2_url',
        'social_media_3_name',
        'social_media_3_url',
        
        // Address Details
        'postal_box',
        'city',
        'country',
        
        // Motto
        'motto',
        'motto_font_size',
        'motto_font_color',
        'motto_font_style',

        // Line Colors
        'primary_line_color',
        'secondary_line_color',
        'primary_line_thickness',
        'secondary_line_thickness',
    ];
    
    protected $casts = [
        'gps_lat' => 'decimal:8',
        'gps_lng' => 'decimal:8',
        'status' => 'string',
    ];
    
    // ============================================
    // RELATIONSHIPS
    // ============================================
    
    public function campuses()
    {
        return $this->hasMany(Campus::class);
    }
    
    public function staff()
    {
        return $this->hasMany(User::class, 'university_id');
    }
    
    public function blackoutPeriods()
    {
        return $this->hasMany(BlackoutPeriod::class);
    }
    
    // ============================================
    // SCOPES
    // ============================================
    
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
    
    // ============================================
    // ACCESSORS (Existing)
    // ============================================
    
    public function getFullNameAttribute()
    {
        return $this->name . ' (' . $this->code . ')';
    }
    
    // ============================================
    // LETTERHEAD ACCESSORS (NEW)
    // ============================================
    
    /**
     * Get full address for letterhead (combines postal_box, city, country)
     */
    public function getLetterheadAddressAttribute()
    {
        $parts = [];
        if ($this->postal_box) $parts[] = $this->postal_box;
        if ($this->city) $parts[] = $this->city;
        if ($this->country) $parts[] = $this->country;
        
        return implode(', ', $parts);
    }
    
    /**
     * Get primary and secondary phone combined
     */
    public function getFormattedPhonesAttribute()
    {
        $phones = [];
        if ($this->phone) $phones[] = $this->phone;
        if ($this->phone_secondary) $phones[] = $this->phone_secondary;
        return implode(' / ', $phones);
    }
    
    /**
     * Get primary and secondary email combined
     */
    public function getFormattedEmailsAttribute()
    {
        $emails = [];
        if ($this->email) $emails[] = $this->email;
        if ($this->email_secondary) $emails[] = $this->email_secondary;
        return implode(' / ', $emails);
    }
    
    /**
     * Get social media as array for iteration
     */
    public function getSocialMediaArrayAttribute()
    {
        $social = [];
        if ($this->social_media_1_name && $this->social_media_1_url) {
            $social[] = [
                'name' => $this->social_media_1_name,
                'url' => $this->social_media_1_url,
                'icon' => $this->getSocialIcon($this->social_media_1_name)
            ];
        }
        if ($this->social_media_2_name && $this->social_media_2_url) {
            $social[] = [
                'name' => $this->social_media_2_name,
                'url' => $this->social_media_2_url,
                'icon' => $this->getSocialIcon($this->social_media_2_name)
            ];
        }
        if ($this->social_media_3_name && $this->social_media_3_url) {
            $social[] = [
                'name' => $this->social_media_3_name,
                'url' => $this->social_media_3_url,
                'icon' => $this->getSocialIcon($this->social_media_3_name)
            ];
        }
        return $social;
    }
    
    /**
     * Get social icon class based on platform name
     */
    private function getSocialIcon($name)
    {
        $name = strtolower($name);
        if (strpos($name, 'facebook') !== false) return 'ti ti-brand-facebook';
        if (strpos($name, 'twitter') !== false) return 'ti ti-brand-twitter';
        if (strpos($name, 'linkedin') !== false) return 'ti ti-brand-linkedin';
        if (strpos($name, 'instagram') !== false) return 'ti ti-brand-instagram';
        if (strpos($name, 'youtube') !== false) return 'ti ti-brand-youtube';
        if (strpos($name, 'whatsapp') !== false) return 'ti ti-brand-whatsapp';
        return 'ti ti-link';
    }
    
    /**
     * Get office title with font styling
     */
    public function getStyledOfficeTitleAttribute()
    {
        return [
            'text' => $this->office_title ?? 'Office of the Deputy Vice Chancellor Academic',
            'font_size' => $this->office_title_font_size ?? '12pt',
            'font_color' => $this->office_title_font_color ?? '#000000',
            'font_weight' => $this->office_title_font_weight ?? 'normal',
        ];
    }
    
    /**
     * Get university name with font styling
     */
    public function getStyledUniversityNameAttribute()
    {
        return [
            'text' => $this->name ?? 'ST JOHN\'S UNIVERSITY OF TANZANIA',
            'font_size' => $this->uni_name_font_size ?? '18pt',
            'font_weight' => $this->uni_name_font_weight ?? 'bold',
        ];
    }
    
    /**
     * Get motto with styling
     */
    public function getStyledMottoAttribute()
    {
        return [
            'text' => $this->motto ?? '',
            'font_size' => $this->motto_font_size ?? '9pt',
            'font_color' => $this->motto_font_color ?? '#666666',
            'font_style' => $this->motto_font_style ?? 'italic',
        ];
    }
    
    /**
     * Get primary color for lines
     */
    public function getPrimaryColorAttribute($value)
    {
        return $value ?? '#000000';
    }
    
    /**
     * Get secondary color for lines
     */
    public function getSecondaryColorAttribute($value)
    {
        return $value ?? '#cccccc';
    }
    
    /**
     * Check if university has logo
     */
    public function getHasLogoAttribute()
    {
        return !empty($this->logo_path);
    }
    
    /**
     * Get logo URL
     */
    public function getLogoUrlAttribute()
    {
        if ($this->logo_path) {
            return Storage::url($this->logo_path);
        }
        return null;
    }
}