<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\LiveSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentLiveSessionController extends Controller
{
    public function index()
    {
        // جلب الحصص للكورسات التي اشترك فيها الطالب
        $sessions = LiveSession::whereHas('course.students', function ($query) {
            $query->where('user_id', Auth::id());
        })
        ->where('status', 'active')
        ->orderBy('start_time', 'asc')
        ->get();

        return view('user.live_sessions.index', compact('sessions'));
    }

    public function join(LiveSession $liveSession)
    {
        // 1. التحقق من الاشتراك
        $isSubscribed = Auth::user()->courses()
            ->where('course_id', $liveSession->course_id)
            ->wherePivot('expires_at', '>', now())
            ->exists();

        if (!$isSubscribed) {
            return back()->with('error', 'يجب أن تكون مشتركاً في الكورس وباشتراك ساري للدخول.');
        }

        // 2. التحقق من الوقت
        if (!$liveSession->isLive()) {
            if ($liveSession->isFinished()) {
                return back()->with('error', 'هذه الحصة قد انتهت بالفعل.');
            }
            return back()->with('error', 'الحصة لم تبدأ بعد. يمكنك الدخول قبل الموعد بـ 10 دقائق.');
        }

        // 3. التوجيه إلى Zoom
        return redirect($liveSession->join_url);
    }
}
