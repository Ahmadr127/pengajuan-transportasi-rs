<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class DriverMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->user() || !$request->user()->isDriver()) {
            abort(403);
        }

        return $next($request);
    }
}
