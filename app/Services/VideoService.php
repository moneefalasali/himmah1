<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\Lesson;
use Aws\S3\S3Client;

class VideoService
{
    protected $disk;
    protected $s3Client;
    protected $bucket;

    public function __construct()
    {
        $this->disk = Storage::disk('wasabi');
        $this->bucket = env('WASABI_BUCKET');
        // Initialize S3 Client for Presigned URLs (build config defensively)
        $region = env('WASABI_DEFAULT_REGION', 'us-east-1');
        $endpoint = env('WASABI_ENDPOINT');
        $accessKey = env('WASABI_ACCESS_KEY_ID');
        $secretKey = env('WASABI_SECRET_ACCESS_KEY');

        $s3Config = [
            'version' => 'latest',
            'region' => $region,
            'use_path_style_endpoint' => env('WASABI_USE_PATH_STYLE_ENDPOINT', true),
        ];

        if (!empty($endpoint)) {
            $s3Config['endpoint'] = $endpoint;
        }

        if (!empty($accessKey) && !empty($secretKey)) {
            $s3Config['credentials'] = [
                'key' => $accessKey,
                'secret' => $secretKey,
            ];
        } else {
            // Log a clear warning so operator can fix .env; do not pass an invalid credentials container
            \Illuminate\Support\Facades\Log::warning('Wasabi S3 credentials missing: please set WASABI_ACCESS_KEY_ID and WASABI_SECRET_ACCESS_KEY in .env');
        }

        $this->s3Client = new S3Client($s3Config);
    }

    /**
     * Generate Presigned URLs for Multipart Upload
     */
    public function initiateMultipartUpload($filename, $contentType = 'video/mp4')
    {
        $key = 'raw/' . time() . '_' . preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $filename);
        
        try {
            $result = $this->s3Client->createMultipartUpload([
                'Bucket'      => $this->bucket,
                'Key'         => $key,
                'ACL'         => 'private',
                'ContentType' => $contentType,
            ]);

            return [
                'uploadId' => $result['UploadId'],
                'key'      => $key,
            ];
        } catch (\Exception $e) {
            Log::error('Wasabi Multipart Initiation Failed', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function getPresignedPartUrl($key, $uploadId, $partNumber)
    {
        $command = $this->s3Client->getCommand('UploadPart', [
            'Bucket'     => $this->bucket,
            'Key'        => $key,
            'UploadId'   => $uploadId,
            'PartNumber' => $partNumber,
        ]);

        return (string) $this->s3Client->createPresignedRequest($command, '+60 minutes')->getUri();
    }

    public function completeMultipartUpload($key, $uploadId, $parts)
    {
        try {
            return $this->s3Client->completeMultipartUpload([
                'Bucket'          => $this->bucket,
                'Key'             => $key,
                'UploadId'        => $uploadId,
                'MultipartUpload' => ['Parts' => $parts],
            ]);
        } catch (\Exception $e) {
            Log::error('Wasabi Multipart Completion Failed', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Generate Signed URL for Wasabi HLS/Video
     */
    public function getWasabiSignedUrl($path, $expiresInMinutes = 20)
    {
        if (empty($path)) return null;
        
        try {
            return $this->disk->temporaryUrl(
                $path,
                now()->addMinutes($expiresInMinutes)
            );
        } catch (\Exception $e) {
            $command = $this->s3Client->getCommand('GetObject', [
                'Bucket' => $this->bucket,
                'Key'    => $path,
            ]);
            return (string) $this->s3Client->createPresignedRequest($command, "+{$expiresInMinutes} minutes")->getUri();
        }
    }

    /* --- Keep legacy methods for compatibility if needed, but marked as deprecated --- */
    public function handleChunkUpload($file, $filename, $chunkIndex, $totalChunks) { return false; }
    public function uploadRawToWasabi($localPath, $lessonId) { return null; }
}
