@props(['university' => null, 'forPdf' => false, 'officeTitle' => null])

@php
    use Illuminate\Support\Facades\Storage;

    $uniName = $university->name ?? 'ST JOHN\'S UNIVERSITY OF TANZANIA';
    $uniNameFontSize = $university->uni_name_font_size ?? '17pt';
    $uniNameFontWeight = $university->uni_name_font_weight ?? 'bold';

    // The office issuing the letter changes per letter type (e.g. "Human
    // Resources Department" for a Leave Approval Letter vs "Deputy Vice
    // Chancellor Academics" for a DUS one) - callers override via the
    // officeTitle prop. It can be a single line or several (an array, or a
    // "|"-separated string, since the university's own stored default is a
    // single DB column).
    $officeTitleSource = $officeTitle ?? $university->office_title ?? 'Deputy Vice Chancellor Academics';
    $officeTitleLines = is_array($officeTitleSource) ? $officeTitleSource : explode('|', $officeTitleSource);
    $officeTitleFontSize = $university->office_title_font_size ?? '16pt';
    $officeTitleFontColor = $university->office_title_font_color ?? '#000000';
    $officeTitleFontWeight = $university->office_title_font_weight ?? 'normal';
    
    // Line colors and thickness
    $primaryLineColor = $university->primary_line_color ?? '#000000';
    $secondaryLineColor = $university->secondary_line_color ?? '#cccccc';
    $primaryLineThickness = $university->primary_line_thickness ?? '2px';
    $secondaryLineThickness = $university->secondary_line_thickness ?? '1px';

    // Thin white divider between the two crest lines, requested by the
    // user as a subtle separator rather than the two colors touching
    // directly - a third of the primary (blue) line's own thickness, with
    // a 1px floor so it never fully disappears even if primaryLineThickness
    // is stored unusually thin. $primaryLineThickness is a free-text field
    // (admin-configurable, "2px" by default) - only the leading number is
    // used, defaulting to 2 if it can't be parsed.
    $dividerThickness = max(1, (int) round(((float) $primaryLineThickness) / 3)).'px';
    
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

    // No separate motto rendering here - the uploaded logo image itself
    // already carries the university's motto baked into the graphic
    // (confirmed by the user against the real SJUT letterhead), so a
    // second rendered copy would duplicate it. The motto_font_* fields on
    // University still exist for the Letterhead Settings form/preview, but
    // this component deliberately doesn't read them.
@endphp

<div class="letter-header" style="font-family: Arial, Helvetica, sans-serif; margin: 0; padding: 0;">

    {{-- DomPDF is a CSS 2.1 engine - flexbox/grid don't lay out reliably and
         can silently stack columns vertically instead of side-by-side,
         which is what was pushing this letter onto a second page. Tables
         are the layout DomPDF actually supports for multi-column rows. --}}

    <!-- MAIN CONTAINER: logo | title block -->
    <table style="width: 100%; border-collapse: collapse;">
        <tr>
            @if($hasLogo)
            <td style="width: 115px; vertical-align: top; padding: 0 6px 0 0;">
                {{-- DomPDF has remote image fetching disabled by default, so a PDF
                     render needs a real filesystem path instead of the /storage/...
                     URL a browser can fetch fine. The logo file's own aspect ratio
                     (taller than wide) is preserved by constraining only
                     max-height - width is never set directly, so it can never be
                     stretched wider than it is tall or vice versa. --}}
                <img src="{{ $forPdf ? public_path('storage/'.$logoPath) : Storage::url($logoPath) }}" alt="{{ $uniName }}" style="max-height: 130px; width: auto; max-width: 115px; display: block;">
            </td>
            @endif
            <td style="vertical-align: top;">

                <!-- Title Section -->
                <div style="text-align: center; margin: 0;">
                    <!-- University Name -->
                    <div style="font-size: {{ $uniNameFontSize }}; font-weight: 700; letter-spacing: 2px; text-transform: uppercase; line-height: 1.15; margin-top: 4px;">
                        {{ $uniName }}
                    </div>
                    <!-- Office Title -->
                    <div style="font-size: {{ $officeTitleFontSize }}; font-weight: {{ $officeTitleFontWeight }}; color: {{ $officeTitleFontColor }}; line-height: 1.25; margin-top: 9px;">
                        @foreach($officeTitleLines as $line)
                            {{ trim($line) }}@if(!$loop->last)<br>@endif
                        @endforeach
                    </div>
                </div>

                <!-- Contact Row -->
                <table style="width: 100%; border-collapse: collapse; font-size: 8.5pt; margin-top: 12px;">
                    <tr>
                        <!-- Phones -->
                        <td style="width: 25%; text-align: center; vertical-align: top;">
                            @if($phonePrimary)
                            <div>Tel: {{ $phonePrimary }}</div>
                            @endif
                            @if($phoneSecondary)
                            <div>{{ $phoneSecondary }}</div>
                            @endif
                        </td>

                        <!-- Email & Website -->
                        <td style="width: 25%; text-align: center; vertical-align: top;">
                            @if($emailPrimary)
                            <div>{{ $emailPrimary }}</div>
                            @endif
                            @if($website)
                            <div>{{ $website }}</div>
                            @endif
                        </td>

                        <!-- Social Media -->
                        <td style="width: 25%; text-align: center; vertical-align: top;">
                            @foreach($socialLinks as $socialName)
                                <div>{{ $socialName }}</div>
                            @endforeach
                        </td>

                        <!-- Address -->
                        <td style="width: 25%; text-align: right; vertical-align: top;">
                            <div>{{ $address }}</div>
                        </td>
                    </tr>
                </table>

            </td>
        </tr>
    </table>

    <!-- DECORATIVE LINES - a thin white divider separates the two crest
         colors (blue over pink) instead of them touching directly. -->
    <div style="margin-top: 6px;">
        <hr style="border: none; border-top: {{ $primaryLineThickness }} solid {{ $primaryLineColor }}; margin: 0;">
    </div>
    <div style="margin-top: 0;">
        <hr style="border: none; border-top: {{ $dividerThickness }} solid #ffffff; margin: 0;">
    </div>
    <div style="margin-top: 0;">
        <hr style="border: none; border-top: {{ $secondaryLineThickness }} solid {{ $secondaryLineColor }}; margin: 0;">
    </div>

</div>