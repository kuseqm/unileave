<?php

namespace App\Http\Controllers\Registry;

use App\Http\Controllers\Controller;
use App\Models\LetterSetting;
use App\Models\University;
use Illuminate\Http\Request;

class LetterSettingController extends Controller
{
    /**
     * Display letter system settings
     */
    public function index()
    {
        $universities = University::where('status', 'active')->get();
        $settings = [];
        $selectedUniversity = old('university_id', request()->get('university_id', $universities->first()->id ?? null));

        foreach ($universities as $university) {
            $settings[$university->id] = LetterSetting::getForUniversity($university->id);
        }

        return view('registry.settings', compact('universities', 'settings', 'selectedUniversity'));
    }

    /**
     * Update letter system settings
     */
    public function update(Request $request)
    {
        $university = University::findOrFail($request->input('university_id'));
        $settings = LetterSetting::getForUniversity($university->id);

        $validated = $request->validate([
            'university_id' => 'required|exists:universities,id',
            'default_file_capacity' => 'required|integer|min:0',
            'auto_create_new_file' => 'boolean',
            'require_approval_for_new_file' => 'boolean',
            'enable_qr_code' => 'boolean',
            'enable_secure_code' => 'boolean',
            'secure_code_length' => 'required|integer|min:8|max:20',
            'secure_code_prefix' => 'nullable|string|max:20',
            'default_footer_text' => 'nullable|string|max:500',
            'default_header_bg_color' => 'nullable|string',
        ]);

        // Set boolean defaults
        $validated['auto_create_new_file'] = $request->has('auto_create_new_file');
        $validated['require_approval_for_new_file'] = $request->has('require_approval_for_new_file');
        $validated['enable_qr_code'] = $request->has('enable_qr_code');
        $validated['enable_secure_code'] = $request->has('enable_secure_code');

        unset($validated['university_id']);
        $settings->update($validated);

        // default_header_bg_color/default_footer_text stay on LetterSetting
        // only - they used to also overwrite university.header_footer_bg_color
        // (the actual letterhead branding color, owned by
        // Admin\LetterheadController's own page) even though this form
        // never exposed that field, so only reset() could ever trigger it.
        // See LetterheadController - that page is the single source of
        // truth for letterhead colors now.

        return redirect()->route('registry.settings')
            ->with('success', 'Letter system settings updated successfully!');
    }

    /**
     * Reset settings to default
     */
    public function reset(Request $request)
    {
        $university = University::findOrFail($request->input('university_id'));
        $settings = LetterSetting::where('university_id', $university->id)->first();

        if ($settings) {
            $settings->update([
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

        return redirect()->route('registry.settings')
            ->with('success', 'Letter system settings reset to defaults!');
    }
}
