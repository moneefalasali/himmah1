<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UniCourse extends Model
{
    use HasFactory;

    protected $fillable = [
        'university_id',
        'course_id',
        'custom_name',
    ];

    /**
     * Get the university that owns the uni course.
     */
    public function university()
    {
        return $this->belongsTo(University::class);
    }

    /**
     * Get the course that owns the uni course.
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the lessons for this uni course through mappings.
     */
    public function lessons()
    {
        return $this->belongsToMany(Lesson::class, 'course_lesson_mappings')
                    ->withPivot('order')
                    ->orderBy('course_lesson_mappings.order');
    }

    /**
     * Get the course lesson mappings for this uni course.
     */
    public function courseLessonMappings()
    {
        return $this->hasMany(CourseLessonMapping::class);
    }

    /**
     * Get the display name for this uni course.
     */
    public function getDisplayNameAttribute()
    {
        return $this->custom_name ?: $this->course->title;
    }

    /**
     * Get the total number of lessons in this uni course.
     */
    public function getTotalLessonsAttribute()
    {
        return $this->lessons()->count();
    }
}

