<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware to check if the authenticated user has a specific permission.
 *
 * This middleware assumes that the User model (or the model returned by Auth::user())
 * has a method named `hasPermission(string $permission)` which returns a boolean.
 *
 * Usage in routes:
 * Route::get('/admin/users', [UserController::class, 'index'])->middleware('permission:manage_users');
 */
class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $permission The required permission slug (e.g., 'manage_users').
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        // 1. Check if a user is authenticated
        if (!Auth::check()) {
            // If not authenticated, return a 403 Forbidden response.
            return $this->unauthorizedResponse($request, 'Authentication required to access this resource.');
        }

        $user = Auth::user();

        // 2. Check if the authenticated user has the required permission
        // This relies on a hypothetical hasPermission method on the User model.
        if (!method_exists($user, 'hasPermission')) {
            // Log an error if the required method is missing, indicating a configuration issue.
            \Log::error('User model is missing the required hasPermission method for CheckPermission middleware.', ['user_id' => $user->id ?? 'N/A']);
            // Fail safe: deny access if the permission check mechanism is broken.
            return $this->unauthorizedResponse($request, 'Permission check mechanism is not configured correctly.');
        }

        if (!$user->hasPermission($permission)) {
            // 3. If the user does not have the permission, return a 403 Forbidden response.
            return $this->unauthorizedResponse($request, "User does not have the required permission: '{$permission}'.");
        }

        // 4. If the user has the permission, allow the request to proceed.
        return $next($request);
    }

    /**
     * Return a 403 Forbidden response, handling both web and API requests.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $message
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function unauthorizedResponse(Request $request, string $message): Response
    {
        if ($request->expectsJson()) {
            // For API requests, return a JSON response
            return response()->json([
                'message' => $message,
                'error' => 'Forbidden',
            ], Response::HTTP_FORBIDDEN); // 403 Forbidden
        }

        // For web requests, redirect to a home page or show a 403 view
        // We'll redirect to the home page with an error message.
        // NOTE: 'home' route must be defined in web.php
        if (app('router')->has('home')) {
            return redirect()->route('home')->with('error', $message);
        }

        // Fallback for web if 'home' route is not defined
        return response()->view('errors.403', ['message' => $message], Response::HTTP_FORBIDDEN);
    }
}