<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Teacher\TeacherCourseController;
use App\Http\Controllers\Teacher\TeacherLessonController;
use App\Http\Controllers\Teacher\TeacherDashboardController;
use App\Http\Controllers\Teacher\TeacherSalesController;
use App\Http\Controllers\Teacher\TeacherQuizController;
use App\Http\Controllers\Teacher\TeacherQuizResultController;
use App\Http\Controllers\Teacher\TeacherLiveSessionController;
use App\Http\Controllers\Teacher\TeacherAIController;
use App\Http\Controllers\Teacher\TeacherVideoUploadController;
use App\Http\Controllers\Teacher\TeacherSubscriptionController;
use App\Http\Controllers\Teacher\TeacherStudentsController;

Route::middleware(['auth', 'teacher'])->prefix('teacher')->name('teacher.')->group(function () {
    
    // Dashboard
    Route::get('/', [TeacherDashboardController::class, 'index'])->name('dashboard');
    
    // Courses Management
    Route::resource('courses', TeacherCourseController::class);
    // Toggle publish/draft for a course
    Route::post('courses/{course}/toggle-publish', [TeacherCourseController::class, 'togglePublish'])->name('courses.toggle_publish');

    // Earnings / Analytics
    Route::get('earnings', [\App\Http\Controllers\Teacher\TeacherEarningsController::class, 'index'])->name('earnings.index');
    
    // Lessons Management
    // List lessons page for a course (teacher-facing)
    Route::get('courses/{course}/lessons', [TeacherLessonController::class, 'index'])->name('courses.lessons');
    Route::post('courses/{course}/lessons', [TeacherLessonController::class, 'store'])->name('lessons.store');
    Route::post('courses/{course}/lessons/{lesson}/attach-video', [TeacherLessonController::class, 'attachVideo'])->name('lessons.attach_video');
    Route::put('courses/{course}/lessons/{lesson}', [TeacherLessonController::class, 'update'])->name('lessons.update');
    Route::delete('courses/{course}/lessons/{lesson}', [TeacherLessonController::class, 'destroy'])->name('lessons.destroy');
    
    // Quizzes
    Route::resource('quizzes', TeacherQuizController::class);
    // Quiz Questions (nested)
    Route::resource('quizzes.questions', \App\Http\Controllers\Teacher\TeacherQuestionController::class)->shallow();
    // Quiz Results for teacher to review attempts
    Route::resource('quizzes.results', TeacherQuizResultController::class)->only(['index','show'])->shallow();
    
    // Live Sessions
    // Note: teacher live-sessions resource is defined in routes/web.php to keep naming consistent.
    
    // Video Upload
    Route::post('video/upload', [TeacherVideoUploadController::class, 'upload'])->name('video.upload');
    
    // Sales & Revenue
    Route::get('sales', [TeacherSalesController::class, 'index'])->name('sales');
    
    // AI Assistant
    // Teacher assistant UI and endpoints (full features)
    Route::get('courses/{course}/ai', [\App\Http\Controllers\Teacher\TeacherAIController::class, 'showAssistant'])->name('courses.ai.show');
    Route::post('courses/{course}/ai/summarize', [\App\Http\Controllers\Teacher\TeacherAIController::class, 'summarizeCourse'])->name('courses.ai.summarize');
    Route::post('courses/{course}/ai/generate-questions', [\App\Http\Controllers\Teacher\TeacherAIController::class, 'generateQuestions'])->name('courses.ai.generate_questions');

    // Subscriptions management
    Route::get('subscriptions', [TeacherSubscriptionController::class, 'index'])->name('subscriptions.index');
    // Students list for teacher
    Route::get('students', [TeacherStudentsController::class, 'index'])->name('students');
    // Teacher chats (course-level chats between teacher and students)
    Route::get('chats', [\App\Http\Controllers\Teacher\TeacherChatController::class, 'index'])->name('chats.index');
});
