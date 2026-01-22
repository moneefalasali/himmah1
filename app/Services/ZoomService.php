<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ZoomService
{
    protected $baseUrl = 'https://api.zoom.us/v2';
    protected $accountId;
    protected $clientId;
    protected $clientSecret;

    public function __construct()
    {
        $this->accountId = config('services.zoom.account_id');
        $this->clientId = config('services.zoom.client_id');
        $this->clientSecret = config('services.zoom.client_secret');
    }

    /**
     * الحصول على Access Token باستخدام Server-to-Server OAuth
     */
    protected function getAccessToken()
    {
        $response = Http::asForm()->withBasicAuth($this->clientId, $this->clientSecret)
            ->post("https://zoom.us/oauth/token", [
                'grant_type' => 'account_credentials',
                'account_id' => $this->accountId,
            ]);

        if ($response->failed()) {
            Log::error('Zoom OAuth Failed: ' . $response->body());
            throw new \Exception('Failed to get Zoom access token');
        }

        return $response->json()['access_token'];
    }

    /**
     * إنشاء اجتماع جديد
     */
    public function createMeeting($data)
    {
        $token = $this->getAccessToken();

        $response = Http::withToken($token)
            ->post("{$this->baseUrl}/users/me/meetings", [
                'topic' => $data['topic'],
                'type' => 2, // Scheduled meeting
                'start_time' => $data['start_time'], // ISO 8601 format
                'duration' => $data['duration'],
                'timezone' => config('app.timezone'),
                'settings' => [
                    'host_video' => true,
                    'participant_video' => true,
                    'join_before_host' => false,
                    'mute_upon_entry' => true,
                    'waiting_room' => true,
                    'auto_recording' => 'cloud', // تفعيل التسجيل السحابي تلقائياً
                ],
            ]);

        if ($response->failed()) {
            Log::error('Zoom Meeting Creation Failed: ' . $response->body());
            throw new \Exception('Failed to create Zoom meeting');
        }

        return $response->json();
    }

    /**
     * حذف اجتماع
     */
    public function deleteMeeting($meetingId)
    {
        $token = $this->getAccessToken();
        $response = Http::withToken($token)->delete("{$this->baseUrl}/meetings/{$meetingId}");
        return $response->successful();
    }

    /**
     * الحصول على تفاصيل التسجيل
     */
    public function getRecording($meetingId)
    {
        $token = $this->getAccessToken();
        $response = Http::withToken($token)->get("{$this->baseUrl}/meetings/{$meetingId}/recordings");
        
        if ($response->failed()) {
            Log::error('Zoom Recording Fetch Failed: ' . $response->body());
            return null;
        }

        return $response->json();
    }
}
