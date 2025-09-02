<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\UniCourse;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
    /**
     * Display a listing of courses.
     */
    public function index()
    {
        $user = Auth::user();
        
        // If user is logged in and has a university, show university-specific courses
        if ($user && $user->university_id) {
            $uniCourses = UniCourse::where('university_id', $user->university_id)
                ->with(['course' => function ($query) {
                    $query->where('status', 'active')
                        ->withCount(['purchases' => function ($q) {
                            $q->where('payment_status', 'completed');
                        }])
                        ->with(['reviews']);
                }])
                ->get()
                ->filter(function ($uniCourse) {
                    return $uniCourse->course && $uniCourse->course->isActive();
                });
                
            return view('courses.index', compact('uniCourses', 'user'));
        }
        
        // Default behavior for users without university or guests
        $courses = Course::where('status', 'active')
            ->withCount(['purchases' => function ($query) {
                $query->where('payment_status', 'completed');
            }])
            ->with(['reviews'])
            ->paginate(12);

        return view('courses.index', compact('courses'));
    }

    /**
     * Display the specified course.
     */
    public function show(Course $course)
    {
        if (!$course->isActive()) {
            abort(404);
        }

        $course->load(['lessons', 'freeLessons', 'reviews.user']);
        
        $user = Auth::user();
        $hasPurchased = $user ? $user->hasPurchased($course->id) : false;
        
        $averageRating = $course->averageRating();
        $studentsCount = $course->studentsCount();
        
        // Get user's review if exists
        $userReview = null;
        if ($user) {
            $userReview = Review::where('user_id', $user->id)
                ->where('course_id', $course->id)
                ->first();
        }

        // Get free lessons for preview
        $freeLessons = $course->lessons()->where('is_free', true)->orderBy('order')->get();

        return view('courses.show', compact(
            'course',
            'hasPurchased',
            'averageRating',
            'studentsCount',
            'userReview',
            'freeLessons'
        ));
    }

    /**
     * Show course curriculum.
     */
    public function curriculum(Course $course)
    {
        $user = Auth::user();

        if (!$user || !$user->hasPurchased($course->id)) {
            return redirect()->route('courses.show', $course)
                ->with('error', 'يجب شراء الدورة للوصول إلى المنهج.');
        }

        // Check if user has university and get university-specific course
        if ($user->university_id) {
            $uniCourse = UniCourse::where('university_id', $user->university_id)
                ->where('course_id', $course->id)
                ->first();
                
            if ($uniCourse) {
                // Load lessons in university-specific order
                $lessons = $uniCourse->lessons()->get();
                
                // Add progress for each lesson
                foreach ($lessons as $lesson) {
                    $lesson->progress = $lesson->progressForUser($user->id);
                }
                
                return view('courses.curriculum', compact('course', 'lessons', 'uniCourse'));
            }
        }

        // Default behavior - load sections and lessons
        $course->load(['sections.lessons']);

        // Add user progress for each lesson
        foreach ($course->sections as $section) {
            foreach ($section->lessons as $lesson) {
                $lesson->progress = $lesson->progressForUser($user->id);
            }
        }

        return view('courses.curriculum', compact('course'));
    }


    /**
     * Store a review for the course.
     */
    public function storeReview(Request $request, Course $course)
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        if (!$user->hasPurchased($course->id)) {
            return back()->with('error', 'يجب شراء الدورة أولاً لتتمكن من تقييمها.');
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        Review::updateOrCreate(
            [
                'user_id' => $user->id,
                'course_id' => $course->id,
            ],
            [
                'rating' => $request->rating,
                'comment' => $request->comment,
            ]
        );

        return back()->with('success', 'تم إضافة تقييمك بنجاح!');
    }

    /**
     * Search courses.
     */
    public function search(Request $request)
    {
        $query = $request->get('q');
        
        $courses = Course::where('status', 'active')
            ->where(function ($queryBuilder) use ($query) {
                $queryBuilder->where('title', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%")
                    ->orWhere('instructor_name', 'like', "%{$query}%");
            })
            ->withCount(['purchases' => function ($queryBuilder) {
                $queryBuilder->where('payment_status', 'completed');
            }])
            ->with(['reviews'])
            ->paginate(12);

        return view('courses.search', compact('courses', 'query'));
    }
    
}

