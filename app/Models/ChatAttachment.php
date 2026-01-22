<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Services\ImageService;

class ChatAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'chat_message_id', 
        'file_path', 
        'file_name', 
        'file_type', 
        'file_size'
    ];

    protected $appends = ['file_url'];

    public function message()
    {
        return $this->belongsTo(ChatMessage::class, 'chat_message_id');
    }

    /**
     * الحصول على الرابط الموقع للملف من Wasabi/S3
     */
    public function getFileUrlAttribute()
    {
        return app(ImageService::class)->getSignedUrl($this->file_path);
    }
}
