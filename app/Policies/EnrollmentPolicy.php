<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Course;
use Illuminate\Auth\Access\HandlesAuthorization;

class EnrollmentPolicy
{
    use HandlesAuthorization;

    /**
     * الإدارة يمكنها إدارة جميع الاشتراكات.
     * المعلم يمكنه إدارة اشتراكات طلابه في دوراته فقط.
     */
    public function manage(User $user, Course $course)
    {
        if ($user->isAdmin()) return true;
        
        return $user->isTeacher() && $course->user_id === $user->id;
    }

    /**
     * المعلم يمكنه فقط عرض قائمة الطلاب (بدون تعديل) إذا لم يكن صاحب الدورة (اختياري).
     * هنا سنقصرها على صاحب الدورة فقط.
     */
    public function viewList(User $user, Course $course)
    {
        if ($user->isAdmin()) return true;
        
        return $user->isTeacher() && $course->user_id === $user->id;
    }
}
