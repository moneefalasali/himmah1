<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class QuizAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_result_id',
        'question_id',
        'answer_text',
        'option_id',
        'is_correct'
    ];

    public function result()
    {
        return $this->belongsTo(QuizResult::class, 'quiz_result_id');
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function option()
    {
        return $this->belongsTo(Option::class);
    }
}
