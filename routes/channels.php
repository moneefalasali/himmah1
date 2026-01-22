<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\ChatRoom;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('chat.{roomId}', function ($user, $roomId) {
    $room = ChatRoom::find($roomId);
    
    if (!$room) return false;

    // الأدمن لديه وصول كامل
    if ($user->isAdmin()) return true;

    // المعلم يجب أن يكون هو معلم الدورة
    if ($user->isTeacher()) {
        return $room->course->user_id === $user->id;
    }

    // الطالب يجب أن يكون مشتركاً في الدورة واشتراكه فعال
    if ($user->isStudent()) {
        return $user->activeCourses()->where('courses.id', $room->course_id)->exists();
    }

    return false;
});
