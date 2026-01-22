<?php

namespace App\Services;

use Vimeo\Vimeo;
use Exception;

class VimeoService
{
    protected $vimeo;

    public function __construct()
    {
        $this->vimeo = new Vimeo(
            config('services.vimeo.client_id'),
            config('services.vimeo.client_secret'),
            config('services.vimeo.access_token')
        );
    }

    /**
     * Upload video to Vimeo
     *
     * @param string $filePath
     * @param string $title
     * @param string $description
     * @return array|null
     */
    public function uploadVideo($filePath, $title, $description = '')
    {
        try {
            // Upload the video
            $response = $this->vimeo->upload($filePath, [
                'name' => $title,
                'description' => $description,
                'privacy' => [
                    'view' => 'anybody', // Can be 'anybody', 'nobody', 'contacts', 'password', 'users', 'disable'
                    'embed' => 'public'  // Can be 'public', 'private'
                ]
            ]);

            if ($response) {
                // Extract video ID from URI (e.g., "/videos/123456789" -> "123456789")
                $videoId = str_replace('/videos/', '', $response);
                
                return [
                    'success' => true,
                    'video_id' => $videoId,
                    'video_uri' => $response,
                    'embed_url' => "https://player.vimeo.com/video/{$videoId}",
                    'watch_url' => "https://vimeo.com/{$videoId}"
                ];
            }

            return ['success' => false, 'error' => 'Failed to upload video'];

        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get video details from Vimeo
     *
     * @param string $videoId
     * @return array|null
     */
    public function getVideoDetails($videoId)
    {
        try {
            $response = $this->vimeo->request("/videos/{$videoId}");
            
            if ($response['status'] === 200) {
                $video = $response['body'];
                
                return [
                    'success' => true,
                    'video_id' => $videoId,
                    'title' => $video['name'],
                    'description' => $video['description'],
                    'duration' => $video['duration'], // in seconds
                    'embed_url' => "https://player.vimeo.com/video/{$videoId}",
                    'watch_url' => $video['link'],
                    'thumbnail' => $video['pictures']['sizes'][0]['link'] ?? null,
                    'status' => $video['status'] // 'available', 'uploading', 'transcoding', etc.
                ];
            }

            return ['success' => false, 'error' => 'Video not found'];

        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Delete video from Vimeo
     *
     * @param string $videoId
     * @return array
     */
    public function deleteVideo($videoId)
    {
        try {
            $response = $this->vimeo->request("/videos/{$videoId}", [], 'DELETE');
            
            if ($response['status'] === 204) {
                return ['success' => true];
            }

            return ['success' => false, 'error' => 'Failed to delete video'];

        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Update video details on Vimeo
     *
     * @param string $videoId
     * @param string $title
     * @param string $description
     * @return array
     */
    public function updateVideo($videoId, $title, $description = '')
    {
        try {
            $response = $this->vimeo->request("/videos/{$videoId}", [
                'name' => $title,
                'description' => $description
            ], 'PATCH');
            
            if ($response['status'] === 200) {
                return ['success' => true];
            }

            return ['success' => false, 'error' => 'Failed to update video'];

        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get embed HTML for video
     *
     * @param string $videoId
     * @param int $width
     * @param int $height
     * @return string
     */
    public function getEmbedHtml($videoId, $width = 640, $height = 360)
    {
        return "<iframe src=\"https://player.vimeo.com/video/{$videoId}\" width=\"{$width}\" height=\"{$height}\" frameborder=\"0\" allow=\"autoplay; fullscreen; picture-in-picture\" allowfullscreen></iframe>";
    }
}

