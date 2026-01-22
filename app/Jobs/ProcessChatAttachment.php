<?php

namespace App\Jobs;

use App\Models\ChatAttachment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class ProcessChatAttachment implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $attachment;

    public function __construct(ChatAttachment $attachment)
    {
        $this->attachment = $attachment;
    }

    public function handle()
    {
        // هنا يمكن إضافة منطق معالجة الملفات مثل:
        // 1. ضغط الصور إذا كانت كبيرة جداً
        // 2. إنشاء مصغرات (Thumbnails) للصور
        // 3. التحقق من سلامة الملفات
        
        if (str_contains($this->attachment->file_type, 'image')) {
            // مثال: منطق معالجة الصور
            \Log::info("Processing image attachment: " . $this->attachment->file_name);
        }
    }
}
