<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_request_id',
        'sender_id',
        'message',
        'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    /**
     * Get the service request that owns this message.
     */
    public function serviceRequest()
    {
        return $this->belongsTo(ServiceRequest::class);
    }

    /**
     * Get the user who sent this message.
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Get the files attached to this message.
     */
    public function files()
    {
        return $this->hasMany(MessageFile::class);
    }

    /**
     * Mark message as read.
     */
    public function markAsRead()
    {
        $this->update(['is_read' => true]);
    }

    /**
     * Check if message is from admin.
     */
    public function isFromAdmin()
    {
        return $this->sender && $this->sender->isAdmin();
    }

    /**
     * Get formatted created time.
     */
    public function getFormattedTimeAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Get message preview (first 100 characters).
     */
    public function getPreviewAttribute()
    {
        return strlen($this->message) > 100 
            ? substr($this->message, 0, 100) . '...' 
            : $this->message;
    }
}

