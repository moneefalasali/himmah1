<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Course;
use App\Models\Purchase;
use App\Models\Sale;
use Illuminate\Support\Facades\DB;

$teacherId = $argv[1] ?? 5;
$courseIds = Course::where('user_id', $teacherId)->pluck('id')->toArray();
$purchased = Purchase::whereIn('course_id', $courseIds)->where('payment_status', 'completed')->pluck('user_id')->toArray();
$manual = DB::table('course_user')->whereIn('course_id', $courseIds)->pluck('user_id')->toArray();
$enrolled = array_values(array_unique(array_merge($purchased, $manual)));
$sales_sum = Sale::where('teacher_id', $teacherId)->whereIn('course_id', $courseIds)->sum('amount');
$teacher_commission_sum = Sale::where('teacher_id', $teacherId)->whereIn('course_id', $courseIds)->sum('teacher_commission');

echo json_encode([
    'teacher_id' => (int)$teacherId,
    'course_ids' => $courseIds,
    'purchased' => $purchased,
    'manual' => $manual,
    'enrolled' => $enrolled,
    'sales_sum' => (float)$sales_sum,
    'teacher_commission_sum' => (float)$teacher_commission_sum,
], JSON_PRETTY_PRINT);

echo PHP_EOL;

// compute manual estimates similar to controller
$manualEstimatedGross = 0;
$manualEstimatedTeacherShare = 0;
$commissionRate = config('app.teacher_commission_rate', 0.5);
$manualRows = DB::table('course_user')->whereIn('course_id', $courseIds)->get(['course_id','user_id']);
foreach ($manualRows as $row) {
    $hasPurchase = Purchase::where('course_id', $row->course_id)->where('user_id', $row->user_id)->where('payment_status', 'completed')->exists();
    $hasSale = Sale::where('course_id', $row->course_id)->where('user_id', $row->user_id)->exists();
    if ($hasPurchase || $hasSale) continue;
    $course = Course::find($row->course_id);
    $price = $course ? (float) ($course->price ?? 0) : 0;
    if ($price <= 0) continue;
    $manualEstimatedGross += $price;
    $manualEstimatedTeacherShare += $price * $commissionRate;
}

echo "manual_estimated_gross: {$manualEstimatedGross}\n";
echo "manual_estimated_teacher_share: {$manualEstimatedTeacherShare}\n";
echo "total_with_estimates_gross: " . (($sales_sum ?? 0) + $manualEstimatedGross) . "\n";
echo "teacher_share_with_estimates: " . (($teacher_commission_sum ?? 0) + $manualEstimatedTeacherShare) . "\n";
