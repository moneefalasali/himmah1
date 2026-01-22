<?php
namespace App\Http\Controllers;
use App\Models\Course;
use App\Models\Category;
use App\Models\Subject;
use App\Models\University;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Request;
class CourseController extends Controller
{
    public function index(Request $request)
    {
        $query = Course::query()->where('status', 'active');
        if ($request->filled('classification')) {
            if ($request->classification === 'university') {
                if (Schema::hasColumn('courses', 'university_id')) {
                    $query->whereNotNull('university_id');
                    if ($request->filled('university_id')) {
                        $query->where('university_id', $request->university_id);
                    }
                }
                if ($request->filled('subject_id') && Schema::hasColumn('courses', 'subject_id')) {
                    $query->where('subject_id', $request->subject_id);
                }
            } else {
                if (Schema::hasColumn('courses', 'university_id')) {
                    $query->whereNull('university_id');
                }
            }
        }
        if ($request->filled('type') && Schema::hasColumn('courses', 'type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        // Category filter
        if ($request->filled('category_id') && Schema::hasColumn('courses', 'category_id')) {
            $query->where('category_id', $request->category_id);
        }
        $courses = $query->with(['university', 'subject', 'teacher'])->latest()->paginate(12);
        $universities = University::all();
        $categories = Category::all();

        if ($request->ajax()) {
            return view('courses._course_list', compact('courses'))->render();
        }

        return view('courses.index', compact('courses', 'universities', 'categories'));
    }
    public function getUniversitySubjects(University $university)
    {
        return response()->json($university->subjects);
    }
    public function show(Course $course)
    {
        $course->load(['university', 'subject', 'teacher', 'sections.lessons', 'reviews']);

        // compute stats: total students (completed purchases + manual enrollments), revenue, instructor profit, avg rating
        try {
            $purchaseIds = \DB::table('purchases')
                ->where('course_id', $course->id)
                ->where('payment_status', 'completed')
                ->pluck('user_id')
                ->toArray();

            $enrolledIds = \DB::table('course_user')
                ->where('course_id', $course->id)
                ->pluck('user_id')
                ->toArray();

            $uniqueStudents = array_unique(array_merge($purchaseIds, $enrolledIds));
            $totalStudents = count($uniqueStudents);

            $totalRevenue = \DB::table('sales')->where('course_id', $course->id)->sum('amount');
            $instructorProfit = \DB::table('sales')->where('course_id', $course->id)->sum('teacher_commission');

            $averageRating = $course->reviews()->avg('rating') ?? 0;
        } catch (\Throwable $e) {
            $totalStudents = 0;
            $totalRevenue = 0;
            $instructorProfit = 0;
            $averageRating = 0;
        }

        $stats = [
            'total_students' => $totalStudents,
            'total_revenue' => $totalRevenue,
            'instructor_profit' => $instructorProfit,
            'average_rating' => $averageRating,
        ];

        return view('courses.show', compact('course', 'stats'));
    }

    /**
     * Show course curriculum (sections and lessons) to enrolled users.
     */
    public function curriculum(Course $course)
    {
        // Load sections, lessons, teacher and published quizzes with questions count
        $course->load([
            'sections.lessons',
            'teacher',
            'quizzes' => function($q) {
                $q->where('status', 'published')->withCount('questions')->orderBy('created_at', 'desc');
            }
        ]);

        return view('courses.curriculum', compact('course'));
    }
}
