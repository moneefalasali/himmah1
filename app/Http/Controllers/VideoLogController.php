<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class VideoLogController extends Controller
{
    public function log(Request $request)
    {
        $request->validate([
            'lesson_id' => 'required|exists:lessons,id',
            'action' => 'required|string',
            'time' => 'nullable|numeric'
        ]);

        DB::table('video_audit_logs')->insert([
            'user_id' => Auth::id(),
            'lesson_id' => $request->lesson_id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'action' => $request->action,
            'timestamp_in_video' => $request->time,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['status' => 'logged']);
    }
}
