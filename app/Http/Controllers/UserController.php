<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Purchase;
use App\Models\ServiceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function profile()
    {
        $user = Auth::user();
        return view('user.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
        ]);

        return back()->with('success', 'تم تحديث الملف الشخصي بنجاح!');
    }

    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'كلمة المرور الحالية غير صحيحة.']);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'تم تغيير كلمة المرور بنجاح!');
    }

    public function myCourses()
    {
        $user = Auth::user();
        $purchases = Purchase::with('course')
            ->where('user_id', $user->id)
            ->where('payment_status', 'completed')
            ->get();

        return view('user.my_courses', compact('purchases'));
    }

    public function myServiceRequests()
    {
        $user = Auth::user();
        $serviceRequests = ServiceRequest::with('serviceType')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('user.my_service_requests', compact('serviceRequests'));
    }

    public function paymentHistory()
    {
        $user = Auth::user();
        $purchases = Purchase::with('course')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('user.payment_history', compact('purchases'));
    }
}

