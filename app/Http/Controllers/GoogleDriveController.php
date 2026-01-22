<?php

namespace App\Http\Controllers;

use App\Services\GoogleDriveService;
use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GoogleDriveController extends Controller
{
    protected $driveService;

    public function __construct(GoogleDriveService $driveService)
    {
        $this->driveService = $driveService;
    }

    /**
     * Proxy Google Drive video stream to hide the real ID and enforce permissions.
     */
    public function proxy(Request $request, $fileId)
    {
        // 1. Verify student permissions (This is crucial)
        $lesson = Lesson::where('video_url', $fileId)
                        ->orWhere('video_path', $fileId)
                        ->first();

        if (!$lesson || !$lesson->canUserAccess(Auth::user())) {
            abort(403, 'Unauthorized access to video.');
        }

        // 2. Get the stream from Google Drive
        $stream = $this->driveService->getFileStream($fileId);

        if (!$stream) {
            abort(404, 'Video not found on Google Drive.');
        }

        // 3. Return the stream as a response
        return response()->stream(function () use ($stream) {
            while (!$stream->eof()) {
                echo $stream->read(1024 * 8);
            }
        }, 200, [
            'Content-Type' => 'video/mp4', // Adjust based on actual file type if needed
            'Cache-Control' => 'no-cache, must-revalidate',
        ]);
    }
}
