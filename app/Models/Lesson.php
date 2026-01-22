<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'section_id',
        'title',
        'description',
        'video_url',
        'video_path',
        'vimeo_video_id',
        'video_platform',
        'processing_status',
        'hls_path',
        'processing_error',
        'duration',
        'order',
        'is_free',
    ];

    protected $casts = [
        'is_free' => 'boolean',
    ];

    /**
     * Get the course that owns the lesson.
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the section that owns the lesson.
     */
    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    /**
     * Get the uni courses that have this lesson.
     */
    public function uniCourses()
    {
        return $this->belongsToMany(UniCourse::class, 'course_lesson_mappings')
                    ->withPivot('order')
                    ->orderBy('course_lesson_mappings.order');
    }

    /**
     * Get the course lesson mappings for this lesson.
     */
    public function courseLessonMappings()
    {
        return $this->hasMany(CourseLessonMapping::class);
    }

    /**
     * Get the learning progress for the lesson.
     */
    public function learningProgress()
    {
        return $this->hasMany(LearningProgress::class);
    }

    /**
     * Get the learning progress for a specific user.
     */
    public function progressForUser($userId)
    {
        return $this->learningProgress()->where('user_id', $userId)->first();
    }

    /**
     * Check if the lesson is completed by a user.
     */
    public function isCompletedByUser($userId)
    {
        $progress = $this->progressForUser($userId);
        return $progress ? $progress->completed : false;
    }

    /**
     * Get formatted duration.
     */
    public function getFormattedDurationAttribute()
    {
        if ($this->duration) {
            $hours = floor($this->duration / 60);
            $minutes = $this->duration % 60;
            
            if ($hours > 0) {
                return $hours . ' ساعة' . ($minutes > 0 ? ' و ' . $minutes . ' دقيقة' : '');
            }
            
            return $minutes . ' دقيقة';
        }
        
        return 'غير محدد';
    }

    /**
     * Check if user can access this lesson.
     */
    public function canUserAccess($user)
    {
        // If lesson is free, anyone can access
        if ($this->is_free) {
            return true;
        }

        // If user is not authenticated, can't access
        if (!$user) {
            return false;
        }

        // Check if user has purchased the course
        return $user->hasPurchased($this->course_id);
    }
}

