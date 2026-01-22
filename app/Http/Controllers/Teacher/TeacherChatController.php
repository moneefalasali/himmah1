<?php
namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Course;

class TeacherChatController extends Controller
{
    public function index(Request $request)
    {
        $teacher = Auth::user();

        // Load teacher courses to show course-chat links
        $courses = Course::where('user_id', $teacher->id)->orderBy('created_at','desc')->get();

        return view('teacher.chats.index', compact('courses'));
    }
}
