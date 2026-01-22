<?php

namespace App\Jobs;

use App\Models\LiveSession;
use App\Models\Lesson; // افتراض وجود موديل الدروس
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ProcessZoomRecording implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $session;
    protected $recordingFiles;

    public function __construct(LiveSession $session, $recordingFiles)
    {
        $this->session = $session;
        $this->recordingFiles = $recordingFiles;
    }

    public function handle()
    {
        try {
            // 1. البحث عن ملف الفيديو (MP4)
            $videoFile = collect($this->recordingFiles)->firstWhere('file_type', 'MP4');
            if (!$videoFile) return;

            $downloadUrl = $videoFile['download_url'];
            $fileName = "recordings/{$this->session->meeting_id}.mp4";

            // 2. تحميل الملف مؤقتاً
            $response = Http::get($downloadUrl . '?access_token=' . $this->getZoomToken());
            Storage::disk('local')->put($fileName, $response->body());

            $localPath = storage_path("app/{$fileName}");

            // 3. الرفع إلى Wasabi
            $wasabiPath = "courses/{$this->session->course_id}/recordings/{$this->session->meeting_id}.mp4";
            Storage::disk('wasabi')->put($wasabiPath, fopen($localPath, 'r+'));

            // 4. تحويل إلى HLS (يتطلب FFMPEG مثبت على السيرفر)
            $hlsFolder = "courses/{$this->session->course_id}/hls/{$this->session->meeting_id}";
            $this->convertToHls($localPath, $hlsFolder);

            // 5. تحديث قاعدة البيانات وربطها كدرس مسجل
            $this->session->update([
                'recording_path' => $wasabiPath,
                'hls_path' => "{$hlsFolder}/playlist.m3u8",
                'status' => 'finished'
            ]);

            // إنشاء درس جديد في الكورس تلقائياً
            Lesson::create([
                'course_id' => $this->session->course_id,
                'title' => "تسجيل حصة: " . $this->session->topic,
                'content' => "هذا هو التسجيل المسجل للحصة المباشرة التي تمت بتاريخ " . $this->session->start_time->format('Y-m-d'),
                'video_path' => "{$hlsFolder}/playlist.m3u8",
                'is_preview' => false,
                'type' => 'recorded_live'
            ]);

            // تنظيف الملفات المؤقتة
            unlink($localPath);

        } catch (\Exception $e) {
            Log::error("Error processing Zoom recording: " . $e->getMessage());
        }
    }

    protected function getZoomToken()
    {
        // نحتاج توكن للتحميل إذا كان التسجيل محمياً
        return app(\App\Services\ZoomService::class)->getAccessToken();
    }

    protected function convertToHls($inputPath, $outputFolder)
    {
        $outputPath = storage_path("app/public/{$outputFolder}");
        if (!file_exists($outputPath)) mkdir($outputPath, 0755, true);

        $command = "ffmpeg -i {$inputPath} -profile:v baseline -level 3.0 -s 640x360 -start_number 0 -hls_time 10 -hls_list_size 0 -f hls {$outputPath}/playlist.m3u8";
        exec($command);

        // رفع مجلد HLS بالكامل إلى Wasabi
        foreach (glob("{$outputPath}/*") as $file) {
            Storage::disk('wasabi')->put("{$outputFolder}/" . basename($file), fopen($file, 'r+'));
        }
    }
}
