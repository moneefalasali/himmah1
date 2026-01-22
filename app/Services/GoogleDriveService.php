<?php

namespace App\Services;

use Google\Client as GoogleClient;
use Google\Service\Drive as GoogleDrive;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class GoogleDriveService
{
    protected $client;
    protected $service;

    public function __construct()
    {
        $this->client = new GoogleClient();
        $authConfig = storage_path('app/google-drive-service-account.json');
        
        if (file_exists($authConfig)) {
            $this->client->setAuthConfig($authConfig);
            $this->client->addScope(GoogleDrive::DRIVE_READONLY);
            $this->service = new GoogleDrive($this->client);
        }
    }

    /**
     * Get a temporary view URL for a private Google Drive file.
     * Since Google Drive doesn't support native presigned URLs for iframes,
     * we use a proxy approach or a temporary access token.
     */
    public function getTemporaryViewUrl($fileId)
    {
        if (!$this->service) {
            Log::error('Google Drive Service not initialized. Missing service account JSON.');
            return null;
        }

        try {
            // Generate a temporary access token for this specific file
            // Note: In a real production environment, you might want to use a proxy route
            // to keep the access token hidden from the frontend.
            $accessToken = $this->client->fetchAccessTokenWithAssertion()['access_token'];
            
            // Return the embed link with the access token
            // This is a simplified version. For better security, use the proxy route.
            return "https://www.googleapis.com/drive/v3/files/{$fileId}?alt=media&access_token={$accessToken}";
        } catch (\Exception $e) {
            Log::error('Google Drive Get URL Failed', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Proxy approach: Get the file stream from Google Drive
     */
    public function getFileStream($fileId)
    {
        try {
            $response = $this->service->files->get($fileId, ['alt' => 'media']);
            return $response->getBody();
        } catch (\Exception $e) {
            Log::error('Google Drive Stream Failed', ['error' => $e->getMessage()]);
            return null;
        }
    }
}
