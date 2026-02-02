<?php

use App\Http\Controllers\Admin\AdminAIController;
use App\Http\Controllers\Admin\AdminCategoryController;
use App\Http\Controllers\Admin\AdminChatController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AdminCourseController;
use App\Http\Controllers\Admin\AdminLiveSessionController;
use App\Http\Controllers\Admin\AdminServiceController;
use App\Http\Controllers\Admin\AdminSubjectController;
use App\Http\Controllers\Admin\AdminUniCourseController;
use App\Http\Controllers\Admin\AdminUniversityController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\VideoStreamController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\User\StudentAIController;
use App\Http\Controllers\User\StudentLiveSessionController;
use App\Http\Controllers\Teacher\TeacherAIController;
use App\Http\Controllers\Teacher\TeacherCourseController;
use App\Http\Controllers\Teacher\TeacherLiveSessionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ZoomWebhookController;
use Illuminate\Support\Facades\Route;

// --- Routes from himmah1-main ---
// استعادة كلمة المرور
Route::get('/password/forgot', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
// إعادة تعيين كلمة المرور
Route::get('/password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');

















/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// الصفحة الرئيسية
Route::get('/', function () {
    return view('welcome');
})->name('home');

// routes المصادقة
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    // Teacher self-registration
    Route::get('/register/teacher', [AuthController::class, 'showTeacherRegisterForm'])->name('register.teacher');
    Route::post('/register/teacher', [AuthController::class, 'registerTeacher']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// routes الدورات (متاحة للجميع)
Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
Route::get('/courses/search', [CourseController::class, 'search'])->name('courses.search');
Route::get('/courses/{course}', [CourseController::class, 'show'])->name('courses.show');

// routes المستخدمين المصادق عليهم
Route::middleware('auth')->group(function () {
    // لوحة التحكم
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [UserController::class, 'profile'])->name('profile');
    Route::put('/profile', [UserController::class, 'updateProfile'])->name('profile.update');
    Route::put('/profile/password', [UserController::class, 'changePassword'])->name('profile.password');
    
    // الدورات والخدمات
    Route::get('/my-courses', [UserController::class, 'myCourses'])->name('my-courses');
    Route::get('/my-service-requests', [UserController::class, 'myServiceRequests'])->name('my-service-requests');
    Route::get('/payment-history', [UserController::class, 'paymentHistory'])->name('payment-history');
    
    // الدورات - routes تتطلب تسجيل دخول
    Route::get('/courses/{course}/curriculum', [CourseController::class, 'curriculum'])->name('courses.curriculum');
    Route::post('/courses/{course}/review', [CourseController::class, 'storeReview'])->name('courses.review');
    
    // الدروس
    Route::get('/lessons/{lesson}', [LessonController::class, 'show'])->name('lessons.show');
    // Provide short-lived signed HLS master playlist URL (used by frontend to avoid embedding URLs in page source)
    Route::get('/lessons/{lesson}/stream-url', [VideoStreamController::class, 'getStreamUrl'])->name('lessons.stream_url');
    Route::post('/lessons/{lesson}/progress', [LessonController::class, 'updateProgress'])->name('lessons.progress');
    Route::post('/lessons/{lesson}/complete', [LessonController::class, 'markCompleted'])->name('lessons.complete');
    
    // الدفع
    Route::get('/courses/{course}/payment', [PaymentController::class, 'showPaymentForm'])->name('payment.form');
    Route::post('/courses/{course}/payment', [PaymentController::class, 'processCoursePayment'])->name('payment.process');
    Route::get('/payment/history', [PaymentController::class, 'paymentHistory'])->name('payment.history');
    
    // الخدمات التعليمية
        // الخدمات التعليمية
    Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
    Route::get('/services/{slug}/create', [ServiceController::class, 'create'])->name('services.create');
    Route::post('/services/{slug}', [ServiceController::class, 'store'])->name('services.store');
    Route::get('/service-requests/{serviceRequest}', [ServiceController::class, 'show'])->name('services.show');
    Route::get('/my-service-requests', [UserController::class, 'myServiceRequests'])->name('my-service-requests');
    Route::post('/service-requests/{serviceRequest}/cancel', [ServiceController::class, 'cancel'])->name('services.cancel');
    Route::get('/service-files/{file}/download', [ServiceController::class, 'downloadFile'])->name('services.download-file');
    
    // الرسائل
    Route::post('/service-requests/{serviceRequest}/messages', [MessageController::class, 'store'])->name('messages.store');
    Route::post('/service-requests/{serviceRequest}/messages/read', [MessageController::class, 'markAsRead'])->name('messages.read');
    Route::get('/service-requests/{serviceRequest}/messages', [MessageController::class, 'getMessages'])->name('messages.get');
    Route::get('/message-files/{file}/download', [MessageController::class, 'downloadFile'])->name('messages.download-file');
});

// PayTabs Callback Routes (لا تحتاج middleware)
Route::post('/payment/callback', [PaymentController::class, 'paymentCallback'])->name('payment.callback');
Route::get('/payment/return', [PaymentController::class, 'paymentReturn'])->name('payment.return');

// Admin Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/statistics', [AdminController::class, 'statistics'])->name('statistics');
    
    // Service Management
    Route::get('/services', [AdminServiceController::class, 'index'])->name('services.index');
    Route::get('/services/{serviceRequest}', [AdminServiceController::class, 'show'])->name('services.show');
    Route::put('/services/{serviceRequest}/status', [AdminServiceController::class, 'updateStatus'])->name('services.status');
    Route::post('/services/{serviceRequest}/message', [AdminServiceController::class, 'sendMessage'])->name('services.message');
    Route::get('/services/export', [AdminServiceController::class, 'export'])->name('services.export');
    Route::get('/services/statistics', [AdminServiceController::class, 'statistics'])->name('services.statistics');
    Route::get('/services/{serviceRequest}/edit', [AdminServiceController::class, 'edit'])->name('services.edit');
    Route::put('/services/{serviceRequest}', [AdminServiceController::class, 'update'])->name('services.update');
    Route::delete('/services/{serviceRequest}', [AdminServiceController::class, 'destroy'])->name('services.destroy');
    Route::resource('service-types', AdminServiceController::class)->except(['show', 'create']);

    
    // Course Management
    // Video chunk upload (used by admin lessons upload UI)
    Route::post('/video/upload', [\App\Http\Controllers\Api\Admin\VideoUploadController::class, 'upload'])->name('admin.video.upload');
    // Admin direct-to-Wasabi presign endpoints (handled under teacher/public routes)

    // New Video System Routes (Admin)
    Route::get('/video/drive/proxy/{fileId}', [\App\Http\Controllers\GoogleDriveController::class, 'proxy'])->name('admin.video.drive.proxy');

    Route::resource('courses', AdminCourseController::class);
    Route::get('/courses/{course}/lessons', [AdminCourseController::class, 'lessons'])->name('courses.lessons');
    Route::post('/courses/{course}/lessons', [AdminCourseController::class, 'storeLesson'])->name('courses.lessons.store');
    Route::put('/courses/{course}/lessons/order', [AdminCourseController::class, 'updateLessonOrder'])->name('courses.lessons.order');
    Route::delete('/courses/{course}/lessons/{lesson}', [AdminCourseController::class, 'destroyLesson'])->name('courses.lessons.destroy');
    
    // Section Management
    Route::post('/courses/{course}/sections', [AdminCourseController::class, 'storeSection'])->name('courses.sections.store');
    Route::put('/courses/{course}/sections/{section}', [AdminCourseController::class, 'updateSection'])->name('courses.sections.update');
    Route::delete('/courses/{course}/sections/{section}', [AdminCourseController::class, 'destroySection'])->name('courses.sections.destroy');
    
    // User Management
    Route::resource('users', AdminUserController::class);
    Route::get('/users/{user}/purchases', [AdminUserController::class, 'purchases'])->name('users.purchases');
    Route::get('/users/{user}/service-requests', [AdminUserController::class, 'serviceRequests'])->name('users.service-requests');
    Route::get('/users/export', [AdminUserController::class, 'export'])->name('users.export');
    
    // University Management
    Route::resource('universities', AdminUniversityController::class);
    
    // Uni Course Management
    Route::resource('uni_courses', AdminUniCourseController::class);
    Route::get('/uni_courses/{uniCourse}/lessons', [AdminUniCourseController::class, 'lessons'])->name('uni_courses.lessons');
    Route::post('/uni_courses/{uniCourse}/lessons/order', [AdminUniCourseController::class, 'updateLessonOrder'])->name('uni_courses.update_lesson_order');
    Route::post('/uni_courses/{uniCourse}/lessons/add', [AdminUniCourseController::class, 'addLesson'])->name('uni_courses.add_lesson');
    Route::delete('/uni_courses/{uniCourse}/lessons/{mapping}', [AdminUniCourseController::class, 'removeLesson'])->name('uni_courses.remove_lesson');
    
});

