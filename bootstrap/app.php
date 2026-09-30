<?php

use App\Http\Middleware\EnsureAddonEnabled;
use App\Http\Middleware\EnsurePolicyIsManageable;
use App\Http\Middleware\LogUserActivity;
use App\Http\Middleware\PreserveFlashOnBackgroundRequests;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Register Spatie permission middleware aliases
        $middleware->alias([
            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
            'role_or_permission' => RoleOrPermissionMiddleware::class,
            'policy.manageable' => EnsurePolicyIsManageable::class,
            'addon' => EnsureAddonEnabled::class,
        ]);

        // Catch-all activity logger for every state-changing request - see
        // app/Http/Middleware/LogUserActivity.php.
        // Background polls (fetch/XHR) must not use up a pending flash message
        // meant for the next real page - see the middleware itself.
        $middleware->web(append: [LogUserActivity::class, PreserveFlashOnBackgroundRequests::class]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
