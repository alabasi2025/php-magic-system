<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;

class Middleware38
{
    public function handle(Request $request, Closure $next)
    {
        return $next($request);
    }
}
