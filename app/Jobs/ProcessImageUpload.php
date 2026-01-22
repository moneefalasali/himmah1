<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use App\Services\ImageService;

class ProcessImageUpload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $model;
    protected $tempPath;
    protected $folder;
    protected $field;

    /**
     * Create a new job instance.
     *
     * @param mixed $model الموديل الذي سيتم تحديثه (Course, User, etc.)
     * @param string $tempPath المسار المؤقت للصورة المرفوعة
     * @param string $folder المجلد المستهدف في Wasabi
     * @param string $field الحقل في قاعدة البيانات (image, avatar, etc.)
     */
    public function __construct($model, $tempPath, $folder = 'general', $field = 'image')
    {
        $this->model = $model;
        $this->tempPath = $tempPath;
        $this->folder = $folder;
        $this->field = $field;
    }

    /**
     * Execute the job.
     */
    public function handle(ImageService $imageService)
    {
        // في حالة الرغبة في معالجة الصورة (تغيير الحجم، ضغط، إلخ) يمكن القيام بذلك هنا قبل الرفع
        // حالياً سنقوم بالرفع المباشر كما هو مطلوب في الـ Pipeline الموحد
        
        $fileContent = Storage::disk('local')->get($this->tempPath);
        $extension = pathinfo($this->tempPath, PATHINFO_EXTENSION);
        
        $filename = \Illuminate\Support\Str::random(40) . '.' . $extension;
        $finalPath = "images/{$this->folder}/{$filename}";
        
        Storage::disk('wasabi')->put($finalPath, $fileContent, 'private');
        
        // حذف الصورة القديمة إذا وجدت
        if ($this->model->{$this->field}) {
            $imageService->deleteImage($this->model->{$this->field});
        }

        // تحديث الموديل بالمسار الجديد
        $this->model->update([
            $this->field => $finalPath
        ]);

        // حذف الملف المؤقت
        Storage::disk('local')->delete($this->tempPath);
    }
}
