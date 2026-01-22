<?php

namespace App\Jobs;

use App\Models\Lesson;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;

class ProcessVideoHLS implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $lesson;
    public $timeout = 7200; // 2 hours

    public function __construct(Lesson $lesson)
    {
        $this->lesson = $lesson;
    }

    public function handle()
    {
        $this->lesson->update(['processing_status' => 'processing']);
        
        $rawDisk = Storage::disk('wasabi');
        $videoPath = $this->lesson->video_path;

        if (empty($videoPath)) {
            Log::error("ProcessVideoHLS: lesson {$this->lesson->id} has empty video_path");
            $this->lesson->update([
                'processing_status' => 'failed',
                'processing_error' => 'Missing video_path for processing',
            ]);
            return;
        }

        $localRawPath = storage_path('app/temp/' . basename($videoPath));

        if (!file_exists(dirname($localRawPath))) {
            mkdir(dirname($localRawPath), 0777, true);
        }

        // Download raw video from Wasabi to local for processing (use stream when available)
        try {
            if (! $rawDisk->exists($videoPath)) {
                Log::error("ProcessVideoHLS: source video not found on disk for lesson {$this->lesson->id}: {$videoPath}");
                $this->lesson->update([
                    'processing_status' => 'failed',
                    'processing_error' => 'Source video not found on storage',
                ]);
                return;
            }

            // Prefer streaming to avoid loading whole file into memory
            if (method_exists($rawDisk, 'readStream')) {
                $stream = $rawDisk->readStream($videoPath);
                if ($stream === false || $stream === null) {
                    // Fallback to get()
                    $content = $rawDisk->get($videoPath);
                    file_put_contents($localRawPath, $content);
                } else {
                    $out = fopen($localRawPath, 'w');
                    if ($out === false) {
                        throw new \RuntimeException('Unable to open local file for writing: ' . $localRawPath);
                    }
                    stream_copy_to_stream($stream, $out);
                    if (is_resource($stream)) fclose($stream);
                    if (is_resource($out)) fclose($out);
                }
            } else {
                $content = $rawDisk->get($videoPath);
                file_put_contents($localRawPath, $content);
            }
        } catch (\Throwable $e) {
            Log::error("ProcessVideoHLS: failed to retrieve video for lesson {$this->lesson->id}: " . $e->getMessage());
            $this->lesson->update([
                'processing_status' => 'failed',
                'processing_error' => Str::limit('Failed to retrieve source video: ' . $e->getMessage(), 1000),
            ]);
            return;
        }

        $outputFolder = storage_path('app/temp/hls/' . $this->lesson->id);
        if (!file_exists($outputFolder)) {
            mkdir($outputFolder, 0777, true);
        }

        try {
            // Check ffmpeg availability before attempting to transcode
            $check = new Process(['ffmpeg', '-version']);
            $check->run();
            if (! $check->isSuccessful()) {
                $err = trim($check->getErrorOutput() ?: $check->getOutput());
                Log::warning("ffmpeg not available: {$err}");
                $this->lesson->update([
                    'processing_status' => 'failed',
                    'processing_error' => Str::limit('ffmpeg not available: ' . $err, 1000),
                ]);
                return;
            }

            $this->transcode($localRawPath, $outputFolder);

            // Upload HLS files to Wasabi
            $this->uploadHlsToWasabi($outputFolder);

            $this->lesson->update([
                'processing_status' => 'completed',
                'hls_path' => 'hls/' . $this->lesson->id . '/master.m3u8'
            ]);

            // Cleanup
            $this->cleanup($localRawPath, $outputFolder);

        } catch (\Exception $e) {
            Log::error("Transcoding failed for lesson {$this->lesson->id}: " . $e->getMessage());
            $this->lesson->update([
                'processing_status' => 'failed',
                'processing_error' => Str::limit($e->getMessage(), 1000),
            ]);
        }
    }

    protected function transcode($input, $outputDir)
    {
        // FFmpeg command for multi-bitrate HLS
        // 360p, 720p, 1080p
        $command = [
            'ffmpeg', '-i', $input,
            '-filter_complex', '[0:v]split=3[v1][v2][v3]; [v1]scale=w=640:h=360[v1out]; [v2]scale=w=1280:h=720[v2out]; [v3]scale=w=1920:h=1080[v3out]',
            
            '-map', '[v1out]', '-c:v:0', 'libx264', '-b:v:0', '800k', '-maxrate:v:0', '856k', '-bufsize:v:0', '1200k',
            '-map', '[v2out]', '-c:v:1', 'libx264', '-b:v:1', '2800k', '-maxrate:v:1', '2996k', '-bufsize:v:1', '4200k',
            '-map', '[v3out]', '-c:v:2', 'libx264', '-b:v:2', '5000k', '-maxrate:v:2', '5350k', '-bufsize:v:2', '7500k',
            
            '-map', 'a:0', '-c:a', 'aac', '-b:a:0', '96k', '-ac', '2',
            '-map', 'a:0', '-c:a', 'aac', '-b:a:1', '128k', '-ac', '2',
            '-map', 'a:0', '-c:a', 'aac', '-b:a:2', '192k', '-ac', '2',
            
            '-f', 'hls', '-hls_time', '10', '-hls_playlist_type', 'vod',
            '-master_pl_name', 'master.m3u8',
            '-hls_segment_filename', "$outputDir/v%v/file%03d.ts",
            '-var_stream_map', 'v:0,a:0 v:1,a:1 v:2,a:2',
            "$outputDir/v%v/index.m3u8"
        ];

        $process = new Process($command);
        $process->setTimeout(7200);
        $process->mustRun();
    }

    protected function uploadHlsToWasabi($folder)
    {
        $disk = Storage::disk('wasabi');
        $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($folder));

        foreach ($files as $file) {
            if ($file->isDir()) continue;
            
            $relativePath = 'hls/' . $this->lesson->id . '/' . str_replace($folder . '/', '', $file->getPathname());
            $disk->put($relativePath, fopen($file->getPathname(), 'r'), 'private');
        }
    }

    protected function cleanup($localRaw, $hlsFolder)
    {
        if (file_exists($localRaw)) unlink($localRaw);
        $this->deleteDirectory($hlsFolder);
    }

    protected function deleteDirectory($dir) {
        if (!file_exists($dir)) return true;
        if (!is_dir($dir)) return unlink($dir);
        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..') continue;
            if (!$this->deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) return false;
        }
        return rmdir($dir);
    }
}
