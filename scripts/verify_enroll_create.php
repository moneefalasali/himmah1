<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Purchase;
use App\Models\Sale;

$courseId = $argv[1] ?? 4;
$users = $argv[2] ?? '8,9';
$userIds = array_map('intval', explode(',', $users));

$purchases = Purchase::where('course_id', $courseId)->whereIn('user_id', $userIds)->get();
$sales = Sale::where('course_id', $courseId)->whereIn('user_id', $userIds)->get();

echo "Purchases:\n";
echo $purchases->toJson(JSON_PRETTY_PRINT) . "\n";
echo "Sales:\n";
echo $sales->toJson(JSON_PRETTY_PRINT) . "\n";
