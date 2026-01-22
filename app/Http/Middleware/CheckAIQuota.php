<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AIUsageLog;
use Carbon\Carbon;

class CheckAIQuota
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        
        // الأدمن ليس له حدود
        if ($user->isAdmin()) {
            return $next($request);
        }

        // تحديد الحد بناءً على نوع الاشتراك
        $limit = ($user->subscription_type === 'premium') 
            ? config('services.ai_limits.premium') 
            : config('services.ai_limits.free');

        // حساب عدد الطلبات اليوم
        $usageCount = AIUsageLog::where('user_id', $user->id)
            ->whereDate('created_at', Carbon::today())
            ->count();

        if ($usageCount >= $limit) {
            return response()->json([
                'error' => 'لقد وصلت للحد الأقصى من طلبات الذكاء الاصطناعي لهذا اليوم. يرجى الترقية أو المحاولة غداً.'
            ], 429);
        }

        return $next($request);
    }
}
