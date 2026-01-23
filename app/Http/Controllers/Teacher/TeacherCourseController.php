<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\University;
use App\Models\Category;
use App\Models\Subject;
use Illuminate\Support\Str;
use App\Services\ImageService;
use Illuminate\Support\Facades\Auth;

if (! function_exists('SchemaHasColumn')) {
    function SchemaHasColumn($table, $column)
    {
        try {
            return \Schema::hasColumn($table, $column);
        } catch (\Throwable $e) {
            return false;
        }
    }
}

class TeacherCourseController extends Controller
{
    protected $imageService;

    public function __construct(?ImageService $imageService)
    {
        $this->imageService = $imageService;
        // enforce policy-based authorization on resource routes where possible
        try {
            $this->authorizeResource(Course::class, 'course');
        } catch (\Throwable $e) {
            // ignore if authorizeResource is not available in this context
        }

        // Ensure teachers cannot access other teachers' course resources
        $this->middleware(function ($request, $next) {
            $course = $request->route('course');
            if ($course instanceof Course) {
                $user = Auth::user();
                if (! method_exists($user, 'isAdmin') || ! $user->isAdmin()) {
                    if ($course->user_id !== $user->id) {
                        abort(403);
                    }
                }
            }
            return $next($request);
        })->only(['show','edit','update','destroy','togglePublish','lessons.store','lessons.update','lessons.destroy']);
    }

    protected function authorizeResourcePolicies()
    {
        // Apply policy-based authorization for resource routes (show, edit, update, delete)
        try {
            $this->authorizeResource(Course::class, 'course');
        } catch (\Throwable $e) {
            // ignore if policies/methods not available at boot time
        }
    }

    public function booted()
    {
        // placeholder in case framework calls booted — keep for compatibility
    }

    public function index(Request $request)
    {
        $user = Auth::user();

        $query = Course::where('user_id', $user->id);

        if ($request->filled('q')) {
            $q = $request->get('q');
            $query->where(function($qf) use ($q) {
                $qf->where('title', 'like', "%$q%")
                   ->orWhere('description', 'like', "%$q%");
            });
        }

        if ($request->filled('status')) {
            $status = $request->get('status');
            if ($status === 'published') {
                if (SchemaHasColumn('courses', 'is_published')) {
                    $query->where('is_published', 1);
                } else {
                    $query->where('status', 'published');
                }
            } elseif ($status === 'draft') {
                if (SchemaHasColumn('courses', 'is_published')) {
                    $query->where('is_published', 0);
                } else {
                    $query->where('status', '!=', 'published');
                }
            }
        }

        $courses = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        // Ensure each course has accurate counts: lessons and unified students count (purchases + manual enroll)
        foreach ($courses as $c) {
            // lessons count
            try {
                $c->lessons_count = $c->lessons()->count();
            } catch (\Throwable $e) {
                $c->lessons_count = 0;
            }

            // unified students count (uses Course::studentsCount which merges purchases and course_user)
            try {
                $c->students_count = $c->studentsCount();
            } catch (\Throwable $e) {
                $c->students_count = 0;
            }
        }

        return view('teacher.courses.index', compact('courses'));
    }

    public function create()
    {
        $universities = University::all();
        $categories = Category::all();
        $subjects = Subject::all();
        return view('teacher.courses.create', compact('universities', 'categories', 'subjects'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'classification' => 'nullable|in:university,general',
            'thumbnail' => 'nullable|image|max:2048',
            'type' => 'nullable|in:recorded,online',
            'category_id' => 'nullable|exists:categories,id',
            'subject_id' => 'nullable|exists:subjects,id',
            'subject_name' => 'nullable|string|max:255',
        ]);

        $data = $request->all();
        $data['user_id'] = Auth::id();

        if ($request->filled('subject_name')) {
            $subjectName = $request->get('subject_name');

            // Prefer an explicitly provided category_id; fall back to first category or create a default one.
            $categoryId = $request->get('category_id');
            if (!$categoryId) {
                $firstCategory = Category::first();
                if ($firstCategory) {
                    $categoryId = $firstCategory->id;
                } else {
                    $default = Category::firstOrCreate([
                        'name' => 'عام',
                        'slug' => Str::slug('عام')
                    ]);
                    $categoryId = $default->id;
                }
            }

            $subject = Subject::firstOrCreate(
                ['name' => $subjectName],
                ['category_id' => $categoryId, 'slug' => Str::slug($subjectName)]
            );

            $data['subject_id'] = $subject->id;
        }

        if ($request->hasFile('thumbnail') && $this->imageService) {
            $data['thumbnail'] = $this->imageService->uploadImage($request->file('thumbnail'), 'courses');
        }

        Course::create($data);

        return redirect()->route('teacher.courses.index')->with('success', 'تم إنشاء الكورس بنجاح');
    }

