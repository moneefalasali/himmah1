<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'course_id',
        'amount',
        'payment_status',
        'payment_method',
        'transaction_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    /**
     * Get the user that owns the purchase.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the course that was purchased.
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Check if the purchase is completed.
     */
    public function isCompleted()
    {
        return $this->payment_status === 'completed';
    }

    /**
     * Check if the purchase is pending.
     */
    public function isPending()
    {
        return $this->payment_status === 'pending';
    }

    /**
     * Check if the purchase failed.
     */
    public function isFailed()
    {
        return $this->payment_status === 'failed';
    }

    /**
     * Get formatted amount in SAR.
     */
    public function getFormattedAmountAttribute()
    {
        return number_format($this->amount, 2) . ' ريال';
    }

    /**
     * Get status in Arabic.
     */
    public function getStatusInArabicAttribute()
    {
        return match($this->payment_status) {
            'completed' => 'مكتمل',
            'pending' => 'في الانتظار',
            'failed' => 'فشل',
            default => 'غير معروف'
        };
    }
}

