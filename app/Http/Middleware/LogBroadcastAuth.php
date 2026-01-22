<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LogBroadcastAuth
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Log incoming auth attempt (before auth middleware runs)
        Log::info('Broadcast auth request started', [
            'ip' => $request->ip(),
            'path' => $request->path(),
            'method' => $request->method(),
            'headers' => [
                'user-agent' => $request->header('User-Agent'),
                'referer' => $request->header('Referer'),
            ],
            'cookies' => array_keys($request->cookies->all()),
        ]);

        $response = $next($request);

        // After next (auth middleware ran if present) — log outcome and user if available
        $user = auth()->user();
        Log::info('Broadcast auth request finished', [
            'status' => $response->getStatusCode(),
            'user_id' => $user ? $user->id : null,
            'user_email' => $user ? $user->email : null,
        ]);

        return $response;
    }
}
