<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Course;
use App\Models\Purchase;
use App\Models\ServiceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    /**
     * Show the admin dashboard.
     */
    public function dashboard()
    {
        // إحصائيات عامة
        $totalUsers = User::where('role', 'user')->count();
        $totalStudents = $totalUsers;
        $totalTeachers = User::where('role', 'teacher')->count();
        $totalCourses = Course::count();
        $totalRevenue = Purchase::where('payment_status', 'completed')->sum('amount');
        $pendingServiceRequests = ServiceRequest::where('status', 'pending')->count();

        // إحصائيات الشهر الحالي
        $currentMonth = now()->startOfMonth();
        $newUsersThisMonth = User::where('role', 'user')
            ->where('created_at', '>=', $currentMonth)
            ->count();
        $revenueThisMonth = Purchase::where('payment_status', 'completed')
            ->where('created_at', '>=', $currentMonth)
            ->sum('amount');
        $newServiceRequestsThisMonth = ServiceRequest::where('created_at', '>=', $currentMonth)
            ->count();

        // أحدث المشتريات
        $recentPurchases = Purchase::with(['user', 'course'])
            ->where('payment_status', 'completed')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // أحدث طلبات الخدمات
        $recentServiceRequests = ServiceRequest::with(['user', 'serviceType'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // إحصائيات الدورات الأكثر مبيعاً
        $topCourses = Course::withCount(['purchases' => function ($query) {
                $query->where('payment_status', 'completed');
            }])
            ->orderBy('purchases_count', 'desc')
            ->limit(5)
            ->get();

        // إحصائيات المبيعات الشهرية (آخر 6 أشهر)
        $monthlySales = Purchase::select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(amount) as total'),
                DB::raw('COUNT(*) as count')
            )
            ->where('payment_status', 'completed')
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        // Recent activities: combine purchases, registrations, and service requests
        $recentActivities = collect();

        foreach ($recentPurchases as $p) {
            $recentActivities->push((object)[
                'created_at' => $p->created_at,
                'description' => ($p->user->name ?? 'مستخدم') . " اشترى " . ($p->course->title ?? 'كورًس') . " — " . number_format($p->amount, 2) . " ر.س",
            ]);
        }

        $recentUsers = User::where('role', 'user')->orderBy('created_at', 'desc')->limit(5)->get();
        foreach ($recentUsers as $u) {
            $recentActivities->push((object)[
                'created_at' => $u->created_at,
                'description' => ($u->name ?? $u->email) . " سجل كمستخدم جديد",
            ]);
        }

        foreach ($recentServiceRequests as $r) {
            $recentActivities->push((object)[
                'created_at' => $r->created_at,
                'description' => ($r->user->name ?? 'مستخدم') . " طلب خدمة: " . ($r->serviceType->name ?? $r->service_type_id),
            ]);
        }

        // sort by created_at desc and take latest 6
        $recentActivities = $recentActivities->sortByDesc('created_at')->values()->take(6);

        $totalSales = Purchase::where('payment_status', 'completed')->count();

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalStudents',
            'totalTeachers',
            'totalCourses',
            'totalRevenue',
            'totalSales',
            'pendingServiceRequests',
            'newUsersThisMonth',
            'revenueThisMonth',
            'newServiceRequestsThisMonth',
            'recentPurchases',
            'recentServiceRequests',
            'recentActivities',
            'topCourses',
            'monthlySales'
        ));
    }

    /**
     * Backwards-compatible index route for admin dashboard.
     */
    public function index(Request $request)
    {
        return $this->dashboard($request ?? null);
    }

    /**
     * Show system statistics.
     */
public function statistics()
{
    // إحصائيات مفصلة
    $stats = [
        'users' => [
            'total' => User::where('role', 'user')->count(),
            'active_today' => User::where('role', 'user')
                ->where('updated_at', '>=', now()->startOfDay())
                ->count(),
            'new_this_week' => User::where('role', 'user')
                ->where('created_at', '>=', now()->startOfWeek())
                ->count(),
            'new_this_month' => User::where('role', 'user')
                ->where('created_at', '>=', now()->startOfMonth())
                ->count(),
        ],
        'courses' => [
            'total' => Course::count(),
            'active' => Course::where('status', 'active')->count(),
            'inactive' => Course::where('status', 'inactive')->count(),
            'total_lessons' => DB::table('lessons')->count(),
        ],
        'sales' => [
            'total_revenue' => Purchase::where('payment_status', 'completed')->sum('amount'),
            'total_sales' => Purchase::where('payment_status', 'completed')->count(),
            'pending_payments' => Purchase::where('payment_status', 'pending')->count(),
            'failed_payments' => Purchase::where('payment_status', 'failed')->count(),
            'revenue_this_month' => Purchase::where('payment_status', 'completed')
                ->where('created_at', '>=', now()->startOfMonth())
                ->sum('amount'),
            'sales_this_month' => Purchase::where('payment_status', 'completed')
                ->where('created_at', '>=', now()->startOfMonth())
                ->count(),
        ],
        'services' => [
            'total_requests' => ServiceRequest::count(),
            'pending' => ServiceRequest::where('status', 'pending')->count(),
            'in_progress' => ServiceRequest::where('status', 'in_progress')->count(),
            'completed' => ServiceRequest::where('status', 'completed')->count(),
            'cancelled' => ServiceRequest::where('status', 'cancelled')->count(),
        ],
    ];

    $newUsersThisMonth = $stats['users']['new_this_month'];
    $salesThisMonth = $stats['sales']['sales_this_month'];

    $conversionRate = $newUsersThisMonth > 0 ? ($salesThisMonth / $newUsersThisMonth) * 100 : 0;

    // بيانات أداء الدورات (عدد المبيعات، الإيرادات، معدل الإكمال، التقييم)
    $coursesPerformance = Course::withCount(['purchases' => function ($query) {
            $query->where('payment_status', 'completed');
        }])
        ->withAvg('reviews', 'rating') // لو عندك تقييمات، وإلا احذف هذا السطر
        ->get()
        ->map(function ($course) {
            // لا حساب معدل الإكمال لتجنب الخطأ
            $completionRate = 0;

            $revenue = Purchase::where('course_id', $course->id)
                ->where('payment_status', 'completed')
                ->sum('amount');

            return (object)[
                'title' => $course->title,
                'sales_count' => $course->purchases_count,
                'revenue' => $revenue,
                'completion_rate' => $completionRate,
'average_rating' => round($course->reviews_avg_rating ?? 0, 2),
            ];
        });

    // بيانات الإيرادات الشهرية - آخر 6 أشهر
    $monthlySales = Purchase::select(
            DB::raw('YEAR(created_at) as year'),
            DB::raw('MONTH(created_at) as month'),
            DB::raw('SUM(amount) as total'),
            DB::raw('COUNT(*) as count')
        )
        ->where('payment_status', 'completed')
        ->where('created_at', '>=', now()->subMonths(6))
        ->groupBy('year', 'month')
        ->orderBy('year')
        ->orderBy('month')
        ->get();

    // تجهيز بيانات الرسم البياني (التسميات والبيانات)
    $revenueLabels = $monthlySales->map(function ($item) {
        return $item->year . '-' . str_pad($item->month, 2, '0', STR_PAD_LEFT);
    });

    $revenueData = $monthlySales->pluck('total');

    return view('admin.statistics', compact(
        'stats',
        'conversionRate',
        'coursesPerformance',
        'revenueLabels',
        'revenueData'
    ));
}
}

