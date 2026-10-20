<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MustChangePassword
{
    /**
     * Force users who must change their password to the change-password page.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() && $request->user()->must_change_password) {
            if (! $request->routeIs('password.change', 'password.update', 'logout')) {
                return redirect()->route('password.change');
            }
        }

        return $next($request);
    }
}
