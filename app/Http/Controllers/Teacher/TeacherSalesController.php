<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Purchase;
use App\Models\Course;

class TeacherSalesController extends Controller
{
    public function index(Request $request)
    {
        $teacher = Auth::user();

        // Gather teacher's course ids
        $courseIds = Course::where('user_id', $teacher->id)->pluck('id')->toArray();

        // Enrolled students: union of completed purchases and manual course_user enrollments
        $purchasedUserIds = Purchase::whereIn('course_id', $courseIds)
            ->where('payment_status', 'completed')
            ->pluck('user_id')
            ->toArray();

        $manualUserIds = DB::table('course_user')->whereIn('course_id', $courseIds)->pluck('user_id')->toArray();

        $enrolledUserIds = array_values(array_unique(array_merge($purchasedUserIds, $manualUserIds)));

        // إحصائيات عامة للمعلم (مفلترة بحسب طلاب المعلم)
        $statsQuery = Sale::where('teacher_id', $teacher->id)->whereIn('course_id', $courseIds);
        if (!empty($enrolledUserIds)) {
            $statsQuery->whereIn('user_id', $enrolledUserIds);
        }

        $stats = [
            'total_revenue' => $statsQuery->sum('amount'),
            'teacher_earnings' => $statsQuery->sum('teacher_commission'),
            'total_sales_count' => $statsQuery->count(),
            'enrolled_count' => count($enrolledUserIds),
        ];

        // estimate manual enrollments earnings per course (if manual enrollment exists without purchase/sale)
        $manualEstimated = 0;
        $manualEstimatedGross = 0;
        $commissionRate = config('app.teacher_commission_rate', 0.5);
        $manualRows = DB::table('course_user')->whereIn('course_id', $courseIds)->get(['course_id','user_id']);
        foreach ($manualRows as $row) {
            $hasPurchase = Purchase::where('course_id', $row->course_id)->where('user_id', $row->user_id)->where('payment_status','completed')->exists();
            $hasSale = Sale::where('course_id', $row->course_id)->where('user_id', $row->user_id)->exists();
            if ($hasPurchase || $hasSale) continue;
            $course = Course::find($row->course_id);
            $price = $course ? (float) ($course->price ?? 0) : 0;
            if ($price <= 0) continue;
            $manualEstimated += $price * $commissionRate;
            $manualEstimatedGross += $price;
        }

        // include manual estimates into final stats totals
        $stats['manual_estimated_teacher_earnings'] = $manualEstimated;
        $stats['manual_estimated_gross'] = $manualEstimatedGross;
        $stats['total_revenue'] = ($stats['total_revenue'] ?? 0) + $manualEstimatedGross;
        $stats['teacher_earnings'] = ($stats['teacher_earnings'] ?? 0) + $manualEstimated;

        // المبيعات حسب الدورة (مفلترة بحسب طلاب المعلم)
        $salesByCourseQuery = Sale::where('teacher_id', $teacher->id)->whereIn('course_id', $courseIds);
        if (!empty($enrolledUserIds)) {
            $salesByCourseQuery->whereIn('user_id', $enrolledUserIds);
        }

        $salesByCourse = $salesByCourseQuery
            ->select('course_id', DB::raw('count(*) as total_sales'), DB::raw('sum(amount) as total_amount'), DB::raw('sum(teacher_commission) as teacher_profit'))
            ->with('course:id,title')
            ->groupBy('course_id')
            ->get();

        // قائمة المبيعات الأخيرة (مفلترة بحسب طلاب المعلم)
        $recentSalesQuery = Sale::where('teacher_id', $teacher->id)->whereIn('course_id', $courseIds);
        if (!empty($enrolledUserIds)) {
            $recentSalesQuery->whereIn('user_id', $enrolledUserIds);
        }

        $recentSales = $recentSalesQuery
            ->with(['course:id,title', 'student:id,name,email'])
            ->latest()
            ->paginate(15);

        // fetch enrolled students to show manual enrollments even when there are no sales
        $enrolledStudents = [];
        if (!empty($enrolledUserIds)) {
            $enrolledStudents = DB::table('users')
                ->select('id', 'name', 'email')
                ->whereIn('id', $enrolledUserIds)
                ->get();
        }

        return view('teacher.sales.index', compact('stats', 'salesByCourse', 'recentSales', 'enrolledStudents'));
    }
}
