<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'role',
        'university_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Get the university that owns the user.
     */
    public function university()
    {
        return $this->belongsTo(University::class);
    }

    /**
     * Get the purchases for the user.
     */
    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    /**
     * Get the service requests for the user.
     */
    public function serviceRequests()
    {
        return $this->hasMany(ServiceRequest::class);
    }

    /**
     * Get the learning progress for the user.
     */
    public function learningProgress()
    {
        return $this->hasMany(LearningProgress::class);
    }

    /**
     * Get the reviews for the user.
     */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Get the messages sent by the user.
     */
    public function messages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    /**
     * Check if user is admin.
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user has purchased a course.
     */
    public function hasPurchased($courseId)
    {
        return $this->purchases()
            ->where('course_id', $courseId)
            ->where('payment_status', 'completed')
            ->exists();
    }
}

