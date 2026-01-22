<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'course_id',
        'rating',
        'comment',
    ];

    /**
     * Get the user that owns the review.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the course that was reviewed.
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get star rating display.
     */
    public function getStarRatingAttribute()
    {
        $stars = '';
        for ($i = 1; $i <= 5; $i++) {
            if ($i <= $this->rating) {
                $stars .= '★';
            } else {
                $stars .= '☆';
            }
        }
        return $stars;
    }

    /**
     * Get rating in Arabic.
     */
    public function getRatingInArabicAttribute()
    {
        return match($this->rating) {
            1 => 'ضعيف جداً',
            2 => 'ضعيف',
            3 => 'متوسط',
            4 => 'جيد',
            5 => 'ممتاز',
            default => 'غير محدد'
        };
    }
}

