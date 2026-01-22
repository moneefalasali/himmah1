<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeacherSubscriptionController extends Controller
{
    public function index(Request $request)
    {
        $teacher = Auth::user();

        // Load teacher courses with enrolled students (pivot data)
        $courses = $teacher->teacherCourses()->with(['students' => function($q) {
            $q->withPivot(['subscription_start','subscription_end','status'])->orderBy('course_user.created_at','desc');
        }])->get();

        return view('teacher.subscriptions.index', compact('courses'));
    }
}
