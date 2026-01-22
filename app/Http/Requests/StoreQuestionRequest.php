<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreQuestionRequest extends FormRequest
{
    public function authorize()
    {
        $user = $this->user();
        if (!$this->route('quiz')) return false;
        $quiz = $this->route('quiz');
        if ($user->isAdmin()) return true;
        return $user->id === $quiz->course->user_id;
    }

    public function rules()
    {
        return [
            'question_text' => 'required|string',
            'type' => 'required|in:multiple_choice,true_false,short_answer',
            'points' => 'nullable|integer|min:0',
            'order' => 'nullable|integer',
            'options' => 'required_if:type,multiple_choice|array',
            'options.*.option_text' => 'required_with:options|string',
            'options.*.is_correct' => 'nullable|boolean',
        ];
    }
}
