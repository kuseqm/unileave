<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * A "saved successfully" message is flashed for exactly one following
 * request. The app's pages poll in the background (notification and message
 * counts, dashboard refreshes) - if one of those requests lands between the
 * redirect and the page the user is being sent to, it used up the message
 * and the page showed nothing. Background requests (fetch/XHR) therefore
 * hand the flash data on untouched to the next real page load.
 */
class PreserveFlashOnBackgroundRequests
{
    public function handle(Request $request, Closure $next): Response
    {
        $isBackground = $request->ajax()
            || $request->expectsJson()
            || $request->header('Sec-Fetch-Dest') === 'empty';

        if ($isBackground && $request->hasSession()) {
            $request->session()->reflash();
        }

        return $next($request);
    }
}
