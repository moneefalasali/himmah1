<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $svc = $app->make(App\Services\VideoService::class);
    $res = $svc->initiateMultipartUpload('test_presign.mp4', 'video/mp4');
    print_r($res);
} catch (Exception $e) {
    echo "ERR: " . $e->getMessage() . PHP_EOL;
}
