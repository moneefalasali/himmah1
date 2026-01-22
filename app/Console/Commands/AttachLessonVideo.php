<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Lesson;
use Illuminate\Support\Facades\Storage;
use App\Jobs\ProcessVideoHLS;

class AttachLessonVideo extends Command
{
    protected $signature = 'lesson:attach-video {lesson_id} {key}';
    protected $description = 'Attach an existing Wasabi key to a lesson and dispatch HLS processing';

    public function handle()
    {
        $id = $this->argument('lesson_id');
        $key = $this->argument('key');

        $lesson = Lesson::find($id);
        if (! $lesson) {
            $this->error("Lesson $id not found");
            return 1;
        }

        $lesson->video_path = $key;
        try {
            $lesson->video_url = Storage::disk('wasabi')->url($key) ?: '';
        } catch (\Exception $e) {
            $lesson->video_url = '';
        }
        $lesson->video_platform = 'wasabi';
        $lesson->processing_status = 'processing';
        $lesson->processing_error = null;
        $lesson->save();

        ProcessVideoHLS::dispatch($lesson);

        $this->info("Attached key to lesson $id and dispatched processing.");
        return 0;
    }
}
