<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use App\Models\LearningProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LessonController extends Controller
{
    /**
     * Display the specified lesson.
     */
    public function show(Lesson $lesson)
    {
        $user = Auth::user();
        
        // Check if user can access this lesson
        if (!$lesson->canUserAccess($user)) {
            // Allow preview for admins and the course owner (teacher)
            if ($user && ($user->isAdmin() || ($user->isTeacher() && $lesson->course->user_id === $user->id))) {
                // allow preview — do not create progress records for preview-only users
            } else {
                if (!$user) {
                    return redirect()->route('login');
                }

                return redirect()->route('courses.show', $lesson->course)
                    ->with('error', 'يجب شراء الدورة للوصول إلى هذا الدرس.');
            }
        }

        $lesson->load(['course']);
        
        // Get or create learning progress for authenticated users
        $progress = null;
        if ($user) {
            $progress = LearningProgress::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'lesson_id' => $lesson->id,
                ],
                [
                    'completed' => false,
                    'watched_duration' => 0,
                ]
            );
        }

        // Get next and previous lessons
        $nextLesson = Lesson::where('course_id', $lesson->course_id)
            ->where('order', '>', $lesson->order)
            ->orderBy('order')
            ->first();
            
        $previousLesson = Lesson::where('course_id', $lesson->course_id)
            ->where('order', '<', $lesson->order)
            ->orderBy('order', 'desc')
            ->first();

        return view('lessons.show', compact(
            'lesson',
            'progress',
            'nextLesson',
            'previousLesson'
        ));
    }

    /**
     * Update learning progress.
     */
    public function updateProgress(Request $request, Lesson $lesson)
    {
        $user = Auth::user();
        
        if (!$user || !$lesson->canUserAccess($user)) {
            return response()->json(['error' => 'غير مصرح'], 403);
        }

        $request->validate([
            'watched_duration' => 'required|integer|min:0',
            'completed' => 'boolean',
        ]);

        $progress = LearningProgress::firstOrCreate(
            [
                'user_id' => $user->id,
                'lesson_id' => $lesson->id,
            ],
            [
                'completed' => false,
                'watched_duration' => 0,
            ]
        );

        if ($request->has('completed') && $request->completed) {
            $progress->markAsCompleted();
        } else {
            $progress->updateWatchedDuration($request->watched_duration);
        }

        return response()->json([
            'success' => true,
            'progress' => $progress->fresh(),
        ]);
    }

    /**
     * Mark lesson as completed.
     */
    public function markCompleted(Lesson $lesson)
    {
        $user = Auth::user();
        
        if (!$user || !$lesson->canUserAccess($user)) {
            return response()->json(['error' => 'غير مصرح'], 403);
        }

        $progress = LearningProgress::firstOrCreate(
            [
                'user_id' => $user->id,
                'lesson_id' => $lesson->id,
            ],
            [
                'completed' => false,
                'watched_duration' => 0,
            ]
        );

        $progress->markAsCompleted();

        return response()->json([
            'success' => true,
            'message' => 'تم تسجيل إكمال الدرس بنجاح!',
        ]);
    }
}

