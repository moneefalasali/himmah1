<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckCourseSubscription
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        
        // الإدارة والمعلم (صاحب الدورة) يمكنهم الوصول دائماً
        if ($user->isAdmin()) {
            return $next($request);
        }

        // الحصول على معرف الدورة من المسار (Route Parameter)
        $courseId = $request->route('course') ? $request->route('course')->id : $request->route('course_id');

        if ($user->isTeacher()) {
            // التحقق إذا كان المعلم هو صاحب هذه الدورة
            $isOwner = $user->teacherCourses()->where('id', $courseId)->exists();
            if ($isOwner) return $next($request);
        }

        // التحقق من اشتراك الطالب
        $subscription = $user->enrolledCourses()->where('course_id', $courseId)->first();

        if (!$subscription) {
            return redirect()->route('home')->with('error', 'أنت غير مشترك في هذه الدورة.');
        }

        $pivot = $subscription->pivot;

        if ($pivot->status === 'suspended') {
            return response()->view('errors.subscription_suspended', [], 403);
        }

        if ($pivot->status === 'expired' || ( $pivot->subscription_end && now()->gt($pivot->subscription_end) )) {
            // تحديث الحالة تلقائياً إذا انتهى الوقت
            if ($pivot->status !== 'expired') {
                $user->enrolledCourses()->updateExistingPivot($courseId, ['status' => 'expired']);
            }
            return response()->view('errors.subscription_expired', [], 403);
        }

        return $next($request);
    }
}
