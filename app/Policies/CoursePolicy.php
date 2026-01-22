<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CoursePolicy
{
    use HandlesAuthorization;

    /**
     * تحديد ما إذا كان المستخدم مسؤولاً (Admin) لتجاوز التحقق.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function before(User $user, $ability)
    {
        // Allow admins to bypass policy checks
        if (method_exists($user, 'isAdmin') && $user->isAdmin()) {
            return true;
        }
    }

    /**
     * تحديد ما إذا كان المستخدم يستطيع عرض قائمة الكورسات.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        // يمكن لأي مستخدم مسجل الدخول (سواء معلم أو مسؤول) عرض قائمة الكورسات الخاصة به أو كلها
        return true;
    }

    /**
     * تحديد ما إذا كان المستخدم يستطيع عرض كورس معين.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Course  $course
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Course $course)
    {
        // يمكن للمسؤول رؤية الكل (تم التعامل معها في before)
        // يمكن للمعلم رؤية كورساته
        return $user->id === $course->user_id;
    }

    /**
     * تحديد ما إذا كان المستخدم يستطيع إنشاء كورس جديد.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        // يمكن للمعلم إنشاء كورسات
        return $user->role === 'teacher'; // نفترض وجود حقل role
    }

    /**
     * تحديد ما إذا كان المستخدم يستطيع تحديث كورس معين.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Course  $course
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Course $course)
    {
        // يمكن للمسؤول التحديث (تم التعامل معها في before)
        // يمكن للمعلم تحديث كورساته فقط
        return $user->id === $course->user_id;
    }

    /**
     * تحديد ما إذا كان المستخدم يستطيع حذف كورس معين.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Course  $course
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Course $course)
    {
        // يمكن للمسؤول الحذف (تم التعامل معها في before)
        // يمكن للمعلم حذف كورساته فقط
        return $user->id === $course->user_id;
    }

    /**
     * Determine whether the user can manage enrollments for the course.
     */
    public function manage(User $user, Course $course)
    {
        if (method_exists($user, 'isAdmin') && $user->isAdmin()) return true;
        return $user->isTeacher() && $course->user_id === $user->id;
    }

    /**
     * Determine whether the user can view the student list for the course.
     */
    public function viewList(User $user, Course $course)
    {
        if (method_exists($user, 'isAdmin') && $user->isAdmin()) return true;
        return $user->isTeacher() && $course->user_id === $user->id;
    }
}
