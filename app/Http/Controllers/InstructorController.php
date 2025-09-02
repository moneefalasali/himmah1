<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\Section;
use App\Models\Purchase;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Services\VimeoService;

class InstructorController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'instructor']);
    }

    /**
     * عرض لوحة تحكم المعلم
     */
    public function dashboard()
    {
        $instructor = Auth::user();
        
        // إحصائيات الدورات
        $totalCourses = Course::where('instructor_name', $instructor->name)->count();
        $activeCourses = Course::where('instructor_name', $instructor->name)
                              ->where('status', 'active')
                              ->count();
        
        // إحصائيات الدروس
        $totalLessons = Lesson::whereHas('course', function($query) use ($instructor) {
            $query->where('instructor_name', $instructor->name);
        })->count();
        
        // إحصائيات المبيعات والأرباح
        $purchases = Purchase::whereHas('course', function($query) use ($instructor) {
            $query->where('instructor_name', $instructor->name);
        })->where('payment_status', 'completed');
        
        $totalRevenue = $purchases->sum('amount');
        $instructorProfit = $totalRevenue * 0.4; // 40% للمعلم
        $totalStudents = $purchases->count();
        
        // إحصائيات الاختبارات
        $totalQuizzes = Quiz::whereHas('course', function($query) use ($instructor) {
            $query->where('instructor_name', $instructor->name);
        })->count();
        
        // الدورات الأخيرة
        $recentCourses = Course::where('instructor_name', $instructor->name)
                              ->orderBy('created_at', 'desc')
                              ->take(5)
                              ->get();
        
        // المبيعات الأخيرة
        $recentSales = Purchase::whereHas('course', function($query) use ($instructor) {
            $query->where('instructor_name', $instructor->name);
        })->where('payment_status', 'completed')
          ->with(['course', 'user'])
          ->orderBy('created_at', 'desc')
          ->take(10)
          ->get();

        return view('instructor.dashboard', compact(
            'totalCourses', 'activeCourses', 'totalLessons', 
            'totalRevenue', 'instructorProfit', 'totalStudents',
            'totalQuizzes', 'recentCourses', 'recentSales'
        ));
    }

    /**
     * عرض قائمة دورات المعلم
     */
    public function courses()
    {
        $instructor = Auth::user();
        $courses = Course::where('instructor_name', $instructor->name)
                        ->withCount(['lessons', 'purchases' => function($query) {
                            $query->where('payment_status', 'completed');
                        }])
                        ->orderBy('created_at', 'desc')
                        ->paginate(15);

        return view('instructor.courses.index', compact('courses'));
    }

    /**
     * عرض نموذج إنشاء دورة جديدة
     */
    public function createCourse()
    {
        return view('instructor.courses.create');
    }

    /**
     * حفظ دورة جديدة
     */
    public function storeCourse(Request $request)
    {
        $validator = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:active,inactive',
            'duration' => 'nullable|integer|min:0',
            'course_size' => 'required|in:normal,large',
            'includes_summary' => 'boolean',
            'includes_tajmeeat' => 'boolean',
        ]);

        $courseData = $request->only([
            'title', 'description', 'price', 'status', 'duration',
            'course_size'
        ]);

        // إضافة اسم المعلم
        $courseData['instructor_name'] = Auth::user()->name;
        
        // معالجة الحقول checkbox
        $courseData['includes_summary'] = $request->has('includes_summary');
        $courseData['includes_tajmeeat'] = $request->has('includes_tajmeeat');

        // معالجة الصورة
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('courses', 'public');
            $courseData['image'] = $imagePath;
        }

        $course = Course::create($courseData);

        return redirect()->route('instructor.courses.show', $course)
            ->with('success', 'تم إنشاء الدورة بنجاح!');
    }

    /**
     * عرض دورة معينة
     */
    public function showCourse(Course $course)
    {
        // التحقق من أن المعلم يملك هذه الدورة
        if ($course->instructor_name !== Auth::user()->name) {
            abort(403);
        }

        $course->load(['sections.lessons' => function($query) {
            $query->orderBy('order');
        }]);
        
        $lessonsWithoutSection = $course->lessons()->whereNull('section_id')->orderBy('order')->get();
        
        // إحصائيات الدورة
        $stats = [
            'total_students' => $course->studentsCount(),
            'total_revenue' => $course->purchases()
                ->where('payment_status', 'completed')
                ->sum('amount'),
            'instructor_profit' => $course->purchases()
                ->where('payment_status', 'completed')
                ->sum('amount') * 0.4,
            'average_rating' => $course->averageRating(),
            'total_reviews' => $course->reviews()->count(),
        ];

        return view('instructor.courses.show', compact('course', 'lessonsWithoutSection', 'stats'));
    }

    /**
     * عرض نموذج تعديل الدورة
     */
    public function editCourse(Course $course)
    {
        if ($course->instructor_name !== Auth::user()->name) {
            abort(403);
        }

        return view('instructor.courses.edit', compact('course'));
    }

    /**
     * تحديث الدورة
     */
    public function updateCourse(Request $request, Course $course)
    {
        if ($course->instructor_name !== Auth::user()->name) {
            abort(403);
        }

        $validator = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:active,inactive',
            'duration' => 'nullable|integer|min:0',
            'course_size' => 'required|in:normal,large',
            'includes_summary' => 'boolean',
            'includes_tajmeeat' => 'boolean',
        ]);

        $courseData = $request->only([
            'title', 'description', 'price', 'status', 'duration',
            'course_size'
        ]);

        // معالجة الحقول checkbox
        $courseData['includes_summary'] = $request->has('includes_summary');
        $courseData['includes_tajmeeat'] = $request->has('includes_tajmeeat');

        // معالجة الصورة
        if ($request->hasFile('image')) {
            // حذف الصورة القديمة
            if ($course->image && Storage::disk('public')->exists($course->image)) {
                Storage::disk('public')->delete($course->image);
            }
            
            $imagePath = $request->file('image')->store('courses', 'public');
            $courseData['image'] = $imagePath;
        }

        $course->update($courseData);

        return redirect()->route('instructor.courses.show', $course)
            ->with('success', 'تم تحديث الدورة بنجاح!');
    }

    /**
     * عرض إدارة الدروس للدورة
     */
    public function lessons(Course $course)
    {
        if ($course->instructor_name !== Auth::user()->name) {
            abort(403);
        }

        $course->load(['sections.lessons' => function($query) {
            $query->orderBy('order');
        }]);
        
        $lessonsWithoutSection = $course->lessons()->whereNull('section_id')->orderBy('order')->get();
        
        return view('instructor.courses.lessons', compact('course', 'lessonsWithoutSection'));
    }

    /**
     * حفظ درس جديد
     */
    public function storeLesson(Request $request, Course $course)
    {
        if ($course->instructor_name !== Auth::user()->name) {
            abort(403);
        }

        $validator = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'video_input_type' => 'required|in:url,vimeo_id,upload',
            'video_url' => 'required_if:video_input_type,url|nullable|url',
            'vimeo_video_id' => 'required_if:video_input_type,vimeo_id|nullable|string',
            'video_file' => 'required_if:video_input_type,upload|nullable|file|mimes:mp4,avi,mov,wmv|max:102400',
            'duration' => 'nullable|integer|min:0',
            'section_id' => 'nullable|exists:sections,id',
            'is_free' => 'boolean',
        ]);

        $vimeoVideoId = null;
        $videoPlatform = null;
        $videoUrl = null;
        $duration = $request->duration;

        // معالجة أنواع الفيديو المختلفة
        switch ($request->video_input_type) {
            case 'url':
                $videoUrl = $request->video_url;
                break;
                
            case 'vimeo_id':
                $vimeoVideoId = $request->vimeo_video_id;
                $videoPlatform = 'vimeo';
                break;
                
            case 'upload':
                if ($request->hasFile('video_file')) {
                    $vimeoService = new VimeoService();
                    $uploadResult = $vimeoService->uploadVideo(
                        $request->file('video_file')->getPathname(),
                        $request->title,
                        $request->description ?? ''
                    );
                    
                    if (!$uploadResult['success']) {
                        return back()->withErrors(['video_file' => 'فشل في رفع الفيديو إلى Vimeo: ' . $uploadResult['error']])->withInput();
                    }
                    
                    $vimeoVideoId = $uploadResult['video_id'];
                    $videoPlatform = 'vimeo';
                }
                break;
        }

        // الحصول على الترتيب الأقصى للقسم أو الدورة
        if ($request->section_id) {
            $maxOrder = Lesson::where('section_id', $request->section_id)->max('order') ?? 0;
        } else {
            $maxOrder = $course->lessons()->whereNull('section_id')->max('order') ?? 0;
        }

        Lesson::create([
            'course_id' => $course->id,
            'section_id' => $request->section_id,
            'title' => $request->title,
            'description' => $request->description,
            'video_url' => $videoUrl,
            'vimeo_video_id' => $vimeoVideoId,
            'video_platform' => $videoPlatform,
            'duration' => $duration,
            'order' => $maxOrder + 1,
            'is_free' => $request->boolean('is_free'),
        ]);

        // تحديث عدد الدروس في الدورة
        $course->update(['total_lessons' => $course->lessons()->count()]);

        return back()->with('success', 'تم إضافة الدرس بنجاح!');
    }

    /**
     * عرض إدارة الاختبارات للدورة
     */
    public function quizzes(Course $course)
    {
        if ($course->instructor_name !== Auth::user()->name) {
            abort(403);
        }

        $quizzes = $course->quizzes()->withCount('questions')->get();
        
        return view('instructor.courses.quizzes', compact('course', 'quizzes'));
    }

    /**
     * عرض نموذج إنشاء اختبار جديد
     */
    public function createQuiz(Course $course)
    {
        if ($course->instructor_name !== Auth::user()->name) {
            abort(403);
        }

        return view('instructor.quizzes.create', compact('course'));
    }

    /**
     * حفظ اختبار جديد
     */
    public function storeQuiz(Request $request, Course $course)
    {
        if ($course->instructor_name !== Auth::user()->name) {
            abort(403);
        }

        $validator = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'time_limit' => 'nullable|integer|min:1',
            'passing_score' => 'required|integer|min:1|max:100',
            'is_active' => 'boolean',
        ]);

        $quiz = $course->quizzes()->create([
            'title' => $request->title,
            'description' => $request->description,
            'time_limit' => $request->time_limit,
            'passing_score' => $request->passing_score,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('instructor.quizzes.edit', [$course, $quiz])
            ->with('success', 'تم إنشاء الاختبار بنجاح! يمكنك الآن إضافة الأسئلة.');
    }

    /**
     * عرض نموذج تعديل الاختبار
     */
    public function editQuiz(Course $course, Quiz $quiz)
    {
        if ($course->instructor_name !== Auth::user()->name) {
            abort(403);
        }

        $quiz->load('questions');
        
        return view('instructor.quizzes.edit', compact('course', 'quiz'));
    }

    /**
     * تحديث الاختبار
     */
    public function updateQuiz(Request $request, Course $course, Quiz $quiz)
    {
        if ($course->instructor_name !== Auth::user()->name) {
            abort(403);
        }

        $validator = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'time_limit' => 'nullable|integer|min:1',
            'passing_score' => 'required|integer|min:1|max:100',
            'is_active' => 'boolean',
        ]);

        $quiz->update([
            'title' => $request->title,
            'description' => $request->description,
            'time_limit' => $request->time_limit,
            'passing_score' => $request->passing_score,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return back()->with('success', 'تم تحديث الاختبار بنجاح!');
    }

    /**
     * عرض تقارير الأرباح
     */
    public function earnings()
    {
        $instructor = Auth::user();
        
        // إحصائيات الأرباح الشهرية
        $monthlyEarnings = Purchase::whereHas('course', function($query) use ($instructor) {
            $query->where('instructor_name', $instructor->name);
        })->where('payment_status', 'completed')
          ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, SUM(amount) as total')
          ->groupBy('month')
          ->orderBy('month', 'desc')
          ->get()
          ->map(function($item) {
              $item->instructor_profit = $item->total * 0.4;
              return $item;
          });

        // إحصائيات الأرباح حسب الدورة
        $courseEarnings = Purchase::whereHas('course', function($query) use ($instructor) {
            $query->where('instructor_name', $instructor->name);
        })->where('payment_status', 'completed')
          ->with('course')
          ->selectRaw('course_id, SUM(amount) as total, COUNT(*) as sales_count')
          ->groupBy('course_id')
          ->orderBy('total', 'desc')
          ->get()
          ->map(function($item) {
              $item->instructor_profit = $item->total * 0.4;
              return $item;
          });

        // إجمالي الأرباح
        $totalRevenue = Purchase::whereHas('course', function($query) use ($instructor) {
            $query->where('instructor_name', $instructor->name);
        })->where('payment_status', 'completed')->sum('amount');
        
        $totalInstructorProfit = $totalRevenue * 0.4;

        return view('instructor.earnings', compact(
            'monthlyEarnings', 'courseEarnings', 'totalRevenue', 'totalInstructorProfit'
        ));
    }

    /**
     * عرض قائمة الطلاب
     */
    public function students()
    {
        $instructor = Auth::user();
        
        $students = Purchase::whereHas('course', function($query) use ($instructor) {
            $query->where('instructor_name', $instructor->name);
        })->where('payment_status', 'completed')
          ->with(['user', 'course'])
          ->select('user_id', 'course_id')
          ->distinct()
          ->get()
          ->groupBy('user_id')
          ->map(function($purchases) {
              return [
                  'user' => $purchases->first()->user,
                  'courses' => $purchases->pluck('course'),
                  'total_spent' => $purchases->sum('amount'),
                  'instructor_profit' => $purchases->sum('amount') * 0.4,
              ];
          });

        return view('instructor.students', compact('students'));
    }
} 