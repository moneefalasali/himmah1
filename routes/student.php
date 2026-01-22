<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\StudentQuizController;
use App\Http\Controllers\User\StudentLiveSessionController;
use App\Http\Controllers\User\StudentAIController;

Route::middleware(['auth'])->prefix('student')->name('student.')->group(function () {
    
    // Quizzes
    Route::get('quizzes/{quiz}', [StudentQuizController::class, 'show'])->name('quizzes.show');
    Route::get('quizzes/{quiz}/take', [StudentQuizController::class, 'start'])->name('quizzes.take');
    Route::post('quizzes/{quiz}/submit', [StudentQuizController::class, 'submit'])->name('quizzes.submit');
    Route::get('quizzes/results/{result}', [StudentQuizController::class, 'result'])->name('quizzes.result');
    
    // Live Sessions
    Route::get('live-sessions', [StudentLiveSessionController::class, 'index'])->name('live-sessions.index');
    Route::get('live-sessions/{session}/join', [StudentLiveSessionController::class, 'join'])->name('live-sessions.join');
    
    // AI Assistant (student-facing: chat/summary)
    Route::get('courses/{course}/ai', [\App\Http\Controllers\User\StudentAIController::class, 'showAssistant'])->name('courses.ai.show');
    Route::post('courses/{course}/ai/ask', [\App\Http\Controllers\User\StudentAIController::class, 'chat'])->name('courses.ai.ask');
});
