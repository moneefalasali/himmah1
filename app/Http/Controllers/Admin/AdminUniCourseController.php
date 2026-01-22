<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\University;
use App\Models\Course;
use App\Models\UniCourse;
use App\Models\Lesson;
use App\Models\CourseLessonMapping;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class AdminUniCourseController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    /**
     * Display a listing of uni courses.
     */
    public function index()
    {
        $uniCourses = UniCourse::with(['university', 'course'])
            ->orderBy('university_id')
            ->orderBy('course_id')
            ->paginate(15);
        return view('admin.uni_courses.index', compact('uniCourses'));
    }

    /**
     * Show the form for creating a new uni course.
     */
    public function create()
    {
        $universities = University::orderBy('name')->get();
        $courses = Course::where('status', 'active')->orderBy('title')->get();
        return view('admin.uni_courses.create', compact('universities', 'courses'));
    }

    /**
     * Store a newly created uni course in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'university_id' => 'required|exists:universities,id',
            'course_id' => 'required|exists:courses,id',
            'custom_name' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Check if this combination already exists
        $exists = UniCourse::where('university_id', $request->university_id)
            ->where('course_id', $request->course_id)
            ->exists();

        if ($exists) {
            return back()->withErrors(['course_id' => 'هذا المقرر مضاف بالفعل لهذه الجامعة'])
                ->withInput();
        }

        DB::transaction(function () use ($request) {
            // Create uni course
            $uniCourse = UniCourse::create([
                'university_id' => $request->university_id,
                'course_id' => $request->course_id,
                'custom_name' => $request->custom_name,
            ]);

            // Map all lessons from the original course with their original order
            $lessons = Lesson::where('course_id', $request->course_id)
                ->orderBy('order')
                ->get();

            foreach ($lessons as $lesson) {
                CourseLessonMapping::create([
                    'uni_course_id' => $uniCourse->id,
                    'lesson_id' => $lesson->id,
                    'order' => $lesson->order,
                ]);
            }
        });

        return redirect()->route('admin.uni_courses.index')
            ->with('success', 'تم إنشاء مقرر الجامعة بنجاح');
    }

    /**
     * Display the specified uni course.
     */
    public function show(UniCourse $uniCourse)
    {
        $uniCourse->load(['university', 'course', 'lessons']);
        return view('admin.uni_courses.show', compact('uniCourse'));
    }

    /**
     * Show the form for editing the specified uni course.
     */
    public function edit(UniCourse $uniCourse)
    {
        $universities = University::orderBy('name')->get();
        $courses = Course::where('status', 'active')->orderBy('title')->get();
        return view('admin.uni_courses.edit', compact('uniCourse', 'universities', 'courses'));
    }

    /**
     * Update the specified uni course in storage.
     */
    public function update(Request $request, UniCourse $uniCourse)
    {
        $validator = Validator::make($request->all(), [
            'university_id' => 'required|exists:universities,id',
            'course_id' => 'required|exists:courses,id',
            'custom_name' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Check if this combination already exists (excluding current record)
        $exists = UniCourse::where('university_id', $request->university_id)
            ->where('course_id', $request->course_id)
            ->where('id', '!=', $uniCourse->id)
            ->exists();

        if ($exists) {
            return back()->withErrors(['course_id' => 'هذا المقرر مضاف بالفعل لهذه الجامعة'])
                ->withInput();
        }

        $uniCourse->update([
            'university_id' => $request->university_id,
            'course_id' => $request->course_id,
            'custom_name' => $request->custom_name,
        ]);

        return redirect()->route('admin.uni_courses.index')
            ->with('success', 'تم تحديث مقرر الجامعة بنجاح');
    }

    /**
     * Remove the specified uni course from storage.
     */
    public function destroy(UniCourse $uniCourse)
    {
        $uniCourse->delete();

        return redirect()->route('admin.uni_courses.index')
            ->with('success', 'تم حذف مقرر الجامعة بنجاح');
    }

    /**
     * Show lesson mappings for a uni course.
     */
    public function lessons(UniCourse $uniCourse)
    {
        $uniCourse->load(['university', 'course']);
        $mappings = $uniCourse->courseLessonMappings()
            ->with('lesson')
            ->orderBy('order')
            ->get();
        
        // Get available lessons that are not yet mapped
        $mappedLessonIds = $mappings->pluck('lesson_id')->toArray();
        $availableLessons = Lesson::where('course_id', $uniCourse->course_id)
            ->whereNotIn('id', $mappedLessonIds)
            ->orderBy('order')
            ->get();

        return view('admin.uni_courses.lessons', compact('uniCourse', 'mappings', 'availableLessons'));
    }

    /**
     * Update lesson mappings order.
     */
    public function updateLessonOrder(Request $request, UniCourse $uniCourse)
    {
        $validator = Validator::make($request->all(), [
            'mappings' => 'required|array',
            'mappings.*.id' => 'required|exists:course_lesson_mappings,id',
            'mappings.*.order' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()]);
        }

        DB::transaction(function () use ($request, $uniCourse) {
            foreach ($request->mappings as $mapping) {
                CourseLessonMapping::where('id', $mapping['id'])
                    ->where('uni_course_id', $uniCourse->id)
                    ->update(['order' => $mapping['order']]);
            }
        });

        return response()->json(['success' => true, 'message' => 'تم تحديث ترتيب الدروس بنجاح']);
    }

    /**
     * Add lesson to uni course.
     */
    public function addLesson(Request $request, UniCourse $uniCourse)
    {
        $validator = Validator::make($request->all(), [
            'lesson_id' => 'required|exists:lessons,id',
            'order' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Check if lesson belongs to the course
        $lesson = Lesson::where('id', $request->lesson_id)
            ->where('course_id', $uniCourse->course_id)
            ->first();

        if (!$lesson) {
            return back()->withErrors(['lesson_id' => 'الدرس غير موجود في هذا المقرر']);
        }

        // Check if mapping already exists
        $exists = CourseLessonMapping::where('uni_course_id', $uniCourse->id)
            ->where('lesson_id', $request->lesson_id)
            ->exists();

        if ($exists) {
            return back()->withErrors(['lesson_id' => 'الدرس مضاف بالفعل']);
        }

        CourseLessonMapping::create([
            'uni_course_id' => $uniCourse->id,
            'lesson_id' => $request->lesson_id,
            'order' => $request->order,
        ]);

        return back()->with('success', 'تم إضافة الدرس بنجاح');
    }

    /**
     * Remove lesson from uni course.
     */
    public function removeLesson(UniCourse $uniCourse, CourseLessonMapping $mapping)
    {
        if ($mapping->uni_course_id !== $uniCourse->id) {
            return back()->with('error', 'غير مسموح');
        }

        $mapping->delete();

        return back()->with('success', 'تم حذف الدرس بنجاح');
    }
}

