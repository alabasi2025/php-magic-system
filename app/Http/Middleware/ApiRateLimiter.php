<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

/**
 * Custom Middleware to implement API throttling for authenticated users.
 *
 * This middleware enforces a limit of 60 requests per minute for any
 * authenticated user accessing the routes it is applied to.
 * Unauthenticated users will fall back to the default 'api' rate limit
 * defined in the RouteServiceProvider, or will not be throttled if
 * the route is not protected by the 'api' middleware group.
 */
class ApiRateLimiter
{
    /**
     * The maximum number of attempts allowed per minute.
     *
     * @var int
     */
    protected $maxAttempts = 60;

    /**
     * The decay time in minutes.
     *
     * @var int
     */
    protected $decayMinutes = 1;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if the user is authenticated. This middleware is specifically for authenticated users.
        if (Auth::check()) {
            // Define a unique key for the authenticated user's rate limit.
            // Using the user's ID ensures each user has their own independent counter.
            $key = 'api-auth:' . Auth::id();

            // Check if the user has exceeded the rate limit.
            if (RateLimiter::tooManyAttempts($key, $this->maxAttempts)) {
                // Get the number of seconds remaining until the user can try again.
                $retryAfter = RateLimiter::availableIn($key);

                // Throw a 429 Too Many Requests response.
                return response()->json([
                    'message' => 'Too Many Attempts. Rate limit exceeded.',
                    'retry_after_seconds' => $retryAfter,
                ], Response::HTTP_TOO_MANY_REQUESTS)
                ->withHeaders([
                    'X-RateLimit-Limit' => $this->maxAttempts,
                    'X-RateLimit-Remaining' => 0,
                    'Retry-After' => $retryAfter,
                ]);
            }

            // Increment the attempt count for the user.
            // The decay time is converted to seconds for the hit method.
            RateLimiter::hit($key, $this->decayMinutes * 60);

            // Proceed with the request.
            $response = $next($request);

            // Attach rate limit headers to the response for informational purposes.
            $response->headers->add([
                'X-RateLimit-Limit' => $this->maxAttempts,
                'X-RateLimit-Remaining' => RateLimiter::remaining($key, $this->maxAttempts),
            ]);

            return $response;
        }

        // If the user is not authenticated, simply pass the request through.
        // Throttling for unauthenticated users should be handled by a separate
        // rate limiter definition (e.g., the default 'api' limiter in RouteServiceProvider
        // or a separate middleware applied to the route group).
        return $next($request);
    }
}