<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use App\Services\VideoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VideoStreamController extends Controller
{
    protected $videoService;

    public function __construct(VideoService $videoService)
    {
        $this->videoService = $videoService;
    }

    /**
     * Get Signed URL for HLS Master Playlist
     */
    public function getStreamUrl(Lesson $lesson)
    {
        if (!$lesson->canUserAccess(Auth::user())) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if ($lesson->processing_status !== 'completed') {
            return response()->json(['error' => 'Video is still processing'], 422);
        }

        // Generate Signed URL for master.m3u8
        $url = $this->videoService->getSignedUrl($lesson->hls_path, 10);

        return response()->json([
            'stream_url' => $url,
            'watermark' => [
                'text' => Auth::user()->email,
                'id' => Auth::user()->id
            ]
        ]);
    }

    /**
     * Proxy for HLS Segments (Optional but recommended for higher security)
     * This ensures segments are also signed or validated
     */
    public function getSegment(Request $request, $lessonId, $quality, $filename)
    {
        $lesson = Lesson::findOrFail($lessonId);
        if (!$lesson->canUserAccess(Auth::user())) {
            abort(403);
        }

        $path = "hls/{$lessonId}/{$quality}/{$filename}";
        return redirect($this->videoService->getSignedUrl($path, 5));
    }
}
