<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Sale;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TeacherDashboardController extends Controller
{
    public function index()
    {
        $teacher = Auth::user();

        // إجماليات مبسطة متوافقة مع view
        // Count unique students across completed purchases and manual enrollments (course_user)
        $courseIds = Course::where('user_id', $teacher->id)->pluck('id')->toArray();

        $purchaseStudentIds = [];
        if (!empty($courseIds)) {
            $purchaseStudentIds = 
                \DB::table('purchases')
                    ->whereIn('course_id', $courseIds)
                    ->where('payment_status', 'completed')
                    ->pluck('user_id')
                    ->toArray();
        }

        $enrolledStudentIds = [];
        if (!empty($courseIds)) {
            $enrolledStudentIds = \DB::table('course_user')
                ->whereIn('course_id', $courseIds)
                ->pluck('user_id')
                ->toArray();
        }

        $uniqueStudentIds = array_unique(array_merge($purchaseStudentIds, $enrolledStudentIds));
        $totalStudents = count($uniqueStudentIds);

        $totalEarnings = Sale::where('teacher_id', $teacher->id)->sum('teacher_commission');

        // الدورات الحديثة للعرض في الواجهة
        $courses = Course::where('user_id', $teacher->id)
            ->withCount('lessons')
            ->latest()
            ->take(6)
            ->get();

        // Aggregate counts for dashboard cards
        $totalCourses = Course::where('user_id', $teacher->id)->count();
        $publishedCourses = Course::where('user_id', $teacher->id)->where('status', 'active')->count();
        $totalLessons = 0;
        if (!empty($courseIds)) {
            $totalLessons = DB::table('lessons')->whereIn('course_id', $courseIds)->count();
        }

        // Attach per-course unique student counts (completed purchases + manual enrollments)
        foreach ($courses as $c) {
            $purchaseIds = \DB::table('purchases')
                ->where('course_id', $c->id)
                ->where('payment_status', 'completed')
                ->pluck('user_id')
                ->toArray();

            $enrolledIds = \DB::table('course_user')
                ->where('course_id', $c->id)
                ->pluck('user_id')
                ->toArray();

            $unique = array_unique(array_merge($purchaseIds, $enrolledIds));
            $c->students_count = count($unique);
        }

        // Log course student counts for debugging
        try {
            $counts = [];
            foreach ($courses as $c) {
                $counts[$c->id] = $c->students_count;
            }
            Log::info('teacher_dashboard_course_student_counts', ['teacher_id' => $teacher->id, 'counts' => $counts]);
        } catch (\Throwable $e) {
            Log::error('teacher_dashboard_logging_failed', ['error' => $e->getMessage()]);
        }

        // المبيعات الأخيرة لعرضها في الشريط الجانبي
        $recentSales = Sale::where('teacher_id', $teacher->id)
            ->with(['user'])
            ->latest()
            ->take(6)
            ->get();

        // بيانات الرسم البياني للمبيعات (آخر 30 يوم)
        $chartData = Sale::where('teacher_id', $teacher->id)
            ->where('created_at', '>=', now()->subDays(30))
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('sum(teacher_commission) as earnings'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('teacher.dashboard', compact('totalStudents', 'totalEarnings', 'courses', 'recentSales', 'chartData', 'totalCourses', 'publishedCourses', 'totalLessons'));
    }
}
