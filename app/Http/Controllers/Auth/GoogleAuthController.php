<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Exception;

class GoogleAuthController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Obtain the user information from Google.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            // البحث عن المستخدم بواسطة google_id أو البريد الإلكتروني
            $user = User::where('google_id', $googleUser->id)
                        ->orWhere('email', $googleUser->email)
                        ->first();

            if ($user) {
                // تحديث بيانات المستخدم إذا كان موجوداً مسبقاً
                $user->update([
                    'google_id' => $googleUser->id,
                    'avatar' => $user->avatar ?? $googleUser->avatar,
                ]);
            } else {
                // إنشاء مستخدم جديد (افتراضياً كطالب)
                $user = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'avatar' => $googleUser->avatar,
                    'role' => 'student', // القيمة الافتراضية للمستخدمين الجدد
                    'password' => null, // لا يوجد كلمة مرور لتسجيل الدخول عبر Google
                ]);
            }

            Auth::login($user);

            return $this->redirectBasedOnRole($user);

        } catch (Exception $e) {
            return redirect('/login')->with('error', 'حدث خطأ أثناء تسجيل الدخول عبر Google: ' . $e->getMessage());
        }
    }

    /**
     * إعادة التوجيه بناءً على دور المستخدم
     */
    protected function redirectBasedOnRole($user)
    {
        if ($user->isAdmin()) {
            return redirect()->intended('/admin/dashboard');
        } elseif ($user->isTeacher()) {
            return redirect()->intended('/teacher/dashboard');
        }

        return redirect()->intended('/student/dashboard');
    }
}
