<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Show the login form
     */
    public function showLoginForm()
    {
        return view('auth.login');
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

        // Find staff by email OR staff_id
        $staff = User::where('email', $request->email)
                    ->orWhere('staff_id', $request->email)
                    ->first();

        // Check if staff exists and password matches
        if (!$staff || !Hash::check($request->password, $staff->password)) {
            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ])->onlyInput('email');
        }

        // Check if account is active
        if ($staff->status !== 'active') {
            return back()->withErrors([
                'email' => 'Your account is ' . $staff->status . '. Please contact HR.',
            ])->onlyInput('email');
        }

        // Update last login information
        $staff->update([
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
        ]);

        // Login the user
        Auth::login($staff);

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
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
