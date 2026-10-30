<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class LogApiRequests
{
    public function handle(Request $request, Closure $next): Response
    {
        $start = microtime(true);

        $response = $next($request);

        $durationMs = round((microtime(true) - $start) * 1000, 2);

        $user = $request->user();

        Log::info('[API] '.$request->method().' '.$request->path(), [
            'user_id' => $user?->id,
            'status' => $response->getStatusCode(),
            'duration_ms' => $durationMs,
            'query' => $request->query(),
            'body' => $request->except(['password', 'token', 'password_confirmation']),
        ]);

        if ($response->getStatusCode() >= 400) {
            Log::warning('[API] response body', [
                'status' => $response->getStatusCode(),
                'body' => substr((string) $response->getContent(), 0, 1000),
            ]);
        }

        return $response;
    }
}
