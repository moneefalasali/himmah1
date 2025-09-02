<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_id',
        'question_text',
        'question_type',
        'options',
        'correct_answer',
        'points',
        'order',
        'explanation',
    ];

    protected $casts = [
        'options' => 'array',
        'correct_answer' => 'array',
        'points' => 'integer',
        'order' => 'integer',
    ];

    /**
     * Get the quiz that owns the question.
     */
    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    /**
     * Get the question type label.
     */
    public function getQuestionTypeLabelAttribute()
    {
        return [
            'multiple_choice' => 'اختيار متعدد',
            'true_false' => 'صح أو خطأ',
            'fill_blank' => 'ملء الفراغ',
            'essay' => 'مقالي',
        ][$this->question_type] ?? $this->question_type;
    }

    /**
     * Check if answer is correct.
     */
    public function isCorrectAnswer($userAnswer)
    {
        if ($this->question_type === 'multiple_choice') {
            return in_array($userAnswer, $this->correct_answer);
        }
        
        return $userAnswer === $this->correct_answer;
    }
} 