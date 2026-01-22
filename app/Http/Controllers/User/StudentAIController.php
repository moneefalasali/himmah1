<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Services\AIService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentAIController extends Controller
{
    protected $aiService;

    public function __construct(AIService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * عرض واجهة مساعد الطالب داخل الكورس (صفحة واجهة الدردشة)
     */
    public function showAssistant(Course $course)
    {
        $user = Auth::user();

        // تأكد أن المستخدم مشترك في الدورة
        if (! $user->isSubscribedTo($course)) {
            return redirect()->route('courses.show', $course)->with('error', 'يجب أن تكون مشتركًا في الدورة للوصول إلى المساعد الذكي.');
        }

        return view('student.ai.assistant', compact('course'));
    }

    /**
     * مساعد الطالب داخل الكورس
     */
    public function chat(Request $request, Course $course)
    {
        $request->validate([
            'message' => 'required|string|max:500',
        ]);

        // 1. التحقق من الاشتراك
        if (!Auth::user()->isSubscribedTo($course)) {
            return response()->json(['error' => 'يجب الاشتراك في الكورس لاستخدام المساعد الذكي.'], 403);
        }

        // 2. بناء السياق (Context) من محتوى الكورس
        $courseContent = $this->getCourseContext($course);

        $systemMessage = "أنت مساعد تعليمي خبير لكورس بعنوان: '{$course->title}'. 
        مهمتك هي شرح الدروس وتبسيط المفاهيم بناءً على محتوى الكورس فقط.
        محتوى الكورس المتوفر: {$courseContent}
        قواعد هامة:
        1. لا تجب على أسئلة خارج نطاق هذا الكورس.
        2. إذا سألك الطالب عن إجابات كويز أو اختبار، ارفض بلباقة وشجعه على التفكير.
        3. استخدم لغة عربية سهلة ومبسطة.";

        $response = $this->aiService->ask(
            $request->message, 
            $systemMessage, 
            Auth::id(), 
            ['feature' => 'student_chat', 'course_id' => $course->id]
        );

        return response()->json(['answer' => $response]);
    }

    /**
     * تجميع محتوى الكورس (عناوين الدروس ووصفها) لتزويد الـ AI بالسياق
     */
    protected function getCourseContext(Course $course)
    {
        $lessons = $course->lessons()->select('title', 'description')->get();
        $context = "وصف الكورس: {$course->description}\n";
        $context .= "عناوين الدروس: " . $lessons->pluck('title')->implode(', ');
        
        return mb_substr($context, 0, 2000); // تحديد حجم السياق لتوفير التوكنات
    }
}
