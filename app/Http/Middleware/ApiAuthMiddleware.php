<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\User;

/**
 * API Authentication Middleware
 * 
 * Handles API authentication and authorization
 * Fixed security vulnerability - now validates tokens against database
 * 
 * @version 5.0.2
 * @date 2025-12-09
 */
class ApiAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Log API request
        Log::info('API Request', [
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        // Check for API token in header
        $apiToken = $request->header('X-API-Token');
        
        if (!$apiToken) {
            return response()->json([
                'success' => false,
                'message' => 'API token is required',
                'error' => 'Missing X-API-Token header'
            ], 401);
        }

        // Validate token format
        if (!$this->isValidTokenFormat($apiToken)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid API token format',
                'error' => 'Token must be a valid format'
            ], 401);
        }

        // Validate token against database
        $user = User::where('api_token', $apiToken)
                    ->where('is_active', true)
                    ->first();
        
        if (!$user) {
            Log::warning('Invalid API token attempt', [
                'token' => substr($apiToken, 0, 8) . '...',
                'ip' => $request->ip()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Invalid API token',
                'error' => 'The provided API token is not valid or has been revoked'
            ], 401);
        }

        // Check if token has expired (if expiration is implemented)
        if (isset($user->api_token_expires_at) && $user->api_token_expires_at < now()) {
            return response()->json([
                'success' => false,
                'message' => 'API token has expired',
                'error' => 'Please generate a new API token'
            ], 401);
        }

        // Add authenticated user to request
        $request->merge([
            'authenticated_user' => $user,
            'api_token' => $apiToken
        ]);

        // Set user for the request
        auth()->setUser($user);

        return $next($request);
    }

    /**
     * Validate token format
     *
     * @param string $token
     * @return bool
     */
    protected function isValidTokenFormat(string $token): bool
    {
        // Token should be at least 32 characters and alphanumeric
        return strlen($token) >= 32 && preg_match('/^[a-zA-Z0-9]+$/', $token);
    }
}
