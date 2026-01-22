<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ValidateVideoReferrer
{
    public function handle(Request $request, Closure $next)
    {
        $allowedHost = parse_url(config('app.url'), PHP_URL_HOST);
        $referrer = $request->headers->get('referer');

        if ($referrer) {
            $referrerHost = parse_url($referrer, PHP_URL_HOST);
            if ($referrerHost !== $allowedHost) {
                return response()->json(['error' => 'Forbidden Referrer'], 403);
            }
        }

        return $next($request);
    }
}
