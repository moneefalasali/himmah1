<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'price',
        'instructor_name',
        'image',
        'status',
        'total_lessons',
        'duration',
        'course_size',
        'includes_summary',
        'includes_tajmeeat',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'includes_summary' => 'boolean',
        'includes_tajmeeat' => 'boolean',
    ];

    /**
     * Get the uni courses for the course.
     */
    public function uniCourses()
    {
        return $this->hasMany(UniCourse::class);
    }

    /**
     * Get the universities that have this course.
     */
    public function universities()
    {
        return $this->belongsToMany(University::class, 'uni_courses')
                    ->withPivot('custom_name')
                    ->withTimestamps();
    }

    /**
     * Get the lessons for the course.
     */
    public function lessons()
    {
        return $this->hasMany(Lesson::class)->orderBy('order');
    }

    /**
     * Get the free lessons for the course (for preview).
     */
    public function freeLessons()
    {
        return $this->hasMany(Lesson::class)->where('is_free', true)->orderBy('order')->limit(3);
    }

    /**
     * Get the purchases for the course.
     */
    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    /**
     * Get the reviews for the course.
     */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Get the average rating for the course.
     */
    public function averageRating()
    {
        return $this->reviews()->avg('rating') ?? 0;
    }

    /**
     * Get the total number of students enrolled.
     */
    public function studentsCount()
    {
        return $this->purchases()->where('payment_status', 'completed')->count();
    }

    /**
     * Check if the course is active.
     */
    public function isActive()
    {
        return $this->status === 'active';
    }

    /**
     * Get suggested price based on course size.
     */
    public function getSuggestedPriceAttribute()
    {
        if ($this->course_size === 'large') {
            return rand(149, 179); // Large course: 149-179 SAR
        }
        
        return rand(129, 149); // Normal course: 129-149 SAR
    }

    /**
     * Check if course is large.
     */
    public function isLarge()
    {
        return $this->course_size === 'large';
    }

    /**
     * Get course features description.
     */
    public function getFeaturesDescriptionAttribute()
    {
        $features = [];
        
        if ($this->includes_summary) {
            $features[] = 'ملخص شامل';
        }
        
        if ($this->includes_tajmeeat) {
            $features[] = 'تجميعات الأسئلة';
        }
        
        if (!empty($features)) {
            return 'يشمل ' . implode(' و ', $features) . ' مجاناً';
        }
        
        return '';
    }

    /**
     * Get formatted price in SAR.
     */
    public function getFormattedPriceAttribute()
    {
        return number_format($this->price, 2) . ' ريال';
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
    
    public function sections()
    {
        return $this->hasMany(Section::class)->orderBy('id');
    }

}

