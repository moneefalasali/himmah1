<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_id',
        'user_id',
        'started_at',
        'completed_at',
        'score',
        'total_points',
        'answers',
        'is_passed',
        'time_taken',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'answers' => 'array',
        'score' => 'integer',
        'total_points' => 'integer',
        'is_passed' => 'boolean',
        'time_taken' => 'integer',
    ];

    /**
     * Get the quiz that owns the attempt.
     */
    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    /**
     * Get the user that owns the attempt.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if attempt is completed.
     */
    public function isCompleted()
    {
        return !is_null($this->completed_at);
    }

    /**
     * Get percentage score.
     */
    public function getPercentageScoreAttribute()
    {
        if ($this->total_points > 0) {
            return round(($this->score / $this->total_points) * 100, 2);
        }
        return 0;
    }

    /**
     * Get formatted time taken.
     */
    public function getFormattedTimeTakenAttribute()
    {
        if (!$this->time_taken) {
            return 'غير محدد';
        }

        $minutes = floor($this->time_taken / 60);
        $seconds = $this->time_taken % 60;

        if ($minutes > 0) {
            return $minutes . ' دقيقة ' . $seconds . ' ثانية';
        }

        return $seconds . ' ثانية';
    }
} 