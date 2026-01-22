<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\Option;
use App\Http\Requests\StoreQuestionRequest;
use App\Http\Requests\UpdateQuestionRequest;

class TeacherQuestionController extends Controller
{
    public function index(Quiz $quiz)
    {
        $this->authorize('view', $quiz);
        $questions = $quiz->questions()->with('options')->orderBy('order')->get();
        return view('teacher.quizzes.questions.index', compact('quiz', 'questions'));
    }

    public function create(Quiz $quiz)
    {
        $this->authorize('update', $quiz);
        return view('teacher.quizzes.questions.create', compact('quiz'));
    }

    public function store(StoreQuestionRequest $request, Quiz $quiz)
    {
        $validated = $request->validated();

        $question = $quiz->questions()->create([
            'question_text' => $validated['question_text'],
            'type' => $validated['type'],
            'points' => $validated['points'] ?? 1,
            'order' => $validated['order'] ?? 0,
        ]);

        if (!empty($validated['options']) && is_array($validated['options'])) {
            foreach ($validated['options'] as $opt) {
                $question->options()->create([
                    'option_text' => $opt['option_text'],
                    'is_correct' => !empty($opt['is_correct']),
                ]);
            }
        }

        return redirect()->route('teacher.quizzes.show', $quiz)->with('success', 'تم إضافة السؤال');
    }

    public function edit(Question $question)
    {
        $quiz = $question->quiz;
        $this->authorize('update', $quiz);
        $question->load('options');
        return view('teacher.quizzes.questions.edit', compact('quiz', 'question'));
    }

    public function update(UpdateQuestionRequest $request, Question $question)
    {
        $validated = $request->validated();

        $quiz = $question->quiz;

        $question->update([
            'question_text' => $validated['question_text'],
            'type' => $validated['type'],
            'points' => $validated['points'] ?? $question->points,
            'order' => $validated['order'] ?? $question->order,
        ]);

        // Sync options: create/update and delete removed ones
        $incoming = collect($validated['options'] ?? []);
        $existingIds = $question->options()->pluck('id')->toArray();
        $keepIds = [];

        foreach ($incoming as $opt) {
            if (!empty($opt['id']) && in_array($opt['id'], $existingIds)) {
                $o = Option::find($opt['id']);
                $o->update([
                    'option_text' => $opt['option_text'],
                    'is_correct' => !empty($opt['is_correct']),
                ]);
                $keepIds[] = $o->id;
            } else {
                $new = $question->options()->create([
                    'option_text' => $opt['option_text'],
                    'is_correct' => !empty($opt['is_correct']),
                ]);
                $keepIds[] = $new->id;
            }
        }

        // delete removed options
        $toDelete = array_diff($existingIds, $keepIds);
        if (!empty($toDelete)) {
            Option::whereIn('id', $toDelete)->delete();
        }

        return redirect()->route('teacher.quizzes.show', $quiz)->with('success', 'تم تحديث السؤال');
    }

    public function destroy(Question $question)
    {
        $quiz = $question->quiz;
        $this->authorize('update', $quiz);
        $question->delete();
        return redirect()->route('teacher.quizzes.show', $quiz)->with('success', 'تم حذف السؤال');
    }
}