// --- Routes from himmah1-main ---
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ... existing routes

Route::get('login/google', [GoogleAuthController::class, 'redirectToGoogle'])->name('login.google');
Route::get('login/google/callback', [GoogleAuthController::class, 'handleGoogleCallback']);




Route::middleware(['auth'])->group(function () {
    // مسارات الدردشة للطلاب والمعلمين
    Route::get('/courses/{course}/chat', [ChatController::class, 'show'])->name('chat.show');
    Route::post('/chat-rooms/{chatRoom}/messages', [ChatController::class, 'store'])->name('chat.messages.store');
    Route::get('/chat-rooms/{chatRoom}/messages/json', [ChatController::class, 'messagesJson'])->name('chat.messages.json');
    Route::post('/chat-messages/{message}/delete', [ChatController::class, 'deleteMessage'])->name('chat.messages.delete');

    // نظام البحث والفلترة المتقدم للدورات
    Route::get('/explore-courses', [\App\Http\Controllers\CourseController::class, 'index'])->name('courses.explore');
    Route::get('/api/subjects', [\App\Http\Controllers\CourseController::class, 'getSubjects'])->name('courses.subjects');

    // مسارات الإدارة لمراقبة الدردشات
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/chats', [AdminChatController::class, 'index'])->name('chat.index');
        Route::get('/chats/{chatRoom}', [AdminChatController::class, 'show'])->name('chat.show');
    });
});

