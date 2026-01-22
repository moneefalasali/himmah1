<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LearningProgress extends Model
{
    use HasFactory;

    protected $table = 'learning_progress';

    protected $fillable = [
        'user_id',
        'lesson_id',
        'completed',
        'watched_duration',
    ];

    protected $casts = [
        'completed' => 'boolean',
    ];

    /**
     * Get the user that owns the learning progress.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the lesson that the progress belongs to.
     */
    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    /**
     * Get the completion percentage.
     */
    public function getCompletionPercentageAttribute()
    {
        if (!$this->lesson || !$this->lesson->duration) {
            return 0;
        }

        $lessonDurationInSeconds = $this->lesson->duration * 60;
        return min(100, ($this->watched_duration / $lessonDurationInSeconds) * 100);
    }

    /**
     * Mark lesson as completed.
     */
    public function markAsCompleted()
    {
        $this->update([
            'completed' => true,
            'watched_duration' => $this->lesson ? $this->lesson->duration * 60 : $this->watched_duration,
        ]);
    }

    /**
     * Update watched duration.
     */
    public function updateWatchedDuration($seconds)
    {
        $this->update([
            'watched_duration' => $seconds,
            'completed' => $this->lesson && $this->lesson->duration ? 
                $seconds >= ($this->lesson->duration * 60 * 0.9) : false, // 90% completion
        ]);
    }
}

