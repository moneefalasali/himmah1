<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Services\AIService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminCourseAIController extends Controller
{
    protected $aiService;

    public function __construct(AIService $aiService)
    {
        $this->aiService = $aiService;
    }

    // Show assistant UI for a specific course
    public function show(Course $course)
    {
        return view('admin.ai.assistant', compact('course'));
    }

    // Summarize provided text/content
    public function summarize(Request $request, Course $course)
    {
        $request->validate([ 'content' => 'required|string' ]);

        $content = $request->input('content');
        $system = "أنت مساعد تلخيص عربي متخصص في المحتوى التعليمي. قدم ملخصًا موجزًا ومنظمًا باللغة العربية مع نقاط رئيسية (3-6 نقاط) وتوصيات قصيرة للمعلم.";

        try {
            $answer = $this->aiService->ask($content, $system, Auth::id(), ['feature' => 'course_summarize', 'course_id' => $course->id]);
            return response()->json(['ok' => true, 'result' => $answer]);
        } catch (\Throwable $e) {
            return response()->json(['ok' => false, 'error' => $e->getMessage()], 500);
        }
    }

    // Generate multiple-choice questions from provided content
    public function generateQuestions(Request $request, Course $course)
    {
        $request->validate([
            'content' => 'required|string',
            'count' => 'nullable|integer|min:1|max:20'
        ]);

        $content = $request->input('content');
        $count = $request->input('count', 5);

        $system = "أنت مولد أسئلة اختيارات متعددة باللغة العربية. استخرج من النص محتوى صالحًا لأسئلة امتحانية: لكل سؤال قدم سؤالًا واحدًا، 4 خيارات (A-D)، وحدد الإجابة الصحيحة، وأعطِ مستوى صعوبة (سهل/متوسط/صعب). أعد النتيجة بصيغة JSON قابلة للتحليل.";

        $userPrompt = "أنشئ {$count} سؤالًا بصيغة JSON عن المحتوى التالي:\n\n" . $content . "\n\n
المخرجات يجب أن تكون JSON بالمفتاح 'questions' كمصفوفة، وكل عنصر يحتوي على: 'question', 'options' (object A-D), 'answer' (مثل 'A'), 'difficulty'.";

        try {
            $answer = $this->aiService->ask($userPrompt, $system, Auth::id(), ['feature' => 'generate_questions', 'course_id' => $course->id]);
            return response()->json(['ok' => true, 'result' => $answer]);
        } catch (\Throwable $e) {
            return response()->json(['ok' => false, 'error' => $e->getMessage()], 500);
        }
    }
}