// --- Routes from project_structure ---
// ... existing routes

Route::middleware(['auth', 'check.ai.quota'])->group(function () {
    
    // مسارات الطالب
    Route::post('/courses/{course}/ai-chat', [StudentAIController::class, 'chat'])->name('student.ai.chat');

    // مسارات المعلم
    Route::middleware(['teacher'])->prefix('teacher')->name('teacher.')->group(function () {
        Route::post('/lessons/{lesson}/generate-quiz', [TeacherAIController::class, 'generateQuiz'])->name('ai.generate-quiz');
        Route::post('/lessons/{lesson}/summarize', [TeacherAIController::class, 'summarizeLesson'])->name('ai.summarize');
    });

    // مسارات الإدارة
    Route::middleware(['admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/ai-reports', [AdminAIController::class, 'generatePlatformReport'])->name('ai.reports');
        Route::get('/ai-reports/download', [AdminAIController::class, 'downloadPlatformReport'])->name('ai.reports.download');
    });
});

// --- Routes from himmah1-main ---
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ... existing routes

Route::get('login/google', [GoogleAuthController::class, 'redirectToGoogle'])->name('login.google');
Route::get('login/google/callback', [GoogleAuthController::class, 'handleGoogleCallback']);




Route::middleware(['auth'])->group(function () {
    // مسارات الدردشة للطلاب والمعلمين
    Route::get('/courses/{course}/chat', [ChatController::class, 'show'])->name('chat.show');
    Route::post('/chat-rooms/{chatRoom}/messages', [ChatController::class, 'store'])->name('chat.messages.store');

    // مسارات الإدارة لمراقبة الدردشات
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/chats', [AdminChatController::class, 'index'])->name('chat.index');
        Route::get('/chats/{chatRoom}', [AdminChatController::class, 'show'])->name('chat.show');
    });
});

