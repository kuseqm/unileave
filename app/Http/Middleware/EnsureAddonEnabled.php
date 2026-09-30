<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Gates an opt-in premium module per institution - permission alone
 * ("view_students") says a person's role is allowed to use the feature at
 * all; this decides whether their own university has been granted it in
 * the first place. A super-admin (managing every institution) always
 * passes, regardless of any single university's own setting.
 */
class EnsureAddonEnabled
{
    public function handle(Request $request, Closure $next, string $addonKey): Response
    {
        abort_unless(
            $request->user()->hasAddon($addonKey),
            403,
            'This feature is not enabled for your institution. Contact the system administrator to request access.'
        );

        return $next($request);
    }
}
