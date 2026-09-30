<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\University;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LetterheadController extends Controller
{
    public function index()
    {
        $university = auth()->user()->university ?? University::first();

        return view('admin.settings.letterhead', compact('university'));
    }

    public function update(Request $request)
    {
        $university = auth()->user()->university ?? University::first();

        $validated = $request->validate([
            'name' => 'required|string|max:191',
            'code' => 'required|string|max:50',
            'status' => 'required|in:active,inactive',
            'office_title' => 'nullable|string|max:255',
            'office_title_font_size' => 'nullable|string',
            'office_title_font_color' => 'nullable|string',
            'office_title_font_weight' => 'nullable|in:normal,bold',
            'uni_name_font_size' => 'nullable|string',
            'uni_name_font_weight' => 'nullable|in:normal,bold',
            'primary_color' => 'nullable|string',
            'secondary_color' => 'nullable|string',
            'header_footer_bg_color' => 'nullable|string',
            'primary_line_color' => 'nullable|string',
            'secondary_line_color' => 'nullable|string',
            'primary_line_thickness' => 'nullable|string',
            'secondary_line_thickness' => 'nullable|string',
            'phone' => 'nullable|string|max:50',
            'phone_secondary' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'email_secondary' => 'nullable|email|max:255',
            // ✅ CHANGED: website from 'url' to 'string' (plain text, not clickable)
            'website' => 'nullable|string|max:255',
            // ✅ CHANGED: social media - only names, no URLs needed
            'social_media_1_name' => 'nullable|string|max:50',
            'social_media_2_name' => 'nullable|string|max:50',
            'social_media_3_name' => 'nullable|string|max:50',
            // ❌ REMOVED: social_media_1_url, social_media_2_url, social_media_3_url
            'address' => 'nullable|string',
            'postal_box' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'motto' => 'nullable|string|max:255',
            'motto_font_size' => 'nullable|string',
            'motto_font_color' => 'nullable|string',
            'motto_font_style' => 'nullable|in:normal,italic',
        ]);

        if ($request->hasFile('logo')) {
            if ($university->logo_path && Storage::disk('public')->exists($university->logo_path)) {
                Storage::disk('public')->delete($university->logo_path);
            }

            $filename = time().'_'.preg_replace('/[^a-zA-Z0-9._-]/', '', $request->file('logo')->getClientOriginalName());
            $path = $request->file('logo')->storeAs('university-logos', $filename, 'public');
            $validated['logo_path'] = $path;
        }

        $university->update($validated);

        return redirect()->route('admin.settings.letterhead')
            ->with('success', 'Letterhead settings updated successfully!');
    }

    public function preview(Request $request)
    {
        $university = auth()->user()->university ?? University::first();
        $previewUniversity = clone $university;

        // ✅ UPDATED field mappings (removed social media URL fields)
        $fieldMappings = [
            'name', 'office_title', 'office_title_font_size', 'office_title_font_color', 'office_title_font_weight',
            'uni_name_font_size', 'uni_name_font_weight', 'primary_color', 'secondary_color',
            'header_footer_bg_color',
            'primary_line_color', 'secondary_line_color', 'primary_line_thickness', 'secondary_line_thickness',
            'phone', 'phone_secondary', 'email', 'email_secondary', 'website',
            // ✅ Only social media names (no URLs)
            'social_media_1_name', 'social_media_2_name', 'social_media_3_name',
            'address', 'postal_box', 'city', 'country',
            'motto', 'motto_font_size', 'motto_font_color', 'motto_font_style',
        ];

        foreach ($fieldMappings as $field) {
            if ($request->has($field)) {
                $previewUniversity->$field = $request->$field;
            }
        }

        return view('components.letter.header', ['university' => $previewUniversity])->render();
    }
}
