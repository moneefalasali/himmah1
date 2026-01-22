<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ImageService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    public function profile()
    {
        $user = Auth::user();
        $user->avatar_url = $this->imageService->getUrl($user->avatar);
        return view('user.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->only('name');

        if ($request->hasFile('avatar')) {
            // حذف الصورة القديمة إذا وجدت
            if ($user->avatar) {
                $this->imageService->deleteImage($user->avatar);
            }
            // رفع الصورة الجديدة
            $data['avatar'] = $this->imageService->uploadImage($request->file('avatar'), 'avatars');
        }

        $user->update($data);

        return back()->with('success', 'تم تحديث الملف الشخصي بنجاح');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if (!Hash::check($request->current_password, Auth::user()->password)) {
            return back()->withErrors(['current_password' => 'كلمة المرور الحالية غير صحيحة']);
        }

        Auth::user()->update([
            'password' => Hash::make($request->password)
        ]);

        return back()->with('success', 'تم تغيير كلمة المرور بنجاح');
    }

    public function myCourses()
    {
        $user = Auth::user();

        // Purchases that are completed
        $purchases = $user->purchases()
            ->with('course')
            ->where('payment_status', 'completed')
            ->orderByDesc('created_at')
            ->get();

        // Also include courses from the pivot table (course_user) in case purchases weren't created
        $enrolled = $user->enrolledCourses()->wherePivot('status', 'active')->get();

        $existingCourseIds = $purchases->pluck('course_id')->toArray();

        foreach ($enrolled as $course) {
            if (! in_array($course->id, $existingCourseIds)) {
                // create an in-memory Purchase object so the view can render uniformly
                $fake = new \App\Models\Purchase([
                    'user_id' => $user->id,
                    'course_id' => $course->id,
                    'amount' => $course->price ?? 0,
                    'payment_status' => 'completed',
                    'payment_method' => 'enrolled_via_admin',
                    'transaction_id' => null,
                ]);
                $fake->setRelation('course', $course);
                $purchases->push($fake);
            }
        }

        return view('user.my_courses', compact('purchases'));
    }

    /**
     * Show payment history for authenticated user.
     */
    public function paymentHistory()
    {
        $user = Auth::user();
        $purchases = \App\Models\Purchase::with('course')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('payment.history', compact('purchases'));
    }
}
