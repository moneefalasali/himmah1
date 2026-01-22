<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ChatRoom extends Model
{
    use HasFactory;

    protected $fillable = ['course_id', 'name', 'is_active', 'type'];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function messages()
    {
        return $this->hasMany(ChatMessage::class);
    }

    public function lastMessage()
    {
        return $this->hasOne(ChatMessage::class)->latestOfMany();
    }
}
