<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseLessonMapping extends Model
{
    use HasFactory;

    protected $fillable = [
        'uni_course_id',
        'lesson_id',
        'order',
    ];

    /**
     * Get the uni course that owns the mapping.
     */
    public function uniCourse()
    {
        return $this->belongsTo(UniCourse::class);
    }

    /**
     * Get the lesson that owns the mapping.
     */
    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }
}

