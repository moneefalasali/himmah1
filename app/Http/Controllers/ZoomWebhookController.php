<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LiveSession;
use App\Jobs\ProcessZoomRecording;
use Illuminate\Support\Facades\Log;

class ZoomWebhookController extends Controller
{
    public function handle(Request $request)
    {
        // 1. التحقق من صحة التوقيع (Security)
        if (!$this->isValidSignature($request)) {
            return response()->json(['message' => 'Invalid signature'], 401);
        }

        $event = $request->input('event');
        $payload = $request->input('payload');

        // 2. التعامل مع تحدي التحقق من Zoom (URL Validation)
        if ($event === 'endpoint.url_validation') {
            return $this->validateUrl($payload['plainToken']);
        }

        // 3. معالجة الأحداث
        switch ($event) {
            case 'meeting.ended':
                $this->handleMeetingEnded($payload['object']);
                break;

            case 'recording.completed':
                $this->handleRecordingCompleted($payload['object']);
                break;
        }

        return response()->json(['status' => 'success']);
    }

    protected function isValidSignature(Request $request)
    {
        $secret = config('services.zoom.webhook_secret');
        $signature = $request->header('x-zm-signature');
        $timestamp = $request->header('x-zm-request-timestamp');
        
        $message = "v0:{$timestamp}:" . $request->getContent();
        $hash = hash_hmac('sha256', $message, $secret);
        $expectedSignature = "v0={$hash}";

        return hash_equals($expectedSignature, $signature);
    }

    protected function validateUrl($plainToken)
    {
        $secret = config('services.zoom.webhook_secret');
        $hash = hash_hmac('sha256', $plainToken, $secret);
        
        return response()->json([
            'plainToken' => $plainToken,
            'encryptedToken' => $hash
        ]);
    }

    protected function handleMeetingEnded($meeting)
    {
        $session = LiveSession::where('meeting_id', $meeting['id'])->first();
        if ($session) {
            $session->update(['status' => 'finished']);
        }
    }

    protected function handleRecordingCompleted($recording)
    {
        $session = LiveSession::where('meeting_id', $recording['id'])->first();
        if ($session) {
            // إرسال مهمة معالجة التسجيل للخلفية
            ProcessZoomRecording::dispatch($session, $recording['recording_files']);
        }
    }
}
