<?php

namespace App\Http\Controllers;

use App\Helpers\AuditLogHelper;
use App\Models\Department;
use App\Models\LeaveType;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Show the login form
     */
    public function showLoginForm()
    {
        $staffCount = User::count();
        $leaveTypeCount = LeaveType::count();
        $departmentCount = Department::count();

        return view('auth.login', compact('staffCount', 'leaveTypeCount', 'departmentCount'));
    }

    /**
     * Staff login - Web Form Only (Accepts Email or Staff ID)
     */
    public function login(Request $request)
    {
        // Validate input
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        // Keyed by identifier + IP, not just IP - one person mistyping
        // their password shouldn't lock out everyone else on the same
        // network (e.g. a shared office/lab computer), but the identifier
        // alone would let someone hammer many IPs against one account.
        $throttleKey = Str::lower($request->email).'|'.$request->ip();
        $maxAttempts = 5;
        $decayMinutes = 5;

        if (RateLimiter::tooManyAttempts($throttleKey, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $minutes = (int) ceil($seconds / 60);

            return back()->withErrors([
                'email' => "Too many failed login attempts. Please try again in {$minutes} minute(s).",
            ])->onlyInput('email');
        }

        // Find staff by email OR staff_id
        $staff = User::where('email', $request->email)
            ->orWhere('staff_id', $request->email)
            ->first();

        // Check if staff exists and password matches
        if (! $staff || ! Hash::check($request->password, $staff->password)) {
            RateLimiter::hit($throttleKey, $decayMinutes * 60);

            $remaining = $maxAttempts - RateLimiter::attempts($throttleKey);
            $message = $remaining > 0
                ? "The provided credentials do not match our records. {$remaining} attempt(s) remaining before your account is temporarily locked."
                : 'Too many failed login attempts. Your account is temporarily locked - please try again in a few minutes.';

            return back()->withErrors([
                'email' => $message,
            ])->onlyInput('email');
        }

        // Check if account is active
        if ($staff->status !== 'active') {
            return back()->withErrors([
                'email' => 'Your account is '.$staff->status.'. Please contact HR.',
            ])->onlyInput('email');
        }

        // First-ever login needs an explicit Terms/Privacy acceptance;
        // once accepted, later logins don't ask again.
        if (! $staff->terms_accepted_at && ! $request->boolean('terms')) {
            RateLimiter::hit($throttleKey, $decayMinutes * 60);

            return back()->withErrors([
                'terms' => 'Please agree to the Terms of Service and Privacy Policy to continue.',
            ])->onlyInput('email');
        }

        // A correct password clears the counter - only failed attempts
        // should count toward the lockout.
        RateLimiter::clear($throttleKey);

        // Update last login information
        $staff->update([
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
            'terms_accepted_at' => $staff->terms_accepted_at ?? now(),
        ]);

        // Login the user
        Auth::login($staff);

        AuditLogHelper::log('login', 'auth', $staff->id);

        // Redirect based on role
        if ($staff->hasRole('admin')) {
            return redirect()->route('dashboard');
        }

        return redirect()->route('dashboard');
    }

    /**
     * Staff logout - Web Only
     */
    public function logout(Request $request)
    {
        // Logged before Auth::logout() clears the session - the helper
        // reads auth()->id() and would log nothing once logged out.
        AuditLogHelper::log('logout', 'auth', auth()->id());

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
