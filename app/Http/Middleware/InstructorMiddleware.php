<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InstructorMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        
        // التحقق من أن المستخدم معلم (يمكن تعديل هذا حسب نظام الأدوار)
        if (!$user->is_instructor) {
            abort(403, 'غير مصرح لك بالوصول إلى لوحة تحكم المعلم');
        }

        return $next($request);
    }
} 