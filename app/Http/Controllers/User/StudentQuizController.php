<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\QuizResult;
use App\Models\QuizAnswer;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class StudentQuizController extends Controller
{
    public function show(Quiz $quiz)
    {
        $this->authorize('take', $quiz);
        
        // التحقق إذا كان الطالب قد حل الاختبار مسبقاً
        $existingResult = QuizResult::where('quiz_id', $quiz->id)
            ->where('user_id', Auth::id())
            ->first();

        return view('user.quizzes.show', compact('quiz', 'existingResult'));
    }

    public function start(Quiz $quiz)
    {
        $this->authorize('take', $quiz);
        
        $questions = $quiz->questions()->with('options')->get();
        return view('user.quizzes.take', compact('quiz', 'questions'));
    }

    public function submit(Request $request, Quiz $quiz)
    {
        $this->authorize('take', $quiz);
        
        $quiz->load('questions.options');
        $answers = $request->input('answers', []);
        $totalPoints = $quiz->questions->sum('points');
        $earnedPoints = 0;

        $result = QuizResult::create([
            'quiz_id' => $quiz->id,
            'user_id' => Auth::id(),
            'total_points' => $totalPoints,
            'earned_points' => 0, // سيتم تحديثه
            'percentage' => 0,
            'completed_at' => Carbon::now(),
        ]);

        foreach ($quiz->questions as $question) {
            $studentAnswer = $answers[$question->id] ?? null;
            $isCorrect = false;
            $optionId = null;
            $answerText = null;

            if ($question->type === 'multiple_choice' || $question->type === 'true_false') {
                $optionId = $studentAnswer;
                $correctOption = $question->options()->where('is_correct', true)->first();
                if ($correctOption && $correctOption->id == $optionId) {
                    $isCorrect = true;
                    $earnedPoints += $question->points;
                }
            } else {
                // Short Answer - يحتاج مراجعة يدوية أو مطابقة نصية بسيطة
                $answerText = $studentAnswer;
                // هنا يمكن إضافة منطق مطابقة نصية إذا كانت الإجابة محددة مسبقاً
            }

            QuizAnswer::create([
                'quiz_result_id' => $result->id,
                'question_id' => $question->id,
                'option_id' => $optionId,
                'answer_text' => $answerText,
                'is_correct' => $isCorrect,
            ]);
        }

        $percentage = ($totalPoints > 0) ? ($earnedPoints / $totalPoints) * 100 : 0;
        $result->update([
            'earned_points' => $earnedPoints,
            'percentage' => $percentage,
        ]);

        return redirect()->route('student.quizzes.result', $result->id);
    }

    public function result(QuizResult $result)
    {
        if ($result->user_id !== Auth::id()) {
            abort(403);
        }

        $result->load('quiz', 'answers.question.options');
        return view('user.quizzes.result_details', compact('result'));
    }
}
