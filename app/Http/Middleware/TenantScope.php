<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;

class TenantScope
{
    public function handle(Request $request, Closure $next)
    {
        // For subdomain-based tenancy
        $domain = $request->getHost();
        $tenant = Tenant::where('domain', $domain)->first();

        if (! $tenant && $request->user()) {
            // Fallback to user's tenant
            $tenant = $request->user()->tenant;
        }

        if (! $tenant) {
            abort(403, 'Tenant not found.');
        }

        // Bind tenant to service container
        app()->instance('tenant', $tenant);
        // Scope all queries automatically via model's global scope (we'll add later)

        return $next($request);
    }
}
