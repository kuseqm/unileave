<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsurePasswordIsChanged
{
    /** Force a password change after this many days, independent of the temp-password flag. */
    private const PASSWORD_EXPIRY_DAYS = 120;

    public function handle(Request $request, Closure $next)
    {
        if (! auth()->check()) {
            return $next($request);
        }

        $user = auth()->user();

        $isTemporary = $user->password_change_required;
        $isExpired = ! $isTemporary
            && (! $user->password_changed_at || $user->password_changed_at->lt(now()->subDays(self::PASSWORD_EXPIRY_DAYS)));

        if (! $isTemporary && ! $isExpired) {
            return $next($request);
        }

        if ($request->routeIs('profile.show', 'profile.password.update', 'logout')) {
            return $next($request);
        }

        $message = $isTemporary
            ? 'You must change your temporary password before continuing.'
            : 'Your password has expired after '.self::PASSWORD_EXPIRY_DAYS.' days. Please set a new one to continue.';

        return redirect()->route('profile.show')->with('warning', $message);
    }
}
