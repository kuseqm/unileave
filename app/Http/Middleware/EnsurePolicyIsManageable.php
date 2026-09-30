<?php

namespace App\Http\Middleware;

use App\Models\Policy;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Stops anyone but the owner from changing a policy. Permissions alone
 * ("edit_policies") say a person may edit policies in general - this decides
 * *which* ones: their own university's, and platform defaults only for
 * super-admins. Reading is unaffected (the route binding already limits what
 * can be opened at all).
 */
class EnsurePolicyIsManageable
{
    public function handle(Request $request, Closure $next): Response
    {
        $policy = $request->route('policy');

        abort_unless(
            $policy instanceof Policy && $policy->isManageableBy($request->user()),
            403,
            $policy instanceof Policy && $policy->isPlatform()
                ? 'This is a platform default policy. Copy it to your university ("Customize for my university") and edit your own copy.'
                : 'You cannot change a policy that belongs to another university.'
        );

        return $next($request);
    }
}
