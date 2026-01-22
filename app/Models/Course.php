<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Purchase;
use App\Models\Review;
use App\Models\Sale;
use App\Models\User;
use App\Models\Quiz;
class Course extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id', 'title', 'description', 'status', 'total_lessons', 'category_id', 'university_id', 'subject_id', 'type', 'price', 'instructor_name', 'image'
    ];
    public function teacher()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function lessons()
    {
        return $this->hasMany(Lesson::class);
    }
    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
    /**
     * Average rating rounded to nearest integer (0-5)
     */
    public function averageRating()
    {
        return (int) round($this->reviews()->avg('rating') ?? 0);
    }
    /**
     * Number of students (completed purchases)
     */
    public function studentsCount()
    {
        // Count unique students from completed purchases and manual enrollments (course_user pivot)
        try {
            $purchaseIds = \DB::table('purchases')
                ->where('course_id', $this->id)
                ->where('payment_status', 'completed')
                ->pluck('user_id')
                ->toArray();

            $enrolledIds = \DB::table('course_user')
                ->where('course_id', $this->id)
                ->pluck('user_id')
                ->toArray();

            return count(array_unique(array_merge($purchaseIds, $enrolledIds)));
        } catch (\Throwable $e) {
            // Fallback to previous behavior on error
            return $this->purchases()->where('payment_status', 'completed')->count();
        }
    }
    public function sections()
    {
        return $this->hasMany(Section::class);
    }

    public function quizzes()
    {
        return $this->hasMany(Quiz::class);
    }

    public function students()
    {
        return $this->belongsToMany(User::class, 'course_user')
            ->withPivot(['subscription_start', 'subscription_end', 'status'])
            ->withTimestamps();
    }
    public function sales()
    {
        return $this->hasMany(Sale::class);
    }
    public function university()
    {
        return $this->belongsTo(University::class);
    }
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
    public function scopePublished($query)
    {
        return $query->where("status", "active");
    }

    public function getFormattedPriceAttribute()
    {
        if (is_null($this->price) || $this->price == 0) {
            return 'مجاني';
        }
        return number_format($this->price, 2) . ' ريال';
    }
}
