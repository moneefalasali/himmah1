<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\VideoService;
use App\Models\Lesson;
use App\Jobs\ProcessVideoHLS;

class VideoUploadController extends Controller
{
    protected $videoService;

    public function __construct(VideoService $videoService)
    {
        $this->videoService = $videoService;
    }

    public function upload(Request $request)
    {
        $request->validate([
            'lesson_id' => 'required',
            'file' => 'required|file',
            'filename' => 'required|string',
            'chunk_index' => 'required|integer',
            'total_chunks' => 'required|integer',
        ]);

        $finalPath = $this->videoService->handleChunkUpload(
            $request->file('file'),
            $request->filename,
            $request->chunk_index,
            $request->total_chunks
        );

        if ($finalPath) {
            // Upload to Wasabi (Raw)
            // If lesson_id is 0, it's a new lesson being created
            $lessonId = $request->lesson_id == '0' ? 'temp_' . time() : $request->lesson_id;
            $wasabiPath = $this->videoService->uploadRawToWasabi($finalPath, $lessonId);
            
            if ($request->lesson_id != '0') {
                $lesson = Lesson::findOrFail($request->lesson_id);
                $lesson->update([
                    'video_path' => $wasabiPath,
                    'processing_status' => 'pending'
                ]);
                \App\Jobs\ProcessVideoHLS::dispatch($lesson);
            }

            return response()->json(['status' => 'completed', 'path' => $wasabiPath]);
        }

        return response()->json(['status' => 'chunk_uploaded']);
    }
}
