<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Http\Request;
use App\Http\Controllers\ChatController;
use App\Models\ChatRoom;

// authenticate as user id from argv or default 9
$userId = $argv[1] ?? 9;
$roomId = $argv[2] ?? 2;

// login as user
$app['auth']->loginUsingId((int)$userId);

$chatRoom = ChatRoom::find((int)$roomId);
$controller = $app->make(ChatController::class);

$request = Request::create('/debug', 'GET');
$response = $controller->messagesJson($request, $chatRoom);

// messagesJson returns a JsonResponse
echo $response->getContent();

