<?php
namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TeacherStudentsController extends Controller
{
    public function index(Request $request)
    {
        // Minimal implementation: return an empty list for now to avoid missing route error.
        $students = collect();

        return view('teacher.students', compact('students'));
    }
}
