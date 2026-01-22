<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class EnsureSingleSession
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $currentSessionId = Session::getId();
            
            // Store current session info in cache or DB
            $storedSessionId = cache()->get('user-session-' . $user->id);

            if ($storedSessionId && $storedSessionId !== $currentSessionId) {
                // If session ID changed, logout the old one (or current one)
                // Here we logout the current one if another is active
                Auth::logout();
                return redirect()->route('login')->with('error', 'تم تسجيل الدخول من جهاز آخر. يسمح بجلسة واحدة فقط.');
            }

            // Update session info
            cache()->put('user-session-' . $user->id, $currentSessionId, now()->addHours(24));
            
            // Bind to IP + User Agent for extra security
            $sessionData = [
                'ip' => $request->ip(),
                'ua' => $request->userAgent()
            ];
            cache()->put('user-device-' . $user->id, $sessionData, now()->addHours(24));
        }

        return $next($request);
    }
}
