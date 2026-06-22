<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // 1. Ensure the user is actually logged in
        if (!Auth::check()) {
            return redirect('/login');
        }

        $user = Auth::user();

        // 2. Check if the user's role exists in the allowed roles array
        if (!in_array($user->role, $roles)) {
            // If they try to access a route they shouldn't, throw a 403 Forbidden error
            abort(403, 'Unauthorized action. You do not have the required permissions.');
        }

        // 3. If everything is good, let the request proceed
        return $next($request);
    }
}