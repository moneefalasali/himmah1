<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'user_id',
        'teacher_id',
        'amount',
        'teacher_commission',
        'admin_commission',
        'transaction_id',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // alias for compatibility with controllers expecting `user` relation
    public function user()
    {
        return $this->student();
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }
}
