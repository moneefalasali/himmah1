<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class ResetPasswordController extends Controller
{
    public function showResetForm(Request $request, $token)
    {
        $email = $request->query('email');
        return view('auth.passwords.reset', compact('token', 'email'));
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);
        $reset = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();
        if (!$reset || !Hash::check($request->token, $reset->token)) {
            return back()->withErrors(['email' => 'رابط الاستعادة غير صالح أو منتهي الصلاحية.']);
        }
        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();
        return redirect()->route('login')->with('status', 'تم تغيير كلمة المرور بنجاح. يمكنك الآن تسجيل الدخول.');
    }
}
