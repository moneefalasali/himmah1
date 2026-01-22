<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Services\AIService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeacherAIController extends Controller
{
    protected $aiService;

    public function __construct(AIService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * توليد أسئلة كويز بناءً على محتوى الدرس
     */
    public function generateQuiz(Request $request, Lesson $lesson)
    {
        $this->authorize('update', $lesson->course);

        $systemMessage = "أنت خبير تربوي. قم بتوليد 5 أسئلة اختيار من متعدد (MCQ) بناءً على محتوى الدرس التالي. 
        يجب أن يكون لكل سؤال 4 خيارات مع تحديد الإجابة الصحيحة. 
        نسق المخرجات كـ JSON.";

        $prompt = "محتوى الدرس: {$lesson->content}";

        $response = $this->aiService->ask(
            $prompt, 
            $systemMessage, 
            Auth::id(), 
            ['feature' => 'teacher_quiz', 'course_id' => $lesson->course_id]
        );

        return response()->json(['quiz' => json_decode($response)]);
    }

    /**
     * تلخيص الدرس
     */
    public function summarizeLesson(Request $request, Lesson $lesson)
    {
        $this->authorize('update', $lesson->course);

        $systemMessage = "قم بتلخيص الدرس التالي في نقاط رئيسية واضحة وشاملة.";
        
        $response = $this->aiService->ask(
            $lesson->content, 
            $systemMessage, 
            Auth::id(), 
            ['feature' => 'teacher_summary', 'course_id' => $lesson->course_id]
        );

        return response()->json(['summary' => $response]);
    }

    // Show assistant UI for a course (teacher full access)
    public function showAssistant(
        \Illuminate\Http\Request $request,
        \App\Models\Course $course
    ) {
        $this->authorize('update', $course);
        return view('teacher.ai.assistant', compact('course'));
    }

    // Summarize arbitrary content for a course (teacher)
    public function summarizeCourse(Request $request, \App\Models\Course $course)
    {
        $this->authorize('update', $course);
        $request->validate(['content' => 'required|string']);
        $system = "أنت مساعد تلخيص عربي متخصص في المحتوى التعليمي. قدم ملخصًا موجزًا ومنظمًا مع نقاط رئيسية (3-6 نقاط).";
        $answer = $this->aiService->ask($request->input('content'), $system, Auth::id(), ['feature' => 'teacher_course_summarize', 'course_id' => $course->id]);
        return response()->json(['ok' => true, 'result' => $answer]);
    }

    // Generate questions for a course (teacher)
    public function generateQuestions(Request $request, \App\Models\Course $course)
    {
        $this->authorize('update', $course);
        $request->validate(['content' => 'required|string', 'count' => 'nullable|integer|min:1|max:20']);
        $count = $request->input('count', 5);
        $system = "أنت مولد أسئلة اختيارات متعددة باللغة العربية. أنت تصوغ أسئلة من محتوى الكورس.";
        $prompt = "انشئ " . $count . " سؤالًا بصيغة JSON من المحتوى التالي:\n\n" . $request->input('content');
        $answer = $this->aiService->ask($prompt, $system, Auth::id(), ['feature' => 'teacher_course_generate_questions', 'course_id' => $course->id]);
        return response()->json(['ok' => true, 'result' => $answer]);
    }
}