// --- Routes from full_advanced_filter_implementation_with_views ---
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ... existing routes

Route::get('login/google', [GoogleAuthController::class, 'redirectToGoogle'])->name('login.google');
Route::get('login/google/callback', [GoogleAuthController::class, 'handleGoogleCallback']);



 // افتراض وجود هذا الكونترولر




Route::middleware(['auth'])->group(function () {
    // مسارات الدردشة للطلاب والمعلمين
    Route::get('/courses/{course}/chat', [ChatController::class, 'show'])->name('chat.show');
    Route::post('/chat-rooms/{chatRoom}/messages', [ChatController::class, 'store'])->name('chat.messages.store');

    // نظام البحث والفلترة المتقدم للدورات
    Route::get('/explore-courses', [\App\Http\Controllers\CourseController::class, 'index'])->name('courses.explore');
    Route::get('/api/subjects', [\App\Http\Controllers\CourseController::class, 'getSubjects'])->name('courses.subjects');

    // مسارات الإدارة لمراقبة الدردشات
    // مسارات الإدارة (Admin Dashboard)
    Route::middleware(['admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
        Route::resource('categories', AdminCategoryController::class);
        Route::resource('subjects', AdminSubjectController::class);
        Route::resource('courses', AdminCourseController::class);
        
        // مسارات الإدارة لمراقبة الدردشات
        Route::get('/chats', [AdminChatController::class, 'index'])->name('chat.index');
        Route::get('/chats/{chatRoom}', [AdminChatController::class, 'show'])->name('chat.show');
    });

    // مسارات لوحة المعلم (Teacher Dashboard)
    Route::middleware(['teacher'])->prefix('teacher')->name('teacher.')->group(function () {
        Route::get('/dashboard', [TeacherCourseController::class, 'index'])->name('dashboard'); // use existing TeacherCourseController
        Route::resource('courses', \App\Http\Controllers\Teacher\TeacherCourseController::class)->except(['show']);
    });
});

// --- Routes from himmah1-main ---
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ... existing routes

Route::get('login/google', [GoogleAuthController::class, 'redirectToGoogle'])->name('login.google');
Route::get('login/google/callback', [GoogleAuthController::class, 'handleGoogleCallback']);

// --- Routes from himmah1-main ---
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ... existing routes

Route::get('login/google', [GoogleAuthController::class, 'redirectToGoogle'])->name('login.google');
Route::get('login/google/callback', [GoogleAuthController::class, 'handleGoogleCallback']);




Route::middleware(['auth'])->group(function () {
    // مسارات الدردشة للطلاب والمعلمين
    Route::get('/courses/{course}/chat', [ChatController::class, 'show'])->name('chat.show');
    Route::post('/chat-rooms/{chatRoom}/messages', [ChatController::class, 'store'])->name('chat.messages.store');

    // مسارات الإدارة لمراقبة الدردشات
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/chats', [AdminChatController::class, 'index'])->name('chat.index');
        Route::get('/chats/{chatRoom}', [AdminChatController::class, 'show'])->name('chat.show');
    });
});

// --- Routes from project_structure ---
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Zoom Webhook (يجب استثناؤه من CSRF في VerifyCsrfToken.php)
Route::post('/webhooks/zoom', [ZoomWebhookController::class, 'handle'])->name('webhooks.zoom');

