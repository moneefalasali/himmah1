<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Use the container to resolve the service
$svc = $app->make(App\Services\ChatService::class);

$roomId = $argv[1] ?? 2;
$forUserId = $argv[2] ?? 9;

$messages = $svc->getRoomMessages((int)$roomId, 50, (int)$forUserId);

echo json_encode(array_map(function($m){
    // normalize model to array
    if ($m instanceof Illuminate\Database\Eloquent\Model) return $m->toArray();
    return (array)$m;
}, $messages->items()), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);


