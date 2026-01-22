<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'service_type_id',
        'title',
        'description',
        'requirements',
        'status',
        'is_urgent',
        'amount',
    ];

    /**
     * Get the user that owns the service request.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the service type for this request.
     */
    public function serviceType()
    {
        return $this->belongsTo(ServiceType::class);
    }

    /**
     * Get the files for this service request.
     */
    public function files()
    {
        return $this->hasMany(ServiceRequestFile::class);
    }

    /**
     * Get the messages for this service request.
     */
    public function messages()
    {
        return $this->hasMany(Message::class)->orderBy('created_at');
    }

    /**
     * Get the latest message for this service request.
     */
    public function latestMessage()
    {
        return $this->hasOne(Message::class)->latest();
    }

    /**
     * Get unread messages count for a specific user.
     */
    public function unreadMessagesCount($userId)
    {
        return $this->messages()
            ->where('sender_id', '!=', $userId)
            ->where('is_read', false)
            ->count();
    }

    /**
     * Check if the request is pending.
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }

    /**
     * Check if the request is in progress.
     */
    public function isInProgress()
    {
        return $this->status === 'in_progress';
    }

    /**
     * Check if the request is completed.
     */
    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    /**
     * Check if the request is cancelled.
     */
    public function isCancelled()
    {
        return $this->status === 'cancelled';
    }

    /**
     * Get status in Arabic.
     */
    public function getStatusInArabicAttribute()
    {
        return match($this->status) {
            'pending' => 'في الانتظار',
            'in_progress' => 'قيد التنفيذ',
            'completed' => 'مكتمل',
            'cancelled' => 'ملغي',
            default => 'غير معروف'
        };
    }

    /**
     * Get status color for UI.
     */
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'pending' => 'warning',
            'in_progress' => 'info',
            'completed' => 'success',
            'cancelled' => 'danger',
            default => 'secondary'
        };
    }

    /**
     * Mark request as in progress.
     */
    public function markAsInProgress()
    {
        $this->update(['status' => 'in_progress']);
    }

    /**
     * Mark request as completed.
     */
    public function markAsCompleted()
    {
        $this->update(['status' => 'completed']);
    }

    /**
     * Mark request as cancelled.
     */
    public function markAsCancelled()
    {
        $this->update(['status' => 'cancelled']);
    }
}