Route::middleware(['auth'])->group(function () {
    
    // مسارات الإدارة (Admin Dashboard)
    Route::middleware(['admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
        Route::resource('categories', AdminCategoryController::class);
        Route::resource('subjects', AdminSubjectController::class);
        Route::resource('courses', AdminCourseController::class);
        
        // إدارة الحصص المباشرة
        Route::get('/live-sessions', [AdminLiveSessionController::class, 'index'])->name('live-sessions.index');
        Route::patch('/live-sessions/{liveSession}/status', [AdminLiveSessionController::class, 'updateStatus'])->name('live-sessions.update-status');
        Route::delete('/live-sessions/{liveSession}', [AdminLiveSessionController::class, 'destroy'])->name('live-sessions.destroy');
    });

    // مسارات لوحة المعلم (Teacher Dashboard)
    Route::middleware(['teacher'])->prefix('teacher')->name('teacher.')->group(function () {
        Route::resource('courses', TeacherCourseController::class);
        // Teacher-managed course sections (teachers can add/edit/delete sections)
        Route::post('/courses/{course}/sections', [\App\Http\Controllers\Teacher\TeacherCourseController::class, 'storeSection'])->name('courses.sections.store');
        Route::put('/courses/{course}/sections/{section}', [\App\Http\Controllers\Teacher\TeacherCourseController::class, 'updateSection'])->name('courses.sections.update');
        Route::delete('/courses/{course}/sections/{section}', [\App\Http\Controllers\Teacher\TeacherCourseController::class, 'destroySection'])->name('courses.sections.destroy');
        // Teacher lesson uploads
        Route::post('/courses/{course}/lessons', [\App\Http\Controllers\Teacher\TeacherLessonController::class, 'store'])->name('lessons.store');
        // Teacher lesson uploads
        // (presign endpoints are defined centrally under authenticated routes)
        
        // إدارة الحصص المباشرة للمعلم
        Route::resource('live-sessions', TeacherLiveSessionController::class);
    });

    // مسارات لوحة الطالب (Student Dashboard)
    Route::prefix('student')->name('student.')->group(function () {
        Route::get('/live-sessions', [StudentLiveSessionController::class, 'index'])->name('live-sessions.index');
        Route::get('/live-sessions/{liveSession}/join', [StudentLiveSessionController::class, 'join'])->name('live-sessions.join');
    });
});
// نظام الخدمات الجديد (الدردشة المباشرة مع الإدارة)
Route::middleware('auth')->group(function () {
    Route::get('/services', [App\Http\Controllers\ServiceController::class, 'index'])->name('services.index');
    Route::get('/services/chat', [App\Http\Controllers\ChatController::class, 'adminChat'])->name('services.chat');
});

// مسارات الدردشة المحدثة
Route::middleware(['auth'])->group(function () {
    Route::get('/chat/room/{room}', [App\Http\Controllers\ChatController::class, 'show'])->name('chat.show');
    Route::get('/chat/services', [App\Http\Controllers\ChatController::class, 'adminChat'])->name('chat.admin');
    Route::get('/chat/course/{course}', [App\Http\Controllers\ChatController::class, 'courseChat'])->name('chat.course');
});

// Dev-only Wasabi test route (available only when APP_DEBUG=true)
if (config('app.debug')) {
    Route::get('/dev/wasabi-test', [\App\Http\Controllers\Dev\WasabiTestController::class, 'index'])->name('dev.wasabi.test');
}

// Unified presign endpoints: authenticated users (teachers or admins) allowed
Route::middleware(['auth'])->group(function () {
    Route::post('/video/presign/initiate', [\App\Http\Controllers\Teacher\VideoPresignController::class, 'initiate'])
        ->name('video.presign.initiate');
    Route::post('/video/presign/complete', [\App\Http\Controllers\Teacher\VideoPresignController::class, 'complete'])
        ->name('video.presign.complete');
});
