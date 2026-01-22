<?php
namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeacherQuizController extends Controller
{
    public function index()
    {
        $quizzes = Quiz::whereHas('course', function($query) {
            $query->where('user_id', Auth::id());
        })->with('course')->latest()->paginate(10);

        return view('teacher.quizzes.index', compact('quizzes'));
    }

    public function create()
    {
        $courses = Course::where('user_id', Auth::id())->get();
        return view('teacher.quizzes.create', compact('courses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration_minutes' => 'nullable|integer|min:1',
            'status' => 'required|in:draft,published',
        ]);

        $course = Course::findOrFail($validated['course_id']);
        if ($course->user_id !== Auth::id()) {
            abort(403);
        }

        Quiz::create($validated);

        return redirect()->route('teacher.quizzes.index')->with('success', 'تم إنشاء الاختبار بنجاح');
    }

    public function show(Quiz $quiz)
    {
        $this->authorize('view', $quiz);
        $quiz->load('questions.options');
        return view('teacher.quizzes.show', compact('quiz'));
    }

    public function edit(Quiz $quiz)
    {
        $this->authorize('update', $quiz);
        $courses = Course::where('user_id', Auth::id())->get();
        return view('teacher.quizzes.edit', compact('quiz', 'courses'));
    }

    public function update(Request $request, Quiz $quiz)
    {
        $this->authorize('update', $quiz);

        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration_minutes' => 'nullable|integer|min:1',
            'status' => 'required|in:draft,published',
        ]);

        $course = Course::findOrFail($validated['course_id']);
        if ($course->user_id !== Auth::id()) abort(403);

        $quiz->update($validated);

        return redirect()->route('teacher.quizzes.index')->with('success', 'تم تحديث الاختبار بنجاح');
    }

    public function destroy(Quiz $quiz)
    {
        $this->authorize('delete', $quiz);
        $quiz->delete();
        return redirect()->route('teacher.quizzes.index')->with('success', 'تم حذف الاختبار');
    }
}
