<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LiveSession;
use App\Services\ZoomService;
use Illuminate\Http\Request;

class AdminLiveSessionController extends Controller
{
    protected $zoomService;

    public function __construct(ZoomService $zoomService)
    {
        $this->zoomService = $zoomService;
    }

    public function index()
    {
        $sessions = LiveSession::with(['course', 'teacher'])->latest()->paginate(20);
        return view('admin.live_sessions.index', compact('sessions'));
    }

    public function updateStatus(Request $request, LiveSession $liveSession)
    {
        $request->validate([
            'status' => 'required|in:active,cancelled,finished'
        ]);

        $liveSession->update(['status' => $request->status]);

        return back()->with('success', 'تم تحديث حالة الحصة بنجاح.');
    }

    public function destroy(LiveSession $liveSession)
    {
        try {
            $this->zoomService->deleteMeeting($liveSession->meeting_id);
            $liveSession->delete();
            return back()->with('success', 'تم حذف الحصة نهائياً.');
        } catch (\Exception $e) {
            $liveSession->delete(); // حذف محلي حتى لو فشل Zoom
            return back()->with('warning', 'تم حذف الحصة محلياً ولكن فشل الحذف من Zoom.');
        }
    }
}
