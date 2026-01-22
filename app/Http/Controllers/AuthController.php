<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\University;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function showRegisterForm()
    {
        $universities = University::orderBy('name')->get();
        return view('auth.register', compact('universities'));
    }

    public function showTeacherRegisterForm()
    {
        $universities = University::orderBy('name')->get();
        return view('auth.register-teacher', compact('universities'));
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            
            if (Auth::user()->role === 'admin') {
                return redirect()->intended('/admin/dashboard');
            }

            if (method_exists(Auth::user(), 'isTeacher') && Auth::user()->isTeacher()) {
                return redirect()->intended(route('teacher.dashboard'));
            }

            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([
            'email' => 'بيانات الاعتماد المقدمة غير صحيحة.',
        ])->withInput();
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:6|confirmed',
            'university_id' => 'nullable|exists:universities,id',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => 'user',
            'university_id' => $request->university_id,
        ]);

        Auth::login($user);

        return redirect('/dashboard')->with('success', 'تم إنشاء الحساب بنجاح!');
    }

    public function registerTeacher(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:6|confirmed',
            'university_id' => 'nullable|exists:universities,id',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => 'teacher',
            'is_instructor' => 1,
            'university_id' => $request->university_id,
        ]);

        Auth::login($user);

        return redirect()->route('teacher.dashboard')->with('success', 'تم إنشاء حساب المعلم بنجاح!');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}

