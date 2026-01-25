<?php
// scripts/check_lessons.php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();
$db = $app->make('db');
$courseId = $argv[1] ?? 54;
$results = $db->select('SELECT id,title,section_id,video_path,video_url,created_at FROM lessons WHERE course_id = ? ORDER BY created_at DESC LIMIT 50', [$courseId]);
echo json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL;