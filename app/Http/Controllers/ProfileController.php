<?php

namespace App\Http\Controllers;

use App\Helpers\AuditLogHelper;
use App\Models\Dependent;
use App\Models\LeaveRequest;
use App\Models\Setting;
use App\Models\StaffAddress;
use App\Models\StaffAward;
use App\Models\StaffCertification;
use App\Models\StaffEmail;
use App\Models\StaffEmergencyContact;
use App\Models\StaffLanguage;
use App\Models\StaffPhone;
use App\Models\StaffQualification;
use App\Models\StaffSkill;
use App\Models\StaffWorkExperience;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Display user profile
     */
    public function show()
    {
        $user = auth()->user()->load([
            'phones', 'emails', 'qualifications', 'skills', 'languages',
            'certifications', 'awards', 'workExperience',
        ]);

        // Leave statistics
        $totalLeaveRequests = LeaveRequest::where('staff_id', $user->id)->count();
        $pendingLeaveRequests = LeaveRequest::where('staff_id', $user->id)->where('status', 'pending')->count();
        $approvedLeaveRequests = LeaveRequest::where('staff_id', $user->id)->where('status', 'approved')->count();
        $rejectedLeaveRequests = LeaveRequest::where('staff_id', $user->id)->where('status', 'rejected')->count();

        return view('profile.show', compact('user', 'totalLeaveRequests', 'pendingLeaveRequests', 'approvedLeaveRequests', 'rejectedLeaveRequests'));

    }

    /**
     * Update profile information
     */
    public function update(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'title' => 'nullable|string|max:10',
            'gender' => 'nullable|in:male,female,other',
            'date_of_birth' => 'nullable|date',
            'national_id' => 'nullable|string|max:50',
            'marital_status' => 'nullable|string|max:20',
            'spouse_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            // Add any other fields you want to update
        ]);

        $user->update([
            'title' => $request->title,
            'gender' => $request->gender,
            'date_of_birth' => $request->date_of_birth,
            'national_id' => $request->national_id,
            'marital_status' => $request->marital_status,
            'spouse_name' => $request->spouse_name,
            'phone' => $request->phone,
        ]);

        return redirect()->back()->with('success', 'Profile updated successfully.');
    }

    /**
     * Update password
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'password' => 'required|string|min:8|confirmed',
        ]);

        auth()->user()->update([
            'password' => Hash::make($request->password),
            'password_change_required' => false,
            'password_changed_at' => now(),
        ]);

        AuditLogHelper::log('password_change', 'auth', auth()->id());

        return redirect()->route('dashboard')->with('success', 'Password changed successfully.');
    }

    /**
     * Update avatar
     */
    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = auth()->user();

        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        $path = $request->file('avatar')->store('avatars', 'public');
        $user->update(['avatar' => $path]);

        return redirect()->back()->with('success', 'Avatar updated successfully.');
    }

    // ============================================
    // PHONE METHODS
    // ============================================

    public function storePhone(Request $request)
    {
        $request->validate([
            'phone_number' => 'required|string|max:20',
            'type' => 'nullable|string|max:50',
            'is_primary' => 'nullable|boolean',
        ]);

        if ($request->is_primary) {
            auth()->user()->phones()->update(['is_primary' => false]);
        }

        auth()->user()->phones()->create($request->all());

        return redirect()->back()->with('success', 'Phone added successfully.');
    }

    public function destroyPhone($id)
    {
        $phone = StaffPhone::where('staff_id', auth()->id())->findOrFail($id);
        $phone->delete();

        return redirect()->back()->with('success', 'Phone deleted successfully.');
    }

    // ============================================
    // EMAIL METHODS
    // ============================================

    public function storeEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|max:255|unique:staff_emails,email',
            'type' => 'nullable|string|max:50',
            'is_primary' => 'nullable|boolean',
        ]);

        if ($request->is_primary) {
            auth()->user()->emails()->update(['is_primary' => false]);
        }

        auth()->user()->emails()->create($request->all());

        return redirect()->back()->with('success', 'Email added successfully.');
    }

    public function destroyEmail($id)
    {
        $email = StaffEmail::where('staff_id', auth()->id())->findOrFail($id);
        $email->delete();

        return redirect()->back()->with('success', 'Email deleted successfully.');
    }

    // ============================================
    // DEPENDENT METHODS
    // ============================================

    public function storeDependent(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'relationship' => 'required|string|max:100',
            'date_of_birth' => 'nullable|date',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'is_beneficiary' => 'nullable|boolean',
        ]);

        auth()->user()->dependents()->create($request->all());

        return redirect()->back()->with('success', 'Dependent added successfully.');
    }

    public function destroyDependent($id)
    {
        $dependent = Dependent::where('staff_id', auth()->id())->findOrFail($id);
        $dependent->delete();

        return redirect()->back()->with('success', 'Dependent deleted successfully.');
    }

    // ============================================
    // ADDRESS METHODS
    // ============================================

    public function storeAddress(Request $request)
    {
        $request->validate([
            'address' => 'required|string|max:500',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'address_type' => 'nullable|in:current,permanent,other',
            'is_primary' => 'nullable|boolean',
        ]);

        if ($request->is_primary) {
            auth()->user()->addresses()->update(['is_primary' => false]);
        }

        auth()->user()->addresses()->create($request->all());

        return redirect()->back()->with('success', 'Address added successfully.');
    }

    public function destroyAddress($id)
    {
        $address = StaffAddress::where('staff_id', auth()->id())->findOrFail($id);
        $address->delete();

        return redirect()->back()->with('success', 'Address deleted successfully.');
    }

    // ============================================
    // BANK DETAILS METHODS
    // ============================================

    public function updateBankDetails(Request $request)
    {
        $request->validate([
            'bank_name' => 'nullable|string|max:255',
            'account_name' => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:50',
            'branch' => 'nullable|string|max:255',
        ]);

        auth()->user()->bankDetails()->updateOrCreate(
            ['staff_id' => auth()->id()],
            $request->all()
        );

        return redirect()->back()->with('success', 'Bank details updated successfully.');
    }

    // ============================================
    // EMERGENCY CONTACT METHODS
    // ============================================

    public function storeEmergencyContact(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'relationship' => 'nullable|string|max:100',
            'phone_primary' => 'required|string|max:20',
            'phone_secondary' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
            'priority' => 'nullable|integer|min:1|max:10',
        ]);

        auth()->user()->emergencyContacts()->create([
            'full_name' => $request->full_name,
            'relationship' => $request->relationship,
            'phone_primary' => $request->phone_primary,
            'phone_secondary' => $request->phone_secondary,
            'email' => $request->email,
            'address' => $request->address,
            'priority' => $request->priority ?? 5,
        ]);

        return redirect()->back()->with('success', 'Emergency contact added successfully.');
    }

    public function destroyEmergencyContact($id)
    {
        $contact = StaffEmergencyContact::where('staff_id', auth()->id())->findOrFail($id);
        $contact->delete();

        if (request()->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Emergency contact deleted successfully.');
    }

    // ============================================
    // QUALIFICATION METHODS
    // ============================================

    public function storeQualification(Request $request)
    {
        $request->validate([
            'qualification_name' => 'required|string|max:255',
            'institution' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:100',
            'year_obtained' => 'nullable|integer|min:1900|max:'.date('Y'),
            'grade' => 'nullable|string|max:50',
            'qualification_type' => 'nullable|string|max:50',
            'is_highest' => 'nullable|boolean',
        ]);

        if ($request->is_highest) {
            auth()->user()->qualifications()->update(['is_highest' => false]);
        }

        auth()->user()->qualifications()->create([
            'qualification_name' => $request->qualification_name,
            'institution' => $request->institution,
            'country' => $request->country,
            'year_obtained' => $request->year_obtained,
            'grade' => $request->grade,
            'qualification_type' => $request->qualification_type,
            'is_highest' => $request->has('is_highest'),
        ]);

        return redirect()->back()->with('success', 'Qualification added successfully.');
    }

    public function destroyQualification($id)
    {
        $qualification = StaffQualification::where('staff_id', auth()->id())->findOrFail($id);
        $qualification->delete();

        if (request()->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Qualification deleted successfully.');
    }

    // ============================================
    // SKILL METHODS
    // ============================================

    public function storeSkill(Request $request)
    {
        $request->validate([
            'skill_name' => 'required|string|max:255',
            'skill_category' => 'nullable|string|max:100',
            'proficiency_level' => 'nullable|string|in:beginner,intermediate,advanced,expert',
            'years_of_experience' => 'nullable|integer|min:0|max:50',
            'description' => 'nullable|string|max:500',
        ]);

        auth()->user()->skills()->create([
            'skill_name' => $request->skill_name,
            'skill_category' => $request->skill_category,
            'proficiency_level' => $request->proficiency_level,
            'years_of_experience' => $request->years_of_experience,
            'description' => $request->description,
        ]);

        return redirect()->back()->with('success', 'Skill added successfully.');
    }

    public function destroySkill($id)
    {
        $skill = StaffSkill::where('staff_id', auth()->id())->findOrFail($id);
        $skill->delete();

        if (request()->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Skill deleted successfully.');
    }

    // ============================================
    // LANGUAGE METHODS
    // ============================================

    public function storeLanguage(Request $request)
    {
        $request->validate([
            'language_name' => 'required|string|max:100',
            'proficiency_read' => 'nullable|string|in:none,basic,intermediate,fluent,native',
            'proficiency_write' => 'nullable|string|in:none,basic,intermediate,fluent,native',
            'proficiency_speak' => 'nullable|string|in:none,basic,intermediate,fluent,native',
            'is_working_language' => 'nullable|boolean',
        ]);

        auth()->user()->languages()->create([
            'language_name' => $request->language_name,
            'proficiency_read' => $request->proficiency_read,
            'proficiency_write' => $request->proficiency_write,
            'proficiency_speak' => $request->proficiency_speak,
            'is_working_language' => $request->has('is_working_language'),
        ]);

        return redirect()->back()->with('success', 'Language added successfully.');
    }

    public function destroyLanguage($id)
    {
        $language = StaffLanguage::where('staff_id', auth()->id())->findOrFail($id);
        $language->delete();

        if (request()->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Language deleted successfully.');
    }

    // ============================================
    // CERTIFICATION METHODS
    // ============================================

    public function storeCertification(Request $request)
    {
        $request->validate([
            'certification' => 'required|string|max:255',
            'issuing_body' => 'nullable|string|max:255',
            'issue_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after:issue_date',
        ]);

        auth()->user()->certifications()->create($request->all());

        return redirect()->back()->with('success', 'Certification added successfully.');
    }

    public function destroyCertification($id)
    {
        $certification = StaffCertification::where('staff_id', auth()->id())->findOrFail($id);
        $certification->delete();

        return redirect()->back()->with('success', 'Certification deleted successfully.');
    }

    // ============================================
    // AWARD METHODS
    // ============================================

    public function storeAward(Request $request)
    {
        $request->validate([
            'award' => 'required|string|max:255',
            'issuing_body' => 'nullable|string|max:255',
            'date_received' => 'nullable|date',
        ]);

        auth()->user()->awards()->create($request->all());

        return redirect()->back()->with('success', 'Award added successfully.');
    }

    public function destroyAward($id)
    {
        $award = StaffAward::where('staff_id', auth()->id())->findOrFail($id);
        $award->delete();

        return redirect()->back()->with('success', 'Award deleted successfully.');
    }

    // ============================================
    // WORK EXPERIENCE METHODS
    // ============================================

    public function storeWorkExperience(Request $request)
    {
        $request->validate([
            'company' => 'required|string|max:255',
            'position' => 'nullable|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
            'responsibilities' => 'nullable|string',
        ]);

        auth()->user()->workExperience()->create($request->all());

        return redirect()->back()->with('success', 'Work experience added successfully.');
    }

    public function destroyWorkExperience($id)
    {
        $experience = StaffWorkExperience::where('staff_id', auth()->id())->findOrFail($id);
        $experience->delete();

        return redirect()->back()->with('success', 'Work experience deleted successfully.');
    }

    // ============================================
    // PERSONAL PREFERENCES & NOTIFICATIONS
    // ============================================

    /**
     * Show profile settings page
     */
    public function settings()
    {
        $user = auth()->user();

        // Get user preferences from settings table
        $emailNotifications = Setting::where('key', 'email_notifications')
            ->where('user_id', $user->id)
            ->first();

        $browserNotifications = Setting::where('key', 'browser_notifications')
            ->where('user_id', $user->id)
            ->first();

        $language = Setting::where('key', 'language')
            ->where('user_id', $user->id)
            ->first();

        $timezone = Setting::where('key', 'timezone')
            ->where('user_id', $user->id)
            ->first();

        $clockFormat = Setting::where('key', 'clock_format')
            ->where('user_id', $user->id)
            ->first();

        $showHebrewDate = Setting::where('key', 'show_hebrew_date')
            ->where('user_id', $user->id)
            ->first();

        return view('profile.settings', compact('user', 'emailNotifications', 'browserNotifications', 'language', 'timezone', 'clockFormat', 'showHebrewDate'));
    }

    /**
     * Update profile settings
     */
    public function updateSettings(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'email_notifications' => 'nullable|boolean',
            'browser_notifications' => 'nullable|boolean',
            'language' => 'nullable|string|max:10',
            'timezone' => 'nullable|string|max:50',
            'clock_format' => 'nullable|in:12,24',
            'show_hebrew_date' => 'nullable|boolean',
        ]);

        // Save user preferences to settings table
        if ($request->has('email_notifications')) {
            Setting::updateOrCreate(
                ['key' => 'email_notifications', 'user_id' => $user->id],
                ['value' => $request->email_notifications ? 'true' : 'false', 'type' => 'boolean', 'group' => 'notifications']
            );
        }

        if ($request->has('browser_notifications')) {
            Setting::updateOrCreate(
                ['key' => 'browser_notifications', 'user_id' => $user->id],
                ['value' => $request->browser_notifications ? 'true' : 'false', 'type' => 'boolean', 'group' => 'notifications']
            );
        }

        if ($request->has('language')) {
            Setting::updateOrCreate(
                ['key' => 'language', 'user_id' => $user->id],
                ['value' => $request->language, 'type' => 'text', 'group' => 'preferences']
            );
        }

        if ($request->has('timezone')) {
            Setting::updateOrCreate(
                ['key' => 'timezone', 'user_id' => $user->id],
                ['value' => $request->timezone, 'type' => 'text', 'group' => 'preferences']
            );
        }

        if ($request->has('clock_format')) {
            Setting::updateOrCreate(
                ['key' => 'clock_format', 'user_id' => $user->id],
                ['value' => $request->clock_format, 'type' => 'text', 'group' => 'preferences']
            );
        }

        // Not gated on has() - this is a checkbox, and an unchecked checkbox
        // sends nothing at all, so has() would never see the "off" state.
        Setting::updateOrCreate(
            ['key' => 'show_hebrew_date', 'user_id' => $user->id],
            ['value' => $request->boolean('show_hebrew_date') ? 'true' : 'false', 'type' => 'boolean', 'group' => 'preferences']
        );

        return redirect()->back()->with('success', 'Profile settings updated successfully.');
    }

    /**
     * Show notification settings page
     */
    public function notificationSettings()
    {
        $user = auth()->user();

        // Get user's notification preferences
        $emailNotifications = Setting::where('key', 'email_notifications')
            ->where('user_id', $user->id)
            ->first();

        $browserNotifications = Setting::where('key', 'browser_notifications')
            ->where('user_id', $user->id)
            ->first();

        $reminderDays = Setting::where('key', 'reminder_days')
            ->where('user_id', $user->id)
            ->first();

        return view('profile.notifications', compact('user', 'emailNotifications', 'browserNotifications', 'reminderDays'));
    }

    /**
     * Update notification settings
     */
    public function updateNotificationSettings(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'email_notifications' => 'nullable|boolean',
            'browser_notifications' => 'nullable|boolean',
            'reminder_days' => 'nullable|integer|min:0|max:30',
        ]);

        Setting::updateOrCreate(
            ['key' => 'email_notifications', 'user_id' => $user->id],
            ['value' => $request->email_notifications ? 'true' : 'false', 'type' => 'boolean', 'group' => 'notifications']
        );

        Setting::updateOrCreate(
            ['key' => 'browser_notifications', 'user_id' => $user->id],
            ['value' => $request->browser_notifications ? 'true' : 'false', 'type' => 'boolean', 'group' => 'notifications']
        );

        if ($request->has('reminder_days')) {
            Setting::updateOrCreate(
                ['key' => 'reminder_days', 'user_id' => $user->id],
                ['value' => $request->reminder_days, 'type' => 'number', 'group' => 'notifications']
            );
        }

        return redirect()->back()->with('success', 'Notification settings updated successfully.');
    }

    // ethods for view modals
    public function getDependentJson($id)
    {
        return response()->json(auth()->user()->dependents()->findOrFail($id));
    }

    public function getAddressJson($id)
    {
        return response()->json(auth()->user()->addresses()->findOrFail($id));
    }

    public function getEmergencyJson($id)
    {
        return response()->json(auth()->user()->emergencyContacts()->findOrFail($id));
    }

    public function getQualificationJson($id)
    {
        return response()->json(auth()->user()->qualifications()->findOrFail($id));
    }

    public function getSkillJson($id)
    {
        return response()->json(auth()->user()->skills()->findOrFail($id));
    }

    public function getLanguageJson($id)
    {
        return response()->json(auth()->user()->languages()->findOrFail($id));
    }

    public function getBankJson($id)
    {
        return response()->json(auth()->user()->bankDetails()->findOrFail($id));
    }

    /**
     * Store a new bank account
     */
    public function storeBankAccount(Request $request)
    {
        $request->validate([
            'bank_name' => 'required|string|max:255',
            'account_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:50|unique:staff_bank_details,account_number',
            'is_primary' => 'nullable|boolean',
        ]);

        // If this is marked as primary, unset other primaries
        if ($request->has('is_primary') && $request->is_primary) {
            auth()->user()->bankDetails()->update(['is_primary' => false]);
        }

        auth()->user()->bankDetails()->create([
            'bank_name' => $request->bank_name,
            'account_name' => $request->account_name,
            'account_number' => $request->account_number,
            'is_primary' => $request->has('is_primary') ? true : false,
        ]);

        return redirect()->back()->with('success', 'Bank account added successfully.');
    }

    /**
     * Delete a bank account
     */
    public function destroyBankAccount($id)
    {
        $bank = auth()->user()->bankDetails()->findOrFail($id);
        $bank->delete();

        if (request()->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Bank account deleted successfully.');
    }
}
