<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!auth()->check()) {
            return redirect('login');
        }

        foreach ($roles as $role) {
            if (auth()->user()->hasRole($role)) {
                return $next($request);
            }
        }

        abort(403, 'Unauthorized access.');
    }
}