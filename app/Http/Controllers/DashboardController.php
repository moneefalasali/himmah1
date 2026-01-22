<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Purchase;
use App\Models\ServiceRequest;
use App\Models\LearningProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();
        
        // إحصائيات المستخدم
        $totalCourses = Purchase::where('user_id', $user->id)
            ->where('payment_status', 'completed')
            ->count();
            
        $totalServiceRequests = ServiceRequest::where('user_id', $user->id)->count();
        
        $completedLessons = LearningProgress::where('user_id', $user->id)
            ->where('completed', true)
            ->count();
            
        // آخر الدورات المشتراة
        $recentCourses = Purchase::with('course')
            ->where('user_id', $user->id)
            ->where('payment_status', 'completed')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
            
        // آخر طلبات الخدمات
        $recentServiceRequests = ServiceRequest::with('serviceType')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('dashboard.index', compact(
            'totalCourses',
            'totalServiceRequests', 
            'completedLessons',
            'recentCourses',
            'recentServiceRequests'
        ));
    }
}

