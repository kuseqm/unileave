<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;

class PasswordResetController extends Controller
{
    /**
     * Show the "forgot password" request form.
     */
    public function showForgotForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Send a password reset link to the given email, if it belongs to an
     * active account. Always shows the same success message either way -
     * confirming or denying an email's existence here would let anyone
     * enumerate real staff emails.
     */
    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        Password::sendResetLink($request->only('email'));

        return back()->with('status', 'If that email is registered, a password reset link has been sent to it.');
    }

    /**
     * Show the "set a new password" form for a given reset token.
     */
    public function showResetForm(Request $request, string $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    /**
     * Complete the reset: verify the token, set the new password, and
     * clear the forced-change flag so the user lands cleanly on their
     * dashboard instead of being asked to change it again immediately.
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'password_change_required' => false,
                    'password_changed_at' => now(),
                    'remember_token' => Str::random(60),
                ])->save();

                // Can't use AuditLogHelper here - it silently no-ops when
                // nobody is authenticated, which is exactly this moment
                // (the whole point of this flow is the user isn't logged
                // in yet). Write the row directly instead.
                AuditLog::create([
                    'user_id' => $user->id,
                    'action' => 'password_reset',
                    'module' => 'auth',
                    'record_id' => $user->id,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('login')->with('success', 'Your password has been reset. You can now sign in.');
        }

        return back()->withErrors(['email' => __($status)]);
    }
}
