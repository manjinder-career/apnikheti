<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Please log in to access administrative pages.');
        }

        $user = auth()->user();

        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        if (empty($roles)) {
            if ($user->isAdmin()) {
                return $next($request);
            }
        } else {
            if ($user->hasRole($roles)) {
                return $next($request);
            }
        }

        abort(403, 'Unauthorized action for your role.');
    }
}
