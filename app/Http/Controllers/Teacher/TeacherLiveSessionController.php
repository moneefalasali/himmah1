<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\LiveSession;
use App\Models\Course;
use App\Services\ZoomService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

class TeacherLiveSessionController extends Controller
{
    protected $zoomService;

    public function __construct(ZoomService $zoomService)
    {
        $this->zoomService = $zoomService;
    }

    public function index()
    {
        // If the `teacher_id` column is missing on live_sessions, avoid throwing a QueryException
        try {
            if (Schema::hasColumn('live_sessions', 'teacher_id')) {
                $sessions = LiveSession::where('teacher_id', Auth::id())
                    ->with('course')
                    ->latest()
                    ->paginate(10);
            } else {
                Log::warning('live_sessions.teacher_id column missing; returning empty sessions for teacher ' . Auth::id());
                $sessions = LiveSession::with('course')->whereRaw('0 = 1')->paginate(10);
            }
        } catch (\Exception $e) {
            Log::error('Error querying live sessions: ' . $e->getMessage());
            $sessions = LiveSession::with('course')->whereRaw('0 = 1')->paginate(10);
        }

        return view('teacher.live_sessions.index', compact('sessions'));
    }

    public function create()
    {
        try {
            if (Schema::hasColumn('courses', 'teacher_id')) {
                $courses = Course::where('teacher_id', Auth::id())->get();
            } elseif (Schema::hasColumn('courses', 'user_id')) {
                // some schemas use `user_id` as the course owner
                $courses = Course::where('user_id', Auth::id())->get();
            } else {
                Log::warning('courses.teacher_id/user_id column missing; returning empty collection for create live session');
                $courses = collect();
            }
        } catch (\Exception $e) {
            Log::error('Error retrieving courses for live session create: ' . $e->getMessage());
            $courses = collect();
        }

        return view('teacher.live_sessions.create', compact('courses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'topic' => 'required|string|max:255',
            'start_time' => 'required|date|after:now',
            'duration' => 'required|integer|min:15|max:300',
        ]);

        try {
            // 1. إنشاء الاجتماع في Zoom
            $zoomMeeting = $this->zoomService->createMeeting([
                'topic' => $request->topic,
                'start_time' => date('Y-m-d\TH:i:s', strtotime($request->start_time)),
                'duration' => $request->duration,
            ]);

            // 2. Validate ownership: ensure selected course belongs to this teacher
            $courseOk = false;
            try {
                if (Schema::hasColumn('courses', 'teacher_id')) {
                    $courseOk = Course::where('id', $request->course_id)->where('teacher_id', Auth::id())->exists();
                } elseif (Schema::hasColumn('courses', 'user_id')) {
                    $courseOk = Course::where('id', $request->course_id)->where('user_id', Auth::id())->exists();
                } else {
                    // Cannot verify ownership safely; deny to be secure
                    $courseOk = false;
                }
            } catch (\Exception $e) {
                Log::error('Error validating course ownership: ' . $e->getMessage());
                $courseOk = false;
            }

            if (! $courseOk) {
                return back()->with('error', 'غير مسموح إنشاء جلسة لهذه الدورة. الرجاء اختيار إحدى دوراتك.');
            }

            // 3. Save the live session in DB
            LiveSession::create([
                'course_id' => $request->course_id,
                'teacher_id' => Auth::id(),
                'topic' => $request->topic,
                'meeting_id' => $zoomMeeting['id'],
                'start_url' => $zoomMeeting['start_url'],
                'join_url' => $zoomMeeting['join_url'],
                'start_time' => $request->start_time,
                'duration' => $request->duration,
                'status' => 'active',
            ]);

            return redirect()->route('teacher.live-sessions.index')
                ->with('success', 'تم إنشاء الحصة الأونلاين بنجاح.');

        } catch (\Exception $e) {
            return back()->with('error', 'فشل إنشاء الحصة: ' . $e->getMessage());
        }
    }

    public function destroy(LiveSession $liveSession)
    {
        $this->authorize('delete', $liveSession);

        try {
            $this->zoomService->deleteMeeting($liveSession->meeting_id);
            $liveSession->delete();
            return back()->with('success', 'تم حذف الحصة بنجاح.');
        } catch (\Exception $e) {
            return back()->with('error', 'فشل حذف الحصة من Zoom.');
        }
    }
}
