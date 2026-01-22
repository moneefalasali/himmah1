<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Quiz;
use App\Models\QuizResult;

class TeacherQuizResultController extends Controller
{
    public function index(Quiz $quiz)
    {
        // only teacher who owns the course can view
        if (!Auth::user()->isAdmin() && Auth::id() !== $quiz->course->user_id) {
            abort(403);
        }

        $results = $quiz->results()->with('user')->latest()->paginate(20);
        return view('teacher.quizzes.results.index', compact('quiz', 'results'));
    }

    public function show(QuizResult $result)
    {
        $quiz = $result->quiz;
        if (!Auth::user()->isAdmin() && Auth::id() !== $quiz->course->user_id) {
            abort(403);
        }

        $result->load('answers.question.options', 'user');
        return view('teacher.quizzes.results.show', compact('result'));
    }
}
