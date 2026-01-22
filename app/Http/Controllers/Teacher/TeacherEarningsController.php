<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Purchase;
use App\Models\Course;
use App\Models\Sale;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TeacherEarningsController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // Gather purchases for teacher courses
        $courseIds = Course::where('user_id', $user->id)->pluck('id')->toArray();

        // Collect enrolled student ids: union of completed purchases and manual course_user enrollments
        $purchasedUserIds = Purchase::whereIn('course_id', $courseIds)
            ->where('payment_status', 'completed')
            ->pluck('user_id')
            ->toArray();

        $manualUserIds = DB::table('course_user')->whereIn('course_id', $courseIds)->pluck('user_id')->toArray();

        $enrolledUserIds = array_values(array_unique(array_merge($purchasedUserIds, $manualUserIds)));

        // Get sales for teacher's courses (don't restrict by enrolled user IDs here)
        $sales = Sale::where('teacher_id', $user->id)
            ->whereIn('course_id', $courseIds)
            ->get();

        // If sales exist use them; otherwise fallback to purchases sums
        if ($sales->isNotEmpty()) {
            $totalGross = $sales->sum(function($s){ return $s->amount ?? 0; });
            $platformFees = $sales->sum(function($s){ return $s->admin_commission ?? 0; });
            $teacherShare = $sales->sum(function($s){ return $s->teacher_commission ?? 0; });

            // monthly data from sales' teacher_commission
            $labels = [];
            $data = [];
            for ($i = 5; $i >= 0; $i--) {
                $dt = now()->subMonths($i);
                $labels[] = $dt->format('M Y');
                $monthTotal = $sales->filter(function($s) use ($dt) {
                    return optional($s->created_at)->format('Y-m') === $dt->format('Y-m');
                })->sum(function($s){ return $s->teacher_commission ?? 0; });
                $data[] = $monthTotal;
            }
        } else {
            // fallback: sum completed purchases and apply commission rate
            $purchases = Purchase::whereIn('course_id', $courseIds)
                ->where('payment_status', 'completed')
                ->get();

            $totalGross = $purchases->sum(function($p){ return $p->amount ?? 0; });
            // platform fee unknown on purchases; assume 0
            $platformFees = 0;
            $commissionRate = 0.5;
            $teacherShare = ($totalGross - $platformFees) * $commissionRate;

            $labels = [];
            $data = [];
            for ($i = 5; $i >= 0; $i--) {
                $dt = now()->subMonths($i);
                $labels[] = $dt->format('M Y');
                $monthTotal = $purchases->filter(function($p) use ($dt) {
                    return optional($p->created_at)->format('Y-m') === $dt->format('Y-m');
                })->sum(function($p){ return $p->amount ?? 0; });
                $data[] = $monthTotal * $commissionRate;
            }
        }

        // Debug log to help diagnose zero-earnings for manually-enrolled students
        try {
            Log::info('teacher_earnings_debug', [
                'teacher_id' => $user->id,
                'course_ids' => $courseIds,
                'purchased_user_ids' => $purchasedUserIds ?? [],
                'manual_user_ids' => $manualUserIds ?? [],
                'enrolled_user_ids' => $enrolledUserIds ?? [],
                'sales_count' => isset($sales) && is_iterable($sales) ? count($sales) : 0,
                'total_gross' => $totalGross ?? 0,
                'platform_fees' => $platformFees ?? 0,
                'teacher_share' => $teacherShare ?? 0,
            ]);
        } catch (\Throwable $e) {
            // ignore logging failures
        }

        // provide enrolled students list and counts to the view so it can display manual enrollments
        $enrolledCount = count($enrolledUserIds);
        $enrolledStudents = [];
        if (!empty($enrolledUserIds)) {
            $enrolledStudents = DB::table('users')
                ->select('id','name','email')
                ->whereIn('id', $enrolledUserIds)
                ->get();
        }

        // Calculate estimated earnings for manual enrollments (no purchase/sale record for that course+user)
        $manualEstimatedGross = 0;
        $manualEstimatedTeacherShare = 0;
        $commissionRate = config('app.teacher_commission_rate', 0.5);

        // get manual enrollment rows for teacher courses
        $manualRows = DB::table('course_user')
            ->whereIn('course_id', $courseIds)
            ->get(['course_id', 'user_id']);

        foreach ($manualRows as $row) {
            // if there is a completed purchase or sale for this course+user, skip (not manual-only)
            $hasPurchase = Purchase::where('course_id', $row->course_id)
                ->where('user_id', $row->user_id)
                ->where('payment_status', 'completed')
                ->exists();

            $hasSale = Sale::where('course_id', $row->course_id)
                ->where('user_id', $row->user_id)
                ->exists();

            if ($hasPurchase || $hasSale) {
                continue;
            }

            // fetch course price
            $course = Course::find($row->course_id);
            $price = $course ? (float) ($course->price ?? 0) : 0;
            if ($price <= 0) {
                continue; // nothing to estimate
            }

            $manualEstimatedGross += $price;
            $manualEstimatedTeacherShare += $price * $commissionRate;
        }

        // add manual estimates to totals and treat them as final totals
        $totalGrossWithEstimates = ($totalGross ?? 0) + $manualEstimatedGross;
        $teacherShareWithEstimates = ($teacherShare ?? 0) + $manualEstimatedTeacherShare;

        // override totals so views display final numbers (including manual enrollments)
        $totalGross = $totalGrossWithEstimates;
        $teacherShare = $teacherShareWithEstimates;

        $includesManualEstimates = true;

        return view('teacher.earnings.index', compact('totalGross','platformFees','teacherShare','labels','data','enrolledCount','enrolledStudents','includesManualEstimates'));
    }
}
