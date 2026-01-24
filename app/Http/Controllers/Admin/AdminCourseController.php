<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Category;
use App\Services\ImageService;
use App\Services\VideoService;
use Illuminate\Http\Request;
use App\Models\Purchase;
use Illuminate\Support\Facades\DB;

class AdminCourseController extends Controller
{
    protected $imageService;
    protected $videoService;

    public function __construct(ImageService $imageService, VideoService $videoService)
    {
        $this->imageService = $imageService;
        $this->videoService = $videoService;
    }

    public function index()
    {
        $courses = Course::with(['teacher', 'university'])->latest()->paginate(20);
        foreach ($courses as $course) {
            $course->thumbnail_url = $this->imageService->getUrl($course->thumbnail);
        }
        return view('admin.courses.index', compact('courses'));
    }

    /**
     * Show create form for a new course
     */
    public function create()
    {
        $categories = Category::all();
        $subjects = \App\Models\Subject::all();
        $universities = \App\Models\University::all();
        return view('admin.courses.create', compact('categories', 'subjects', 'universities'));
    }

    /**
     * Store a newly created course
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'nullable|numeric',
            'instructor_name' => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
            'category_id' => 'nullable|exists:categories,id',
            'subject_id' => 'nullable|exists:subjects,id',
            'subject_name' => 'nullable|string|max:255',
            'university_id' => 'nullable|exists:universities,id',
            'duration' => 'nullable|integer',
            'image' => 'nullable|image|max:5120',
        ]);

        $data = $request->only(['title', 'description', 'price', 'instructor_name', 'status', 'duration', 'category_id', 'university_id']);

        // Determine subject_id: prefer explicit subject_id, otherwise create/find by name under category
        if ($request->filled('subject_id')) {
            $data['subject_id'] = $request->subject_id;
        } elseif ($request->filled('subject_name')) {
            $subjectName = trim($request->subject_name);
            $subject = \App\Models\Subject::firstOrCreate(
                ['name' => $subjectName],
                ['category_id' => $request->category_id ?? null, 'slug' => \Illuminate\Support\Str::slug($subjectName)]
            );
            $data['subject_id'] = $subject->id;
        }

        if ($request->hasFile('image')) {
            $path = $this->imageService->uploadImage($request->file('image'), 'courses');
            if ($path) {
                $data['image'] = $path;
            }
        }

        Course::create($data);

        return redirect()->route('admin.courses.index')->with('success', 'تم إضافة الدورة بنجاح');
    }

    /**
     * إضافة درس جديد مع فيديو عبر النظام الجديد
     */
    public function storeLesson(Request $request, Course $course)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'section_id' => 'nullable|exists:sections,id',
            'description' => 'nullable|string',
            'duration' => 'nullable|integer',
            'is_free' => 'nullable|boolean',
            'video_platform' => 'required|in:wasabi,vimeo,google_drive',
            'google_drive_url' => 'required_if:video_platform,google_drive|nullable|string',
            'vimeo_video_id' => 'required_if:video_platform,vimeo|nullable|string',
            'video_file' => 'nullable|file',
            'video_path' => 'nullable|string',
        ]);

        $lessonData = [
            'title' => $request->title,
            'section_id' => $request->section_id ?: null,
            'description' => $request->description ?: null,
            'duration' => $request->duration ?: null,
            'video_url' => '',
            'video_path' => null,
            'video_platform' => null,
            'is_free' => $request->has('is_free') ? (bool)$request->is_free : false,
        ];

        // Create lesson record first
        $lesson = $course->lessons()->create($lessonData);

        // Handle video source
        if ($request->video_platform === 'google_drive') {
            $lesson->video_platform = 'external';
            $lesson->video_url = $request->google_drive_url;
            $lesson->save();
        } elseif ($request->video_platform === 'vimeo') {
            $lesson->video_platform = 'vimeo';
            $lesson->vimeo_video_id = $request->vimeo_video_id;
            $lesson->save();
        } elseif ($request->video_platform === 'wasabi') {
            // Support either a direct uploaded file or a previously assembled+uploaded path from chunk uploader
            $lesson->video_platform = 'wasabi';
            $lesson->processing_status = 'processing';
            $lesson->save();

            if ($request->filled('video_path')) {
                // Frontend chunk uploader returned a Wasabi path
                $lesson->video_url = $request->video_path;
                $lesson->save();
                // Optionally dispatch processing job for HLS or other processing
                // dispatch(new \App\Jobs\ProcessVideoHLS($lesson));
            } elseif ($request->hasFile('video_file')) {
                // Upload raw file to Wasabi via VideoService
                $file = $request->file('video_file');
                $localPath = $file->getRealPath();
                try {
                    $remotePath = $this->videoService->uploadRawToWasabi($localPath, $lesson->id);
                    $lesson->video_url = $remotePath;
                    $lesson->save();
                    // Optionally dispatch processing job
                    // dispatch(new \App\Jobs\ProcessVideoHLS($lesson));
                } catch (\Exception $e) {
                    \Log::error('Video upload failed: ' . $e->getMessage());
                    return back()->withErrors(['video_file' => 'فشل رفع الفيديو.'])->withInput();
                }
            } else {
                return back()->withErrors(['video_file' => 'الرجاء اختيار ملف فيديو أو استخدام واجهة رفع القطع.'])->withInput();
            }
        }

        return back()->with('success', 'تم إضافة الدرس بنجاح');
    }

    /**
     * Show lessons and sections for a course
     */
    public function lessons(Course $course)
    {
        $course->load(['sections.lessons']);
        $lessonsWithoutSection = \App\Models\Lesson::where('course_id', $course->id)
            ->whereNull('section_id')
            ->orderBy('order')
            ->get();

        return view('admin.courses.lessons', compact('course', 'lessonsWithoutSection'));
    }

    /**
     * Store a new section for the course
     */
    public function storeSection(Request $request, Course $course)
    {
        $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $course->sections()->create(['title' => $request->title]);
        return back()->with('success', 'تم إضافة القسم');
    }

    /**
     * Update an existing section
     */
    public function updateSection(Request $request, Course $course, \App\Models\Section $section)
    {
        $request->validate(['title' => 'required|string|max:255']);
        $section->update(['title' => $request->title]);
        return back()->with('success', 'تم تعديل القسم');
    }

    /**
     * Delete a section
     */
    public function destroySection(Course $course, \App\Models\Section $section)
    {
        // Move lessons to no section before deleting
        \App\Models\Lesson::where('section_id', $section->id)->update(['section_id' => null]);
        $section->delete();
        return back()->with('success', 'تم حذف القسم');
    }

    /**
     * Delete a lesson
     */
    public function destroyLesson(Course $course, \App\Models\Lesson $lesson)
    {
        $lesson->delete();
        return back()->with('success', 'تم حذف الدرس');
    }

    public function destroy(Course $course)
    {
        if ($course->thumbnail) {
            $this->imageService->deleteImage($course->thumbnail);
        }
        $course->delete();
        return back()->with('success', 'تم حذف الكورس بنجاح');
    }

    /**
     * Display course details for admin
     */
    public function show(Course $course)
    {
        $course->load(['lessons', 'reviews.user']);

        // students: union of completed purchases and manual enrollments
        $purchased = Purchase::where('course_id', $course->id)
            ->where('payment_status', 'completed')
            ->pluck('user_id')
            ->toArray();

        $manual = DB::table('course_user')->where('course_id', $course->id)->pluck('user_id')->toArray();

        $uniqueStudents = collect(array_merge($purchased, $manual))->unique()->filter()->count();

        $totalRevenue = Purchase::where('course_id', $course->id)->where('payment_status', 'completed')->sum('amount');
        $averageRating = $course->reviews()->avg('rating') ?? 0;
        $totalReviews = $course->reviews()->count();

        $stats = [
            'total_students' => $uniqueStudents,
            'total_revenue' => $totalRevenue,
            'average_rating' => round($averageRating, 1),
            'total_reviews' => $totalReviews,
        ];

        return view('admin.courses.show', compact('course', 'stats'));
    }

    /**
     * Show edit form for a course
     */
    public function edit(Course $course)
    {
        $categories = Category::all();
        $subjects = \App\Models\Subject::all();
        $universities = \App\Models\University::all();

        if ($course->image && $this->imageService) {
            $course->image_url = $this->imageService->getUrl($course->image);
        }

        return view('admin.courses.edit', compact('course', 'categories', 'subjects', 'universities'));
    }

    /**
     * Update an existing course
     */
    public function update(Request $request, Course $course)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'nullable|numeric',
            'instructor_name' => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
            'category_id' => 'nullable|exists:categories,id',
            'subject_id' => 'nullable|exists:subjects,id',
            'subject_name' => 'nullable|string|max:255',
            'university_id' => 'nullable|exists:universities,id',
            'duration' => 'nullable|integer',
            'image' => 'nullable|image|max:5120',
        ]);

        $data = $request->only(['title', 'description', 'price', 'instructor_name', 'status', 'duration', 'category_id', 'university_id']);

        if ($request->filled('subject_id')) {
            $data['subject_id'] = $request->subject_id;
        } elseif ($request->filled('subject_name')) {
            $subjectName = trim($request->subject_name);
            $subject = \App\Models\Subject::firstOrCreate(
                ['name' => $subjectName],
                ['category_id' => $request->category_id ?? null, 'slug' => \Illuminate\Support\Str::slug($subjectName)]
            );
            $data['subject_id'] = $subject->id;
        }

        if ($request->hasFile('image')) {
            if ($course->image && $this->imageService) {
                $this->imageService->deleteImage($course->image);
            }
            $path = $this->imageService->uploadImage($request->file('image'), 'courses');
            if ($path) {
                $data['image'] = $path;
            }
        }

        $course->update($data);

        return redirect()->route('admin.courses.index')->with('success', 'تم تحديث الدورة بنجاح');
    }
}
