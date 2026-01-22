<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class University extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'city',
    ];

    /**
     * Get the users for the university.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the uni courses for the university.
     */
    public function uniCourses()
    {
        return $this->hasMany(UniCourse::class);
    }

    /**
     * Get the courses available for this university.
     */
    public function courses()
    {
        return $this->belongsToMany(Course::class, 'uni_courses')
                    ->withPivot('custom_name')
                    ->withTimestamps();
    }
}

