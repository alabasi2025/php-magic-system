<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware to check if the authenticated user has any of the required roles.
 *
 * This middleware assumes that the User model has a 'hasRole' method,
 * typically provided by a package like spatie/laravel-permission or a custom implementation.
 *
 * Usage in routes: middleware('role:admin,editor')
 */
class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles  The list of required roles (e.g., 'admin', 'editor').
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // 1. Check if a user is authenticated
        if (!Auth::check()) {
            // If not authenticated, redirect to login or abort with 401 Unauthorized.
            // For middleware, aborting with 403 is common for authorization failures,
            // but 401 might be more appropriate if the user is not logged in at all.
            // We'll use 403 as it's an authorization check, assuming the 'auth' middleware runs first.
            return abort(Response::HTTP_FORBIDDEN, 'You are not authorized to access this resource. Please log in.');
        }

        $user = $request->user();

        // 2. Check if the user has any of the required roles
        // The middleware parameter is a comma-separated string, which Laravel automatically
        // passes as separate arguments if the method signature uses variadic arguments (...$roles).
        // We iterate through the required roles.
        foreach ($roles as $role) {
            // We assume the User model has a hasRole($role) method.
            if ($user->hasRole($role)) {
                // If the user has at least one of the required roles, allow the request to proceed.
                return $next($request);
            }
        }

        // 3. If the loop completes without finding a matching role, deny access.
        // Abort with a 403 Forbidden response.
        return abort(Response::HTTP_FORBIDDEN, 'You do not have the required role(s) to access this resource.');
    }
}