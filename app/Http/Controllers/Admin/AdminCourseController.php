<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lesson;
use App\Services\VimeoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AdminCourseController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    /**
     * Display a listing of courses.
     */
    public function index()
    {
        $courses = Course::withCount(['lessons', 'purchases' => function ($query) {
                $query->where('payment_status', 'completed');
            }])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.courses.index', compact('courses'));
    }

    /**
     * Show the form for creating a new course.
     */
    public function create()
    {
        return view('admin.courses.create');
    }

    /**
     * Store a newly created course.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'instructor_name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:active,inactive',
            'duration' => 'nullable|integer|min:0',
            'course_size' => 'required|in:normal,large',
            'includes_summary' => 'boolean',
            'includes_tajmeeat' => 'boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $courseData = $request->only([
            'title', 'description', 'price', 'instructor_name', 'status', 'duration',
            'course_size'
        ]);

        // Handle checkbox fields
        $courseData['includes_summary'] = $request->has('includes_summary');
        $courseData['includes_tajmeeat'] = $request->has('includes_tajmeeat');

        // Handle image upload
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('courses', 'public');
            $courseData['image'] = $imagePath;
        }

        $course = Course::create($courseData);

        return redirect()->route('admin.courses.show', $course)
            ->with('success', 'تم إنشاء الدورة بنجاح!');
    }

    /**
     * Display the specified course.
     */
    public function show(Course $course)
    {
        $course->load(['lessons' => function ($query) {
            $query->orderBy('order');
        }]);

        $stats = [
            'total_students' => $course->studentsCount(),
            'total_revenue' => $course->purchases()
                ->where('payment_status', 'completed')
                ->sum('amount'),
            'average_rating' => $course->averageRating(),
            'total_reviews' => $course->reviews()->count(),
        ];

        return view('admin.courses.show', compact('course', 'stats'));
    }

    /**
     * Show the form for editing the specified course.
     */
    public function edit(Course $course)
    {
        return view('admin.courses.edit', compact('course'));
    }

    /**
     * Update the specified course.
     */
    public function update(Request $request, Course $course)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'instructor_name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:active,inactive',
            'duration' => 'nullable|integer|min:0',
            'course_size' => 'required|in:normal,large',
            'includes_summary' => 'boolean',
            'includes_tajmeeat' => 'boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $courseData = $request->only([
            'title', 'description', 'price', 'instructor_name', 'status', 'duration',
            'course_size'
        ]);

        // Handle checkbox fields
        $courseData['includes_summary'] = $request->has('includes_summary');
        $courseData['includes_tajmeeat'] = $request->has('includes_tajmeeat');

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($course->image && Storage::disk('public')->exists($course->image)) {
                Storage::disk('public')->delete($course->image);
            }
            
            $imagePath = $request->file('image')->store('courses', 'public');
            $courseData['image'] = $imagePath;
        }

        $course->update($courseData);

        return redirect()->route('admin.courses.show', $course)
            ->with('success', 'تم تحديث الدورة بنجاح!');
    }

    /**
     * Remove the specified course.
     */
    public function destroy(Course $course)
    {
        // Check if course has any purchases
        if ($course->purchases()->where('payment_status', 'completed')->exists()) {
            return back()->with('error', 'لا يمكن حذف الدورة لأنها تحتوي على مشتريات.');
        }

        // Delete course image
        if ($course->image && Storage::disk('public')->exists($course->image)) {
            Storage::disk('public')->delete($course->image);
        }

        // Delete course lessons and their files
        foreach ($course->lessons as $lesson) {
            // Delete lesson files if any
            // Note: You might want to implement file cleanup for lessons
        }

        $course->delete();

        return redirect()->route('admin.courses.index')
            ->with('success', 'تم حذف الدورة بنجاح!');
    }

    /**
     * Show lessons for a course.
     */
    public function lessons(Course $course)
    {
        $course->load(['sections.lessons' => function($query) {
            $query->orderBy('order');
        }]);
        
        // Get lessons without sections (legacy lessons)
        $lessonsWithoutSection = $course->lessons()->whereNull('section_id')->orderBy('order')->get();
        
        return view('admin.courses.lessons', compact('course', 'lessonsWithoutSection'));
    }

    /**
     * Store a new lesson for a course.
     */
    public function storeLesson(Request $request, Course $course)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'video_input_type' => 'required|in:url,vimeo_id,upload',
            'video_url' => 'required_if:video_input_type,url|nullable|url',
            'vimeo_video_id' => 'required_if:video_input_type,vimeo_id|nullable|string',
            'video_file' => 'required_if:video_input_type,upload|nullable|file|mimes:mp4,avi,mov,wmv|max:102400', // 100MB max
            'duration' => 'nullable|integer|min:0',
            'section_id' => 'nullable|exists:sections,id',
            'is_free' => 'boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $vimeoVideoId = null;
        $videoPlatform = null;
        $videoUrl = null;
        $duration = $request->duration;

        // Handle different video input types
        switch ($request->video_input_type) {
            case 'url':
                $videoUrl = $request->video_url;
                break;
                
            case 'vimeo_id':
                $vimeoVideoId = $request->vimeo_video_id;
                $videoPlatform = 'vimeo';
                
                // Get video details from Vimeo to validate and get duration
                $vimeoService = new VimeoService();
                $videoDetails = $vimeoService->getVideoDetails($vimeoVideoId);
                
                if (!$videoDetails['success']) {
                    return back()->withErrors(['vimeo_video_id' => 'معرف الفيديو غير صحيح أو الفيديو غير موجود على Vimeo.'])->withInput();
                }
                
                // Use Vimeo duration if not provided
                if (!$duration && isset($videoDetails['duration'])) {
                    $duration = intval($videoDetails['duration'] / 60); // Convert seconds to minutes
                }
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
                    
                    // Get video duration from Vimeo after upload
                    if (!$duration) {
                        $videoDetails = $vimeoService->getVideoDetails($vimeoVideoId);
                        if ($videoDetails['success'] && isset($videoDetails['duration'])) {
                            $duration = intval($videoDetails['duration'] / 60); // Convert seconds to minutes
                        }
                    }
                }
                break;
        }

        // Get max order for the section or course
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

        // Update course total lessons count
        $course->update(['total_lessons' => $course->lessons()->count()]);

        return back()->with('success', 'تم إضافة الدرس بنجاح!');
    }

    /**
     * Update lesson order.
     */
    public function updateLessonOrder(Request $request, Course $course)
    {
        $lessonIds = $request->input('lesson_ids', []);
        
        foreach ($lessonIds as $index => $lessonId) {
            Lesson::where('id', $lessonId)
                ->where('course_id', $course->id)
                ->update(['order' => $index + 1]);
        }

        return response()->json(['success' => true]);
     }
    
    /**
     * Store a new section for a course.
     */
    public function storeSection(Request $request, Course $course)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $course->sections()->create([
            'title' => $request->title,
        ]);

        return back()->with('success', 'تم إضافة القسم بنجاح!');
    }
    
    /**
     * Update a section.
     */
    public function updateSection(Request $request, Course $course, $sectionId)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $section = $course->sections()->findOrFail($sectionId);
        $section->update([
            'title' => $request->title,
        ]);

        return back()->with('success', 'تم تحديث القسم بنجاح!');
    }
    
    /**
     * Delete a section.
     */
    public function destroySection(Course $course, $sectionId)
    {
        $section = $course->sections()->findOrFail($sectionId);
        
        // Move lessons to no section (set section_id to null)
        $section->lessons()->update(['section_id' => null]);
        
        $section->delete();

        return back()->with('success', 'تم حذف القسم بنجاح! تم نقل الدروس إلى قائمة الدروس بدون قسم.');
    }
    
    /**
     * Delete a lesson.
     */
    public function destroyLesson(Course $course, Lesson $lesson)
    {
        if ($lesson->course_id !== $course->id) {
            abort(404);
        }
        $lesson->delete();
        // Update course total lessons count
        $course->update(['total_lessons' => $course->lessons()->count()]);
        return back()->with('success', 'تم حذف الدرس بنجاح!');
    }
}

