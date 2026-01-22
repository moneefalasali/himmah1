<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Course;
use App\Models\Purchase;
use App\Models\Sale;
use Illuminate\Support\Str as SupportStr;

$apply = in_array('--apply', $argv);
$commissionRate = config('app.teacher_commission_rate', 0.5);

$rows = DB::table('course_user')->get(['course_id','user_id']);

$toCreate = [];
foreach ($rows as $r) {
    $courseId = $r->course_id;
    $userId = $r->user_id;
    $hasPurchase = Purchase::where('course_id', $courseId)->where('user_id', $userId)->where('payment_status','completed')->exists();
    $hasSale = Sale::where('course_id', $courseId)->where('user_id', $userId)->exists();
    if ($hasPurchase || $hasSale) continue;
    $course = Course::find($courseId);
    if (!$course) continue;
    $price = (float) ($course->price ?? 0);
    if ($price <= 0) continue;
    $toCreate[] = ['course_id'=>$courseId,'user_id'=>$userId,'price'=>$price, 'teacher_id'=>$course->user_id];
}

echo "Found " . count($toCreate) . " manual enrollments without purchase/sale.\n";
if (empty($toCreate)) exit(0);

foreach ($toCreate as $item) {
    echo "Course {$item['course_id']} - User {$item['user_id']} - Price {$item['price']}\n";
}

if (!$apply) {
    echo "\nDry-run mode. To apply changes run: php scripts/convert_manual_enrollments.php --apply\n";
    exit(0);
}

$createdPurchases = 0;
$createdSales = 0;

foreach ($toCreate as $item) {
    try {
        Purchase::create([
            'user_id' => $item['user_id'],
            'course_id' => $item['course_id'],
            'amount' => $item['price'],
            'payment_status' => 'completed',
            'payment_method' => 'manual-conversion',
            'transaction_id' => SupportStr::uuid(),
        ]);
        $createdPurchases++;
    } catch (\Throwable $e) {
        echo "Failed to create purchase for course {$item['course_id']} user {$item['user_id']}: {$e->getMessage()}\n";
        continue;
    }
    try {
        $teacherCommission = $item['price'] * $commissionRate;
        $adminCommission = max(0, $item['price'] - $teacherCommission);
        Sale::create([
            'course_id' => $item['course_id'],
            'user_id' => $item['user_id'],
            'teacher_id' => $item['teacher_id'],
            'amount' => $item['price'],
            'teacher_commission' => $teacherCommission,
            'admin_commission' => $adminCommission,
            'transaction_id' => SupportStr::uuid(),
        ]);
        $createdSales++;
    } catch (\Throwable $e) {
        echo "Failed to create sale for course {$item['course_id']} user {$item['user_id']}: {$e->getMessage()}\n";
    }
}

echo "Created purchases: {$createdPurchases}, sales: {$createdSales}\n";
