<?php

namespace App\Http\Middleware;

use App\Helpers\AuditLogHelper;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Catch-all activity logger: records every state-changing (POST/PUT/PATCH/
 * DELETE) request an authenticated user makes, so nothing new ever ships
 * unaudited by default. Routes that already log themselves with richer
 * detail (before/after values, a specific action name) are skipped here to
 * avoid a duplicate, blander entry sitting next to the real one.
 */
class LogUserActivity
{
    /**
     * Route names already logged elsewhere with more useful detail.
     */
    private const ALREADY_LOGGED = [
        'login', 'logout',
        'profile.password.update',
        'leave-requests.store', 'leave-requests.approve', 'leave-requests.reject',
        'leave-requests.acting.process', 'leave-requests.cancel',
        'leave-requests.dismiss-unresponsive', 'leave-requests.restore-unresponsive',
        'leave-requests.reassign-acting', 'leave-requests.override-acting',
        'permissions.store', 'permissions.cancel',
        'permissions.hod.approve', 'permissions.hod.reject',
        'permissions.hr.approve', 'permissions.hr.reject',
        'travel-permissions.store', 'travel-permissions.approve', 'travel-permissions.reject',
        'travel-permissions.acting.process',
        'registry.incoming.store', 'registry.incoming.update', 'registry.incoming.destroy',
        'registry.incoming.assign', 'registry.incoming.status',
        'staff.store', 'staff.update', 'staff.destroy',
    ];

    /**
     * Route-name suffix -> action label. Anything not listed keeps its
     * literal suffix (e.g. "assign", "dispatch", "submit").
     */
    private const ACTION_ALIASES = [
        'store' => 'create',
        'destroy' => 'delete',
        'update' => 'update',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (! in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'], true)) {
            return $response;
        }

        if (! auth()->check()) {
            return $response;
        }

        $routeName = $request->route()?->getName();

        if (! $routeName || in_array($routeName, self::ALREADY_LOGGED, true)) {
            return $response;
        }

        if (! $this->wasSuccessful($response)) {
            return $response;
        }

        [$module, $action] = $this->deriveModuleAndAction($routeName);

        AuditLogHelper::log($action, $module, $this->extractRecordId($request));

        return $response;
    }

    /**
     * A validation failure or a deliberate ->with('error', ...) redirect
     * means the action didn't actually happen - only record real outcomes.
     */
    private function wasSuccessful(Response $response): bool
    {
        if ($response->isRedirect()) {
            return ! session()->has('errors') && ! session()->has('error');
        }

        return $response->isSuccessful();
    }

    /**
     * @return array{0: string, 1: string}
     */
    private function deriveModuleAndAction(string $routeName): array
    {
        $parts = explode('.', $routeName);
        $suffix = end($parts);
        $module = implode('.', array_slice($parts, 0, -1)) ?: $routeName;
        $action = self::ACTION_ALIASES[$suffix] ?? $suffix;

        return [$module, $action];
    }

    private function extractRecordId(Request $request): ?string
    {
        foreach ($request->route()?->parameters() ?? [] as $value) {
            if (is_scalar($value) && ! is_bool($value)) {
                return (string) $value;
            }
        }

        return null;
    }
}
