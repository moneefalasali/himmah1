<?php
namespace App\Models;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role', 'avatar', 'google_id', 'is_instructor'
    ];

    protected $appends = ['avatar_url'];

    protected $hidden = [
        'password', 'remember_token', 'google_id',
    ];

    public function getAvatarUrlAttribute()
    {
        if (!$this->avatar) {
            return asset('assets/images/default-avatar.png');
        }
        return app(\App\Services\ImageService::class)->getSignedUrl($this->avatar);
    }

    public function isAdmin() { return $this->role === 'admin'; }
    public function isTeacher() { return $this->role === 'teacher' || $this->is_instructor; }
    public function isStudent() { return in_array($this->role, ['student', 'user']); }

    public function isEnrolledIn($course)
    {
        $courseId = is_object($course) ? $course->id : $course;
        return $this->enrolledCourses()->where('course_id', $courseId)->wherePivot('status', 'active')->exists();
    }

    public function hasPurchased($courseId)
    {
        return $this->enrolledCourses()->where('course_id', $courseId)->wherePivot('status', 'active')->exists();
    }

    public function isSubscribedTo($course)
    {
        $courseId = is_object($course) ? $course->id : $course;
        return $this->hasPurchased($courseId);
    }

    public function teacherCourses()
    {
        return $this->hasMany(Course::class, 'user_id');
    }

    public function enrolledCourses()
    {
        return $this->belongsToMany(Course::class, 'course_user')
            ->withPivot(['subscription_start', 'subscription_end', 'status'])
            ->withTimestamps();
    }

    public function activeCourses()
    {
        return $this->enrolledCourses()
            ->wherePivot('status', 'active')
            ->wherePivot('subscription_end', '>', now());
    }

    public function purchases()
    {
        return $this->hasMany(\App\Models\Purchase::class);
    }

    public function serviceRequests()
    {
        return $this->hasMany(\App\Models\ServiceRequest::class);
    }

    public function reviews()
    {
        return $this->hasMany(\App\Models\Review::class);
    }

    public function learningProgress()
    {
        return $this->hasMany(\App\Models\LearningProgress::class);
    }
}
