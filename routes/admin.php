<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AdminCourseController;
use App\Http\Controllers\Admin\AdminUniversityController;
use App\Http\Controllers\Admin\AdminSubjectController;
use App\Http\Controllers\Admin\AdminUniCourseController;
use App\Http\Controllers\Admin\AdminCategoryController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminChatController;
use App\Http\Controllers\Admin\AdminLiveSessionController;
use App\Http\Controllers\Admin\AdminAIController;
use App\Http\Controllers\Admin\AdminEnrollmentController;

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    
    Route::get('/', [AdminController::class, 'index'])->name('dashboard');
    
    // الجامعات والمقررات
    Route::resource('universities', AdminUniversityController::class);
    Route::resource('subjects', AdminSubjectController::class);
    Route::resource('uni-courses', AdminUniCourseController::class);
    Route::resource('categories', AdminCategoryController::class);
    
    // الكورسات والمستخدمين
    Route::resource('courses', AdminCourseController::class);
    Route::resource('users', AdminUserController::class);
    
    // اشتراكات الدورات (نظرة عامة)
    Route::get('enrollments', [AdminEnrollmentController::class, 'overview'])->name('enrollments.index');
    Route::get('courses/{course}/enrollments', [AdminEnrollmentController::class, 'index'])->name('courses.enrollments');
    Route::post('courses/{course}/enrollments', [AdminEnrollmentController::class, 'enroll'])->name('enrollments.store');
    Route::put('courses/{course}/enrollments/{student}', [AdminEnrollmentController::class, 'updateStatus'])->name('enrollments.update');
    
    // نظام الخدمات الجديد (دردشات العملاء)
    Route::get('customer-chats', [AdminChatController::class, 'index'])->name('customer-chats.index');
    Route::get('customer-chats/{room}', [AdminChatController::class, 'show'])->name('customer-chats.show');
    
    // الحصص الأونلاين والـ AI
    Route::resource('live-sessions', controller: AdminLiveSessionController::class);
    // مساعدة ذكية خاصة بالكورس: تلخيص وإنشاء أسئلة
    Route::get('courses/{course}/ai-assistant', [\App\Http\Controllers\Admin\AdminCourseAIController::class, 'show'])->name('courses.ai.assistant');
    Route::post('courses/{course}/ai-assistant/summarize', [\App\Http\Controllers\Admin\AdminCourseAIController::class, 'summarize'])->name('courses.ai.summarize');
    Route::post('courses/{course}/ai-assistant/generate-questions', [\App\Http\Controllers\Admin\AdminCourseAIController::class, 'generateQuestions'])->name('courses.ai.generate_questions');
    Route::get('ai-reports', [AdminAIController::class, 'reports'])->name('ai.reports');
});
