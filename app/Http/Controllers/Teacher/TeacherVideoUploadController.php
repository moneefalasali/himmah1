<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Services\VideoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeacherVideoUploadController extends Controller
{
    protected $videoService;

    public function __construct(VideoService $videoService)
    {
        $this->videoService = $videoService;
    }

    /**
     * معالجة رفع الفيديو بالـ Chunks للمعلم
     */
    public function upload(Request $request)
    {
        // Accept both snake_case (from admin JS) and camelCase (possible variants)
        $request->validate([
            'file' => 'required|file',
            'filename' => 'required|string',
            'chunk_index' => 'sometimes|integer',
            'total_chunks' => 'sometimes|integer',
            'chunkIndex' => 'sometimes|integer',
            'totalChunks' => 'sometimes|integer',
        ]);

        $file = $request->file('file');
        $filename = $request->input('filename');
        $chunkIndex = $request->input('chunk_index', $request->input('chunkIndex'));
        $totalChunks = $request->input('total_chunks', $request->input('totalChunks'));

        $result = $this->videoService->handleChunkUpload($file, $filename, $chunkIndex, $totalChunks);

        if ($result) {
            // تم اكتمال الرفع وتجميع الملف محلياً
            // نرفع الملف الآن إلى Wasabi
            $wasabiPath = $this->videoService->uploadRawToWasabi($result, Auth::id() . '_' . uniqid());
            
            return response()->json([
                'success' => true,
                'path' => $wasabiPath
            ]);
        }

        return response()->json(['status' => 'chunk_uploaded']);
    }
}
