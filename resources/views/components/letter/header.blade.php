@props(['university' => null])

@php
    use Illuminate\Support\Facades\Storage;
    
    $uniName = $university->name ?? 'ST JOHN\'S UNIVERSITY OF TANZANIA';
    $uniNameFontSize = $university->uni_name_font_size ?? '16pt';
    $uniNameFontWeight = $university->uni_name_font_weight ?? 'bold';
    
    $officeTitle = $university->office_title ?? 'Office of the Deputy Vice Chancellor Academic';
    $officeTitleFontSize = $university->office_title_font_size ?? '14pt';  // Increased from 12pt to 14pt
    $officeTitleFontColor = $university->office_title_font_color ?? '#000000';
    $officeTitleFontWeight = $university->office_title_font_weight ?? 'normal';
    
    // Line colors and thickness
    $primaryLineColor = $university->primary_line_color ?? '#000000';
    $secondaryLineColor = $university->secondary_line_color ?? '#cccccc';
    $primaryLineThickness = $university->primary_line_thickness ?? '2px';
    $secondaryLineThickness = $university->secondary_line_thickness ?? '1px';
    
    // Contact Information
    $phonePrimary = $university->phone ?? '+255 67 70 86093';
    $phoneSecondary = $university->phone_secondary ?? null;
    $emailPrimary = $university->email ?? 'dvca@sjut.ac.tz';
    $website = $university->website ?? 'www.sjut.ac.tz';
    $address = $university->address ?? 'P. O. Box 47, Dodoma';
    
    // Social Media - Names only
    $socialLinks = [];
    
    if ($university->social_media_1_name) {
        $socialLinks[] = $university->social_media_1_name;
    }
    if ($university->social_media_2_name) {
        $socialLinks[] = $university->social_media_2_name;
    }
    if ($university->social_media_3_name) {
        $socialLinks[] = $university->social_media_3_name;
    }
    
    $logoPath = $university->logo_path ?? null;
    $hasLogo = $logoPath && Storage::disk('public')->exists($logoPath);
@endphp

<div class="letter-header" style="font-family: 'Times New Roman', Times, serif; margin: 0; padding: 0;">
    
    <!-- MAIN CONTAINER -->
    <div style="display: flex; gap: 2px; align-items: flex-start;">
        
        <!-- LOGO -->
        @if($hasLogo)
        <div style="flex-shrink: 0; line-height: 0;">
            <img src="{{ Storage::url($logoPath) }}" alt="{{ $uniName }}" style="max-height: 160px; width: auto; max-width: 150px; display: block;">
        </div>
        @endif
        
        <!-- RIGHT CONTENT -->
        <div style="flex-grow: 1;">
            
            <!-- Title Section -->
            <div style="text-align: center; margin: 0;">
                <!-- University Name -->
                <div style="font-family: 'Playfair Display', serif; font-size: {{ $uniNameFontSize }}; font-weight: 600; letter-spacing: 1.2px; text-transform: uppercase; line-height: 1.1; margin-top: 30px;">
                    {{ $uniName }}
                </div>
                <!-- Office Title - Increased font size, kept margin small -->
                <div style="font-size: {{ $officeTitleFontSize }}; font-weight: {{ $officeTitleFontWeight }}; color: {{ $officeTitleFontColor }}; line-height: 1.3; margin-top: 5px;">
                    {{ $officeTitle }}
                </div>
            </div>
            
            <!-- Contact Grid -->
            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 6px; font-size: 10pt; margin-top: 10px; padding: 0; text-align: center;">
                
                <!-- Phones -->
                <div>
                    @if($phonePrimary)
                    <div><i class="ti ti-phone" style="font-size: 9pt;"></i> {{ $phonePrimary }}</div>
                    @endif
                    @if($phoneSecondary)
                    <div style="margin-top: 2px;"><i class="ti ti-phone" style="font-size: 9pt;"></i> {{ $phoneSecondary }}</div>
                    @endif
                </div>
                
                <!-- Email & Website -->
                <div>
                    @if($emailPrimary)
                    <div><i class="ti ti-mail" style="font-size: 9pt;"></i> {{ $emailPrimary }}</div>
                    @endif
                    @if($website)
                    <div style="margin-top: 2px;"><i class="ti ti-world" style="font-size: 9pt;"></i> {{ $website }}</div>
                    @endif
                </div>
                
                <!-- Social Media -->
                <div>
                    @foreach($socialLinks as $socialName)
                    @php
                        $icon = 'ti ti-link';
                        if (str_contains(strtolower($socialName), 'facebook')) $icon = 'ti ti-brand-facebook';
                        elseif (str_contains(strtolower($socialName), 'twitter')) $icon = 'ti ti-brand-twitter';
                        elseif (str_contains(strtolower($socialName), 'instagram')) $icon = 'ti ti-brand-instagram';
                        elseif (str_contains(strtolower($socialName), 'linkedin')) $icon = 'ti ti-brand-linkedin';
                        elseif (str_contains(strtolower($socialName), 'youtube')) $icon = 'ti ti-brand-youtube';
                    @endphp
                    <div style="margin-top: {{ $loop->first ? 0 : 2 }}px;"><i class="{{ $icon }}" style="font-size: 9pt;"></i> {{ $socialName }}</div>
                    @endforeach
                </div>
                
                <!-- Address -->
                <div>
                    <div><i class="ti ti-map-pin" style="font-size: 9pt;"></i> {{ $address }}</div>
                </div>
                
            </div>
            
        </div>
    </div>
    
    <!-- DECORATIVE LINES -->
    <div style="margin-top: 8px;">
        <hr style="border: none; border-top: {{ $primaryLineThickness }} solid {{ $primaryLineColor }}; margin: 0;">
    </div>
    <div style="margin-top: 1px;">
        <hr style="border: none; border-top: {{ $secondaryLineThickness }} solid {{ $secondaryLineColor }}; margin: 0;">
    </div>
    
</div>