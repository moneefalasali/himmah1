<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$id = $argv[1] ?? 42;
use App\Models\Lesson;
use Illuminate\Support\Facades\Storage;

$l = Lesson::find($id);
if (! $l) {
    echo json_encode(['error' => "Lesson $id not found"], JSON_PRETTY_PRINT) . PHP_EOL;
    exit(1);
}

$output = [
    'id' => $l->id,
    'title' => $l->title,
    'video_path' => $l->video_path,
    'hls_path' => $l->hls_path,
    'video_platform' => $l->video_platform ?? null,
    'processing_status' => $l->processing_status,
    'processing_error' => $l->processing_error,
    'created_at' => (string) $l->created_at,
    'updated_at' => (string) $l->updated_at,
];

try {
    $output['video_exists'] = $l->video_path ? (bool) Storage::disk('wasabi')->exists($l->video_path) : false;
} catch (Throwable $e) {
    $output['video_exists_error'] = $e->getMessage();
}
try {
    $output['hls_exists'] = $l->hls_path ? (bool) Storage::disk('wasabi')->exists($l->hls_path) : false;
    $output['hls_url'] = $l->hls_path ? Storage::disk('wasabi')->url($l->hls_path) : null;
} catch (Throwable $e) {
    $output['hls_exists_error'] = $e->getMessage();
}

echo json_encode($output, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL;
