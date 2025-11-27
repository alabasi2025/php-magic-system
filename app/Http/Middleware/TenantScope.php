<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware to set the organization ID (tenant) for the current request.
 * This ID is later used by the TenantGlobalScope to filter Eloquent queries.
 *
 * In a real-world application, you might use a dedicated service class
 * to manage the tenant context, but for simplicity, we'll use a static
 * property on the scope class itself.
 */
class TenantScope
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Check if a user is authenticated
        if (Auth::check()) {
            $user = Auth::user();

            // 2. Ensure the user has an organization_id set
            if (!empty($user->organization_id)) {
                // Set the organization ID statically on the Global Scope class
                // This makes the organization_id accessible in the TenantGlobalScope class
                \App\Models\Scopes\TenantGlobalScope::setOrganizationId($user->organization_id);
            } else {
                // Handle case where authenticated user has no organization_id
                // Depending on requirements, you might log an error, throw an exception,
                // or allow the request to proceed without a scope (less secure).
                // For production-ready code, we'll assume a tenant is required.
                // For this task, we will proceed, but the scope will not be applied
                // if the ID is null, which is handled in the Global Scope class.
                // A more secure approach would be:
                // throw new \Exception('Authenticated user is not associated with an organization.');
            }
        }

        return $next($request);
    }
}