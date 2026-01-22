<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Http\Request;
use App\Http\Controllers\ChatController;
use App\Models\ChatRoom;

$userId = $argv[1] ?? 5; // teacher
$roomId = $argv[2] ?? 2;
$content = $argv[3] ?? 'Automated test message from teacher at ' . date('c');

$app['auth']->loginUsingId((int)$userId);
$chatRoom = ChatRoom::find((int)$roomId);
$controller = $app->make(ChatController::class);

$request = Request::create('/chat-rooms/'.$roomId.'/messages', 'POST', ['content' => $content]);
$response = $controller->store($request, $chatRoom);

echo "Response:\n";
try {
    echo $response->getContent() . "\n";
} catch (Exception $e) {
    echo "Cannot read response content: " . $e->getMessage() . "\n";
}

echo "\nCheck logs for 'Chat message stored' and 'Message broadcast attempted'.\n";
