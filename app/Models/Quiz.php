<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'title',
        'description',
        'time_limit',
        'passing_score',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'time_limit' => 'integer',
        'passing_score' => 'integer',
    ];

    /**
     * Get the course that owns the quiz.
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the questions for the quiz.
     */
    public function questions()
    {
        return $this->hasMany(QuizQuestion::class)->orderBy('order');
    }

    /**
     * Get the attempts for the quiz.
     */
    public function attempts()
    {
        return $this->hasMany(QuizAttempt::class);
    }

    /**
     * Check if quiz is active.
     */
    public function isActive()
    {
        return $this->is_active;
    }

    /**
     * Get total questions count.
     */
    public function getTotalQuestionsAttribute()
    {
        return $this->questions()->count();
    }

    /**
     * Get total points.
     */
    public function getTotalPointsAttribute()
    {
        return $this->questions()->sum('points');
    }
} 