<?php

namespace App\Policies;

use App\Models\User;
use App\Models\ChatRoom;
use App\Models\Course;

class ChatPolicy
{
    /**
     * هل يمكن للمستخدم رؤية الدردشة؟
     */
    public function viewChat(User $user, Course $course)
    {
        if ($user->isAdmin()) return true;

        if ($user->isTeacher()) {
            return $course->user_id === $user->id;
        }

        if ($user->isStudent()) {
            return $user->activeCourses()->where('courses.id', $course->id)->exists();
        }

        return false;
    }

    /**
     * هل يمكن للمستخدم إرسال رسالة؟
     */
    public function sendMessage(User $user, ChatRoom $chatRoom)
    {
        // الأدمن لا يمكنه إرسال رسائل حسب المتطلبات (للقراءة فقط)
        if ($user->isAdmin()) return false;

        if ($user->isTeacher()) {
            return $chatRoom->course->user_id === $user->id;
        }

        if ($user->isStudent()) {
            return $user->activeCourses()->where('courses.id', $chatRoom->course_id)->exists();
        }

        return false;
    }

    /**
     * هل يمكن للأدمن مراقبة الدردشة؟
     */
    public function monitor(User $user)
    {
        return $user->isAdmin();
    }
}
