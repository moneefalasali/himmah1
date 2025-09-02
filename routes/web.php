
<?php


use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\ForgotPasswordController;

// استعادة كلمة المرور
Route::get('/password/forgot', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
// إعادة تعيين كلمة المرور
Route::get('/password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AdminCourseController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminServiceController;
use App\Http\Controllers\Admin\AdminUniversityController;
use App\Http\Controllers\Admin\AdminUniCourseController;

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
    Route::post('/lessons/{lesson}/progress', [LessonController::class, 'updateProgress'])->name('lessons.progress');
    Route::post('/lessons/{lesson}/complete', [LessonController::class, 'markCompleted'])->name('lessons.complete');
    
    // الدفع
    Route::get('/courses/{course}/payment', [PaymentController::class, 'showPaymentForm'])->name('payment.form');
    Route::post('/courses/{course}/payment', [PaymentController::class, 'processCoursePayment'])->name('payment.process');
    Route::get('/payment/history', [PaymentController::class, 'paymentHistory'])->name('payment.history');
    
    // الخدمات التعليمية
    Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
    Route::get('/services/{serviceTypeName}/create', [ServiceController::class, 'create'])->name('services.create');
    Route::post('/services/{serviceTypeName}', [ServiceController::class, 'store'])->name('services.store');
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