    public function edit(Course $course)
    {
        $this->authorize('update', $course);
        $universities = University::all();
        $categories = Category::all();
        $subjects = Subject::all();
        if ($this->imageService && $course->thumbnail) {
            $course->thumbnail_url = $this->imageService->getUrl($course->thumbnail);
        }
        return view('teacher.courses.edit', compact('course', 'universities', 'categories', 'subjects'));
    }

    public function update(Request $request, Course $course)
    {
        $this->authorize('update', $course);
        
        $request->validate([
            'title' => 'required|string|max:255',
            'thumbnail' => 'nullable|image|max:2048',
            'category_id' => 'nullable|exists:categories,id',
            'subject_id' => 'nullable|exists:subjects,id',
            'subject_name' => 'nullable|string|max:255',
        ]);

        $data = $request->all();

        if ($request->filled('subject_name')) {
            $subjectName = $request->get('subject_name');

            $categoryId = $request->get('category_id');
            if (!$categoryId) {
                $firstCategory = Category::first();
                if ($firstCategory) {
                    $categoryId = $firstCategory->id;
                } else {
                    $default = Category::firstOrCreate([
                        'name' => 'عام',
                        'slug' => Str::slug('عام')
                    ]);
                    $categoryId = $default->id;
                }
            }

            $subject = Subject::firstOrCreate(
                ['name' => $subjectName],
                ['category_id' => $categoryId, 'slug' => Str::slug($subjectName)]
            );

            $data['subject_id'] = $subject->id;
        }

        if ($request->hasFile('thumbnail') && $this->imageService) {
            if ($course->thumbnail) {
                $this->imageService->deleteImage($course->thumbnail);
            }
            $data['thumbnail'] = $this->imageService->uploadImage($request->file('thumbnail'), 'courses');
        }

        $course->update($data);

        return redirect()->route('teacher.courses.index')->with('success', 'تم تحديث الكورس بنجاح');
    }

    public function togglePublish(Request $request, Course $course)
    {
        $this->authorize('update', $course);

        if (SchemaHasColumn('courses', 'is_published')) {
            $course->is_published = !$course->is_published;
        } else {
            $course->status = ($course->status === 'published') ? 'draft' : 'published';
        }
        $course->save();

        return back()->with('status', 'Course status updated.');
    }

    /**
     * Store a new section for the teacher's course
     */
    public function storeSection(Request $request, Course $course)
    {
        $this->authorize('update', $course);

        $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $course->sections()->create(['title' => $request->title]);
        return back()->with('success', 'تم إضافة القسم');
    }

    /**
     * Update a section
     */
    public function updateSection(Request $request, Course $course, \App\Models\Section $section)
    {
        $this->authorize('update', $course);
        $request->validate(['title' => 'required|string|max:255']);
        $section->update(['title' => $request->title]);
        return back()->with('success', 'تم تعديل القسم');
    }

    /**
     * Delete a section
     */
    public function destroySection(Course $course, \App\Models\Section $section)
    {
        $this->authorize('update', $course);
        \App\Models\Lesson::where('section_id', $section->id)->update(['section_id' => null]);
        $section->delete();
        return back()->with('success', 'تم حذف القسم');
    }
}
