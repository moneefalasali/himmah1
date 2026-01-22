<?php
namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class ImageService
{
    protected $disk;
    public function __construct()
    {
        // Do not resolve disk here to avoid throwing during container resolution.
        // We'll lazily resolve the filesystem when needed.
        $this->disk = null;
    }

    /**
     * Lazily resolve the filesystem disk instance.
     */
    protected function getDisk()
    {
        if ($this->disk instanceof \Illuminate\Contracts\Filesystem\Filesystem) {
            return $this->disk;
        }

        $default = config('filesystems.default');
        $s3Config = config('filesystems.disks.s3');

        // prefer s3 if configured with a driver
        if (is_array($s3Config) && !empty($s3Config) && !empty($s3Config['driver'])) {
            try {
                $this->disk = Storage::disk('s3');
                return $this->disk;
            } catch (\Exception $e) {
                \Log::warning('Failed to initialize s3 disk, falling back to default: ' . $e->getMessage());
            }
        }

        try {
            $this->disk = Storage::disk($default);
        } catch (\Exception $e) {
            \Log::warning('Failed to initialize default disk: ' . $e->getMessage());
            // As a last resort, use the local driver via facade without specifying disk
            $this->disk = Storage::disk('local');
        }

        return $this->disk;
    }

    /**
     * رفع الصورة إلى Wasabi بشكل Private
     */
    public function uploadImage(UploadedFile $file, $folder = 'general')
    {
        $filename = Str::random(40) . '.' . $file->getClientOriginalExtension();
        $path = "images/{$folder}/{$filename}";
        
        $disk = $this->getDisk();
        $stream = fopen($file->getRealPath(), 'r+');
        try {
            $disk->put($path, $stream, [
            'visibility' => 'private',
            'ContentType' => $file->getMimeType()
            ]);
        } catch (\Exception $e) {
            \Log::error('Image upload failed: ' . $e->getMessage());
            if (is_resource($stream)) {
                fclose($stream);
            }
            return null;
        }
        
        if (is_resource($stream)) {
            fclose($stream);
        }
        return $path;
    }

    /**
     * توليد رابط موقع (Signed URL) للصورة لضمان الحماية
     */
    public function getUrl($path, $expiresInMinutes = 60)
    {
        if (!$path) return asset('images/default-placeholder.png');

        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        try {
            $disk = $this->getDisk();
            if (method_exists($disk, 'temporaryUrl')) {
                return $disk->temporaryUrl(
                    $path,
                    now()->addMinutes($expiresInMinutes)
                );
            }
        } catch (\Exception $e) {
            \Log::error("Error generating signed URL: " . $e->getMessage());
        }

        return asset('images/default-placeholder.png');
    }

    public function deleteImage($path)
    {
        try {
            $disk = $this->getDisk();
            if ($path && $disk->exists($path)) {
                return $disk->delete($path);
            }
        } catch (\Exception $e) {
            \Log::warning('Failed to delete image: ' . $e->getMessage());
        }
        return false;
    }
}
