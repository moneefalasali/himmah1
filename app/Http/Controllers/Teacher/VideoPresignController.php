<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Services\VideoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class VideoPresignController extends Controller
{
    protected $videoService;

    public function __construct(VideoService $videoService)
    {
        $this->videoService = $videoService;
    }

    /**
     * Initiate multipart upload and return presigned URLs for parts.
     */
    public function initiate(Request $request)
    {
        $request->validate([
            'filename' => 'required|string',
            'parts' => 'required|integer|min:1|max:10000',
            'content_type' => 'nullable|string',
        ]);

        try {
            $initData = $this->videoService->initiateMultipartUpload(
                $request->input('filename'),
                $request->input('content_type', 'video/mp4')
            );

            $urls = [];
            for ($i = 1; $i <= (int)$request->input('parts'); $i++) {
                $urls[] = [
                    'part' => $i,
                    'url' => $this->videoService->getPresignedPartUrl($initData['key'], $initData['uploadId'], $i),
                ];
            }

            return response()->json([
                'ok' => true,
                'uploadId' => $initData['uploadId'],
                'key' => $initData['key'],
                'parts' => $urls,
            ]);
        } catch (Exception $e) {
            Log::error('Wasabi presign initiate error', ['message' => $e->getMessage()]);
            return response()->json(['ok' => false, 'message' => 'Failed to initiate upload'], 500);
        }
    }

    /**
     * Complete multipart upload.
     */
    public function complete(Request $request)
    {
        $request->validate([
            'uploadId' => 'required|string',
            'key' => 'required|string',
            'parts' => 'required|array',
        ]);

        try {
            $result = $this->videoService->completeMultipartUpload(
                $request->input('key'),
                $request->input('uploadId'),
                $request->input('parts')
            );

            // Here you should update your database (e.g., the Lesson model)
            // with the new video information.
            // For example:
            // $lesson = Lesson::find($request->input('lesson_id'));
            // if ($lesson) {
            //     $lesson->update([
            //         'video_platform' => 'wasabi',
            //         'video_path' => $request->input('key'),
            //         'processing_status' => 'pending_transcode', // or similar
            //     ]);
            // }

            return response()->json([
                'ok' => true,
                'key' => $request->input('key'),
                'location' => $result['Location'] ?? null
            ]);
        } catch (Exception $e) {
            Log::error('Wasabi presign complete error', ['message' => $e->getMessage()]);
            return response()->json(['ok' => false, 'message' => 'Failed to complete upload'], 500);
        }
    }
}
