<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

// args: userId, roomId
$userId = $argv[1] ?? 9;
$roomId = $argv[2] ?? 2;

// login as user
$app['auth']->loginUsingId((int)$userId);

// channel name used by Echo.private('chat.{id}') is "private-chat.{id}"
$channel = "private-chat." . $roomId;

$request = Request::create('/broadcasting/auth', 'POST', [
    'socket_id' => '1234.1',
    'channel_name' => $channel,
]);

// set XHR header so controller treats it like an ajax auth
$request->headers->set('X-Requested-With', 'XMLHttpRequest');

// ensure request->user() returns our logged-in user
$request->setUserResolver(function () use ($app) {
    return $app['auth']->user();
});

$httpKernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $httpKernel->handle($request);

echo "Status: " . $response->getStatusCode() . "\n";
echo $response->getContent() . "\n";

$httpKernel->terminate($request, $response);
