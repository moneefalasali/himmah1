<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Policies\EnrollmentPolicy;
use App\Models\Purchase;
use App\Models\Sale;
use Illuminate\Support\Str as SupportStr;

class AdminEnrollmentController extends Controller
{
    public function index(Course $course)
    {
        $this->authorize('viewList', $course);
        
        $students = $course->students()->paginate(20);
        return view('admin.enrollments.index', compact('course', 'students'));
    }

    // Overview: list courses with enrollment counts
    public function overview()
    {
        // admin middleware already restricts access; avoid calling a non-existing policy method
        $courses = Course::withCount('students')->latest()->paginate(20);
        return view('admin.enrollments.overview', compact('courses'));
    }

    public function updateStatus(Request $request, Course $course, User $student)
    {
        $this->authorize('manage', $course);

        $validated = $request->validate([
            'status' => 'required|in:active,suspended,expired',
            'subscription_end' => 'nullable|date'
        ]);

        // Safely fetch existing pivot data (student may not be loaded via the relation)
        $existing = $course->students()->where('user_id', $student->id)->first();
        $currentSubscriptionEnd = null;
        if ($existing && isset($existing->pivot)) {
            $currentSubscriptionEnd = $existing->pivot->subscription_end ?? null;
        }

        $course->students()->updateExistingPivot($student->id, [
            'status' => $validated['status'],
            'subscription_end' => $validated['subscription_end'] ?? $currentSubscriptionEnd
        ]);

        return back()->with('success', 'تم تحديث حالة الاشتراك بنجاح.');
    }

    public function enroll(Request $request, Course $course)
    {
        $this->authorize('manage', $course);

        $validated = $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'email' => 'nullable|email',
            'name' => 'nullable|string|max:255',
            'duration_months' => 'required|integer|min:1'
        ]);

        // determine user
        if (!empty($validated['user_id'])) {
            $user = User::find($validated['user_id']);
            $created = false;
        } elseif (!empty($validated['email'])) {
            $user = User::where('email', $validated['email'])->first();
            if (!$user) {
                $user = User::create([
                    'name' => $validated['name'] ?? explode('@', $validated['email'])[0],
                    'email' => $validated['email'],
                    'password' => bcrypt(Str::random(12)),
                    'role' => 'student'
                ]);
                $created = true;
            } else {
                $created = false;
            }
        } else {
            return back()->with('error', 'الرجاء تحديد User ID أو بريد إلكتروني صالح.');
        }

        $course->students()->syncWithoutDetaching([
            $user->id => [
                'subscription_start' => now(),
                'subscription_end' => now()->addMonths($validated['duration_months']),
                'status' => 'active'
            ]
        ]);

        // If there is no purchase or sale for this user+course, create completed Purchase and Sale records
        try {
            $existingPurchase = Purchase::where('course_id', $course->id)->where('user_id', $user->id)->where('payment_status', 'completed')->exists();
            $existingSale = Sale::where('course_id', $course->id)->where('user_id', $user->id)->exists();

            $price = (float) ($course->price ?? 0);
            if (!$existingPurchase) {
                Purchase::create([
                    'user_id' => $user->id,
                    'course_id' => $course->id,
                    'amount' => $price,
                    'payment_status' => 'completed',
                    'payment_method' => 'manual',
                    'transaction_id' => SupportStr::uuid(),
                ]);
            }

            if (!$existingSale) {
                $commissionRate = config('app.teacher_commission_rate', 0.5);
                $teacherCommission = $price * $commissionRate;
                $adminCommission = max(0, $price - $teacherCommission);

                Sale::create([
                    'course_id' => $course->id,
                    'user_id' => $user->id,
                    'teacher_id' => $course->user_id,
                    'amount' => $price,
                    'teacher_commission' => $teacherCommission,
                    'admin_commission' => $adminCommission,
                    'transaction_id' => SupportStr::uuid(),
                ]);
            }
        } catch (\Throwable $e) {
            // don't block enrollment on DB errors; log if possible
            logger()->error('enroll_manual_create_purchase_sale_failed', ['error' => $e->getMessage(), 'course_id' => $course->id, 'user_id' => $user->id]);
        }

        $msg = $created ? 'تم إنشاء مستخدم جديد وتسجيله في الدورة.' : 'تم تسجيل المستخدم في الدورة بنجاح.';
        return back()->with('success', $msg);
    }
}
