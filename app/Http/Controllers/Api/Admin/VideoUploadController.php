<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Services\VideoService;

class VideoUploadController extends Controller
{
    protected $videoService;

    public function __construct(VideoService $videoService)
    {
        $this->videoService = $videoService;
    }

    /**
     * Receive a chunk and assemble when complete, then upload to Wasabi.
     */
    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file',
            'filename' => 'required|string',
            'chunk_index' => 'required|integer|min:0',
            'total_chunks' => 'required|integer|min:1',
        ]);

        $filename = $request->input('filename');
        $chunkIndex = (int) $request->input('chunk_index');
        $totalChunks = (int) $request->input('total_chunks');

        $tempDir = storage_path('app/temp/uploads');
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0777, true);
        }

        $partPath = $tempDir . DIRECTORY_SEPARATOR . $filename . '.part' . $chunkIndex;
        // Move uploaded chunk to part file
        $request->file('file')->move($tempDir, $filename . '.part' . $chunkIndex);

        // If last chunk, assemble
        if ($chunkIndex === $totalChunks - 1) {
            $finalLocal = $tempDir . DIRECTORY_SEPARATOR . $filename . '.assembled';
            $out = fopen($finalLocal, 'wb');
            for ($i = 0; $i < $totalChunks; $i++) {
                $partFile = $tempDir . DIRECTORY_SEPARATOR . $filename . '.part' . $i;
                if (!file_exists($partFile)) {
                    fclose($out);
                    return response()->json(['error' => 'Missing chunk ' . $i], 500);
                }
                $in = fopen($partFile, 'rb');
                stream_copy_to_stream($in, $out);
                fclose($in);
                @unlink($partFile);
            }
            fclose($out);

            // Upload assembled file to Wasabi using VideoService
            try {
                $remotePath = $this->videoService->uploadRawToWasabi($finalLocal, time());
                @unlink($finalLocal);
            } catch (\Exception $e) {
                return response()->json(['error' => 'Upload failed: ' . $e->getMessage()], 500);
            }

            return response()->json(['path' => $remotePath]);
        }

        return response()->json(['status' => 'part_received']);
    }
}
