<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;

class Middleware27
{
    public function handle(Request $request, Closure $next)
    {
        return $next($request);
    }
}
